<?php
/**
 * Ajax Product child handler.
 *
 * This handles ajax product child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Product_Child
 */
class WC_Multistore_Ajax_Product_Child {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if( WOO_MULTISTORE()->site->get_type() != 'child' ){ return; }

		add_action( 'wp_ajax_nopriv_wc_multistore_update_child_product', array( $this, 'wc_multistore_update_child_product') );
		add_action( 'wp_ajax_wc_multistore_update_child_product', array( $this, 'wc_multistore_update_child_product') );

		add_action( 'wp_ajax_wc_multistore_trash_child_product', array( $this, 'wc_multistore_trash_child_product') );
		add_action( 'wp_ajax_nopriv_wc_multistore_trash_child_product', array( $this, 'wc_multistore_trash_child_product') );

		add_action( 'wp_ajax_wc_multistore_untrash_child_product', array( $this, 'wc_multistore_untrash_child_product') );
		add_action( 'wp_ajax_nopriv_wc_multistore_untrash_child_product', array( $this, 'wc_multistore_untrash_child_product') );

		add_action( 'wp_ajax_wc_multistore_delete_child_product', array( $this, 'wc_multistore_delete_child_product') );
		add_action( 'wp_ajax_nopriv_wc_multistore_delete_child_product', array( $this, 'wc_multistore_delete_child_product') );
	}

	public function wc_multistore_update_child_product(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$data = json_decode( stripslashes( $_REQUEST['data'] ), true );
		$classname = wc_multistore_get_product_class_name( 'child', $data['product_type'] );

		if( ! $classname ){
			$result = array(
				'status' => 'failed',
				'message' => 'Invalid Product Type'
			);

			wp_send_json($result);
			wp_die();
		}

		$wc_multistore_child_product = new $classname( $data );
		$wc_multistore_child_product->update();

		$result = $wc_multistore_child_product->save();

		wp_send_json($result);
		wp_die();
	}

	public function wc_multistore_trash_child_product(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$data = $_REQUEST['data'];

		$classname = wc_multistore_get_product_class_name( 'child', $data['product_type'] );

		if( ! $classname ){
			$result = array(
				'status' => 'failed',
				'message' => 'Invalid Product Type'
			);

			wp_send_json($result);
			wp_die();
		}

		$wc_multistore_child_product = new $classname( $data );

		$result = $wc_multistore_child_product->trash();

		wp_send_json($result);
		wp_die();
	}

	public function wc_multistore_untrash_child_product(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$data = $_REQUEST['data'];

		$classname = wc_multistore_get_product_class_name( 'child', $data['product_type'] );

		if( ! $classname ){
			$result = array(
				'status' => 'failed',
				'message' => 'Invalid Product Type'
			);

			wp_send_json($result);
			wp_die();
		}

		$wc_multistore_child_product = new $classname( $data );

		$result = $wc_multistore_child_product->untrash();

		wp_send_json($result);
		wp_die();
	}

	public function wc_multistore_delete_child_product(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$product_id = $_REQUEST['data']['product_id'];
		$product_sku = $_REQUEST['data']['product_sku'];
		$child_product_id = wc_multistore_product_get_slave_product_id($product_id, $product_sku);
		$wc_product = wc_get_product($child_product_id);
		$classname = wc_multistore_get_product_class_name( 'child', $wc_product->get_type() );

		if( ! $classname ){
			$result = array(
				'status' => 'failed',
				'message' => 'Invalid Product Type'
			);

			wp_send_json($result);
			wp_die();
		}

		$wc_multistore_child_product = new $classname( $wc_product );

		$result = $wc_multistore_child_product->delete();

		wp_send_json($result);
		wp_die();
	}
}