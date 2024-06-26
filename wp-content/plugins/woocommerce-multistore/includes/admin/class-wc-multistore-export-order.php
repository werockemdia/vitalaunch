<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class WC Multistore Export Order
 */
class WC_Multistore_Export_Order{

	/**
	 * @var array
	 */
	private $system_messages = array();

	/**
	 * @var string[]
	 */
	private $network_fields = array(
		'site_id'  => 'Site ID',
//		'blogname' => 'Site Title (Blogname)',
	);

	/**
	 * @var string[]
	 */
	private $order_fields = array(
		'id'                   => '',
		'order_number'         => '',
		'items'                => '',
		'parent_id'            => '',
		'status'               => '',
		'currency'             => '',
		'version'              => '',
		'prices_include_tax'   => '',
		'date_created'         => '',
		'date_modified'        => '',
		'discount_total'       => '',
		'discount_tax'         => '',
		'shipping_total'       => '',
		'shipping_tax'         => '',
		'cart_tax'             => '',
		'total'                => '',
		'total_tax'            => '',
		'customer_id'          => '',
		'order_key'            => '',
		'billing_first_name'   => 'Billing First Name',
		'billing_last_name'    => 'Billing Last Name',
		'billing_company'      => 'Billing Company',
		'billing_address_1'    => 'Billing Address 1',
		'billing_address_2'    => 'Billing Address 2',
		'billing_city'         => 'Billing City',
		'billing_postcode'     => 'Billing Postal/Zip Code',
		'billing_state'        => 'Billing State',
		'billing_country'      => 'Billing Country',
		'billing_phone'        => 'Phone Number',
		'billing_email'        => 'Email Address',
		'shipping_first_name'  => 'Shipping First Name',
		'shipping_last_name'   => 'Shipping Last Name',
		'shipping_company'     => 'Shipping Company',
		'shipping_address_1'   => 'Shipping Address 1',
		'shipping_address_2'   => 'Shipping Address 2',
		'shipping_city'        => 'Shipping City',
		'shipping_postcode'    => 'Shipping Postal/Zip Code',
		'shipping_state'       => 'Shipping State',
		'shipping_country'     => 'Shipping Country',
		'payment_method'       => '',
		'payment_method_title' => '',
		'transaction_id'       => '',
		'customer_ip_address'  => '',
		'customer_user_agent'  => '',
		'created_via'          => '',
		'customer_note'        => '',
		'date_completed'       => '',
		'date_paid'            => '',
		'cart_hash'            => '',
		'meta_data'            => '',
	);

	/**
	 * @var string[]
	 */
	private $order_item_fields = array(
		'product_id'   => '',
		'variation_id' => '',
		'quantity'     => '',
		'tax_class'    => '',
		'subtotal'     => '',
		'subtotal_tax' => '',
		'total'        => '',
		'total_tax'    => '',
		'taxes'        => '',
		'meta'         => '',
	);

	/**
	 * @var string[]
	 */
	private $order_item_product_fields = array(
		'name'               => '',
		'slug'               => '',
		'date_created'       => '',
		'date_modified'      => '',
		'status'             => '',
		'featured'           => '',
		'catalog_visibility' => '',
		'description'        => '',
		'short_description'  => '',
		'sku'                => '',
		'price'              => '',
		'regular_price'      => '',
		'sale_price'         => '',
		'date_on_sale_from'  => '',
		'date_on_sale_to'    => '',
		'total_sales'        => '',
		'tax_status'         => '',
		'tax_class'          => '',
		'manage_stock'       => '',
		'stock_quantity'     => '',
		'stock_status'       => '',
		'backorders'         => '',
		'low_stock_amount'   => '',
		'sold_individually'  => '',
		'weight'             => '',
		'length'             => '',
		'width'              => '',
		'height'             => '',
		'upsell_ids'         => '',
		'cross_sell_ids'     => '',
		'parent_id'          => '',
		'reviews_allowed'    => '',
		'purchase_note'      => '',
		'attributes'         => '',
		'default_attributes' => '',
		'menu_order'         => '',
		'virtual'            => '',
		'downloadable'       => '',
		'category_ids'       => '',
		'tag_ids'            => '',
		'shipping_class_id'  => '',
		'downloads'          => '',
		'image_id'           => '',
		'gallery_image_ids'  => '',
		'download_limit'     => '',
		'download_expiry'    => '',
		'rating_counts'      => '',
		'average_rating'     => '',
		'review_count'       => '',
		'meta'               => '',
	);

