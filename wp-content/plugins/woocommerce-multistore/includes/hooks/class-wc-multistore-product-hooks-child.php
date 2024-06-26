<?php
/**
 * Child Product Handler
 *
 * This handles child product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Hooks_Child
 */
class WC_Multistore_Product_Hooks_Child{

	public $settings;

	public $sites;

	public $licence;

	public function __construct(){
		$this->settings = WOO_MULTISTORE()->settings;
		$this->sites = WOO_MULTISTORE()->sites;
		$this->hooks();
	}

	public function hooks(){
		if ( ! WOO_MULTISTORE()->license->is_active() ) {
			return;
		}

		add_action( 'before_delete_post', array( $this, 'before_delete_post' ), 10 );
		add_action( 'woocommerce_product_duplicate_before_save', array( $this, 'remove_multistore_meta' ), 10 );
	}


	public function before_delete_post($id){
		global $post_type;


		if ( empty( $post_type ) || ! in_array( $post_type, array( 'product' ) ) ) {
			return;
		}

		$wc_product = wc_get_product($id);

		if( WOO_MULTISTORE()->site->get_type() != 'child' ){
			return;
		}

		if( ! $wc_product ){
			return;
		}

		if( ! wc_multistore_is_child_product($wc_product) ){
			return;
		}

		if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' && empty( $wc_product->get_sku('edit') ) ){
			return;
		}

		$classname = wc_multistore_get_product_class_name( 'child', $wc_product->get_type() );

		if( ! $classname ){
			return;
		}

		$wc_multistore_child_product = new $classname( $wc_product );

		$wc_multistore_child_product->delete_sync_data_from_master();
	}

	public function remove_multistore_meta( $duplicate ){
		$duplicate->delete_meta_data('_woonet_settings');
		$duplicate->delete_meta_data('_woonet_network_is_child_product_id');
		$duplicate->delete_meta_data('_woonet_network_is_child_product_sku');
		$duplicate->delete_meta_data('_woonet_network_is_child_product_url');
		return $duplicate;
	}
}