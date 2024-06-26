<?php
/**
 * Child Order Hooks handler.
 *
 * This handles child order hooks related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\Coupons\DataStore as CouponsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\DataStore as OrdersStatsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;
/**
 * Class WC_Multistore_Order_Hooks_Child
 **/
class WC_Multistore_Order_Hooks_Child {

	public $settings;

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( WOO_MULTISTORE()->site->get_type() != 'child' ){ return; }
		$this->settings = WOO_MULTISTORE()->settings;

		$this->hooks();
	}

	public function hooks(){
		// import order
		add_action( 'woocommerce_update_order', array( $this, 'on_update_original_order' ), 10, 2 );
		add_action( 'woocommerce_order_refunded', array( $this, 'order_refunded'), 10, 2 );
		add_filter( 'woocommerce_refund_deleted', array( $this, 'refund_deleted' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// sequential order number
		add_action( 'woocommerce_order_object_updated_props', array( $this, 'add_order_number'), 10, 2 );
		add_filter( 'woocommerce_order_number', array( $this,'get_order_number'), 10, 2 );
		add_filter( 'woocommerce_shortcode_order_tracking_order_id', array( $this,'woocommerce_shortcode_order_tracking_order_id'), 10, 1 );
		add_filter( 'woocommerce_shop_order_search_fields', array( $this,'add_sequential_shop_order_search_fields') );
	}

	public function enqueue_scripts(){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return; }
		if( WOO_MULTISTORE()->site->get_settings()['child_inherit_changes_fields_control__import_order'] != 'yes' ){ return; }

		global $wp;

		if( ! isset( $wp->query_vars['order-received'] ) ){
			return;
		}

		$order_id = $wp->query_vars['order-received'];
		$order = wc_get_order($order_id);


		$wc_multistore_master_order = new WC_Multistore_Order_Master($order);
		$order_data = $wc_multistore_master_order->get_data();
		$order_data['action'] = 'wc_multistore_import_order';
		$order_data['key'] = WOO_MULTISTORE()->site->get_id();

		wp_register_script('wc-multistore-thank-you-order-master-js',WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-thank-you-order-master.js',	array( 'jquery' ),	false,true );
		wp_enqueue_script( 'wc-multistore-thank-you-order-master-js' );
		wp_localize_script(	'wc-multistore-thank-you-order-master-js','wc_multistore_order_data',array( 'data' => $order_data ) );
	}

	public function on_update_original_order( $order_id, $order ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return; }
		if( WOO_MULTISTORE()->site->get_settings()['child_inherit_changes_fields_control__import_order'] != 'yes' ){ return; }

		if( is_checkout() ){
			return;
		}

		if( wc_multistore_get_master_site_of_the_order( $order ) ){
			return;
		}

		if( 'refunded' == $order->get_status() ){
			return false;
		}

		$wc_multistore_master_order = new WC_Multistore_Order_Master($order);
		$wc_multistore_master_order->import();
	}

	public function order_refunded( $order_id, $refund_id ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return; }
		if( WOO_MULTISTORE()->site->get_settings()['child_inherit_changes_fields_control__import_order'] != 'yes' ){ return; }

		$wc_order = wc_get_order( $order_id );

		if ( empty( $wc_order )  ) {
			return;
		}

		// If it's something else, we don't want that.
		if( ! is_a( $wc_order, 'WC_Order') ) {
			return;
		}

		remove_action('woocommerce_order_refunded', array($this, 'order_refunded'));

		$wc_order_refund = wc_get_order($refund_id);
		$wc_multistore_order_refund_master = new WC_Multistore_Order_Refund_Master( $wc_order, $wc_order_refund );
		$wc_multistore_order_refund_master->refund_child();
	}

	public function refund_deleted( $refund_id, $order_id ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return; }
		if( WOO_MULTISTORE()->site->get_settings()['child_inherit_changes_fields_control__import_order'] != 'yes' ){ return; }

		$site_id = WOO_MULTISTORE()->site->get_id();
		$data = array(
			'refund_id' => $refund_id,
			'site_id' => $site_id
		);

		if( is_multisite() ){
			switch_to_blog(get_site_option('wc_multistore_master_store'));
			$wc_multistore_order_refund_child = new WC_Multistore_Order_Refund_Child();
			$wc_multistore_order_refund_child->delete($data);
			restore_current_blog();
		}else{
			$wc_multistore_order_api_child = new WC_Multistore_Order_Api_Child();
			$wc_multistore_order_api_child->send_delete_refund_order_data_to_master( $data, $site_id );
		}

	}

	public function add_order_number( $order, $updated_props ) {
		if( WOO_MULTISTORE()->settings['sequential-order-numbers'] != 'yes' ){ return; }

		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		if ( wp_is_post_revision( $order->get_id() ) ) {
			return;
		}

		$wc_multistore_sequential_order_number = new WC_Multistore_Sequential_Order_Number();
		$wc_multistore_sequential_order_number->add_order_number($order);
	}

	/**
	 * Return the order number
	 */
	public function get_order_number( $order_number, $order ) {
		if( WOO_MULTISTORE()->settings['sequential-order-numbers'] != 'yes' ){ return $order_number; }

		$_order_number = $order->get_meta('_order_number', true, 'edit');

		if ( $_order_number > 0 ) {
			return $_order_number;
		}

		remove_filter( 'woocommerce_order_number', array( $this,'get_order_number'), 10, 2 );
		$_order_nubmer = $order->get_order_number();
		add_filter( 'woocommerce_order_number', array( $this,'get_order_number'), 10, 2 );

		// if set the order number, return
		if ( ! empty( $_order_nubmer ) ) {
			return $_order_nubmer;
		}

		return $order_number;

	}

	public function woocommerce_shortcode_order_tracking_order_id( $order_id ) {
		if( WOO_MULTISTORE()->settings['sequential-order-numbers'] != 'yes' ){ return $order_id; }
		$orders = wc_get_orders(
			array(
				'meta_key'      => "_order_number",
				'meta_value'    => $order_id,
				'meta_compare'  => '='
			)
		);

		if( ! empty( $orders ) ){
			$order_number = $orders[0]->get_id();

			if ( ! empty( $order_number ) ) {
				return $order_number;
			}
		}

		return $order_id;
	}

	public function add_sequential_shop_order_search_fields( $search_fields ) {
		if( WOO_MULTISTORE()->settings['sequential-order-numbers'] != 'yes' ){ return $search_fields; }

		$search_fields[] = '_order_number';

		return $search_fields;
	}
}