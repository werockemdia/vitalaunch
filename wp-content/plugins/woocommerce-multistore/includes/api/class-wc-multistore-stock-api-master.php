<?php
/**
 * Stock api master handler.
 *
 * This handles stock api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Stock_Api_Master
 */
class WC_Multistore_Stock_Api_Master extends WC_Multistore_Request {
	public function sync_stock_to( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$body = $args;
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['action'] = 'wc_multistore_child_receive_stock';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}
}