<?php
/**
 * API Stores
 *
 * @since: 4.1.6
 **/

defined( 'ABSPATH' ) || exit;

class WC_Multistore_Stores_Rest_Api {
	/**
	 * Add action hooks on instantiation
	 **/
	public function __construct() {
		add_action( 'rest_api_init', array( $this,'register_rest_route' ) );
	}

	public function register_rest_route(){
		// class loaded by rest_api_init hook.
		register_rest_route( 'woonet/v1', 'stores', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_stores' ),
			'permission_callback' => '__return_true',
		));
	}

	public function get_stores( WP_REST_Request $request ) {
        $sites = WOO_MULTISTORE()->site->get_sites();
        $site_ids = array();

        foreach ( $sites as $site ) {
            $site_ids[] = array(
                'id'  => $site->get_id(),
                'url' => $site->get_url(),
            );
        }

        return $site_ids;
	}

}

new WC_Multistore_Stores_Rest_Api();
