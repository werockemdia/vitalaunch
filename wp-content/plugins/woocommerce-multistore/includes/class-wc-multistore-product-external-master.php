<?php
/**
 * External Master Product Handler
 *
 * This handles external master product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_External_Master
 */
class WC_Multistore_Product_External_Master extends WC_Multistore_Abstract_Product_Master{

	public function get_data() {
		$data =  parent::get_data();
		$data['product_url'] = $this->wc_product->get_product_url('edit');
		$data['button_text'] = $this->wc_product->get_button_text('edit');
		return apply_filters('wc_multistore_master_product_external_data', $data );
	}

}