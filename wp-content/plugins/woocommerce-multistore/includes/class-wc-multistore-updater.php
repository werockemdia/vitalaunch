<?php
/**
 * Plugin update handler.
 *
 * This handles plugin update related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Updater
 */
class WC_Multistore_Updater {

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }

		$this->hooks();
	}
	
	public function hooks(){
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_plugin_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_call' ), 10, 3 );
		add_action('in_plugin_update_message-woocommerce-multistore/woocommerce-multistore.php',  array( $this, 'show_update_notification' ), 10, 2);
	}

	public function check_for_plugin_update( $checked_data ) {

		if ( empty( $checked_data->checked ) || ! isset( $checked_data->checked[ WOO_MSTORE_PLUGIN_BASE_NAME ] ) ) {
			return $checked_data;
		}

		$request_string = $this->prepare_request( 'plugin_update' );
        
        if ( $request_string === false ) {
			return $checked_data;
		}

        // Start checking for an update
        $request_uri = WOO_MSTORE_APP_API_URL . '?' . http_build_query( $request_string, '', '&' );

        $data = wp_remote_get( $request_uri );

		if ( is_wp_error( $data ) || $data['response']['code'] != 200 ) {
			return $checked_data;
		}

		$response_block = json_decode( $data['body'] );

		if ( ! is_array( $response_block ) || count( $response_block ) < 1 ) {
			$no_update      = true;
			$response_block = array(
				(object) array(
					'message' => new stdClass(),
				),
			);
		}

		// retrieve the last message within the $response_block
		$response_block = $response_block[ count( $response_block ) - 1 ];
		$response       = isset( $response_block->message ) ? $response_block->message : '';

		if ( is_object( $response ) && ! empty( $response ) ) {
			// include slug and plugin data
			$response->slug    = WOO_MSTORE_PLUGIN_SLUG;
			$response->plugin  = WOO_MSTORE_PLUGIN_BASE_NAME;
			$response->banners = array(
				'1x' => WOO_MSTORE_ASSET_URL . '/assets/images/banner-772x250.jpg',
				'2x' => WOO_MSTORE_ASSET_URL . '/assets/images/banner-1544x500.jpg',
			);

			$response->icons = array(
				'1x' => WOO_MSTORE_ASSET_URL . '/assets/images/icon-128x128.jpg',
				'2x' => WOO_MSTORE_ASSET_URL . '/assets/images/icon-256x256.jpg',
			);

			if ( isset( $no_update ) ) {
				$checked_data->no_update[ WOO_MSTORE_PLUGIN_BASE_NAME ] = $response;
			} else {
				$checked_data->response[ WOO_MSTORE_PLUGIN_BASE_NAME ] = $response;
			}
		}
		
		return $checked_data;
	}


	public function plugins_api_call( $def, $action, $args ) {
		if ( ! is_object( $args ) || ! isset( $args->slug ) || $args->slug != WOO_MSTORE_PLUGIN_SLUG ) {
			return false;
		}

		// $args->package_type = $this->package_type;

		$request_string = $this->prepare_request( $action, $args );

		if ( $request_string === false ) {
			return new WP_Error( 'plugins_api_failed', __( 'An error occour when try to identify the pluguin.', 'woonet' ) . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>' . __( 'Try again', 'woonet' ) . '&lt;/a>' );
		}

		$request_uri = WOO_MSTORE_APP_API_URL . '?' . http_build_query( $request_string, '', '&' );
		$data        = wp_remote_get( $request_uri );

		if ( is_wp_error( $data ) || $data['response']['code'] != 200 ) {
			return new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.', 'woonet' ) . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>' . __( 'Try again', 'woonet' ) . '&lt;/a>', $data->get_error_message() );
		}

		$response_block = json_decode( $data['body'] );
		// retrieve the last message within the $response_block
		$response_block = $response_block[ count( $response_block ) - 1 ];
		$response       = $response_block->message;

		if ( is_object( $response ) && ! empty( $response ) ) {
			// include slug and plugin data
			$response->slug   = WOO_MSTORE_PLUGIN_SLUG;
			$response->plugin = WOO_MSTORE_PLUGIN_BASE_NAME;

			$response->sections = (array) $response->sections;
			$response->banners  = array(
				'low'  => WOO_MSTORE_ASSET_URL . '/assets/images/banner-772x250.jpg',
				'high' => WOO_MSTORE_ASSET_URL . '/assets/images/banner-1544x500.jpg',
			);

			return $response;
		}
	}


	/**
	 * Prepare request to check version update.
	 *
	 * @param mixed $action
	 * @param mixed $args
	 */
	public function prepare_request( $action, $args = array() ) {
        if ( function_exists('is_multisite') && is_multisite() === false && get_option('wc_multistore_network_type') == 'child' ) {
            return $this->prepare_request_for_single_site($action, $args);
        } 

		global $wp_version;

		$license_data = get_site_option( 'wc_multistore_license' );
		$license_key = isset( $license_data['key'] ) ? $license_data['key'] : '';

		return array(
			'woo_sl_action'     => $action,
			'version'           => WOO_MSTORE_VERSION,
			'product_unique_id' => WOO_MSTORE_PRODUCT_ID,
			'licence_key'       => $license_key,
			'domain'            => WOO_MSTORE_INSTANCE,
			'wp-version'        => $wp_version,
		);
    }
    
    /**
     * Prepare request to check version update process for single site.
     *
     * @param mixed $action
     * @param mixed $args
     */
    public function prepare_request_for_single_site( $action, $args = array() ) {
        global $wp_version;
        
        $wc_multistore_site_api_child = new WC_Multistore_Site_Api_Child();
        
        $result = $wc_multistore_site_api_child->get_master_license_data();

        if ( !empty( $result['data']['license_data']['key']) && !empty($result['data']['license_data']['domain']) ) {
            return array(
                'woo_sl_action'     => $action,
                'version'           => WOO_MSTORE_VERSION,
                'product_unique_id' => WOO_MSTORE_PRODUCT_ID,
                'licence_key'       => $result['data']['license_data']['key'],
                'domain'            => $result['data']['license_data']['domain'],
                'wp-version'        => $wp_version,
            );
        }

        return false;
    }

	public function show_update_notification( $currentPluginMetadata, $newPluginMetadata ){
		// check "upgrade_notice"
		printf(
			'<br><strong>%s</strong>',
			__( 'You need to delete the WooMultistore bulk sync addon if you are using it, bulk sync is integrated into the WooMultistore since version 4.5.0', 'woonet' )
		);
	}
}