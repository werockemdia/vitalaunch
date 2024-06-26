<?php
/**
 * Site api Master handler.
 *
 * This handles site api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Site_Api_Master
 */
class WC_Multistore_Site_Api_Master extends WC_Multistore_Request {
	public function update_child_site_settings($site,$args){
		$body = $args;
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['action'] = 'wc_multistore_update_child_site_settings';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		$this->send($admin_url, $args);
	}

	public function get_child_status($site){
		$admin_url = $site->get_url() . '/wp-admin/admin-ajax.php';
		$body['action'] = 'wc_multistore_get_child_status';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_reset_child_site($site){
		$admin_url = $site->get_url() . '/wp-admin/admin-ajax.php';
		$body['action'] = 'wc_multistore_reset_child_site';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

}