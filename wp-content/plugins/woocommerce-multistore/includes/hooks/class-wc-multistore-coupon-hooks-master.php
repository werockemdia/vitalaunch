<?php
/**
 * Master coupon hooks handler.
 *
 * This handles master coupon hooks related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Coupon_Hooks_Master
 **/
class WC_Multistore_Coupon_Hooks_Master {

	public $settings;

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){ return; }
		$this->settings = WOO_MULTISTORE()->settings;
		$this->hooks();
	}

	public function hooks(){
		add_action('woocommerce_new_coupon', array( $this,'sync' ), 10, 2 );
		add_action('woocommerce_update_coupon', array( $this,'sync' ), 10, 2 );
		add_action('before_delete_post', array( $this,'delete' ), 10, 1 );
		add_action('wp_trash_post', array( $this,'trash' ), 10, 1 );
		add_action('untrash_post', array( $this,'untrash' ), 10, 1 );

		add_action('woocommerce_increase_coupon_usage_count', array( $this,'increase_usage_count' ), 10, 3 );
		add_action('woocommerce_decrease_coupon_usage_count', array( $this,'decrease_usage_count' ), 10, 3 );
	}

	public function sync( $id, $coupon ) {
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$result = $wc_multistore_coupon_master->sync($coupon);
	}

	public function delete($id){
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		if (get_post_type( $id ) != 'shop_coupon' ) {
			return;
		}

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$result = $wc_multistore_coupon_master->delete($id);
	}

	public function trash($id){
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		if (get_post_type( $id ) != 'shop_coupon' ) {
			return;
		}

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$result = $wc_multistore_coupon_master->trash($id);
	}

	public function untrash($id){
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		if (get_post_type( $id ) != 'shop_coupon' ) {
			return;
		}

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$result = $wc_multistore_coupon_master->untrash($id);
	}

	public function increase_usage_count( $coupon, $new_count, $used_by ) {
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$result = $wc_multistore_coupon_master->increase_usage_count($coupon, $new_count, $used_by);
	}

	public function decrease_usage_count( $coupon, $new_count, $used_by ) {
		if ( WOO_MULTISTORE()->settings['sync-coupons'] != 'yes' ) {
			return;
		}

		$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
		$result = $wc_multistore_coupon_master->decrease_usage_count($coupon, $new_count, $used_by);
	}

	/**
	 * Disable Hooks
	 */
	public function disable_hooks(){
		remove_action('woocommerce_new_coupon', array( $this,'sync' ) );
		remove_action('woocommerce_update_coupon', array( $this,'sync' ) );
		remove_action('woocommerce_delete_coupon', array( $this,'delete' ) );
		remove_action('woocommerce_trash_coupon', array( $this,'trash' ) );
		remove_action('untrash_post', array( $this,'untrash' ) );
	}
}