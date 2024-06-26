<?php
/**
 * Master Order Hooks handler.
 *
 * This handles master order hooks related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Hooks_Master
 **/
class WC_Multistore_Order_Hooks_Master {

	public $settings;

	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){ return; }
		$this->settings = WOO_MULTISTORE()->settings;

		$this->hooks();
	}

	public function hooks(){
		// import order
		add_action( 'woocommerce_update_order', array( $this, 'on_update_imported_order'), 10, 2);
		add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'woocommerce_shop_order_search_order_origin_id' ) );
		add_filter( 'manage_shop_order_posts_columns', array( $this, 'set_custom_edit_post_columns' ), 99, 1 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'print_order_originating_column' ), 99, 2 );
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'hide_item_meta' ), 10, 2 );
		add_action( 'woocommerce_can_restore_order_stock',  array( $this, 'can_restore_stock'), 10, 2 );

		// sequential order number
		add_action( 'woocommerce_order_object_updated_props', array( $this, 'add_order_number'), 10, 2 );
		add_filter( 'woocommerce_order_number', array( $this,'get_order_number'), 10, 2 );
		add_filter( 'woocommerce_shortcode_order_tracking_order_id', array( $this,'woocommerce_shortcode_order_tracking_order_id'), 10, 1 );
		add_filter( 'woocommerce_shop_order_search_fields', array( $this,'add_sequential_shop_order_search_fields') );
	}

	public function on_update_imported_order( $order_id, $wc_order ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return $order_id; }

		$master_site_id = $wc_order->get_meta('WOONET_PARENT_ORDER_ORIGIN_SID');
		if( empty( $master_site_id ) ){
			return $order_id;
		}

		if( WOO_MULTISTORE()->sites[$master_site_id]->settings['child_inherit_changes_fields_control__import_order'] == 'no' ){
			return $order_id;
		}

		$wc_multistore_order_child = new WC_Multistore_Order_Child($wc_order);
		$wc_multistore_order_child->update_master();

		$this->disable_emails();

		return $order_id;
	}

	public function print_order_originating_column( $column, $post_id ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return; }
		global $the_order;
		switch ( $column ) {
			case 'woonet-order-originating':
				$text = $the_order->get_meta( 'WOONET_PARENT_ORDER_ORIGIN_TEXT', true, 'edit' );
				$parent_id = $the_order->get_meta( 'WOONET_PARENT_ORDER_ORIGIN_ID', true, 'edit' );
				$url = $the_order->get_meta( 'WOONET_PARENT_ORDER_ORIGIN_URL', true, 'edit' ) . '/wp-admin/post.php?post='.$parent_id.'&action=edit';
				echo "<a target='_blank' href='" . $url . "'>" . $text . "</a>";
				break;
		}
	}

	public function set_custom_edit_post_columns( $columns ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return $columns; }

		$columns['woonet-order-originating'] = __( 'Originating Site', 'Site where the order originated.' );
		return $columns;
	}

	public function woocommerce_shop_order_search_order_origin_id( $search_fields ) {
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return $search_fields; }

		$search_fields[] = 'WOONET_PARENT_ORDER_ORIGIN_ID';
		return $search_fields;
	}

	public function hide_item_meta( $formatted_meta, $data ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return $formatted_meta; }

		foreach( $formatted_meta as $key => $meta ){
			if( strpos( $meta->key, '_woonet_' ) !== false ){
				unset( $formatted_meta[$key] );
			}
		}
		return $formatted_meta;
	}

	function can_restore_stock( $return, $order ){
		if( WOO_MULTISTORE()->settings['enable-order-import'] != 'yes' ){ return $return; }
		$mapped_order = wc_get_orders(
			array(
				'meta_key' => 'WOONET_PARENT_ORDER_ORIGIN_SID',
				'meta_value' => $order->get_id(),
				'meta_comparison' => '='
			)
		);

		if( empty( $mapped_order ) ){
			return $return;
		}

		return false;
	}

	public function disable_emails() {
		add_filter('woocommerce_email_classes', function(){
			$order_statuses = wc_get_order_statuses();

			foreach ( $order_statuses as $key => $order_status ){
				$key = str_replace('wc-', 'woocommerce_order_status_', $key );
				remove_all_actions( $key );
				foreach ( $order_statuses as $os_key => $order_s ){
					$os_key = str_replace('wc-', '', $os_key );
					$os_key = $key.'_to_'.$os_key.'_notification';
					remove_all_actions( $os_key );
				}
			}

			return [];
		}, 99, 1);

		$order_statuses = wc_get_order_statuses();

		foreach ( $order_statuses as $key => $order_status ){
			$key = str_replace('wc-', 'woocommerce_order_status_', $key );
			remove_all_actions( $key );
			foreach ( $order_statuses as $os_key => $order_s ){
				$os_key = str_replace('wc-', '', $os_key );
				$os_key = $key.'_to_'.$os_key.'_notification';
				remove_all_actions( $os_key );
			}
		}

		remove_all_actions('woocommerce_order_partially_refunded');
		remove_all_actions('woocommerce_order_fully_refunded');
		remove_all_actions('woocommerce_order_status_refunded_notification');
		remove_all_actions('woocommerce_order_partially_refunded_notification');
		remove_action('woocommerce_order_status_refunded', array(	'WC_Emails', 'send_transactional_email' ) );
		remove_action('woocommerce_order_partially_refunded', array( 'WC_Emails',	'send_transactional_email' ) );
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
				'meta_key' => '_order_number',
				'meta_value' => $order_id,
				'meta_comparison' => '=',
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