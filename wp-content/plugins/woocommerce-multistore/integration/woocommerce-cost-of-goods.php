<?php
/**
 * Integrate WooCommerce Cost of Goods
 * URL: http://www.woocommerce.com/products/woocommerce-cost-of-goods/
 * Plugin URL: http://www.woocommerce.com/products/woocommerce-cost-of-goods/
 *
 * @since 4.1.6
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_WOOCOMMERCE_COST_OF_GOODS {

	public $meta_keys  = array(
		'_wc_cog_cost',
	);

	public function __construct() {
		add_filter( 'wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys' ), PHP_INT_MAX, 1 );
	}

	public function add_meta_keys( $meta_keys ) {
		return array_merge( $meta_keys, $this->meta_keys );
	}
}

new WOO_MSTORE_INTEGRATION_WOOCOMMERCE_COST_OF_GOODS();