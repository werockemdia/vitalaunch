<?php
/**
 * Integrates the plugin Product GTIN (EAN, UPC, ISBN) for WooCommerce
 * 
 * Plugin URL: https://wordpress.org/plugins/product-gtin-ean-upc-isbn-for-woocommerce/
 * 
 * @since 4.1.2
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_PRODUCT_GTIN_EAN_UPC_ISBN_FOR_WOOCOMMERCE {

	public $meta_keys = array(
		'_wpm_gtin_code',
		'_wpm_gtin_code_label',
	);

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 */
	public function __construct() {
		add_filter( 'wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys' ), PHP_INT_MAX, 1 );
	}


	public function add_meta_keys( $meta_keys ) {
		return array_merge( $meta_keys, $this->meta_keys );
	}
}

new WOO_MSTORE_INTEGRATION_PRODUCT_GTIN_EAN_UPC_ISBN_FOR_WOOCOMMERCE();
