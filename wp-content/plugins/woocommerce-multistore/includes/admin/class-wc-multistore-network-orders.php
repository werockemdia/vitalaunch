<?php
/**
 * Network Orders Handler
 *
 * This handles Network Orders related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Network_Orders
 */
class WC_Multistore_Network_Orders {

	/**
	 * @param bool $init_actions
	 */
	public function __construct( $init_actions = true ) {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! is_multisite() && WOO_MULTISTORE()->site->get_type() == 'child' ){ return; }
		if( ! WOO_MULTISTORE()->permission ){ return; }

		if ( $init_actions ) {
			$this->hooks();
		}
	}

	/**
	 * Hooks
	 */
	public function hooks(){
	    add_action( 'network_admin_menu', array( $this, 'add_network_submenu_page' ), 12 );
	    add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 12 );

	    add_action( 'load-multistore_page_network-orders', array( $this, 'admin_notices' ) );

	    add_action( 'admin_print_styles-multistore_page_network-orders', array( $this, 'admin_print_styles' ) );
	    add_action( 'admin_print_scripts-multistore_page_network-orders', array( $this, 'admin_print_scripts' ) );

		add_action( 'load-multistore_page_network-orders', array( $this, 'screen_options' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_options' ), 15, 3 );
	    add_filter( 'set_screen_option_orders_per_page', array( $this, 'set_screen_options' ), 15, 3 );
	    add_filter( 'manage_multistore_page_network-orders-network_columns', array( $this, 'add_column_headers' ) );

	    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

	    if ( isset( $_GET['page'] ) && $_GET['page'] == 'network-orders' ) {
		    add_action( 'wp_loaded', array( $this, 'orders_interface_form_submit' ), 1 );
	    }
    }

	/**
	 * Adds network orders submenu page
	 */
	public function add_network_submenu_page() {
		add_submenu_page( 'woonet-woocommerce', __( 'Vendor Orders', 'woonet' ), __( 'Vendor Orders', 'woonet' ), 'manage_woocommerce', 'network-orders', array( $this, 'orders_interface' ), 4 );
	}

	/**
	 * Adds network orders submenu page
	 */
	public function add_submenu_page() {
        if( ! is_multisite() ){
	        add_submenu_page('woonet-woocommerce','Vendor Orders','Vendor Orders','manage_woocommerce','network-orders', array( $this, 'orders_interface' ) );
        }
	}

	/**
	 * Notices
	 */
	function admin_notices() {
		global $WOO_SL_messages;

		if ( ! is_array( $WOO_SL_messages ) || count( $WOO_SL_messages ) < 1 ) {
			return;
		}

		foreach ( $WOO_SL_messages as $message_data ) {
			echo "<div id='notice' class='" . $message_data['status'] . " fade'><p>" . $message_data['message'] . '</p></div>';
		}
	}

	/**
	 * Styles
	 */
	function admin_print_styles() {
		$WC_url = plugins_url() . '/woocommerce';
		wp_enqueue_style( 'woocommerce_admin_styles', $WC_url . '/assets/css/wc-multistore-admin.css', array() );
	}

	/**
	 * Scripts
	 */
	function admin_print_scripts() {
		$WC_url = plugins_url() . '/woocommerce';
		wp_register_script( 'jquery-tiptip', $WC_url . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'jquery-tiptip' );

		wp_register_script( 'woocommerce_admin', $WC_url . '/assets/js/admin/woocommerce_admin.js', array(
			'jquery',
			'jquery-blockui',
			'jquery-ui-sortable',
			'jquery-ui-widget',
			'jquery-ui-core',
			'jquery-tiptip'
		) );
		wp_enqueue_script( 'woocommerce_admin' );

		$locale  = localeconv();
		$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
		$params = array(
			/* translators: %s: decimal */
			'i18n_decimal_error'                => sprintf( __( 'Please enter with one decimal point (%s) without thousand separators.', 'woocommerce' ), $decimal ),
			/* translators: %s: price decimal separator */
			'i18n_mon_decimal_error'            => sprintf( __( 'Please enter with one monetary decimal point (%s) without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
			'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
			'i18n_sale_less_than_regular_error' => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
			'i18n_delete_product_notice'        => __( 'This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'woocommerce' ),
			'i18n_remove_personal_data_notice'  => __( 'This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'woocommerce' ),
			'decimal_point'                     => $decimal,
			'mon_decimal_point'                 => wc_get_price_decimal_separator(),
			'ajax_url'                          => admin_url( 'admin-ajax.php' ),
			'strings'                           => array(
				'import_products' => __( 'Import', 'woocommerce' ),
				'export_products' => __( 'Export', 'woocommerce' ),
			),
			'nonces'                            => array(
				'gateway_toggle' => wp_create_nonce( 'woocommerce-toggle-payment-gateway-enabled' ),
			),
			'urls'                              => array(
				'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
				'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
			),
		);

		wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
	}

	/**
	 * Screen options
	 */
	public function screen_options() {
		$screen = get_current_screen();
		if ( is_object( $screen ) && $screen->id == 'multistore_page_network-orders-network' || $screen->id == 'multistore_page_network-orders' ) {
			$args = array(
				'label'   => __( 'Orders per Page', 'woonet' ),
				'default' => 10,
				'option'  => 'orders_per_page',
			);
			add_screen_option( 'per_page', $args );
		}
	}

	/**
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return int|mixed
	 */
	public function set_screen_options( $status, $option, $value ) {
		if ( 'orders_per_page' == $option ) {
			$status = absint( $value );
		}

		return $status;
	}

	/**
	 * Enqueue assets for the updater
	 */
	public function enqueue_assets() {
		if ( is_network_admin() ) {
			wp_enqueue_script( 'woosl-network-orders', WOO_MSTORE_ASSET_URL . '/assets/js/wc-multistore-network-orders.js', array( 'jquery' ), WOO_MSTORE_VERSION, true );
		}
	}

	/**
	 * @param $term
	 * @param $blog_prefix
	 *
	 * @return mixed|void
	 */

	public function orders_interface_form_submit() {
		$action   = isset( $_POST['action'] ) ? $_POST['action'] : '';
		$data_set = $_POST;

		if ( empty( $action ) ) {
			$action   = isset( $_GET['action'] ) ? $_GET['action'] : '';
			$data_set = $_GET;
		}

        if( empty( $action ) || empty( $data_set['post'] ) ){
            return;
        }

        $posts_list        = (array) $data_set['post'];
        $update_post_array = array();
        $response          = array();

        foreach ( $posts_list as  $post_data ) {
            list($site_id, $post_id) = explode( '_', $post_data );

            if ( ! empty( $post_id ) ) {
                $update_post_array[ $site_id ][] = array(
                    'status' => $action,
                    'post'   => $post_id,
                );
            }
        }

        if ( ! empty( $update_post_array ) ) {
            if( ! empty( $update_post_array['master'] ) ){
	            $response['master']  = wc_multistore_update_orders_status( $update_post_array['master'], 'master' );
            }

            foreach ( WOO_MULTISTORE()->active_sites as $site ){
                if( ! empty( $update_post_array[$site->get_id()] ) ){
                    if( is_multisite() ){
	                    $response[$site->get_id()] = wc_multistore_update_orders_status( $update_post_array[$site->get_id()], $site->get_id() );
                    }else{
                        $wc_multistore_order_api_master = new WC_Multistore_Order_Api_Master();
                        $result = $wc_multistore_order_api_master->sync_order_status( $update_post_array[$site->get_id()], $site->get_id() );
	                    $response[$site->get_id()] = $result['data'];
                    }
                }
            }

            set_transient( 'woonet_order_status_updates', $response, 300 );

            wp_redirect(
                add_query_arg(
                    array(
                        'paged' => ! empty( $_REQUEST['paged'] ) ? (int) $_REQUEST['paged'] : 1,
                    ),
                    network_admin_url( 'admin.php?page=network-orders' )
                )
            );
            die;
		}
	}

	/**
	 *
	 */
	function orders_interface() {
		$per_page       = ! empty( get_user_option('orders_per_page') ) ? get_user_option('orders_per_page') : 10 ;
		$site_filter    = isset( $_REQUEST['woonet_site_filter'] ) ? esc_attr($_REQUEST['woonet_site_filter']) : '';
		$paged          = isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;
		$post_status    = isset( $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : '';
		$search         = empty( $_REQUEST['s'] ) ? '' : esc_sql( $_REQUEST['s'] );
        $network_orders = wc_multistore_get_network_orders( $per_page, $paged, $post_status, $search, $site_filter );
		$total_orders_count = $network_orders['total_orders_count'];
        $highest_total_queried_orders_count = $network_orders['highest_total_queried_orders_count'];
        $total_orders_count_by_status = $network_orders['total_orders_count_by_status'];
		$woonet_messages = get_transient( 'woonet_order_status_updates' );
		delete_transient( 'woonet_order_status_updates' ); // we need it only once.
		?>

        <div id="woonet" class="wrap">
            <div class='order_status_updates'>
		        <?php if ( ! empty( $woonet_messages ) ) : ?>
			         <?php foreach ( $woonet_messages as $site ) : ?>
				        <?php $site_status = $site['status'] == 'failed' ? 'error' : 'success'; ?>
                        <div class="notice notice-<?php echo $site_status; ?> is-dismissible">
                            <p><?php _e( $site['message'], 'woonet' ); ?></p>
                        </div>
			        <?php endforeach; ?>
		        <?php endif; ?>
            </div>

            <h2>Orders</h2>
            <ul class="subsubsub">
                <li class="all">
                    <a class="<?php if ( $post_status == '' ) {	echo 'current';	} ?>" href="admin.php?page=network-orders"> All <span class="count">(<?php echo $total_orders_count; ?>)</span> </a>
                </li>
				<?php foreach ( $total_orders_count_by_status as $order_status => $count ): ?>
                    <?php if( !empty($count) ): ?>
                        <li class="<?php echo $order_status; ?>">
                            <span class="separator"> |</span>
                            <a class="<?php if ( $post_status == $order_status ) { echo 'current'; } ?>" href="admin.php?page=network-orders&post_status=<?php echo $order_status; ?>"><?php echo wc_get_order_status_name($order_status); ?> <span class="count">(<?php echo $total_orders_count_by_status[ $order_status ]; ?>)</span></a>
                        </li>
				    <?php endif; ?>
				<?php endforeach; ?>
            </ul>

            <form id="posts-filter" method="get">
		        <?php if ( ! empty( $_REQUEST['orderby'] ) ) : ?>
			        <input type="hidden" name="orderby" value="<?php echo esc_attr( $_REQUEST['orderby'] ); ?>" />
	            <?php endif; ?>

	            <?php if ( ! empty( $_REQUEST['order'] ) ) : ?>
                    <input type="hidden" name="order" value="<?php echo esc_attr( $_REQUEST['order'] ); ?>" />
	            <?php endif; ?>

                <?php if ( ! empty( $_REQUEST['post_mime_type'] ) ) : ?>
                    <input type="hidden" name="post_mime_type" value="<?php echo esc_attr( $_REQUEST['post_mime_type'] ); ?>" />
	            <?php endif; ?>

                <?php if ( ! empty( $_REQUEST['detached'] ) ) : ?>
                    <input type="hidden" name="detached" value="<?php echo esc_attr( $_REQUEST['detached'] ); ?>" />
	            <?php endif; ?>

                <p class="search-box">
                    <label class="screen-reader-text" for="<?php echo esc_attr( 'post-search-input' ); ?>"><?php __( 'Search orders', 'woocommerce' ); ?>:</label>
                    <input type="search" id="<?php echo esc_attr( 'post-search-input' ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
                    <input type="hidden" id="page" name="page" value="network-orders" />
			        <?php submit_button( __( 'Search orders', 'woocommerce' ), '', '', false, array( 'id' => 'search-submit' ) ); ?>
                </p>

                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
				        <?php $this->bulk_action( $post_status ); ?>
                    </div>

                    <div class="alignleft site-filter">
                        <select class='woonet_site_filter' name='woonet_site_filter' data-attr="<?php echo network_admin_url('admin.php?page=network-orders'); ?>">
					        <?php
                                $sites = array_merge(
                                    array(WOO_MULTISTORE()->site),
                                    WOO_MULTISTORE()->active_sites
                                );
                            ?>
					        <option value=''> All </option>

                            <?php if ( ! empty( $sites ) ) : ?>
                                <?php foreach( $sites as $site ) : ?>
                                    <?php if ( $site->get_type() == 'master' ) : ?>
                                        <option <?php selected($site_filter, 'master' ); ?> value='master'> <?php echo esc_html($site->get_url()); ?> </option>
                                    <?php else: ?>
                                        <option <?php selected($site_filter, $site->get_id() ); ?> value='<?php echo $site->get_id(); ?>'> <?php echo esc_html($site->get_url()); ?> </option>
							        <?php endif; ?>
						        <?php endforeach; ?>
					        <?php endif; ?>
                        </select>
                    </div>

			        <?php $this->pagination( $highest_total_queried_orders_count, $per_page, $paged ); ?>
                </div>

                <div class="post-type-shop_order">
                    <table class="wp-list-table widefat fixed posts">
                        <thead>
                        <tr>
                            <th style="" class="manage-column column-cb check-column" id="cb"><label for="cb-select-all-1" class="screen-reader-text"><?php _e( 'Select All', 'woonet' ); ?></label><input type="checkbox" id="cb-select-all-1"></th>
                            <th style="" class="manage-column column-order_blog" id="order_blog" scope="col"><?php _e( 'Store name', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_title column-primary" id="order_title" scope="col"><?php _e( 'Order', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_date" id="order_date" scope="col"><?php _e( 'Date', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_status" id="order_status" scope="col"><?php _e( 'Status', 'woonet' ); ?></th>
                            <th style="" class="manage-column" id="vendor_charged" scope="col"><?php _e( 'Vendor Charged', 'woonet' ); ?></th>
                            <th style="" class="manage-column" id="vendor_charged_amount" scope="col"><?php _e( 'Vendor Charged Amount', 'woonet' ); ?></th>
                            <th style="" class="manage-column" id="vendor_charged_detail" scope="col"><?php _e( 'Vendor Charged Detail', 'woonet' ); ?></th>
                            <?php /*
                            <th style="" class="manage-column column-billing_address" id="billing_address" scope="col"><?php _e( 'Billing', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-shipping_address" id="shipping_address" scope="col"><?php _e( 'Ship to', 'woonet' ); ?></th>
                            */ ?>
                            <th style="" class="manage-column column-order_total" id="order_total" scope="col"><?php _e( 'Total', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_actions" id="order_actions" scope="col"><?php _e( 'Actions', 'woonet' ); ?></th>
                            <?php if ( WOO_MULTISTORE()->settings['enable-order-import'] == 'yes' ) : ?>
                                <th style="" class="manage-column column-order_originated" id="order_originated" scope="col"><?php _e( 'Originating Site', 'woonet' ); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>

                        <tfoot>
                        <tr>
                            <th style="" class="manage-column column-cb check-column" id="cb"><label for="cb-select-all-1" class="screen-reader-text"><?php _e( 'Select All', 'woonet' ); ?></label><input type="checkbox" id="cb-select-all-1"></th>
                            <th style="" class="manage-column column-order_blog" id="order_blog" scope="col"><?php _e( 'Store name', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_title column-primary" id="order_title" scope="col"><?php _e( 'Order', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_date" id="order_date" scope="col"><?php _e( 'Date', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_status" id="order_status" scope="col"><?php _e( 'Status', 'woonet' ); ?></th>
                            <th style="" class="manage-column" id="vendor_charged" scope="col"><?php _e( 'Vendor Charged', 'woonet' ); ?></th>
                            <th style="" class="manage-column" id="vendor_charged_amount" scope="col"><?php _e( 'Vendor Charged Amount', 'woonet' ); ?></th>
                            <th style="" class="manage-column" id="vendor_charged_detail" scope="col"><?php _e( 'Vendor Charged Detail', 'woonet' ); ?></th>
                            <?php /*
                            <th style="" class="manage-column column-billing_address" id="billing_address" scope="col"><?php _e( 'Billing', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-shipping_address" id="shipping_address" scope="col"><?php _e( 'Ship to', 'woonet' ); ?></th>
                            */ ?>
                            <th style="" class="manage-column column-order_total" id="order_total" scope="col"><?php _e( 'Total', 'woonet' ); ?></th>
                            <th style="" class="manage-column column-order_actions" id="order_actions" scope="col"><?php _e( 'Actions', 'woonet' ); ?></th>
	                        <?php if ( WOO_MULTISTORE()->settings['enable-order-import'] == 'yes' ) : ?>
                                <th style="" class="manage-column column-order_originated" id="order_originated" scope="col"><?php _e( 'Originating Site', 'woonet' ); ?></th>
	                        <?php endif; ?>
                        </tr>
                        </tfoot>

                        <tbody id="the-list">
                        <?php if ( ! empty( $network_orders['orders'] ) ) : ?>
                            <?php foreach ( $network_orders['orders'] as $order_data ) : ?>
				                <?php if ( ! empty( $order_data ) ) : ?>
                                    <tr class="post-<?php echo esc_attr( $order_data['store_name'] ); ?>_<?php echo esc_attr( $order_data['id'] ); ?> type-shop_order status-<?php echo esc_attr( $order_data['status'] ); ?> post-password-required hentry" id="post-<?php echo esc_attr( $order_data['site_id'] ); ?>_<?php echo esc_attr( $order_data['id'] ); ?>">
                                        <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $order_data['site_id']; ?>_<?php echo $order_data['id']; ?>" name="post[]" id="cb-select-<?php echo $order_data['site_id']; ?>_<?php echo $order_data['id']; ?>"><div class="locked-indicator"></div></th>
                                        <td class="order_blog column-order_blog"><?php $this->render_shop_order_columns( 'order_blog', $order_data, $order_data['site_id'] ); ?></td>
                                        <td class="order_title column-order_title"><?php $this->render_shop_order_columns( 'order_title', $order_data, $order_data['site_id'] ); ?></td>
                                        <td class="order_date column-order_date"><?php $this->render_shop_order_columns( 'order_date', $order_data, $order_data['site_id'] ); ?></td>
                                        <td class="order_status column-order_status"><?php $this->render_shop_order_columns( 'order_status', $order_data, $order_data['site_id'] ); ?></td>
                                        <td class="vendor_charged column-vendor_charged">
                                            <?php $id_post = $order_data['id'];
                                            $rtyu = $order_data['site_id'];
                                            $table_name = "wp_".$rtyu."_postmeta";
                                            global $wpdb;
                                            $data = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `post_id` = $id_post AND `meta_key` LIKE 'status_payment_changed'");
                                            
                                            if($data[0]->meta_value == 'succeeded'){
                                                echo "Yes";
                                            }else{
                                               echo "NO"; 
                                            }

                                            
                                            ?>
                                            </td>
                                        <td class="vendor_chargedamount column-vendor_charged_amount">
                                            <?php $id_post = $order_data['id'];
                                            $rtyu = $order_data['site_id'];
                                            $table_name = "wp_".$rtyu."_postmeta";
                                            global $wpdb;
                                            $data = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `post_id` = $id_post AND `meta_key` LIKE 'vendor_charged_amount'");
                                            echo $data[0]->meta_value/100;

                                            
                                            ?>
                                            </td>
                                            <td class="vendor_chargeddetail column-vendor_charged_detail">
                                            <?php 
                                            $storename = $order_data['store_name'];
                                            $id_post = $order_data['id'];
                                            $rtyu = $order_data['site_id'];
                                            $table_name = "wp_".$rtyu."_postmeta";
                                            global $wpdb;
                                            $data = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `post_id` = $id_post AND `meta_key` LIKE 'vendor_charged_amount'");
                                            $charged = $data[0]->meta_value/100; ?>
                                            
                                            Vendor <?php echo $storename; ?> is charged <?php echo $charged; ?> for Order <?php echo $order_data['id']; ?>
                                            </td>
                                        <td class="order_total column-order_total"><?php echo get_woocommerce_currency_symbol( $order_data['currency'] ); ?> <?php $this->render_shop_order_columns( 'order_total', $order_data, $order_data['site_id'] ); ?></td>
                                        <td class="order_actions column-order_actions"><?php $this->render_shop_order_columns( 'order_actions', $order_data, $order_data['site_id'] ); ?></td>
                                        <td class="order_originated column-order_originated">
                                        <?php 
                                        $site_id = $order_data['site_id'];
                                        if($site_id == 'master'){
                                            echo "vitalaunch.io"; 
                                        }else{
                                        global $wpdb;
                                        $data = $wpdb->get_results("SELECT * FROM `wp_blogs` WHERE `blog_id` = $site_id");
                                        echo $data[0]->domain;
                                        }
                                        ?></td>
                                    </tr>
		                        <?php endif; ?>
                            <?php endforeach; ?>
				        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="tablenav bottom">
			        <?php $this->pagination( $highest_total_queried_orders_count, $per_page, $paged, 'bottom' ); ?>
                </div>
            </form>
        </div>
		<?php

	}

	public function render_shop_order_columns( $column, $the_order, $site_id ) {
		switch ( $column ) {
			case 'order_status':
				printf(
					'<mark class="order-status status-%s"><span>%s</span></mark>',
					esc_attr( $the_order['status'] ),
					esc_html( wc_get_order_status_name( $the_order['status'] ) )
				);
				break;
			case 'order_blog':
				echo '<span class="na">' . $the_order['store_name'] . '</span>';
				break;
			case 'order_date':
				echo '<abbr title="' . esc_attr( $the_order['date_created'] ) . '">' . esc_html( $the_order['date_created'] ) . '</abbr>';
				break;
			case 'customer_message':
				if ( $the_order->get_customer_note() ) {
					echo '<span class="note-on tips" data-tip="' . esc_attr( $the_order['customer_note'] ) . '">' . __( 'Yes', 'woonet' ) . '</span>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
			case 'order_items':
				echo '<a href="#" class="show_order_items">' . count( $the_order['line_items'] ) . '</a>';

				if ( sizeof( $the_order['line_items'] ) > 0 ) {

					echo '<table class="order_items" cellspacing="0">';

					foreach ( $the_order['line_items'] as $item ) {
						?>
                        <tr class="<?php echo apply_filters( 'woocommerce_admin_order_item_class', '', $item ); ?>">
                            <td class="qty"><?php echo absint( $item['qty'] ); ?></td>
                            <td class="name">
								<?php $item['name']; ?>
								<?php if ( $item['meta'] ) : ?>
                                    <a class="tips" href="#" data-tip="<?php echo esc_attr( $item['meta'] ); ?>">[?]</a>
								<?php endif; ?>
                            </td>
                        </tr>
						<?php
					}

					echo '</table>';

				} else {
					echo '&ndash;';
				}
				break;
			case 'billing_address':
				$address = implode( ' ', $the_order['billing'] );

				if ( $address ) {
					echo esc_html( $address );

					if ( $the_order['payment_method_title'] ) {
						/* translators: %s: payment method */
						echo '<span class="description">' . sprintf( __( 'via %s', 'woocommerce' ), esc_html( $the_order['payment_method_title'] ) ) . '</span>'; // WPCS: XSS ok.
					}
				} else {
					echo '&ndash;';
				}
				break;
			case 'shipping_address':
				if ( ! empty( $the_order['shipping'] ) ) {
					echo '<a target="_blank" href="' . esc_url( 'https://maps.google.com/maps?&q=' . urlencode( implode( ',', $the_order['shipping'] ) ) . '&z=16' ) . '">' . esc_html( preg_replace( '#<br\s*/?>#i', ', ', implode( ' ', $the_order['shipping'] ) ) ) . '</a>';
				} else {
					echo '&ndash;';
				}

				if ( ! empty( $the_order['shipping_method_title'] ) ) {
					echo '<small class="meta">' . __( 'Via', 'woonet' ) . ' ' . esc_html( $the_order['shipping_method_title'] ) . '</small>';
				}

				break;
			case 'order_notes':
				echo '';
				break;
			case 'order_total':
				echo esc_html( strip_tags( $the_order['total'] ) );

				if ( ! empty( $the_order['payment_method_title'] ) ) {
					echo '<small class="meta">' . __( 'Via', 'woonet' ) . ' ' . esc_html( $the_order['payment_method_title'] ) . '</small>';
				}
				break;
			case 'order_title':
				$customer_tip = '';

				if ( $address = $the_order['billing'] ) {
					$customer_tip .= __( 'Billing:', 'woonet' ) . ' ' . implode( ' ', $the_order['billing'] ) . '<br/><br/>';
				}

				if ( $the_order['billing']['phone'] ) {
					$customer_tip .= __( 'Tel:', 'woonet' ) . ' ' . $the_order['billing']['phone'];
				}

				echo '<div class="tips" data-tip="' . esc_attr( $customer_tip ) . '">';

				if ( false ) {
					// if ( ! empty( $user_info ) ) {

					$username = '<a href="' . $the_order['store_url'] . 'user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

					if ( $user_info->first_name || $user_info->last_name ) {
						$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
					} else {
						$username .= esc_html( ucfirst( $user_info->display_name ) );
					}

					$username .= '</a>';

				} else {
					if ( $the_order['billing']['first_name'] || $the_order['billing']['first_name'] ) {
						$username = trim( $the_order['billing']['first_name'] . ' ' . $the_order['billing']['last_name'] );
					} else {
						$username = __( 'Guest', 'woonet' );
					}
				}

				if ( ! empty( $the_order['__custom_order_id'] ) ) {
					$display_order_id = $the_order['__custom_order_id'];
				} else {
					$display_order_id = $the_order['id'];
				}

				printf( __( '%1$s by %2$s', 'woonet' ), '<a href="' . $the_order['store_url'] . '/wp-admin/post.php?post=' . esc_attr( $the_order['id'] ) . '&action=edit' . '"><strong>' . esc_attr( $display_order_id ) . '</strong></a>', $username );

				if ( $the_order['billing']['email'] ) {
					echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $the_order['billing']['email'] ) . '">' . esc_html( $the_order['billing']['email'] ) . '</a></small>';
				}

				echo '</div>';

				break;
			case 'order_actions':
				echo '<p class="woo-network-order-actions">';

				do_action( 'WOO_MSTORE_ORDER/woocommerce_admin_order_actions_start', $the_order );

				$actions = array();

				if ( ! empty( $the_order['status'] ) && in_array( $the_order['status'], array( 'pending', 'on-hold' ) ) ) {
					$actions['processing'] = array(
						'url'    => add_query_arg(
							array(
								'action' => 'wc-processing',
								'post'   => $site_id . '_' . $the_order['id'],
								'paged'  => ! empty( $_REQUEST['paged'] ) ? (int) $_REQUEST['paged'] : 1,
							),
							admin_url( 'admin.php?page=network-orders' )
						),
						'name'   => __( 'Processing', 'woonet' ),
						'action' => 'processing',
					);
				}

				if ( ! empty( $the_order['status'] ) && in_array( $the_order['status'], array( 'pending', 'on-hold', 'processing' ) ) ) {
					$actions['complete'] = array(
						'url'    => add_query_arg(
							array(
								'action' => 'wc-completed',
								'post'   => $site_id . '_' . $the_order['id'],
								'paged'  => ! empty( $_REQUEST['paged'] ) ? (int) $_REQUEST['paged'] : 1,
							),
							admin_url( 'admin.php?page=network-orders' )
						),
						'name'   => __( 'Complete', 'woonet' ),
						'action' => 'complete',
					);

					$actions['cancel'] = array(
						'url'    => add_query_arg(
							array(
								'action' => 'wc-cancelled',
								'post'   => $site_id . '_' . $the_order['id'],
								'paged'  => ! empty( $_REQUEST['paged'] ) ? (int) $_REQUEST['paged'] : 1,
							),
							admin_url( 'admin.php?page=network-orders' )
						),
						'name'   => __( 'Cancel', 'woonet' ),
						'action' => 'cancel',
					);
				}

				$actions['view'] = array(
					'url'    => esc_url( $the_order['store_url'] . '/wp-admin/post.php?post=' . $the_order['id'] . '&action=edit' ),
					'name'   => __( 'View', 'woonet' ),
					'action' => 'view',
				);

				if ( $the_order['status'] != 'refunded' && $the_order['status'] != 'cancelled' && empty($the_order['is_imported']) ) {
					$actions['refund'] = array(
						'action' => 'refund',
						'name'   => 'Refund entire order',
						'url'    => add_query_arg(
							array(
								'action' => 'refund',
								'post'   => $site_id . '_' . $the_order['id'],
								'paged'  => ! empty( $_REQUEST['paged'] ) ? (int) $_REQUEST['paged'] : 1,
							),
							network_admin_url( 'admin.php?page=network-orders' )
						),
					);
				}


				$actions = apply_filters( 'WOO_MSTORE_ORDER/woocommerce_admin_order_actions', $actions, $the_order, $site_id );

				foreach ( $actions as $action ) {

					printf(
						'<a style="margin-bottom: 5px;" class="button tips wc-action-button wc-action-button-%1$s %1$s" href="%2$s" data-tip="%3$s">%3$s</a>',
						esc_attr( $action['action'] ),
						esc_url( $action['url'] ),
						esc_attr( $action['name'] )
					);
				}

				do_action( 'WOO_MSTORE_ORDER/woocommerce_admin_order_actions_end', $the_order );
				echo '</p>';
				break;
			case 'order_originated':
                if( isset( $the_order['meta_data']['WOONET_PARENT_ORDER_ORIGIN_URL'] ) && isset( $the_order['meta_data']['WOONET_PARENT_ORDER_ORIGIN_TEXT'] ) ){
                    $text = $the_order['meta_data']['WOONET_PARENT_ORDER_ORIGIN_TEXT'];
                    $parent_id = $the_order['meta_data']['WOONET_PARENT_ORDER_ORIGIN_ID'];
                    $url = $the_order['meta_data']['WOONET_PARENT_ORDER_ORIGIN_URL'] . '/wp-admin/post.php?post='.$parent_id.'&action=edit';
	                echo "<a target='_blank' href='" . $url . "'>" . $text . "</a>";
                }
				break;
		}
	}

	/**
	 * @param $total_items
	 * @param $per_page
	 * @param $paged
	 * @param string $which
	 */
	function pagination( $total_items, $per_page, $paged, $which = 'top' ) {
		$total_pages    = ceil( $total_items / $per_page );
		$output         = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';
		$current        = $paged;
		$current_url    = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url    = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );
		$page_links     = array();
        $search         = isset( $_REQUEST['s'] ) ? esc_sql( $_REQUEST['s'] ) : '';

		$disable_first = $disable_last = '';
		if ( $current == 1 ) {
			$disable_first = ' disabled';
		}

		if ( $current == $total_pages ) {
			$disable_last = ' disabled';
		}

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first . ' button',
			esc_attr__( 'Go to the first page', 'woonet' ),
			esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first . ' button',
			esc_attr__( 'Go to the previous page', 'woonet' ),
			esc_url( add_query_arg(
                        array(
	                        'paged' => max( 1, $current - 1 ),
                            's'     => $search
			            ),
                        $current_url
                    ),
		    ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which ) {
			$html_current_page = $current;
		} else {
			$html_current_page = sprintf(
				"%s<input class='current-page' id='current-page-selector' title='%s' type='text' name='paged' value='%s' size='%d' />",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Select Page', 'woonet' ) . '</label>',
				esc_attr__( 'Current page', 'woonet' ),
				$current,
				strlen( $total_pages )
			);
		}

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[]     = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last . ' button',
			esc_attr__( 'Go to the next page', 'woonet' ),
			esc_url(
                add_query_arg(
	                array(
		                'paged' => min( $total_pages, $current + 1 ),
		                's'     => $search
	                ),
	                $current_url
                ),
            ),
			'&rsaquo;'
		);

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last . ' button',
			esc_attr__( 'Go to the last page', 'woonet' ),
			esc_url(
                add_query_arg(
	                array(
		                'paged' => $total_pages,
		                's'     => $search
	                ),
	                $current_url
                ),
            ),
			'&raquo;'
		);

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}

		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}

		$_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $_pagination;

	}


	/**
	 * @param $post_status
	 */
	function bulk_action( $post_status ) {
		if ( $post_status == 'trash' ) {
			$actions = array(
				'untrash'       => _x( 'Restore', 'Bulk Action', 'woonet' ),
				'delete'        => _x( 'Delete Permanently', 'Bulk Action', 'woonet' ),
				'wc-processing' => _x( 'Mark processing', 'Bulk Action', 'woonet' ),
				'wc-on-hold'    => _x( 'Mark on-hold', 'Bulk Action', 'woonet' ),
				'wc-completed'  => _x( 'Mark complete', 'Bulk Action', 'woonet' ),
			);
		} else {
			$actions = array(
				'trash'      => _x( 'Move to Trash', 'Bulk Action', 'woonet' ),
				'wc-processing' => _x( 'Mark processing', 'Bulk Action', 'woonet' ),
				'wc-on-hold'    => _x( 'Mark on-hold', 'Bulk Action', 'woonet' ),
				'wc-completed'  => _x( 'Mark complete', 'Bulk Action', 'woonet' ),
                'refund' => _x( 'Refund entire order', 'Bulk Action', 'woonet' ),
			);
		}

		$actions = apply_filters( 'WOO_MSTORE_ORDER/bulk_actions-edit-shop_order', $actions );
		?>
        <label class="screen-reader-text" for="bulk-action-selector-top"><?php _e( 'Select bulk action', 'woonet' ); ?></label>

        <select id="bulk-action-selector-top" name="action">
            <option selected="selected" value="-1"><?php _e( 'Bulk Actions', 'woonet' ); ?></option>
			<?php
			if ( ! empty( $actions ) ) {
				foreach ( $actions as $key => $value ) {
					?>
                    <option value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $value ); ?> </option>
					<?php
				}
			}
			?>
        </select>
        <input type="submit" value="Apply" class="button action" id="doaction" name="">
		<?php
	}

	/**
	 * @return array
	 */
	public function add_column_headers() {
		$column_headers = array(
			'order_blog'       => __( 'Store name', 'woonet' ),
			'order_title'      => __( 'Order', 'woocommerce' ),
			'order_date'       => __( 'Date', 'woocommerce' ),
			'order_status'     => __( 'Status', 'woocommerce' ),
			'billing_address'  => __( 'Billing', 'woocommerce' ),
			'shipping_address' => __( 'Ship to', 'woocommerce' ),
			'order_total'      => __( 'Total', 'woocommerce' ),
			'order_actions'    => __( 'Actions', 'woocommerce' ),
		);

		if ( ! empty( WOO_MULTISTORE()->settings['enable-order-import'] ) || WOO_MULTISTORE()->settings['enable-order-import'] == 'yes' ) {
			$column_headers['woonet-order-originating'] = __( 'Originating Site', 'Site where the order originated.' );
		}

		return $column_headers;
	}

}