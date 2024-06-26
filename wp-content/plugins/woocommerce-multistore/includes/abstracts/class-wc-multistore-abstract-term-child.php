<?php
/**
 * Abstract Child Term Handler
 *
 * This handles abstract child Term related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Term_Child
 */
class WC_Multistore_Abstract_Term_Child{

	public $term;

	public $data;

	public $site_settings;

	public function __construct( $term ){
		if( is_numeric( $term ) ){

		}else{
			if ( class_exists( 'SitePress' ) ) {
				global $sitepress;
				$has_wpml_filter = remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
			}

			$child_term_id = wc_multistore_get_child_term_id( $term['term_id'] );
			if( $child_term_id ){
				$this->term = get_term( $child_term_id );
			}else{
				if( $term_exists = get_term_by('slug', $term['slug'], $term['taxonomy'] , ARRAY_A ) ){
					$this->term = get_term( $term_exists['term_id'] );
				}else{
					$result = wp_insert_term( $term['name'], $term['taxonomy'], array( 'slug' => $term['slug'] ) );
					$this->term = get_term( $result['term_id'] );
				}
			}

			if ( class_exists( 'SitePress' ) ) {
				if( $has_wpml_filter ){
					add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
				}
			}

			$this->site_settings = wc_multistore_get_site_settings();
			$this->data = $term;
		}

	}


	public function update(){
		$args = array();

		$args['name'] = $this->data['name'];
		$args['slug'] = $this->data['slug'];
		$args['description'] = wp_kses_post( $this->data['description'] );
		$args['parent'] = wc_multistore_get_child_term_id( $this->data['parent'] );

		remove_filter('pre_term_description', 'wp_filter_kses' );
		remove_filter('term_description', 'wp_kses_data' );

		if ( class_exists( 'SitePress' ) ) {
			global $sitepress;
			$has_wpml_filter = remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
		}

		wp_update_term( $this->term->term_id, $this->data['taxonomy'], $args );

		if ( class_exists( 'SitePress' ) ) {
			if( $has_wpml_filter ){
				add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
			}
		}

		add_filter('pre_term_description', 'wp_filter_kses' );
		add_filter('term_description', 'wp_kses_data' );

		update_term_meta( $this->term->term_id, 'thumbnail_id', $this->get_image_id() );
		update_term_meta( $this->term->term_id, '_woonet_master_term_id', $this->data['term_id']);
		update_term_meta( $this->term->term_id, 'order', $this->data['order'] );

		do_action( 'wc_multistore_child_term_saved', $this->term, $this->data );
	}

	public function get_image_id(){
		if( empty($this->data['thumbnail'])  ){
			return $this->data['thumbnail'];
		}

		$wc_multistore_image_child = new WC_Multistore_Image_Child($this->data['thumbnail']);

		return $wc_multistore_image_child->save();
	}
	
}