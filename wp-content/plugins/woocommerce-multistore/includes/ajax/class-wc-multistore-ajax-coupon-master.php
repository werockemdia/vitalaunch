<?php
/**
 * Ajax Coupon master handler.
 *
 * This handles ajax coupon master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Coupon_Master
 */
class WC_Multistore_Ajax_Coupon_Master {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){ return; }

		add_action( 'wp_ajax_wc_multistore_increase_master_coupon_usage_count', array( $this, 'wc_multistore_increase_master_coupon_usage_count' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_increase_master_coupon_usage_count', array( $this, 'wc_multistore_increase_master_coupon_usage_count' ) );

		add_action( 'wp_ajax_wc_multistore_decrease_master_coupon_usage_count', array( $this, 'wc_multistore_decrease_master_coupon_usage_count' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_decrease_master_coupon_usage_count', array( $this, 'wc_multistore_decrease_master_coupon_usage_count' ) );
	}

	

	public function wc_multistore_increase_master_coupon_usage_count(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);

			echo wp_json_encode( $result );
			wp_die();
		}

		global $WC_Multistore_Coupon_Hooks_Master;
		global $WC_Multistore_Coupon_Hooks_Child;
		remove_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'increase_usage_count' ) );
		remove_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'increase_usage_count' ) );

		$id = $_REQUEST['data']['master_coupon_id'];
		update_post_meta( $id, 'usage_count', $_REQUEST['data']['new_count'] );

		$master_coupon = new WC_Coupon( (int) $id );

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$wc_multistore_coupon_master->increase_usage_count( $master_coupon, $_REQUEST['data']['new_count'], $_REQUEST['data']['used_by'] );

		add_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'increase_usage_count' , 10, 3  ) );
		add_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'increase_usage_count' , 10, 3  ) );

		$result = array(
			'status' => 'success'
		);
		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_decrease_master_coupon_usage_count(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);

			echo wp_json_encode( $result );
			wp_die();
		}

		global $WC_Multistore_Coupon_Hooks_Master;
		global $WC_Multistore_Coupon_Hooks_Child;
		remove_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'decrease_usage_count' ) );
		remove_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'decrease_usage_count' ) );

		$id = $_REQUEST['data']['master_coupon_id'];
		update_post_meta( $id, 'usage_count', $_REQUEST['data']['new_count'] );

		$master_coupon = new WC_Coupon( (int) $id );

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$wc_multistore_coupon_master->decrease_usage_count( $master_coupon, $_REQUEST['data']['new_count'], $_REQUEST['data']['used_by'] );

		add_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'decrease_usage_count' , 10, 3  ) );
		add_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'decrease_usage_count' , 10, 3  ) );

		$result = array(
			'status' => 'success'
		);
		echo wp_json_encode($result);
		wp_die();
	}


}