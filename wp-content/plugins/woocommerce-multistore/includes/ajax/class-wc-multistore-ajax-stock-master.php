<?php
/**
 * Ajax Stock master handler.
 *
 * This handles ajax stock master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Stock_Master
 */
class WC_Multistore_Ajax_Stock_Master {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }
		if ( WOO_MULTISTORE()->site->get_type() != 'master' ) { return; }

		add_action( 'wp_ajax_nopriv_wc_multistore_master_receive_stock', array( $this, 'wc_multistore_master_receive_stock' ) );
		add_action( 'wp_ajax_wc_multistore_master_receive_stock', array( $this, 'wc_multistore_master_receive_stock' ) );
	}


	public function wc_multistore_master_receive_stock(){
		$master_product_id = wc_multistore_product_get_master_product_id( $_REQUEST['master_product_id'], $_REQUEST['master_product_sku'] );

		$wc_product = wc_get_product( $master_product_id );

		if( ! $wc_product ){
			echo wp_json_encode(array('status' => 'success'));
			wp_die();
		}

		$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
		$wc_multistore_master_product = new $classname($wc_product);
		$wc_multistore_master_product->update_stock($_REQUEST['stock_quantity'], $_REQUEST['blog_id']);

		if( $wc_product->is_type('variation') ){
			$parent_id = $wc_product->get_parent_id();
			$wc_product_parent = wc_get_product( $parent_id );
			$parent_classname = wc_multistore_get_product_class_name( 'master', $wc_product_parent->get_type() );
			$wc_multistore_parent_master_product = new $parent_classname($wc_product_parent);
			$wc_multistore_parent_master_product->update_stock($_REQUEST['parent']['stock_quantity'], $_REQUEST['blog_id']);
		}

		echo wp_json_encode(array('status' => 'success'));
		wp_die();
	}

}