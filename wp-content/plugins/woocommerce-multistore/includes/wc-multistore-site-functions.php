<?php

defined( 'ABSPATH' ) || exit;

if( ! function_exists( 'wc_multistore_site_is_master' ) ){

	function wc_multistore_site_is_master(){
		$is_master_site         = WOO_MULTISTORE()->site->get_type() == 'master';

		if ( $is_master_site ) {
			return true;
		}

		return false;
	}

}

if( ! function_exists( 'wc_multistore_site_is_child' ) ){

	function wc_multistore_site_is_child(){
		$is_child_site          = WOO_MULTISTORE()->site->get_type() == 'child';

		if ( $is_child_site ) {
			return true;
		}

		return false;
	}

}

if( ! function_exists( 'wc_multistore_get_site_settings' ) ){

	function wc_multistore_get_site_settings(){
		if(is_multisite()){
			$current_site_id = get_current_blog_id();
			$sites           = WOO_MULTISTORE()->sites;
			$site            = $sites[ $current_site_id ];
		}else{
			$site = WOO_MULTISTORE()->site;
		}

		return $site->settings;
	}

}