<?php
/**
 * Abstract Master Term Handler
 *
 * This handles Abstract master term related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Term_Master
 */
class WC_Multistore_Abstract_Term_Master{

	public $term;

	public $data;


	public function __construct( $term ) {
		$this->term = $term;
		$this->data = $this->set_data();
	}

	public function set_data(){
		$data = array();
		$data['master_blog_id'] = get_current_blog_id();
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
		$data['thumbnail'] = $this->get_image();
		$data['order'] = get_term_meta( $this->term->term_id, 'order', true );
		$data['meta'] = get_term_meta( $this->term->term_id );

		return apply_filters( 'wc_multistore_master_term_data', $data, $this->term );
	}

	public function get_image(){
		$thumbnail_id = get_term_meta( $this->term->term_id, 'thumbnail_id', true );

		if( empty($thumbnail_id) ){
			return $thumbnail_id;
		}

		$attachment = get_post( $thumbnail_id );

		if( ! $attachment ){
			return $thumbnail_id;
		}

		$wc_multistore_image_master = new WC_Multistore_Image_Master($attachment);
		return $wc_multistore_image_master->data;
	}


}