<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Run uninstall logic to remove options, and leftover settings.
 */
class WOO_MSTORE_SINGLE_UNINSTALLER {

	public function __construct() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woo_multistore_global_images_data" );
	}
}

new WOO_MSTORE_SINGLE_UNINSTALLER();
