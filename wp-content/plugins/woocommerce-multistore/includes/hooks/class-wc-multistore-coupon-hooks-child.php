<?php
/**
 * Child coupon hooks handler.
 *
 * This handles child coupon hooks related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Coupon_Hooks_Child
 **/
class WC_Multistore_Coupon_Hooks_Child {

	public $settings;

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( WOO_MULTISTORE()->site->get_type() == 'master' ){ return; }
		$this->settings = WOO_MULTISTORE()->settings;

		$this->hooks();
	}

	public function hooks(){
		add_action('woocommerce_increase_coupon_usage_count', array( $this,'increase_usage_count' ), 10, 3 );
		add_action('woocommerce_decrease_coupon_usage_count', array( $this,'decrease_usage_count' ), 10, 3 );
	}


	public function increase_usage_count( $coupon, $new_count, $used_by ) {
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		if( ! wc_multistore_is_child_coupon($coupon->get_id()) ){
			return;
		}

		$wc_multistore_coupon_child = new WC_Multistore_Coupon_Child();
		$result = $wc_multistore_coupon_child->increase_usage_count($coupon, $new_count, $used_by);
	}

	public function decrease_usage_count( $coupon, $new_count, $used_by ) {
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		if( ! wc_multistore_is_child_coupon( $coupon->get_id() ) ){
			return;
		}

		$wc_multistore_coupon_child = new WC_Multistore_Coupon_Child();
		$result = $wc_multistore_coupon_child->decrease_usage_count($coupon, $new_count, $used_by);
	}

}