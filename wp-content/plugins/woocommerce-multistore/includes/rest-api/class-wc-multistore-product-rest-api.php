<?php
/**
 * API Product Sync
 * 
 * Handles synchronization of new products added via the REST API.
 *
 * Add the right metadata, sync with the stores for products added via the REST API.
 * @since: 4.4.8
 **/

defined( 'ABSPATH' ) || exit;

class WC_Multistore_Product_Rest_Api {
    /**
     * Add action hooks on instantiation
     **/
    public function __construct() {
        add_action( 'woocommerce_rest_insert_product_object', array( $this, 'wc_multistore_rest_insert_product_object'), 10, 3 );
    }

    public function wc_multistore_rest_insert_product_object( $object, $request, $isCreating ) {
        if (  WOO_MULTISTORE()->site->get_type() != 'master' ) {
            return;
        }

        if ( WOO_MULTISTORE()->settings['synchronize-rest-by-default'] == 'yes' ) {
	        $classname                    = wc_multistore_get_product_class_name( 'master', $object->get_type() );

			if( ! $classname ){
				return;
			}

	        $wc_multistore_master_product = new $classname( $object );

            if ( $isCreating ) {
	            WOO_MULTISTORE()->active_sites;

	            foreach ( WOO_MULTISTORE()->active_sites as $site ) {
		            if ( $site->settings['child_inherit_changes_fields_control__synchronize_rest_by_default'] == 'yes' ) {
			            $publish_to = '_woonet_publish_to_' . $site->get_id();
			            $inherit    = '_woonet_publish_to_' . $site->get_id() . '_child_inheir';
			            $stock      = '_woonet_' . $site->get_id() . '_child_stock_synchronize';

			            $wc_multistore_master_product->settings[ $publish_to ] = 'yes';
			            $wc_multistore_master_product->settings[ $stock ]      = 'no';

			            if ( WOO_MULTISTORE()->settings['inherit-rest-by-default'] == 'yes' ) {
				            $wc_multistore_master_product->settings[ $inherit ] = 'yes';
			            }

			            $wc_multistore_master_product->save_settings();
			            $wc_multistore_master_product->save();
		            }
	            }
            }

	        $wc_multistore_master_product->sync();
        }
    }
}