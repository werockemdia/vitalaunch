<?php
/**
 * Network Products Handler
 *
 * This handles network products related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Network_Products
 */
class WC_Multistore_Network_Products {

	/**
	 * Constructor
	 */
	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ) { return; }
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }

		$this->hooks();
	}

	/**
	 * Hooks
	 */
	public function hooks(){
		add_action( 'network_admin_menu', array( $this, 'add_network_submenu_page' ),11 );
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ),11 );
	}

	/**
	 * Submenu Page
	 */
	public function add_network_submenu_page() {
		add_submenu_page('woonet-woocommerce', __( 'Network Products', 'woonet' ), __( 'Network Products', 'woonet' ),'manage_woocommerce','woonet-woocommerce-products',	array( $this, 'network_products_interface',	), 3 );
	}

	/**
	 * Submenu Page
	 */
	public function add_submenu_page() {
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		if( is_multisite() ){
			return;
		}

		add_submenu_page('woonet-woocommerce','Network Products','Network Products','manage_woocommerce','network-products', array( $this,	'admin_network_products_page'	) );
	}


	/**
	 * Network products page
	 */
	public function network_products_interface() {
		switch_to_blog(get_site_option('wc_multistore_master_store'));
		$url = admin_url('edit.php?post_type=product' );
		restore_current_blog();

		wp_redirect( $url );
		die;
	}

	/**
	 * Admin Network products page
	 */
	function admin_network_products_page() {
		$url = admin_url('edit.php?post_type=product' );

		wp_redirect( $url );
		exit;
	}
}