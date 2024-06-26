<?php
/**
 * Ajax Site child handler.
 *
 * This handles ajax site child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Site_Child
 */
class WC_Multistore_Ajax_Site_Child {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		if( WOO_MULTISTORE()->site->get_type() == 'master' ){
			return;
		}

		add_action( 'wp_ajax_wc_multistore_delete_master_site', array( $this, 'wc_multistore_delete_master_site') );
		add_action( 'wp_ajax_nopriv_wc_multistore_delete_master_site', array( $this, 'wc_multistore_delete_master_site') );

		add_action( 'wp_ajax_wc_multistore_connect_master_site', array( $this, 'wc_multistore_connect_master_site') );
		add_action( 'wp_ajax_nopriv_wc_multistore_connect_master_site', array( $this, 'wc_multistore_connect_master_site') );

		add_action( 'wp_ajax_nopriv_wc_multistore_get_child_status', array( $this, 'wc_multistore_get_child_status') );
		add_action( 'wp_ajax_wc_multistore_get_child_status', array( $this, 'wc_multistore_get_child_status') );

		add_action( 'wp_ajax_nopriv_wc_multistore_reset_child_site', array( $this, 'wc_multistore_reset_child_site') );
		add_action( 'wp_ajax_wc_multistore_reset_child_site', array( $this, 'wc_multistore_reset_child_site') );
	}



	public function wc_multistore_delete_master_site(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_delete_master_site' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		delete_site_option('wc_multistore_master_connect');
		$result = array(
			'status' => 'success'
		);
		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_connect_master_site(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_connect_master_site' ) ) {
			$data = array(
				'error'   => 1,
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		if ( empty( $_POST['url'] ) ) {
			echo wp_json_encode(
				array(
					'error'   => 1,
					'message' => 'No valid URL provided.',
				)
			);
			wp_die();
		}

		$wc_multistore_site_api_child = new WC_Multistore_Site_Api_Child();
		$response = $wc_multistore_site_api_child->connect_to_master_site( $_POST['url'], $_POST['nonce'] );

		if( $response['status'] == 'success' ){
			$parts = parse_url( $_POST['url'] );
			parse_str( $parts['query'], $query );

			$master_data = array(
				'key'        => $query['k'],
				'id'       => $query['id'],
				'master_url' => $parts['scheme'] . '://' . $parts['host'] . preg_replace( '/\/wp-admin\/admin-ajax.php/', '', $parts['path'] ),
			);

			update_option('wc_multistore_master_connect', $master_data );
			update_option( 'wc_multistore_setup_wizard_complete', 'yes' );

			echo wp_json_encode(
				array(
					'success' => 1,
					'message' => 'Site added to the network.',
				)
			);

			wp_die();
		}else{
			echo wp_json_encode(
				array(
					'error'   => 1,
					'message' => 'Remote failed to verify the site.',
				)
			);
			wp_die();
		}

	}

	public function wc_multistore_get_child_status(){
		if( empty( $_REQUEST['key'] ) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions',
				'code' => '403'
			);

			echo wp_json_encode( $result );
			wp_die();
		}

		$result = array(
			'status' => 'success',
			'version' => WOO_MSTORE_VERSION
		);
		echo wp_json_encode( $result );
		wp_die();
	}

	public function wc_multistore_reset_child_site(){
		if( empty( $_REQUEST['key'] ) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions',
				'code' => '403'
			);

			echo wp_json_encode( $result );
			wp_die();
		}

		global $wpdb;

		delete_option('wc_multistore_sites');
		delete_option('wc_multistore_site');
		delete_option('wc_multistore_settings');

		$products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%woonet%' ";
		$booking_products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%wc_multistore%' ";
		$terms = "DELETE FROM $wpdb->termmeta WHERE meta_key like '%woonet%' ";
		$comments = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%wc_multistore%' ";
		$comments2 = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%woonet%' ";
		$order_items = "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key like '%woonet%' ";
		$transients = "DELETE FROM $wpdb->options WHERE option_name like '%transient_wc_multistore%' ";
		$users = "DELETE FROM $wpdb->usermeta WHERE meta_key like '%woonet%' ";

		$wpdb->query($products);
		$wpdb->query($booking_products);
		$wpdb->query($terms);
		$wpdb->query($comments);
		$wpdb->query($comments2);
		$wpdb->query($order_items);
		$wpdb->query($transients);
		$wpdb->query($users);
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woo_multistore_global_images_data" );

		delete_site_option( 'wc_multistore_network_type' );
		delete_site_option( 'wc_multistore_license' );
		delete_site_option( 'wc_multistore_custom_metadata' );
		delete_site_option( 'wc_multistore_custom_taxonomy' );
		delete_site_option( 'wc_multistore_sequential_order_number' );
		delete_site_option( 'wc_multistore_child_sites_deactivated' );
		delete_site_option( 'wc_multistore_child_sites' );
		delete_site_option( 'wc_multistore_orders_export_options' );
		delete_site_option( '_transient_wc_multistore_version_check' );
		delete_site_option( '_transient_timeout_wc_multistore_version_check' );
		delete_site_option( 'wc_multistore_master_connect' );
		delete_site_option( 'wc_multistore_setup_wizard_complete' );

//		echo wp_json_encode( $result );
		wp_die();
	}


}