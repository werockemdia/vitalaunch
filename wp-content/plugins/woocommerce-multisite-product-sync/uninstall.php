<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

/*
 * Deleted options when plugin uninstall.
 */
delete_site_option( 'wcmps_auto_sync' );
delete_site_option( 'wcmps_auto_sync_type' );
delete_site_option( 'wcmps_auto_sync_sub_blogs' );
delete_site_option( 'wcmps_auto_sync_main_blog' );
delete_site_option( 'wcmps_stock_sync' );
delete_site_option( 'wcmps_old' );
delete_site_option( 'wcmps_product_delete' );
delete_site_option( 'wcmps_exclude_product_meta_data' );