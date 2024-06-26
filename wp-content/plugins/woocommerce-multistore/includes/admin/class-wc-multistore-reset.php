<?php
/**
 * Plugin Reset handler.
 *
 * This handles plugin reset functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Reset
 */
class WC_Multistore_Reset {

	private $system_messages = array();

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }

		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ), 15 );
		} else {
			add_action( 'admin_menu', array( $this, 'network_admin_menu' ), 15 );
		}

		$this->init();
	}

	public function network_admin_menu() {
		$menus_hook = add_submenu_page('woonet-woocommerce',__( 'Reset', 'woonet' ),	__( 'Reset', 'woonet' ),'manage_woocommerce','woonet-multistore-reset', array( $this, 'interface_multistore_reset_page' ),99);

		add_action( 'load-' . $menus_hook, array( $this, 'admin_notices' ) );
		add_action( 'admin_print_styles-' . $menus_hook, array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $menus_hook, array( $this, 'admin_print_scripts' ) );
	}

	public function interface_multistore_reset_page() {
		include WOO_MSTORE_PATH . 'includes/admin/views/html-multistore-reset.php';
	}

	public function admin_print_styles() {

		if ( is_multisite() ) {
			$url = WOO_MSTORE_URL;
		} else {
			$url = dirname( WOO_MSTORE_URL );
		}
		wp_enqueue_style( 'woonet-multistore-reset', $url . '/assets/css/wc-multistore-reset.css' );
	}

	public function admin_print_scripts() {
		if ( is_multisite() ) {
			$url = WOO_MSTORE_URL;
		} else {
			$url = dirname( WOO_MSTORE_URL );
		}
	}

	public function init() {

		// check for any forms save
		if ( isset( $_POST['woo_multistore_form_submit'] ) && 'reset-multistore' == $_POST['woo_multistore_form_submit'] ) {
			
			$woonet_multistore_reset_nonce = $_POST['woonet-multistore-reset-nonce'];
			
			/* Verifying nonce */
			if ( ! wp_verify_nonce( $woonet_multistore_reset_nonce, 'woonet-multistore-reset' ) ) {
				$this->system_messages[] = array(
					'type'    => 'error',
					'message' => 'Nonce is invalid.',
				);	 
			} else {
				
				/* Checking if user checked the confirm checkbox on reset form */
				if(isset( $_POST['reset-confirm'] ) && $_POST['reset-confirm'] == '1') {
					if( is_multisite() ) {
						/* If form is submitted from Multisite */
						$this->form_submit_settings_multisite();
					} else if( get_option( 'wc_multistore_network_type' ) == 'master') {

						/* If form is submitted from Master site */
						$this->form_submit_settings_master();
					} else if( get_option( 'wc_multistore_network_type' ) == 'child') {

						/* If form is submitted from Child Site */
						$this->form_submit_settings_child();
					}
				} else {
				
					/* If user does not checked the confirm checkbox */
					$this->system_messages[] = array(
						'type'    => 'error',
						'message' => 'Please confirm before proceeding.',
					);			
				}
			}
		}
	}
	
	private function form_submit_settings_multisite() {

		global $wpdb;

		WOO_MULTISTORE()->license->deactivate();
		delete_site_option('wc_multistore_settings' );
		delete_site_option('wc_multistore_sites' );

		// child sites
		if( ! empty( WOO_MULTISTORE()->sites ) ){
			foreach( WOO_MULTISTORE()->sites as $site ){
				switch_to_blog( $site->get_id() );
					delete_option('wc_multistore_site');
					delete_option('wc_multistore_settings');

					$products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%woonet%' ";
					$booking_products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%wc_multistore%' ";
					$terms = "DELETE FROM $wpdb->termmeta WHERE meta_key like '%woonet%' ";
					$comments = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%wc_multistore%' ";
					$comments2 = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%woonet%' ";
					$order_items = "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key like '%woonet%' ";

					$wpdb->query($products);
					$wpdb->query($booking_products);
					$wpdb->query($terms);
					$wpdb->query($comments);
					$wpdb->query($comments2);
					$wpdb->query($order_items);
				restore_current_blog();
			}
		}

		//master site
		switch_to_blog( get_site_option('wc_multistore_master_store') );
			delete_option('wc_multistore_site');
			delete_option('wc_multistore_settings');

			$products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%woonet%' ";
			$products2 = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%WOONET%' ";
			$terms = "DELETE FROM $wpdb->termmeta WHERE meta_key like '%woonet%' ";
			$users = "DELETE FROM $wpdb->usermeta WHERE meta_key like '%woonet%' ";
			$order_items = "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key like '%woonet%' ";

			$wpdb->query($products);
			$wpdb->query($products2);
			$wpdb->query($users);
			$wpdb->query($order_items);
			$wpdb->query($terms);
		restore_current_blog();
		delete_site_option( 'wc_multistore_master_store' );
		delete_site_option( 'woonet_master_store' );
		delete_site_option( 'wc_multistore_custom_metadata' );
		delete_site_option( 'wc_multistore_custom_taxonomy' );
		delete_site_option( 'wc_multistore_sequential_order_number' );
		delete_site_option( 'wc_multistore_license' );
		delete_site_option( 'wc_multistore_setup_wizard_complete' );

		wp_redirect( network_site_url( 'wp-admin/network/admin.php?page=woonet-woocommerce' ) );
		die();

	}
	
	private function form_submit_settings_master() {
		global $wpdb;

		if( !empty(WOO_MULTISTORE()->sites) ){
			foreach ( WOO_MULTISTORE()->sites as $site ){
				$wc_multistore_site_api_master = new WC_Multistore_Site_Api_Master();
				$wc_multistore_site_api_master->send_reset_child_site( $site );
			}
		}
		
		/* Deactivating Licence Ket upon reset from Master site */
		WOO_MULTISTORE()->license->deactivate();
			delete_option('wc_multistore_sites');
			delete_option('wc_multistore_site');
			delete_option('wc_multistore_settings');

			$products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%woonet%' ";
			$booking_products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%wc_multistore%' ";
			$terms = "DELETE FROM $wpdb->termmeta WHERE meta_key like '%woonet%' ";
			$comments = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%wc_multistore%' ";
			$comments2 = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%woonet%' ";
			$order_items = "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key like '%woonet%' ";
			$transients = "DELETE FROM $wpdb->options WHERE option_name like '%transient_wc_multistore%' ";
			$users = "DELETE FROM $wpdb->usermeta WHERE meta_key like '%woonet%' ";

			$wpdb->query($products);
			$wpdb->query($booking_products);
			$wpdb->query($terms);
			$wpdb->query($comments);
			$wpdb->query($comments2);
			$wpdb->query($order_items);
			$wpdb->query($transients);
			$wpdb->query($users);

			delete_site_option( 'wc_multistore_network_type' );
			delete_site_option( 'wc_multistore_license' );
			delete_site_option( 'wc_multistore_custom_metadata' );
			delete_site_option( 'wc_multistore_custom_taxonomy' );
			delete_site_option( 'wc_multistore_sequential_order_number' );
			delete_site_option( 'wc_multistore_child_sites_deactivated' );
			delete_site_option( 'wc_multistore_child_sites' );
			delete_site_option( 'wc_multistore_orders_export_options' );
			delete_site_option( '_transient_wc_multistore_version_check' );
			delete_site_option( '_transient_timeout_wc_multistore_version_check' );
			delete_site_option( 'wc_multistore_setup_wizard_complete' );


			wp_redirect( admin_url( 'admin.php?page=woonet-woocommerce', 'relative' ) );
			die();
	}

	private function form_submit_settings_child() {
		global $wpdb;

		$wc_multistore_site_api_child = new WC_Multistore_Site_Api_Child();
		$wc_multistore_site_api_child->send_reset_child_site_from_master();

		delete_option('wc_multistore_sites');
		delete_option('wc_multistore_site');
		delete_option('wc_multistore_settings');

		$products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%woonet%' ";
		$booking_products = "DELETE FROM $wpdb->postmeta WHERE meta_key like '%wc_multistore%' ";
		$terms = "DELETE FROM $wpdb->termmeta WHERE meta_key like '%woonet%' ";
		$comments = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%wc_multistore%' ";
		$comments2 = "DELETE FROM $wpdb->commentmeta WHERE meta_key like '%woonet%' ";
		$order_items = "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key like '%woonet%' ";
		$transients = "DELETE FROM $wpdb->options WHERE option_name like '%transient_wc_multistore%' ";
		$users = "DELETE FROM $wpdb->usermeta WHERE meta_key like '%woonet%' ";

		$wpdb->query($products);
		$wpdb->query($booking_products);
		$wpdb->query($terms);
		$wpdb->query($comments);
		$wpdb->query($comments2);
		$wpdb->query($order_items);
		$wpdb->query($transients);
		$wpdb->query($users);
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woo_multistore_global_images_data" );

		delete_site_option( 'wc_multistore_network_type' );
		delete_site_option( 'wc_multistore_license' );
		delete_site_option( 'wc_multistore_custom_metadata' );
		delete_site_option( 'wc_multistore_custom_taxonomy' );
		delete_site_option( 'wc_multistore_sequential_order_number' );
		delete_site_option( 'wc_multistore_child_sites_deactivated' );
		delete_site_option( 'wc_multistore_child_sites' );
		delete_site_option( 'wc_multistore_orders_export_options' );
		delete_site_option( '_transient_wc_multistore_version_check' );
		delete_site_option( '_transient_timeout_wc_multistore_version_check' );
		delete_site_option( 'wc_multistore_master_connect' );
		delete_site_option( 'wc_multistore_setup_wizard_complete' );

		wp_redirect( admin_url( 'admin.php?page=woonet-woocommerce', 'relative' ) );
		die();
	}


	public function admin_notices() {
		if ( count( $this->system_messages ) < 1 ) {
			return;
		}
		foreach ( $this->system_messages as $system_message ) {
			if ( isset( $system_message['type'] ) ) {
				echo "<div class='notice " . $system_message['type'] . "'><p>" . $system_message['message'] . '</p></div>';
			} else {
				echo "<div class='notice notice-error'><p>" . $system_message . '</p></div>';
			}
		}
	}

}