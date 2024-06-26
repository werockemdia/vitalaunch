<?php
/**
 * Database handler.
 *
 * This handles database functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Database
 */
class WC_Multistore_Database {

//	public $tables = array(
//		'woo_multistore_image_metadata',
//		'woo_multistore_order_metadata'
//	);

	public function __construct(){
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }

		$this->create_tables();
	}

	public function create_tables(){
		if( is_multisite() || WOO_MULTISTORE()->site->get_type() == 'master' ){
			return;
		}

		global $wpdb;

		$table_name  = $wpdb->prefix . 'woo_multistore_global_images_data';
		$collate     = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		$exists      = $this->maybe_create_table(
			$table_name,
			"
			CREATE TABLE {$table_name} (
				global_image_id bigint(20) unsigned NOT NULL UNIQUE,
				data longtext NOT NULL DEFAULT '',
				PRIMARY KEY  (global_image_id),
				KEY (global_image_id)
			) $collate;
			"
		);
	}

	public function maybe_create_table( $table_name, $create_ddl ) {
		global $wpdb;

		foreach ( $wpdb->get_col( 'SHOW TABLES', 0 ) as $table ) {
			if ( $table === $table_name ) {
				return true;
			}
		}

		// Didn't find it, so try to create it.
		$wpdb->query( $create_ddl );

		// We cannot directly tell that whether this succeeded!
		foreach ( $wpdb->get_col( 'SHOW TABLES', 0 ) as $table ) {
			if ( $table === $table_name ) {
				return true;
			}
		}

		return false;
	}

}