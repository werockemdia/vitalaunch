<?php
/**
 * Product api Child handler.
 *
 * This handles product api child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Api_Child
 */
class WC_Multistore_Product_Api_Child extends WC_Multistore_Request {

	public $admin_url;

	public $child_site;

	public function __construct() {
		$master_data = get_site_option('wc_multistore_master_connect');
		if( !empty($master_data) ){
			$this->admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
			$this->child_site = WOO_MULTISTORE()->site;
		}
	}

	public function send_delete_sync_data_from_master( $master_product_id, $master_product_sku ){
		$body['action'] = 'wc_multistore_delete_sync_data_from_master';
		$body['key'] = $this->child_site->get_id();
		$body['id'] = $master_product_id;
		$body['sku'] = $master_product_sku;

		$args = array(
			'method' => 'GET',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}


}