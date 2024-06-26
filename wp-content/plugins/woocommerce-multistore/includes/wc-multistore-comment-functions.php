<?php

defined( 'ABSPATH' ) || exit;

if( ! function_exists( 'wc_multistore_child_comment_exists' ) ){

	function wc_multistore_child_comment_exists( $parent_id ){
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT comment_ID FROM $wpdb->commentmeta WHERE meta_key = 'wc_multistore_parent_id' AND meta_value = %s;", $parent_id
			)
		);
	}

}

if( ! function_exists( 'wc_multistore_get_child_comment_id' ) ){

	function wc_multistore_get_child_comment_id( $comment_id, $site_id ){
		global $wpdb;

		$meta_key = 'wc_multistore_parent_id_'.$comment_id.'_sid_'.$site_id;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT comment_ID FROM $wpdb->commentmeta WHERE meta_key = %s;", $meta_key
			)
		);
	}

}

if( ! function_exists( 'wc_multistore_imported_comment_exists' ) ){

	function wc_multistore_imported_comment_exists( $comment_id, $site_id ){
		global $wpdb;

		$meta_key = 'wc_multistore_parent_id_'.$comment_id.'_sid_'.$site_id;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT comment_ID FROM $wpdb->commentmeta WHERE meta_key = %s;", $meta_key
			)
		);
	}

}