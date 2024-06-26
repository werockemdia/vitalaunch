<?php
/**
 * Ajax Order Note master handler.
 *
 * This handles ajax order note master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Order_Note_Master
 */
class WC_Multistore_Ajax_Order_Note_Master {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){return;}

		add_action( 'wp_ajax_wc_multistore_create_order_note', array( $this, 'wc_multistore_create_order_note' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_create_order_note', array( $this, 'wc_multistore_create_order_note' ) );

		add_action( 'wp_ajax_wc_multistore_delete_order_note', array( $this, 'wc_multistore_delete_order_note' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_delete_order_note', array( $this, 'wc_multistore_delete_order_note' ) );
	}

	public function wc_multistore_create_order_note(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$order_id = wc_multistore_get_imported_order_id( $_REQUEST['data']['order_id'], $_REQUEST['data']['site_id'] );;
		$customer_note = $_REQUEST['data']['customer_note'];
		$comment_id = $_REQUEST['data']['comment_id'];
		$site_id = $_REQUEST['data']['site_id'];
		$order = wc_get_order( $order_id );

		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_new_customer_order_note_added' ), 10, 2  );
		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_new_customer_order_note_added_for_original_order' ), 10, 2 );

		$note_id = $order->add_order_note( $customer_note , 1 );

		$meta_key = 'wc_multistore_parent_id_'.$comment_id.'_sid_'.$site_id;
		add_comment_meta( $note_id, $meta_key, true );

		do_action('wc_multistore_customer_order_note_added_to_imported_order');
		do_action( 'wc_multistore_customer_order_note_created_from_original_order', $_REQUEST['data'], $note_id );

		$result = array(
			'status' => 'success',
			'order_note_id' => $note_id
		);

		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_delete_order_note(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$comment_id = $_REQUEST['data']['comment_id'];
		$site_id = $_REQUEST['data']['site_id'];

		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_deleted_comment' ), 10, 2 );
		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_deleted_comment_for_original_order' ), 10, 2 );

		if( $wc_multistore_imported_comment_exists = wc_multistore_imported_comment_exists( $comment_id, $site_id  ) ){
			wp_delete_comment( $wc_multistore_imported_comment_exists );
		}

		do_action( 'wc_multistore_customer_order_note_deleted_from_imported_order', $_REQUEST['data'] );

		$result = array(
			'status' => 'success',
			'comment_id' => $comment_id
		);

		echo wp_json_encode($result);
		wp_die();
	}

}