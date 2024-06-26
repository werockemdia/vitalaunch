<?php
/**
 * Abstract Child Order Handler
 *
 * This handles abstract child order related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\Coupons\DataStore as CouponsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\DataStore as OrdersStatsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;

/**
 * Class WC_Multistore_Abstract_Order_Child
 */
class WC_Multistore_Abstract_Order_Child {

	public $wc_order;

	public $data;

	public function __construct($wc_order) {
		if ( is_a( $wc_order, 'WC_Order' ) ) {
			$this->wc_order = $wc_order;
		} else {
			$child_id = wc_multistore_get_imported_order_id( $wc_order['id'], $wc_order['site_id'] );
			if ( $child_id ) {
				$this->wc_order = wc_get_order($child_id);
			} else {
				$this->wc_order = new WC_Order();
			}

			$this->data = $wc_order;
		}
	}

	public function update(){
		$this->import_tax_class();
		$this->import_tax_rates();


		// Add order shipping
		$this->wc_order->set_address( $this->data['billing_address'], 'billing' );
		$this->wc_order->set_address( $this->data['shipping_address'], 'shipping' );
//		$this->wc_order->update_status( $this->data['status'], 'Imported order via API from child site.', TRUE );
		$this->wc_order->set_status( $this->data['status']);

		$this->wc_order->set_currency( $this->data['currency'] );
		$this->wc_order->set_prices_include_tax( $this->data['prices_include_tax'] );
		$this->wc_order->set_discount_total( $this->data['total_discount'] );
		$this->wc_order->set_discount_tax( $this->data['discount_tax'] );
		$this->wc_order->set_shipping_total( $this->data['total_shipping'] );
		$this->wc_order->set_shipping_tax(  $this->data['shipping_tax'] );
		$this->wc_order->set_cart_tax(  $this->data['cart_tax'] );
		$this->wc_order->set_total(  $this->data['total'] );

		// Add order customer details.
		if( apply_filters('wc_multistore_import_order_customer', true ) ) {
			if(empty($this->data['customer_data'])){
				$customer_id = 0;
			}else{
				$customer_id = $this->add_update_customer();
			}
			if( ! is_wp_error($customer_id) ){
				$this->wc_order->set_customer_id( $customer_id );
			}
		}

		// Add order payment methods.
		$this->wc_order->set_payment_method( $this->data['payment_details']['method_id'] );
		$this->wc_order->set_payment_method_title( $this->data['payment_details']['method_title'] );
		$this->wc_order->set_date_paid( $this->data['payment_details']['paid'] );
		$this->wc_order->set_transaction_id( $this->data['transaction_id'] );
		$this->wc_order->set_customer_ip_address( $this->data['customer_ip_address'] );
		$this->wc_order->set_customer_user_agent( $this->data['customer_user_agent'] );
		$this->wc_order->set_created_via( $this->data['created_via'] );
		$this->wc_order->set_customer_note( $this->data['customer_note'] );
	}

