<?php

defined( 'ABSPATH' ) || exit;

if( ! function_exists('wc_multistore_get_child_product_attribute') ) {

	function wc_multistore_get_child_product_attribute( $name ) {
		if( empty( $name ) ){
			return false;
		}

		$name = apply_filters('wc_multistore_before_get_child_attribute', $name );

		global $wpdb;

		return $wpdb->get_var( "SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '{$name}'" );
	}
}

if( ! function_exists('wc_multistore_create_child_product_attribute') ) {

	function wc_multistore_create_child_product_attribute( $attribute ) {
		if( empty( $attribute ) ){
			return false;
		}
		global $wpdb;

		$args = array(
			'attribute_label'   => $attribute['attribute_label'],
			'attribute_name'    => $attribute['attribute_name'],
			'attribute_type'    => $attribute['attribute_type'],
			'attribute_orderby' => $attribute['attribute_orderby'],
			'attribute_public'  => $attribute['attribute_public'],
		);

		$args = apply_filters('wc_multistore_before_create_child_attribute', $args );

		$results = $wpdb->insert(
			$wpdb->prefix . 'woocommerce_attribute_taxonomies',
			$args,
			array( '%s', '%s', '%s', '%s', '%d' )
		);

		if ( is_wp_error( $results ) ) {
			return new WP_Error(
				'cannot_create_attribute',
				$results->get_error_message(),
				array( 'status' => 400 )
			);
		}

		$id = $wpdb->insert_id;

		do_action( 'woocommerce_api_create_product_attribute', $id, $args );

		// Clear transients
		delete_transient( 'wc_attribute_taxonomies' );
		WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );

		if ( ! taxonomy_exists( $attribute['name'] ) ) {
			register_taxonomy(
				$attribute['name'],
				array('product', 'product_variation'),
				array(
					'hierarchical' => false,
					'label'        => ucfirst( $attribute['attribute_label'] ),
					'query_var'    => true,
					'rewrite'      => array( 'slug' => sanitize_title( $attribute['name'] ) ), // The base slug
				)
			);
		}

		return $wpdb->insert_id;
	}
}