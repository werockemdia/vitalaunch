jQuery( function( $ ) {
		toggle_order_items_empty_warning();
		$( "#export_time_after" ).datepicker();
		$( "#export_time_before" ).datepicker();

		jQuery( "#order_status" ).select2( {
			placeholder: woonet_woocommerce_orders_export.order_status_filter_placeholder
		} );

		jQuery( "#site_filter" ).select2( {
			placeholder: woonet_woocommerce_orders_export.site_filter_placeholder
		} );

		jQuery( "#order_status" ).select2( {
			placeholder: woonet_woocommerce_orders_export.order_status_filter_placeholder
		} );

		$( "#export_fields" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});

		$( '.export_fields' ).controlgroup( {
			direction: "vertical"
		} );
		$( 'input', '.export_fields' ).checkboxradio({
			icon: false
		});

		$( 'span.dashicons', '#order_fields_selected' ).on('click', remove_sortable_export_field);

		// Export Fields input on change add or remove from the selected table
		$( 'input', '.export_fields' ).on( 'change', function() {
			const element_id = $( this ).data('name');




			if ( this.checked ) {
				add_sortable_export_field( this );
			} else {
				$( '#'+element_id ).remove();
			}


			toggle_order_items_empty_warning();



			$('#order_fields_selected').sortable( "refresh" );
		} );

		// If Order Items is checked but no other item fields, display a warning under the selected Order Items field
		function toggle_order_items_empty_warning() {
			let getOrderItems = $(".export_fields").find('[data-name="order__order_items"]');

			if(getOrderItems.is(':checked') === true){
				let regex = 'order_item';
				let foundElement=0;
				$( 'input', '.export_fields' ).each(function( index ) {
					const element_id = $(this).data('name');
					if(element_id !== "order__order_items"){
						if (element_id.indexOf(regex) !== -1) {
							if ($(this).is(':checked')) {
								foundElement = 1;
								return false;
							}
						}
					}

				});

				if(foundElement === 0){
					// console.log($("#order__order_items").html());
					$("#order__order_items").append( '<span id="warn_remove" style="display: block;color: red;">WARNING: In order for this column to not be empty you need to also select at least one item field</span>' );
				}else{
					if($("#warn_remove").length !== 0){
						$("#warn_remove").remove();
					}
				}
			}
		}

		function add_sortable_export_field( element ) {
			const element_id = $( element ).data('name');

			var input = $('<input/>', {
				type: 'text',
				name: 'export_fields[' + element_id + ']',
				value: element_id
			});
			var div = $('<div/>', {
				id: element_id,
				class: 'ui-state-highlight'
			});

			div.append( 'Column name:' )
				.append( input )
				.append('<span>(' + $('.ui-accordion-header-active').text() + ' -> ' + $( element ).parent().text() + ')</span>')
				.append('<span class="dashicons dashicons-no-alt"></span>')
				.appendTo('#order_fields_selected');

			$('span.dashicons', '#'+element_id).on('click', remove_sortable_export_field);
		}

		function remove_sortable_export_field() {
			let sortable_export_field = $( this ).parent( 'div.ui-state-highlight' ),
				sortable_export_field_id = $( sortable_export_field ).attr( 'id' );

			$( 'input[data-name="' + sortable_export_field_id + '"]', '#export_fields' ).prop( "checked", false );
			$( 'input[data-name="' + sortable_export_field_id + '"]', '#export_fields' ).checkboxradio( "refresh" );

			$( sortable_export_field ).remove();

			$('#order_fields_selected').sortable( "refresh" );
			toggle_order_items_empty_warning();
		}

		$( '#order_fields_selected' ).sortable();
		$( '#order_fields_selected' ).disableSelection();

		$( '#form_data' ).submit( function( event) {
			check_dependency_order_items();
			if ( $( '.ui-sortable-handle', '#order_fields_selected' ).length === 0 ) {
				event.preventDefault();
				alert( 'Please select at least one field to export.' );
			}
		} );

		// Checks if a field that requires "Order Items" has been checked
		function check_dependency_order_items(){
			let regex = 'order_item';
			$( 'input', '.export_fields' ).each(function( index ) {
				const element_id = $( this ).data('name');
				if(element_id.indexOf(regex) !== -1){
					if($(this).is(':checked')){
						let getOrderItems = $(".export_fields").find('[data-name="order__items"]');
						if(getOrderItems.is(':checked') === false){
							getOrderItems.click();
							return false;
						}
					}
				};
			});

		}

});