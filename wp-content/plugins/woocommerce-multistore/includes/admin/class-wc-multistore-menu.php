<?php
/**
 * Menu Handler
 *
 * This handles menu related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Menu
 */
class WC_Multistore_Menu {

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->hooks();
	}

	/**
	 * Hooks
	 */
	public function hooks(){
		if( ! WOO_MULTISTORE()->permission ){ return; }

		if( is_multisite() ){
			add_action( 'network_admin_menu', array( $this, 'add_menu_page' ) );
		}else{
			add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'wc_multistore_admin_scripts' ) );
	}

	/**
	 * Menu page
	 */
	public function add_menu_page(){
		add_menu_page( __( 'MultiStore', 'woonet' ), __( 'MultiStore', 'woonet' ), 'manage_woocommerce', 'woonet-woocommerce', null, null, '55.5' );
	}

	public function wc_multistore_admin_scripts(){
		wp_enqueue_style( 'woonet_admin', WOO_MSTORE_URL . '/assets/css/wc-multistore-admin.css' );
	}
}