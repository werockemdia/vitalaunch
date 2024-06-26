<?php
/**
 * Product api Master handler.
 *
 * This handles product api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Api_Master
 */
class WC_Multistore_Product_Api_Master extends WC_Multistore_Request {

	public function send_product_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = wp_json_encode($args);
		$body['action'] = 'wc_multistore_update_child_product';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_trash_product_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_trash_child_product';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_untrash_product_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_untrash_child_product';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_delete_product_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_delete_child_product';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

}