<?php
/**
 * Abstract Master Order Handler
 *
 * This handles abstract master order related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Order_Master
 */
class WC_Multistore_Abstract_Order_Master {
	public $wc_order;

	public $data;

	public function __construct($wc_order) {
		$this->wc_order = $wc_order;
		$this->data = $this->get_data();
	}

	public function get_data() {
		$data = array();
		$data['id'] = $this->wc_order->get_id();
		$data['order_number'] = $this->wc_order->get_order_number();
		$data['date_created'] = ! empty( $this->wc_order->get_date_created('edit') ) ? $this->wc_order->get_date_created('edit')->date( 'Y-m-d H:i:s' ) : '';
		$data['date_modified'] = ! empty( $this->wc_order->get_date_modified('edit') ) ? $this->wc_order->get_date_modified('edit')->date( 'Y-m-d H:i:s' ) : '';
//		$data['completed_at'] = $this->wc_order->get_order_number();
		$data['status'] = $this->wc_order->get_status('edit');
		$data['currency'] = $this->wc_order->get_currency('edit');
		$data['prices_include_tax'] = $this->wc_order->get_prices_include_tax('edit');
		$data['discount_tax'] = $this->wc_order->get_discount_tax('edit');
		$data['total'] = $this->wc_order->get_total('edit');
		$data['subtotal'] = $this->wc_order->get_subtotal('edit');
		$data['total_line_items_quantity'] = $this->wc_order->get_item_count();
		$data['total_tax'] = $this->wc_order->get_total_tax();
		$data['total_shipping'] = $this->wc_order->get_shipping_total();
		$data['cart_tax'] = $this->wc_order->get_cart_tax();
		$data['shipping_tax'] = $this->wc_order->get_shipping_tax();
		$data['total_discount'] = $this->wc_order->get_total_discount();
		$data['cart_discount'] = wc_format_decimal( 0, 2 );
		$data['order_discount'] = wc_format_decimal( 0, 2 );
		$data['shipping_methods'] = $this->wc_order->get_shipping_method();
		$data['transaction_id'] = $this->wc_order->get_transaction_id();
		$data['payment_details'] = array(
			'method_id'    => $this->wc_order->get_payment_method(),
			'method_title' => $this->wc_order->get_payment_method_title(),
			'paid'         => ! empty( $this->wc_order->get_date_paid('edit') ) ? $this->wc_order->get_date_paid('edit')->date( 'Y-m-d H:i:s' ) : ''
		);
		$data['billing_address'] = array(
			'first_name' => $this->wc_order->get_billing_first_name(),
			'last_name'  => $this->wc_order->get_billing_last_name(),
			'company'    => $this->wc_order->get_billing_company(),
			'address_1'  => $this->wc_order->get_billing_address_1(),
			'address_2'  => $this->wc_order->get_billing_address_2(),
			'city'       => $this->wc_order->get_billing_city(),
			'state'      => $this->wc_order->get_billing_state(),
			'postcode'   => $this->wc_order->get_billing_postcode(),
			'country'    => $this->wc_order->get_billing_country(),
			'email'      => $this->wc_order->get_billing_email(),
			'phone'      => $this->wc_order->get_billing_phone(),
		);
		$data['shipping_address'] = array(
			'first_name' => $this->wc_order->get_shipping_first_name(),
			'last_name'  => $this->wc_order->get_shipping_last_name(),
			'company'    => $this->wc_order->get_shipping_company(),
			'address_1'  => $this->wc_order->get_shipping_address_1(),
			'address_2'  => $this->wc_order->get_shipping_address_2(),
			'city'       => $this->wc_order->get_shipping_city(),
			'state'      => $this->wc_order->get_shipping_state(),
			'postcode'   => $this->wc_order->get_shipping_postcode(),
			'country'    => $this->wc_order->get_shipping_country(),
		);
		$data['customer_note'] = $this->wc_order->get_customer_note();
		$data['customer_ip_address'] = $this->wc_order->get_customer_ip_address();
		$data['customer_user_agent'] = $this->wc_order->get_customer_user_agent();
		$data['customer_id'] = $this->wc_order->get_user_id();
		$data['created_via'] = $this->wc_order->get_created_via();
		$data['view_order_url'] = $this->wc_order->get_view_order_url();
		$data['meta_data'] = $this->get_meta_data();
		$data['line_items'] = $this->get_items();
		$data['shipping_items'] = $this->get_shipping_items();
		$data['tax_items'] = $this->get_tax_items();
		$data['tax_rates'] = $this->get_tax_rates();
		$data['fee_items'] = $this->get_fee_items();
		$data['coupon_items'] = $this->get_coupon_items();

		if( is_multisite() ){
			switch_to_blog( get_site_option('wc_multistore_master_store') );
			$data['ajax_url'] = admin_url( 'admin-ajax.php' );
			restore_current_blog();
		}else{
			$master_data = get_site_option('wc_multistore_master_connect');
			$data['ajax_url'] = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
		}

		$data['site_id']            = WOO_MULTISTORE()->site->get_id();
		$data['url']                = home_url();
		$data['order_site_name']    = get_bloginfo( 'name' );
		$data['text']               = home_url() . ' | #' . $this->wc_order->get_order_number();
		$data['origin_order_id']    = $this->wc_order->get_id();

		if ( $customer = get_userdata( $this->wc_order->get_customer_id() ) ) {
			$data['customer_data']      = $customer->to_array();
			$data['customer_meta_data'] = get_user_meta( $this->wc_order->get_customer_id() );
		}

		return apply_filters( 'wc_multistore_original_order_data', $data );
	}

