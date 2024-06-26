<?php

defined( 'ABSPATH' ) || exit;


if( ! function_exists('wc_multistore_get_attachment_by_url') ){
	function wc_multistore_get_attachment_by_url( $image_url ) {
		return attachment_url_to_postid($image_url);
	}
}


if( ! function_exists('wc_multistore_get_child_attachment_id') ){

	/**
	 * Returns slave attachment id or false
	 *
	 * @param int $master_attachment_id
	 *
	 * @return false|mixed|string|null
	 *
	 */
	function wc_multistore_get_child_attachment_id( $master_attachment_id ) {
		global $wpdb;

		$query = "SELECT post_id from {$wpdb->prefix}postmeta WHERE meta_key = '_woonet_master_attachment_id' AND meta_value = '{$master_attachment_id}'";

		return $wpdb->get_var( $query );
	}

}