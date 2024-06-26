<?php
/**
 * Integrate Custom Product Tabs for WooCommerce
 * URL: https://wordpress.org/plugins/yikes-inc-easy-custom-woocommerce-product-tabs/
 * Plugin URL: https://wordpress.org/plugins/yikes-inc-easy-custom-woocommerce-product-tabs/
 *
 * @since 4.1.5
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_CUSTOM_PRODUCT_TABS_FOR_WOOCOMMERCE {


	public $meta_keys = array(
		'yikes_woo_products_tabs'
	);

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 */
	public function __construct() {
	add_filter( 'wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys' ), PHP_INT_MAX, 1 );
	}

	public function add_meta_keys( $meta_keys ) {
		return array_merge( $meta_keys,$this->meta_keys );
	}
}

new WOO_MSTORE_INTEGRATION_CUSTOM_PRODUCT_TABS_FOR_WOOCOMMERCE();