	private function get_items() {
		$order_items = array();
		if ( $this->wc_order->get_items() ) {
			foreach ( $this->wc_order->get_items() as $item ) {

				//  Product needs to be cloned on master site if not a child product
				$json_product = array();
				if ( ! get_post_meta( $item->get_product_id(), '_woonet_network_is_child_product_id', true ) && ! get_post_meta( $item->get_product_id(), '_woonet_network_is_child_product_sku', true ) ) {
					$json_product = $this->product_to_json( $item->get_product_id() );
				}

				$item_data    = $item->get_data();
				$item_data['meta_data'] = json_decode(json_encode($item_data['meta_data']), true);

				$order_items[] = array_merge(
					$item_data,
					array(
						'product_id'             => $item->get_product_id(),
						'variation_id'           => $item->get_variation_id(),
						'master_product_blog_id' => get_post_meta( $item->get_product_id(), '_woonet_network_is_child_site_id', true ),
						'master_product_id'      => get_post_meta( $item->get_product_id(), '_woonet_network_is_child_product_id', true ),
						'master_product_sku'     => get_post_meta( $item->get_product_id(), '_sku', true ),
						'master_variation_id'    => get_post_meta( $item->get_variation_id(), '_woonet_network_is_child_product_id', true ),
						'master_variation_sku'   => get_post_meta( $item->get_variation_id(), '_sku', true ),
						'quantity'               => $item->get_quantity(),
						'unsynced_product'       => empty($json_product) ? '' : $json_product
					)
				);
			}
		}

		return $order_items;
	}

	private function get_shipping_items() {
		$order_shipping_items = array();

		if ( $this->wc_order->get_items( 'shipping' ) ) {
			foreach ( $this->wc_order->get_items( 'shipping' ) as $shipping_item ) {
				$shipping_item_data     = $shipping_item->get_data();
				$order_shipping_items[] = $shipping_item_data;
			}
		}

		return $order_shipping_items;
	}


	private function get_tax_items() {
		$order_tax_items = array();
		if ( $this->wc_order->get_items( 'tax' ) ) {
			foreach ( $this->wc_order->get_items( 'tax' ) as $tax_item ) {
				$order_tax_items[] = $tax_item->get_data();
			}
		}

		return $order_tax_items;
	}

	private function get_tax_rates() {
		$order_tax_rates = array();
		if ( $this->wc_order->get_items( 'tax' ) ) {
			foreach ( $this->wc_order->get_items( 'tax' ) as $tax_item ) {
				$order_tax_rates[] = WC_Tax::_get_tax_rate( $tax_item->get_rate_id(), ARRAY_A );
			}
		}

		return $order_tax_rates;
	}

