<?php
/**
 * Stock api child handler.
 *
 * This handles stock api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Stock_Api_Child
 */
class WC_Multistore_Stock_Api_Child extends WC_Multistore_Request {
	public function sync_stock_to_master( $args, $site_id ){
		$master_data = get_site_option('wc_multistore_master_connect');
		$body = $args;
		$admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
		$body['action'] = 'wc_multistore_master_receive_stock';
		$body['key'] = $site_id;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}
}