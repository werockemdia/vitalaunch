<?php
/**
 * Sync role based pricing created by the Price by User Role for WooCommerce
 * created by Tyche Software
 * URL: https://www.tychesoftwares.com/
 * Plugin URL: https://www.tychesoftwares.com/store/premium-plugins/price-user-role-woocommerce/
 * 
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_TYCHE_PRICE_BY_USER_ROLE {


	public function __construct() {
		add_filter('wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys'), 10, 1 );
	}


	public function add_meta_keys( $meta_keys ) {
		if ( ! function_exists('alg_get_user_roles') ) {
			return $meta_keys;
		}

		$meta_keys[] = '_alg_wc_price_by_user_role_per_product_settings_enabled';


		$roles = alg_get_user_roles();

		/**
		 * Run for product (simple, grouped, variable, etc ) and variations. 
		 */
		if ( ! empty( $roles ) ) {
			foreach( $roles as $role_key => $role ) {
				$meta_keys[] = '_alg_wc_price_by_user_role_regular_price_' . $role_key;
				$meta_keys[] = '_alg_wc_price_by_user_role_sale_price_' . $role_key;
				$meta_keys[] = '_alg_wc_price_by_user_role_empty_price_' . $role_key;
			}
		}

		return $meta_keys;
	}
}

new WOO_MSTORE_INTEGRATION_TYCHE_PRICE_BY_USER_ROLE();