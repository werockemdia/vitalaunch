<?php
/**
 * Bulk Sync handler.
 *
 * This handles bulk sync related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Bulk_Sync
 */
class WC_Multistore_Bulk_Sync {

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }

		$this->hooks();
	}

	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_run_woomulti_bulk_sync', array( $this, 'sync' ) );
		add_action( 'wp_ajax_cancel_woomulti_bulk_sync', array( $this, 'cancel_sync' ) );

		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'add_submenu' ), 16 );
			add_action( 'admin_menu', array( $this, 'add_submenu' ), 16 );
		} elseif ( get_option( 'wc_multistore_network_type' ) == 'master' ) {
			add_action( 'admin_menu', array( $this, 'add_submenu_non_multisite' ), 16 );
		}
	}

	public function enqueue_assets() {
		if ( is_admin() ) {
			wp_register_style( 'wc-multistore-bulk-sync-css', WOO_MSTORE_ASSET_URL .'/assets/css/wc-multistore-bulk-sync.css', array(), null );
			wp_enqueue_style( 'wc-multistore-bulk-sync-css' );

			wp_register_script( 'wc-multistore-bulk-sync-js', WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-bulk-sync.js', array(), null );
			wp_enqueue_script( 'wc-multistore-bulk-sync-js' );

			wp_enqueue_script( 'jquery-ui-progressbar' );
		}
	}


	public function add_submenu() {
		// only if superadmin
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( is_network_admin() ) {
			$hookname = add_submenu_page(
				'woonet-woocommerce',
				'Bulk Sync',
				'Bulk Sync',
				'manage_woocommerce',
				'woonet-bulk-sync-products',
				array( $this, 'menu_callback_bulk_sync_all_menu' )
			);
		} else {

			$hookname = add_submenu_page(
				'woocommerce',
				'Bulk Sync',
				'Bulk Sync',
				'manage_woocommerce',
				'woonet-bulk-sync-products',
				array( $this, 'menu_callback_bulk_sync_all_menu' )
			);

		}
	}

	public function add_submenu_non_multisite() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$hookname = add_submenu_page(
			'woonet-woocommerce',
			'Bulk Sync',
			'Bulk Sync',
			'manage_woocommerce',
			'woonet-bulk-sync-products',
			array( $this, 'menu_callback_bulk_sync_all_menu' )
		);
	}

	public function menu_callback_bulk_sync_all_menu() {
		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-bulk-sync.php';
	}

	public function get_ids() {
		$product_ids = get_posts(
			array(
				'post_type'   => 'product',
				'numberposts' => -1,
				'post_status' => 'publish',
				'fields'      => 'ids',
			)
		);

		return $product_ids;
	}

	/**
	 * sync for multisite
	 *
	 * @return void
	 */
	public function sync() {

		if ( ! empty( $_POST['data'] ) ) {
			$params = array();
			parse_str( $_POST['data'], $params );
			$queue_id = $params['queue_id'] = uniqid();

			$query = new WP_Query();

			if ( is_array( $params ) && count( $params ) >= 1 ) {
				if(is_multisite()){
					switch_to_blog( (int) get_site_option('wc_multistore_master_store') );
				}
				// we can proceed with sync
				if ( ! empty( $params['select-all-products'] ) ) {
					$products = $query->query(
						array(
							'fields'         => 'ids',
							'posts_per_page' => -1,
							'post_type'      => 'product',
							'meta_query'      => array(
								array(
									'key' => '_woonet_is_clone',
									'compare' => 'NOT EXISTS'
								),
							),
						)
					);
				} else {
					// category selected
					$products = $query->query(
						array(
							'fields'         => 'ids',
							'posts_per_page' => -1,
							'post_type'      => 'product',
							'tax_query'      => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'id',
									'terms'    => $params['select_categories'],
								),
							),
							'meta_query'      => array(
								array(
									'key' => '_woonet_is_clone',
									'compare' => 'NOT EXISTS'
								),
							),
						)
					);
				}

				delete_site_transient( 'woomulti_bulk_sync_product_data' );
				delete_site_transient( 'woomulti_bulk_sync_product_data' );
				set_site_transient( 'woomulti_bulk_sync_product_data', $products, 60 * 60 * 24 );
				set_site_transient( 'woomulti_bulk_sync_params', $params, 60 * 60 * 24 );

				echo json_encode(
					array(
						'message'  => 'Sync Settings Saved.',
						'status'   => 'in-progress',
						'queue_id' => $queue_id,
					)
				);
				die;
			}
		} else {
			$settings   = get_site_transient( 'woomulti_bulk_sync_params' );
			$products = get_site_transient( 'woomulti_bulk_sync_product_data' );

			$current_product = array_shift( $products );

			// update transient
			set_site_transient( 'woomulti_bulk_sync_product_data', $products, 60 * 60 * 24 );
			set_site_transient( 'woomulti_bulk_sync_params', $settings, 60 * 60 * 24 );

			if( is_multisite() ){
				switch_to_blog( (int) get_site_option('wc_multistore_master_store') );
			}

			if ( ! empty( $current_product ) ) {
				foreach ( $settings['select_child_sites'] as $site_id ) {
					$_REQUEST[ '_woonet_publish_to_' . $site_id ]                   = 'yes';
					$_REQUEST[ '_woonet_publish_to_' . $site_id . '_child_inheir' ] = ! empty( $settings['child-sync'] ) ? $settings['child-sync'] : 'no';
					$_REQUEST[ '_woonet_' . $site_id . '_child_stock_synchronize' ] = ! empty( $settings['stock-sync'] ) ? $settings['stock-sync'] : 'no';
				}

				$wc_product = wc_get_product($current_product);
				if( wc_multistore_is_child_product( $wc_product ) ){
					return;
				}

				$classname = wc_multistore_get_product_class_name('master', $wc_product->get_type() );

				$remaining = (int) count( $products );
				$next = (int) array_shift( $products );
				$current = absint( $current_product );

				if( ! $classname ){
					// send response
					echo json_encode(
						array(
							'message' => 'Skipped #' . $current . ' - unsupported product type. Next: #' . $next . '. Total remaining: ' . $remaining,
							'status'  => 'in-progress',
						)
					);
					die;
				}

				$multistore_product = new $classname( $wc_product );
				$multistore_product->save();

				$result = array();
				foreach ( $settings['select_child_sites'] as $site_id ) {
					$result[] = $multistore_product->sync_to( $site_id );
				}

				// send response
				echo json_encode(
					array(
						'message' => 'Synced #' . $current . '. Next: #' . $next . '. Total remaining: ' . $remaining,
						'status'  => 'in-progress',
						'result'  => $result,
					)
				);
				die;
			}

			if ( count( $products ) == 0 ) {
				// send response
				echo json_encode(
					array(
						'message' => 'Sync completed.',
						'status'  => 'completed',
					)
				);
			}
			die;
		}
	}

	private function delete_transient_from_all_blogs() {

		if ( is_multisite() ) {
			$get_site_ids    = get_sites();
			$current_blog_id = get_current_blog_id();

			// loop through the blog IDs and delete transient from each
			foreach ( $get_site_ids as $id ) {
				switch_to_blog( $id->blog_id );
				delete_transient( 'woomulti_bulk_sync_product_data' );
				delete_transient( 'woomulti_bulk_sync_params' );
			}

			// switch to the original blog ID
			switch_to_blog( $current_blog_id );

		} else {
			delete_transient( 'woomulti_bulk_sync_product_data' );
			delete_transient( 'woomulti_bulk_sync_params' );
		}
	}

	public function cancel_sync() {
		$this->delete_transient_from_all_blogs();
	}
}