<?php
/**
 * WooMultistore single site init
 *
 * @package WooMultistore
 * @since 3.0.0
 */

defined( 'ABSPATH' ) || exit;

class WC_Multistore_Setup_Wizard {

	public $is_complete = false;

	/**
	 * Initialize the action hooks and load the plugin classes
	 **/
	public function __construct() {
		$this->is_complete = get_site_option('wc_multistore_setup_wizard_complete') == 'yes';
		$this->hooks();

		if( is_multisite() ){
			$this->wc_multistore_migrate_settings_multisite_4_8_0();
		}else{
			$this->wc_multistore_migrate_settings_single_master_4_8_0();
			$this->wc_multistore_migrate_settings_single_child_4_8_0();
		}
	}

	public function hooks(){
		if( ! WOO_MULTISTORE()->permission ){ return; }

		if( is_multisite() ){
			add_action( 'network_admin_menu', array($this, 'add_network_submenu') );
			add_action( 'network_admin_notices', array( $this, 'network_admin_notices_5' ) );
		}else{
			add_action( 'admin_menu', array( $this, 'add_submenu' ) );
			add_action( 'admin_notices', array( $this, 'network_admin_notices_5' ) );
		}

		add_action( 'admin_head', array( $this, 'remove_setup_wizard_from_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function add_network_submenu() {
		add_submenu_page('woonet-woocommerce','Setup Wizard',	'Setup Wizard','manage_woocommerce','woonet-woocommerce', array( $this, 'sub_menu_callback' ),1);


		$elect_master_store_hookname = add_submenu_page('woonet-woocommerce','Select Master Store','Select Master Store','manage_options','woonet-master-store', array( $this, 'menu_callback_select_master_store' ) );
		add_action( 'load-' . $elect_master_store_hookname, array( $this, 'select_master_store_form_submit' ) );

		$enter_license_key_hookname = add_submenu_page('woonet-woocommerce','Enter License Key','Enter License Key','manage_options','woonet-license-key', array( $this, 'menu_callback_enter_license_key' )	);
		add_action( 'load-' . $enter_license_key_hookname, array( $this, 'enter_license_key_form_submit' ) );
	}

	public function add_submenu() {
		if( get_option('wc_multistore_network_type') == 'child' ){
			add_submenu_page('woonet-woocommerce','Setup Wizard',	'Setup Wizard','manage_woocommerce','woonet-woocommerce', array( $this, 'sub_menu_callback' ),1);
		}else{
			add_submenu_page('woonet-woocommerce','Setup Wizard',	'Setup Wizard','manage_woocommerce','woonet-woocommerce',array( $this, 'sub_menu_callback' ),1	);
		}

		$select_network_type_hookname = add_submenu_page('woonet-woocommerce','Select Network Type','Select Network Type','manage_woocommerce','woonet-network-type', array( $this, 'menu_callback_select_network_type' ) );
		add_action( 'load-' . $select_network_type_hookname, array( $this, 'select_network_type_form_submit' ) );

		$enter_license_key_hookname = add_submenu_page('woonet-woocommerce','Enter License Key','Enter License Key','manage_woocommerce','woonet-license-key', array( $this, 'menu_callback_enter_license_key' )	);
		add_action( 'load-' . $enter_license_key_hookname, array( $this, 'enter_license_key_form_submit' ) );

		$add_site_hookname = add_submenu_page('woonet-woocommerce','Add a Site','Add a Site','manage_options','woonet-connect-child', array( $this, 'menu_callback_connect_child' )	);

		$connect_to_master_hookname = add_submenu_page('woonet-woocommerce','Connect to Master','Connect to Master','manage_woocommerce',	'woonet-connect-master', array( $this, 'menu_callback_connect_master' ) );
		add_action( 'load-' . $connect_to_master_hookname, array( $this, 'woonet_save_master_site' ) );

	}

	public function sub_menu_callback() {
		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-setup-wizard.php';
	}

	public function menu_callback_select_network_type() {
		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-setup-wizard-select-network-type.php';
	}

	public function menu_callback_select_master_store() {
		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-setup-wizard-select-master-store.php';
	}

	public function menu_callback_enter_license_key() {
		require_once WOO_MSTORE_PATH . 'includes/admin/views/html-setup-wizard-license-key.php';
	}

	public function menu_callback_connect_child() {
		require_once WOO_MSTORE_SINGLE_INCLUDES_PATH . 'admin/views/html-setup-wizard-connect-child-sites.php';
	}

	public function menu_callback_connect_master() {
		require_once WOO_MSTORE_SINGLE_INCLUDES_PATH . 'admin/views/html-setup-wizard-connect-master-site.php';
	}

	public function remove_setup_wizard_from_menu() {
		remove_submenu_page( 'woonet-woocommerce', 'woonet-license-key' );
		remove_submenu_page( 'woonet-woocommerce', 'woonet-master-store' );
		remove_submenu_page( 'woonet-woocommerce', 'woonet-network-type' );
		remove_submenu_page( 'woonet-woocommerce', 'woonet-connect-child' );
		remove_submenu_page( 'woonet-woocommerce', 'woonet-connect-master' );
	}

	public function select_master_store_form_submit() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woonet_select_master_store' ) ) {
			wp_die( 'Nope! You are not allowed to pefrom this action.' );
		}

		$_SESSION['mstore_form_submit_messages'] = array();

		if ( ! empty( $_POST['wc_multistore_master_store'] ) ) {
			update_site_option( 'wc_multistore_master_store', strip_tags( $_POST['wc_multistore_master_store'] ) );
			if( WOO_MULTISTORE()->license->is_active() ){
				update_site_option('wc_multistore_setup_wizard_complete','yes' );
			}
			// redirect
			wp_redirect( network_admin_url( 'admin.php?page=woonet-woocommerce' ) );
			die();
		} else {
			$_SESSION['mstore_form_submit_messages'][] = __( 'Please select a master store.', 'woonet' );
		}
	}

