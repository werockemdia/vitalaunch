<?php
/**
 * Ajax Order child handler.
 *
 * This handles ajax order child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Order_Child
 */
class WC_Multistore_Ajax_Order_Child {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		add_action( 'wp_ajax_wc_multistore_update_master_order', array( $this, 'wc_multistore_update_master_order' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_update_master_order', array( $this, 'wc_multistore_update_master_order' ) );

		add_action( 'wp_ajax_wc_multistore_get_export_orders', array( $this, 'wc_multistore_get_export_orders' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_get_export_orders', array( $this, 'wc_multistore_get_export_orders' ) );

		add_action( 'wp_ajax_wc_multistore_get_orders', array( $this, 'wc_multistore_get_orders' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_get_orders', array( $this, 'wc_multistore_get_orders' ) );

		add_action( 'wp_ajax_wc_multistore_sync_order_status', array( $this, 'wc_multistore_sync_order_status' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_sync_order_status', array( $this, 'wc_multistore_sync_order_status' ) );
	}

	public function wc_multistore_update_master_order(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$wc_order = wc_get_order($_REQUEST['original_order_id']);
		$wc_multistore_master_order = new WC_Multistore_Order_Master($wc_order);
		$wc_multistore_master_order->update($_REQUEST);
	}

	public function wc_multistore_get_export_orders(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		if ( ! empty( $_POST['data'] ) ) {
			$WC_Multistore_Export_Order = new WC_Multistore_Export_Order();

			$_POST['export_format'] = $_POST['data']['export_format'];
			$_POST['export_time_after'] = $_POST['data']['export_time_after'];
			$_POST['export_time_before'] = $_POST['data']['export_time_before'];
			$_POST['site_filter'] = $_POST['data']['site_filter'];
			$_POST['order_status'] = $_POST['data']['order_status'];
			$_POST['row_format'] = $_POST['data']['row_format'];
			$_POST['export_fields'] = $_POST['data']['export_fields'];

			$WC_Multistore_Export_Order->validate_settings();

			$orders = $WC_Multistore_Export_Order->get_orders();
			$rows = array();
			foreach ($orders as $order){
				if( $_POST['row_format'] == 'row_per_product'){
//					$order = wc_get_order( $order['ID'] );
					$order_items = $order->get_items();
					if( $order_items ){
						foreach ( $order_items as $order_item ){
							$rows[] = $WC_Multistore_Export_Order->get_product_row($order, $order_item);
						}
					}
				}else{
					$rows[] = $WC_Multistore_Export_Order->get_order_row( $order );
				}
			}

			// JSON_UNESCAPED_UNICODE for chinese encoding
			wp_send_json(
				array(
					'status'  => 'success',
					'message' => 'Orders successfully retrieved.',
					'rows'  => $rows,
				), null,JSON_UNESCAPED_UNICODE
			);
		} else {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Missing parameters for ' . get_bloginfo( 'name' ),
				)
			);
		}
	}

	public function wc_multistore_get_orders(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$per_page = 10;
		$page     = 1;

		if ( ! empty( $_REQUEST['data']['per_page'] ) ) {
			$per_page = (int) $_REQUEST['data']['per_page'];
		}

		if ( ! empty( $_REQUEST['data']['page'] ) ) {
			$page = (int) $_REQUEST['data']['page'];
		}

		if ( ! empty( $_REQUEST['data']['post_status'] ) ) {
			$post_status = $_REQUEST['data']['post_status'];
		} else {
			$post_status = '';
		}

		if ( ! empty( $_REQUEST['data']['search'] ) ) {
			$search = $_REQUEST['data']['search'];
		} else {
			$search = '';
		}

		if ( ! empty( $_REQUEST['data']['post_type'] ) ) {
			$post_type = $_REQUEST['data']['post_type'];
		} else {
			$post_type = '';
		}

		$orders = wc_multistore_get_orders( $per_page, $page, $post_status, $search );

		$result = array(
			'status' => 'success',
			'orders' => $orders
		);

		wp_send_json($result);
		wp_die();
	}


	public function wc_multistore_sync_order_status(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$status_message = wc_multistore_update_orders_status( (array) $_POST['data'], WOO_MULTISTORE()->site->get_id() );

		echo json_encode(
			array(
				'status'  => $status_message['status'],
				'message' => $status_message['message'],
			)
		);

		die;
	}

}