<?php
/**
 * Bulk Update handler.
 *
 * This handles bulk update related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Bulk_Update
 */
class WC_Multistore_Bulk_Update {

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }

		$this->hooks();
	}

	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_run_woomulti_bulk_update', array( $this, 'update' ) );
		add_action( 'wp_ajax_cancel_woomulti_bulk_update', array( $this, 'cancel_update' ) );

		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'add_submenu' ), 16 );
			add_action( 'admin_menu', array( $this, 'add_submenu' ), 16 );
		} elseif ( get_option( 'wc_multistore_network_type' ) == 'master' ) {
			add_action( 'admin_menu', array( $this, 'add_submenu_non_multisite' ), 16 );
		}
	}

	public function enqueue_assets() {
		if ( is_admin() ) {
			if( isset($_GET['page']) && $_GET['page'] == 'woonet-bulk-update-products' ){
				wp_enqueue_style( 'select2-css', WC()->plugin_url() . '/assets/css/select2.css' );

				wp_register_style( 'wc-multistore-bulk-update-css', WOO_MSTORE_ASSET_URL .'/assets/css/wc-multistore-bulk-update.css', array('select2-css'), null );
				wp_enqueue_style( 'wc-multistore-bulk-update-css' );

				wp_register_script( 'wc-multistore-bulk-update-js', WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-bulk-update.js', array('select2'), null );
				wp_enqueue_script( 'wc-multistore-bulk-update-js' );

				wp_enqueue_script( 'jquery-ui-progressbar' );
			}
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
				'Bulk Update',
				'Bulk Update',
				'manage_woocommerce',
				'woonet-bulk-update-products',
				array( $this, 'menu_callback_bulk_update_all_menu' )
			);
		} else {

			$hookname = add_submenu_page(
				'woocommerce',
				'Bulk Update',
				'Bulk Update',
				'manage_woocommerce',
				'woonet-bulk-update-products',
				array( $this, 'menu_callback_bulk_update_all_menu' )
			);

		}
	}

	public function add_submenu_non_multisite() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$hookname = add_submenu_page(
			'woonet-woocommerce',
			'Bulk Update',
			'Bulk Update',
			'manage_woocommerce',
			'woonet-bulk-update-products',
			array( $this, 'menu_callback_bulk_update_all_menu' )
		);
	}

	public function menu_callback_bulk_update_all_menu() {
		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-bulk-update.php';
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
	 *
	 * @return void
	 */
	public function update() {

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

				delete_site_transient( 'woomulti_bulk_update_products' );
				set_site_transient( 'woomulti_bulk_update_products', $products, 60 * 60 * 24 );

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
			$products = get_site_transient( 'woomulti_bulk_update_products' );

			$current_product = array_shift( $products );

			// update transient
			set_site_transient( 'woomulti_bulk_update_products', $products, 60 * 60 * 24 );

			if( is_multisite() ){
				switch_to_blog( (int) get_site_option('wc_multistore_master_store') );
			}

			if ( ! empty( $current_product ) ) {
				$wc_product = wc_get_product( $current_product );

				if( wc_multistore_is_child_product( $wc_product ) ){
					return;
				}

				$classname = wc_multistore_get_product_class_name('master', $wc_product->get_type() );

				$remaining = (int) count( $products );
				$next = (int) array_shift( $products );
				$current = absint( $current_product );

				$image = ' <a target="_blank" href="' . esc_url( get_edit_post_link( $wc_product->get_id() ) ) . '">' . $wc_product->get_image( 'thumbnail' ) . '</a>';
				$title = ' <a target="_blank" href="' . esc_url( get_edit_post_link( $wc_product->get_id() ) ) . '">' . $wc_product->get_title() . '</a>';

				if( ! $classname ){
					// send response
					echo json_encode(
						array(
							'message' =>  $image . ' ' . $title . ' #' . $current . ' - unsupported product type. Next: # ' . $next . ' Total remaining: ' . $remaining,
							'status'  => 'in-progress',
						)
					);
					die;
				}

				$multistore_product = new $classname( $wc_product );

				if( ! $multistore_product->is_enabled_sync ){
					// send response
					echo json_encode(
						array(
							'message' => $image . ' ' . $title . ' #' . $current . ' - <span style="color:#FF9800;">Skipped</span> Next: # ' . $next . ' Total remaining: ' . $remaining,
							'status'  => 'in-progress',
						)
					);
					die;
				}

				$result = array();
				foreach ( WOO_MULTISTORE()->active_sites as $site ) {
					if( $multistore_product->should_publish_to($site->get_id()) ){
						$result[] = $multistore_product->sync_to( $site->get_id() );
					}else{
						$result[] = array(
							'message' => $image . ' ' . $title . ' #' . $current . ' - product settings. Next: # ' . $next . ' Total remaining: ' . $remaining,
							'status'  => 'in-progress',
						);
					}
				}

				// send response
				echo json_encode(
					array(
						'message' => $image . ' ' . $title . ' #' . $current . ' Next: # ' . $next . ' Total remaining: ' . $remaining,
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
				delete_transient( 'woomulti_bulk_update_products' );
			}

			// switch to the original blog ID
			switch_to_blog( $current_blog_id );

		} else {
			delete_transient( 'woomulti_bulk_update_products' );
		}
	}

	public function cancel_update() {
		$this->delete_transient_from_all_blogs();
	}
}