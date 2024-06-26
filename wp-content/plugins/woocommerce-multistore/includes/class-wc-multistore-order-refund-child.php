<?php
/**
 * Child Order Refund Handler
 *
 * This handles child order refund related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Refund_Child
 */
class WC_Multistore_Order_Refund_Child {

	public function delete($data){
		$orders = wc_get_orders(
			array(
				'meta_key'      => "_woonet_refund_id_{$data['refund_id']}_sid_{$data['site_id']}",
				'meta_compare'  => 'EXISTS'
			)
		);

		if( ! empty( $orders ) ){
			$child_refund_id = $orders[0]->get_id();
			$child_refund    = wc_get_order( $child_refund_id );
			if( $child_refund ){
				$child_refund->delete(true );
			}
		}
	}

	public function refund($data){
		$child_order_id = wc_multistore_get_imported_order_id( $data['order_data']['id'], $data['site_id'] );
		global $wpdb;
		$items                  = array();
		foreach ( $data['order_data']['line_items'] as $key => $line_item ){
			$cloned_line_item_id = $wpdb->get_var( "SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key='_woonet_line_item_site_{$data['site_id']}_id_{$key}'" );
			$tax_rate_name = '';
			$tax_rate_id   = 0;
			$refund_tax_data = array();

			if( $line_item['refund_tax'] ){
				foreach( $line_item['refund_tax'] as $refund_tax_key => $refund_item_tax ){
					if( $line_item['tax_data'] ){
						foreach ( $line_item['tax_data'] as $line_item_tax_data){
							if( $line_item_tax_data['tax_rate_id'] == $refund_tax_key ){

								$tax_rate_name              = $line_item_tax_data['tax_rate_name'];
								$tax_rate_name              = $data['order_site_name'] . '_' . $tax_rate_name . '_' . $line_item_tax_data['tax_rate_id'];
								$tax_rate_exists            = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_name='{$tax_rate_name}'", OBJECT );

								if( $tax_rate_exists ){
									$tax_rate_id = $tax_rate_exists->tax_rate_id;
									$refund_tax_data[ $tax_rate_id ] = $refund_item_tax;
								}

							}
						}
					}
				}
			}

			$line_item[ 'refund_tax' ]      = $refund_tax_data;
			$items[ $cloned_line_item_id ]  = $line_item;

		}

		$this->disable_emails();

		$refund = wc_create_refund( array(
			'amount'         => $data['order_data']['refund_amount'],
			'reason'         => $data['order_data']['refund_reason'],
			'order_id'       => $child_order_id,
			'line_items'     => $items,
			'refund_payment' => false
		));


		if( ! is_wp_error( $refund ) ){
			$refund->update_meta_data('_woonet_refund_id_' . $data['refund_id'] .'_sid_' . $data['site_id'], 'yes' );
		}

		global $WC_Multistore_Order_Hooks_Child;
		remove_action('woocommerce_order_refunded', array( $WC_Multistore_Order_Hooks_Child, 'order_refunded' ) );
		remove_all_actions('woocommerce_order_refunded' );
		$refund->save();
		add_action('woocommerce_order_refunded', array( $WC_Multistore_Order_Hooks_Child, 'order_refunded' ), 10, 2 );
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
}