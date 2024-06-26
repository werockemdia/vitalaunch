<?php
/**
 * Order Note Child handler.
 *
 * This handles order notes child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Note_Hooks_Child
 */
class WC_Multistore_Order_Note_Hooks_Child {

	/**
	 * Class constructor
	 **/
	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( WOO_MULTISTORE()->site->get_type() != 'child' ){ return; }
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return; }

		$this->hooks();
	}

	/**
	 * Load hooks
	 */
	public function hooks(){
		add_filter( 'woocommerce_order_note_added', array( $this, 'on_new_customer_order_note_added_for_original_order' ), 10, 2 );
		add_filter( 'deleted_comment', array( $this, 'on_deleted_comment_for_original_order' ), 10, 2 );
	}

	/**
	 * Send a request to import order site to create the order note
	 * @param $comment_id
	 * @param $order
	 */
	public function on_new_customer_order_note_added_for_original_order( $comment_id, $order ){
		if( ! is_a( $order , 'WC_Order') ){
			return;
		}

		if( ! (bool) get_comment_meta( $comment_id, 'is_customer_note', true ) ){
			return;
		}

		$order_note = wc_get_order_note( $comment_id );
		$site_id = WOO_MULTISTORE()->site->get_id();
		$order_id = $order->get_id();

		// Data
		$data = array(
			'site_id' => $site_id,
			'order_id' => $order_id,
			'comment_id' => $comment_id,
			'customer_note' => $order_note->content,
		);

		if( is_multisite() ){
			switch_to_blog( get_site_option('wc_multistore_master_store') );
			$note_id = $this->create_customer_order_note_from_original_order_to_imported_order( $data );
			restore_current_blog();
		}else{
			$wc_multistore_order_note_api_child = new WC_Multistore_Order_Note_Api_Child();
			$result = $wc_multistore_order_note_api_child->send_order_note_data_to_master($data);
		}

	}



	/**
	 * Create order note on imported order site
	 * @param $data
	 */
	public function create_customer_order_note_from_original_order_to_imported_order( $data ){
		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$order_id = wc_multistore_get_imported_order_id( $data['order_id'], $data['site_id'] );;
		$customer_note = $data['customer_note'];
		$comment_id = $data['comment_id'];
		$site_id = $data['site_id'];
		$order = wc_get_order( $order_id );

		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_new_customer_order_note_added' ), 10, 2  );
		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_new_customer_order_note_added_for_original_order' ), 10, 2 );
		remove_action('woocommerce_new_customer_note', array('WC_Emails', 'send_queued_transactional_email') );
		remove_action('woocommerce_new_customer_note', array('WC_Emails', 'send_transactional_email') );

		$note_id = $order->add_order_note( $customer_note , 1 );

		$meta_key = 'wc_multistore_parent_id_'.$comment_id.'_sid_'.$site_id;
		add_comment_meta( $note_id, $meta_key, true );


		do_action('wc_multistore_customer_order_note_added_to_imported_order');
		do_action( 'wc_multistore_customer_order_note_created_from_original_order', $data, $note_id );

		return $note_id;
	}


	/**
	 * Send a request to import order site to delete the order note
	 * @param $comment_id
	 * @param $comment
	 */
	public function on_deleted_comment_for_original_order( $comment_id, $comment ){
		$comment_type               = $comment->comment_type;
		$order_id                   = $comment->comment_post_ID;
		$order                      = wc_get_order( $order_id );

		if( $comment_type != 'order_note' ){
			return;
		}

		if( ! is_a( $order , 'WC_Order') ){
			return;
		}


		// Data
		$data = array(
			'site_id' => WOO_MULTISTORE()->site->get_id(),
			'comment_id' => $comment_id,
		);

		if( is_multisite() ){
			switch_to_blog( get_site_option('wc_multistore_master_store') );
			$this->delete_customer_order_note_from_imported_order( $data );
			restore_current_blog();
		}else{
			$wc_multistore_order_note_api_child = new WC_Multistore_Order_Note_Api_Child();
			$result = $wc_multistore_order_note_api_child->send_delete_order_note_data_to_master($data);
		}

	}

	/**
	 * Delete order note from imported order
	 * @param $data
	 */
	public function delete_customer_order_note_from_imported_order($data){
		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$comment_id = $data['comment_id'];
		$site_id = $data['site_id'];

		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_deleted_comment_for_original_order' ), 10, 2 );
		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_deleted_comment' ), 10, 2 );

		if( $wc_multistore_imported_comment_exists = wc_multistore_imported_comment_exists( $comment_id, $site_id  ) ){
			wp_delete_comment( $wc_multistore_imported_comment_exists );
		}

		do_action( 'wc_multistore_customer_order_note_deleted_from_imported_order', $data );
	}


}