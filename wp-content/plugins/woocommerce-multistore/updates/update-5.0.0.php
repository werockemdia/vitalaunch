<?php defined( 'ABSPATH' ) || exit;

echo '<p>' . __( 'Applying update 5.0.0', 'woonet' ) . '</p>';

if( is_multisite() ){
	$master_store = get_site_option('wc_multistore_master_store');

	echo '<p>' . __( 'Applying update 5.0.0 to products', 'woonet' ) . '</p>';
	wc_multistore_migrate_product_settings_multisite_5_0_0($master_store);
	echo '<p>' . __( 'Applied update 5.0.0 to products', 'woonet' ) . '</p>';


	echo '<p>' . __( 'Applying update 5.0.0 to terms', 'woonet' ) . '</p>';
	wc_multistore_migrate_term_settings_multisite_5_0_0( $master_store );
	echo '<p>' . __( 'Applied update 5.0.0 to terms', 'woonet' ) . '</p>';

	//	 images_mapping
	echo '<p>' . __( 'Applying update 5.0.0 to images', 'woonet' ) . '</p>';
	wc_multistore_migrate_image_settings_multisite_5_0_0( $master_store );
	echo '<p>' . __( 'Applied update 5.0.0 to images', 'woonet' ) . '</p>';
}else{
	$network_type = get_site_option('wc_multistore_network_type' );

	if( $network_type == 'master' ){
		echo '<p>' . __( 'Applying update 5.0.0 to products', 'woonet' ) . '</p>';
		wc_multistore_migrate_product_settings_single_master_5_0_0();
		echo '<p>' . __( 'Applied update 5.0.0 to products', 'woonet' ) . '</p>';
	}elseif ($network_type == 'child'){
		echo '<p>' . __( 'Applying update 5.0.0 to products', 'woonet' ) . '</p>';
		wc_multistore_migrate_product_settings_single_child_5_0_0();
		echo '<p>' . __( 'Applied update 5.0.0 to products', 'woonet' ) . '</p>';
	}
}


function wc_multistore_migrate_product_settings_multisite_5_0_0( $master_store ){
    global $wpdb;
	$stores = array();
	foreach ( WOO_MULTISTORE()->sites as $site ){
		switch_to_blog($site->get_id());
		$query = "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_woonet_network_is_child_site_id' AND meta_value = '$master_store'";
		$result = $wpdb->get_results($query);
		restore_current_blog();
		$stores[$site->get_id()] = $result;
	}


	if( ! empty( $stores ) ){
		foreach ( $stores as $site_id => $product_ids ){
			if( ! empty( $product_ids ) ) {
				switch_to_blog( $site_id );
				foreach ( $product_ids as $product_id ) {
					$inherit_query            = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_woonet_child_inherit_updates' AND post_id = '$product_id->post_id'";
					$stock_query              = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_woonet_child_stock_synchronize' AND post_id = '$product_id->post_id'";
					$master_product_id_query  = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_woonet_network_is_child_product_id' AND post_id = '$product_id->post_id'";
					$master_product_sku_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_woonet_network_is_child_product_sku' AND post_id = '$product_id->post_id'";

					$inherit_value      = $wpdb->get_var( $inherit_query );
					$stock_value        = $wpdb->get_var( $stock_query );
					$master_product_id  = $wpdb->get_var( $master_product_id_query );
					$master_product_sku = $wpdb->get_var( $master_product_sku_query );

					$meta_key_publish = '_woonet_publish_to_' . $site_id;
					$meta_key_inherit = '_woonet_publish_to_' . $site_id . '_child_inheir';
					$meta_key_stock   = '_woonet_' . $site_id . '_child_stock_synchronize';

					$delete_child_site_query  = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key ='_woonet_network_is_child_site_id' AND post_id = '$product_id->post_id'";
					$delete_child_site_query2 = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key ='_woonet_child_inherit_updates' AND post_id = '$product_id->post_id'";
					$delete_child_site_query3 = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key ='_woonet_child_stock_synchronize' AND post_id = '$product_id->post_id'";
					$wpdb->query( $delete_child_site_query );
					$wpdb->query( $delete_child_site_query2 );
					$wpdb->query( $delete_child_site_query3 );

					switch_to_blog( $master_store );
					$settings = get_site_option( 'wc_multistore_settings' );
					if ( $settings['sync-by-sku'] == 'yes' ) {
						$master_id_query   = "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_sku' AND meta_value = '$master_product_sku' ";
						$master_product_id = $wpdb->get_var( $master_id_query );
					}

					if( ! empty( $master_product_id ) ){
						$product_settings = get_post_meta( $master_product_id, '_woonet_settings', true );
						if ( empty( $product_settings ) ) {
							$product_settings = array();
						}

						$product_settings[ $meta_key_publish ] = get_post_meta( $master_product_id, $meta_key_publish, true );
						$product_settings[ $meta_key_inherit ] = $inherit_value;
						$product_settings[ $meta_key_stock ]   = $stock_value;

						update_post_meta( $master_product_id, '_woonet_settings', $product_settings );
						delete_post_meta( $master_product_id, $meta_key_publish );
					}

					restore_current_blog();

				}
				restore_current_blog();
			}
		}
	}
}

