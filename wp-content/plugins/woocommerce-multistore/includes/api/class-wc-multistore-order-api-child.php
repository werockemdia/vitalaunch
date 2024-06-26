<?php
/**
 * Order api Child handler.
 *
 * This handles order api child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Api_Child
 */
class WC_Multistore_Order_Api_Child extends WC_Multistore_Request {

	public $admin_url;

	public $child_site;

	public function __construct() {

		if( is_multisite() ){
			$master_store = get_site_option('wc_multistore_master_store');
			$this->child_site = WOO_MULTISTORE()->site;

			switch_to_blog($master_store);

			if ( class_exists( 'SitePress' ) ) {
				global $sitepress;
				$has_wpml_filter = remove_filter( 'pre_option_home', array( $sitepress, 'pre_option_home' ) );
			}

			$this->admin_url = get_bloginfo('url') . '/wp-admin/admin-ajax.php';

			if ( class_exists( 'SitePress' ) ) {
				if ( $has_wpml_filter ) {
					global $sitepress;
					add_filter( 'pre_option_home', array( $sitepress, 'pre_option_home' ) );
				}
			}

			restore_current_blog();
		}else{
			$master_data = get_site_option('wc_multistore_master_connect');
			if( !empty($master_data) ){
				$this->admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
				$this->child_site = WOO_MULTISTORE()->site;
			}
		}

	}

	public function send_order_data_to_master( $args ){
		$body['action'] = 'wc_multistore_import_order';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function send_refund_order_data_to_master( $args, $site_id ){
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_refund_order';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function send_delete_refund_order_data_to_master( $args, $site_id ){
		$body['data'] = $args;
		$body['action'] = 'wc_multistore_delete_refund_order';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function get_sequential_order_number(){
		$body['action'] = 'wc_multistore_get_sequential_order_number';
		$body['key'] = $this->child_site->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}
}