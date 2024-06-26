<?php
/**
 * Product Category Master Handler
 *
 * This handles product category master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Category_Master
 */
class WC_Multistore_Product_Category_Master extends WC_Multistore_Abstract_Term_Master {
	public function sync(){
		global $WC_Multistore_Product_Category_Hooks_Master;
		if(is_multisite()){
			remove_action('edited_product_cat', array( $WC_Multistore_Product_Category_Hooks_Master, 'republish_category_changes' ) );
			foreach (WOO_MULTISTORE()->active_sites as $site){
				if( $site->settings['child_inherit_changes_fields_control__product_cat'] == 'yes' ){
					switch_to_blog($site->get_id());
					$wc_multistore_product_category_child = new WC_Multistore_Product_Category_Child($this->data);
					$wc_multistore_product_category_child->update();
					restore_current_blog();
				}
			}
			add_action('edited_product_cat', array( $WC_Multistore_Product_Category_Hooks_Master, 'republish_category_changes' ) );
		}else{
			foreach (WOO_MULTISTORE()->active_sites as $site){
				if( $site->settings['child_inherit_changes_fields_control__product_cat'] == 'yes' ){
					$wc_multistore_product_category_api_master = new WC_Multistore_Product_Category_Api_Master();
					$wc_multistore_product_category_api_master->send_data_to_child($this->data, $site->get_id());
				}
			}
		}

	}
}