<?php
/**
 * Order api Master handler.
 *
 * This handles product api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Api_Master
 */
class WC_Multistore_Order_Api_Master extends WC_Multistore_Request {

	public function send_order_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_update_master_order';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_get_orders_request( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url() . '/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_get_export_orders';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function get_child_orders( $page = 1, $per_page = 10, $post_status = '', $search = '', $site_id = null, $post_type = '' ) {
		$args = array(
			'page'        => $page,
			'per_page'    => $per_page,
			'post_status' => $post_status,
			'search'      => $search,
			'post_type'   => $post_type
		);

		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url() . '/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_get_orders';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $admin_url, $args );
	}

	public function sync_order_status( $args, $site_id ) {
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url() . '/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_sync_order_status';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $admin_url, $args );
	}

}