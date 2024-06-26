<?php

defined( 'ABSPATH' ) || exit;


if( ! function_exists('wc_multistore_get_product_class_name') ) {
	/**
	 * Gets the classname of woomultistore product based on product type and multistore type
	 * @param string $wc_multistore_type
	 * @param string $product_type
	 *
	 * @return string
	 * Returns the classname based on product type and multistore type. Ex: WC_Multistore_Product_Simple_Master
	 */
	function wc_multistore_get_product_class_name( $wc_multistore_type, $product_type ) {
		$product_type = ucfirst($product_type);
		$wc_multistore_type = ucfirst($wc_multistore_type);
		$class_name =  'WC_Multistore_Product_'. $product_type . '_' . $wc_multistore_type;

		if( ! class_exists($class_name) ){
			return false;
		}

		return $class_name;
	}
}

if( ! function_exists('wc_multistore_is_child_product') ) {
	/**
	 * Checks if a product is a multistore child product. Used on child stores
	 * @param $wc_product
	 *
	 * @return bool
	 * Return false or true if the product is a child product
	 */
	function wc_multistore_is_child_product( $wc_product ) {
		$settings = WOO_MULTISTORE()->settings;

		if( $settings['sync-by-sku'] == 'yes' ){
			$master_product = $wc_product->get_meta('_woonet_network_is_child_product_sku', true);
		}else{
			$master_product = $wc_product->get_meta('_woonet_network_is_child_product_id', true);
		}

		return ! empty( $master_product );
	}
}

if( ! function_exists('wc_multistore_product_get_master_product_id') ) {

	/**
	 * Gets the master product id by sku if sync by sku is enabled. Used on main store
	 * @param $id
	 * @param $sku
	 *
	 * @return int|mixed
	 * Returns master product id
	 */
	function wc_multistore_product_get_master_product_id( $id, $sku ){
		$settings = WOO_MULTISTORE()->settings;

		if( $settings['sync-by-sku'] == 'yes' ){
			$master_product_id = wc_get_product_id_by_sku( $sku );
		}else{
			$master_product_id = $id;
		}

		return $master_product_id;
	}
}

if( ! function_exists('wc_multistore_product_get_master_blog_id') ) {
	/**
	 * Get master product site id from a child product. Used on child stores
	 * @param $wc_product
	 *
	 * @return mixed
	 * Returns master product site id or empty
	 */
	function wc_multistore_product_get_master_blog_id( $wc_product ){
		return $wc_product->get_meta('_woonet_network_is_child_site_id', true);
	}
}

if( ! function_exists('wc_multistore_product_get_slave_product_id') ) {
	/**
	 * Get master product id from a child product. Used on child stores
	 * @param $product_id
	 * @param false $sku
	 *
	 * @return string|null
	 * Returns master product id or empty
	 */
	function wc_multistore_product_get_slave_product_id( $product_id, $sku = false ){
		global $wpdb;
		$settings = WOO_MULTISTORE()->settings;

		if( $settings['sync-by-sku'] == 'yes' ){
			$master_product_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id from {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND meta_value =%s", $sku ) );
		}else{
			$query = "SELECT post_id from {$wpdb->prefix}postmeta WHERE meta_key='_woonet_network_is_child_product_id' AND meta_value='{$product_id}'";
			$master_product_id = $wpdb->get_var( $query );
		}

		return $master_product_id;
	}
}