	private function get_fee_items() {
		$order_fee_items = array();
		if ( $this->wc_order->get_items( 'fee' ) ) {
			foreach ( $this->wc_order->get_items( 'fee' ) as $fee_item ) {
				$order_fee_items[] = $fee_item->get_data();
			}
		}

		return $order_fee_items;
	}

	private function get_coupon_items() {
		$order_coupon_items = array();
		if ( $this->wc_order->get_items( 'coupon' ) ) {
			foreach ( $this->wc_order->get_items( 'coupon' ) as $coupon_item ) {
				$coupon_details = $coupon_item->get_data();
				if ( $coupon_details['meta_data'] ) {
					$coupon_details['meta_data'] = $coupon_details['meta_data'][0]->get_data();
				}

				if ( ! $coupon_item['meta_data'] ) {
					$coupon                      = new WC_COUPON( $coupon_item['code'] );
					$coupon_details['meta_data'] = $coupon->get_data();
				}

				$order_coupon_items[] = $coupon_details;
			}
		}

		return $order_coupon_items;
	}

	public function get_meta_data(){
		$meta_data = array();

		foreach ( $this->wc_order->get_meta_data() as $meta ) {
			$meta_data[ $meta->key ] = $meta->value;
		}
		return $meta_data;
	}

	public function product_to_json( $product_id ) {
		$wc_product = wc_get_product( $product_id );

		if( ! $wc_product ){
			return array();
		}

		$product = array(
			'_woomulti_version'        => defined( 'WOO_MSTORE_VERSION' ) ? WOO_MSTORE_VERSION : '',
			'_woomulti_sync_init_time' => time(),
		);

		$product['product'] = array(
			'ID'                    => $wc_product->get_id(),
			'post_content'          => $wc_product->get_description(),
			'post_title'            => $wc_product->get_name(),
			'post_name'             => $wc_product->get_slug(),
			'post_parent'           => $wc_product->get_parent_id(),
			'post_type'             => 'product',
			'product_type'          => $wc_product->get_type(),
			'sku'                   => $wc_product->get_sku(),
		);

		$product['product_type'] = $wc_product->get_type();

		$product['product_image'] = array(
			'image_src'  => wp_get_attachment_url( get_post_thumbnail_id( $product_id ) ),
			'ID' =>  get_post_thumbnail_id( $product_id ),
		);

		$product['meta'] = array();

		$_meta = get_post_meta( $wc_product->get_id() );

		foreach ( $_meta as $key => $value ) {
			if( $key == '_price' || $key == '_sale_price' || $key == '_regular_price' || $key == '_product_attributes' ){
				if ( is_array( $value ) ) {
					$product['meta'][ $key ] = maybe_unserialize( $value[0] );
				} else {
					$product['meta'][ $key ] = maybe_unserialize( $value );
				}
			}
		}

		if ( $product_attributes = $wc_product->get_attributes() ) {

			$product['product_attributes'] = array();

			foreach ( $product_attributes as $pa ) {
				$terms       = $pa->get_terms();
				$terms_array = array();

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$terms_array[] = (array) $term;
					}
				}

				$attr = array(
					'id'        => $pa->get_id(),
					'name'      => $pa->get_name(),
					'slug'      => $pa->get_name(), // name is slug
					'options'   => $pa->get_options(),
					'terms'     => $terms_array,
					'taxonomy'  => $pa->get_taxonomy_object(),
					'variation' => $pa->get_variation(),
				);

				$product['product_attributes'][] = $attr;
			}

		}

		if ( $wc_product->get_type() == 'variable' ) {

			$product['product_variations'] = array();

			$variations = $wc_product->get_available_variations();
			$variations = wp_list_pluck( $variations, 'variation_id' );

			foreach ( $variations as $variation ) {
				$wc_variation  = wc_get_product( $variation );
				$shipping_data = null;

				if ( $wc_variation->get_shipping_class() ) {
					$shipping_class = wp_get_post_terms( $variation, 'product_shipping_class' );

					if ( ! empty( $shipping_class[0]->term_id ) ) {
						$shipping_data = array(
							'id'          => $shipping_class[0]->term_id,
							'name'        => $shipping_class[0]->name,
							'slug'        => $shipping_class[0]->slug,
							'description' => $shipping_class[0]->name,
						);
					}
				}

				$thumb_id = get_post_thumbnail_id( $wc_variation->get_id() );

				if ( ! empty( $thumb_id ) ) {
					$variation_image = array(
						'image_src'  => wp_get_attachment_url( $thumb_id ),
						'ID'         => $thumb_id,
					);
				} else {
					$variation_image = false;
				}

				$variation_meta = array(
					'_regular_price'    => get_post_meta( $variation, '_regular_price', false ),
					'_price'            => get_post_meta( $variation, '_price', false ),
					'_sale_price'       => get_post_meta( $variation, '_sale_price', false ),
				);

				$product['product_variations'][] = array(
					'product'         => get_post( $variation ),
					'meta'            => $variation_meta,
					'shipping_class'  => isset( $shipping_data ) ? $shipping_data : array(),
					'stock_status'    => $wc_variation->get_stock_status(),
					'manage_stock'    => $wc_variation->get_manage_stock(),
					'stock_quantity'  => $wc_variation->get_stock_quantity(),
					'backorders'      => $wc_variation->get_backorders(),
					'attributes'      => $wc_variation->get_attributes(),
					'low_stock'       => $wc_variation->get_low_stock_amount(),
					'sku'             => ! empty( $wc_product->get_sku() ) && $wc_product->get_sku() == $wc_variation->get_sku() ? '' : $wc_variation->get_sku(),
					'variation_image' => $variation_image,
				);
			}
		}

		return  $product;
	}

	public function import(){
		if( is_multisite() ){
			$wc_multistore_order_api_child = new WC_Multistore_Order_Api_Child();
			$result = $wc_multistore_order_api_child->send_order_data_to_master($this->data);
		}else{
			$wc_multistore_order_api_child = new WC_Multistore_Order_Api_Child();
			$result = $wc_multistore_order_api_child->send_order_data_to_master($this->data);
		}
	}

	private function update_order_meta( $order_meta ) {
		// Set order meta data
		$order_meta_data = apply_filters( 'woonet_imported_order_metadata', $order_meta );
		if ( ! empty( $order_meta_data ) && is_array( $order_meta_data ) ) {
			foreach ( $order_meta_data as $meta_data ) {
				if( $meta_data['key'] == '_order_number' && WOO_MULTISTORE()->settings['sequential-order-numbers'] == 'yes' ){
					continue;
				}
				if( apply_filters('wc_multistore_import_order_meta_key', true, $meta_data['key']) ){
					$this->wc_order->update_meta_data( $meta_data['key'], $meta_data['value'] );
				}
			}
		}
	}

	public function update($data){
		global $WC_Multistore_Order_Hooks_Child;
		global $WC_Multistore_Order_Hooks_Master;

		remove_action('woocommerce_update_order', array($WC_Multistore_Order_Hooks_Child,'on_update_original_order'));
		remove_action('woocommerce_update_order', array( $WC_Multistore_Order_Hooks_Master, 'on_update_imported_order') );
		remove_action('woocommerce_create_order', array( $WC_Multistore_Order_Hooks_Master, 'on_update_imported_order') );

		$this->wc_order->set_status( $data['order_status'] );
		$this->update_order_meta( $data['order_meta'] );

		$this->wc_order->save();

		add_action('woocommerce_update_order', array($WC_Multistore_Order_Hooks_Child,'on_update_original_order'),10, 2 );
		add_action('woocommerce_update_order', array( $WC_Multistore_Order_Hooks_Master, 'on_update_imported_order'),10, 2 );
		add_action('woocommerce_create_order', array( $WC_Multistore_Order_Hooks_Master, 'on_update_imported_order'),10, 2 );
	}
}