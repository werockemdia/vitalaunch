<?php
/**
 * Sites api handler.
 *
 * This handles sites api related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Sites_Api
 */
class WC_Multistore_Sites_Api extends WC_Multistore_Request {
	public function update_child_settings($site,$args){
		$body = $args;
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['action'] = 'woonet_save_sites';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		$this->send($admin_url, $args);
	}
}