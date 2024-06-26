<?php
/**
 * Ajax Order master handler.
 *
 * This handles ajax order master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Order_Master
 */
class WC_Multistore_Ajax_Order_Master {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }

		add_action( 'wp_ajax_wc_multistore_import_order', array( $this, 'wc_multistore_import_order' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_import_order', array( $this, 'wc_multistore_import_order' ) );

		add_action( 'wp_ajax_wc_multistore_refund_order', array( $this, 'wc_multistore_refund_order' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_refund_order', array( $this, 'wc_multistore_refund_order' ) );

		add_action( 'wp_ajax_wc_multistore_delete_refund_order', array( $this, 'wc_multistore_delete_refund_order' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_delete_refund_order', array( $this, 'wc_multistore_delete_refund_order' ) );

		add_action( 'wp_ajax_wc_multistore_get_sequential_order_number', array( $this, 'wc_multistore_get_sequential_order_number' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_get_sequential_order_number', array( $this, 'wc_multistore_get_sequential_order_number' ) );
	}

	public function wc_multistore_import_order(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}
		if(isset($_REQUEST['data'])){
			$wc_multistore_order_child = new WC_Multistore_Order_Child($_REQUEST['data']);
		}else{
			$wc_multistore_order_child = new WC_Multistore_Order_Child($_REQUEST);
		}

		$wc_multistore_order_child->update();
		$wc_multistore_order_child->save();
		$result = array(
			'status' => 'success'
		);
		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_refund_order(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$wc_multistore_order_refund_child = new WC_Multistore_Order_Refund_Child();
		$wc_multistore_order_refund_child->refund($_REQUEST['data']);

		$result = array(
			'status' => 'success'
		);
		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_delete_refund_order(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$wc_multistore_order_refund_child = new WC_Multistore_Order_Refund_Child();
		$wc_multistore_order_refund_child->delete($_REQUEST['data']);

		$result = array(
			'status' => 'success'
		);
		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_get_sequential_order_number(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$wc_multistore_sequential_order_number = new WC_Multistore_Sequential_Order_Number();
		$order_number = $wc_multistore_sequential_order_number->get_current_sequential_order_number();

		if( WOO_MULTISTORE()->settings['enable-order-import'] == 'yes' && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'wc_multistore_import_order' ){
			update_option('wc_multistore_sequential_order_number', $order_number + 2);
		}else{
			update_option('wc_multistore_sequential_order_number', $order_number + 1);
		}

		$result = array(
			'status' => 'success',
			'order_number' => $order_number
		);
		echo wp_json_encode($result);
		wp_die();
	}

}