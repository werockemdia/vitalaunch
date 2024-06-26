<?php
/**
 * API Orders
 *
 * @since: 4.1.6
 **/


defined( 'ABSPATH' ) || exit;

class WC_Multistore_Orders_Rest_Api {
    /**
     * Add action hooks on instantiation
     **/
    public function __construct() {
	    add_action( 'rest_api_init', array( $this,'register_rest_route' ) );
    }

	public function register_rest_route(){
		// class loaded by rest_api_init hook.
		register_rest_route( 'woonet/v1', 'orders', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_child_orders' ),
			'permission_callback' => '__return_true',
		));
	}
    
    public function get_child_orders( WP_REST_Request $request ) {
        $per_page = $request->get_param( 'per_page' );
        $page = $request->get_param( 'page' );

	    $network_orders = wc_multistore_get_network_orders( $per_page, $page, 'shop_order', '', '' );
        $output = array();
        
        if ( ! empty( $network_orders ) && ! empty( $network_orders['orders'] ) ) {
	        $output = $network_orders['orders'];
        }
        
        return $output;
    }
    
}

new WC_Multistore_Orders_Rest_Api();