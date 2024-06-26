<?php
/**
 * Stock child update handler.
 *
 * This handles stock update for child products related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Stock_Hooks_Child
 */
class WC_Multistore_Stock_Hooks_Child {

	public function __construct() {
		$this->hooks();
	}

	public function hooks(){
		add_action( 'woocommerce_product_set_stock', array( $this, 'update_child_product_stock' ) );
		add_action( 'woocommerce_variation_set_stock', array( $this, 'update_child_variation_stock' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts(){
		if( WOO_MULTISTORE()->site->get_type() != 'child' ){
			return;
		}

		global $wp;

		if( ! isset( $wp->query_vars['order-received'] ) ){
			return;
		}

		$order_id = $wp->query_vars['order-received'];
		$order = wc_get_order($order_id);
		$items = $order->get_items();

		$data = array();
		foreach ($items as $item){
			$wc_product = $item->get_product();

			if( ! $wc_product ){
				continue;
			}

			if( ! wc_multistore_is_child_product( $wc_product ) ){
				continue;
			}

			$classname = wc_multistore_get_product_class_name( 'child', $wc_product->get_type() );

			if( ! $classname ){
				continue;
			}

			$wc_multistore_product_child = new $classname( $wc_product );


			$data[] = $wc_multistore_product_child->get_ajax_stock_data();
		}

		if( empty( $data ) ){
			return;
		}

		wp_register_script('wc-multistore-thank-you-child-js',WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-thank-you-child.js',	array( 'jquery' ),	false,true );
		wp_enqueue_script( 'wc-multistore-thank-you-child-js' );
		wp_localize_script(	'wc-multistore-thank-you-child-js','wc_multistore_child_stock_data',array(	'child_products' => $data ) );
	}

	public function update_child_product_stock( $wc_product_with_stock ){
		if( WOO_MULTISTORE()->site->get_type() != 'child' ){
			return;
		}

		if( ! wc_multistore_is_child_product( $wc_product_with_stock ) ){
			return;
		}

		if(  is_admin() && ! wc_multistore_is_saving_order() ){
			return;
		}

		if( is_checkout() ){
			return;
		}

		$classname = wc_multistore_get_product_class_name( 'child', $wc_product_with_stock->get_type() );

		if( ! $classname ){
			return;
		}

		$wc_multistore_product_child = new $classname( $wc_product_with_stock );

		$wc_multistore_product_child->sync_stock_to_master();
	}

	public function update_child_variation_stock( $wc_product_with_stock ){
		if( WOO_MULTISTORE()->site->get_type() != 'child' ){
			return;
		}

		if( ! wc_multistore_is_child_product( $wc_product_with_stock ) ){
			return;
		}

		if( is_admin() && ! wc_multistore_is_saving_order() ){
			return;
		}

		if( is_checkout() ){
			return;
		}

		$classname = wc_multistore_get_product_class_name( 'child', $wc_product_with_stock->get_type() );

		if( ! $classname ){
			return;
		}

		$wc_multistore_product_child = new $classname( $wc_product_with_stock );

		$wc_multistore_product_child->sync_stock_to_master();
	}


}