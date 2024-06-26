<?php
/**
 * Ajax Order Note child handler.
 *
 * This handles ajax order note child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Order_Note_Child
 */
class WC_Multistore_Ajax_Order_Note_Child {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if( WOO_MULTISTORE()->site->get_type() == 'master' ){return;}

		add_action( 'wp_ajax_wc_multistore_create_master_order_note', array( $this, 'wc_multistore_create_master_order_note' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_create_master_order_note', array( $this, 'wc_multistore_create_master_order_note' ) );

		add_action( 'wp_ajax_wc_multistore_delete_master_order_note', array( $this, 'wc_multistore_delete_master_order_note' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_delete_master_order_note', array( $this, 'wc_multistore_delete_master_order_note' ) );
	}

	public function wc_multistore_create_master_order_note(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$order_id = $_REQUEST['data']['order_id'];
		$customer_note = $_REQUEST['data']['customer_note'];
		$comment_id = $_REQUEST['data']['comment_id'];
		$order = wc_get_order( $order_id );

		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_new_customer_order_note_added' ), 10, 2  );
		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_new_customer_order_note_added_for_original_order' ), 10, 2 );

		$note_id = $order->add_order_note( $customer_note , 1 );
		add_comment_meta( $note_id, 'wc_multistore_parent_id', $comment_id );

		add_action('woocommerce_new_customer_note', array('WC_Emails', 'send_queued_transactional_email') );
		add_action('woocommerce_new_customer_note', array('WC_Emails', 'send_transactional_email') );

		if( is_multisite() ){
			do_action('woocommerce_new_customer_note', array(	'order_id' => $order_id, 'customer_note' => $customer_note ) );
		}

		do_action('wc_multistore_customer_order_note_added_to_original_order');

		do_action( 'wc_multistore_customer_order_note_created_from_imported_order', $_REQUEST['data'], $note_id );

		$result = array(
			'status' => 'success',
			'order_note_id' => $note_id
		);

		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_delete_master_order_note(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}
		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$comment_id = $_REQUEST['data']['comment_id'];

		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_deleted_comment_for_original_order' ), 10, 2 );
		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_deleted_comment' ), 10, 2 );

		if( $wc_multistore_imported_comment_exists = wc_multistore_child_comment_exists( $comment_id  ) ){
			wp_delete_comment( $wc_multistore_imported_comment_exists );
		}

		do_action( 'wc_multistore_customer_order_note_deleted_from_original_order', $_REQUEST['data'] );

		$result = array(
			'status' => 'success',
			'comment_id' => $wc_multistore_imported_comment_exists
		);

		echo wp_json_encode($result);
		wp_die();
	}

}