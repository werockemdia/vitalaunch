<?php

defined( 'ABSPATH' ) || exit;

if( ! function_exists( 'wc_multistore_order_is_imported' ) ){
	/**
	 * @param $order
	 * If an order has WOONET_PARENT_ORDER_ORIGIN_URL meta field, it's and imported order.
	 * @return string|null
	 */
	function wc_multistore_order_is_imported( $order ){
		$is_imported = $order->get_meta('WOONET_PARENT_ORDER_ORIGIN_URL', true, 'edit');

		if( ! empty( $is_imported ) ){
			return true;
		}

		return false;
	}
}

if( ! function_exists( 'wc_multistore_get_imported_order_id' ) ) {
	/**
	 * @param $order_id
	 * @param $site_id
	 * Returns Imported order id or null
	 *
	 * @return string|null
	 */
	function wc_multistore_get_imported_order_id( $order_id, $site_id ) {
		$meta_key = "WOONET_IMPORT_ORDER_MAP_OID_{$order_id}_SID_{$site_id}";
		$orders = wc_get_orders(
			array(
				'meta_key'      => $meta_key,
				'meta_compare'  => 'EXISTS'
			)
		);

		if( empty( $orders ) ){
			return false;
		}

		return $orders[0]->get_id();
	}
}

if( ! function_exists( 'wc_multistore_get_master_site_of_the_order' ) ) {
	/**
	 * @param $order
	 * Returns Master site id for the imported order
	 *
	 * @return string|null
	 */
	function wc_multistore_get_master_site_of_the_order( $order ) {
		return $order->get_meta( 'WOONET_PARENT_ORDER_ORIGIN_SID', true, 'edit' );
	}
}

if( ! function_exists( 'wc_multistore_get_original_order_id' ) ) {
	/**
	 * @param $order
	 * Returns Master order id for the imported order
	 *
	 * @return string|null
	 */
	function wc_multistore_get_original_order_id( $order ) {
		return $order->get_meta( 'WOONET_PARENT_ORDER_ORIGIN_ID', true, 'edit' );
	}
}

if( ! function_exists( 'wc_multistore_get_orders_total' ) ) {
	/**
	 */
	function wc_multistore_get_orders_total() {
		$statuses = array_keys( wc_get_order_statuses() );
		$status_counts = array_map( 'wc_orders_count', $statuses, array_fill(0 , count($statuses) , 'shop_order') );
		$orders_count  = array_sum( $status_counts );

		return $orders_count;
	}
}

if( ! function_exists( 'wc_multistore_get_orders_total_by_status' ) ) {
	/**
	 */
	function wc_multistore_get_orders_total_by_status() {
		$statuses = array_keys( wc_get_order_statuses() );
		$status_counts = array_map( 'wc_orders_count', $statuses, array_fill(0 , count($statuses) , 'shop_order') );
		$status_counts = array_combine( $statuses, $status_counts );

		return $status_counts;
	}
}

if( ! function_exists( 'wc_multistore_get_orders' ) ) {
	/**
	 */
	function wc_multistore_get_orders( $per_page, $paged, $post_status, $search ) {
		if( empty($post_status) ){
			$post_status = array_keys( wc_get_order_statuses() );
		}

		$queried_orders_args = array(
			'limit'     => $per_page,
			'page'      => $paged,
			'status'    => $post_status,
			'orderby'   => 'date',
			'order'     => 'DESC',
			'type'      => 'shop_order'
		);

		$queried_orders_args = apply_filters( 'wc_multistore_network_orders_args', $queried_orders_args );

		$queried_orders_total_args = array(
			'limit'     => -1,
			'status'    => $post_status,
			'orderby'   => 'date',
			'order'     => 'DESC',
			'type'      => 'shop_order',
			'return'    => 'ids'
		);

		if( ! empty( $search ) ){
			$order_ids = wc_order_search( $search );

			if ( ! empty( $order_ids ) ) {
				$queried_orders_args['post__in'] = array_merge( $order_ids, array( 0 ) );
				$queried_orders_total_args['post__in'] = array_merge( $order_ids, array( 0 ) );
			}
		}

		$queried_orders = wc_get_orders( $queried_orders_args );
		$total_orders_count = wc_multistore_get_orders_total();
		$total_queried_orders_count = count( wc_get_orders( $queried_orders_total_args ) );
		$total_orders_count_by_status = wc_multistore_get_orders_total_by_status();

		$orders_array = array();
		if ( ! empty( $queried_orders ) ) {
			foreach ( $queried_orders as $order ) {
				$order_data = $order->get_data();

				$order_data['__custom_order_id'] = apply_filters( 'woocommerce_order_number', $order->get_id(), $order );

				$items = array();

				foreach ( $order->get_items() as $item ) {
					$items[] = array_merge(
						$item->get_data(),
						array(
							'meta_data' => get_post_meta( $item->get_id() ),
						)
					);
				}

				$order_meta = array();

				$order_meta_data = $order->get_meta_data();
				foreach ( $order_meta_data as $meta_obj ) {
					$meta_array = $meta_obj->get_data();
					$order_meta[ $meta_array['key'] ] = isset( $meta_array['value'] ) ? $meta_array['value']  : '';
				}

				$order_data = array_merge(
					$order_data,
					array(
						'date_created'   => ! empty( $order_data['date_created'] ) ? $order_data['date_created']->date( 'Y/m/d H:i:s' ) : '',
						'date_modified'  => ! empty( $order_data['date_modified'] ) ? $order_data['date_modified']->date( 'Y/m/d H:i:s' ) : '',
						'meta_data'      => $order_meta,
						'line_items'     => $items,
						'shipping_lines' => array(), // not needed
						'shipping_method_title'  => $order->get_shipping_method(),
						'is_imported'    => wc_multistore_get_master_site_of_the_order($order)
					),
					array(
						'store_url'  => site_url(),
						'store_name' => get_bloginfo( 'name' ),
					)
				);

				$orders_array[] = apply_filters( 'WOO_MSTORE_ORDER/woocommerce_add_order_to_results', $order_data, $order );
			}
		}

		$data = array(
			'orders' => $orders_array,
			'total_orders_count' => $total_orders_count,
			'total_queried_orders_count' => $total_queried_orders_count,
			'total_orders_count_by_status' => $total_orders_count_by_status,
		);

		return $data;
	}
}

