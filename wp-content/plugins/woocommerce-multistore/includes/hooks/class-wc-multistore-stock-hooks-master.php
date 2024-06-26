<?php
/**
 * Stock master update handler.
 *
 * This handles stock update for master products related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Stock_Hooks_Master
 */
class WC_Multistore_Stock_Hooks_Master {

	public function __construct() {
		$this->hooks();
	}

	public function hooks(){
		add_action( 'woocommerce_product_set_stock', array( $this, 'update_master_product_stock' ), 10 );
		add_action( 'woocommerce_variation_set_stock', array( $this, 'update_master_variation_stock' ), 10 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function update_master_product_stock( $wc_product_with_stock ){
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		if( wc_multistore_is_child_product( $wc_product_with_stock ) ){
			return;
		}

		if( ! wc_multistore_is_saving_order() ){
			return;
		}

		if( is_checkout() ){
			return;
		}

		$classname = wc_multistore_get_product_class_name('master', $wc_product_with_stock->get_type());

		if( ! $classname ){
			return;
		}

		$wc_multistore_master_product = new $classname($wc_product_with_stock);
		$wc_multistore_master_product->sync_stock();
	}

	public function update_master_variation_stock( $wc_product_with_stock ){
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		if( wc_multistore_is_child_product( $wc_product_with_stock ) ){
			return;
		}

		if( ! wc_multistore_is_saving_order() ){
			return;
		}

		if( is_checkout() ){
			return;
		}

		$classname = wc_multistore_get_product_class_name('master', $wc_product_with_stock->get_type());

		if( ! $classname ){
			return;
		}

		$wc_multistore_master_product = new $classname($wc_product_with_stock);
		$wc_multistore_master_product->sync_stock();
	}

	public function enqueue_scripts(){
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
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
		foreach ( $items as $item ){
			$wc_product = $item->get_product();
			$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );

			if( ! $classname ){
				continue;
			}

			$wc_multistore_product_master = new $classname( $wc_product );


			$data[] = $wc_multistore_product_master->get_ajax_stock_data();
		}

		if( empty( $data ) ){
			return;
		}

		wp_register_script('wc-multistore-thank-you-master-js',WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-thank-you-master.js',	array( 'jquery' ),	false,true );
		wp_enqueue_script( 'wc-multistore-thank-you-master-js' );
		wp_localize_script('wc-multistore-thank-you-master-js','wc_multistore_master_stock_data',array(	'master_products' => $data ) );
	}
}