<?php
/**
 * Integrate WPC Countdown Timer
 * URL: https://wordpress.org/plugins/wpc-countdown-timer/
 * Plugin URL: https://wordpress.org/plugins/wpc-countdown-timer/
 *
 * @since 4.1.5
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_WPC_COUNTDOWN_TIMER {

	public $meta_keys = array(
		'wooct_active',
		'wooct_style',
		'wooct_time_start',
		'wooct_time_end',
		'wooct_text_above',
		'wooct_text_under',
		'wooct_text_ended',
	);

	public function __construct() {
		if ( is_multisite() ) {
			add_filter( 'wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys' ), PHP_INT_MAX, 1 );
		}
	}

	public function add_meta_keys( $meta_keys ) {
		return array_merge( $meta_keys, $this->meta_keys );
	}
}

new WOO_MSTORE_INTEGRATION_WPC_COUNTDOWN_TIMER();