if( ! function_exists( 'wc_multistore_get_network_orders' ) ) {
	/**
	 */
	function wc_multistore_get_network_orders( $per_page, $paged, $post_status, $search, $site_filter ) {
		$network_orders = array();

		if( ! empty( $site_filter ) ){
			if( is_multisite() ){
				if ($site_filter == 'master'){
					switch_to_blog( get_site_option('wc_multistore_master_store') );
					$network_orders['master'] = wc_multistore_get_orders($per_page, $paged, $post_status, $search);
					restore_current_blog();
				}
			}else{
				if ($site_filter == 'master'){
					$network_orders['master'] = wc_multistore_get_orders($per_page, $paged, $post_status, $search);
				}
			}

			foreach ( WOO_MULTISTORE()->active_sites as $site ){
				if( is_multisite() ){
					if ( $site_filter == $site->get_id() ){
						switch_to_blog($site->get_id());
						$network_orders[$site->get_id()] = wc_multistore_get_orders($per_page, $paged, $post_status, $search);
						restore_current_blog();
					}
				}else{
					$WC_Multistore_Order_Api_Master = new WC_Multistore_Order_Api_Master();
					$response = $WC_Multistore_Order_Api_Master->get_child_orders( $paged, $per_page, $post_status, $search, $site->get_id() );
					$network_orders[$site->get_id()] = $response['data']['orders'];
				}
			}
		}else{
			if( is_multisite() ){
				switch_to_blog( get_site_option('wc_multistore_master_store') );
				$network_orders['master'] = wc_multistore_get_orders($per_page, $paged, $post_status, $search);
				restore_current_blog();
			}else{
				$network_orders['master'] = wc_multistore_get_orders($per_page, $paged, $post_status, $search);
			}

			foreach ( WOO_MULTISTORE()->active_sites as $site ){
				if( is_multisite() ){
					switch_to_blog($site->get_id());
					$network_orders[$site->get_id()] = wc_multistore_get_orders($per_page, $paged, $post_status, $search);
					restore_current_blog();
				}else{
					$WC_Multistore_Order_Api_Master = new WC_Multistore_Order_Api_Master();
					$response = $WC_Multistore_Order_Api_Master->get_child_orders( $paged, $per_page, $post_status, $search, $site->get_id() );
					$network_orders[$site->get_id()] = $response['data']['orders'];
				}
			}
		}


		$data = array(
			'total_orders_count' => 0,
			'highest_total_orders_count' => 0,
			'total_queried_orders_count' => 0,
			'highest_total_queried_orders_count' => 0,
			'total_orders_count_by_status' => array()
		);


		$sorted_site_orders = array();
		foreach ( $network_orders as $site => $orders_data ){
			if ( ! empty( $orders_data['orders'] ) ) {
				foreach ( $orders_data['orders'] as $site_order ) {
					$site_order['site_id'] = $site;

					$timestamp = apply_filters('WOO_MSTORE_network_orders_sort', strtotime( $site_order['date_created'] ), $site_order );

					if ( ! array_key_exists( $timestamp, $sorted_site_orders ) ) {
						$sorted_site_orders[ strval( $timestamp ) ] = $site_order;
					} else {
						$sorted_site_orders[ strval( $timestamp + ( mt_rand( 11111, 99999 ) / 100000 ) ) ] = $site_order;
					}
				}
			}

			$data['total_orders_count'] = $data['total_orders_count'] + $orders_data['total_orders_count'];
			$data['total_queried_orders_count'] = $data['total_queried_orders_count'] + $orders_data['total_queried_orders_count'];

			if( $orders_data['total_orders_count'] > $data['highest_total_orders_count'] ){
				$data['highest_total_orders_count'] = $orders_data['total_orders_count'];
			}

			if( $orders_data['total_queried_orders_count'] > $data['highest_total_queried_orders_count'] ){
				$data['highest_total_queried_orders_count'] = $orders_data['total_queried_orders_count'];
			}

			foreach ( $orders_data['total_orders_count_by_status'] as $status => $count ){
				if( ! isset( $data['total_orders_count_by_status'][$status] ) ){
					$data['total_orders_count_by_status'][$status] = $count;
				}else{
					$data['total_orders_count_by_status'][$status] = $count + $data['total_orders_count_by_status'][$status];
				}
			}

		}

		krsort( $sorted_site_orders, SORT_NUMERIC );

		$data['orders'] = $sorted_site_orders;

		return $data;
	}
}


