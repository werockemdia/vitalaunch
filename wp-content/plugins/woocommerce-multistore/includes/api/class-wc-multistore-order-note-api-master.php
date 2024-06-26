<?php
/**
 * Order Note api Master handler.
 *
 * This handles order note api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Note_Api_Master
 */
class WC_Multistore_Order_Note_Api_Master extends WC_Multistore_Request {

	public function send_create_order_note_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_create_master_order_note';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_delete_order_note_data_to_child( $args, $site_id ) {
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_delete_master_order_note';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

}