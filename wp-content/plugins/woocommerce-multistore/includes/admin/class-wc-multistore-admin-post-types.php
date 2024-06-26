<?php
/**
 * Post Types handler
 *
 * This handles post types related functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class WC_Multistore_Admin_Post_Types
 */
class WC_Multistore_Admin_Post_Types {

	public $sites;

	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }

		$this->sites = WOO_MULTISTORE()->active_sites;
		$this->settings = WOO_MULTISTORE()->settings;
		$this->hooks();
	}

	/**
	 * Hooks
	 */
	public function hooks(){
		add_action( 'add_inline_data', array( $this, 'add_quick_edit_inline_data' ), 10, 2 );

		add_filter( 'manage_product_posts_columns', array( $this, 'wc_multistore_columns' ), 99 );
		add_action( 'manage_product_posts_custom_column' , array( $this, 'wc_multistore_column' ), 10, 2 );
//		add_action( 'manage_product_posts_custom_column' , array( $this, 'wc_multistore_variations_stock' ), 10, 2 );


		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 20, 2 );
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 20, 2 );


		add_action( 'admin_notices', array( $this, 'add_sync_notice' ) );
		add_action( 'network_admin_notices', array( $this, 'add_sync_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_sync_scripts' ) );

		add_action( 'wp_after_admin_bar_render', array( $this, 'add_quick_edit_notice' ) );