if( ! function_exists( 'wc_multistore_update_orders_status' ) ) {
	/**
	 */
	function wc_multistore_update_orders_status( $order_ids, $site_id ) {
		if( empty($order_ids) ){
			return '';
		}

		$posts_list     = $order_ids;
		$failed         = array();
		$success        = array();
		$status_message = '';

		$wc_status = wc_get_order_statuses();

		// Initialize payment gateways in case order has hooked status transition actions.
		WC()->payment_gateways();

		do_action( 'WOO_MSTORE_ORDER/handle_bulk_actions-edit-shop_order_start', $posts_list );

		if( is_multisite() ){
			if( $site_id == 'master' ){
				switch_to_blog( get_site_option('wc_multistore_master_store') );
			}else{
				switch_to_blog( $site_id );
			}
		}

		foreach ( $posts_list as $post ) {
			$post['status'] = str_replace('mark_', 'wc-', $post['status']);

			if ( $post['status'] == 'delete' ) {
				wp_delete_post( $post['post'], true );
				$success[] = '#' . $post['post'];
			} elseif ( $post['status'] == 'untrash' ) {
				wp_untrash_post( $post['post'] );
				$success[] = '#' . $post['post'];
			} elseif ( $post['status'] == 'trash' ) {
				wp_trash_post( $post['post'] );
				$success[] = '#' . $post['post'];
			} elseif ( $post['status'] == 'refund' ) {
				$order = wc_get_order( (int) $post['post'] );

				if ( $order && $order->get_status() == 'refunded' || wc_multistore_get_master_site_of_the_order($order) ) {
					// Order already refunded.
					// @todo show error notice.
					return '';
				}
				if ( wc_multistore_refund_order( $order ) ) {
					$success[] =(int) $post['post'];
				} else {
					$failed[] = (int) $post['post'];
				};
			} elseif ( array_key_exists(  $post['status'], $wc_status ) ) {
				$order = wc_get_order( (int) $post['post'] );

				if ( $order && $order->update_status(  $post['status'], __( 'Order status changed by WooMultistore API', 'woonet' ), true ) ) {
					$success[] = '#' . $post['post'];
					do_action( 'woocommerce_order_edit_status', $post['post'], $post['status'] );
				} else {
					$failed[] = '#' . $post['post'];
				}
			} else {
				// Custom bulk actions.
				do_action( 'WOO_MSTORE_ORDER/handle_bulk_actions-edit-shop_order', $post['status'], $post['post'] );
			}
		}

		do_action( 'WOO_MSTORE_ORDER/handle_bulk_actions-edit-shop_order_end' );

		if ( ! empty( $success ) ) {
			$status_message .= 'Status/action for order(s) ' . implode( ',', $success ) . ' were succesfully updated on ' . site_url() . '.';
		}

		if ( ! empty( $failed ) ) {
			$status_message .= 'Status/action for order(s) ' . implode( ',', $failed ) . ' failed to update on ' . site_url() . '.';
		}

		if( is_multisite() ){
			restore_current_blog();
		}

		return array(
			'status'  => 'success',
			'message' => $status_message,
		);
	}
}


if( ! function_exists( 'wc_multistore_refund_order' ) ) {

	function wc_multistore_refund_order( $order ) {
		// Don't handle WC_ORDER_REFUND.
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return $order;
		}

		$refund_amount = 0;
		$line_items    = array();

		if ( $items = $order->get_items( array( 'line_item', 'fee', 'shipping' ) ) ) {
			foreach ( $items as $item_id => $item ) {
				$line_total = $order->get_line_total( $item, false, false );
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
			}
		}

		$is_refund_payment = false;
		$pay_method        = wc_get_payment_gateway_by_order( $order );
		$refund_amount     = $order->get_remaining_refund_amount();

		if ( $pay_method && method_exists( $pay_method, 'can_refund_order' ) ) {
			$is_refund_payment = $pay_method->can_refund_order( $order );
		}

		if ( $refund_amount == 0 ) {
			return $order;
		}

		$refund = wc_create_refund(
			array(
				'amount'         => $refund_amount,
				'reason'         => 'Refund initiated from WooMultistore Network Order Interface.',
				'order_id'       => $order->get_id(),
				'line_items'     => $line_items,
				'refund_payment' => $is_refund_payment,
				'restock_items'  => true,
			)
		);

		return $refund;
	}
}