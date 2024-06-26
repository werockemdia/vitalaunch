<?php
/**
 * Sync Ploylang language metadata.
 *
 * @since 4.1.6
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_WC_POLYLANG {

	public function __construct() {
		add_filter( 'wc_multistore_master_product_data', array( $this, 'add_pll_data' ), 10, 1 );
		add_action( 'wc_multistore_child_product_saved', array( $this, 'set_pll_language' ), 10, 2 );
	}

	public function add_pll_data( $data ) {
		$data['pll'] = pll_get_post_language( $data['ID'] );;

		return $data;
	}

	public function set_pll_language( $wc_product, $data ) {
		if ( ! empty( $data['pll'] ) ) {
			pll_set_post_language( $wc_product->get_id(), strip_tags( $data['pll'] ) );
		}
	}
}

new WOO_MSTORE_INTEGRATION_WC_POLYLANG();