<?php
/**
 * Admin notices handler.
 *
 * This handles plugin notices related functionality in WooMultistore.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Admin_Notices
 */
class WC_Multistore_Admin_Notices {
	/**
	 * Class constructor
	 **/
	public function __construct(){
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }

		add_action('admin_notices', array( $this, 'deprecated_addons' ) );
//		add_action('admin_notices', array( $this, 'sku_sync_notice' ) );
		add_action('network_admin_notices', array( $this, 'deprecated_addons' ) );
	}


	public function sku_sync_notice(){
        global $product_object;

		if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' && empty($product_object->get_sku('edit')) ){
			?>
            <div class="notice notice-error is-dismissible">
                <p>Sync by Sku is enabled and this product does not have a sku</p>
            </div>
            <?php
		}
    }

	public function deprecated_addons(){
        // Advanced Custom Fields Addon
        if( file_exists(ABSPATH . 'wp-content/plugins/acf-woocommerce-multistore-add-on/acf-woomultistore.php') ){
	        $acf_addon_data = get_plugin_data(  ABSPATH . 'wp-content/plugins/acf-woocommerce-multistore-add-on/acf-woomultistore.php' );
	        $acf_addon_version = $acf_addon_data['Version'];
	        if( version_compare( $acf_addon_version , '1.0.8',  '<' ) ){
		        deactivate_plugins('acf-woocommerce-multistore-add-on/acf-woomultistore.php');
		        ?>
                <div class="notice notice-error is-dismissible">
                    <p>ACF WooMultistore Add-On version <?php echo $acf_addon_version; ?> is deprecated and it was deactivated, please replace the addon with the version found <a target="_blank" href="https://woomultistore.com/product/acf-advanced-custom-fields-woomultistore-add-on/">here</a>  </p>
                </div>
		        <?php
	        }
        }

		// Woocommerce Wholesale Price Addon
		if( file_exists(ABSPATH . 'wp-content/plugins/addon-for-wholesale-price-plugin/woocommerce-multistore-usermeta-sync.php' ) ){
			deactivate_plugins('addon-for-wholesale-price-plugin/woocommerce-multistore-usermeta-sync.php');
			?>
			<div class="notice notice-error is-dismissible">
                <p>WooMultistore Wholesale Prices (Free & Pro) Add-on is deprecated and it was deactivated, please replace the addon with the version found <a target="_blank" href="https://woomultistore.com/product/woocommerce-wholesale-price-woomultistore-add-on/">here</a>  </p>
			</div>
			<?php
		}

        // WWP Wholesale price addon 2
		if( file_exists(ABSPATH . 'wp-content/plugins/addon-for-wholesale-price-plugin/wwpp-woocommerce-multistore-add-on.php' ) ){
			$wwp_addon_data = get_plugin_data(  ABSPATH . 'wp-content/plugins/addon-for-wholesale-price-plugin/wwpp-woocommerce-multistore-add-on.php' );
			$wwp_addon_version = $wwp_addon_data['Version'];
			if( version_compare( $wwp_addon_version , '2.0.3',  '<' ) ){
				deactivate_plugins('addon-for-wholesale-price-plugin/wwpp-woocommerce-multistore-add-on.php');
				?>
                <div class="notice notice-error is-dismissible">
                    <p>WooMultistore Wholesale Prices (Free & Pro) Add-on is deprecated and it was deactivated, please replace the addon with the version found <a target="_blank" href="https://woomultistore.com/product/woocommerce-wholesale-price-woomultistore-add-on/">here</a>  </p>                </div>
				<?php
			}
		}

		// Woocommmerce Bookings Addon
		if( file_exists(ABSPATH . 'wp-content/plugins/addon-woocommerce-bookings-plugin/woo-multistore-bookings.php' ) ){
			$wcbp_addon_data = get_plugin_data(  ABSPATH . 'wp-content/plugins/addon-woocommerce-bookings-plugin/woo-multistore-bookings.php' );
			$wcbp_addon_version = $wcbp_addon_data['Version'];
			if( version_compare( $wcbp_addon_version , '1.0.3',  '<' ) ){
				deactivate_plugins('addon-woocommerce-bookings-plugin/woo-multistore-bookings.php');
				?>
				<div class="notice notice-error is-dismissible">
					<p>WooMultistore Bookings Add-on version <?php echo $wcbp_addon_version; ?> is deprecated and it was deactivated, please replace the addon with the version found <a target="_blank" href="https://woomultistore.com/product/woocommerce-bookings-woomultistore-add-on/">here</a>  </p>
				</div>
				<?php
			}
		}

		// Change Sequential Order Number Addon
		if( file_exists(ABSPATH . 'wp-content/plugins/change-sequential-order-number-add-on-for-woomultistore/change-sequential-order-number.php' ) ){
			$cson_addon_data = get_plugin_data(  ABSPATH . 'wp-content/plugins/change-sequential-order-number-add-on-for-woomultistore/change-sequential-order-number.php' );
			$cson_addon_version = $cson_addon_data['Version'];
			if( version_compare( $cson_addon_version , '2.0.3',  '<' ) ){
				deactivate_plugins('change-sequential-order-number-add-on-for-woomultistore/change-sequential-order-number.php');
				?>
                <div class="notice notice-error is-dismissible">
                    <p>WooMultistore Change Sequential Order Number Add-on version <?php echo $cson_addon_version; ?> is deprecated and it was deactivated, please replace the addon with the version found <a target="_blank" href="https://woomultistore.com/product/change-sequential-order-number-add-on-for-woomultistore/">here</a>  </p>
                </div>
				<?php
			}
		}

		// PPOM Addon
		if( file_exists(ABSPATH . 'wp-content/plugins/ppom-for-woocommerce-woomultistore-add-on/woomulti-addon-ppom.php' ) ){
			$ppom_addon_data = get_plugin_data(  ABSPATH . 'wp-content/plugins/ppom-for-woocommerce-woomultistore-add-on/woomulti-addon-ppom.php' );
			$ppom_addon_version = $ppom_addon_data['Version'];
			if( version_compare( $ppom_addon_version , '2.0.3',  '<' ) ){
				deactivate_plugins('ppom-for-woocommerce-woomultistore-add-on/woomulti-addon-ppom.php');
				?>
                <div class="notice notice-error is-dismissible">
                    <p>WooMultistore Addon for PPOM version <?php echo $ppom_addon_version; ?> is deprecated and it was deactivated, please replace the addon with the version found <a target="_blank" href="https://woomultistore.com/product/ppom-for-woocommerce-add-on/">here</a>  </p>
                </div>
				<?php
			}
		}

		// WPML Addon
		if( file_exists(ABSPATH . 'wp-content/plugins/wpml-woocommerce-multistore-add-on/wpml-woocommerce-multistore-add-on.php' ) ){
			$wpml_addon_data = get_plugin_data(  ABSPATH . 'wp-content/plugins/wpml-woocommerce-multistore-add-on/wpml-woocommerce-multistore-add-on.php' );
			$wpml_addon_version = $wpml_addon_data['Version'];
			if( version_compare( $wpml_addon_version , '2.0.7',  '<' ) ){
				deactivate_plugins('wpml-woocommerce-multistore-add-on/wpml-woocommerce-multistore-add-on.php');
				?>
                <div class="notice notice-error is-dismissible">
                    <p>WPML - WooMultistore Add-On version <?php echo $wpml_addon_version; ?> is deprecated and it was deactivated, please replace the addon with the version found <a target="_blank" href="https://woomultistore.com/product/wpml-woocommerce-multistore-addon/">here</a>  </p>
                </div>
				<?php
			}
		}

		// WPAI Addon
		if( file_exists(ABSPATH . 'wp-content/plugins/wp-all-import-woocommerce-multistore-add-on/woomultistore-wpai-addon.php' ) ){
			$wpai_addon_data = get_plugin_data(  ABSPATH . 'wp-content/plugins/wp-all-import-woocommerce-multistore-add-on/woomultistore-wpai-addon.php' );
			$wpai_addon_version = $wpai_addon_data['Version'];
			if( version_compare( $wpai_addon_version , '2.1.3',  '<' ) ){
				deactivate_plugins('wp-all-import-woocommerce-multistore-add-on/woomultistore-wpai-addon.php');
				?>
                <div class="notice notice-error is-dismissible">
                    <p>WP All Import - WooMultistore Add-On version <?php echo $wpai_addon_version; ?> is deprecated and it was deactivated, please update the plugin from the plugins page</p>
                </div>
				<?php
			}
		}
	}

}
