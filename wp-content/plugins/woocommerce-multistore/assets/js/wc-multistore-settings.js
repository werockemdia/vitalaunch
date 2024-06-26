jQuery( function( $ ) {

	var wc_multistore_settings = {
		init: function() {
			$('.wc-multistore-variations-settings-switch').change(function() {
				if ( this.value === 'yes' ) {
					$(this).parents('table').find('.wc-multistore-variations-settings').removeClass('hidden');
				} else {
					$(this).parents('table').find('.wc-multistore-variations-settings').addClass('hidden');
				}
			});

			$('.wc-multistore-attributes-settings-switch').change(function() {
				if ( this.value === 'yes' ) {
					$(this).parents('table').find('.wc-multistore-attributes-settings').removeClass('hidden');
				} else {
					$(this).parents('table').find('.wc-multistore-attributes-settings').addClass('hidden');
				}
			});

			$('.wc-multistore-categories-settings-switch').change(function() {
				if ( this.value === 'yes' ) {
					$(this).parents('table').find('.wc-multistore-categories-settings').removeClass('hidden');
				} else {
					$(this).parents('table').find('.wc-multistore-categories-settings').addClass('hidden');
				}
			});


			$('.woomulti_option_with_warning').change(function() {
				if ( this.value == 'yes' ) {
					$('.woomulti_options_warning', $(this).parent().parent()).show();
				} else {
					$('.woomulti_options_warning', $(this).parent().parent()).hide();
				}
			});

			if ( $('.row-order-import-to').attr( 'data-option-visible') == 'yes' ) {
				$('.row-order-import-to').show();
			} else {
				$('.row-order-import-to').hide();
			}

			$( "select[name=enable-order-import]" ).on( 'change', function(){
				if ( this.value == 'yes' ) {
					$('.row-order-import-to').show();
				} else {
					$('.row-order-import-to').hide();
				}
			});

			if ( $('.row-global-image-master').attr( 'data-option-visible') == 'yes' ) {
				$('.row-global-image-master').show();
			} else {
				$('.row-global-image-master').hide();
			}

			$( "select[name=enable-global-image]" ).on( 'change', function(){
				if ( this.value == 'yes' ) {
					$('.row-global-image-master').show();
				} else {
					$('.row-global-image-master').hide();
				}
			});

			$("#synchronize-by-default").change(function() {
				if ( this.value === 'yes' ) {
					$('#inherit-by-default').removeAttr('disabled');
				} else {
					$('#inherit-by-default').attr('disabled', true);
				}
			});

			$("#synchronize-rest-by-default").change(function() {
				if ( this.value == 'yes' ) {
					$('#inherit-rest-by-default').removeAttr('disabled');
				} else {
					$('#inherit-rest-by-default').attr('disabled', true);
				}
			});
		},
		maybe_display_import_to: function(){
			if ( $('.row-order-import-to').attr('data-option-visible') == 'yes' ) {
				$('.row-order-import-to').css('display', 'block !important');
			}
		},
		maybe_display_global_image_master: function(){
			if ( $('.row-global-image-master').attr('data-option-visible') == 'yes' ) {
				$('.row-order-import-to').css('display', 'block !important');
			}
		},
		enable_tips: function(){
			$( '.tips' ).tipTip( {'attribute': 'data-tip','fadeIn': 50,'fadeOut': 50,'delay': 200} );
		},
		sort_tabs: function(){
			var tabs = $( "#fields-control" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
			$( "#fields-control li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
			tabs.find( ".ui-tabs-nav" ).sortable({
				axis: "y",
				stop: function() {
					tabs.tabs( "refresh" );
				}
			});
		},
	};

	$(document).ready(function(){
		wc_multistore_settings.init();
		wc_multistore_settings.maybe_display_import_to();
		wc_multistore_settings.maybe_display_global_image_master();
		wc_multistore_settings.enable_tips();
		wc_multistore_settings.sort_tabs();
	});

});