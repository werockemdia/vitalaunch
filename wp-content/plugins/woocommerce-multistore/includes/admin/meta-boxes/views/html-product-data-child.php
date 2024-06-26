<?php
echo '<div id="woonet_data" class="panel woocommerce_options_panel" style="display:none;">';
foreach ( $this->child_fields as $field ) {
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

			$value = get_post_meta( get_the_ID(), $field['id'], true );
			printf(
				'<input type="hidden" name="%s" value="" /><input type="checkbox" id="%s" class="%s" %s %s %s />',
				$field['id'],
				$field['id'],
				$field['class'],
				empty( $field['disabled'] ) ? '' : 'disabled="disabled"',
				checked( wc_string_to_bool( isset( $field['checked'] ) ? $field['checked'] : $value ), true, false ),
				empty( $field['set_default_value'] ) ? '' : 'data-default-value="' . $value . '"'
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