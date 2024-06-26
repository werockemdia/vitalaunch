<?php
/**
 * Variable Master Product Handler
 *
 * This handles variable master product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Variable_Master
 */
class WC_Multistore_Product_Variable_Master extends WC_Multistore_Abstract_Product_Master {

	public function get_data() {
		$data = parent::get_data();
		$data['variations'] = $this->get_variations();
		return apply_filters('wc_multistore_master_product_variable_data', $data );
	}


	public function get_variations(){
		$wc_multistore_variations = array();
		$variations = $this->wc_product->get_children();

		if( empty( $variations ) ){
			return $wc_multistore_variations;
		}

		foreach ( $variations as $variation_id ){
			$wc_product_variation = wc_get_product( $variation_id );

			if( $wc_product_variation->get_type() == 'variation' ){
				$variation_master = new WC_Multistore_Product_Variation_Master( $wc_product_variation );
				update_post_meta($variation_master->wc_product->get_id(), '_woonet_settings', $this->settings );
				$wc_multistore_variations[] = $variation_master->get_data();
			}
		}

		return $wc_multistore_variations;
	}

}