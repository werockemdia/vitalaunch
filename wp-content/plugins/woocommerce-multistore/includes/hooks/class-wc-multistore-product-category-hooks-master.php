<?php
/**
 * Product Category Master handler.
 *
 * This handles product category master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Category_Hooks_Master
 */
final class WC_Multistore_Product_Category_Hooks_Master {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'init' ), 1 );
	}

	public function init() {
		if ( ! WOO_MULTISTORE()->license->is_active() ) {return;}
		if ( WOO_MULTISTORE()->site->get_type() != 'master' ) { return;	}
		if( is_multisite() && get_current_blog_id() != get_site_option('wc_multistore_master_store') ){ return; }

		//add_action( 'edited_product_cat', array( $this, 'republish_category_changes' ) );
	}


	public function republish_category_changes( $master_term_id ) {
		if ( doing_action( 'wp_ajax_inline-save-tax' ) ) {
			return;
		}

		if( doing_action('woocommerce_update_product') ){
			return;
		}

		if( doing_action('wp_ajax_wc_multistore_ajax_sync') ){
			return;
		}

		$term = get_term($master_term_id);
		$wc_multistore_product_cat_master = new WC_Multistore_Product_Category_Master($term);
		$wc_multistore_product_cat_master->sync();
	}
}
