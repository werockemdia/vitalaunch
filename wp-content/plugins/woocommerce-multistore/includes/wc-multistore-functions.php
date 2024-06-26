<?php
/**
 * getallheaders may not be defined for Nginx servers
 * load the ployfill to provide a backup for nginx enviornments
 */

defined( 'ABSPATH' ) || exit;

//require_once dirname( __FILE__ ) . '/getallheaders.php';
if ( ! function_exists( 'wc_multistore_min_user_role' ) ) {
	function wc_multistore_min_user_role() {
		$can_publish = false;

		if ( is_multisite() ) {
			$master_blog = get_site_option( 'wc_multistore_master_store' );
			if ( empty( $master_blog ) || $master_blog != get_current_blog_id() ) {
				$can_publish = true;
			}
		} else {
			$master_blog = get_option( 'wc_multistore_network_type' );

			if ( empty( $master_blog ) || $master_blog != 'master' ) {
				$can_publish = true;
			}
		}


		$settings = get_site_option( 'wc_multistore_settings' );
		$user     = wp_get_current_user();

		if ( ! empty( $settings['publish-capability'] ) ) {
			$allowed_capabilities = array( $settings['publish-capability'], 'administrator' );

			if ( $settings['publish-capability'] == 'super-admin' && is_super_admin() ) {
				$can_publish = true;
			}

			if ( array_intersect( $allowed_capabilities, $user->roles ) ) {
				$can_publish = true;
			}
		} else {
			$allowed_capabilities = array( 'administrator' );

			if ( array_intersect( $allowed_capabilities, $user->roles ) ) {
				$can_publish = true;
			}
		}

		if ( ! $user->exists() ) {
			$can_publish = true;
		}

		return apply_filters( 'WOO_MSTORE/permission/user_can_publish', $can_publish, $settings );
	}
}

if ( ! function_exists( 'wc_multistore_get_sites' ) ) {
	function wc_multistore_get_sites() {
		if( is_multisite() ) {
			$get_sites_args = array(
				'number'   => 999,
				'archived' => 0,
				'spam'     => 0,
				'deleted'  => 0,
			);
			$wp_sites       = get_sites( $get_sites_args );
			$sites          = array();
			$saved_sites    = get_site_option('wc_multistore_sites', array());

			foreach ( $wp_sites as $wp_site ) {
				$master_site = get_site_option('wc_multistore_master_store');
				$site = array();

				if( isset( $saved_sites[ $wp_site->blog_id ] ) ){
					unset($saved_sites[ $wp_site->blog_id ]['name']);
					unset($saved_sites[ $wp_site->blog_id ]['url']);
					$site = $saved_sites[ $wp_site->blog_id ];
				}else{
					$site['id'] = $wp_site->blog_id;
				}

				$active_sitewide_plugins = get_site_option('active_sitewide_plugins' );
				$active_plugins = get_blog_option( $wp_site->blog_id, 'active_plugins' );
				if ( ( $active_plugins && isset( array_flip( $active_plugins ) [ 'woocommerce/woocommerce.php' ] ) ) || isset( $active_sitewide_plugins [ 'woocommerce/woocommerce.php' ] ) ) {
					$site['is_active'] = 'yes';
				} else {
					$site['is_active'] = 'no';
				}

				if( ! empty( $master_site ) && $wp_site->blog_id == $master_site ){
					continue;
				}

				$sites[ $wp_site->blog_id ] = new WC_Multistore_Site( $site );
			}
		}else{
			$wp_sites          = get_site_option('wc_multistore_sites', array());

			$sites = array();
			foreach ( $wp_sites as $key => $wp_site ) {
				$sites[ $key ] = new WC_Multistore_Site( $wp_site );
			}
		}

		return $sites;
	}
}

if ( ! function_exists( 'wc_multistore_is_admin_products_page' ) ) {
	function wc_multistore_is_admin_products_page() {
		if( ! is_admin() ){
			return false;
		}

		if( ! function_exists('get_current_screen') ){
			return false;
		}

		$screen = get_current_screen();

		if( empty($screen) || empty($screen->id) ){
			return false;
		}

		if( in_array( $screen->id, array( 'edit-product', 'woocommerce_page_woonet-woocommerce-products-network', 'multistore_page_woonet-woocommerce-products-network' ) ) ){
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wc_multistore_is_admin_product_page' ) ) {
	function wc_multistore_is_admin_product_page() {
		if( ! is_admin() ){
			return false;
		}

		if( ! function_exists('get_current_screen') ){
			return false;
		}

		$screen = get_current_screen();

		if( empty($screen) || empty($screen->id) ){
			return false;
		}

		if( $screen->id == 'product' ){
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wc_multistore_is_quick_edit' ) ) {
	function wc_multistore_is_quick_edit() {
		if ( ! empty( $_REQUEST['woocommerce_quick_edit'] ) && $_REQUEST['woocommerce_quick_edit'] == 1
		     && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'inline-save' ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wc_multistore_is_saving_order' ) ) {
	function wc_multistore_is_saving_order() {
		$is_doing_refund    = ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'woocommerce_refund_line_items' );
		$is_saving_order    = ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'woocommerce_save_order_items' );
		$is_shop_order      = ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'shop_order' && isset( $_REQUEST['action'] ) );


		if( $is_saving_order || $is_shop_order || $is_doing_refund ){
			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'getallheaders' ) ) {
	/**
	 * Get all HTTP header key/values as an associative array for the current request.
	 *
	 * @return array[string] The HTTP header key/value pairs.
	 */
	function getallheaders() {
		$headers = array();

		$copy_server = array(
			'CONTENT_TYPE'   => 'Content-Type',
			'CONTENT_LENGTH' => 'Content-Length',
			'CONTENT_MD5'    => 'Content-Md5',
		);

		foreach ( $_SERVER as $key => $value ) {
			if ( substr( $key, 0, 5 ) === 'HTTP_' ) {
				$key = substr( $key, 5 );
				if ( ! isset( $copy_server[ $key ] ) || ! isset( $_SERVER[ $key ] ) ) {
					$key             = str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', $key ) ) ) );
					$headers[ $key ] = $value;
				}
			} elseif ( isset( $copy_server[ $key ] ) ) {
				$headers[ $copy_server[ $key ] ] = $value;
			}
		}

		if ( ! isset( $headers['Authorization'] ) ) {
			if ( isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
				$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			} elseif ( isset( $_SERVER['PHP_AUTH_USER'] ) ) {
				$basic_pass               = isset( $_SERVER['PHP_AUTH_PW'] ) ? $_SERVER['PHP_AUTH_PW'] : '';
				$headers['Authorization'] = 'Basic ' . base64_encode( $_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass );
			} elseif ( isset( $_SERVER['PHP_AUTH_DIGEST'] ) ) {
				$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
			}
		}

		return $headers;
	}
}


/**
 * Fix the json response received from another server so that
 * json_decode can decode them correctly.
 */
function woomulti_json_decode( $string, $return_type = 0 ) {
	$json = json_decode( $string, $return_type );

	if ( $json === null ) {
		$json = json_decode( stripslashes( $string ), $return_type );
	}

	if ( $json === null ) {
		$string = iconv( 'UTF-8', 'ISO-8859-1//IGNORE', $string );
		$json   = json_decode( $string, $return_type );
	}

	return $json;
}


