<?php
/**
 * Ajax Coupon child handler.
 *
 * This handles ajax coupon child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Coupon_Child
 */
class WC_Multistore_Ajax_Coupon_Child {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if( WOO_MULTISTORE()->site->get_type() == 'master' ){ return; }

		add_action( 'wp_ajax_wc_multistore_update_child_coupon', array( $this, 'wc_multistore_update_child_coupon' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_update_child_coupon', array( $this, 'wc_multistore_update_child_coupon' ) );

		add_action( 'wp_ajax_wc_multistore_delete_child_coupon', array( $this, 'wc_multistore_delete_child_coupon' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_delete_child_coupon', array( $this, 'wc_multistore_delete_child_coupon' ) );

		add_action( 'wp_ajax_wc_multistore_trash_child_coupon', array( $this, 'wc_multistore_trash_child_coupon' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_trash_child_coupon', array( $this, 'wc_multistore_trash_child_coupon' ) );

		add_action( 'wp_ajax_wc_multistore_untrash_child_coupon', array( $this, 'wc_multistore_untrash_child_coupon' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_untrash_child_coupon', array( $this, 'wc_multistore_untrash_child_coupon' ) );

		add_action( 'wp_ajax_wc_multistore_increase_child_coupon_usage_count', array( $this, 'wc_multistore_increase_child_coupon_usage_count' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_increase_child_coupon_usage_count', array( $this, 'wc_multistore_increase_child_coupon_usage_count' ) );

		add_action( 'wp_ajax_wc_multistore_decrease_child_coupon_usage_count', array( $this, 'wc_multistore_decrease_child_coupon_usage_count' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_decrease_child_coupon_usage_count', array( $this, 'wc_multistore_decrease_child_coupon_usage_count' ) );
	}

	public function wc_multistore_update_child_coupon(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$wc_multistore_coupon_child = new WC_Multistore_Coupon_Child();
		$wc_multistore_coupon_child->create($_REQUEST['data']);

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'Orders successfully retrieved.',
				'coupon'  => $_REQUEST['data'],
			), null,JSON_UNESCAPED_UNICODE
		);
	}

	public function wc_multistore_delete_child_coupon(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Coupon_Hooks_Master;
		remove_action('before_delete_post', array( $WC_Multistore_Coupon_Hooks_Master,'delete' ) );

		$id = wc_multistore_get_child_coupon_id( $_REQUEST['data'] );
		wp_delete_post( $id, true );

		add_action('before_delete_post', array( $WC_Multistore_Coupon_Hooks_Master,'delete', 10, 1 ) );

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'Orders successfully retrieved.',
				'coupon'  => $_REQUEST['data'],
			), null,JSON_UNESCAPED_UNICODE
		);
	}

	public function wc_multistore_trash_child_coupon(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Coupon_Hooks_Master;
		remove_action('wp_trash_post', array( $WC_Multistore_Coupon_Hooks_Master,'trash' ) );

		$id = wc_multistore_get_child_coupon_id( $_REQUEST['data'] );
		wp_trash_post( $id );

		add_action('before_delete_post', array( $WC_Multistore_Coupon_Hooks_Master,'trash', 10, 1 ) );

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'Orders successfully retrieved.',
				'coupon'  => $_REQUEST['data'],
			), null,JSON_UNESCAPED_UNICODE
		);
	}

	public function wc_multistore_untrash_child_coupon(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Coupon_Hooks_Master;
		remove_action('wp_untrash_post', array( $WC_Multistore_Coupon_Hooks_Master,'untrash' ) );

		$id = wc_multistore_get_child_coupon_id( $_REQUEST['data'] );
		wp_untrash_post( $id );

		add_action('wp_untrash_post', array( $WC_Multistore_Coupon_Hooks_Master,'untrash', 10, 1 ) );

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'Orders successfully retrieved.',
				'coupon'  => $_REQUEST['data'],
			), null,JSON_UNESCAPED_UNICODE
		);
	}

	public function wc_multistore_increase_child_coupon_usage_count(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Coupon_Hooks_Master;
		remove_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'increase_usage_count' ) );

		$id = wc_multistore_get_child_coupon_id( $_REQUEST['data']['coupon_id'] );

		update_post_meta( $id, 'usage_count', $_REQUEST['data']['new_count'] );

		add_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'increase_usage_count' , 10, 3  ) );

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'Orders successfully retrieved.',
				'coupon'  => $_REQUEST['data'],
			), null,JSON_UNESCAPED_UNICODE
		);
	}

	public function wc_multistore_decrease_child_coupon_usage_count(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		global $WC_Multistore_Coupon_Hooks_Master;
		remove_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'decrease_usage_count' ) );

		$id = wc_multistore_get_child_coupon_id( $_REQUEST['data']['coupon_id'] );

		update_post_meta( $id, 'usage_count', $_REQUEST['data']['new_count'] );

		add_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'decrease_usage_count' , 10, 3  ) );

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'Orders successfully retrieved.',
				'coupon'  => $_REQUEST['data'],
			), null,JSON_UNESCAPED_UNICODE
		);
	}


}