<?php
/**
 * Variation Child Product Handler
 *
 * This handles variation child product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Variation_Child
 */
class WC_Multistore_Product_Variation_Child extends WC_Multistore_Abstract_Product_Child {

	public function get_attributes() {
		return $this->data['product_attributes'];
	}

	public function update() {
		$this->settings = $this->data['settings'];

		$this->wc_product->set_parent_id( $this->get_parent_id() );

		if ( $this->site_settings['override__synchronize-stock'] != 'yes' && $this->site_settings['child_inherit_changes_fields_control__variations_stock'] == 'yes' ) {
			$this->wc_product->set_manage_stock( $this->data['manage_stock'] );
			if( isset( $this->data['stock_quantity'] ) ){
				$this->wc_product->set_stock_quantity( $this->data['stock_quantity'] );
			}else{
				$this->wc_product->set_stock_quantity( null );
			}
			$this->wc_product->set_stock_status( $this->data['stock_status'] );
			$this->wc_product->set_low_stock_amount( $this->data['low_stock_amount'] );

			if ( $this->site_settings['child_inherit_changes_fields_control__allow_backorders'] == 'yes' ) {
				$this->wc_product->set_backorders( $this->data['backorders'] );
			}
		}

		if ( $this->site_settings['child_inherit_changes_fields_control__variations_data'] == 'yes' ) {
			$this->wc_product->set_name( $this->data['post_title'] );

			$this->wc_product->set_slug( $this->data['slug'] );

			if( isset( $this->data['post_date'] ) ){
				$this->wc_product->set_date_created( $this->data['post_date'] );
			}

			if( isset( $this->data['date_modified'] ) ){
				$this->wc_product->set_date_modified( $this->data['date_modified'] );
			}

			$this->wc_product->set_featured( $this->data['featured'] );

			$this->wc_product->set_catalog_visibility( $this->data['catalog_visibility'] );

			$this->wc_product->set_description( $this->data['description'] );

			$this->wc_product->set_short_description( $this->data['post_excerpt'] );

			$this->wc_product->set_total_sales( $this->data['total_sales'] );

			$this->wc_product->set_tax_class( $this->data['tax_class'] );

			$this->wc_product->set_sold_individually( $this->data['sold_individually'] );

			$this->wc_product->set_weight( $this->data['weight'] );

			$this->wc_product->set_length( $this->data['length'] );

			$this->wc_product->set_width( $this->data['width'] );

			$this->wc_product->set_height( $this->data['height'] );


			$this->wc_product->set_purchase_note( $this->data['purchase_note'] );

			$this->wc_product->set_attributes( $this->get_attributes() );

			$this->wc_product->set_menu_order( $this->data['menu_order'] );

			$this->wc_product->set_post_password( $this->data['post_password'] );

			$this->wc_product->set_virtual( $this->data['virtual'] );

			$this->wc_product->set_downloadable( $this->data['downloadable'] );

			$this->wc_product->set_downloads( $this->get_downloads() );

			$this->wc_product->set_shipping_class_id( $this->get_shipping_class_id() );

			$this->wc_product->set_image_id( $this->get_image_id() );

			$this->wc_product->set_gallery_image_ids( $this->get_gallery_image_ids() );

			$this->wc_product->set_download_limit( $this->data['download_limit'] );

			$this->wc_product->set_download_expiry( $this->data['download_expiry'] );

			$this->wc_product->set_reviews_allowed( $this->data['reviews_allowed'] );

			if( isset( $this->data['rating_counts'] ) ){
				$this->wc_product->set_rating_counts( $this->data['rating_counts'] );
			}else{
				$this->wc_product->set_rating_counts( array() );
			}

			$this->wc_product->set_review_count( $this->data['review_count'] );
		}

		if ( $this->site_settings['child_inherit_changes_fields_control__variations_status'] == 'yes' ) {
			$this->wc_product->set_status( $this->data['post_status'] );
		}

		if ( $this->site_settings['child_inherit_changes_fields_control__variations_sku'] == 'yes' ) {
			if( apply_filters( 'wc_multistore_set_sku', true, $this->data, $this->wc_product ) ){
				if( empty($this->data['sku']) && WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' ){
					$this->duplicate_sku = true;
				}

				if( !empty( $this->data['sku'] ) && ! wc_product_has_unique_sku( $this->wc_product->get_id() , $this->data['sku'] ) ){

					$this->duplicate_sku = true;
				}else{
					$this->wc_product->set_sku( $this->data['sku'] );
				}
			}
		}

		if ( $this->site_settings['child_inherit_changes_fields_control__variations_price'] == 'yes' && apply_filters( 'WOO_MSTORE_SYNC/sync_child/sync_variation_price', true ) === true ) {
			$this->wc_product->set_price( $this->data['price'] );
			$this->wc_product->set_regular_price( $this->data['regular_price'] );
		}

		if ( $this->site_settings['child_inherit_changes_fields_control__variations_sale_price'] == 'yes' && apply_filters( 'WOO_MSTORE_SYNC/sync_child/sync_variation_sale_price', true ) === true ) {
			$this->wc_product->set_sale_price( $this->data['sale_price'] );

			if( isset( $this->data['date_on_sale_from'] ) ){
				$this->wc_product->set_date_on_sale_from( $this->data['date_on_sale_from'] );
			}else{
				$this->wc_product->set_date_on_sale_from( '' );
			}

			if( isset( $this->data['date_on_sale_to'] ) ){
				$this->wc_product->set_date_on_sale_to( $this->data['date_on_sale_to'] );
			}else{
				$this->wc_product->set_date_on_sale_to( '' );
			}
		}


		if ( $this->site_settings['child_inherit_changes_fields_control__default_variations'] == 'yes' ) {
			if( ! empty( $this->data['default_attributes'] ) ){
				$this->wc_product->set_default_attributes( $this->data['default_attributes'] );
			}else{
				$this->wc_product->set_default_attributes( array() );
			}
		}


		$this->set_meta_data();
	}

	public function get_ajax_stock_data(){
		$data = array();

		if( ! $this->has_stock_sync_enabled() ){
			return $data;
		}

		if( ! $this->has_publish_enabled() ){
			return $data;
		}

		$data['action'] = 'wc_multistore_master_receive_stock';
		$data['parent'] = $this->wc_product->get_parent_data();
		$data['stock_quantity'] = $this->wc_product->get_stock_quantity('edit');
		$data['master_product_id'] = $this->wc_product->get_meta('_woonet_network_is_child_product_id');
		$data['master_product_sku'] = $this->wc_product->get_sku('edit');
		$data['blog_id'] =  WOO_MULTISTORE()->site->get_id();
		if( is_multisite() ){
			switch_to_blog( get_site_option('wc_multistore_master_store') );
			$data['ajax_url'] = admin_url( 'admin-ajax.php' );
			restore_current_blog();
		}else{
			$master_data = get_site_option('wc_multistore_master_connect');
			$data['ajax_url'] = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
		}

		return $data;
	}


	public function has_stock_sync_enabled() {
		if( $this->site_settings['override__synchronize-stock'] == 'yes' ){
			return false;
		}

		if( $this->site_settings['child_inherit_changes_fields_control__variations_stock'] != 'yes' ){
			return false;
		}

		return true;
	}
}