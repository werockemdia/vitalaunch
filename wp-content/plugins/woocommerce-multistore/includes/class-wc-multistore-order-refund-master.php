<?php
/**
 * Master Order Refund Handler
 *
 * This handles master order refund related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Order_Refund_Master
 */
class WC_Multistore_Order_Refund_Master {

	public $settings;

	public $data;

	public $wc_order;

	public $wc_order_refund;

	public function __construct( $wc_order, $wc_order_refund ) {
		$this->settings = WOO_MULTISTORE()->settings;
		$this->wc_order = $wc_order;
		$this->wc_order_refund = $wc_order_refund;
		$this->data = $this->get_data();
	}

	public function get_data() {
		$data = array(
			'site_id'         => WOO_MULTISTORE()->site->get_id(),
			'refund_id'         => $this->wc_order_refund->get_id(),
			'order_data'        => $this->wc_order->get_data(),
			'order_site_name'   => get_bloginfo( 'name' ),
		);

		$refund_amount          = isset( $_POST['refund_amount'] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refund_amount'] ) ), wc_get_price_decimals() ) : 0;
		$refund_reason          = isset( $_POST['refund_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['refund_reason'] ) ) : '';
		$line_item_qtys         = isset( $_POST['line_item_qtys'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_qtys'] ) ), true ) : array();
		$line_item_totals       = isset( $_POST['line_item_totals'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_totals'] ) ), true ) : array();
		$line_item_tax_totals   = isset( $_POST['line_item_tax_totals'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_tax_totals'] ) ), true ) : array();

		// Prepare line items which we are refunding.
		$line_items = array();

		// For full programaticaly refunded orders we don't have $_POST
		if( $this->wc_order_refund->get_amount() ==  $this->wc_order->get_total() && ! $_POST['refund_amount'] ){

			if ( $items = $this->wc_order->get_items( array( 'line_item', 'fee', 'shipping' ) ) ) {
				foreach ( $items as $item_id => $item ) {
					$line_total = $this->wc_order->get_line_total( $item, false, false );
					$qty        = $item->get_quantity();
					$tax_data   = wc_get_order_item_meta( $item_id, '_line_tax_data' );

					$refund_tax = array();

					// Check if it's shipping costs. If so, get shipping taxes.
					if ( $item instanceof \WC_Order_Item_Shipping ) {
						$tax_data = wc_get_order_item_meta( $item_id, 'taxes' );
					}

					// If taxdata is set, format as decimal.
					if ( ! empty( $tax_data['total'] ) ) {
						$refund_tax = array_filter( array_map( 'wc_format_decimal', $tax_data['total'] ) );
					}

					// Calculate line total, including tax.
					$line_total_inc_tax = wc_format_decimal( $line_total ) + ( is_numeric( reset( $refund_tax ) ) ? wc_format_decimal( reset( $refund_tax ) ) : 0 );

					// Add the total for this line tot the grand total.
					$refund_amount = wc_format_decimal( $refund_amount ) + round( $line_total_inc_tax, 2 );

					// Fill item per line.
					$line_items[ $item_id ] = array(
						'qty'          => $qty,
						'refund_total' => wc_format_decimal( $line_total ),
						'refund_tax'   => array_map( 'wc_round_tax_total', $refund_tax )
					);

					// Add taxes data for sync
					$taxes_data = array();
					if( $line_items[ $item_id ]['refund_tax'] ){
						foreach ( $line_items[ $item_id ]['refund_tax']  as $tax_id => $tax ){
							$taxes_data[] = WC_Tax::_get_tax_rate( $tax_id, ARRAY_A );
						}
					}
					$line_items[ $item_id ]['tax_data'] = $taxes_data;
				}
			}

		}else{
			$item_ids   = array_unique( array_merge( array_keys( $line_item_qtys ), array_keys( $line_item_totals ) ) );

			foreach ( $item_ids as $item_id ) {
				$line_items[ $item_id ] = array(
					'qty'          => 0,
					'refund_total' => 0,
					'refund_tax'   => array(),
				);
			}
			foreach ( $line_item_qtys as $item_id => $qty ) {
				$line_items[ $item_id ]['qty'] = max( $qty, 0 );
			}
			foreach ( $line_item_totals as $item_id => $total ) {
				$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
			}
			foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
				$line_items[ $item_id ]['refund_tax'] = array_filter( array_map( 'wc_format_decimal', $tax_totals ) );
				$taxes_data = array();
				if( $line_items[ $item_id ]['refund_tax'] ){
					foreach ( $line_items[ $item_id ]['refund_tax']  as $tax_id => $tax ){
						$taxes_data[] = WC_Tax::_get_tax_rate( $tax_id, ARRAY_A );
					}
				}
				$line_items[ $item_id ]['tax_data'] = $taxes_data;
			}
		}

		$data['order_data']['line_items'] = $line_items;
		$data['order_data']['refund_amount'] = $refund_amount;
		$data['order_data']['refund_reason'] = $refund_reason;

		return $data;
	}

	public function refund_child(){
		if( is_multisite() ){
			switch_to_blog( get_site_option('wc_multistore_master_store') );

			$wc_multistore_order_refund_child = new WC_Multistore_Order_Refund_Child();
			$wc_multistore_order_refund_child->refund($this->data);

			restore_current_blog();
		}else{
			$wc_multistore_order_api_master = new WC_Multistore_Order_Api_Child();
			$result = $wc_multistore_order_api_master->send_refund_order_data_to_master( $this->data, WOO_MULTISTORE()->site->get_id() );
		}


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