<div id="woonet_data" class="panel woocommerce_options_panel" style="display:none;">
<?php
global $product_object;

if( ! empty( $this->master_fields ) ){

$toggle_publish_to = $this->settings['synchronize-by-default'];
$toggle_inherit_to = $this->settings['inherit-by-default'];
$toggle_stock = $this->settings['synchronize-stock'];
$product_settings = $product_object->get_meta('_woonet_settings');
?>

<h3>Product Settings</h3>

<?php
	foreach ( $this->master_fields as $field ) {
		if ( ! is_array( $field ) ) {
			if ( $field == 'start_group' ) {
				echo '<div class="options_group">';
			} elseif ( $field == 'end_group' ) {
				echo '</div>';
			}

			continue;
		}

		switch ( $field['type'] ) {
			case 'heading':
				printf( '<h4>%s</h4>', $field['label'] );
				break;
			case 'description':
				printf(
					'<p class="form-field %s"><span class="description">%s</span></p>',
					$field['class'],
					wp_kses_post( $field['label'] )
				);
				break;
			case 'checkbox':
				printf(
					'<p class="form-field no_label %s" %s>',
					$field['class'],
					isset( $field['custom_attribute'] ) ? $field['custom_attribute'] : ''
				);
				if ( ! empty( $field['label'] ) ) {
					printf( '<label for="%s">%s</label>', $field['id'], $field['label'] );
				}

				$value =  $product_object->get_meta($field['id']);
               
				printf(
					'<label class="wc-multistore-checkbox-label"><input type="hidden" name="%s" value="" /><input type="checkbox" id="%s" class="%s wc-multistore-checkbox" %s %s %s /><span class="checkmark"></span></label>',
					$field['id'],
					$field['id'],
					$field['class'],
					empty( $field['disabled'] ) ? '' : 'disabled="disabled"',
					checked( wc_string_to_bool( isset( $field['checked'] ) ? $field['checked'] : $value ), true, false ),
					empty( $field['set_default_value'] ) ? '' : 'data-default-value="' . $value . '"',
				);

				if ( ! empty( $field['desc_tip'] ) ) {
					printf(
						'<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />',
						esc_attr( $field['desc_tip'] ),
						esc_url( plugins_url() . '/woocommerce' )
					);
				}
				printf(
					'<span class="description">%s</span>',
					wp_kses_post( $field['description'] )
				);


				if( str_contains($field['id'], '_woonet_publish_to_') && !str_contains($field['id'], '_child_inheir') ){
					$child_data = $product_object->get_meta('_woonet_children_data');
					$explode = explode('_', $field['id']);
					$site_id = $explode[4];

					if( ! empty($child_data) && isset($child_data[$site_id]) && isset($child_data[$site_id]['edit_link']) ){
						echo '  <a target="_blank" href="'.$child_data[$site_id]['edit_link'].'">edit</a>';
					}
				}


				echo '</p>';
				break;
			default:
				$func = 'woocommerce_wp_' . $field['type'] . '_input';
				if ( function_exists( $func ) ) {
					$func( $field );
				}
				break;
		}
	}

	echo '</div>';
}

?>