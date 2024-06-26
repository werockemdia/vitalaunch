<?php
/**
 * Variable Child Product Handler
 *
 * This handles variable child product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Variable_Child
 */
class WC_Multistore_Product_Variable_Child extends WC_Multistore_Abstract_Product_Child {

	public $variation_errors;

	public function update() {
		parent::update();

		if ( $this->wc_product->get_id() == 0){
			parent::save();
		}

		if ( $this->wc_product->get_id() == 0 || ( $this->wc_product->get_id() > 0 && $this->has_publish_changes_enabled() )  ) {
			$this->wc_product->set_children( $this->get_children() );
		}
	}

	public function get_children(){
		$variations = $this->data['variations'];
		$ids = $this->wc_product->get_children();


		if( empty( $variations ) ){
			return null;
		}

		if( $this->site_settings['child_inherit_changes_fields_control__variations'] != 'yes' ){
			return $ids;
		}

		$new_ids = array();
		foreach ( $variations as $variation ){
			$wc_multistore_product_variation_child = new WC_Multistore_Product_Variation_Child($variation);
			$wc_multistore_product_variation_child->update();
			$wc_multistore_product_variation_child->wc_product->set_parent_id( $this->wc_product->get_id() );
			$result = $wc_multistore_product_variation_child->save();
			if( $result['status'] == 'success' ){
				$new_ids[] = $wc_multistore_product_variation_child->wc_product->get_id();
			}else{
				$this->variation_errors[] =  $result;
			}
		}

		if( ! empty( $ids ) ){
			foreach ( $ids as $id ){
				if( ! in_array( $id, $new_ids ) ){
					$variation = wc_get_product($id);
					if( $variation && $variation->get_type() == 'variation' ){
						$variation->delete(true);
					}
				}
			}
		}

		return $new_ids;
	}


	public function get_sync_data(){
		$post_type_object = get_post_type_object( 'product' );
		$data = array(
			'status' => 'success',
			'id' => $this->wc_product->get_id(),
			'sku' => $this->wc_product->get_sku('edit'),
			'edit_link' => admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $this->wc_product->get_id() ) ),
			'variation_errors' => $this->variation_errors
		);

		return $data;
	}

}