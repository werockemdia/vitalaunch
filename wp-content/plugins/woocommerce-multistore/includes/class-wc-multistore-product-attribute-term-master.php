<?php
/**
 * Product Attribute Term Master Handler
 *
 * This handles product attribute master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Attribute_Term_Master
 */
class WC_Multistore_Product_Attribute_Term_Master extends WC_Multistore_Abstract_Term_Master {

	public function set_data(){
		$data = array();
		$data['term_id'] = $this->term->term_id;
		$data['name'] = $this->term->name;
		$data['slug'] = $this->term->slug;
		$data['term_group'] = $this->term->term_group;
		$data['term_taxonomy_id'] = $this->term->term_taxonomy_id;
		$data['taxonomy'] = $this->term->taxonomy;
		$data['description'] = $this->term->description;
		$data['parent'] = $this->term->parent;
		$data['count'] = $this->term->count;
		$data['filter'] = $this->term->filter;
//		$data['thumbnail'] = $this->get_image();
		$data['order'] = get_term_meta( $this->term->term_id, 'order', true );
		$data['meta'] = get_term_meta( $this->term->term_id );

		return apply_filters( 'wc_multistore_master_term_data', $data, $this->term );
	}
}