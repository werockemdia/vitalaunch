<?php
/**
 * Data update Handler
 *
 * This data update related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Data_Update
 */
class WC_Multistore_Data_Update {

	public $is_up_to_date = false;

	public function __construct(){

		if( ! WOO_MULTISTORE()->license->is_active()){
			return;
		}

		if( ! WOO_MULTISTORE()->setup->is_complete ){
			return;
		}

		$this->is_up_to_date = $this->is_up_to_date();

		if( ! WOO_MULTISTORE()->permission ){ return; }

		$this->hooks();
	}

	public function is_up_to_date(){
		$old_options = get_site_option('mstore_options');
		if ( ! empty( $old_options ) && isset( $old_options['version'] ) && version_compare( $old_options['version'], '5.0.0', '<' ) ) {
			return false;
		}

		$options = get_site_option('wc_multistore_settings');
		if ( ! empty( $options ) && isset( $options['version'] ) && version_compare( $options['version'], '5.0.0', '<' ) ) {
			return false;
		}

		if ( ! is_multisite() && ! empty( get_site_option('wc_multistore_global_images') ) && ! empty( $options ) && isset( $options['version'] ) && version_compare( $options['version'], '5.0.9', '<' ) ) {
			return false;
		}

		return true;
	}

	public function hooks(){
		if ( $this->is_up_to_date ) {
			return;
		}
		add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ), 999 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );
		add_action( 'network_admin_notices', array( $this, 'network_admin_notices' ) );
		add_action( 'admin_notices', array( $this, 'network_admin_notices' ) );
	}

	public function network_admin_menu() {
		// only if superadmin
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}


		add_submenu_page('woonet-woocommerce', __( 'Upgrade', 'woonet' ),	__( 'Upgrade', 'woonet' ),'manage_woocommerce','woonet-upgrade', array( $this, 'update_run' ) );
	}

	public function admin_menu() {
		// only if superadmin

		add_submenu_page('woonet-woocommerce', __( 'Upgrade', 'woonet' ),	__( 'Upgrade', 'woonet' ),'manage_woocommerce','woonet-upgrade', array( $this, 'update_run' ) );
	}

	public function network_admin_notices() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$this->update_wizard_notice();
		}
	}

	/**
	 * Updates routines
	 */
	public function update_wizard_notice() {
		include WOO_MSTORE_PATH . 'includes/admin/views/html-notice-update.php';
	}


	public function update_run() {
		//set the start update option to let know others there's an update in progress
		add_site_option( 'mstore_update_wizard_started', 'true' );

		echo '<div class="wrap">';
		echo '<h1>Update</h1>';
		echo '<br/>';

		$options = get_site_option('wc_multistore_settings');

		$version = $options['version'];

		if ( version_compare( $version, WOO_MSTORE_VERSION, '<' ) ) {
			if ( version_compare( $version, '1.5.1', '<' ) && is_multisite() ) {
				include_once( WOO_MSTORE_PATH . 'updates/update-1.5.1.php' );

				//update the options, in case of timeout, to allow later for resume
				$options['version'] = '1.5';
				update_site_option('wc_multistore_settings', $options);
			}

			if ( version_compare( $version, '2.0.17', '<' ) && is_multisite() ) {
				include_once( WOO_MSTORE_PATH . 'updates/update-2.0.17.php' );

				$options['version'] = '2.0.17';
				update_site_option('wc_multistore_settings', $options);
			}

			if ( version_compare( $version, '5.0.0', '<' ) ) {
				include_once( WOO_MSTORE_PATH . 'updates/update-5.0.0.php' );

				$options['version'] = '5.0.0';
				update_site_option('wc_multistore_settings', $options);
				delete_site_option('wc_multistore_sites2' );
				delete_site_option('wc_multistore_master_connect2' );
			}

			if ( version_compare( $version, '5.0.9', '<' ) ) {
				include_once( WOO_MSTORE_PATH . 'updates/update-5.0.9.php' );

				$options['version'] = '5.0.9';
				update_site_option('wc_multistore_settings', $options);
			}
		}

		delete_site_option( 'mstore_update_wizard_started' );

		//set the last version
		$options['version'] = WOO_MSTORE_VERSION;
		update_site_option('wc_multistore_settings', $options);

		wp_redirect( network_admin_url( 'admin.php?page=woonet-woocommerce' ) );

		echo '<p>' . __( 'Update successfully completed.', 'woonet' ) . '</p>';
		echo '</div>';
	}

	public function enqueue_scripts(){
		wp_enqueue_script( 'wc-multistore-data-update-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-data-update.js', array( 'jquery' ), WOO_MSTORE_VERSION );
	}
}