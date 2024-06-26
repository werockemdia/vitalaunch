<?php
/**
 * Various compatibility fixes for 3rd party plugins
 *
 * @class   WC_Multistore_Compatibility
 * @since   2.1.2
 */

defined( 'ABSPATH' ) || exit;

class WC_Multistore_Compatibility {
    /**
     * Hook in ajax event handlers.
     */
    public function __construct()
    {
    	add_action( 'WOO_MSTORE_admin_product/sync_started', array($this, 'fix_compatibility_with_woothumb_premium'), 10, 0 );
    }

    public function fix_compatibility_with_woothumb_premium() {
    	remove_filter( 'woocommerce_product_get_gallery_image_ids', 
			array( 'Iconic_WooThumbs_Product_Variation', 'get_gallery_image_ids', ), 10, 2 );

		remove_filter( 'woocommerce_product_variation_get_gallery_image_ids', 
			array( 'Iconic_WooThumbs_Product_Variation', 'get_gallery_image_ids', ), 10, 2 );
    }
}