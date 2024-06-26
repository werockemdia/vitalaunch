<?php
/**
 * WooMultistore
 *
 * @package     WooMultistore
 * @author      Lykke Media AS
 * @copyright   2021 Lykke Media AS
 *
 * @wordpress-plugin
 * Plugin Name: WooMultistore
 * Description: WooMultistore plugin can be used to manage features on unlimited WooCommerce stores from one single WordPress admin.
 * Author: Lykke Media AS
 * Author URI: https://woomultistore.com/
 * Version: 5.2.4
 * Requires at least: 5.3.0
 * Tested up to: 6.3.2
 * Requires PHP: 7.4
 *
 * WC requires at least: 3.6.0
 * WC tested up to: 8.2.1
 * Network: true
 **/

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WC_MULTISTORE_FILE' ) ) {
	define( 'WC_MULTISTORE_FILE', __FILE__ );
}


defined( 'ABSPATH' ) || exit;

/**
 * Final Class WOO_MSTORE_MULTI_INIT
 **/
final class WOO_MSTORE_MULTI_INIT {
	/**
	 * Permission
	 **/
	public $permission = false;

	/**
	 * Settings
	 **/
	public $settings = null;

	/**
	 * Site
	 **/
	public $site = null;

	/**
	 * Sites
	 **/
	public $sites = null;

	/**
	 * Active Sites
	 **/
	public $active_sites = null;

	/**
	 * License
	 **/
	public $license = null;

	/**
	 * Setup
	 **/
	public $setup = null;

	/**
	 * Data
	 **/
	public $data = null;

	/**
	 * Request
	 **/
	public $request = null;

	/**
	 * Product
	 */
	public $product = null;

	/**
	 * Image
	 **/
	public $image = null;

	/**
	 * logger
	 **/
	public $logger = null;

