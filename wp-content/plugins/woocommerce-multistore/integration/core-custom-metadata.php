<?php
/**
 * Sync custom metadata
 *
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_Sync_Custom_Meta {

	private $metadata;

	private $metadata_options;

	public function __construct() {
		$this->metadata_options = get_site_option( 'wc_multistore_custom_metadata', array() );
		if ( ! empty( $this->metadata_options ) && is_array( $this->metadata_options ) ) {
			$this->metadata = array_keys( $this->metadata_options );
		}

		add_filter( 'wc_multistore_master_product_data', array( $this, 'add_metadata' ), 10, 2 );
		add_action( 'wc_multistore_child_product_saved', array( $this, 'sync_metadata' ), 10, 2 );
	}


	public function add_metadata( $data, $wc_product ) {
		$metadata = array();

		if ( WOO_MULTISTORE()->settings['sync-custom-metadata'] != 'yes' ) {
			return $data;
		}

		if ( empty( $this->metadata) ) {
			return $data;
		}

		$data['_custom_metadata'] = array();

		foreach ( $this->metadata as $meta_key ) {
			$meta_value = $wc_product->get_meta( $meta_key );

			if ( ! empty( $meta_value ) ) {
				$metadata [ $meta_key ] = $meta_value;
			}
		}


		if ( ! empty( $this->metadata_options ) ) {
			foreach ( $this->metadata_options  as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $k => $v ) {
						if ( isset( $metadata [ $key ] ) ) {
							$meta_value = $metadata [ $key ];
						} else {
							$meta_value = array();
						}

						if ( isset( $data['_custom_metadata'][ $k ] ) ) {
							$data['_custom_metadata'][ $k ][ $key ] = $meta_value;
						} else {
							$data['_custom_metadata'][ $k ]         = array();
							$data['_custom_metadata'][ $k ][ $key ] = $meta_value;
						}
					}
				}
			}
		}

		return $data;

	}

	public function sync_metadata( $wc_product, $data ) {
		if( is_multisite() ){
			$site = WOO_MULTISTORE()->sites[get_current_blog_id()];
		}else{
			$site = WOO_MULTISTORE()->site;
		}

		if ( empty( $data['_custom_metadata'] ) ) {
			return;
		}

		if ( ! isset( $data['_custom_metadata'][ $site->get_id() ] ) ) {
			return;
		}

		if ( ! empty( $data['_custom_metadata'][ $site->get_id() ] ) ) {
			foreach ( $data['_custom_metadata'][ $site->get_id() ] as $meta_key => $meta_value ) {
				if( empty($meta_value) ){
					delete_post_meta( $wc_product->get_id(), $meta_key );
				}else{
					update_post_meta($wc_product->get_id(), $meta_key, $meta_value);
				}
			}
		}
	}
}

new WOO_MSTORE_INTEGRATION_Sync_Custom_Meta();
