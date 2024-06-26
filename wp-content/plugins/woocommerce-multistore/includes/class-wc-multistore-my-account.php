<?php
/**
 * My Account Class
 *
 * Allow a user to see the same info at any site he logs in to.
 *
 * @author      Tonny
 * @category    Admin
 * @package     Multistore/Admin
 * @version     2.4.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Multistore_My_Account.
 */
class WC_Multistore_My_Account {

	public function __construct() {
		if( ! is_multisite() ){ return;	}
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }

		if ( 'no' == WOO_MULTISTORE()->settings['network-user-info'] ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'get_account_menu_items' ), PHP_INT_MAX );
	}

	public function enqueue_scripts() {
		wp_register_script(	'wc-multistore-front-my-account',WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-front-my-account.js',array( 'jquery-ui-accordion' ),	'1.11.4',true);
	}

	public function get_account_menu_items( $items ) {

		static $processed_items = array();

		$processed_items   = array_merge( $processed_items, $items );


		if ( empty( $GLOBALS['switched'] ) ) {
			$items = $processed_items;

			$common_endpoints = array(
				'customer-logout',
				'dashboard',
				'edit-account',
				'edit-address',
			);
			foreach ( array_keys( $items ) as $endpoint ) {
				if ( ! in_array( $endpoint, $common_endpoints) ) {
					add_action( 'woocommerce_account_' . $endpoint . '_endpoint', array( $this, 'account_endpoint_start' ), -PHP_INT_MAX );
					add_action( 'woocommerce_account_' . $endpoint . '_endpoint', array( $this, 'account_endpoint' ), PHP_INT_MAX );
				}
			}
		}

		return $items;
	}

	public function account_endpoint_start() {
		ob_start();
	}

	public function account_endpoint() {
		remove_action( 'woocommerce_account_content', 'woocommerce_output_all_notices', 5 );

		static $processed_sites = array();
		static $content         = '';

		$current_user_id   = get_current_user_id();
		$processed_sites[] = get_current_blog_id();

		$content .= sprintf(
			'<h3>%s</h3><div>%s</div>',
			get_bloginfo('name'),
			ob_get_clean()
		);

		$store_ids = array();
		$master_store_id = get_site_option('wc_multistore_master_store');

		$store_ids[] = $master_store_id;
		foreach (WOO_MULTISTORE()->active_sites as $site){
			$store_ids[] = $site->get_id();
		}

		// get "My account" menu item content for all blogs
		if ( $store_ids ) {
			foreach( $store_ids as $store_id ) {
				if ( in_array( $store_id, $processed_sites ) ) {
					continue;
				}

				if ( ! is_user_member_of_blog( $current_user_id, $store_id ) ) {
					continue;
				}

				switch_to_blog( $store_id );
				$current_page    = empty( $current_page ) ? 1 : absint( $current_page );
				$customer_orders = wc_get_orders(
					apply_filters(
						'woocommerce_my_account_my_orders_query',
						array(
							'customer' => get_current_user_id(),
							'page'     => $current_page,
							'paginate' => true,
						)
					)
				);

				do_action( 'woocommerce_account_content' );

				restore_current_blog();
			}
		}

		if ( empty( $GLOBALS['switched'] ) ) {
			echo '<div id="woo_mstore_accordion">' . $content . '</div>';
			wp_enqueue_style( 'jquery-ui-accordion' );
			wp_enqueue_script( 'wc-multistore-front-my-account' );
		}
	}
}