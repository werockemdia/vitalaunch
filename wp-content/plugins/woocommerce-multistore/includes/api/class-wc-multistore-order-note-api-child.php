<?php
/**
 * Order Note api Child handler.
 *
 * This handles order note api child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Note_Api_Child
 */
class WC_Multistore_Order_Note_Api_Child extends WC_Multistore_Request {

	public $admin_url;

	public $child_site;

	public function __construct() {
		$master_data = get_site_option('wc_multistore_master_connect');
		if( !empty($master_data) ){
			$this->admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
			$this->child_site = WOO_MULTISTORE()->site;
		}
	}

	public function send_order_note_data_to_master( $args ){
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_create_order_note';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function send_delete_order_note_data_to_master( $args ){
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_delete_order_note';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}


}