<?php
/**
 * Site api child handler.
 *
 * This handles site api child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Site_Api_Child
 */
class WC_Multistore_Site_Api_Child extends WC_Multistore_Request {

	public $admin_url;

	public $child_site;

	public function __construct() {
		$master_data = get_site_option('wc_multistore_master_connect');
		if( !empty($master_data) ){
			$this->admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
			$this->child_site = WOO_MULTISTORE()->site;
		}
	}

	public function get_master_status(){
		$body['action'] = 'wc_multistore_get_master_status';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'GET',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function connect_to_master_site( $url, $key ){
		$body['action'] = 'wc_multistore_connect_child_site';
		$body['key'] = $key;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send($url, $args);
	}

	public function get_master_license_data(){
		$body['action'] = 'wc_multistore_get_master_license_data';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'GET',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}


	public function send_reset_child_site_from_master(){
		$body['action'] = 'wc_multistore_reset_child_site_from_master';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'GET',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

}