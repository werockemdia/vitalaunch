<?php
/**
 * Sequential Order Number handler.
 *
 * This handles sequential order number functionality.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Multistore_Sequential_Order_Number
 */
class WC_Multistore_Sequential_Order_Number {



	/**
	 * retirve next order_number
	 */
	public function get_next_network_order_number() {
		if( is_multisite() ){
			$network_order_number = get_site_option( 'wc_multistore_sequential_order_number', 0 );
			if ( $network_order_number >= 1 ) {
				return $network_order_number;
			}
			$network_order_number = $this->get_highest_order_number_from_master();
		}else{
			if ( WOO_MULTISTORE()->site->get_type() == 'master' ) {
				$network_order_number = get_option( 'wc_multistore_sequential_order_number' );

				if ( $network_order_number >= 1 ) {
					return $network_order_number;
				}
				$network_order_number = $this->get_highest_order_number_from_master();

			} else {
				$wc_multistore_order_api_child = new WC_Multistore_Order_Api_Child();
				$result = $wc_multistore_order_api_child->get_sequential_order_number();
				$network_order_number = $result['data']['order_number'];

				if ( ! empty( $network_order_number ) && $network_order_number >= 1 ) {
					$network_order_number = (int) $network_order_number;
				}
			}
		}


		return $network_order_number;
	}


	/**
	 * set next order_number
	 */
	public function update_network_order_number( $order_number ) {
		update_site_option( 'wc_multistore_sequential_order_number', $order_number );
	}

	public function add_order_number( $order ) {
		// check if there's already an order_number
		$order_number = $order->get_meta('_order_number', true, 'edit');

		if ( $order_number > 0 ) {
			return $order_number;
		}

		$network_order_number = $this->get_next_network_order_number();

		if( is_multisite() ){
			$this->update_network_order_number( $network_order_number + 1 );
			$order->update_meta_data('_order_number', $network_order_number + 1 );
		}else{
			if( WOO_MULTISTORE()->site->get_type() == 'master' ){
				if( WOO_MULTISTORE()->settings['enable-order-import'] == 'yes' && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'wc_multistore_import_order' ){
					$order->update_meta_data('_order_number', $network_order_number + 1 );
					$this->update_network_order_number( $network_order_number + 1 );
				}else{
					$order->update_meta_data('_order_number', $network_order_number + 1 );
					$this->update_network_order_number( $network_order_number + 1 );
				}
			}else{
				$order->update_meta_data('_order_number', $network_order_number + 1 );
			}
		}

		return $network_order_number;
	}

	public function get_current_sequential_order_number() {
		$network_order_number = get_site_option( 'wc_multistore_sequential_order_number', 0 );
		if ( $network_order_number >= 1 ) {
			return $network_order_number;
		}

		return $this->get_highest_order_number_from_master();
	}

	public function get_highest_order_number_from_master() {
		$args = array(
			'numberposts' => 1,
			'orderby' => 'date',
			'order' => 'DESC',
		);

		if( is_multisite() ){
			switch_to_blog(get_site_option('wc_multistore_master_store'));
			$orders = wc_get_orders($args);

			if( ! empty( $orders ) ){
				$high = $orders[0]->get_id();
			}else{
				$high = 0;
			}

			restore_current_blog();
			return $high ;
		}

		$orders = wc_get_orders($args);

		if( ! empty( $orders ) ){
			$high = $orders[0]->get_id();
		}else{
			$high = 0;
		}

		return $high;
	}
}