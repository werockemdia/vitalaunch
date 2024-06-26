<?php
/**
 * Licence Handler
 *
 * This handles licence related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Licence
 */
class WC_Multistore_Licence {

	/**
	 * @var array
	 */
	public $errors = array();

	/**
	 * @var array
	 */
	public $messages = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->hooks();
	}

	public function hooks(){
        $this->check_status();
        $this->process_add_form();
        $this->process_edit_form();
	}

	/**
	 * Add licence menu page
	 */
	public function add_menu_page(){
		if( ! WOO_MULTISTORE()->permission ){ return; }

		add_submenu_page('woonet-woocommerce', 'Licence', 'Licence', 'manage_woocommerce', 'woonet-woocommerce', array( $this,'menu_page' ), 55.5 );
	}

	/**
	 * Display licence page
	 */
	public function menu_page(){
		if( $this->is_active() ){
			require_once( WOO_MSTORE_PATH . 'includes/admin/views/html-admin-page-licence-form-edit.php' );
		}else{
			require_once( WOO_MSTORE_PATH . 'includes/admin/views/html-admin-page-licence-form-add.php' );
		}
	}

	/**
	 * Checks licence status
	 * @return false|void
	 */
	public function check_status(){
		$license_data = get_site_option( 'wc_multistore_license' );

		if ( empty( $license_data['key'] ) || empty( $license_data['last_check'] ) ) {
			delete_site_option( 'wc_multistore_license' ); // delete if there's any old key in the database such as after migration
			return;
		}

		if ( isset( $license_data['last_check'] ) ) {
			if ( time() < ( $license_data['last_check'] + 86400 ) ) { // 86400s = 24h
				return;
			}
		}

		$args = array(
			'woo_sl_action'     => 'status-check',
			'licence_key'       => $license_data['key'],
			'product_unique_id' => WOO_MSTORE_PRODUCT_ID,
			'domain'            => WOO_MSTORE_INSTANCE,
		);

		$request_uri = WOO_MSTORE_APP_API_URL . '?' . http_build_query( $args, '', '&' );
		$request     = wp_remote_get( $request_uri );

		if ( defined( 'WOO_MOSTORE_DEV_ENV' ) && WOO_MOSTORE_DEV_ENV == true ) {
			error_log( var_export( $license_data, true ) );
			error_log( var_export( $request, true ) );
		}

		if ( is_wp_error( $request ) || $request['response']['code'] != 200 ) {
			return false;
		}

		$result = json_decode( $request['body'] );

        if( ! is_countable( $result ) ){
            return false;
        }

		$result = $result[ count( $result ) - 1 ];

		if ( isset( $result->status_code ) ) {
			if ( $result->status_code == 's205' || $result->status_code == 's215' ) {
				$license_data['last_check'] = time();
				update_site_option( 'wc_multistore_license', $license_data );
			} else {
				delete_site_option( 'wc_multistore_license' );
			}
		}
	}

	/**
	 * Process licence add form
	 */
	public function process_add_form(){
		if( ! isset( $_POST['submit_wc_multistore_add_license_form'] ) ){
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_multistore_license_nonce'] ) ), 'wc_multistore_license' ) ) {
			return;
		}

		if( ! empty( $_POST['license_key'] ) ){
			$license_data['key']        = $_POST['license_key'];
			$license_data['last_check'] = time();
			$this->activate( $_POST['license_key'] );
		}
	}

	/**
	 * Process licence edit form
	 */
	public function process_edit_form(){
		if( ! isset( $_POST['submit_wc_multistore_edit_license_form'] ) ){
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_multistore_edit_license_nonce'] ) ), 'wc_multistore_edit_license' ) ) {
			return;
		}

		$this->deactivate();
	}

	/**
	 * @return bool
	 */
	public function is_active(){
        if( $this->is_local_instance() ){
            return true;
        }

		if( WOO_MULTISTORE()->site->get_type() == 'child' ){
			return true;
		}

		return ! empty( get_site_option( 'wc_multistore_license' ) );
	}

	/**
	 * @return false|mixed
	 */
	public function get(){
		if( get_option('wc_multistore_network_type') == 'child' ){
			$connection  = get_option( 'woonet_master_connect' );

			$url = trim( $connection['master_url'] ) . '/wp-admin/admin-ajax.php';

			$body = array(
				'action' => 'wc_multistore_get_licence_data',
				'Authorization' => $connection['key']
			);

			$headers = array(
				'Authorization' => $connection['key'],
			);

			$request = new WC_Multistore_Request();

			return $request->send( $url, 'POST', $body, $headers );
		}

		return get_site_option( 'wc_multistore_license' );
	}

	/**
	 * Displays error notices
	 */
	public function display_errors(){
		if ( $this->errors ) {
			echo '<div id="woocommerce_errors" class="error notice is-dismissible">';
			foreach ( $this->errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Displays success notices
	 */
	public function display_messages(){
		if ( $this->messages ) {
			echo '<div id="message" class="updated notice is-dismissible">';
			foreach ( $this->messages as $message ) {
				echo '<p>' . wp_kses_post( $message ) . '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Activates licence key for domain
	 * @param $key
	 */
	public function activate( $key ) {
		$key = sanitize_key( trim( $key ) );

		$args = array(
			'woo_sl_action'     => 'activate',
			'licence_key'       => $key,
			'product_unique_id' => WOO_MSTORE_PRODUCT_ID,
			'domain'            => WOO_MSTORE_INSTANCE,
		);

		$request_uri = WOO_MSTORE_APP_API_URL . '?' . http_build_query( $args, '', '&' );
		$request     = wp_remote_get( $request_uri );

		if ( is_wp_error( $request ) || $request['response']['code'] != 200 ) {
			$this->errors[] = 'There was a problem connecting to ' . WOO_MSTORE_APP_API_URL;

			return;
		}

		$result = json_decode( $request['body'] );
		$result = $result[ count( $result ) - 1 ];

		if ( isset( $result->status ) ) {
			if ( $result->status == 'success' && in_array( $result->status_code, array( 's100', 's101' ) ) ) {
				// the license is active and the software is active
				$license_data = get_site_option( 'wc_multistore_license' );

				// save the license
				$license_data['key']        = $key;
				$license_data['last_check'] = time();

				update_site_option( 'wc_multistore_license', $license_data );

				$this->messages[] = 'Licence activated for: ' .WOO_MSTORE_INSTANCE;

				return;
			} else {
				$this->errors[] = 'There was a problem activating the licence: ' . $result->message;

				return;
			}
		}

		$this->errors[] = 'There was a problem with the data block received from ' . WOO_MSTORE_APP_API_URL;
	}

	/**
	 * Deactivates licence key for domain
	 */
	public function deactivate() {
		$license_data = get_site_option( 'wc_multistore_license' );

		if( ! $license_data ){
			return;
		}

		// build the request query
		$args = array(
			'woo_sl_action'     => 'deactivate',
			'licence_key'       => $license_data['key'],
			'product_unique_id' => WOO_MSTORE_PRODUCT_ID,
			'domain'            => WOO_MSTORE_INSTANCE,
		);

		$request_uri = WOO_MSTORE_APP_API_URL . '?' . http_build_query( $args, '', '&' );
		$request     = wp_remote_get( $request_uri );

		if ( is_wp_error( $request ) || $request['response']['code'] != 200 ) {
			$this->errors[] = 'There was a problem connecting to ' . WOO_MSTORE_APP_API_URL;

			return;
		}

		$result = json_decode( $request['body'] );
		$result = $result[ count( $result ) - 1 ];

		if ( isset( $result->status ) ) {
			if ( $result->status == 'success' && $result->status_code == 's201' ) {
				delete_site_option( 'wc_multistore_license' );
				$this->messages[] = 'Licence deactivated for: ' . WOO_MSTORE_INSTANCE;

				return;
			} else { // if message code is e104  force de-activation
				if ( $result->status_code == 'e002' || $result->status_code == 'e104' ) {
					delete_site_option( 'wc_multistore_license' );
					$this->messages[] = $result->message;

					return;
				} else {
					delete_site_option( 'wc_multistore_license' );
					$this->messages[] = 'There was a problem deactivating the licence: ' . $result->message;

					return;
				}
			}
		}

		$this->errors[] = 'There was a problem with the data received from ' . WOO_MSTORE_APP_API_URL;
	}

	/**
	 * Local development do not need a license.
	 */
	function is_local_instance() {

		$instance = trailingslashit( WOO_MSTORE_INSTANCE );

		if ( strpos( $instance, base64_decode( 'bG9jYWxob3N0Lw==' ) ) !== false
			|| strpos( $instance, base64_decode( 'MTI3LjAuMC4xLw==' ) ) !== false
			|| strpos( $instance, base64_decode( 'c3RhZ2luZy53cGVuZ2luZS5jb20=' ) ) !== false
			) {
				return true;
		}

		return false;
	}

	function admin_no_key_notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->is_active() ){
			return;
		}

		$screen = get_current_screen();

		if ( is_multisite() ) {
			if ( isset( $screen->id ) && $screen->id == 'settings_page_woo-ms-options-network' ) {
				return;
			}
			?>
			<div class="updated fade">
				<p><?php esc_html_e( 'WooMultistore plugin is inactive, please enter your', 'woonet' ); ?> <a
						href="<?php echo network_admin_url(); ?>admin.php?page=woonet-woocommerce"><?php esc_html_e( 'Licence Key', 'woonet' ); ?></a>
				</p></div>
			<?php
		}
	}
}
