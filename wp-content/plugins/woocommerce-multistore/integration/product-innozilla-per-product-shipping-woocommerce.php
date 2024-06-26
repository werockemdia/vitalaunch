<?php
/**
 * Integrates the plugin Product Innozilla Per Product Shipping for WooCommerce
 * 
 * Plugin URL: https://wordpress.org/plugins/innozilla-per-product-shipping-woocommerce/
 * 
 * @since 4.1.2
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_PRODUCT_INNOZILLA_PER_PRODUCT_SHIPPING_WOOCOMMERCE {


	public $meta_keys = array(
		'_per_product_shipping',
		'_per_product_shipping_add_to_all',
	);

	public function __construct() {
		add_filter( 'wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys' ), 10, 1 );
		add_filter( 'wc_multistore_master_product_data', array( $this, 'add_innozilla_data' ), 10, 1 );
		add_filter( 'wc_multistore_child_product_saved', array( $this, 'set_shipping_rules' ), 10, 2 );
	}


	public function add_meta_keys( $meta_keys ) {
		return array_merge( $meta_keys, $this->meta_keys );
	}

	public function add_innozilla_data( $data ) {
		global $wpdb;

		$per_product_shipping_rules = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . 'innozilla_per_product_shipping_rules_woo WHERE product_id=' . (int) $data['ID'], ARRAY_A );
		$data['innozilla'] = $per_product_shipping_rules;

		return $data;
	}


	public function set_shipping_rules( $wc_product, $data ){
		global $wpdb;

		$wpdb->delete( $wpdb->prefix . 'innozilla_per_product_shipping_rules_woo', array(	'product_id'  =>  $wc_product->get_id() ) );

		if ( ! empty( $data['innozilla'] ) ){
			foreach( $data['innozilla'] as $per_product_shipping_rule ){
				unset($per_product_shipping_rule['iz_rule_id']);
				$per_product_shipping_rule['product_id'] = $wc_product->get_id();
				$wpdb->insert( $wpdb->prefix . 'innozilla_per_product_shipping_rules_woo', $per_product_shipping_rule);
			}
		}
	}

}
new WOO_MSTORE_INTEGRATION_PRODUCT_INNOZILLA_PER_PRODUCT_SHIPPING_WOOCOMMERCE();