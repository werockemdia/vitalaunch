<?php
/**
 * Master Coupon Handler
 *
 * This handles master coupon related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Coupon_Master
 */
class WC_Multistore_Coupon_Master {

	public function sync($coupon){
		$data = $coupon->get_data();

		if( ! empty($data['product_ids']) ){
			$data['product_skus'] = array();
			foreach ( $data['product_ids'] as $product_id ){
				$sku = get_post_meta($product_id, '_sku');
				$data['product_skus'][] = $sku;
			}
		}

		if( ! empty($data['excluded_product_ids']) ){
			$data['excluded_product_skus'] = array();
			foreach ( $data['excluded_product_ids'] as $product_id ){
				$sku = get_post_meta($product_id, '_sku');
				$data['excluded_product_skus'][] = $sku;
			}
		}

		$data[ 'date_created' ] = !empty($data['date_created']) && method_exists( $data['date_created'], 'format' ) ? $data['date_created']->format( 'Y-m-d H:i:s' ) : null;
		$data[ 'date_modified'] = !empty($data['date_modified']) && method_exists( $data['date_modified'], 'format' ) ? $data['date_modified']->format( 'Y-m-d H:i:s' ) : null;
		$data[ 'date_expires' ] = !empty($data['date_expires']) && method_exists( $data['date_expires'], 'format' ) ? $data['date_expires']->format( 'Y-m-d H:i:s' ) : null;
		unset(  $data['meta_data'] );

		foreach (WOO_MULTISTORE()->active_sites as $site){
			$this->sync_to( $data, $site->get_id() );
		}

		return $data;
	}

	public function sync_to($data, $site_id){
		if( is_multisite() ){
			switch_to_blog($site_id);
			$wc_multistore_coupon_child = new WC_Multistore_Coupon_Child();
			$wc_multistore_coupon_child->create($data);
			restore_current_blog();
		}else{
			$wc_multistore_coupon_api_master = new WC_Multistore_Coupon_Api_Master();
			$result = $wc_multistore_coupon_api_master->send_coupon_data_to_child( $data, $site_id );
		}

		return $data;
	}

	public function delete($id) {
		foreach (WOO_MULTISTORE()->active_sites as $site){
			$this->delete_to($id, $site->get_id());
		}

		return $id;
	}

	public function delete_to($id, $site_id) {
		if(is_multisite()){
			switch_to_blog($site_id);
			global $WC_Multistore_Coupon_Hooks_Master;
			remove_action('before_delete_post', array( $WC_Multistore_Coupon_Hooks_Master,'delete' ) );

			$id = wc_multistore_get_child_coupon_id( $id );
			wp_delete_post( $id, true );

			add_action('before_delete_post', array( $WC_Multistore_Coupon_Hooks_Master,'delete'), 10, 1 );

			restore_current_blog();
		}else{
			$wc_multistore_coupon_api_master = new WC_Multistore_Coupon_Api_Master();
			$result = $wc_multistore_coupon_api_master->send_delete_coupon_data_to_child( $id, $site_id );
		}
	}

	public function trash($id) {
		foreach (WOO_MULTISTORE()->active_sites as $site){
			$this->trash_to($id, $site->get_id());
		}

		return $id;
	}

	public function trash_to($id, $site_id) {
		if(is_multisite()){
			switch_to_blog($site_id);

			global $WC_Multistore_Coupon_Hooks_Master;
			remove_action('wp_trash_post', array( $WC_Multistore_Coupon_Hooks_Master,'trash' ) );

			$id = wc_multistore_get_child_coupon_id( $id );
			wp_trash_post( $id );

			add_action('wp_trash_post', array( $WC_Multistore_Coupon_Hooks_Master,'trash') , 10, 1 );

			restore_current_blog();
		}else{
			$wc_multistore_coupon_api_master = new WC_Multistore_Coupon_Api_Master();
			$result = $wc_multistore_coupon_api_master->send_trash_coupon_data_to_child( $id, $site_id );
		}
	}