function wc_multistore_migrate_term_settings_multisite_5_0_0( $master_store ){
	foreach ( WOO_MULTISTORE()->sites as $site ){
		switch_to_blog($site->get_id());
		$terms = get_option('terms_mapping');

		if(isset($terms[$master_store])){
			foreach ( $terms[$master_store] as $master_term_id => $child_term ){
                update_term_meta( $child_term, '_woonet_master_term_id', $master_term_id );
			}
		}
		delete_option('terms_mapping');
		restore_current_blog();
	}
}

function wc_multistore_migrate_image_settings_multisite_5_0_0( $master_store ){
	foreach ( WOO_MULTISTORE()->sites as $site ){
		switch_to_blog($site->get_id());
		$images = get_option('images_mapping');
		if(isset($images[$master_store])){
			foreach ( $images[$master_store] as $master_attachment_id => $child_attachment ){
                update_post_meta( $child_attachment, '_woonet_master_attachment_id', $master_attachment_id );
			}
		}
		delete_option('images_mapping');
		restore_current_blog();
	}
}


function wc_multistore_migrate_product_settings_single_master_5_0_0(){
	global $wpdb;

	$temp_sites = get_site_option('wc_multistore_sites2');

	$products_query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product'";


	$products = $wpdb->get_results($products_query);

	if( ! empty( $products ) ){
		foreach ( $products as $std_class ){
			$product_id = $std_class->ID;
			$product_settings = array();
			foreach ( WOO_MULTISTORE()->sites as $site ){
				$meta_key_publish = '_woonet_publish_to_' . $temp_sites[$site->get_id()]['uuid'];
				$meta_key_inherit = '_woonet_publish_to_' . $temp_sites[$site->get_id()]['uuid'] . '_child_inheir';
				$meta_key_stock = '_woonet_' . $temp_sites[$site->get_id()]['uuid'] . '_child_stock_synchronize';

				$meta_key_publish2 = '_woonet_publish_to_' . $site->get_id();
				$meta_key_inherit2 = '_woonet_publish_to_' . $site->get_id() . '_child_inheir';
				$meta_key_stock2 = '_woonet_' . $site->get_id() . '_child_stock_synchronize';


				$publish_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_publish' AND post_id = {$product_id}";
				$inherit_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_inherit' AND post_id = {$product_id}";
				$stock_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_stock' AND post_id = {$product_id}";

				$publish_value = $wpdb->get_var($publish_query);
				$inherit_value = $wpdb->get_var($inherit_query);
				$stock_value =  $wpdb->get_var($stock_query);


				if( ! empty($publish_value) ){
					$product_settings[$meta_key_publish2] = $publish_value;
					delete_post_meta( $product_id, $meta_key_publish );
				}

				if( ! empty($inherit_value) ){
					$product_settings[$meta_key_inherit2] = $inherit_value;
					delete_post_meta( $product_id, $meta_key_inherit );
				}

				if( ! empty($stock_value) ) {
					$product_settings[ $meta_key_stock2 ] = $stock_value;
					delete_post_meta( $product_id, $meta_key_stock );
				}

				if( ! empty($product_settings) ){
					update_post_meta( $product_id, '_woonet_settings', $product_settings );
					update_post_meta( $product_id, '_woonet_network_main_product', true );
				}
			}
		}
	}

	if( ! empty( $temp_sites ) ){
		$order_meta_rows = array();
		foreach ( WOO_MULTISTORE()->sites as $site ){
			$site_id = $site->get_id();
			$uuid = $temp_sites[$site_id]['uuid'];

			if( empty( $site_id ) || empty( $uuid ) ){
				continue;
			}

			$meta_rows_query = "SELECT * FROM {$wpdb->prefix}postmeta where meta_key like 'WOONET_MAP_ORDER_SID_{$uuid}%'";
			$result = $wpdb->get_results($meta_rows_query, ARRAY_A);

			if( ! empty( $result ) ){
				$order_meta_rows[$site_id] = $result;
			}
		}

		if( ! empty( $order_meta_rows ) ){
			foreach ( $order_meta_rows as $site_key => $orders ){
				foreach ($orders as $order_meta){
					$meta_id = $order_meta['meta_id'];
					$old_meta_key = $order_meta['meta_key'];
					$meta_exploded = explode('OID_', $old_meta_key);
					$order_id = $meta_exploded[1];
					$new_meta_key = 'WOONET_IMPORT_ORDER_MAP_OID_'.$order_id.'_SID_'.$site_key;

					$update_meta_query = "update {$wpdb->prefix}postmeta set meta_key = '{$new_meta_key}' WHERE meta_id = {$meta_id}";
					update_post_meta($order_meta['post_id'], 'WOONET_PARENT_ORDER_ORIGIN_SID', $site_key );
					$wpdb->query($update_meta_query);
				}
			}
		}

		foreach (WOO_MULTISTORE()->sites as $site){
			$site_id = $site->get_id();
			$uuid = $temp_sites[$site_id]['uuid'];

			if( empty( $site_id ) || empty( $uuid ) ){
				continue;
			}

			$uuid_replace_query = "update {$wpdb->prefix}usermeta set meta_key = replace(meta_key, $uuid, $site_id)";
			$wpdb->query($uuid_replace_query);
		}

	}

}