	/**
	 * @var string[]
	 */
	private $order_item_shipping_fields = array(
		'method_title' => '',
		'method_id'    => '',
		'instance_id'  => '',
		'total'        => '',
		'total_tax'    => '',
		'taxes'        => '',
	);

	/**
	 * @var string[]
	 */
	private $order_item_tax_fields = array(
		'rate_code'          => '',
		'rate_id'            => '',
		'label'              => '',
		'compound'           => '',
		'tax_total'          => '',
		'shipping_tax_total' => '',
	);

	/**
	 * @var string[]
	 */
	private $order_item_coupon_fields = array(
		'code'         => '',
		'discount'     => '',
		'discount_tax' => '',
	);

	/**
	 * @var string[]
	 */
	private $order_item_fee_fields = array(
		'tax_class'  => '',
		'tax_status' => '',
		'amount'     => '',
		'total'      => '',
		'total_tax'  => '',
		'taxes'      => '',
	);

	/**
	 * @var array
	 */
	public $errors_log = array();

	/**
	 * @var string
	 */
	private $export_type = '';

	/**
	 * @var string
	 */
	private $export_time_after = '';

	/**
	 * @var string
	 */
	private $export_time_before = '';

	/**
	 * @var array
	 */
	private $site_filter = array();

	/**
	 * @var array
	 */
	private $order_status = array();

	/**
	 * @var string
	 */
	private $row_format = '';

	/**
	 * @var array
	 */
	private $export_fields = array();

