<?php

defined( 'ABSPATH' ) || exit;

if( ! function_exists( 'wc_multistore_find_child_coupon_id' ) ){

	function wc_multistore_find_child_coupon_id( $parent_id, $code ){
		global $wpdb;

		$id =  $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_woonet_master_term_id' AND meta_value = %s;", $parent_id
			)
		);

		if( empty($id) ){
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1;", $code ) );
		}

		return $id;
	}

}

if( ! function_exists( 'wc_multistore_get_child_coupon_id' ) ){

	function wc_multistore_get_child_coupon_id( $parent_id ){
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_woonet_master_term_id' AND meta_value = %s;", $parent_id
			)
		);

	}

}

if( ! function_exists( 'wc_multistore_is_child_coupon' ) ){

	function wc_multistore_is_child_coupon( $coupon_id ){
		return get_post_meta($coupon_id, '_woonet_master_term_id', true );
	}

}
