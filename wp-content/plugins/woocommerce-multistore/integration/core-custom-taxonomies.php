<?php
/**
 * Sync custom taxonomies
 *
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_CUSTOM_TAXONOMIES {

	private $taxonomies       = array();

	private $taxonomy_options = array();

	public function __construct() {
		$this->taxonomy_options = get_site_option( 'wc_multistore_custom_taxonomy', array() );
		if ( ! empty( $this->taxonomy_options ) && is_array( $this->taxonomy_options ) ) {
			$this->taxonomies = array_keys( $this->taxonomy_options );
		}

		add_filter( 'wc_multistore_master_product_data', array( $this, 'add_taxonomy_terms' ), 10, 1 );
		add_action( 'wc_multistore_child_product_saved', array( $this, 'sync_taxonomy_terms' ), 10, 2 );
	}

	public function add_taxonomy_terms( $data ) {
		$custom_tax = array();
		$product_id = $data['ID'];
		$data['_custom_taxonomies'] = array();

		if ( WOO_MULTISTORE()->settings['sync-custom-taxonomy'] != 'yes' ) {
			return $data;
		}

		if ( empty( $this->taxonomies) ) {
			return $data;
		}

		foreach ( $this->taxonomies as $tax ) {
			$_terms = get_the_terms( $product_id, $tax );

			$custom_tax [ $tax ] = array();

			if ( ! empty( $_terms ) ) {
				foreach ( $_terms as $trm ) {
					$custom_tax [ $tax ][] = $trm->name;
				}
			}else{
				$custom_tax [ $tax ] = '';
			}
		}

		if ( ! empty( $this->taxonomy_options ) ) {
			foreach ( $this->taxonomy_options  as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $k => $v ) {
						if ( isset( $custom_tax [ $key ] ) ) {
							$tax_terms = $custom_tax [ $key ];
						} else {
							$tax_terms = array();
						}

						if ( isset( $data['_custom_taxonomies'][ $k ] ) ) {
							$data['_custom_taxonomies'][ $k ][ $key ] = $tax_terms;
						} else {
							$data['_custom_taxonomies'][ $k ]         = array();
							$data['_custom_taxonomies'][ $k ][ $key ] = $tax_terms;
						}
					}
				}
			}
		}

		return $data;

	}

	public function sync_taxonomy_terms( $wc_product, $data ) {
		if( is_multisite() ){
			$site = WOO_MULTISTORE()->sites[get_current_blog_id()];
		}else{
			$site = WOO_MULTISTORE()->site;
		}

		if ( empty( $data['_custom_taxonomies'] ) ) {
			return;
		}

		if ( ! isset( $data['_custom_taxonomies'][ $site->get_id() ] ) ) {
			return;
		}
		if ( ! empty( $data['_custom_taxonomies'][ $site->get_id() ] ) ) {
			foreach ( $data['_custom_taxonomies'][ $site->get_id() ] as $tax => $terms ) {
				wp_set_object_terms( $wc_product->get_id(), $terms, $tax );
			}
		}
	}

}


new WOO_MSTORE_INTEGRATION_CUSTOM_TAXONOMIES();
