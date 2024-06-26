<?php
/**
 * Integrates the plugin Elementor & Elementor Pro
 * 
 * @since 4.1.5
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_ELEMENTOR_ELEMENTOR_PRO {


	public $meta_keys = array(
		'_elementor_edit_mode',
		'_elementor_template_type',
		'_elementor_version',
		'_elementor_pro_version',
		'_wp_page_template',
		'_elementor_data',
		'_elementor_controls_usage',
	);

	public function __construct() {
		add_filter( 'wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys' ), PHP_INT_MAX, 1 );
	}


	public function add_meta_keys( $meta_keys ) {
		return array_merge( $meta_keys, $this->meta_keys );
	}

}

new WOO_MSTORE_INTEGRATION_ELEMENTOR_ELEMENTOR_PRO();