	public function save(){
		$this->disable_emails();

		global $WC_Multistore_Order_Hooks_Child;
		global $WC_Multistore_Order_Hooks_Master;

		remove_action('woocommerce_update_order', array( $WC_Multistore_Order_Hooks_Child, 'on_update_original_order' ) );
		remove_action('woocommerce_update_order', array( $WC_Multistore_Order_Hooks_Master, 'on_update_imported_order' ) );

		$limit = apply_filters( 'woocommerce_load_webhooks_limit', null );
		wc_load_webhooks( 'active', $limit );

		$this->remove_order_meta();
		$this->add_order_meta();
		$this->add_woonet_order_meta();

		do_action('wc_multistore_before_import_order_save', $this->wc_order, $this->data );

		if ( $this->wc_order->save() ) {
			$new_tax_items_rate_id = $this->add_tax_items();
			$this->add_shipping_items( $new_tax_items_rate_id );
			$this->add_coupon_items();
			$this->add_fee_items( $new_tax_items_rate_id );
			$this->add_order_items( $new_tax_items_rate_id );

			// Analytics data update
			OrdersStatsDataStore::sync_order( $this->wc_order->get_id() );
			CouponsDataStore::sync_order_coupons( $this->wc_order->get_id() );
			ReportsCache::invalidate();
		}
		wc_webhook_execute_queue();

//		add_action('woocommerce_update_order', array( $WC_Multistore_Order_Hooks_Child, 'on_update_original_order'), 10, 2 );
//		add_action('woocommerce_update_order', array( $WC_Multistore_Order_Hooks_Master, 'on_update_imported_order' ), 10, 2 );
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

	private function import_tax_class() {
		$tax_classes = WC_Tax::get_tax_classes();
		if ( ! in_array( 'Child tax rates', $tax_classes ) ) {
			WC_Tax::create_tax_class( 'Child tax' );
		}
	}


	private function import_tax_rates() {
		global $wpdb;


		if ( isset($this->data['tax_rates']) ) {
			foreach ( $this->data['tax_rates'] as $tax_rate ) {
				$tax_rate_id = $tax_rate['tax_rate_id'];
				unset( $tax_rate['tax_rate_id'] );

				$tax_rate['tax_rate_class'] = 'child-tax';
				$tax_rate_name              = $this->data['order_site_name'] . '_' . $tax_rate['tax_rate_name'] . '_' . $tax_rate_id;
				$tax_rate['tax_rate_name']  = $tax_rate_name;
				$tax_rate_exists            = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_name='{$tax_rate_name}'", OBJECT );

				if ( $tax_rate_exists ) {
					WC_Tax::_update_tax_rate( $tax_rate_exists->tax_rate_id, $tax_rate );
				} else {
					WC_Tax::_insert_tax_rate( $tax_rate );
				}
			}
		}
	}

	private function add_tax_items() {
		global $wpdb;

		$this->remove_tax_items();

		$new_tax_items_rate_id = array();
		if ( isset($this->data['tax_items']) ) {
			foreach ( $this->data['tax_items'] as $tax_item ) {
				$tax_name    = $this->data['order_site_name'] . ' ' . $tax_item['rate_code'] . '_' . $tax_item['rate_id'];
				$tax_item_id = wc_add_order_item( $this->wc_order->get_id(), array(
					'order_item_name' => $tax_name,
					'order_item_type' => 'tax'
				) );

				if ( $tax_item_id ) {
					$tax_item_name   = $this->data['order_site_name'] . '_' . $tax_item['label'] . '_' . $tax_item['rate_id'];
					$tax_rate_exists = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_name='{$tax_item_name}'", OBJECT );

					if ( $tax_rate_exists ) {
						wc_add_order_item_meta( $tax_item_id, 'rate_id', $tax_rate_exists->tax_rate_id );
						$new_tax_items_rate_id[ $tax_item['rate_id'] ] = $tax_rate_exists->tax_rate_id;
					} else {
						wc_add_order_item_meta( $tax_item_id, 'rate_id', $tax_item['rate_id'] );
						$new_tax_items_rate_id[ $tax_item['rate_id'] ] = $tax_item['rate_id'];
					}

					wc_add_order_item_meta( $tax_item_id, 'label', $this->data['order_site_name'] . ' ' . $tax_item['label'] . '_' . $tax_item['rate_id'] );
					wc_add_order_item_meta( $tax_item_id, 'compound', $tax_item['compound'] );
					wc_add_order_item_meta( $tax_item_id, 'tax_amount', $tax_item['tax_total'] );
					wc_add_order_item_meta( $tax_item_id, 'total_tax', $tax_item['tax_total'] );
					wc_add_order_item_meta( $tax_item_id, 'shipping_tax_amount', $tax_item['shipping_tax_total'] );
					wc_add_order_item_meta( $tax_item_id, 'rate_percent', $tax_item['rate_percent'] );
				}
			}
		}

		return $new_tax_items_rate_id;
	}


	private function remove_tax_items(){
		if ( $tax_items = $this->wc_order->get_items( 'tax' ) ) {
			foreach ( $tax_items as $tax_item ) {
				wc_delete_order_item( $tax_item->get_id() );
			}
		}
	}

	private function add_shipping_items( $new_tax_items_rate_id ) {

		$this->remove_shipping_items();

		if ( isset( $this->data['shipping_items'] ) ) {
			foreach ( $this->data['shipping_items'] as $shipping_item ) {
				if ( isset( $shipping_item['taxes'] ) ) {
					foreach ( $shipping_item['taxes'] as $shipping_tax_main_key => $shipping_item_tax ) {
						foreach ( $shipping_item_tax as $shipping_tax_key => $value ) {
							if ( array_key_exists( $shipping_tax_key, $new_tax_items_rate_id ) ) {
								unset( $shipping_item['taxes'][ $shipping_tax_main_key ][ $shipping_tax_key ] );
								$shipping_item['taxes'][ $shipping_tax_main_key ][ $new_tax_items_rate_id[ $shipping_tax_key ] ] = $value;
							}
						}
					}
				}

				$shipping_item_id = wc_add_order_item( $this->wc_order->get_id(), array(
					'order_item_name' => $shipping_item['name'],
					'order_item_type' => 'shipping'
				) );
				if ( $shipping_item_id ) {
					wc_add_order_item_meta( $shipping_item_id, 'method_id', $shipping_item['method_id'] );
					wc_add_order_item_meta( $shipping_item_id, 'instance_id', $shipping_item['instance_id'] );
					wc_add_order_item_meta( $shipping_item_id, 'cost', $shipping_item['total'] );
					wc_add_order_item_meta( $shipping_item_id, 'total_tax', $shipping_item['total_tax'] );
					if ( isset( $shipping_item['taxes'] ) ) {
						wc_add_order_item_meta( $shipping_item_id, 'taxes', $shipping_item['taxes'] );
					}
					wc_add_order_item_meta( $shipping_item_id, '_woonet_line_item_site_' . $this->data['site_id'] . '_id_' . $shipping_item['id'], 'yes' );
					if ( $shipping_item['meta_data'] ) {
						foreach ( $shipping_item['meta_data'] as $shipping_meta_data ) {
							if ( $shipping_meta_data['key'] == 'Items' ) {
								wc_add_order_item_meta( $shipping_item_id, 'Items', $shipping_meta_data['value'] );
							}
						}
					}
				}
			}

			//return array( $shipping_tax_main_key, $shipping_item_tax, $shipping_tax_key, $value );
		}

	}

	private function remove_shipping_items() {
		// Remove shipping items
		if ( $child_order_shipping_items = $this->wc_order->get_items( 'shipping' ) ) {
			foreach ( $child_order_shipping_items as $child_order_shipping_item ) {
				wc_delete_order_item( $child_order_shipping_item->get_id() );
			}
		}
	}

	private function add_coupon_items() {
		$this->remove_coupon_items();

		// Add coupon items
		if ( isset($this->data['coupon_items']) ) {
			foreach ( $this->data['coupon_items'] as $coupon_item ) {
				$coupon = $this->clone_coupon($coupon_item);
				$coupon_item_id = wc_add_order_item( $this->wc_order->get_id(), array(
					'order_item_name' => $coupon_item['code'],
					'order_item_type' => 'coupon'
				) );
				if ( $coupon_item_id ) {
					wc_add_order_item_meta( $coupon_item_id, 'discount_amount', $coupon_item['discount'] );
					wc_add_order_item_meta( $coupon_item_id, 'discount_amount_tax', $coupon_item['discount_tax'] );
					wc_add_order_item_meta( $coupon_item_id, 'coupon_data', $coupon->get_data() );
				}
			}
		}
	}

	private function remove_coupon_items() {
		if ( $child_order_coupon_items = $this->wc_order->get_items( 'coupon' ) ) {
			foreach ( $child_order_coupon_items as $child_order_coupon_item ) {
				wc_delete_order_item( $child_order_coupon_item->get_id() );
			}
		}
	}

	public function clone_coupon($coupon_item){
		if( $this->coupon_exists( $coupon_item['code'] ) ){
			return new WC_Coupon( $coupon_item['code'] );
		}

		$coupon = new WC_Coupon( $this->data['order_site_name'].' '.$coupon_item['code'] );
		$coupon->set_code( $this->data['order_site_name'].' '.$coupon_item['code'] );
		$coupon->set_amount($coupon_item['amount']);
		if( $coupon_item['meta_data'] ){
			$coupon->set_discount_type( $coupon_item['meta_data']['discount_type'] );
			$coupon->set_date_expires( strtotime( $coupon_item['meta_data']['date_expires']->date ) );
			$coupon->set_description( $coupon_item['meta_data']['description'] );
		}
		$coupon->save();

		return $coupon;
	}

	public function coupon_exists( $coupon_code ) {
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE post_type = 'shop_coupon' AND post_title = '%s'", $coupon_code );
		$coupon_codes = $wpdb->get_results($sql);
		if (count($coupon_codes)> 0) {
			return true;
		}
		else {
			return false;
		}
	}

	private function add_fee_items( $new_tax_items_rate_id ) {
		$this->remove_fee_items();

		// Add fee items
		if ( isset($this->data['fee_items']) ) {
			// Match the original tax rate id with the new tax rate id
			if ( $this->data['fee_items']['taxes'] ) {
				foreach ( $this->data['fee_items']['taxes'] as $shipping_tax_main_key => $shipping_item_tax ) {
					foreach ( $shipping_item_tax as $shipping_tax_key => $value ) {
						if ( array_key_exists( $shipping_tax_key, $new_tax_items_rate_id ) ) {
							unset( $this->data['fee_items']['taxes'][ $shipping_tax_main_key ][ $shipping_tax_key ] );
							$this->data['fee_items']['taxes'][ $shipping_tax_main_key ][ $new_tax_items_rate_id[ $shipping_tax_key ] ] = $value;
						}
					}
				}
			}

			foreach ( $this->data['fee_items'] as $order_fee_item ) {
				$fee_item_id = wc_add_order_item( $this->wc_order->get_id(), array(
					'order_item_name' => $order_fee_item['name'],
					'order_item_type' => 'fee'
				) );
				if ( $fee_item_id ) {
					wc_add_order_item_meta( $fee_item_id, '_fee_amount', $order_fee_item['amount'] );
					wc_add_order_item_meta( $fee_item_id, '_tax_class', $order_fee_item['tax_class'] );
					wc_add_order_item_meta( $fee_item_id, '_tax_status', $order_fee_item['tax_status'] );
					wc_add_order_item_meta( $fee_item_id, '_line_total', $order_fee_item['total'] );
					wc_add_order_item_meta( $fee_item_id, '_line_tax', $order_fee_item['total_tax'] );
					wc_add_order_item_meta( $fee_item_id, '_line_tax_data', $order_fee_item['taxes'] );
					wc_add_order_item_meta( $fee_item_id, '_woonet_line_item_site_' . $this->data['site_id'] . '_id_' . $order_fee_item['id'], 'yes' );
				}
			}
		}
	}

	private function remove_fee_items() {
		if ( $child_order_fees = $this->wc_order->get_items( 'fee' ) ) {
			foreach ( $child_order_fees as $child_order_fee ) {
				wc_delete_order_item( $child_order_fee->get_id() );
			}
		}
	}

	private function add_order_items( $new_tax_items_rate_id ) {
		global $wpdb;

		$this->remove_order_items();
		// Add order items
		foreach ( $this->data['line_items'] as $key => $item ) {

			if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' ){
				$linked_product_id = wc_get_product_id_by_sku( $item['master_product_sku'] );
				$linked_variation_id = wc_get_product_id_by_sku( $item['master_variation_sku'] );
			}else{
				$linked_product_id = $item['master_product_id'];
				$linked_variation_id = $item['master_variation_id'];
			}


			// Clone product if it's not linked
			if ( ! $linked_product_id ) {
				$linked_product_id = $this->clone_product( $item );
			}

			// Clone attributes to main site
			$this->clone_terms( $linked_product_id, $item['unsynced_product'] );

			// If variation is not synced get cloned variation id
			if ( ! $linked_variation_id ) {
				$linked_variation_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='_woonet_cloned_variation_id_{$item['variation_id']}_sid_{$this->data['site_id']}'" );
			}

			// Match the original tax rate id with the new tax rate id
			if ( isset( $item['taxes'] ) ) {
				foreach ( $item['taxes'] as $main_key => $item_tax ) {
					foreach ( $item_tax as $key => $value ) {
						if ( array_key_exists( $key, $new_tax_items_rate_id ) ) {
							unset( $item['taxes'][ $main_key ][ $key ] );
							$item['taxes'][ $main_key ][ $new_tax_items_rate_id[ $key ] ] = $value;
						}
					}
				}
			}

			// Add order line items data
			$item_id = wc_add_order_item( $this->wc_order->get_id(), array(
				'order_item_name' => $item['name'],
				'order_item_type' => 'line_item'
			) );
			if ( $item_id ) {
				wc_add_order_item_meta( $item_id, '_qty', $item['quantity'] );
				wc_add_order_item_meta( $item_id, '_tax_class', $item['tax_class'] );
				wc_add_order_item_meta( $item_id, '_product_id', $linked_product_id );
				wc_add_order_item_meta( $item_id, '_variation_id', $linked_variation_id );
				wc_add_order_item_meta( $item_id, '_line_subtotal', $item['subtotal'] );
				wc_add_order_item_meta( $item_id, '_line_subtotal_tax', $item['subtotal_tax'] );
				wc_add_order_item_meta( $item_id, '_line_total', $item['total'] );
				wc_add_order_item_meta( $item_id, '_line_tax', $item['total_tax'] );
				if(isset($item['taxes'])){
					wc_add_order_item_meta( $item_id, '_line_tax_data', $item['taxes'] );
				}
				wc_add_order_item_meta( $item_id, '_woonet_line_item_site_' . $this->data['site_id'] . '_id_' . $item['id'], 'yes' );
				wc_add_order_item_meta( $item_id, '_reduced_stock', $item['quantity'] );
				if ( ! empty( $item['meta_data'] ) ) {
					foreach ( $item['meta_data'] as $item_meta_data ) {
						wc_add_order_item_meta( $item_id, $item_meta_data['key'], $item_meta_data['value'] );
					}
				}
			}
		}
	}

