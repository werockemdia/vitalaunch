<?php
/**
 * Variation Master Product Handler
 *
 * This handles variation master product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Variation_Master
 */
class WC_Multistore_Product_Variation_Master extends WC_Multistore_Abstract_Product_Master {

	public function get_attributes(){
		return $this->wc_product->get_attributes('edit');
	}

	public function get_categories() {
		return array();
	}

	public function get_ajax_stock_data(){
		$sites = WOO_MULTISTORE()->active_sites;
		$data = array();

		foreach ( $sites as $site ){
			if( $this->should_sync_stock_to( $site->get_id() ) ) {
				$product_data = array();
				$product_data['action'] = 'wc_multistore_child_receive_stock';
				$product_data['parent'] = $this->wc_product->get_parent_data();
				$product_data['stock_quantity'] = $this->wc_product->get_stock_quantity('edit');
				$product_data['master_product_id'] = $this->wc_product->get_id();
				$product_data['master_product_sku'] = $this->wc_product->get_sku('edit');
				$product_data['master_blog_id'] =  get_current_blog_id();
				$product_data['blog_id'] =  $site->get_id();
				$product_data['ajax_url'] = $site->get_url().'/wp-admin/admin-ajax.php';
				$data[] = $product_data;
			}
		}

		return $data;
	}

	public function should_sync_stock_to( $site ){
		$sites = WOO_MULTISTORE()->sites;

		if( $sites[$site]->settings['override__synchronize-stock'] == 'yes' ){
			return false;
		}

		if(  ! $this->should_publish_to($site) ){
			return false;
		}

		if( $sites[$site]->settings['child_inherit_changes_fields_control__variations_stock'] != 'yes' ){
			return false;
		}

		return true;
	}
}