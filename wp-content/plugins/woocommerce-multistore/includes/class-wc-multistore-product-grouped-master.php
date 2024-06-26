<?php
/**
 * Grouped Master Product Handler
 *
 * This handles grouped master product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Grouped_Master
 */
class WC_Multistore_Product_Grouped_Master extends WC_Multistore_Abstract_Product_Master{

	public function get_data() {
		$data = parent::get_data();
		$data['children'] = $this->get_children();
		return apply_filters('wc_multistore_master_product_grouped_data', $data );
	}

	public function get_children(){
		$children = $this->wc_product->get_children();
		$wc_multistore_children = array();

		if( empty( $children ) || $this->depth ){
			return $wc_multistore_children;
		}

		foreach ( $children as $child_id ){
			$wc_product_child = wc_get_product( $child_id );
			if( ! $wc_product_child ){
				$logger = wc_get_logger();
				$data   = array(
					$child_id => 'skipped_grouped_child_id',
				);
				$msg    = print_r( $data, true );
				$logger->add( 'woomultistore', $msg );
				continue;
			}
			$classname = wc_multistore_get_product_class_name('master',$wc_product_child->get_type());
			$multistore_product_master = new $classname( $wc_product_child, true );
			update_post_meta($multistore_product_master->wc_product->get_id(), '_woonet_settings', $this->settings );
			$wc_multistore_children[] = $multistore_product_master->get_data();
		}

		return $wc_multistore_children;
	}

}