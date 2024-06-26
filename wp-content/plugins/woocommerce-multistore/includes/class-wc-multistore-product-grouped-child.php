<?php
/**
 * Grouped Child Product Handler
 *
 * This handles grouped child product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Grouped_Child
 */
class WC_Multistore_Product_Grouped_Child extends WC_Multistore_Abstract_Product_Child {
	public function update() {
		parent::update();

		if ( $this->wc_product->get_id() == 0 || ( $this->wc_product->get_id() > 0 && $this->has_publish_changes_enabled() )  ) {
			$this->wc_product->set_children( $this->get_children() );
		}
	}

	public function get_children(){
		$children = $this->data['children'];
		$children_ids = array();

		if( empty( $children ) ){
			return $children_ids;
		}

		foreach ( $children as $child ){
			$type = ucfirst($child['product_type']);
			$classname = 'WC_Multistore_Product_'. $type . '_Child';
			$multistore_child_product = new $classname( $child );
			$multistore_child_product->update();
			$multistore_child_product->save();
			$children_ids[] = $multistore_child_product->wc_product->get_id();
		}

		return $children_ids;
	}
}