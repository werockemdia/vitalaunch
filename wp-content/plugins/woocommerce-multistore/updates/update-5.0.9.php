<?php defined( 'ABSPATH' ) || exit;


echo '<p>' . __( 'Applying update 5.0.9', 'woonet' ) . '</p>';
wc_multistore_migrate_global_images_single_child_5_0_9();
echo '<p>' . __( 'Applied update 5.0.9', 'woonet' ) . '</p>';


function wc_multistore_migrate_global_images_single_child_5_0_9(){
	$global_images = get_site_option('wc_multistore_global_images', array());

	if( ! empty( $global_images ) && wc_multistore_create_global_images_table() ){
		foreach ( $global_images as $global_image_id => $data ){
			if( ! empty($global_image_id) && ! empty( $data ) ){

				wc_multistore_update_global_image_metadata($global_image_id, $data);
			}
		}
	}

	delete_site_option('wc_multistore_global_images');
}


function wc_multistore_create_global_images_table(){
	global $wpdb;

	$table_name  = $wpdb->prefix . 'woo_multistore_global_images_data';
	$collate     = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

	$exists      = wc_multistore_maybe_create_table(
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

	return $exists;
}

function wc_multistore_maybe_create_table( $table_name, $create_ddl  ){
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
