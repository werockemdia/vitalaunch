<?php
/**
 * Child Coupon Handler
 *
 * This handles child coupon related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Coupon_Child
 */
class WC_Multistore_Coupon_Child {

	public function create($data){
		global $WC_Multistore_Coupon_Hooks_Master;
		remove_action('woocommerce_new_coupon', array( $WC_Multistore_Coupon_Hooks_Master,'sync' ) );
		remove_action('woocommerce_update_coupon', array( $WC_Multistore_Coupon_Hooks_Master,'sync' ) );

		$props = $data;
		if ( ! empty( $props['product_ids'] ) ) {
			$product_ids = array();
			foreach ( $props['product_ids'] as $key => $parent_id ){
				if( $product_id = wc_multistore_product_get_slave_product_id($parent_id, $props['product_skus'][$key]) ){
					$product_ids[] = $product_id;
				}
			}
			$props['product_ids'] = $product_ids;
		} else {
			$props['product_ids'] = array();
		}

		if ( ! empty( $props['excluded_product_ids'] ) ) {
			$excluded_product_ids = array();
			foreach ( $props['excluded_product_ids'] as $key => $parent_id ){
				if( $excluded_product_id = wc_multistore_product_get_slave_product_id($parent_id, $props['excluded_product_skus'][$key]) ){
					$excluded_product_ids[] = $excluded_product_id;
				}
			}
			$props['excluded_product_ids'] = $excluded_product_ids;
		} else {
			$props['excluded_product_ids'] = array();
		}

		if ( ! empty( $props['product_categories'] ) ) {
			$product_category_ids = array();
			foreach ( $props['product_categories'] as $parent_id ){
				if( $product_category_id = wc_multistore_get_child_term_id($parent_id) ){
					$product_category_ids[] = $product_category_id;
				}
			}
			$props['product_categories'] = $product_category_ids;
		} else {
			$props['product_categories'] = array();
		}

		if ( ! empty( $props['excluded_product_categories'] ) ) {
			$excluded_product_category_ids = array();
			foreach ( $props['excluded_product_categories'] as $parent_id ){
				if( $excluded_product_category_id = wc_multistore_get_child_term_id($parent_id) ){
					$excluded_product_category_ids[] = $excluded_product_category_id;
				}
			}
			$props['excluded_product_categories'] = $excluded_product_category_ids;
		} else {
			$props['excluded_product_categories'] = array();
		}

		$id = wc_multistore_find_child_coupon_id( $props['id'], $props['code'] );

		// Check if a mapped coupon exists.
		if ( empty( $id ) ) {
			$props['id'] = null;
			$coupon = new WC_Coupon( $props['code'] );
		} else {
			$props['id'] = $id;
			$coupon = new WC_Coupon( $id );
		}

		$coupon->set_props( $props );
		$coupon->set_excluded_product_categories( $props['excluded_product_categories'] );
		$coupon->set_product_categories( $props['product_categories'] );
		$coupon->set_product_ids( $props['product_ids'] );
		$coupon->set_excluded_product_ids( $props['excluded_product_ids'] );
		$coupon->set_description( $props['description'] );
		$coupon->set_date_created( $props['date_created'] );
		$coupon->set_date_modified( $props['date_modified'] );
		if(!empty($props['date_expires'])){
			$coupon->set_date_expires( $props['date_expires'] );
		}else{
			$coupon->set_date_expires( null );
		}
		$coupon->update_meta_data( '_woonet_master_term_id', $data['id'] );

		$coupon->save();

		add_action('woocommerce_new_coupon', array( $WC_Multistore_Coupon_Hooks_Master,'sync' ), 10, 2 );
		add_action('woocommerce_update_coupon', array( $WC_Multistore_Coupon_Hooks_Master,'sync' ), 10, 2 );
	}

	public function increase_usage_count($coupon, $new_count, $used_by) {
		$data = array(
			'master_coupon_id'   => get_post_meta($coupon->get_id(),'_woonet_master_term_id', true),
			'coupon_id'   => $coupon->get_id(),
			'coupon_code' => $coupon->get_code(),
			'update_from' => WOO_MULTISTORE()->site->get_type(),
			'new_count'   => $new_count,
			'site'        => WOO_MULTISTORE()->site->get_id(),
			'used_by'     => $used_by,
			'type'        => 'increase',
		);

		if(is_multisite()){
			switch_to_blog(get_site_option('wc_multistore_master_store'));
				update_post_meta($data['master_coupon_id'], 'usage_count', $new_count );
				$master_coupon      = new WC_Coupon( (int) $data['master_coupon_id'] );
				$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
				$wc_multistore_coupon_master->increase_usage_count($master_coupon,$new_count,$used_by);
			restore_current_blog();
		}else {
			$wc_multistore_coupon_api_child = new WC_Multistore_Coupon_Api_Child();
			$result = $wc_multistore_coupon_api_child->send_increase_coupon_usage_count_to_master($data);
		}
	}

	public function decrease_usage_count($coupon, $new_count, $used_by) {
		$data = array(
			'master_coupon_id'   => get_post_meta($coupon->get_id(),'_woonet_master_term_id', true),
			'coupon_id'   => $coupon->get_id(),
			'coupon_code' => $coupon->get_code(),
			'update_from' => WOO_MULTISTORE()->site->get_type(),
			'new_count'   => $new_count,
			'site'        => WOO_MULTISTORE()->site->get_id(),
			'used_by'     => $used_by,
			'type'        => 'decrease',
		);

		if(is_multisite()){
			switch_to_blog(get_site_option('wc_multistore_master_store'));
			update_post_meta($data['master_coupon_id'], 'usage_count', $new_count );
			$master_coupon      = new WC_Coupon( (int) $data['master_coupon_id'] );
			$wc_multistore_coupon_master = new WC_Multistore_Coupon_Master();
			$wc_multistore_coupon_master->decrease_usage_count($master_coupon,$new_count,$used_by);
			restore_current_blog();
		}else {
			$wc_multistore_coupon_api_child = new WC_Multistore_Coupon_Api_Child();
			$result = $wc_multistore_coupon_api_child->send_decrease_coupon_usage_count_to_master($data);
		}
	}


}