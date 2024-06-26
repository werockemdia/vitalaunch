<?php
/**
 * Request Handler
 *
 * This handles requests related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Request
 */
class WC_Multistore_Request{

	public function send( $url, $args ) {
		$result = array();
		$args['timeout'] = 60;
		$response = wp_remote_request( $url, $args );

		if( is_wp_error( $response ) ){
			$result['status'] = 'failed';
			$result['code'] = $response->get_error_code();
			$result['message'] = $response->get_error_message();

			return $result;
		}

		if( $response['response']['code'] == 200 ){
			$body = json_decode(wp_remote_retrieve_body($response),true);
			$result['status'] = $body['status'];
			$result['message'] = isset( $body['message'] ) ? $body['message'] : '';
			$result['code'] = isset( $body['code'] ) ? $body['code'] : '';
			$result['data'] = $body;
		}else{
			$result['status'] = 'failed';
			$result['code'] = $response['response']['code'];
			$result['message'] = $response['response']['message'];
		}

		return $result;
	}

}