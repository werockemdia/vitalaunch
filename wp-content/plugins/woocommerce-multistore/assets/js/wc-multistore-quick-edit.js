jQuery( function( $ ) {

	var wc_multistore_quick_edit = {
		notice: $('.wc-multistore-quick-edit-notice-container'),
		action: $('.wc-multistore-quick-edit-notice-container').data('action'),
		cancel_action: 'wc_multistore_cancel_inline_save_ajax',
		nonce: $('#wc_multistore_quick_edit_nonce').val(),
		transient: false,
		edit_inline_button: false,
		post_id: false,
		sku: false,
		inline_data: false,
		post_name: false,
		image_src: false,
		is_child: false,
		sites: wc_multistore_data.sites,
		settings: wc_multistore_data.settings,
		init: function() {
			$( '#the-list' ).on('click','.editinline', function(){
				wc_multistore_quick_edit.edit_inline_button = $(this);
				wc_multistore_quick_edit.post_id = wc_multistore_quick_edit.get_post_id($(this));
				wc_multistore_quick_edit.inline_data = wc_multistore_quick_edit.get_inline_data();
				wc_multistore_quick_edit.post_name = wc_multistore_quick_edit.get_post_name();
				wc_multistore_quick_edit.image_src = wc_multistore_quick_edit.get_image_src();
				wc_multistore_quick_edit.is_child = wc_multistore_quick_edit.get_is_child();
				wc_multistore_quick_edit.set_field_values();
			});

			$('.woonet_toggle_all_sites').change(function () {
				var checked = $(this).is(":checked");
				var publish_to_fields = $('input[type="checkbox"]._woonet_publish_to');

				publish_to_fields.each(function (el) {
					$(this).prop('checked', checked);
					$(this).trigger('change');
				});
			});

			$('.woonet_toggle_inherit_to').change(function () {
				var checked = $(this).is(":checked");
				var inherit_to_fields = $('input[type="checkbox"]._woonet_inherit_to');

				inherit_to_fields.each(function (el) {
					$(this).prop('checked', checked);
					$(this).trigger('change');
				});
			});

			$('.woonet_toggle_stock_to').change(function () {
				var checked = $(this).is(":checked");
				var stock_fields = $('input[type="checkbox"]._woonet_sync_stock');

				stock_fields.each(function (el) {
					$(this).prop('checked', checked);
					$(this).trigger('change');
				});
			});

			$('input[type="checkbox"]._woonet_publish_to').change(function () {
				var checked = $(this).is(":checked");

				if( checked ){
					$(this).prev().val('yes');
				}else{
					$(this).prev().val('no');
				}
			});

			$('input[type="checkbox"]._woonet_inherit_to').change(function () {
				var checked = $(this).is(":checked");

				if( checked ){
					$(this).prev().val('yes');
				}else{
					$(this).prev().val('no');
				}
			});

			$('input[type="checkbox"]._woonet_sync_stock').change(function () {
				var checked = $(this).is(":checked");

				if( checked ){
					$(this).prev().val('yes');
				}else{
					$(this).prev().val('no');
				}
			});

			$('.wc-multistore-quick-edit-notice-close-sync-screen a').on( 'click', function (){
				wc_multistore_quick_edit.close_container();
			} );

			$('.wc-multistore-quick-edit-notice-cancel-sync').on( 'click', function (){
				wc_multistore_quick_edit.cancel();
			} );
		},
		get_post_id: function (element){
			let post_id = element.closest( 'tr' ).attr( 'id' );
			return post_id.replace( 'post-', '' );
		},
		get_sku: function (fields){
			let deserialized = this.deserialize(fields);
			return deserialized['_sku'];
		},
		get_inline_data: function(){
			return $( '#inline_' + this.post_id );
		},
		get_post_name: function(){
			return  this.inline_data.find('.post_name').text();
		},
		get_image_src: function(){
			return  $( '#post-' + this.post_id ).find('img').attr('src');
		},
		get_is_child: function(){
			return this.inline_data.find('._woonet_is_child_product').text();
		},
		get_selected_sites: function(fields){
			let selected_sites = [];
			let deserialized = this.deserialize(fields);
			$.each( this.sites, function( site_id, val ) {
				var publish_to = '_woonet_publish_to_' + site_id;
				if( deserialized[publish_to] === 'yes' ){
					selected_sites.push(site_id);
				}
			});
			return selected_sites;
		},
		set_field_values:function(){
			if( this.is_child === 'yes' ){
				$('#woonet-quick-edit-fields').remove();
			}else{
				$('#woonet-quick-edit-fields-slave').hide();
			}

			var woonet_synchronize_stock =  wc_multistore_quick_edit.inline_data.find( '._woonet_synchronize-stock' ).text();

			$.each( this.sites, function( site_id, val ) {
				var publish_to = '_woonet_publish_to_' + site_id;
				var inherit = '_woonet_publish_to_' + site_id + '_child_inheir';
				var stock = '_woonet_' + site_id + '_child_stock_synchronize';

				var publish_to_value = wc_multistore_quick_edit.inline_data.find( '.' + publish_to ).text();
				var inherit_value = wc_multistore_quick_edit.inline_data.find( '.' + inherit ).text();
				var stock_value = wc_multistore_quick_edit.inline_data.find( '.' + stock ).text();

				if( woonet_synchronize_stock === 'yes' ){
					stock_value = 'yes';
				}

				if( publish_to_value === 'yes' ){
					$( '.' + publish_to ).prop( 'checked', true );
					$( '.' + publish_to ).trigger('change');
					// $( '.' + publish_to ).val( publish_to_value );
				}else{
					$( '.' + publish_to ).prop( 'checked', false );
					$( '.' + publish_to ).trigger('change');
				}

				if( inherit_value === 'yes' ){
					$( '.' + inherit ).prop( 'checked', true );
					$( '.' + inherit ).trigger('change');
					// $( '.' + inherit ).val( inherit_value );
				}else{
					$( '.' + inherit ).prop( 'checked', false );
					$( '.' + inherit ).trigger('change');
				}


				if( stock_value === 'yes' ){
					$( '.' + stock ).prop( 'checked', true );
					$( '.' + stock ).trigger('change');
					// $( '.' + stock ).val( stock_value );
				}else{
					$( '.' + stock ).prop( 'checked', false );
					$( '.' + stock ).trigger('change');
				}

			});
		},
		deserialize: function(fields){
			var data = {};

			var i, vars = fields.split('&');
			for (i = 0; i < vars.length; i++) {
				if(!vars[i]) {
					continue;
				}
				var pair = vars[i].split('=');
				if(pair.length < 2) {
					continue;
				}
				data[pair[0]] = pair[1];
			}

			return data;
		},
		update_column: function( result, site_id ){
			$( '#post-' + this.post_id ).find('.woonet-quick-edit-site-id-'+ site_id + ' a').attr('href', result.data.edit_link);
			$( '#post-' + this.post_id ).find('.woonet-quick-edit-site-id-'+ site_id + ' a').text('edit');
		},
		variation_errors: function( response ){
			if( response.result !== undefined && response.site_id !== undefined ){
				if( response.result.data !== undefined && response.result.data.variation_errors !== undefined && response.result.data.variation_errors !== null ){
					for ( i = 0; i < response.result.data.variation_errors.length; i++ ){
						this.notice.find('.wc-multistore-quick-edit-notice-message').append('<p class="notice-error">Variation: ' + response.result.data.variation_errors[i].message + '</p>');
					}
				}
			}
		},
		send_request: function(fields){
			let selected_sites = this.get_selected_sites(fields);
			let total_sites = selected_sites.length;

			if( this.is_child === 'yes' ){
				return;
			}

			if( total_sites < 1 ){
				return;
			}

			this.sku = this.get_sku(fields);

			var params = {
				action: this.action,
				post_ID: this.post_id,
				sku: this.sku,
				nonce: this.nonce,
				total_sites: total_sites,
				selected_sites: selected_sites,
			};

			params = $.param(params);

			if( this.settings['sync-method'] === 'background' ){
				this.display_notice();
				this.show_close_button();
				this.background_sync(params);
				this.close_countdown();
			}else{
				this.display_notice();
				this.ajax_sync(params);
			}

		},
		ajax_sync:function(params){
			window.wc_multistore_quick_edit_ajax = $.post(ajaxurl, params, function(data){
				let response = JSON.parse(data);

				if(response.status === 'pending'){
					wc_multistore_quick_edit.update_progress(response.progress);
					params = {
						action: wc_multistore_quick_edit.action,
						nonce: wc_multistore_quick_edit.nonce,
						transient: response.transient,
						sku: wc_multistore_quick_edit.sku,
					};

					params = $.param(params);
					wc_multistore_quick_edit.set_transient(response.transient);
					wc_multistore_quick_edit.ajax_sync(params);
					wc_multistore_quick_edit.variation_errors(response, response.site_id);
					if(response.result.status === 'failed'){
						wc_multistore_quick_edit.failed(response.result.message);
					}
				}

				if(response.status === 'completed'){
					wc_multistore_quick_edit.update_progress(100);
					wc_multistore_quick_edit.hide_cancel_button();
					wc_multistore_quick_edit.show_close_button();
					wc_multistore_quick_edit.close_countdown();
					wc_multistore_quick_edit.variation_errors(response);
					if(response.result.status === 'failed'){
						wc_multistore_quick_edit.failed(response.result.message);
					}
				}

				if(response.status === 'failed'){
					wc_multistore_quick_edit.hide_cancel_button();
					wc_multistore_quick_edit.failed(response.message);
					wc_multistore_quick_edit.show_close_button();
				}

				if( response.result !== undefined && response.site_id !== undefined ){
					wc_multistore_quick_edit.update_column(response.result, response.site_id);
				}

			}).fail(function(error) {
				wc_multistore_quick_edit.hide_cancel_button();
				wc_multistore_quick_edit.failed("Sync failed due to server error. Please try again.");
				wc_multistore_quick_edit.show_close_button();

			});
		},
		background_sync:function(params){
			$.post(ajaxurl, params, function(data){
				// console.log(data);
			}).fail(function(error) {
			});
		},
		display_notice:function(){
			this.notice.find('img').attr('src', this.image_src);
			this.notice.find('.wc-multistore-quick-edit-notice-name').text(this.post_name);
			this.notice.find('.wc-multistore-quick-edit-notice-progress-bar').progressbar({
				value:  0
			});
			this.hide_message();
			this.hide_close_button();
			this.show_cancel_button();
			$('.wc-multistore-quick-edit-notice-close-sync-screen a').attr('data-attr', 5);
			this.notice.show();
		},
		hide_message: function (){
			this.notice.find('.wc-multistore-quick-edit-notice-message').text('');
		},
		hide_cancel_button: function (){
			this.notice.find('.wc-multistore-quick-edit-notice-cancel-sync').hide();
		},
		show_cancel_button: function (){
			this.notice.find('.wc-multistore-quick-edit-notice-cancel-sync').show();
		},
		hide_close_button: function (){
			this.notice.find('.wc-multistore-quick-edit-notice-close-sync-screen').hide();
		},
		show_close_button: function (){
			this.notice.find('.wc-multistore-quick-edit-notice-close-sync-screen').show();
		},
		failed:function (message){
			let error = '<p class="notice-error">' + message + '</p>'
			this.notice.find('.wc-multistore-quick-edit-notice-message').append(error);
		},
		update_progress:function(percentage){
			this.notice.find('.wc-multistore-quick-edit-notice-progress-bar').progressbar({
				value: percentage
			});
		},
		set_transient:function(transient){
			this.transient = transient;
		},
		cancel:function(){
			if (confirm("Do you really want to cancel sync?") ) {
				window.wc_multistore_quick_edit_ajax.abort();
				let params = {
					action: wc_multistore_quick_edit.cancel_action,
					transient: wc_multistore_quick_edit.transient,
					nonce: wc_multistore_quick_edit.nonce,
				};
				params = $.param(params);

				$.post(ajaxurl, params, function(response) {
					wc_multistore_quick_edit.hide_cancel_button();
					wc_multistore_quick_edit.show_close_button();
					wc_multistore_quick_edit.close_countdown();
				});
				window.clearInterval( window.wc_multistore_quick_edit_close_counter );
			}
		},
		close_container:function (){
			this.notice.slideUp();
		},
		close_countdown:function(){
			window.wc_multistore_quick_edit_close_counter = setInterval(function(){
				var counter = $('.wc-multistore-quick-edit-notice-close-sync-screen a').attr('data-attr');
				if (counter <= 0) {
					$('.wc-multistore-quick-edit-notice-container').slideUp();
					window.clearInterval( window.wc_multistore_quick_edit_close_counter );
					wc_multistore_quick_edit.update_progress(0);
				} else {
					$('.wc-multistore-quick-edit-notice-close-sync-screen a').text("Close (" + counter + ") ");
					counter = counter - 1;
					$('.wc-multistore-quick-edit-notice-close-sync-screen a').attr('data-attr', counter);
				}
			}, 1000);
		},
		override_inline_edit_save: function(){
			inlineEditPost.save = function(id) {
				var params, fields, page = $('.post_status_page').val() || '';

				if ( typeof(id) === 'object' ) {
					id = this.getId(id);
				}

				$( 'table.widefat .spinner' ).addClass( 'is-active' );

				params = {
					action: 'inline-save',
					post_type: typenow,
					post_ID: id,
					edit_date: 'true',
					post_status: page
				};

				fields = $('#edit-'+id).find(':input').serialize();
				params = fields + '&' + $.param(params);

				// Make Ajax request.
				$.post( ajaxurl, params,
					function(r) {
						var $errorNotice = $( '#edit-' + id + ' .inline-edit-save .notice-error' ),
							$error = $errorNotice.find( '.error' );

						$( 'table.widefat .spinner' ).removeClass( 'is-active' );

						if (r) {
							if ( -1 !== r.indexOf( '<tr' ) ) {
								$(inlineEditPost.what+id).siblings('tr.hidden').addBack().remove();
								$('#edit-'+id).before(r).remove();
								$( inlineEditPost.what + id ).hide().fadeIn( 400, function() {
									// Move focus back to the Quick Edit button. $( this ) is the row being animated.
									$( this ).find( '.editinline' )
										.attr( 'aria-expanded', 'false' )
										.trigger( 'focus' );
									wp.a11y.speak( wp.i18n.__( 'Changes saved.' ) );
								});
								wc_multistore_quick_edit.send_request(fields);

							} else {
								r = r.replace( /<.[^<>]*?>/g, '' );
								$errorNotice.removeClass( 'hidden' );
								$error.html( r );
								wp.a11y.speak( $error.text() );
							}
						} else {
							$errorNotice.removeClass( 'hidden' );
							$error.text( wp.i18n.__( 'Error while saving the changes.' ) );
							wp.a11y.speak( wp.i18n.__( 'Error while saving the changes.' ) );
						}
					},
					'html');

				// Prevent submitting the form when pressing Enter on a focused field.
				return false;
			}
		}

	};

	$(document).ready(function(){
		wc_multistore_quick_edit.init();
		wc_multistore_quick_edit.override_inline_edit_save();
	});

});