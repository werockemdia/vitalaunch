<?php
/**
 * API to check plugin version.
 */

defined( 'ABSPATH' ) || exit;

class WC_Multistore_Version {

	/**
	 * Initialize the action hooks and load the plugin classes
	 **/
	public function __construct() {
		if( is_multisite() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( WOO_MULTISTORE()->site->get_type() != 'master' ){ return; }

        $this->hooks();
	}

	public function hooks() {
		add_action( 'admin_init', array( $this, 'check_versions' ), 10, 0 );

		if ( get_transient( 'wc_multistore_show_update_notice' ) ) {
			add_action( 'admin_notices', array( $this, 'show_update_notice' ), 10, 0 );
		}
    }


	/**
	 * Check all child sites and notify the user to
	 * update if running an older version.
	 */
	public function check_versions() {
		if ( get_transient( 'wc_multistore_version_check' ) ) {
			return;
		}

		// Do not check more than once every 72 hours.
		set_transient( 'wc_multistore_version_check', time(), 72 * 60 * 60 );
		$_set_update_notice = false;
        
        $wc_multistore_site_api_master = new WC_Multistore_Site_Api_Master();

		$results = array();
        foreach (WOO_MULTISTORE()->active_sites as $site){
	        $result      = $wc_multistore_site_api_master->get_child_status($site);
	        $results[] = $result;
        }


		foreach ( $results as $result ) {
			if ( isset( $result['data']['status'] ) && $result['status'] == 'failed' ) {
				$_set_update_notice = true;
			} elseif ( ! empty( $result['data']['status'] ) && defined( 'WOO_MSTORE_VERSION' ) && version_compare( WOO_MSTORE_VERSION, $result['data']['version'], '!=' ) ) {
				$_set_update_notice = true;
			}
		}

		if ( $_set_update_notice === true ) {
			set_transient( 'wc_multistore_show_update_notice', true, 12 * 60 * 60 );
		} else {
			delete_transient( 'wc_multistore_show_update_notice' );
		}
	}

	/**
	 * Show update notice
	 */
	public function show_update_notice() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php _e( 'Some of your child sites may be running older versions of <a target="_blank" href="https://woomultistore.com/"> WooMultistore</a>. You may update from WordPress or download the plugin from the <a target="_blank" href="https://woomultistore.com/my-account/downloads/"> download section </a> of our website. If you have updated recently, you can ignore the warning.' ); ?></p>
		</div>
		<?php
	}
}