	private function remove_order_items(){
		// Remove line items
		if ( $child_order_items = $this->wc_order->get_items() ) {
			foreach ( $child_order_items as $child_order_item ) {
				wc_delete_order_item( $child_order_item->get_id() );
			}
		}
	}

	public function add_update_customer() {
		global $wpdb;
		$customer_id        = $this->data['customer_data']['ID'];
		$mapp_id            = "WOONET_UMAP_SID{$this->data['site_id']}_CID_{$customer_id}";
		$mapped_customer    = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}usermeta WHERE meta_key='{$mapp_id}'", OBJECT );

		if ( ! empty( $mapped_customer->user_id ) ) {
			$user_id = $mapped_customer->user_id;
		} else {
			$user_id = wp_create_user( 'childsite_' . $this->data['customer_data']['user_nicename'], 'childsite_' . $this->data['customer_data']['user_nicename'], 'childsite_' . '+' . $this->data['customer_data']['user_email'] );

			if ( $user_id ) {
				update_user_meta( $user_id, $mapp_id, true );
			}
		}

		if ( ! empty( $user_id ) && ! empty( $this->data['customer_meta_data'] ) ) {
			foreach( $this->data['customer_meta_data'] as $key => $value ) {
				if( strpos( $key, 'capabilities' ) === false && strpos( $key, 'user_level' ) === false ){
					update_user_meta( $user_id, $key, $value[0] );
				}
			}
		}

