<?php

defined( 'ABSPATH' ) || exit;

if( ! function_exists('wc_multistore_update_global_image_metadata') ){
	function  wc_multistore_update_global_image_metadata( $global_image_id, $metadata ){
		global $wpdb;

		if ( ! $metadata || ! $global_image_id ) {
			return false;
		}

		$global_image_id = (int) $global_image_id;
		if ( ! $global_image_id ) {
			return false;
		}

		$table = $wpdb->prefix . 'woo_multistore_global_images_data';
		if ( ! $table ) {
			return false;
		}

		$data = array(
			'global_image_id' => $global_image_id,
			'data' => maybe_serialize($metadata)
		);

		$meta_ids = $wpdb->get_var("SELECT data FROM $table  WHERE global_image_id = $global_image_id"  );
		if ( empty( $meta_ids ) ) {
			return $wpdb->insert($table, $data, array( '%d', '%s' ) );
		}

		$where = array(
			'global_image_id' => $global_image_id,
		);

		$result = $wpdb->update( $table, $data, $where );

		if ( ! $result ) {

			return false;
		}

		return true;
	}
}

if( ! function_exists('wc_multistore_get_global_image_metadata') ){
	function  wc_multistore_get_global_image_metadata( $global_image_id ){
		global $wpdb;

		if ( ! $global_image_id ) {
			return false;
		}

		$global_image_id = (int) $global_image_id;

		$table = $wpdb->prefix . 'woo_multistore_global_images_data';

		$result = $wpdb->get_var( "SELECT data FROM $table WHERE global_image_id = $global_image_id"  );

		if ( ! $result ) {
			return false;
		}

		return maybe_unserialize($result);
	}
}