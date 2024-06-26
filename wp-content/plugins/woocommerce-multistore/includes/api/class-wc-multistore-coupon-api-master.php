<?php
/**
 * Coupon api Master handler.
 *
 * This handles coupon api master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Coupon_Api_Master
 */
class WC_Multistore_Coupon_Api_Master extends WC_Multistore_Request {

	public function send_coupon_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_update_child_coupon';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_delete_coupon_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_delete_child_coupon';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_trash_coupon_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_trash_child_coupon';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_untrash_coupon_data_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_untrash_child_coupon';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_increase_coupon_usage_count_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_increase_child_coupon_usage_count';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

	public function send_decrease_coupon_usage_count_to_child( $args, $site_id ){
		$site = WOO_MULTISTORE()->active_sites[$site_id];
		$admin_url = $site->get_url().'/wp-admin/admin-ajax.php';
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_decrease_child_coupon_usage_count';
		$body['key'] = $site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($admin_url, $args);
	}

}