	/**
	 *
	 */
	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! is_multisite() && WOO_MULTISTORE()->site->get_type() == 'child' ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }

		$this->hooks();
	}

	/**
	 *
	 */
	public function hooks() {
		add_action( 'network_admin_menu', array( $this, 'add_network_admin_menu' ), 13 );
		if( ! is_multisite() ){
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 13 );
		}

		add_action( 'init', array($this, 'export'), 20 );
	}


	public function add_network_admin_menu() {
		if ( ! current_user_can( 'manage_sites' ) ) {
			return;
		}

		$menus_hook = add_submenu_page('woonet-woocommerce', __( 'Order Export', 'woonet' ), __( 'Order Export', 'woonet' ),'manage_woocommerce','woonet-woocommerce-orders-export', array( $this, 'orders_export_page' ),5 );

		add_action( 'load-' . $menus_hook, array( $this, 'admin_notices' ) );
		add_action( 'admin_print_styles-' . $menus_hook, array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $menus_hook, array( $this, 'admin_print_scripts' ) );
	}

	/**
	 *
	 */
	public function add_admin_menu() {
		$menus_hook = add_submenu_page('woonet-woocommerce', __( 'Order Export', 'woonet' ), __( 'Order Export', 'woonet' ),'manage_woocommerce','woonet-woocommerce-orders-export', array( $this, 'orders_export_page' ),5 );

		add_action( 'load-' . $menus_hook, array( $this, 'admin_notices' ) );
		add_action( 'admin_print_styles-' . $menus_hook, array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $menus_hook, array( $this, 'admin_print_scripts' ) );
	}

	/**
	 *
	 */
	public function export() {
		if ( empty( $_POST['evcoe_form_submit'] ) || 'export' != $_POST['evcoe_form_submit'] ) {
			return;
		}

		$this->validate_settings();

		ini_set( 'max_execution_time', 500 );

		$exporter_class_name = 'WC_Multistore_Export_'.ucfirst( $this->export_type );
		$filename = 'network_orders_export';
		$filename .= empty( $this->export_time_after ) ? '' : '_from_' . date( 'Ymd', $this->export_time_after );
		$filename .= empty( $this->export_time_before ) ? '' : '_to_' . date( 'Ymd', $this->export_time_before );

		$exporter = new $exporter_class_name( $filename );
		$exporter->initialize();

		// Add header
		$header = apply_filters('wc_multistore_export_orders_header', $this->get_header());
		$exporter->addRow($header);

		if( is_multisite() ){
			$orders = $this->get_orders();
			// Add rows
			foreach( $orders as $site_id => $site_orders ){
				foreach ( $site_orders as $order ){
					switch_to_blog($site_id);
					if( $this->row_format == 'row_per_product'){
						$order_items = $order->get_items();
						foreach ( $order_items as $order_item ){
							$row = $this->get_product_row($order, $order_item);
							$exporter->addRow($row);
						}
					}else{
						$row = $this->get_order_row($order);
						$exporter->addRow($row);
					}
					restore_current_blog();
				}
			}
		}else{
			if ( in_array( 'master', $this->site_filter ) ) {
				$master_orders = $this->get_orders();
				foreach ( $master_orders as $order ){
					if( ! empty( $master_orders ) ){
						if( $this->row_format == 'row_per_product'){
							$order_items = $order->get_items();
							foreach ( $order_items as $order_item ){
								$row = $this->get_product_row($order, $order_item);
								$exporter->addRow($row);
							}
						}else{
							$row = $this->get_order_row( $order );
							$exporter->addRow($row);
						}
					}
				}
			}

			// Add child sites rows
			if( ! empty( $this->site_filter ) ){
				foreach ( $this->site_filter as $site ){
					if( $site == 'master' ){
						continue;
					}
					$wc_multistore_order_api_master = new WC_Multistore_Order_Api_Master();
					$result = $wc_multistore_order_api_master->send_get_orders_request(
						array(
							'export_format'      => $this->export_type,
							'export_time_after'  => $_POST['export_time_after'],
							'export_time_before' => $_POST['export_time_before'],
							'site_filter'        => $this->site_filter,
							'order_status'       => $this->order_status,
							'row_format'         => $this->row_format,
							'export_fields'      => $this->export_fields,
						),
						$site
					);

					$child_site_rows = $result['data']['rows'];
					if( !empty($child_site_rows) ){
						foreach ($child_site_rows as $child_site_row){
							$exporter->addRow($child_site_row);
						}
					}
				}
			}
		}


		// Output
		$exporter->finalize();

		exit;
	}

	/**
	 *
	 */
	public function admin_print_styles() {
		wp_enqueue_style( 'jquery-ui', WOO_MSTORE_URL . '/assets/css/jquery-ui.css' );
		wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css' );
		wp_enqueue_style( 'wc-multistore-export-css', WOO_MSTORE_URL . '/assets/css/wc-multistore-export.css' );
	}

	/**
	 *
	 */
	public function admin_print_scripts() {
		wp_enqueue_script('jquery-ui-tabs' );
		wp_enqueue_script('jquery-ui-datepicker' );
		wp_enqueue_script('jquery-ui-accordion' );
		wp_enqueue_script('jquery-ui-controlgroup' );
		wp_enqueue_script('jquery-ui-checkboxradio' );
		wp_enqueue_script('jquery-ui-sortable' );

		wp_enqueue_script('wc-multistore-export-js',WOO_MSTORE_URL . '/assets/js/wc-multistore-export.js', array( 'jquery', 'jquery-ui-tabs', 'select2', 'jquery-ui-datepicker', 'jquery-ui-accordion', 'jquery-ui-controlgroup', 'jquery-ui-checkboxradio', 'jquery-ui-sortable' ) );
		wp_localize_script('wc-multistore-export-js','woonet_woocommerce_orders_export',	array( 'site_filter_placeholder' => __( 'Please select sites to export', 'woonet' ), 'order_status_filter_placeholder' => __( 'Please select order status to export', 'woonet' ), ) );
	}

	/**
	 *
	 */
	public function admin_notices() {
		if ( count( $this->system_messages ) < 1 ) {
			return;
		}

		foreach ( $this->system_messages as $system_message ) {
			if ( isset( $system_message['type'] ) ) {
				echo "<div class='notice " . $system_message['type'] . "'><p>" . $system_message['message'] . '</p></div>';
			} else {
				echo "<div class='notice notice-error'><p>" . $system_message . '</p></div>';
			}
		}
	}

	/**
	 *
	 */
	public function orders_export_page() {
		include WOO_MSTORE_PATH . 'includes/admin/views/html-order-export.php';
	}

	/**
	 *
	 */
	public function validate_settings() {
//		$nonce = $_POST['woonet-orders-export-interface-nonce'];
//		if ( ! wp_verify_nonce( $nonce, 'woonet-orders-export/interface-export' ) ) {
//			$this->errors_log[] = "Invalid nonce";
//
//			return;
//		}

		if ( isset( $_POST['export_format'] ) && in_array( $_POST['export_format'], array( 'csv', 'xls' ) ) ) {
			$this->export_type = $_POST['export_format'];
		} else {
			$this->errors_log[] = "Invalid export format";
		}

		if ( empty( $_POST['export_time_after'] ) ) {
			$this->export_time_after = 0;
		} else {
			$this->export_time_after = strtotime( $_POST['export_time_after'] );

			if ( false === $this->export_time_after ) {
				$this->errors_log[] = "Invalid time After";
			}
		}

		if ( empty( $_POST['export_time_before'] ) ) {
			$this->export_time_before = 9999999999;
		} else {
			$this->export_time_before = strtotime( $_POST['export_time_before'] );

			if ( false === $this->export_time_before ) {
				$this->errors_log[] = "Invalid time Before";
			}
		}

		if ( isset( $_POST['site_filter'] ) && is_array( $_POST['site_filter'] ) ) {
			if( is_multisite() ){
				$this->site_filter = array_filter( array_map( 'intval', $_POST['site_filter'] ) );
			}else{
				$this->site_filter = array_filter( array_map( 'sanitize_key', $_POST['site_filter'] ) );
			}

			if ( empty( $this->site_filter ) ) {
				$this->errors_log[] = "Empty site filter";
			}
		} else {
			$this->errors_log[] = "Empty site filter";
		}

		if ( isset( $_POST['order_status'] ) && is_array( $_POST['order_status'] ) ) {
			$this->order_status = $_POST['order_status'];
		} else {
			$this->order_status = array_keys( wc_get_order_statuses() );
		}

		if ( isset( $_POST['row_format'] ) && in_array( $_POST['row_format'], array(
				'row_per_order',
				'row_per_product'
			) ) ) {
			$this->row_format = $_POST['row_format'];
		} else {
			$this->errors_log[] = "Invalid row export format";
		}

		$this->export_fields = empty( $_POST["export_fields"] ) ? array() : $_POST["export_fields"];

		update_site_option( 'wc_multistore_orders_export_options', array(
			'export_type'        => $this->export_type,
			'export_time_after'  => $this->export_time_after,
			'export_time_before' => $this->export_time_before,
			'site_filter'        => $this->site_filter,
			'order_status'       => $this->order_status,
			'row_format'         => $this->row_format,
			'export_fields'      => $this->export_fields,
		) );
	}

	/**
	 * @return array|object|stdClass[]|null
	 */
	public function get_orders() {
		$status = array_map( 'esc_sql', $this->order_status );
		$status = "'" . implode( "', '", $status ) . "'";
		$date_start = date( 'Y-m-d', $this->export_time_after );
		$date_end = date( 'Y-m-d', $this->export_time_before );
		$date_start = new DateTime("$date_start 00:00:00", wp_timezone());
		$date_end = new DateTime("$date_end 23:59:59", wp_timezone());

		$args = apply_filters( 'wc_multistore_export_orders_args', array(
			'limit'     => -1,
			'status'    => $status,
			'orderby'   => 'ID',
			'order'     => 'ASC',
			'type'      => 'shop_order',
			'date_created' => $date_start->format('U').'...'.$date_end->format('U')
		) );

		if( ! is_multisite() ){
			return wc_get_orders( $args );
		}

		$orders = array();

		if( in_array( get_site_option('wc_multistore_master_store'), $this->site_filter ) ){
			switch_to_blog( get_site_option('wc_multistore_master_store') );
			$orders[get_site_option('wc_multistore_master_store')] = wc_get_orders( $args );
			restore_current_blog();
		}

		foreach ( WOO_MULTISTORE()->active_sites as $site ){
			if( in_array( $site->get_id(), $this->site_filter ) ){
				switch_to_blog( $site->get_id() );
				$orders[$site->get_id()] = wc_get_orders( $args );
				restore_current_blog();
			}
		}

		return $orders;
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function get_order_row( $order ){
		$row = array();

		foreach ( $this->export_fields as $export_field => $export_field_column_name ) {
			if( 0 === strpos( $export_field, 'order_item_' ) ){
				continue;
			}
			$cell =  $this->get_order_cell( $export_field, $order );
			$row[] = $cell;
		}

		return apply_filters('wc_multistore_export_orders_order_row', $row, $order );
	}

	/**
	 * @param WC_Order $order
	 *
	 * @param WC_Order_Item $order_item
	 *
	 * @return array
	 */
	public function get_product_row( $order, $order_item ){
		$row = array();

		foreach ( $this->export_fields as $export_field => $export_field_column_name ) {
			$cell =  $this->get_product_cell( $export_field, $order, $order_item );
			$row[] = $cell;
		}

		return apply_filters('wc_multistore_export_orders_product_row', $row, $order, $order_item);
	}

	/**
	 * @param $field
	 * @param $order
	 *
	 * @return array|mixed|string
	 */
	function get_order_cell( $field, $order ){
		$cell = '';

		if( $this->is_network_field( $field ) ){
			$cell = $this->get_network_field( $field );
		}

		if( $this->is_order_field( $field ) ){
			$cell = $this->get_order_field( $field, $order );
		}

		if( $this->is_order_items_field( $field ) ){
			$cell = $this->get_order_items_field( $field, $order );
		}

		return $cell;
	}

	/**
	 * @param $field
	 * @param $order
	 * @param $order_item
	 *
	 * @return array|mixed|string
	 */
	function get_product_cell( $field, $order, $order_item ){
		$cell = '';

		if( $this->is_network_field( $field ) ){
			$cell = $this->get_network_field( $field );
		}

		if( $this->is_order_field( $field ) ){
			$cell = $this->get_order_field( $field, $order );
		}

		if( $this->is_order_item_field( $field ) ){
			$cell = $this->get_order_item_field( $field, $order_item );
		}

		if( $this->is_order_item_product_field( $field ) ){
			$cell = $this->get_order_item_product_field( $field, $order_item );
		}

		if( $this->is_order_item_shipping_field( $field ) ){
			$cell = array();
			$shipping_items = $order->get_items('shipping');
			if( ! empty( $shipping_items ) ){
				foreach ($shipping_items as $shipping_item){
					$cell[] = $this->get_order_item_shipping_field( $field, $shipping_item );
				}
			}
		}

		if( $this->is_order_item_tax_field( $field ) ){
			$cell = array();
			$tax_items = $order->get_items('tax');
			if( ! empty( $tax_items ) ){
				foreach ($tax_items as $tax_item){
					$cell[] = $this->get_order_item_tax_field( $field, $tax_item );
				}
			}
		}

		if( $this->is_order_item_coupon_field( $field ) ){
			$cell = array();
			$coupon_items = $order->get_items('coupon');
			if( ! empty( $coupon_items ) ){
				foreach ($coupon_items as $coupon_item){
					$cell[] = $this->get_order_item_coupon_field( $field, $coupon_item );
				}
			}
		}

		if( $this->is_order_item_fee_field( $field ) ){
			$cell = array();
			$fee_items = $order->get_items('fee');
			if( ! empty( $fee_items ) ){
				foreach ($fee_items as $fee_item){
					$cell[] = $this->get_order_item_fee_field( $field, $fee_item );
				}
			}
		}

		if( $this->is_order_items_field( $field ) ){
			$cell = $this->get_order_items_field( $field, $order );
		}

		return $cell;
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_network_field( $field ){
		if( 0 === strpos( $field, 'network__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 *
	 * @return mixed
	 */
	function get_network_field( $field ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;

		return $this->$function_name();
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_field( $field ){
		if( 0 === strpos( $field, 'order__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 * @param $order
	 *
	 * @return mixed
	 */
	function get_order_field( $field, $order ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;

		return $order->$function_name();
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_items_field( $field ){
		if( 0 === strpos( $field, 'order__items' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_item_field( $field ){
		if( 0 === strpos( $field, 'order_item__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 * @param $order_item
	 *
	 * @return mixed
	 */
	function get_order_item_field( $field, $order_item ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;
		return $order_item->$function_name();
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_item_product_field($field){
		if( 0 === strpos( $field, 'order_item_product__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 * @param $order_item
	 *
	 * @return mixed|string
	 */
	function get_order_item_product_field( $field, $order_item ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;

		$product = $order_item->get_product();

		if( ! $product ){
			return '';
		}

		$value = $product->$function_name();

		if( $field_name == 'attributes' ){
			$terms = array();
			if( ! empty( $value ) ){
				foreach ( $value as $attribute ){
					if( is_object( $attribute ) ){
						$terms = $attribute->get_terms();
					}
				}

				$value = $terms;
			}
		}

		return $value;

	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_item_shipping_field($field){
		if( 0 === strpos( $field, 'order_item_shipping__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 * @param $order_item
	 *
	 * @return string
	 */
	function get_order_item_shipping_field( $field, $order_item ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;

		$shipping = new WC_Order_Item_Shipping( $order_item->get_id() );

		if( ! $shipping ){
			return '';
		}

		return $shipping->$function_name();
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_item_tax_field($field){
		if( 0 === strpos( $field, 'order_item_tax__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 * @param $order_item
	 *
	 * @return string
	 */
	function get_order_item_tax_field( $field, $order_item ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;

		$tax = new WC_Order_Item_Tax( $order_item->get_id() );

		if( ! $tax ){
			return '';
		}

		return $tax->$function_name();
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_item_coupon_field($field){
		if( 0 === strpos( $field, 'order_item_coupon__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 * @param $order_item
	 *
	 * @return string
	 */
	function get_order_item_coupon_field( $field, $order_item ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;

		$coupon = new WC_Order_Item_Coupon( $order_item->get_id() );

		if( ! $coupon ){
			return '';
		}

		return $coupon->$function_name();
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	function is_order_item_fee_field($field){
		if( 0 === strpos( $field, 'order_item_fee__' ) ){
			return true;
		}

		return false;
	}

	/**
	 * @param $field
	 * @param $order_item
	 *
	 * @return string
	 */
	function get_order_item_fee_field( $field, $order_item ){
		list($class,$field_name) = explode('__', $field );
		$function_name = 'get_'. $field_name;

		$fee = new WC_Order_Item_Fee( $order_item->get_id() );

		if( ! $fee ){
			return '';
		}

		return $fee->$function_name();
	}

	/**
	 * @param $field
	 * @param $order
	 *
	 * @return array|string
	 */
	function get_order_items_field( $field, $order ){
		$order_items = $order->get_items( array( 'line_item', 'fee', 'shipping', 'tax', 'coupon' ) );

		if( ! $order_items ){ return ''; }

		$order__items = array();
		foreach ( $order_items as $order_item ){
			$order__item = array();
			foreach ( $this->export_fields as $export_field => $export_field_column_name ) {
				if( $this->is_order_item_field($export_field) && $order_item->get_type() == 'line_item' ){
					$order__item[$export_field] = $this->get_order_item_field( $export_field, $order_item );
				}

				if( $this->is_order_item_product_field($export_field) && $order_item->get_type() == 'line_item' ){
					$order__item[$export_field] = $this->get_order_item_product_field( $export_field, $order_item );
				}

				if( $this->is_order_item_shipping_field($export_field) && $order_item->get_type() == 'shipping' ){
					$order__item[$export_field] = $this->get_order_item_shipping_field( $export_field, $order_item );
				}

				if( $this->is_order_item_tax_field($export_field) && $order_item->get_type() == 'tax' ){
					$order__item[$export_field] = $this->get_order_item_tax_field( $export_field, $order_item );
				}

				if( $this->is_order_item_coupon_field($export_field) && $order_item->get_type() == 'coupon' ){
					$order__item[$export_field] = $this->get_order_item_coupon_field( $export_field, $order_item );
				}

				if( $this->is_order_item_fee_field($export_field) && $order_item->get_type() == 'fee' ){
					$order__item[$export_field] = $this->get_order_item_fee_field( $export_field, $order_item );
				}
			}

			$order__items[] = $order__item;
		}

		return $order__items;
	}

	/**
	 * @return array
	 */
	private function get_header() {
		$header = array();

		foreach ( $this->export_fields as $key => $value ) {
			if ( 'row_per_order' != $this->row_format || 0 !== strpos( $value, 'order_item_' ) ) {
				$header[ $key ] = $value;
			}
		}

		return $header;
	}

	/**
	 * @return string|void
	 */
	private function get_site_id() {
		return get_bloginfo( 'name' );
	}

	/**
	 * @return string
	 */
	private function get_blogname() {
		$blog_details = get_blog_details( get_current_blog_id() );

		if ( ! empty( $blog_details ) && ! empty( $blog_details->blogname ) ) {
			return $blog_details->blogname;
		}

		return "(Empty Site Title)";
	}

}