	public function untrash($id) {
		foreach (WOO_MULTISTORE()->active_sites as $site){
			$this->untrash_to($id, $site->get_id());
		}

		return $id;
	}

	public function untrash_to($id, $site_id) {
		if( is_multisite() ){
			switch_to_blog($site_id);
			global $WC_Multistore_Coupon_Hooks_Master;
			remove_action('untrash_post', array( $WC_Multistore_Coupon_Hooks_Master,'untrash' ) );

			$id = wc_multistore_get_child_coupon_id( $id );
			wp_untrash_post( $id );

			add_action('untrash_post', array( $WC_Multistore_Coupon_Hooks_Master,'untrash'), 10, 1  );

			restore_current_blog();
		}else{
			$wc_multistore_coupon_api_master = new WC_Multistore_Coupon_Api_Master();
			$result = $wc_multistore_coupon_api_master->send_untrash_coupon_data_to_child( $id, $site_id );
		}
	}

	public function increase_usage_count($coupon, $new_count, $used_by) {
		$data = array(
			'coupon_id'   => $coupon->get_id(),
			'coupon_code' => $coupon->get_code(),
			'new_count'   => $new_count,
			'used_by'     => $used_by,
			'type'        => 'increase',
		);

		foreach (WOO_MULTISTORE()->active_sites as $site){
			$this->increase_usage_count_to( $data, $site->get_id() );
		}

		return $data;
	}

	public function increase_usage_count_to( $data, $site_id ) {
		if( is_multisite() ){
			switch_to_blog( $site_id );
			global $WC_Multistore_Coupon_Hooks_Master;
			global $WC_Multistore_Coupon_Hooks_Child;
			remove_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'increase_usage_count' ) );
			remove_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'increase_usage_count' ) );

			$id = wc_multistore_get_child_coupon_id( $data['coupon_id'] );

			update_post_meta( $id, 'usage_count', $data['new_count'] );

			add_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'increase_usage_count') , 10, 3  );
			add_action('woocommerce_increase_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'increase_usage_count') , 10, 3  );

			restore_current_blog();
		}else{
			$wc_multistore_coupon_api_master = new WC_Multistore_Coupon_Api_Master();
			$result = $wc_multistore_coupon_api_master->send_increase_coupon_usage_count_to_child( $data, $site_id );
		}
	}

	public function decrease_usage_count($coupon, $new_count, $used_by) {
		$data = array(
			'coupon_id'   => $coupon->get_id(),
			'coupon_code' => $coupon->get_code(),
			'new_count'   => $new_count,
			'used_by'     => $used_by,
			'type'        => 'increase',
		);

		foreach (WOO_MULTISTORE()->active_sites as $site){
			$this->decrease_usage_count_to( $data, $site->get_id() );
		}

		return $data;
	}

	public function decrease_usage_count_to( $data, $site_id ) {
		if( is_multisite() ){
			switch_to_blog( $site_id );
			global $WC_Multistore_Coupon_Hooks_Master;
			global $WC_Multistore_Coupon_Hooks_Child;
			remove_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'decrease_usage_count' ) );
			remove_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'decrease_usage_count' ) );

			$id = wc_multistore_get_child_coupon_id( $data['coupon_id'] );

			update_post_meta( $id, 'usage_count', $data['new_count'] );

			add_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Master,'decrease_usage_count') , 10, 3  );
			add_action('woocommerce_decrease_coupon_usage_count', array( $WC_Multistore_Coupon_Hooks_Child,'decrease_usage_count') , 10, 3  );

			restore_current_blog();
		}else{
			$wc_multistore_coupon_api_master = new WC_Multistore_Coupon_Api_Master();
			$result = $wc_multistore_coupon_api_master->send_decrease_coupon_usage_count_to_child( $data, $site_id );
		}
	}
}