//		add_filter( 'woocommerce_products_admin_list_table_filters', array( $this, 'add_products_parent_child_filter' ) );
//		add_filter( 'posts_clauses', array( $this, 'filter_parent_child_post_clauses' ) );
	}

	function wc_multistore_columns($columns) {
		$columns['wc_multistore'] = __('WOO Multistore', 'woonet');

		return $columns;
	}


	function wc_multistore_column( $column, $post_id ) {
		switch ( $column ) {
			case 'wc_multistore' :
				global $product;

				$is_child_product = wc_multistore_is_child_product( $product );

				if( WOO_MULTISTORE()->site->get_type() != 'master'){
					if( $is_child_product ){
						include WOO_MSTORE_SINGLE_INCLUDES_PATH . 'admin/views/html-column-wc-multistore-child.php';
					}
				}else{
					include WOO_MSTORE_SINGLE_INCLUDES_PATH . 'admin/views/html-column-wc-multistore-master.php';
				}

				break;
		}
	}


	function wc_multistore_variations_stock( $column, $post_id ) {
		switch ( $column ) {
			case 'is_in_stock' :
				global $product;

				if( ! empty($product->get_children()) && $product->get_type() == 'variable' ){
					foreach ($product->get_children() as $child_id){
						$wc_p = wc_get_product($child_id);
						echo '<p> # '. $child_id . ' - ' . $wc_p->get_stock_quantity('edit'). '</p>';
					}
				}

				break;
		}
	}


	/**
	 * Custom bulk edit - form.
	 *
	 * @param string $column_name Column being shown.
	 * @param string $post_type Post type being shown.
	 */
	public function bulk_edit( $column_name, $post_type ) {
		if ( 'price' !== $column_name || 'product' !== $post_type ) {
			return;
		}

		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		$shipping_class = get_terms(
			'product_shipping_class',
			array(
				'hide_empty' => false,
			)
		);

		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-bulk-edit-product.php';
	}

	/**
	 * Custom quick edit - form.
	 *
	 * @param string $column_name Column being shown.
	 * @param string $post_type Post type being shown.
	 */
	public function quick_edit( $column_name, $post_type ) {
		if ( 'price' !== $column_name || 'product' !== $post_type ) {
			return;
		}

		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		$shipping_class = get_terms(
			'product_shipping_class',
			array(
				'hide_empty' => false,
			)
		);

		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-quick-edit-product.php';
	}

	public function add_quick_edit_inline_data( $post, $post_type ) {
		if( $post_type->name != 'product' ){
			return;
		}
		global $product;

		$product_settings = $product->get_meta( '_woonet_settings' );

		$synchronize_by_default_name = 'synchronize-by-default';
		$inherit_by_default_name = 'inherit-by-default';
		$synchronize_stock_by_default_name = 'synchronize-stock';


		$synchronize_by_default_value = $this->settings[$synchronize_by_default_name];
		$inherit_by_default_value = $this->settings[$inherit_by_default_name];
		$synchronize_stock_by_default_value = $this->settings[$synchronize_stock_by_default_name];

		$is_child_product = wc_multistore_is_child_product( $product );


		if( WOO_MULTISTORE()->site->get_type() == 'child' || $is_child_product ){
			echo '<div class="_woonet_is_child_product">yes</div>';
		}

		// general settings
		echo '
				<div class="' . '_woonet_' . $synchronize_by_default_name . '">' . esc_html( $synchronize_by_default_value ) . '</div>
				<div class="' . '_woonet_' . $inherit_by_default_name . '">' . esc_html( $inherit_by_default_value ) . '</div>
				<div class="' . '_woonet_' . $synchronize_stock_by_default_name . '">' . esc_html( $synchronize_stock_by_default_value ) . '</div>
			';

		// product settings
		foreach ( $this->sites as $site ){
			$publish_to_meta_name = '_woonet_publish_to_' . $site->get_id();
			$inherit_meta_name = '_woonet_publish_to_' . $site->get_id() . '_child_inheir';
			$stock_meta_name = '_woonet_' . $site->get_id() . '_child_stock_synchronize';

			$publish_to_value = ( empty( $product_settings ) || ! isset( $product_settings[$publish_to_meta_name] ) ) ? $synchronize_by_default_value : $product_settings[$publish_to_meta_name];
			$inherit_value = ( empty( $product_settings ) || ! isset( $product_settings[$inherit_meta_name] ) ) ? $inherit_by_default_value : $product_settings[$inherit_meta_name] ;
			$stock_value = ( empty( $product_settings ) || ! isset( $product_settings[$stock_meta_name] ) ) ? $synchronize_stock_by_default_value : $product_settings[$stock_meta_name];


			echo '
				<div class="' . $publish_to_meta_name . '">' . esc_html( $publish_to_value ) . '</div>
				<div class="' . $inherit_meta_name . '">' . esc_html( $inherit_value ) . '</div>
				<div class="' . $stock_meta_name . '">' . esc_html( $stock_value ) . '</div>
			';
		}
	}

	public function add_sync_notice(){
		if ( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ) {
			if ( $products = get_transient( 'wc_multistore_scheduled_products' ) ) {
				$action = 'wc_multistore_scheduled_products';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-background-sync-notice.php';
				delete_transient('wc_multistore_scheduled_products');
			}

			if ( $products = get_transient( 'wc_multistore_scheduled_trash_products' ) ) {
				$action = 'wc_multistore_scheduled_trash_products';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-background-sync-notice.php';
				delete_transient('wc_multistore_scheduled_trash_products');
			}

			if ( $products = get_transient( 'wc_multistore_scheduled_untrash_products' ) ) {
				$action = 'wc_multistore_scheduled_untrash_products';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-background-sync-notice.php';
				delete_transient('wc_multistore_scheduled_untrash_products');
			}

			if ( $products = get_transient( 'wc_multistore_scheduled_delete_products' ) ) {
				$action = 'wc_multistore_scheduled_delete_products';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-background-sync-notice.php';
				delete_transient('wc_multistore_scheduled_delete_products');
			}

			if( $products = get_transient( 'wc_multistore_ajax_products' ) ){
				$transient = 'wc_multistore_ajax_products_' . uniqid();
				set_transient( $transient, $products );
				delete_transient( 'wc_multistore_ajax_products' );
				$action = 'wc_multistore_ajax_sync';
				$cancel_action = 'wc_multistore_cancel_ajax_sync';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-ajax-sync-notice.php';
			}

			if( $products = get_transient( 'wc_multistore_trash_ajax_products' ) ){
				$transient = 'wc_multistore_trash_ajax_products_' . uniqid();
				set_transient( $transient, $products );
				delete_transient( 'wc_multistore_trash_ajax_products' );
				$action = 'wc_multistore_ajax_trash';
				$cancel_action = 'wc_multistore_cancel_ajax_trash';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-ajax-sync-notice.php';
			}

			if( $products = get_transient( 'wc_multistore_untrash_ajax_products' ) ){
				$transient = 'wc_multistore_untrash_ajax_products_' . uniqid();
				set_transient( $transient, $products );
				delete_transient( 'wc_multistore_untrash_ajax_products' );
				$action = 'wc_multistore_ajax_untrash';
				$cancel_action = 'wc_multistore_cancel_ajax_untrash';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-ajax-sync-notice.php';
			}

			if( $products = get_transient( 'wc_multistore_delete_ajax_products' ) ){
				$transient = 'wc_multistore_delete_ajax_products_' . uniqid();
				set_transient( $transient, $products );
				delete_transient( 'wc_multistore_delete_ajax_products' );
				$action = 'wc_multistore_ajax_delete';
				$cancel_action = 'wc_multistore_cancel_ajax_delete';
				require_once WOO_MSTORE_PATH . 'includes/admin/views/html-ajax-sync-notice.php';
			}
		}
	}

	public function add_quick_edit_notice(){
		if ( wc_multistore_is_admin_products_page() ){
			if($this->settings['sync-method'] == 'background') {
				$action = 'wc_multistore_inline_save_background';
			}else{
				$action = 'wc_multistore_inline_save_ajax';
			}

			require_once WOO_MSTORE_PATH . 'includes/admin/views/html-quick-edit-notice.php';
		}
	}


	public function add_sync_scripts(){
		if ( wc_multistore_is_admin_products_page() ) {
			if($this->settings['sync-method'] == 'background') {
				wp_enqueue_script( 'wc-multistore-background-sync-notice-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-background-sync-notice.js', array(), WOO_MSTORE_VERSION );
				wp_localize_script( 'wc-multistore-background-sync-notice-js', 'wc_multistore_data', array('settings' => $this->settings,	'sites'    => $this->sites	) );
				wp_register_style( 'wc-multistore-background-sync-notice-css', WOO_MSTORE_ASSET_URL . '/assets/css/wc-multistore-background-sync-notice.css', array(), WOO_MSTORE_VERSION );
				wp_enqueue_style( 'wc-multistore-background-sync-notice-css' );

			}else{
				wp_enqueue_script( 'wc-multistore-ajax-sync-notice-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-ajax-sync-notice.js', array(), WOO_MSTORE_VERSION );
				wp_localize_script( 'wc-multistore-ajax-sync-notice-js', 'wc_multistore_data', array('settings' => $this->settings , 'sites' => $this->sites ) );
				wp_register_style( 'wc-multistore-ajax-sync-notice-css', WOO_MSTORE_ASSET_URL . '/assets/css/wc-multistore-ajax-sync-notice.css', array(), WOO_MSTORE_VERSION );
				wp_enqueue_style( 'wc-multistore-ajax-sync-notice-css' );
			}

			wp_enqueue_script( 'wc-multistore-bulk-edit-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-bulk-edit.js', array( 'woocommerce_quick-edit' ), WOO_MSTORE_VERSION );
			wp_localize_script( 'wc-multistore-bulk-edit-js', 'wc_multistore_data', array('settings' => $this->settings , 'sites' => $this->sites ) );
			wp_enqueue_script( 'wc-multistore-quick-edit-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-quick-edit.js', array( 'woocommerce_quick-edit' ), WOO_MSTORE_VERSION );
			wp_localize_script( 'wc-multistore-quick-edit-js', 'wc_multistore_data', array('settings' => $this->settings , 'sites' => $this->sites ) );
			wp_register_style( 'wc-multistore-quick-edit-css', WOO_MSTORE_ASSET_URL . '/assets/css/wc-multistore-quick-edit.css', array(), WOO_MSTORE_VERSION );
			wp_enqueue_style( 'wc-multistore-quick-edit-css' );
			wp_register_style( 'wc-multistore-bulk-edit-css', WOO_MSTORE_ASSET_URL . '/assets/css/wc-multistore-bulk-edit.css', array(), WOO_MSTORE_VERSION );
			wp_enqueue_style( 'wc-multistore-bulk-edit-css' );

			wp_enqueue_script( 'jquery-ui-progressbar' );
		}

		if( wc_multistore_is_admin_product_page() ){
			if($this->settings['sync-method'] == 'background'){
				wp_enqueue_script( 'wc-multistore-background-sync-notice-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-background-sync-notice.js', array(), WOO_MSTORE_VERSION );
				wp_localize_script( 'wc-multistore-background-sync-notice-js', 'wc_multistore_data', array('settings' => $this->settings , 'sites' => $this->sites ) );
				wp_register_style( 'wc-multistore-background-sync-notice-css', WOO_MSTORE_ASSET_URL . '/assets/css/wc-multistore-background-sync-notice.css', array(), WOO_MSTORE_VERSION );
				wp_enqueue_style( 'wc-multistore-background-sync-notice-css' );
			}else{
				wp_enqueue_script( 'wc-multistore-ajax-sync-notice-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-ajax-sync-notice.js', array(), WOO_MSTORE_VERSION );
				wp_localize_script( 'wc-multistore-ajax-sync-notice-js', 'wc_multistore_data', array('settings' => $this->settings , 'sites' => $this->sites ) );
				wp_register_style( 'wc-multistore-ajax-sync-notice-css', WOO_MSTORE_ASSET_URL . '/assets/css/wc-multistore-ajax-sync-notice.css', array(), WOO_MSTORE_VERSION );
				wp_enqueue_style( 'wc-multistore-ajax-sync-notice-css' );

				wp_enqueue_script( 'jquery-ui-progressbar' );
			}
		}
	}

}