<?php
/**
 * Settings handler.
 *
 * This handles settings functionality.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Multistore_Settings
 */
class WC_Multistore_Settings{

	/**
	 * @var array|object
	 */
	private $settings;

	/**
	 * @var
	 */
	private $sites;

	/**
	 * @var
	 */
	private $messages;

	/**
	 * @var array
	 */
	private $defaults = array(
        'version'                                                               => WOO_MSTORE_VERSION,
        'db_version'                                                            => WOO_MSTORE_VERSION,
        'synchronize-by-default'                                                => 'no',
        'synchronize-rest-by-default'                                           => 'no',
        'inherit-by-default'                                  	                => 'no',
        'inherit-rest-by-default'                                               => 'no',
        'synchronize-stock'                                                     => 'no',
        'synchronize-trash'                                                     => 'no',
        'sequential-order-numbers'                                              => 'no',
        'network-user-info'                                                     => 'no',
        'sync-coupons'											                => 'no',
        'sync-custom-metadata'									                => 'no',
        'sync-custom-taxonomy'						                            => 'no',
        'sync-by-sku'										                    => 'no',
        'enable-global-image'									                => 'no',
        'global-image-master'									                =>  0,
        'enable-order-import'									                => 'no',
        'disable-ajax-sync'										                => 'no',
        'background-sync'										                => 'no',
        'sync-method'										                    => 'ajax',
        'publish-capability'                                                    => 'administrator',
	);

	/**
	 *
	 */
	function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
        $this->sites = WOO_MULTISTORE()->active_sites;
        $this->settings = $this->set_settings();
		$this->hooks();
	}

	/**
	 * @return array|object
	 */
	private function set_settings(){
	    $settings = get_site_option('wc_multistore_settings', array() );

	    return wp_parse_args( $settings, $this->defaults );
    }

	/**
	 * @return array|object
	 */
	public function get_settings(){
		return $this->settings;
	}

	/**
	 *
	 */
	public function update(){
		if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'wc-multistore-settings' ) {
			return;
		}

		if ( ! isset( $_POST['wc_multistore_form_submit'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['mstore_form_nonce'], 'mstore_form_submit' ) ) {
			return;
		}

		$this->save( $_POST );
	}

	/**
	 * @param $args
	 */
	public function save( $args ){
		$args = apply_filters( 'woo_mstore/options/options_save', $args );

		if( ! empty( $args['sites'] ) ){
			$sites = $args['sites'];
			unset($args['sites']);
		}

		foreach ( $args as $key => $value ) {
			if ( is_array( $value ) ) {
                foreach ( $value as $next_key => $next_value ){
                    if( is_array( $next_value ) ){
	                    $value[$next_key] = array_map('strip_tags', $next_value );
                    }else{
	                    $value[$next_key] = strip_tags( $next_value );
                    }
                }
				$args[ $key ] = $value;
			} else {
				$args[ $key ] = strip_tags( $value );
			}
		}

		$args = wp_parse_args( $args, $this->defaults );
		if( is_multisite() ){
			update_site_option('wc_multistore_settings', $args );
		}else{
			update_option('wc_multistore_settings', $args );

			if( WOO_MULTISTORE()->site->get_type() == 'master' && ! empty($sites) ){

				$settings = $args;
				$wc_multistore_settings_api = new WC_Multistore_Settings_Api_Master();
				foreach ($this->sites as $site){
					$site_settings = $sites[$site->get_id()];
					$settings['site_settings'] = $site_settings;
					$result = $wc_multistore_settings_api->send_settings_to_child( $site, $settings );
					$this->messages[] = $result['message'];
				}
			}
		}


		$this->settings = $args;
		$this->messages[] = __( 'Settings Saved', 'woonet' );

		do_action('wc_multistore_settings_saved', $args );
	}

	/**
	 *
	 */
	function admin_notices() {
		if ( $this->messages == '' ) {
			return;
		}

		if ( count( $this->messages ) > 0 ) {
			echo "<div id='notice' class='updated fade'><p>" . implode( '</p><p>', $this->messages ) . '</p></div>';
		}
	}

	/**
	 *
	 */
	public function admin_print_styles() {
		wp_enqueue_style( 'jquery-ui-css', WOO_MSTORE_URL . '/assets/css/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui-accordion', WOO_MSTORE_URL . '/assets/css/jquery-ui-accordion.css' );
		wp_enqueue_style( 'wc-multistore-settings', WOO_MSTORE_URL . '/assets/css/wc-multistore-settings.css' );
	}

	/**
	 *
	 */
	public function admin_print_scripts() {
		wp_enqueue_script('jquery-tiptip');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('wc-multistore-settings',WOO_MSTORE_URL . '/assets/js/wc-multistore-settings.js', array('jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-tiptip' ),WOO_MSTORE_VERSION,true );
	}

	/**
	 *
	 */
	public function hooks(){
		if( ! WOO_MULTISTORE()->permission ){ return; }

		if( is_multisite() ){
			add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ) , 14 );
		}else{
			if( WOO_MULTISTORE()->site->get_type() == 'master' ){
				add_action( 'admin_menu', array( $this, 'network_admin_menu' ) , 14 );
			}else{
				add_action( 'admin_menu', array( $this, 'child_admin_menu' ) , 14 );
			}
		}
		add_action( 'admin_init', array( $this, 'update' ) , 14 );
	}

	/**
	 *
	 */
	function network_admin_menu() {
		$hookID = add_submenu_page( 'woonet-woocommerce', 'Settings ', 'Settings ', 'manage_woocommerce', 'wc-multistore-settings', array(	$this, 'output' ) );

		add_action( 'load-' . $hookID, array( $this, 'admin_notices' ) );
		add_action( 'admin_print_styles-' . $hookID, array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $hookID, array( $this, 'admin_print_scripts' ) );
	}

	public function child_admin_menu() {
		$hookID = add_submenu_page('woonet-woocommerce','Settings (Main site)','Settings (Main site)','manage_woocommerce',	'woonet-woocommerce-settings', 	array( $this, 'redirect_child_settings' ) );
		add_action( 'load-' . $hookID, array( $this, 'redirect_child_settings' ) );
	}

	public function redirect_child_settings() {
		$master_data = get_site_option('wc_multistore_master_connect');
		if( !empty($master_data) ){
			$this->admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
			$this->child_site = WOO_MULTISTORE()->site;
		}

		if ( ! empty( $master_data['master_url'] ) ) {
			wp_redirect( esc_url( $master_data['master_url'] . '/wp-admin/admin.php?page=wc-multistore-settings' ) );
			die();
		}
	}

	/**
	 *
	 */
	public function output() {
		require_once( WOO_MSTORE_SINGLE_INCLUDES_PATH . 'admin/views/html-settings.php' );
	}
}