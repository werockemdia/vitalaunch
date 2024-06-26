<?php
/**
 * Ajax Product category child handler.
 *
 * This handles ajax product category child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Product_Category_Child
 */
class WC_Multistore_Ajax_Product_Category_Child {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if( WOO_MULTISTORE()->site->get_type() == 'master' ){return;}

		add_action( 'wp_ajax_wc_multistore_update_child_product_category', array( $this, 'wc_multistore_update_child_product_category') );
		add_action( 'wp_ajax_nopriv_wc_multistore_update_child_product_category', array( $this, 'wc_multistore_update_child_product_category') );
	}

	public function wc_multistore_update_child_product_category(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->site->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}
		$wc_multistore_product_category_child = new WC_Multistore_Product_Category_Child($_REQUEST['data']);
		$wc_multistore_product_category_child->update();

		echo wp_json_encode(
			array(
				'status' => 'success'
			)
		);
		wp_die();
	}

}