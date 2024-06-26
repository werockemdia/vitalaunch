<?php
/**
 * The plugin lets the customer manage multiple inventory per product.
 * This integration syncs multi-inventory data when the product is synced
 * across the network.
 *
 * URL: https://www.stockmanagementlabs.com/
 * Plugin URL: https://www.stockmanagementlabs.com/addons/atum-multi-inventory/
 *
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || exit;

class WOO_MSTORE_INTEGRATION_ATUM_MULTI_INVENTORY {

	public $meta_keys = array(
		'_multi_inventory',
		'_inventory_sorting_mode',
		'_inventory_iteration',
		'_expirable_inventories',
		'_price_per_inventory',
	);

	/**
	 * Initialize the action hooks and load the plugin classes
	 **/
	public function __construct() {
		if ( is_multisite() ) {
			add_filter( 'wc_multistore_whitelisted_meta_keys', array( $this, 'add_meta_keys' ), 10, 1 );
		}
	}

	public function add_meta_keys( $meta_keys ) {
		return array_merge( $meta_keys, $this->meta_keys );
	}
}

new WOO_MSTORE_INTEGRATION_ATUM_MULTI_INVENTORY();