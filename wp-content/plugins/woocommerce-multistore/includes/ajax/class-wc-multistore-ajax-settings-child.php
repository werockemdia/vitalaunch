<?php
/**
 * Ajax settings child handler.
 *
 * This handles ajax settings child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Settings_Child
 */
class WC_Multistore_Ajax_Settings_Child {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if ( WOO_MULTISTORE()->site->get_type() == 'master' ){ return; }

		add_action( 'wp_ajax_woonet_wc_multistore_save_child_settings', array( $this, 'wc_multistore_save_child_settings' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_save_child_settings', array( $this, 'wc_multistore_save_child_settings' ) );
	}

	public function wc_multistore_save_child_settings(){
		if( $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id()){
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions',
				'code' => '403'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		$site_settings = $_REQUEST['site_settings'];
		unset($_REQUEST['site_settings']);

		$WC_Multistore_Settings = new WC_Multistore_Settings();
		$WC_Multistore_Settings->save($_REQUEST);

		WOO_MULTISTORE()->site->set_settings( $site_settings );
		WOO_MULTISTORE()->site->save();

		$data = array(
			'status' => 'success',
			'message' => 'Successfully saved settings for ' . WOO_MULTISTORE()->site->get_name(),
		);
		echo wp_json_encode($data);
		wp_die();
	}
}