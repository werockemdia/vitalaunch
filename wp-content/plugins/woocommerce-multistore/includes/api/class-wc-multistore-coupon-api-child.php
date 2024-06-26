<?php
/**
 * Coupon api Child handler.
 *
 * This handles coupon api child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Coupon_Api_Child
 */
class WC_Multistore_Coupon_Api_Child extends WC_Multistore_Request {

	public $admin_url;

	public $child_site;

	public function __construct() {

		if( is_multisite() ){
			$master_store = get_site_option('wc_multistore_master_store');
			$this->child_site = WOO_MULTISTORE()->site;

			switch_to_blog($master_store);
			$this->admin_url = get_bloginfo('url') . '/wp-admin/admin-ajax.php';
			restore_current_blog();
		}else{
			$master_data = get_site_option('wc_multistore_master_connect');
			if( !empty($master_data) ){
				$this->admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
				$this->child_site = WOO_MULTISTORE()->site;
			}
		}

	}

	public function send_increase_coupon_usage_count_to_master( $args ){
		$body['action'] = 'wc_multistore_increase_master_coupon_usage_count';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function send_decrease_coupon_usage_count_to_master( $args ){
		$body['action'] = 'wc_multistore_decrease_master_coupon_usage_count';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

}