	public function woonet_save_master_site() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woonet_save_master_site' ) ) {
			wp_die( 'Nope! You are not allowed to perform this action.' );
		}

		if ( ! empty( $_REQUEST['submit'] ) && $_REQUEST['submit'] == 'save' ) {
			$master_url = $_REQUEST['wc_multistore_master_url'];

			$master_data = get_site_option('wc_multistore_master_connect');
			$master_data['master_url'] = 'https://'. rtrim($master_url, '/');
			if( ! empty( $master_url ) ){
				update_site_option('wc_multistore_master_connect', $master_data);
				$_SESSION['mstore_form_submit_success_messages'][] = 'Master Site saved successfully.';
			}else{
				$_SESSION['mstore_form_submit_messages'][] = 'Master Site Url cannot bet empty.';
			}

		}
	}

	public function enter_license_key_form_submit() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woonet_license_verify_submit' )	) {
			wp_die( 'Nope! You are not allowed to pefrom this action.' );
		}

		$_SESSION['mstore_form_submit_messages'] = array();

		if ( ! empty( $_REQUEST['woonet_license_key_remove'] ) ) {
			WOO_MULTISTORE()->license->deactivate();
			if( empty( WOO_MULTISTORE()->license->errors ) ){
				delete_site_option('wc_multistore_setup_wizard_complete');
				wp_redirect( network_admin_url( 'admin.php?page=woonet-woocommerce' ) );
				die();
			}
		}

		if ( isset( $_POST['woonet_license_key'] ) ) {

			$license_key = isset( $_POST['woonet_license_key'] ) ? sanitize_key( trim( $_POST['woonet_license_key'] ) ) : '';

			if ( $license_key == '' ) {
				$_SESSION['mstore_form_submit_messages'][] = __( "Licence key can't be empty", 'woonet' );
				return;
			}

			WOO_MULTISTORE()->license->activate($_POST['woonet_license_key']);


			if( empty(WOO_MULTISTORE()->license->errors) ){
				update_site_option('wc_multistore_setup_wizard_complete','yes' );
				wp_redirect( network_admin_url( 'admin.php?page=woonet-woocommerce' ) );
				die();
			}
		}
	}



	// $_POST
	public function select_network_type_form_submit() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woonet_select_network_type' ) ) {
			wp_die( 'Nope! You are not allowed to perform this action.' );
		}

		$_SESSION['mstore_form_submit_messages'] = array();

		if ( ! empty( $_POST['wc_multistore_network_type'] ) ) {
			update_option( 'wc_multistore_network_type', strip_tags( $_POST['wc_multistore_network_type'] ) );
			// redirect
			wp_redirect( admin_url( 'admin.php?page=woonet-woocommerce', 'relative' ) );
			die();
		} else {
			$_SESSION['mstore_form_submit_messages'][] = __( 'Please select a network type.', 'woonet' );
		}
	}

	public function enqueue_assets() {
		if ( is_admin() ) {
			wp_register_style( 'woomulti-single-css', WOO_MSTORE_URL . '/assets/css/wc-multistore-main.css', array(), WOO_MSTORE_VERSION );
			wp_enqueue_style( 'woomulti-single-css' );

			wp_register_script( 'woomulti-single-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-main.js', array(), WOO_MSTORE_VERSION );
			wp_enqueue_script( 'woomulti-single-js' );
		}
	}

	public function network_admin_notices_5() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			if( is_multisite()  && ! $this->is_complete ){
				include WOO_MSTORE_PATH . 'includes/admin/views/html-notice-update-5.php';
			}

			if( ! is_multisite() && ! WOO_MULTISTORE()->data->is_up_to_date ){
				include WOO_MSTORE_PATH . 'includes/admin/views/html-notice-update-5.php';
			}

		}
	}


	public function wc_multistore_migrate_settings_multisite_4_8_0(){
		$old_settings = get_site_option('mstore_options');
		$mstore_license = get_site_option('mstore_license');
		$woonet_settings_custom_metadata = get_site_option('woonet_settings_custom_metadata');
		$woonet_settings_custom_taxonomy = get_site_option('woonet_settings_custom_taxonomy');
		$mstore_orders_export_options = get_site_option('mstore_orders_export_options');
		$mstore_current_network_order_number = get_site_option('mstore_current_network_order_number');


		$new_settings = array();
		$new_site_settings = array();
		if( ! empty( $old_settings ) ){
			$new_settings['version'] = $old_settings['version'];
			$new_settings['db_version'] = $old_settings['db_version'];
			$new_settings['synchronize-by-default'] = $old_settings['synchronize-by-default'];
			$new_settings['synchronize-rest-by-default'] = $old_settings['synchronize-rest-by-default'];
			$new_settings['inherit-by-default'] = $old_settings['inherit-by-default'];
			$new_settings['inherit-rest-by-default'] = $old_settings['inherit-rest-by-default'];
			$new_settings['synchronize-stock'] = $old_settings['synchronize-stock'];
			$new_settings['synchronize-trash'] = $old_settings['synchronize-trash'];
			$new_settings['sequential-order-numbers'] = $old_settings['sequential-order-numbers'];
			$new_settings['publish-capability'] = $old_settings['publish-capability'];
			$new_settings['network-user-info'] = $old_settings['network-user-info'];
			$new_settings['sync-coupons'] = $old_settings['sync-coupons'];
			$new_settings['disable-ajax-sync'] = $old_settings['disable-ajax-sync'];
			$new_settings['background-sync'] = $old_settings['background-sync'];
			$new_settings['sync-custom-metadata'] = $old_settings['sync-custom-metadata'];
			$new_settings['sync-custom-taxonomy'] = $old_settings['sync-custom-taxonomy'];
			$new_settings['sync-by-sku'] = $old_settings['sync-by-sku'];
			$new_settings['enable-global-image'] = $old_settings['enable-global-image'];
			$new_settings['global-image-master'] = $old_settings['global-image-master'];
			$new_settings['order-import-to'] = $old_settings['order-import-to'];
			$new_settings['enable-order-import'] = $old_settings['enable-order-import'];

			$get_sites_args = array(
				'number'   => 999,
				'fields'   => 'ids',
				'archived' => 0,
				'spam'     => 0,
				'deleted'  => 0,
			);
			$sites = get_sites( $get_sites_args );
			$new_sites = array();
			foreach ( $sites as $site ){
				if(!isset($old_settings['child_inherit_changes_fields_control__title'][$site])){
					continue;
				}
				$new_site_settings['child_inherit_changes_fields_control__title'] = $old_settings['child_inherit_changes_fields_control__title'][$site];
				$new_site_settings['child_inherit_changes_fields_control__description'] = $old_settings['child_inherit_changes_fields_control__description'][$site];
				$new_site_settings['child_inherit_changes_fields_control__short_description'] = $old_settings['child_inherit_changes_fields_control__short_description'][$site];
				$new_site_settings['child_inherit_changes_fields_control__price'] = $old_settings['child_inherit_changes_fields_control__price'][$site];
				$new_site_settings['child_inherit_changes_fields_control__product_tag'] = $old_settings['child_inherit_changes_fields_control__product_tag'][$site];
				$new_site_settings['child_inherit_changes_fields_control__attributes'] = $old_settings['child_inherit_changes_fields_control__attributes'][$site];
				$new_site_settings['child_inherit_changes_fields_control__attribute_name'] = $old_settings['child_inherit_changes_fields_control__attribute_name'][$site];
				$new_site_settings['child_inherit_changes_fields_control__default_variations'] = $old_settings['child_inherit_changes_fields_control__default_variations'][$site];
				$new_site_settings['child_inherit_changes_fields_control__reviews'] = $old_settings['child_inherit_changes_fields_control__reviews'][$site];
				$new_site_settings['child_inherit_changes_fields_control__slug'] = $old_settings['child_inherit_changes_fields_control__slug'][$site];
				$new_site_settings['child_inherit_changes_fields_control__purchase_note'] = $old_settings['child_inherit_changes_fields_control__purchase_note'][$site];
				$new_site_settings['child_inherit_changes_fields_control__status'] = $old_settings['child_inherit_changes_fields_control__status'][$site];
				$new_site_settings['child_inherit_changes_fields_control__featured'] = $old_settings['child_inherit_changes_fields_control__featured'][$site];
				$new_site_settings['child_inherit_changes_fields_control__catalogue_visibility'] = $old_settings['child_inherit_changes_fields_control__catalogue_visibility'][$site];
				$new_site_settings['child_inherit_changes_fields_control__sale_price'] = $old_settings['child_inherit_changes_fields_control__sale_price'][$site];
				$new_site_settings['child_inherit_changes_fields_control__sku'] = $old_settings['child_inherit_changes_fields_control__sku'][$site];
				$new_site_settings['child_inherit_changes_fields_control__product_image'] = $old_settings['child_inherit_changes_fields_control__product_image'][$site];
				$new_site_settings['child_inherit_changes_fields_control__product_gallery'] = $old_settings['child_inherit_changes_fields_control__product_gallery'][$site];
				$new_site_settings['child_inherit_changes_fields_control__allow_backorders'] = $old_settings['child_inherit_changes_fields_control__allow_backorders'][$site];
				$new_site_settings['child_inherit_changes_fields_control__menu_order'] = $old_settings['child_inherit_changes_fields_control__menu_order'][$site];
				$new_site_settings['child_inherit_changes_fields_control__shipping_class'] = $old_settings['child_inherit_changes_fields_control__shipping_class'][$site];
				$new_site_settings['child_inherit_changes_fields_control__upsell'] = $old_settings['child_inherit_changes_fields_control__upsell'][$site];
				$new_site_settings['child_inherit_changes_fields_control__cross_sells'] = $old_settings['child_inherit_changes_fields_control__cross_sells'][$site];
				$new_site_settings['child_inherit_changes_fields_control__variations'] = $old_settings['child_inherit_changes_fields_control__variations'][$site];
				$new_site_settings['child_inherit_changes_fields_control__variations_data'] = $old_settings['child_inherit_changes_fields_control__variations_data'][$site];
				$new_site_settings['child_inherit_changes_fields_control__variations_sku'] = $old_settings['child_inherit_changes_fields_control__variations_sku'][$site];
				$new_site_settings['child_inherit_changes_fields_control__variations_status'] = $old_settings['child_inherit_changes_fields_control__variations_status'][$site];
				$new_site_settings['child_inherit_changes_fields_control__variations_stock'] = $old_settings['child_inherit_changes_fields_control__variations_stock'][$site];
				$new_site_settings['child_inherit_changes_fields_control__variations_price'] = $old_settings['child_inherit_changes_fields_control__variations_price'][$site];
				$new_site_settings['child_inherit_changes_fields_control__variations_sale_price'] = $old_settings['child_inherit_changes_fields_control__variations_sale_price'][$site];
				$new_site_settings['child_inherit_changes_fields_control__product_cat'] = $old_settings['child_inherit_changes_fields_control__product_cat'][$site];
				$new_site_settings['child_inherit_changes_fields_control__category_changes'] = $old_settings['child_inherit_changes_fields_control__category_changes'][$site];
				$new_site_settings['child_inherit_changes_fields_control__category_meta'] = $old_settings['child_inherit_changes_fields_control__category_meta'][$site];
				$new_site_settings['child_inherit_changes_fields_control__synchronize_rest_by_default'] = $old_settings['child_inherit_changes_fields_control__synchronize_rest_by_default'][$site];
				$new_site_settings['child_inherit_changes_fields_control__import_order'] = $old_settings['child_inherit_changes_fields_control__import_order'][$site];
				$new_site_settings['override__synchronize-stock'] = $old_settings['override__synchronize-stock'][$site];

				switch_to_blog( $site );
				update_option('wc_multistore_settings', $new_settings );
				$new_site = array(
					'id' => $site,
					'name' => get_bloginfo('name'),
					'url'  => get_bloginfo('url'),
					'settings'  => $new_site_settings,
				);
				update_option('wc_multistore_site', $new_site );

				$new_sites[$site] = $new_site;
				restore_current_blog();

			}

			if(!empty($woonet_settings_custom_metadata)){

			}


			update_site_option('wc_multistore_settings', $new_settings );
			update_site_option('wc_multistore_license', $mstore_license );
			update_site_option('wc_multistore_sites', $new_sites );
			update_site_option('wc_multistore_custom_metadata', $woonet_settings_custom_metadata );
			update_site_option('wc_multistore_custom_taxonomy', $woonet_settings_custom_taxonomy );
			update_site_option('wc_multistore_sequential_order_number', $mstore_current_network_order_number );
			if( !empty($mstore_orders_export_options) ){
				update_site_option('wc_multistore_orders_export_options', $mstore_orders_export_options );
			}

			delete_site_option('mstore_options' );
			delete_site_option('mstore_license' );
			delete_site_option('woonet_settings_custom_metadata' );
			delete_site_option('woonet_settings_custom_taxonomy' );
			delete_site_option('woonet_sequential_order_number' );
			delete_site_option('woonet_setup_wizard_complete' );
			delete_site_option('mstore_setup_wizard_completed' );
			delete_site_option('mstore_orders_export_options' );
			delete_site_option('mstore_current_network_order_number' );
		}
	}

	public function wc_multistore_migrate_settings_single_master_4_8_0(){
		$old_settings = get_site_option('woonet_options');
		$mstore_license = get_site_option('mstore_license');
		$network_type = get_site_option('woonet_network_type');
		$custom_meta = get_site_option('woonet_settings_custom_metadata');
		$custom_tax = get_site_option('woonet_settings_custom_taxonomy');
		$sequential_order_number = get_site_option('woonet_sequential_order_number');
		$woonet_setup_wizard_complete = get_site_option('woonet_setup_wizard_complete');
		$woonet_child_sites_deactivated = get_site_option('woonet_child_sites_deactivated');
		$mstore_orders_export_options = get_site_option('mstore_orders_export_options');
		$new_settings = array();

		if( empty( $network_type ) || $network_type != 'master' ){
			return;
		}

		if( ! empty( $old_settings ) ){
			$new_settings['version'] = '4.7.9';
			$new_settings['db_version'] = '4.7.9';
			$new_settings['synchronize-by-default'] = $old_settings['synchronize-by-default'];
			$new_settings['synchronize-rest-by-default'] = $old_settings['synchronize-rest-by-default'];
			$new_settings['inherit-by-default'] = $old_settings['inherit-by-default'];
			$new_settings['inherit-rest-by-default'] = $old_settings['inherit-rest-by-default'];
			$new_settings['synchronize-stock'] = $old_settings['synchronize-stock'];
			$new_settings['synchronize-trash'] = $old_settings['synchronize-trash'];
			$new_settings['sequential-order-numbers'] = $old_settings['sequential-order-numbers'];
			$new_settings['publish-capability'] = $old_settings['publish-capability'];
			$new_settings['sync-coupons'] = $old_settings['enable-coupon-sync'];
			$new_settings['disable-ajax-sync'] = $old_settings['disable-ajax-sync'];
			$new_settings['background-sync'] = $old_settings['background-sync'];
			$new_settings['sync-custom-metadata'] = $old_settings['sync-custom-metadata'];
			$new_settings['sync-custom-taxonomy'] = $old_settings['sync-custom-taxonomy'];
			$new_settings['sync-by-sku'] = $old_settings['sync-by-sku'];
			$new_settings['enable-global-image'] = $old_settings['enable-global-image'];
			$new_settings['enable-order-import'] = $old_settings['enable-order-import'];


			$sites = get_site_option('woonet_child_sites');
			$new_sites = array();
			$new_sites2 = array();
			foreach ( $sites as $key => $site ){
				$new_sites[$key]['id'] = $key;
				$new_sites[$key]['site_url'] = $site['site_url'];
				$new_sites[$key]['url'] = $site['site_url'];
				$new_sites[$key]['type'] = 'child';
				$new_sites[$key]['date_added'] = $site['date_added'];
				if( ! empty( $woonet_child_sites_deactivated ) && in_array( $key, $woonet_child_sites_deactivated ) ){
					$new_sites[$key]['is_active'] = 'no';
				}else{
					$new_sites[$key]['is_active'] = 'yes';
				}
				$new_sites2[$key]['uuid'] = $site['uuid'];
			}

			update_site_option('wc_multistore_settings', $new_settings );
			update_site_option('wc_multistore_sites', $new_sites );
			update_site_option('wc_multistore_sites2', $new_sites2 );
			update_site_option('wc_multistore_network_type', $network_type );
			update_site_option('wc_multistore_custom_metadata', $custom_meta );
			update_site_option('wc_multistore_custom_taxonomy', $custom_tax );
			update_site_option('wc_multistore_sequential_order_number', $sequential_order_number );
			update_site_option('wc_multistore_setup_wizard_complete', $woonet_setup_wizard_complete );
			update_site_option('wc_multistore_child_sites_deactivated', $woonet_child_sites_deactivated );
			update_site_option('wc_multistore_license', $mstore_license );

			if( !empty($mstore_orders_export_options) ){
				update_site_option('wc_multistore_orders_export_options', $mstore_orders_export_options );
			}

			delete_site_option('woonet_options' );
			delete_site_option('woonet_child_sites');
			delete_site_option('woonet_network_type');
			delete_site_option('woonet_settings_custom_metadata');
			delete_site_option('woonet_settings_custom_taxonomy');
			delete_site_option('woonet_sequential_order_number');
			delete_site_option('woonet_setup_wizard_complete');
			delete_site_option('woonet_child_sites_deactivated');
			delete_site_option('mstore_license');
			delete_site_option('mstore_orders_export_options');
		}
	}

	public function wc_multistore_migrate_settings_single_child_4_8_0(){
		$old_settings = get_site_option('woonet_options');
		$network_type = get_site_option('woonet_network_type');
		$custom_meta = get_site_option('woonet_settings_custom_metadata');
		$custom_tax = get_site_option('woonet_settings_custom_taxonomy');
		$sequential_order_number = get_site_option('woonet_sequential_order_number');
		$woonet_setup_wizard_complete = get_site_option('woonet_setup_wizard_complete');
		$woonet_master_connect = get_site_option('woonet_master_connect');
		$new_site = array();
		$new_settings = array();

		if( empty( $network_type ) || $network_type != 'child' ){
			return;
		}

		if( ! empty( $old_settings ) ){
			$new_settings['version'] = '4.7.9';
			$new_settings['db_version'] = '4.7.9';
			$new_settings['synchronize-by-default'] = $old_settings['synchronize-by-default'];
			$new_settings['synchronize-rest-by-default'] = $old_settings['synchronize-rest-by-default'];
			$new_settings['inherit-by-default'] = $old_settings['inherit-by-default'];
			$new_settings['inherit-rest-by-default'] = $old_settings['inherit-rest-by-default'];
			$new_settings['synchronize-stock'] = $old_settings['synchronize-stock'];
			$new_settings['synchronize-trash'] = $old_settings['synchronize-trash'];
			$new_settings['sequential-order-numbers'] = $old_settings['sequential-order-numbers'];
			$new_settings['publish-capability'] = $old_settings['publish-capability'];
			$new_settings['sync-coupons'] = $old_settings['enable-coupon-sync'];
			$new_settings['disable-ajax-sync'] = $old_settings['disable-ajax-sync'];
			$new_settings['background-sync'] = $old_settings['background-sync'];
			$new_settings['sync-custom-metadata'] = $old_settings['sync-custom-metadata'];
			$new_settings['sync-custom-taxonomy'] = $old_settings['sync-custom-taxonomy'];
			$new_settings['sync-by-sku'] = $old_settings['sync-by-sku'];
			$new_settings['enable-global-image'] = $old_settings['enable-global-image'];
			$new_settings['enable-order-import'] = $old_settings['enable-order-import'];

			$new_site_settings['child_inherit_changes_fields_control__title'] = $old_settings['child_inherit_changes_fields_control__title'];
			$new_site_settings['child_inherit_changes_fields_control__description'] = $old_settings['child_inherit_changes_fields_control__description'];
			$new_site_settings['child_inherit_changes_fields_control__short_description'] = $old_settings['child_inherit_changes_fields_control__short_description'];
			$new_site_settings['child_inherit_changes_fields_control__price'] = $old_settings['child_inherit_changes_fields_control__price'];
			$new_site_settings['child_inherit_changes_fields_control__product_tag'] = $old_settings['child_inherit_changes_fields_control__product_tag'];
			$new_site_settings['child_inherit_changes_fields_control__attributes'] = $old_settings['child_inherit_changes_fields_control__attributes'];
			$new_site_settings['child_inherit_changes_fields_control__attribute_name'] = $old_settings['child_inherit_changes_fields_control__attribute_name'];
			$new_site_settings['child_inherit_changes_fields_control__default_variations'] = $old_settings['child_inherit_changes_fields_control__default_variations'];
			$new_site_settings['child_inherit_changes_fields_control__reviews'] = $old_settings['child_inherit_changes_fields_control__reviews'];
			$new_site_settings['child_inherit_changes_fields_control__slug'] = $old_settings['child_inherit_changes_fields_control__slug'];
			$new_site_settings['child_inherit_changes_fields_control__purchase_note'] = $old_settings['child_inherit_changes_fields_control__purchase_note'];
			$new_site_settings['child_inherit_changes_fields_control__status'] = $old_settings['child_inherit_changes_fields_control__status'];
			$new_site_settings['child_inherit_changes_fields_control__featured'] = $old_settings['child_inherit_changes_fields_control__featured'];
			$new_site_settings['child_inherit_changes_fields_control__catalogue_visibility'] = $old_settings['child_inherit_changes_fields_control__catalogue_visibility'];
			$new_site_settings['child_inherit_changes_fields_control__sale_price'] = $old_settings['child_inherit_changes_fields_control__sale_price'];
			$new_site_settings['child_inherit_changes_fields_control__sku'] = $old_settings['child_inherit_changes_fields_control__sku'];
			$new_site_settings['child_inherit_changes_fields_control__product_image'] = $old_settings['child_inherit_changes_fields_control__product_image'];
			$new_site_settings['child_inherit_changes_fields_control__product_gallery'] = $old_settings['child_inherit_changes_fields_control__product_gallery'];
			$new_site_settings['child_inherit_changes_fields_control__allow_backorders'] = $old_settings['child_inherit_changes_fields_control__allow_backorders'];
			$new_site_settings['child_inherit_changes_fields_control__menu_order'] = $old_settings['child_inherit_changes_fields_control__menu_order'];
			$new_site_settings['child_inherit_changes_fields_control__shipping_class'] = $old_settings['child_inherit_changes_fields_control__shipping_class'];
			$new_site_settings['child_inherit_changes_fields_control__upsell'] = $old_settings['child_inherit_changes_fields_control__upsell'];
			$new_site_settings['child_inherit_changes_fields_control__cross_sells'] = $old_settings['child_inherit_changes_fields_control__cross_sells'];
			$new_site_settings['child_inherit_changes_fields_control__variations'] = $old_settings['child_inherit_changes_fields_control__variations'];
			$new_site_settings['child_inherit_changes_fields_control__variations_data'] = $old_settings['child_inherit_changes_fields_control__variations_data'];
			$new_site_settings['child_inherit_changes_fields_control__variations_sku'] = $old_settings['child_inherit_changes_fields_control__variations_sku'];
			$new_site_settings['child_inherit_changes_fields_control__variations_status'] = $old_settings['child_inherit_changes_fields_control__variations_status'];
			$new_site_settings['child_inherit_changes_fields_control__variations_stock'] = $old_settings['child_inherit_changes_fields_control__variations_stock'];
			$new_site_settings['child_inherit_changes_fields_control__variations_price'] = $old_settings['child_inherit_changes_fields_control__variations_price'];
			$new_site_settings['child_inherit_changes_fields_control__variations_sale_price'] = $old_settings['child_inherit_changes_fields_control__variations_sale_price'];
			$new_site_settings['child_inherit_changes_fields_control__product_cat'] = $old_settings['child_inherit_changes_fields_control__product_cat'];
			$new_site_settings['child_inherit_changes_fields_control__category_changes'] = $old_settings['child_inherit_changes_fields_control__category_changes'];
			$new_site_settings['child_inherit_changes_fields_control__category_meta'] = $old_settings['child_inherit_changes_fields_control__category_meta'];
			$new_site_settings['child_inherit_changes_fields_control__synchronize_rest_by_default'] = $old_settings['child_inherit_changes_fields_control__synchronize_rest_by_default'];
			$new_site_settings['child_inherit_changes_fields_control__import_order'] = $old_settings['child_inherit_changes_fields_control__import_order'];
			$new_site_settings['override__synchronize-stock'] = $old_settings['override__synchronize-stock'];
			$new_site['settings'] = $new_site_settings;

			update_site_option('wc_multistore_settings', $new_settings );
			update_site_option('wc_multistore_site', $new_site );
			update_site_option('wc_multistore_network_type', $network_type );
			update_site_option('wc_multistore_custom_metadata', $custom_meta );
			update_site_option('wc_multistore_custom_taxonomy', $custom_tax );
			update_site_option('wc_multistore_sequential_order_number', $sequential_order_number );
			update_site_option('wc_multistore_setup_wizard_complete', $woonet_setup_wizard_complete );

			if( ! empty( $woonet_master_connect ) ){
				$woonet_master_connect['id'] = $woonet_master_connect['key'];
				update_site_option('wc_multistore_master_connect2', $woonet_master_connect);
				unset($woonet_master_connect['uuid']);
			}
			update_site_option('wc_multistore_master_connect', $woonet_master_connect );

			delete_site_option('woonet_options' );
			delete_site_option('woonet_network_type');
			delete_site_option('woonet_settings_custom_metadata');
			delete_site_option('woonet_settings_custom_taxonomy');
			delete_site_option('woonet_sequential_order_number');
			delete_site_option('woonet_setup_wizard_complete');
			delete_site_option('woonet_master_connect');
		}
	}
}