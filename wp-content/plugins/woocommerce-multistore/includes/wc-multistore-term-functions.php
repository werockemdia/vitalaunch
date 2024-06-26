<?php

defined( 'ABSPATH' ) || exit;

if( ! function_exists( 'wc_multistore_get_child_term_id' ) ){

	function wc_multistore_get_child_term_id( $term_id ){
		global $wpdb;

		$query = "SELECT term_id FROM {$wpdb->prefix}termmeta WHERE meta_key='_woonet_master_term_id' AND meta_value={$term_id}";

		return $wpdb->get_var( $query );
	}
}