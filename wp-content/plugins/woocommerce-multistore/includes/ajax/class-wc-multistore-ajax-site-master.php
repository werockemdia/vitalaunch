<?php
/**
 * Ajax Site Master handler.
 *
 * This handles ajax site master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Site_Master
 */
class WC_Multistore_Ajax_Site_Master {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if( WOO_MULTISTORE()->site->get_type() == 'child' ){ return; }

		add_action( 'wp_ajax_wc_multistore_connect_child_site', array( $this, 'wc_multistore_connect_child_site') );
		add_action( 'wp_ajax_nopriv_wc_multistore_connect_child_site', array( $this, 'wc_multistore_connect_child_site') );

		add_action( 'wp_ajax_wc_multistore_get_child_site_version', array( $this, 'wc_multistore_get_child_site_version') );
		add_action( 'wp_ajax_nopriv_wc_multistore_get_child_site_version', array( $this, 'wc_multistore_get_child_site_version') );

		add_action( 'wp_ajax_wc_multistore_get_master_status', array( $this, 'wc_multistore_get_master_status' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_get_master_status', array( $this, 'wc_multistore_get_master_status' ) );

		add_action( 'wp_ajax_wc_multistore_save_child_site', array( $this, 'wc_multistore_save_child_site' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_save_child_site', array( $this, 'wc_multistore_save_child_site' ) );

		add_action( 'wp_ajax_wc_multistore_get_master_license_data', array( $this, 'wc_multistore_get_master_license_data' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_get_master_license_data', array( $this, 'wc_multistore_get_master_license_data' ) );

		add_action( 'wp_ajax_wc_multistore_reset_child_site_from_master', array( $this, 'wc_multistore_reset_child_site_from_master' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_reset_child_site_from_master', array( $this, 'wc_multistore_reset_child_site_from_master' ) );
	}

	public function wc_multistore_connect_child_site(){
		if ( ! empty( $_POST['url'] ) ) {
			if ( filter_var( $_POST['url'], FILTER_VALIDATE_URL ) === false ) {
				wp_send_json(
					array(
						'status'   => 'failed',
						'error'   => 1,
						'message' => 'Please enter a valid URL.',
					)
				);

				wp_die();
			}

			$url         = esc_url_raw( $_POST['url'] );
			$child_sites = get_option( 'wc_multistore_sites', array() );
			$site_key    = sha1( uniqid() );
			$uuid        = md5( $url );

			$child_sites[ $uuid ] = array(
				'url'   => $url,
				'name'   => $url,
				'date_added' => time(),
				'site_key'   => $site_key,
				'id'         => $uuid,
			);

			if ( update_option( 'wc_multistore_sites', $child_sites ) ) {
				// now hide the wizard alert
				update_option( 'wc_multistore_setup_wizard_complete', 'yes' );

				wp_send_json(
					array(
						'status'   => 'success',
						'success'  => 1,
						'message'  => 'Site successfully added',
						'copy_url' => admin_url( 'admin-ajax.php?action=woonet_verify&k=' . $site_key . '&id=' . $uuid ),
					)
				);
				wp_die();
			} else {
				wp_send_json(
					array(
						'status'   => 'failed',
						'error'   => 1,
						'message' => 'Can not save site.',
					)
				);
			}
			wp_die();
		}

		if( !empty($_POST['key']) ){
			wp_send_json(
				array(
					'status'   => 'success',
					'message'  => 'Site successfully added',
				)
			);
			wp_die();
		}

		wp_send_json(
			array(
				'status'   => 'failed',
				'error' => 1,
				'msg'   => 'No valid URL provided.',
			)
		);

		wp_die();
	}


	public function wc_multistore_get_child_site_version() {
		if ( ! isset( $_POST['nonce'] ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions',
				'code' => '403'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		$wc_multistore_site_api_master = new WC_Multistore_Site_Api_Master();
		$site = WOO_MULTISTORE()->sites[$_POST['key']];

		$result = $wc_multistore_site_api_master->get_child_status($site);

		echo wp_json_encode( $result );
		wp_die();

	}

	public function wc_multistore_get_master_status() {
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);

			echo wp_json_encode( $result );
			wp_die();
		}

		$result = array(
			'status' => 'success',
			'version' => WOO_MSTORE_VERSION
		);
		echo wp_json_encode( $result );
		wp_die();
	}

	public function wc_multistore_save_child_site() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'woonet_delete_site' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions',
				'code' => '403'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		$site_id = $_REQUEST['id'];
		$url = $_REQUEST['url'];

		if( ! empty( $url ) ){
			if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
				wp_send_json(
					array(
						'status' => 'failed',
						'message' => 'Please enter a valid URL.',
						'code' => '500'
					)
				);
				wp_die();
			}
		}

		if( ! empty( $site_id ) && ! empty( $url ) ){
			WOO_MULTISTORE()->sites[$site_id]->set_name($url);
			WOO_MULTISTORE()->sites[$site_id]->set_url($url);
			WOO_MULTISTORE()->sites[$site_id]->save();

			$result = array(
				'status' => 'success',
				'message' => 'Site Saved'
			);
			wp_send_json( $result );
			wp_die();
		}

		wp_send_json(
			array(
				'status' => 'failed',
				'message' => 'Can not save site.',
				'code' => '500'
			)
		);
		wp_die();
	}

	public function wc_multistore_get_master_license_data() {
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);

			echo wp_json_encode( $result );
			wp_die();
		}
		$license_data = get_site_option( 'wc_multistore_license' );
		$license_key = isset( $license_data['key'] ) ? $license_data['key'] : '';

		$result = array(
			'status' => 'success',
			'license_data' => array(
				'key' => $license_key,
				'domain' => WOO_MSTORE_INSTANCE,
			)
		);
		echo wp_json_encode( $result );
		wp_die();
	}

	public function wc_multistore_reset_child_site_from_master() {
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);

			echo wp_json_encode( $result );
			wp_die();
		}

		$id = $_REQUEST['key'];
		$sites = get_site_option('wc_multistore_sites');
		unset( $sites[$id]);
		update_site_option('wc_multistore_sites', $sites );


		wp_die();
	}

}