function wc_multistore_migrate_product_settings_single_child_5_0_0(){
	global $wpdb;

	$temp_sites = get_site_option('wc_multistore_master_connect2');

	$site = get_site_option('wc_multistore_master_connect');

	$products_query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product'";
	$variations_query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product_variation'";
	$orders_query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order'";


	$products = $wpdb->get_results($products_query);
	$variations = $wpdb->get_results($variations_query);
	$orders = $wpdb->get_results($orders_query);


	if( ! empty( $products ) ){
		foreach ( $products as $std_class ){
			$product_id = $std_class->ID;
			$product_settings = array();

			$meta_key_master_id = '_woonet_master_product_id';
			$meta_key_master_sku = '_woonet_master_product_sku';
			$meta_key_publish = '_woonet_publish_to_' . $temp_sites['uuid'];
			$meta_key_inherit = '_woonet_publish_to_' . $temp_sites['uuid'] . '_child_inheir';
			$meta_key_stock = '_woonet_' . $temp_sites['uuid'] . '_child_stock_synchronize';

			$meta_key_publish2 = '_woonet_publish_to_' . $site['key'];
			$meta_key_inherit2 = '_woonet_publish_to_' . $site['key'] . '_child_inheir';
			$meta_key_stock2 = '_woonet_' . $site['key'] . '_child_stock_synchronize';


			$master_id_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_master_id' AND post_id = {$product_id}";
			$master_sku_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_master_sku' AND post_id = {$product_id}";
			$publish_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_publish' AND post_id = {$product_id}";
			$inherit_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_inherit' AND post_id = {$product_id}";
			$stock_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_stock' AND post_id = {$product_id}";

			$master_id_value = $wpdb->get_var($master_id_query);
			$master_sku_value = $wpdb->get_var($master_sku_query);
			$publish_value = $wpdb->get_var($publish_query);
			$inherit_value = $wpdb->get_var($inherit_query);
			$stock_value =  $wpdb->get_var($stock_query);

			if( ! empty( $master_id_value) ){
				delete_post_meta( $product_id, $meta_key_master_id );
				update_post_meta( $product_id, '_woonet_network_is_child_product_id', $master_id_value );
			}

			if( ! empty($master_sku_value) ){
				delete_post_meta( $product_id, $meta_key_master_sku );
				update_post_meta( $product_id, '_woonet_network_is_child_product_sku', $master_sku_value );
			}

			if( ! empty($publish_value) ){
				$product_settings[$meta_key_publish2] = $publish_value;
				delete_post_meta( $product_id, $meta_key_publish );
			}

			if( ! empty($inherit_value) ){
				$product_settings[$meta_key_inherit2] = $inherit_value;
				delete_post_meta( $product_id, $meta_key_inherit );
			}

			if( ! empty($stock_value) ) {
				$product_settings[ $meta_key_stock2 ] = $stock_value;
				delete_post_meta( $product_id, $meta_key_stock );
			}

			if( ! empty($product_settings) ){
				update_post_meta( $product_id, '_woonet_settings', $product_settings );
				update_post_meta( $product_id, '_woonet_network_main_product', true );
			}

		}
	}


	if( ! empty( $variations ) ) {
		foreach ( $variations as $std_class ) {
			$variation_id = $std_class->ID;

			$meta_key_master_id = '_woonet_master_product_id';
			$meta_key_master_sku = '_woonet_master_product_sku';

			$master_id_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_master_id' AND post_id = {$variation_id}";
			$master_sku_query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$meta_key_master_sku' AND post_id = {$variation_id}";

			$master_id_value = $wpdb->get_var($master_id_query);
			$master_sku_value = $wpdb->get_var($master_sku_query);

			if( ! empty( $master_id_value) ){
				delete_post_meta( $variation_id, $meta_key_master_id );
				update_post_meta( $variation_id, '_woonet_network_is_child_product_id', $master_id_value );
			}

			if( ! empty($master_sku_value) ){
				delete_post_meta( $variation_id, $meta_key_master_sku );
				update_post_meta( $variation_id, '_woonet_network_is_child_product_sku', $master_sku_value );
			}
		}
	}
}