<?php
/**
 * Integration handler.
 *
 * This handles product integration related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Integration
 */
final class WC_Multistore_Integration {
    
    /**
     *  Supported plugin list
     *
     * @var $supported_plugins
     */
    private $supported_plugins = array(
        'woocommerce/woocommerce.php'  => array( 'core-custom-taxonomies.php', 'core-custom-metadata.php', 'core-auto-suggest-addon.php' ),
        'price-by-user-role-for-woocommerce-pro/price-by-user-role-for-woocommerce-pro.php' => 'tyche-price-by-user-role.php',
        'atum-multi-inventory/atum-multi-inventory.php' => 'atum-multi-inventory.php',
        'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' => 'woocommerce-pdf-invoices-packingslips.php',
        'product-gtin-ean-upc-isbn-for-woocommerce/product-gtin-ean-upc-isbn-for-woocommerce.php' => 'product-gtin-ean-upc-isbn-for-woocommerce.php',
        'wpc-countdown-timer/wpc-countdown-timer.php' => 'wpc-countdown-time.php',
        'yikes-inc-easy-custom-woocommerce-product-tabs/yikes-inc-easy-custom-woocommerce-product-tabs.php' => 'custom-product-tabs-wp-all-import-add-on.php',
        // 'elementor/elementor.php' => 'elementor-elementor-pro.php',
        'innozilla-per-product-shipping-woocommerce-pro/woocommerce-innozilla-shipping-per-product-pro.php' => 'product-innozilla-per-product-shipping-woocommerce.php',
        'innozilla-per-product-shipping-woocommerce/woocommerce-innozilla-shipping-per-product.php' => 'product-innozilla-per-product-shipping-woocommerce.php',
        'woocommerce-cost-of-goods/woocommerce-cost-of-goods.php' => 'woocommerce-cost-of-goods.php',
        'polylang-wc/polylang-wc.php' => 'wp-polylang.php',
        // 'polylang/polylang.php' => 'wp-polylang.php',
    );
    
    /**
     *  Path to the integration folder
     *
     * @var $integration_path
     */
    private $integration_path = null;
    
    /**
     * Initialize the action hooks and load the plugin classes
     **/
    public function __construct() {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $this->integration_path = WOO_MSTORE_PATH . '/integration/';
        $this->init();
    }
    
    public function init() {
        $this->add_supported_plugins();
        $this->load_support_for_active_plugins();
    }
    
    
    /**
     * Run filter to add supported plugins to the list
     *
     * @return void
     */
    private function add_supported_plugins() {
        $this->supported_plugins = apply_filters('WOO_MSTORE_Integration/add_supported_plugins', $this->supported_plugins );
    }
    
    /**
     * Load integration support for all 3rd party plugins
     *
     * @return void
     */
    private function load_support_for_active_plugins() {
        if ( ! empty( $this->supported_plugins ) ) {
            foreach ( $this->supported_plugins as $plugin_name => $plugin_support_file ) {
                if ( is_array( $plugin_support_file ) ) {
                    foreach ( $plugin_support_file as $component ) {
                        if ( is_plugin_active( $plugin_name ) && apply_filters( 'WOO_MSTORE_Integration/load', '__return_true', $plugin_name ) ) {
                            $this->_load_supported_plugin( $component );
                        }
                    }
                } else {
                    if ( is_plugin_active( $plugin_name ) && apply_filters( 'WOO_MSTORE_Integration/load', '__return_true', $plugin_name ) ) {
                        $this->_load_supported_plugin( $plugin_support_file );
                    }
                }
            }
        }
        
        do_action( 'WOO_MSTORE_Integration/supported_plugins_loaded', 100, 200 );
    }
    
    /**
     * Load supported plugin file
     *
     * @param mixed $file_to_load string integration file to load
     * @return void
     */
    private function _load_supported_plugin( $file_to_load ) {
        if ( file_exists( $this->integration_path . $file_to_load ) ) {
            include_once $this->integration_path . $file_to_load;
        }
    }
}