	/**
	 * Instance
	 **/
	public static $_instance = null;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->hooks();
	}

	/**
	 * @return WOO_MSTORE_MULTI_INIT|null
	 */
	public static function getInstance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 *
	 */
	public function define_constants() {
		$this->define( 'WOO_MSTORE_PATH',  plugin_dir_path( __FILE__ )  );
		$this->define( 'WOO_MSTORE_URL',  plugins_url( '', __FILE__ ) );
		$this->define( 'WOO_MSTORE_APP_API_URL', 'https://woomultistore.com/index.php' );

		$this->define( 'WOO_MSTORE_PLUGIN_BASE_NAME', 'woocommerce-multistore/woocommerce-multistore.php' );
		$this->define( 'WOO_MSTORE_PLUGIN_SLUG', 'woocommerce-multistore' );
		$this->define( 'WOO_MSTORE_VERSION', '5.2.4' );
		$this->define( 'WOO_MSTORE_DB_VERSION', '1.0' );

		$this->define( 'WOO_MSTORE_PRODUCT_ID', 'WCMSTORE' );
		$this->define( 'WOO_MSTORE_INSTANCE', str_replace( array( 'https://', 'http://' ), '', network_site_url() ) );

		$this->define( 'WOO_MSTORE_SINGLE_TEMPLATES_PATH', dirname( __FILE__ ) . '/templates/' );
		$this->define( 'WOO_MSTORE_SINGLE_INCLUDES_PATH', dirname( __FILE__ ) . '/includes/' );

		$this->define( 'WOO_MSTORE_ASSET_URL',  plugins_url( '', __FILE__ ) );
	}

	/**
	 *
	 */
	public function includes(){

		// functions
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-image-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-attachment-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-term-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-comment-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-site-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-product-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-order-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-coupon-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-product-attribute-functions.php';
		require_once WOO_MSTORE_PATH . '/includes/wc-multistore-deprecated-hooks.php';


		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-request.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-licence.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-updater.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-menu.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-install.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-deactivate.php';

		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-custom-taxonomy.php';

		require_once WOO_MSTORE_PATH . '/includes/admin/meta-boxes/class-wc-multistore-meta-box-product-data.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-admin-post-types.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-site.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-sites.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-setup-wizard.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-connected-sites.php';

		// Abstracts
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-attachment-master.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-attachment-child.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-comment-master.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-comment-child.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-term-master.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-term-child.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-product-master.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-product-child.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-order-master.php';
		require_once WOO_MSTORE_PATH . '/includes/abstracts/class-wc-multistore-abstract-order-child.php';

		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-integration.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-download-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-download-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-review-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-review-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-attachment-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-attachment-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-image-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-image-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-shipping-class-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-shipping-class-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-category-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-category-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-tag-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-tag-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-attribute-term-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-attribute-term-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-attribute-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-attribute-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-simple-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-simple-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-variable-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-variable-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-variation-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-variation-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-grouped-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-grouped-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-external-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-product-external-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-order-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-order-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-order-refund-child.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-order-refund-master.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-network-orders.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-network-products.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-settings.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-sequential-order-number.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-coupon-master.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-coupon-child.php';

		// Ajax
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-product-master.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-product-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-order-master.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-order-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-stock-master.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-stock-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-site-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-site-master.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-settings-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-product-category-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-order-note-master.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-order-note-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-image-master.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-coupon-child.php';
		require_once WOO_MSTORE_PATH . '/includes/ajax/class-wc-multistore-ajax-coupon-master.php';

		// export functionality
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-export.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-export-csv.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-export-xls.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-export-order.php';

		// Load reset functionality from the core multistore plugin.
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-reset.php';

		// Hooks
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-product-hooks-master.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-product-hooks-child.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-stock-hooks-child.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-stock-hooks-master.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-order-hooks-child.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-order-hooks-master.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-order-note-hooks-master.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-order-note-hooks-child.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-image-hooks-master.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-image-hooks-child.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-coupon-hooks-master.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-coupon-hooks-child.php';
		require_once WOO_MSTORE_PATH . '/includes/hooks/class-wc-multistore-product-category-hooks-master.php';

		// API
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-site-api-child.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-site-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-product-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-product-api-child.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-settings-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-stock-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-stock-api-child.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-order-api-child.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-order-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-product-category-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-order-note-api-child.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-order-note-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-coupon-api-child.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-coupon-api-master.php';
		require_once WOO_MSTORE_PATH . '/includes/api/class-wc-multistore-image-api-child.php';

		// REST API
		require_once WOO_MSTORE_PATH . '/includes/rest-api/class-wc-multistore-product-rest-api.php';
		require_once WOO_MSTORE_PATH . '/includes/rest-api/class-wc-multistore-orders-rest-api.php';
		require_once WOO_MSTORE_PATH . '/includes/rest-api/class-wc-multistore-stores-rest-api.php';

		// Database
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-database.php';

		// Bulk Sync
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-bulk-sync.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-bulk-update.php';

		// Utility
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-admin-notices.php';
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-data-update.php';

		// Multisite
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-my-account.php';
//		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-compatibility.php';

		// Single Site
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-version.php';
		require_once WOO_MSTORE_PATH . '/includes/class-wc-multistore-ssl.php';

		// Troubleshoot
		require_once WOO_MSTORE_PATH . '/includes/admin/class-wc-multistore-troubleshoot.php';
	}

	/**
	 *
	 */
	public function hooks(){
		register_activation_hook(  WC_MULTISTORE_FILE  , array( 'WC_Multistore_Install','install' ) );
		register_deactivation_hook( WC_MULTISTORE_FILE, array( 'WC_Multistore_Deactivate','deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'dependencies' ) );
		add_action( 'network_admin_notices', array( $this, 'network_admin_notices' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'plugin_row_meta', array( $this, 'action_links' ), 10, 2 );
		add_filter( 'before_woocommerce_init', array( $this, 'before_woocommerce_init' ), 10 );
	}

	public function init(){
		$this->load_textdomain();
		$this->permission = wc_multistore_min_user_role();

		$GLOBALS['WC_Multistore_Menu'] = new WC_Multistore_Menu();
		$this->license = new WC_Multistore_Licence();
		$this->site = new WC_Multistore_Site();
		$WC_Multistore_Sites = new WC_Multistore_Sites();
		$this->sites = $WC_Multistore_Sites->get_sites();
		$this->active_sites = $WC_Multistore_Sites->get_active_sites();
		$this->setup = new WC_Multistore_Setup_Wizard();
		$this->data = new WC_Multistore_Data_Update();
		$WC_Multistore_Settings = new WC_Multistore_Settings();
		$this->settings = $WC_Multistore_Settings->get_settings();

		$this->request = new WC_Multistore_Request();

		$GLOBALS['WC_Multistore_Database'] = new WC_Multistore_Database();
		$GLOBALS['WC_Multistore_Ajax_Settings_Child'] = new WC_Multistore_Ajax_Settings_Child();
		$GLOBALS['WC_Multistore_Ajax_Site_Master'] = new WC_Multistore_Ajax_Site_Master();
		$GLOBALS['WC_Multistore_Ajax_Site_Child'] = new WC_Multistore_Ajax_Site_Child();
		$GLOBALS['WC_Multistore_Ajax_Product_Master'] = new WC_Multistore_Ajax_Product_Master();
		$GLOBALS['WC_Multistore_Ajax_Product_Child'] = new WC_Multistore_Ajax_Product_Child();
		$GLOBALS['WC_Multistore_Ajax_Order_Master'] = new WC_Multistore_Ajax_Order_Master();
		$GLOBALS['WC_Multistore_Ajax_Order_Child'] = new WC_Multistore_Ajax_Order_Child();
		$GLOBALS['WC_Multistore_Ajax_Stock_Master'] = new WC_Multistore_Ajax_Stock_Master();
		$GLOBALS['WC_Multistore_Ajax_Stock_Child'] = new WC_Multistore_Ajax_Stock_Child();
		$GLOBALS['WC_Multistore_Ajax_Product_Category_Child'] = new WC_Multistore_Ajax_Product_Category_Child();
		$GLOBALS['WC_Multistore_Ajax_Order_Note_Master'] = new WC_Multistore_Ajax_Order_Note_Master();
		$GLOBALS['WC_Multistore_Ajax_Order_Note_Child'] = new WC_Multistore_Ajax_Order_Note_Child();
		$GLOBALS['WC_Multistore_Ajax_Coupon_Child'] = new WC_Multistore_Ajax_Coupon_Child();
		$GLOBALS['WC_Multistore_Ajax_Coupon_Master'] = new WC_Multistore_Ajax_Coupon_Master();
		$GLOBALS['WC_Multistore_Ajax_Image_Master'] = new WC_Multistore_Ajax_Image_Master();

		$GLOBALS['WC_Multistore_Product_Hooks_Master'] = new WC_Multistore_Product_Hooks_Master();
		$GLOBALS['WC_Multistore_Product_Hooks_Child'] = new WC_Multistore_Product_Hooks_Child();
		$GLOBALS['WC_Multistore_Meta_Box_Product_Data'] = new WC_Multistore_Meta_Box_Product_Data();
		$GLOBALS['WC_Multistore_Admin_Post_Types'] = new WC_Multistore_Admin_Post_Types();
		$GLOBALS['WC_Multistore_Stock_Hooks_Master'] = new WC_Multistore_Stock_Hooks_Master();
		$GLOBALS['WC_Multistore_Stock_Hooks_Child'] = new WC_Multistore_Stock_Hooks_Child();
		$GLOBALS['WC_Multistore_Updater'] = new WC_Multistore_Updater();
		$GLOBALS['WC_Multistore_Custom_Taxonomy'] = new WC_Multistore_Custom_Taxonomy();
		$GLOBALS['WC_Multistore_Connected_Sites'] = new WC_Multistore_Connected_Sites();
		$GLOBALS['WC_Multistore_Network_Orders'] = new WC_Multistore_Network_Orders();
		$GLOBALS['WC_Multistore_Network_Products'] = new WC_Multistore_Network_Products();

		$GLOBALS['WC_Multistore_Product_Category_Hooks_Master'] = new WC_Multistore_Product_Category_Hooks_Master();
		$GLOBALS['WC_Multistore_Integration'] = new WC_Multistore_Integration();
		$GLOBALS['WC_Multistore_Export_Order'] = new WC_Multistore_Export_Order();
		$GLOBALS['WC_Multistore_Reset'] = new WC_Multistore_Reset();
		$GLOBALS['WC_Multistore_Order_Hooks_Child'] = new WC_Multistore_Order_Hooks_Child();
		$GLOBALS['WC_Multistore_Order_Hooks_Master'] = new WC_Multistore_Order_Hooks_Master();
		$GLOBALS['WC_Multistore_Order_Note_Hooks_Master'] = new WC_Multistore_Order_Note_Hooks_Master();
		$GLOBALS['WC_Multistore_Order_Note_Hooks_Child'] = new WC_Multistore_Order_Note_Hooks_Child();
		$GLOBALS['WC_Multistore_Image_Hooks_Master'] = new WC_Multistore_Image_Hooks_Master();
		$GLOBALS['WC_Multistore_Image_Hooks_Child'] = new WC_Multistore_Image_Hooks_Child();
		$GLOBALS['WC_Multistore_Coupon_Hooks_Master'] = new WC_Multistore_Coupon_Hooks_Master();
		$GLOBALS['WC_Multistore_Coupon_Hooks_Child'] = new WC_Multistore_Coupon_Hooks_Child();
		$GLOBALS['WC_Multistore_Bulk_Sync'] = new WC_Multistore_Bulk_Sync();
		$GLOBALS['WC_Multistore_Bulk_Update'] = new WC_Multistore_Bulk_Update();
		$GLOBALS['WC_Multistore_My_Account'] = new WC_Multistore_My_Account();
		$GLOBALS['WC_Multistore_Stores_Rest_Api'] = new WC_Multistore_Stores_Rest_Api();
		$GLOBALS['WC_Multistore_Orders_Rest_Api'] = new WC_Multistore_Orders_Rest_Api();
		$GLOBALS['WC_Multistore_Product_Rest_Api'] = new WC_Multistore_Product_Rest_Api();
		$GLOBALS['WC_Multistore_Admin_Notices'] = new WC_Multistore_Admin_Notices();
//		$GLOBALS['WC_Multistore_Compatibility'] = new WC_Multistore_Compatibility();
		$GLOBALS['WC_Multistore_Version'] = new WC_Multistore_Version();
		$GLOBALS['WC_Multistore_Ssl'] = new WC_Multistore_Ssl();
		//$GLOBALS['WC_Multistore_Troubleshoot'] = new WC_Multistore_Troubleshoot();


		do_action( 'woomultistore_loaded' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 4.2.0
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce' ), '4.2.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 4.2.0
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '4.2.0' );
	}

	/**
	 *
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woonet', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 *
	 */
	public function network_admin_notices() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$this->setup_wizard_notice();
		}
	}

	/**
	 *
	 */
	public function admin_notices() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$this->setup_wizard_notice();
		}
	}

	/**
	 * First time usage require a setup
	 */
	public function setup_wizard_notice() {
		$screen = get_current_screen();
		if ( is_object( $screen ) && $screen->id == 'toplevel_page_woonet-woocommerce-network' ) {
			return;
		}

		if ( ! $this->setup->is_complete ) {
			include WOO_MSTORE_PATH . 'includes/admin/views/html-notice-setup.php';
		}
	}

	/**
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function action_links( $links, $file ) {
		if ( strpos( $file, 'woocommerce-multistore.php' ) !== false ) {
			unset( $links[2] );
			$links[] = '<a href="' . esc_url( network_admin_url( 'settings.php?page=woo-ms-options' ) ) . '">Settings</a>';
			$links[] = '<a href="https://woomultistore.com/documentation/">Docs</a>';
			$links[] = '<a href="https://woomultistore.com/plugin-api-filters-actions/">API docs</a>';
			$links[] = '<a href="https://woomultistore.com/addons/">Addons</a>';
		}

		return $links;
	}

	public function before_woocommerce_init(){
		if( class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ){
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('product_block_editor', __FILE__, true);
		}
	}

	public function dependencies(){
		if( is_multisite() ){
			$get_sites_args = array(
				'number'   => 999,
				'archived' => 0,
				'spam'     => 0,
				'deleted'  => 0,
			);
			$wp_sites       = get_sites( $get_sites_args );
			$is_woo_active = false;

			foreach ( $wp_sites as $wp_site ) {
				$active_sitewide_plugins = get_site_option('active_sitewide_plugins' );
				$active_plugins = get_blog_option( $wp_site->blog_id, 'active_plugins' );
				if ( ( $active_plugins && isset( array_flip( $active_plugins ) [ 'woocommerce/woocommerce.php' ] ) ) || isset( $active_sitewide_plugins [ 'woocommerce/woocommerce.php' ] ) ) {
					$is_woo_active = true;
				}
			}

			if( ! $is_woo_active ){
				add_action( 'admin_notices', array( $this, 'woocommerce_notice' ) );
				add_action( 'network_admin_notices', array( $this, 'woocommerce_notice' ) );
				deactivate_plugins(WOO_MSTORE_PLUGIN_BASE_NAME);
			}

		}else{
			if( ! class_exists('WooCommerce') ){
				add_action( 'admin_notices', array( $this, 'woocommerce_notice' ) );
				add_action( 'network_admin_notices', array( $this, 'woocommerce_notice' ) );
				deactivate_plugins(WOO_MSTORE_PLUGIN_BASE_NAME);
			}
		}
	}

	public function woocommerce_notice() {
		$class   = 'notice notice-error';
		$message = __( 'Multistore requires WooCommerce to be activated', 'woonet' );

		printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}

function WOO_MULTISTORE() {
	return WOO_MSTORE_MULTI_INIT::getInstance();
}

$GLOBALS['WOO_MULTISTORE'] = WOO_MULTISTORE();