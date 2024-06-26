<?php
/**
 * Connected Sites handler.
 *
 * This handles connected sites related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Connected_Sites
 */
class WC_Multistore_Connected_Sites {

	/**
	 * Initialize action hooks and load the plugin classes
	 **/
	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }
		$this->hooks();
	}

	public function hooks(){
		add_action( 'network_admin_menu', array( $this, 'add_network_submenu_page' ), 11 );
		add_action( 'admin_menu', array( $this, 'add_woomultistore_submenu' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Submenu Page
	 */
	public function add_network_submenu_page() {
		add_submenu_page('woonet-woocommerce', __( 'Sites', 'woonet' ), __( 'Sites', 'woonet' ),'manage_woocommerce','woonet-woocommerce-sites',	array( $this, 'menu_callback_connected_sites',	), 3 );
	}

	/**
	 * Add a primary menu for WooMultistore
	 **/
	public function add_woomultistore_submenu() {
		if ( WOO_MULTISTORE()->site->get_type() == 'master' ) {
			// enter license key
			$hookname = add_submenu_page('woonet-woocommerce','Sites','Sites','manage_woocommerce',	'woonet-connected-sites',	array( $this, 'menu_callback_connected_sites' ),2 );
			add_action( 'load-' . $hookname, array( $this, 'connected_sites_form_submit' ) );
		}
	}

	public function menu_callback_connected_sites() {
		include WOO_MSTORE_PATH . 'includes/admin/views/html-connected-sites.php';
	}

	public function connected_sites_form_submit() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woonet_delete_site' ) ) {
			wp_die( 'Nope! You are not allowed to perform this action.' );
		}

		$_SESSION['mstore_form_submit_messages'] = array();

		if ( ! empty( $_REQUEST['submit'] ) && $_REQUEST['submit'] == 'remove' ) {
			WOO_MULTISTORE()->sites[ $_POST['__key'] ]->delete();

			$_SESSION['mstore_form_submit_messages'][] = 'Site removed successfully.';

		} elseif ( ! empty( $_REQUEST['submit'] ) && $_REQUEST['submit'] == 'deactivate' ) {
			WOO_MULTISTORE()->sites[ $_POST['__key'] ]->deactivate();

			$_SESSION['mstore_form_submit_messages'][] = 'Site deactivated succesfully.';

		} elseif ( ! empty( $_REQUEST['submit'] ) && $_REQUEST['submit'] == 'activate' ) {
			WOO_MULTISTORE()->sites[ $_POST['__key'] ]->activate();

			$_SESSION['mstore_form_submit_messages'][] = 'Site activated successfully.';

		}
	}

	public function admin_enqueue_scripts() {
		if ( is_multisite() ) {
			return;
		}

		if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'woonet-connected-sites' ) {
			return;
		}

		if( empty( WOO_MULTISTORE()->sites ) ){
			return;
		}

		wp_register_script( 'wc-multistore-connected-sites-js', WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-connected-sites.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'wc-multistore-connected-sites-js' );

		$sites = array();
		foreach( WOO_MULTISTORE()->sites as $site ){
			$sites[] = array(
				'adminUrl' => admin_url( 'admin-ajax.php' ),
				'action' => 'wc_multistore_get_child_site_version',
				'nonce' => WOO_MULTISTORE()->site->get_id(),
				'key' => $site->get_id()
			);
		}

		wp_localize_script(	'wc-multistore-connected-sites-js','wc_multistore_data',array(	'sites' => $sites ) );
	}

}