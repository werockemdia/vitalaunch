<?php
/*
Plugin Name: WooCommerce Multisite Product Sync
Description: WooCommerce Multisite Product Sync plugin is the perfect solution for sync products (simple, grouped, virtual, downloadable, external/affiliate and variable) in your WordPress Multisite Network.
Version:     2.2.0
Author:      Obtain Infotech
Author URI:  https://www.obtaininfotech.com/
License:     GPL2
Text Domain: wcmps
*/

update_site_option( 'wcmps_licence', 1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that create database tables.
 * 'wcmps_relationships' table use for multisite relationships.
 * 'wcmps_cf' table use for store special custom fields like image, file etc...
 * 'wcmps_queue' table use for add products sync in queue.
 */ 
if ( ! function_exists( 'wcmps_plugin_activation' ) ) {
    register_activation_hook( __FILE__, 'wcmps_plugin_activation' );
    function wcmps_plugin_activation() {
        
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_wcmps_relationships = $wpdb->base_prefix . 'wcmps_relationships';
        $wcmps_relationships_sql = "CREATE TABLE $table_wcmps_relationships (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `source_item_id` bigint(20) NOT NULL,
            `source_blog_id` tinyint(4) NOT NULL,
            `destination_item_id` bigint(20) NOT NULL,
            `destination_blog_id` tinyint(4) NOT NULL,
            `relationship_id` varchar(200) NOT NULL,
            `type` varchar(20) NOT NULL,
            `type_name` varchar(200) NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        
        $table_wcmps_cf = $wpdb->base_prefix . 'wcmps_cf';
        $wcmps_cf_sql = "CREATE TABLE $table_wcmps_cf (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `filed_key` text NOT NULL,
            `field_type` varchar(150) NOT NULL,
            `field_data` text NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        
        $table_wcmps_queue = $wpdb->base_prefix . 'wcmps_queue';
        $wcmps_queue_sql = "CREATE TABLE $table_wcmps_queue (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `source_item_id` bigint(20) NOT NULL,
            `source_blog_id` tinyint(4) NOT NULL,
            `data` text NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`source_item_id`, `source_blog_id`)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $wcmps_relationships_sql );
        dbDelta( $wcmps_cf_sql );
        dbDelta( $wcmps_queue_sql );        
        
        $auto_sync = get_site_option( 'wcmps_auto_sync' );
        if ( ! $auto_sync ) {
            update_site_option( 'wcmps_auto_sync', 1 );
        }
        
        $auto_sync_type = get_site_option( 'wcmps_auto_sync_type' );
        if ( ! $auto_sync_type ) {
            update_site_option( 'wcmps_auto_sync_type', 'all-sites' );
        }
        
        $stock_sync = get_site_option( 'wcmps_stock_sync' );
        if ( ! $stock_sync ) {
            update_site_option( 'wcmps_stock_sync', 1 );
        }
        
        $stock_sync_status = get_site_option( 'wcmps_stock_sync_status' );
        if ( ! $stock_sync_status ) {
            update_site_option( 'wcmps_stock_sync_status', 'completed' );
        }
        
        $old = get_site_option( 'wcmps_old' );
        if ( ! $old ) {
            update_site_option( 'wcmps_old', 1 );
        }
    }
}

/*
 * This is a file for plugin core class.
 */
require  plugin_dir_path( __FILE__ ) . 'includes/class-wcmps.php';

/*
 * This is a function file for network settings.
 * Add network admin menu
 * Add network pages
 */
require  plugin_dir_path( __FILE__ ) . 'includes/wcmps-network.php';

/*
 * This is a file for plugin core functions.
 */
require  plugin_dir_path( __FILE__ ) . 'includes/wcmps-functions.php';

/*
 * This is a file for copier content functions.
 */
require  plugin_dir_path( __FILE__ ) . 'includes/content-copier.php';

/*
 * This is a file for extra plugins support like ACF.
 */
require  plugin_dir_path( __FILE__ ) . 'includes/extra.php';