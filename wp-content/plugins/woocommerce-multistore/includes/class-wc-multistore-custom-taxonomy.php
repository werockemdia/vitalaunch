<?php
/**
 * Custom Taxonomy handler.
 *
 * This handles custom taxonomy related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Custom_Taxonomy
 */
class WC_Multistore_Custom_Taxonomy {

	public $excluded_taxonomies = array(
		'category',
		'post_tag',
		'nav_menu',
		'link_category',
		'post_format',
		'product_type',
		'product_visibility',
		'product_cat',
		'product_tag',
		'product_shipping_class',
	);

	public $excluded_meta_keys = array(
		'_edit_lock',
		'_edit_last',
		'_sku',
		'_regular_price',
		'total_sales',
		'_tax_status',
		'_tax_class',
		'_tax_class',
		'_manage_stock',
		'_backorders',
		'_sold_individually',
		'_virtual',
		'_downloadable',
		'_download_limit',
		'_download_expiry',
		'_stock',
		'_stock_status',
		'_wc_average_rating',
		'_wc_review_count',
		'_product_version',
		'_price',
		'_sale_price',
		'_thumbnail_id',
		'_wp_old_slug',
		'_wp_trash_meta_comments_status',
		'_wp_desired_post_slug',
		'_crosssell_ids',
		'_upsell_ids',
		'_default_attributes',
		'_weight',
		'_downloadable_files',
		'woonet_settings',
		'_wp_trash_meta_status',
		'_wp_trash_meta_time',
		'_children',
		'_product_image_gallery',
		'_woonet_network_main_product',
		'_product_attributes',
		'_wpml_word_count',
		'_wpml_media_featured',
		'_wpml_media_duplicate',
		'_last_translation_edit_mode',
		'attr_label_translations',
		'_wcml_average_rating',
		'_wcml_review_count',
		'wcml_sync_hash',
		'_wc_cog_cost',
		'yikes_woo_products_tabs',
		'_wc_rating_count',
		'_variation_description',
		'attribute_0'
	);

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }

		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'add_submenu_multisite' ) );
			add_action( 'admin_head', array( $this, 'remove_set_taxonomy_from_menu' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'add_submenu_single' ) );
			add_action( 'admin_head', array( $this, 'remove_set_taxonomy_from_menu' ) );
		}
	}

	/**
	 * Add submenu to multisite.
	 * @multi-site
	 */
	public function add_submenu_multisite() {
		$hook_id = add_submenu_page('woonet-woocommerce','Custom Taxonomy & Metadata Settings','Custom Taxonomy & Metadata Settings','manage_options','woonet-set-taxonomy', array( $this, 'custom_taxonomies_setting' ) );
		add_action( 'load-' . $hook_id, array( $this, 'options_update' ) );
	}


	/**
	 * Add submenu to single site.
	 * @single-site
	 */
	public function add_submenu_single() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( WOO_MULTISTORE()->site->get_type() == 'master' ) {	$hook_id = add_submenu_page('woonet-woocommerce',	'Custom Taxonomy & Metadata Settings','Custom Taxonomy & Metadata Settings','manage_options','woonet-set-taxonomy',	array( $this, 'custom_taxonomies_setting' )	);
			add_action( 'load-' . $hook_id, array( $this, 'options_update' ) );
		}
	}



	/**
	 * Remove_set_taxonomy_from_menu
	 * @shared-sites
	 * @return void
	 */
	public function remove_set_taxonomy_from_menu() {
		remove_submenu_page( 'woonet-woocommerce', 'woonet-set-taxonomy' );
	}

	/**
	 * Custom_taxonomies_setting
	 *
	 * @return void
	 */
	public function custom_taxonomies_setting() {
		$woo_mstore_custom_taxonomies = $this->get_taxonomies();
		$woo_mstore_custom_meta_keys = $this->get_meta_keys();
        require_once WOO_MSTORE_PATH . 'includes/admin/views/html-settings-custom-taxonomy-metadata.php';
	}

	/**
	 * Custom_taxonomies_setting
	 * @for single-site
	 * @return void
	 */
	public function options_update() {
		if ( empty( $_REQUEST['Submit'] ) ) {
			return false;
		}

		if ( empty( $_REQUEST['_mstore_form_submit_taxonomies_nonce'] )
			|| ! wp_verify_nonce( $_REQUEST['_mstore_form_submit_taxonomies_nonce'], 'mstore_form_submit_taxonomies' ) ) {
			wp_die( 'You are not allowed to access this page.' );
		}


		if ( WOO_MULTISTORE()->settings['sync-custom-taxonomy']  == 'yes' ) {
			if ( ! empty( $_REQUEST['__wc_multistore_custom_taxonomy'] ) ) {
				update_site_option( 'wc_multistore_custom_taxonomy', $_REQUEST['__wc_multistore_custom_taxonomy'] );
			} else {
				update_site_option( 'wc_multistore_custom_taxonomy', array() );
			}
		}

		if ( WOO_MULTISTORE()->settings['sync-custom-metadata'] == 'yes' ) {
			if ( ! empty( $_REQUEST['__wc_multistore_custom_metadata'] ) ) {
				update_site_option( 'wc_multistore_custom_metadata', $_REQUEST['__wc_multistore_custom_metadata'] );
			} else {
				update_site_option( 'wc_multistore_custom_metadata', array() );
			}
		}
	}

	/**
	 * Get_taxonomies
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		$taxonomies        = get_taxonomies();
		$_filterd_taxonomies = array();

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $tax ) {
				if ( in_array( $tax, $this->excluded_taxonomies ) || substr( $tax, 0, 3 ) == 'pa_' ) {
					continue;
				}

				$_filterd_taxonomies[] = $tax;
			}
		}

		return $_filterd_taxonomies;
	}

	/**
	 * Get_taxonomies
	 *
	 * @return array
	 */
	public function get_meta_keys() {
		global $wpdb;

		$query = "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} RIGHT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID WHERE {$wpdb->posts}.post_type IN ('product', 'product_variation')";

		$result = $wpdb->get_results( $query, ARRAY_A );
		$meta_keys = array();

		if ( ! empty( $result ) ) {
			foreach ( $result as $key ) {
				if ( in_array( $key['meta_key'], $this->excluded_meta_keys ) || substr( $key['meta_key'], 0, 7 ) == '_woonet' ) {
					continue;
				}

				if( substr( $key['meta_key'], 0, 12 ) == 'attribute_pa' ){
					continue;
				}

				if( substr( $key['meta_key'], 0, 10 ) == 'attribute_' ){
					continue;
				}

				$meta_keys[] = $key['meta_key'];
			}
		}

		return $meta_keys;
	}
}