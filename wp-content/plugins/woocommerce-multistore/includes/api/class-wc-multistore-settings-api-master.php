<?php
/**
 * Settings api master handler.
 *
 * This handles settings api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Settings_Api_Master
 */
class WC_Multistore_Settings_Api_Master extends WC_Multistore_Request {

	public function send_settings_to_child($site,$args){
		$body = $args;
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['action'] = 'wc_multistore_save_child_settings';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

}