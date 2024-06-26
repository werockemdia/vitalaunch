<?php
/**
 * Product Attribute Child Handler
 *
 * This handles product attribute child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Attribute_Child
 */
class WC_Multistore_Product_Attribute_Child extends WC_Multistore_Abstract_Term_Child {


	public $wc_attribute;

	public $data;
	
	public $site_settings;

	public function __construct( $attribute ){
		$attribute = apply_filters( 'wc_multistore_attribute_data', $attribute );

		if( is_numeric( $attribute ) ){

		}else{
			if( isset( $attribute['id'] ) && $attribute['id'] > 0 ){
				$child_attribute_id = wc_multistore_get_child_product_attribute( $attribute['attribute_name'] );
				if ( ! empty( $child_attribute_id ) ) {
					$this->wc_attribute = new WC_Product_Attribute();
					$this->wc_attribute->set_id($child_attribute_id);
					$this->wc_attribute->set_name($attribute['name']);
				}else{
					$child_attribute_id = wc_multistore_create_child_product_attribute($attribute);
					$this->wc_attribute = new WC_Product_Attribute();
					$this->wc_attribute->set_id($child_attribute_id);
				}
			}else{
				$this->wc_attribute =  new WC_Product_Attribute();
				$this->wc_attribute->set_id(0 );
				$this->wc_attribute->set_name( $attribute['name'] );
				$this->wc_attribute->set_variation( $attribute['variation'] );
//				$this->wc_attribute->set_is_taxonomy( $attribute['taxonomy'] );
				$this->wc_attribute->set_options( $attribute['terms'] );
				$this->wc_attribute->set_visible( $attribute['visible'] );
			}

			$this->site_settings = wc_multistore_get_site_settings();
			$this->data = $attribute;
		}

	}


	public function update(){
		$args = array(
			'slug'    => $this->data['attribute_name'],
			'attribute_type'    => $this->data['attribute_type'],
			'type'              => $this->data['attribute_type'],
			'attribute_orderby' => $this->data['attribute_orderby'],
			'has_archives'      => $this->data['attribute_public'],
		);

		if ( $this->site_settings['child_inherit_changes_fields_control__attribute_name'] == 'yes' ) {
			$this->wc_attribute->set_name( $this->data['name'] );
			$args['name'] = $this->data['attribute_label'];
		}

		$this->wc_attribute->set_options( $this->get_options() );
		$this->wc_attribute->set_position( $this->data['position'] );
		$this->wc_attribute->set_visible( $this->data['visible'] );
		$this->wc_attribute->set_variation( $this->data['variation'] );
//		$this->wc_attribute->set_is_taxonomy( $this->data['taxonomy'] );

		wc_update_attribute( $this->wc_attribute->get_id(), $args );
	}


	public function save(){
		if( $this->wc_attribute->get_id() ){
			$this->update();
		}

		do_action('wc_multistore_child_attribute_saved', $this->wc_attribute, $this->data );

		return $this->wc_attribute;
	}

	public function get_options(){
		if( empty( $this->data['terms'] )  ){
			return array();
		}

		$terms   = array();

		foreach ( $this->data['terms'] as $product_term ) {
			$wc_multistore_attribute_child = new WC_Multistore_Product_Attribute_Term_Child($product_term);
			$wc_multistore_attribute_child->update();

			if ( $wc_multistore_attribute_child->term->term_id ) {
				$terms[] = $wc_multistore_attribute_child->term->term_id;
			}
		}

		return $terms;
	}
}