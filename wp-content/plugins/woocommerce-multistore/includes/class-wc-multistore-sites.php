<?php
/**
 * Sites handler.
 *
 * This handles sites functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Sites
 */
class WC_Multistore_Sites {
	/**
	 * @var array
	 */
	protected $sites;

	/**
	 *
	 */
	public function __construct() {
		$this->sites = $this->set_sites();
		$this->hooks();
	}

	/**
	 * @return array
	 */
	private function set_sites(){
		return wc_multistore_get_sites();
	}

	/**
	 *
	 */
	public function hooks(){
		add_action('admin_init', array( $this, 'update') );
	}

	/**
	 * Returns an array of WC_Multistore_Site object
	 *
	 * @return array
	 */
	public function get_sites(){
		return $this->sites;
	}

	/**
	 * Returns an array of active WC_Multistore_Site object
	 *
	 * @return array
	 */
	public function get_active_sites(){
		$active_sites = array();
		foreach ( $this->sites as $site ){
			if( $site->is_active() ){
				$active_sites[$site->get_id()] = $site;
			}
		}
		return $active_sites;
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

		if( !empty($_POST['sites']) ){
			$args = $_POST['sites'];
			$this->save( $args );
		}
	}

	/**
	 * @param $args
	 */
	public function save( $args ){
		$sites = array();
		$sites_data = array();


		if( is_multisite() ){
			foreach ( $args as $site_id => $arg ) {
				$this->sites[$site_id]->set_settings( $arg );
				$this->sites[$site_id]->save();
				$sites_data[$site_id] = $this->sites[$site_id]->get_data();
			}
			update_site_option('wc_multistore_sites', $sites_data);
		}else{
			if( !empty( $args ) ){
				foreach ( $args as $site_id => $arg ) {
					$this->sites[$site_id]->set_settings( $arg );
					$this->sites[$site_id]->save();
					$sites_data[$site_id] = $this->sites[$site_id]->get_data();
				}

				update_site_option('wc_multistore_sites', $sites_data);
			}
		}

		do_action('wc_multistore_sites_saved', $sites, $this->sites );
	}
}