		return $user_id;
	}

	private function remove_order_meta() {
		// Remove order meta
		$child_order_meta = $this->wc_order->get_data();

		if( ! isset( $child_order_meta['meta_data'] ) ){
			return;
		}

		foreach ( $child_order_meta['meta_data'] as $child_order_meta ) {
			if( $child_order_meta->key == '_order_number' && $this->is_enabled_sequential_order_number() ){
				continue;
			}

			if( apply_filters('wc_multistore_import_order_meta_key', true, $child_order_meta->key) ){
				$this->wc_order->delete_meta_data($child_order_meta->key);
			}
		}
	}

	/**
	 * @return bool
	 */
	public function is_enabled_sequential_order_number(){
		$sequential_order_number  = WOO_MULTISTORE()->settings['sequential-order-numbers'];

		if ( $sequential_order_number == 'yes') {
			return true;
		}

		return false;
	}

	private function add_order_meta() {
		// Add order meta
		if ( ! isset( $this->data['meta_data'] ) ) {
			return;
		}

		foreach ( $this->data['meta_data'] as $order_meta_key => $order_meta_value ) {
			if( $order_meta_key == '_order_number' && $this->is_enabled_sequential_order_number() ){
				$wc_multistore_sequential_order_number = new WC_Multistore_Sequential_Order_Number();
				$wc_multistore_sequential_order_number->add_order_number($this->wc_order);
				continue;
			}

			if( apply_filters('wc_multistore_import_order_meta_key', true, $order_meta_key) ){
				$this->wc_order->update_meta_data( $order_meta_key, $order_meta_value );
			}
		}
	}

	private function add_woonet_order_meta() {
		$this->wc_order->update_meta_data("WOONET_IMPORT_ORDER_MAP_OID_{$this->data['id']}_SID_" . $this->data['site_id'], true );
		$this->wc_order->update_meta_data("WOONET_PARENT_ORDER_ORIGIN_SID", $this->data['site_id'] );
		$this->wc_order->update_meta_data("WOONET_PARENT_ORDER_ORIGIN_PID", $this->data['id'] );
		$this->wc_order->update_meta_data("WOONET_PARENT_ORDER_ORIGIN_URL", $this->data['url'] );
		$this->wc_order->update_meta_data("WOONET_PARENT_ORDER_ORIGIN_TEXT", $this->data['text'] );
		$this->wc_order->update_meta_data("WOONET_PARENT_ORDER_ORIGIN_ID", $this->data['id'] );
		$this->wc_order->update_meta_data("_new_order_email_sent", true );
	}

	public function clone_product( $original_product ){
		global $wpdb;
		$original_item_id   = $original_product['unsynced_product']['product']['ID'];
		$secondary_site_id  = $this->data['site_id'];
		$product_type       = $original_product['unsynced_product']['product_type'];
		$update_clone_product_data = apply_filters('wc_multistore_update_cloned_product_data', true);

		if( $cloned_product_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='_is_clone_of_id_{$original_item_id}_sid_{$secondary_site_id}'" ) ){
			$cloned_product = wc_get_product( $cloned_product_id );
		}else{
			//create new product if not exists
			switch ( $product_type ) {
				case 'simple':
					$cloned_product = new WC_Product_Simple();
					break;

				case 'variable':
					$cloned_product = new WC_Product_Variable();
					break;

				case 'external':
					$cloned_product = new WC_Product_External();
					break;

				case 'booking':
					// If class 'WC_Product_Booking' exist then this class is defined into the Woocommerce Booking Plugin.
					if ( class_exists( 'WC_Product_Booking' ) ) {
						$cloned_product = new WC_Product_Booking();
					} else {
						$cloned_product = new WC_Product();
					}
					break;

				default :
					$cloned_product = new WC_Product();
					break;
			}
		}

		//set product data
		if( ! $cloned_product_id || ( $cloned_product_id && $update_clone_product_data ) ){
			$cloned_product->set_name( 'Cloned '.$original_product['unsynced_product']['product']['post_title'] );
			$cloned_product->set_status( 'private' );
			$cloned_product->set_description( $original_product['unsynced_product']['product']['post_content'] );
		}

		$cloned_product->set_price( $original_product['unsynced_product']['meta']['_price'] );
		$cloned_product->set_regular_price( $original_product['unsynced_product']['meta']['_regular_price'] );

		if( ! empty( $original_product['unsynced_product']['meta']['_sale_price'] ) ){
			$cloned_product->set_sale_price( $original_product['unsynced_product']['meta']['_sale_price'] );
		}

		if( ! empty( $original_product['unsynced_product']['product']['sku'] ) ){
			if( ! wc_get_product_id_by_sku( $original_product['unsynced_product']['product']['sku'] ) ){
				$cloned_product->set_sku( $original_product['unsynced_product']['product']['sku'] );
			}else{
				$logger = wc_get_logger();
				$message = 'Duplicate sku found:'. $original_product['unsynced_product']['product']['sku'] . ' for parent product id: ' . $original_product['unsynced_product']['product']['ID'];
				$logger->add('woocommerce-multistore', $message );
			}
		}

		$cloned_product->update_meta_data('_is_clone_of_id_' . $original_product['unsynced_product']['product']['ID'] .'_sid_' . $secondary_site_id , 'yes' );
		$cloned_product->update_meta_data('_woonet_is_clone', 'yes' );
		$cloned_product->save();

		if( $product_type == 'variable' ){
			if( $original_product['unsynced_product']['product_variations'] ){

				foreach ( $original_product['unsynced_product']['product_variations'] as $original_variation ){
					if( $cloned_variation_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='_woonet_cloned_variation_id_{$original_variation['product']->ID}_sid_{$secondary_site_id}'" ) ){
						$variation = wc_get_product( $cloned_variation_id );
					}else{
						$variation = new WC_Product_Variation();
					}

					$variation->set_parent_id( $cloned_product->get_id() );
					$variation->set_name( 'Clone '.$original_variation['product']->post_title );
					$variation->set_status( 'private' );

					if( ! empty( $original_variation['sku'] ) ){
						if( ! wc_get_product_id_by_sku( $original_variation['sku'] ) ){
							$variation->set_sku( $original_variation['sku'] );
						}else{
							$logger = wc_get_logger();
							$message = 'Duplicate sku found:'.$original_variation['sku'] . ' for variation parent id: ' . $original_variation['product']->ID;
							$logger->add('woocommerce-multistore', $message );
						}
					}

					$variation->set_price($original_variation['meta']['_price'][0]);
//                    $variation->set_regular_price($original_variation['meta']['_regular_price'][0]);
//                    $variation->set_sale_price($original_variation['meta']['_sale_price'][0]);
					$variation->set_attributes($original_variation['attributes']);
					$variation->save();

					foreach ( $original_variation['meta'] as $key => $original_variation_value ){
						if ( strpos( $key, 'attribute_pa_' ) === 0){
							update_post_meta( $variation->get_id(), $key, $original_variation_value[0] );
						}
					}

					update_post_meta( $variation->get_id(), '_woonet_cloned_variation_id_' . $original_variation['product']->ID .'_sid_' . $secondary_site_id, 'yes' );

					// Set variation image
					if( ! $cloned_variation_id || ( $cloned_variation_id && $update_clone_product_data ) ){
						if( ! empty( $original_variation['variation_image']['ID'] ) ){
							if( ! $cloned_variation_image_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='_woonet_cloned_attachment_id_{$original_variation['variation_image']['ID']}_sid_{$secondary_site_id}'" ) ){
								// create new image and set it as product thumbnail
								$cloned_variation_image_id = media_sideload_image( trim( $original_variation['variation_image']['image_src'] ), $variation->get_id(), null, 'id' );

								if ( ! empty( $cloned_variation_image_id ) && ! is_wp_error( $cloned_variation_image_id ) ) {
									set_post_thumbnail( $variation->get_id(), $cloned_variation_image_id );
									update_post_meta( $cloned_variation_image_id, '_woonet_cloned_attachment_id_' . $original_variation['variation_image']['ID'] .'_sid_' . $secondary_site_id, 'yes' );
								} else {
									error_log( $cloned_variation_image_id->get_error_message() . ' Supplied URL: ' . $original_variation['variation_image']['image_src'] );
								}
							}else{
								set_post_thumbnail( $variation->get_id(), $cloned_variation_image_id );
							}
						}
					}

				}
			}
		}

		// Set product image
		if( ! $cloned_product_id || ( $cloned_product_id && $update_clone_product_data ) ){
			if( ! empty( $original_product['unsynced_product']['product_image']['ID'] ) ){
				if( ! $cloned_image_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='_woonet_cloned_attachment_id_{$original_product['unsynced_product']['product_image']['ID']}_sid_{$secondary_site_id}'" ) ){
					// create new image and set it as product thumbnail
					$cloned_image_id = media_sideload_image( trim( $original_product['unsynced_product']['product_image']['image_src'] ), $cloned_product->get_id(), null, 'id' );

					if ( ! empty( $cloned_image_id ) && ! is_wp_error( $cloned_image_id ) ) {
						set_post_thumbnail( $cloned_product->get_id(), $cloned_image_id );
						update_post_meta( $cloned_image_id, '_woonet_cloned_attachment_id_' . $original_product['unsynced_product']['product_image']['ID'] .'_sid_' . $secondary_site_id, 'yes' );
					} else {
						error_log( $cloned_image_id->get_error_message() . ' Supplied URL: ' . $original_product['unsynced_product']['product_image']['image_src'] );
					}
				}else{
					set_post_thumbnail( $cloned_product->get_id(), $cloned_image_id );
				}
			}
		}

		return $cloned_product->get_id();
	}

	public function clone_terms( $child_product_id, $product ){
		if( empty( $product['product_attributes'] ) || ! $attributes = $product['product_attributes'] ){
			return;
		}

		$product_attributes_array = array();
		$_product_attributes = array();

		foreach ( $attributes as $attr ) {
			// process taxonomy
			if ( ! empty( $attr['taxonomy'] ) ) {
				$id = wc_attribute_taxonomy_id_by_name( $attr['name'] ); //in effect its similar to by_slug

				if ( ! $id ) {
					$id = wc_create_attribute(
						array(
							'name'  => $attr['taxonomy']->attribute_label,
							'label' => $attr['taxonomy']->attribute_label,
							'slug'  => $attr['name'],
							'type'  => 'select',
						)
					);
				}

				/**
				 * If taxonomy slug on the child is different from the master,
				 * call to term_exists will fail and terms will not be added correctly.
				 * So, we get the taxonomy name on the child by the taxonomy ID.
				 */
				$_tax_name = $attr['name'];

				// If taxonomy doesn't exists we create it
				if ( ! taxonomy_exists( $_tax_name ) ) {
					register_taxonomy(
						$_tax_name,
						'product_variation',
						array(
							'hierarchical' => false,
							'label'        => ucfirst( $attr['taxonomy']->attribute_label ),
							'query_var'    => true,
							'rewrite'      => array( 'slug' => sanitize_title( $attr['name'] ) ), // The base slug
						)
					);
				}

				if ( ! is_wp_error( $id ) ) {
					$post_terms_to_add = array();

					foreach ( $attr['terms'] as $term ) {
						if ( ! term_exists( $term['name'], $_tax_name ) ) {
							$term_id = wp_insert_term( $term['name'], $_tax_name, array(
								//'slug' => $term['slug'],
							));
						}

						if ( ! array_key_exists( $term['slug'], $product_attributes_array ) ) {
							// fetch the term again to get its slug
							$_trm = get_term_by( 'name', $term['name'], $_tax_name );

							if ( $_trm->slug ) {
								$product_attributes_array[ $term['slug'] ] = $_trm->slug;
							}
						}

						$post_terms_to_add[] = $term['name'];
					}

					$set_terms = wp_set_object_terms( $child_product_id, $post_terms_to_add, $_tax_name, false );

					$_product_attributes[$_tax_name] = array(
						'name' => $_tax_name,
						'value' => '',
						'is_visible' => '0',
						'is_taxonomy' => '1',
						'is_variation' => wc_string_to_bool( $attr['variation'] )
					);

				}
			}
		}

		update_post_meta( $child_product_id, '_product_attributes', $_product_attributes );

	}


	private function get_order_meta() {
		$order_meta = array();

		if ( ! empty( $this->wc_order->get_meta_data() ) ) {
			foreach ( $this->wc_order->get_meta_data() as $WC_Meta_Data ) {
				$meta_data = $WC_Meta_Data->get_data();
				if ( strpos( $meta_data['key'], 'WOONET' ) === false ) {
					$order_meta[] = $meta_data;
				}
			}
		}

		return $order_meta;
	}


	public function get_data(){
		$order_meta          = $this->get_order_meta();

		return array(
			'original_site_id'  => $this->wc_order->get_meta('WOONET_PARENT_ORDER_ORIGIN_SID'),
			'original_order_id' => $this->wc_order->get_meta('WOONET_PARENT_ORDER_ORIGIN_ID'),
			'order_status'      => $this->wc_order->get_status('edit'),
			'order_meta'        => $order_meta
		);
	}

	public function update_master(){
		$data = $this->get_data();

		if( ! isset( WOO_MULTISTORE()->sites[$data['original_site_id']] ) || ! WOO_MULTISTORE()->sites[$data['original_site_id']] ){
			return false;
		}

		$url = WOO_MULTISTORE()->sites[$data['original_site_id']]->get_url();
		$ajax_url = $url.'/wp-admin/admin-ajax.php';

		$body = $data;
		$body['action'] = 'wc_multistore_update_master_order';
		$body['key'] = WOO_MULTISTORE()->sites[$data['original_site_id']]->get_id();

		$args = array(
			'method' => 'POST',
			'body' => $body,
			'timeout' => 60
		);

		return wp_remote_request( $ajax_url, $args );
	}
}