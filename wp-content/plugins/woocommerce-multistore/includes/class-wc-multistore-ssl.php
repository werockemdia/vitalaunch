<?php
/**
 * SSL handler.
 *
 * This handles SSL functionality.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Multistore_Ssl
 */
class WC_Multistore_Ssl {
	public function __construct() {
		if ( ! defined( 'WOO_MOSTORE_DEV_ENV' ) || WOO_MOSTORE_DEV_ENV != true ) {
			return;
		}
		$this->init();
	}

	public function init() {
		// disable curl SSL validation.
		add_filter('http_request_args', array($this, 'disable_ssl_validation'), 10, 2);
		add_filter('http_request_reject_unsafe_urls', array($this, 'reject_unsafe_url'), 10, 2);
	}

	public function disable_ssl_validation( $params, $url ) {
		$params['sslverify'] = false;
        return $params;
	}

	public function reject_unsafe_url( $flag, $url ) {
		return false;
	}
}

