<?php
/**
 * Order Note Master handler.
 *
 * This handles master order notes related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Note_Hooks_Master
 */
class WC_Multistore_Order_Note_Hooks_Master {

	/**
	 * Class constructor
	 **/
	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){ return; }
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return; }

		$this->hooks();
	}

	/**
	 * Load hooks
	 */
	public function hooks(){
		add_filter( 'woocommerce_order_note_added', array( $this, 'on_new_customer_order_note_added' ), 10, 2 );
		add_filter( 'woocommerce_new_order_note_data', array( $this, 'disable_new_order_note_email' ) );
		add_filter( 'deleted_comment', array( $this, 'on_deleted_comment' ), 10, 2 );
	}

	/**
	 * Send a request to original order site to create the order note
	 * @param $comment_id
	 * @param $order
	 */
	public function on_new_customer_order_note_added( $comment_id, $order ){
		if( ! is_a( $order , 'WC_Order') ){
			return;
		}

		if( ! wc_multistore_order_is_imported( $order ) ){
			return;
		}

		if( ! (bool) get_comment_meta( $comment_id, 'is_customer_note', true ) ){
			return;
		}

		$order_note = wc_get_order_note( $comment_id );
		$site_id = wc_multistore_get_master_site_of_the_order( $order );
		$order_id = wc_multistore_get_original_order_id( $order );

		if( ! isset( WOO_MULTISTORE()->active_sites[$site_id] ) || ! WOO_MULTISTORE()->active_sites[$site_id] ){
			return;
		}

		// Data
		$data = array(
			'site_id' => $site_id,
			'order_id' => $order_id,
			'comment_id' => $comment_id,
			'customer_note' => $order_note->content,
		);

		if(is_multisite()){
			switch_to_blog( $site_id );
			$note_id = $this->create_customer_order_note_from_imported_order_to_original_order( $data );
			restore_current_blog();
		}else{
			$wc_multistore_order_note_api_master = new WC_Multistore_Order_Note_Api_Master();
			$result = $wc_multistore_order_note_api_master->send_create_order_note_data_to_child($data, $site_id);
		}

	}



	/**
	 * Create order note on original order site
	 * @param $data
	 */
	public function create_customer_order_note_from_imported_order_to_original_order( $data ){
		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$order_id = $data['order_id'];
		$customer_note = $data['customer_note'];
		$comment_id = $data['comment_id'];
		$order = wc_get_order( $order_id );

		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_new_customer_order_note_added' ), 10, 2  );
		remove_action('woocommerce_order_note_added', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_new_customer_order_note_added_for_original_order' ), 10, 2 );

		$note_id = $order->add_order_note( $customer_note , 1 );
		add_comment_meta( $note_id, 'wc_multistore_parent_id', $comment_id );

		add_action('woocommerce_new_customer_note', array('WC_Emails', 'send_queued_transactional_email') );
		add_action('woocommerce_new_customer_note', array('WC_Emails', 'send_transactional_email') );
		do_action('woocommerce_new_customer_note', array(	'order_id' => $order_id, 'customer_note' => $customer_note ) );

		do_action('wc_multistore_customer_order_note_added_to_original_order');

		do_action( 'wc_multistore_customer_order_note_created_from_imported_order', $data, $note_id );

		return $note_id;
	}



	/**
	 * Send a request to original order site to delete the order note
	 * @param $comment_id
	 * @param $comment
	 */
	public function on_deleted_comment( $comment_id, $comment ){
		$comment_type               = $comment->comment_type;
		$order_id                   = $comment->comment_post_ID;
		$order                      = wc_get_order( $order_id );

		if( $comment_type != 'order_note' ){
			return;
		}

		if( ! is_a( $order , 'WC_Order') ){
			return;
		}

		if( ! wc_multistore_order_is_imported( $order ) ){
			return;
		}

		// Data
		$data = array(
			'site_id' => wc_multistore_get_master_site_of_the_order( $order ),
			'comment_id' => $comment_id,
		);

		if( ! isset( WOO_MULTISTORE()->active_sites[$data['site_id']] ) || ! WOO_MULTISTORE()->active_sites[$data['site_id']] ){
			return;
		}

		if(is_multisite()){
			switch_to_blog( wc_multistore_get_master_site_of_the_order( $order ) );
			$this->delete_customer_order_note( $data );
			restore_current_blog();
		}else{
			$wc_multistore_order_note_api_master = new WC_Multistore_Order_Note_Api_Master();
			$result = $wc_multistore_order_note_api_master->send_delete_order_note_data_to_child($data, $data['site_id'] );
		}

	}

	/**
	 * Delete order note on original order site
	 * @param $data
	 */
	public function delete_customer_order_note($data){
		global $WC_Multistore_Order_Note_Hooks_Master;
		global $WC_Multistore_Order_Note_Hooks_Child;
		$comment_id = $data['comment_id'];

		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Master, 'on_deleted_comment' ), 10, 2 );
		remove_filter( 'deleted_comment', array( $WC_Multistore_Order_Note_Hooks_Child, 'on_deleted_comment_for_original_order' ), 10, 2 );

		if( $wc_multistore_child_comment_exists = wc_multistore_child_comment_exists( $comment_id ) ){
			wp_delete_comment( $wc_multistore_child_comment_exists );
		}

		do_action( 'wc_multistore_customer_order_note_deleted_from_original_order', $data );
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public function disable_new_order_note_email( $data ){
		$order_id   = $data['comment_post_ID'];
		$order      = wc_get_order( $order_id );

		if( ! is_a( $order , 'WC_Order') ){
			return $data;
		}

		if( wc_multistore_order_is_imported( $order ) ){
			remove_action('woocommerce_new_customer_note', array('WC_Emails', 'send_queued_transactional_email') );
			remove_action('woocommerce_new_customer_note', array('WC_Emails', 'send_transactional_email') );
		}

		return $data;
	}
}