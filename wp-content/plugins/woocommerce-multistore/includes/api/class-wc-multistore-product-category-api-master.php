<?php
/**
 * Product category api Master handler.
 *
 * This handles product category api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Category_Api_Master
 */
class WC_Multistore_Product_Category_Api_Master extends WC_Multistore_Request {

	public function send_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_update_child_product_category';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

}