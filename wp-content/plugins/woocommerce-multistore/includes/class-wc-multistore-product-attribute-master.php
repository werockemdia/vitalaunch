<?php
/**
 * Product Attribute Master Handler
 *
 * This handles product attribute master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Attribute_Master
 */
class WC_Multistore_Product_Attribute_Master extends WC_Multistore_Abstract_Term_Master {

	public $attribute;

	public $data;


	public function __construct( $attribute ) {
		$this->attribute = $attribute;
		$this->data = $this->set_data();
	}

	public function set_data(){
		$tax_object =  $this->attribute->get_taxonomy_object();

		$data = array();
		$data['id'] = $this->attribute->get_id();
		$data['name'] = $this->attribute->get_name();
		$data['slug'] = $this->attribute->get_name();
		$data['variation'] = $this->attribute->get_variation();
		$data['taxonomy'] = $this->attribute->is_taxonomy();
		$data['position'] = $this->attribute->get_position();
		$data['visible'] = $this->attribute->get_visible();
		if( $data['id'] > 0 ){
			$data['attribute_name'] = $tax_object->attribute_name;
			$data['attribute_label'] = $tax_object->attribute_label;
			$data['attribute_type'] = $tax_object->attribute_type;
			$data['attribute_orderby'] = $tax_object->attribute_orderby;
			$data['attribute_public'] = $tax_object->attribute_public;
		}

		$data['terms'] = $this->get_terms();

		return apply_filters( 'wc_multistore_master_attribute_data', $data, $this->attribute );
	}

	public function get_terms(){
		$data  = array();
		$terms = $this->attribute->get_terms();

		if( empty( $terms ) && $this->attribute->get_id() > 0 ){
			return $terms;
		}

		if( ! $this->attribute->get_id() > 0 ){
			return $this->attribute->get_options();
		}

		foreach ( $terms as $key => $term ){
			$wc_multistore_attribute_master = new WC_Multistore_Product_Attribute_Term_Master( $term );
			$data[$key] = $wc_multistore_attribute_master->data;
		}

		return $data;
	}
}