<?php
/**
 * Master Product Handler
 *
 * This handles master product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Hooks_Master
 */
class WC_Multistore_Product_Hooks_Master{

	public $settings;

	public $sites;

	public function __construct(){
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( is_admin() && ! WOO_MULTISTORE()->permission ){ return; }

        $this->settings = WOO_MULTISTORE()->settings;
        $this->sites = WOO_MULTISTORE()->active_sites;
		$this->hooks();
	}

	public function hooks(){
		add_action( 'woocommerce_update_product', array( $this, 'update_master_product' ), 10, 2 );

		add_action( 'wc_multistore_scheduled_products', array( $this, 'run_scheduled_products_sync' ), 10, 2 );
		add_action( 'wc_multistore_scheduled_trash_products', array( $this, 'run_scheduled_products_trash' ), 10, 2 );
		add_action( 'wc_multistore_scheduled_untrash_products', array( $this, 'run_scheduled_products_untrash' ), 10, 2 );
		add_action( 'wc_multistore_scheduled_delete_products', array( $this, 'run_scheduled_products_delete' ), 10, 2 );

		add_action( 'wp_trash_post', array( $this, 'trash_post' ), 20 );
		add_action( 'untrash_post', array( $this, 'untrash_post' ), 20 );
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ), 20 );

		add_action( 'admin_notices', array( $this, 'output_error' ) );
		add_action( 'woocommerce_product_duplicate_before_save', array( $this, 'remove_multistore_meta' ), 10 );
	}


    public function update_master_product( $product_id, $wc_product ){
	    if( ! empty($_REQUEST['page'] ) && $_REQUEST['page'] == 'pmxi-admin-import' && ! empty($_REQUEST['action'] ) && $_REQUEST['action'] == 'process' ) {
		    return;
	    }

		if( did_action('wp_ajax_nopriv_wc_multistore_master_receive_stock') || did_action('wp_ajax_wc_multistore_master_receive_stock') ){
			return;
		}

	    if( ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'woocommerce_save_variations' ) || ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'woocommerce_add_variation' ) || ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'woocommerce_remove_variation' ) || wp_is_post_revision( $product_id ) ){
			return;
	    }

	    if( $wc_product->get_type() == 'variation' ){
		    return;
	    }

	    if( WOO_MULTISTORE()->site->get_type() != 'master' ){
		    return;
	    }

	    if( wc_multistore_is_saving_order() ){
		    return;
	    }

	    if( is_checkout() ){
		    return;
	    }

        if( wc_multistore_is_child_product( $wc_product ) ){
            return;
        }

		if( wp_is_post_revision( $wc_product ) ){
            return;
        }

		if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' && empty($wc_product->get_sku('edit')) ){
			$error = 'Product does not have a sku while sync by sku is enabled.';
			update_site_option('wc_multistore_product_errors', $error);
			return;
		}

	    $classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );

		if( ! $classname ){
			return;
		}

	    $multistore_product = new $classname( $wc_product );


		// needs removed when woocommerce fixes the bug
	    // abstract class WC_Data line 808
	    if( wc_multistore_is_admin_product_page() ){
			if( ! isset( $_REQUEST['crosssell_ids'] ) || empty( $_REQUEST['crosssell_ids'] ) ){
				$multistore_product->wc_product->set_cross_sell_ids(array());
			}

		    if( ! isset( $_REQUEST['upsell_ids'] ) || empty( $_REQUEST['upsell_ids'] ) ){
			    $multistore_product->wc_product->set_upsell_ids(array());
		    }

		    if( ! isset( $_REQUEST['product_image_gallery'] ) || empty( $_REQUEST['product_image_gallery'] ) ){
			    $multistore_product->wc_product->set_gallery_image_ids(array());
		    }
		}

	    $multistore_product->save();

	    // quick edit on products page
	    if( wc_multistore_is_quick_edit() ){
		    return;
	    }

		// background sync
        if( $this->settings['sync-method'] == 'background' ){
	        if( $multistore_product->is_enabled_sync ) {
		        $multistore_product->set_scheduler( 'wc_multistore_scheduled_products' );
				if( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ){
					$multistore_product->set_scheduler_transient( 'wc_multistore_scheduled_products' );
				}
	        }
            return;
        }else{
	        // ajax sync on products/product page
	        if( ( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ) ){
		        if( $multistore_product->is_enabled_sync ){
			        $multistore_product->set_ajax_transient( 'wc_multistore_ajax_products' );
		        }
		        return;
	        }
        }

		// default sync
	    if( $multistore_product->is_enabled_sync ){
	        $multistore_product->sync();
		}
    }


	public function trash_post($id){
		global $post_type;

		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		if( $this->settings['synchronize-trash'] != 'yes' ){
			return;
		}

		if ( empty( $post_type ) ||  $post_type != 'product' ) {
			return;
		}

		$wc_product = wc_get_product($id);

		if( ! $wc_product ){
			return;
		}

		if( $wc_product->is_type('variation') ){
			return;
		}

		if( wc_multistore_is_child_product($wc_product) ){
			return;
		}

		if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' && empty( $wc_product->get_sku('edit') ) ){
			return;
		}

		$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );

		if( ! $classname ){
			return;
		}

		$multistore_product = new $classname( $wc_product );

		if(  $this->settings['sync-method'] == 'background' ){
			if( $multistore_product->is_enabled_sync ){
				$multistore_product->set_scheduler( 'wc_multistore_scheduled_trash_products' );
				if( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ){
					$multistore_product->set_scheduler_transient( 'wc_multistore_scheduled_trash_products' );
				}
			}
			return;
		}else{
			if( ( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ) ){
				if( $multistore_product->is_enabled_sync ){
					$multistore_product->set_ajax_transient( 'wc_multistore_trash_ajax_products' );
				}
				return;
			}
		}

		$multistore_product->trash_children();
	}

	public function untrash_post($id){
		global $post_type;

		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		if( $this->settings['synchronize-trash'] != 'yes' ){
			return;
		}

		if ( empty( $post_type ) ||  $post_type != 'product' ) {
			return;
		}

		$wc_product = wc_get_product($id);

		if( ! $wc_product ){
			return;
		}

		if( $wc_product->is_type('variation') ){
			return;
		}

		if( wc_multistore_is_child_product($wc_product) ){
			return;
		}

		if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' && empty( $wc_product->get_sku('edit') ) ){
			return;
		}

		$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );

		if( ! $classname ){
			return;
		}

		$multistore_product = new $classname( $wc_product );

		if(  $this->settings['sync-method'] == 'background' ){
			if( $multistore_product->is_enabled_sync ){
				$multistore_product->set_scheduler( 'wc_multistore_scheduled_untrash_products' );
				if( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ){
					$multistore_product->set_scheduler_transient( 'wc_multistore_scheduled_untrash_products' );
				}
			}
			return;
		}else{
			if( ( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ) ){
				if( $multistore_product->is_enabled_sync ){
					$multistore_product->set_ajax_transient( 'wc_multistore_untrash_ajax_products' );
				}
				return;
			}
		}

		$multistore_product->untrash_children();
	}

	public function before_delete_post($id){
		global $post_type;

		if( WOO_MULTISTORE()->site->get_type() != 'master' ){
			return;
		}

		if( $this->settings['synchronize-trash'] != 'yes' ){
			return;
		}

		if ( empty( $post_type ) ||  $post_type != 'product' ) {
			return;
		}

		$wc_product = wc_get_product($id);

		if( ! $wc_product ){
			return;
		}

		if( $wc_product->is_type('variation') ){
			return;
		}

		if( wc_multistore_is_child_product($wc_product) ){
			return;
		}

		if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' && empty( $wc_product->get_sku('edit') ) ){
			return;
		}

		$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );

		if( ! $classname ){
			return;
		}

		$multistore_product = new $classname( $wc_product );

		if(  $this->settings['sync-method'] == 'background' ){
			if( $multistore_product->is_enabled_sync ){
				$multistore_product->set_scheduler( 'wc_multistore_scheduled_delete_products' );
				if( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ){
					$multistore_product->set_scheduler_transient( 'wc_multistore_scheduled_delete_products' );
				}
			}
			return;
		}else{
			if( ( wc_multistore_is_admin_product_page() || wc_multistore_is_admin_products_page() ) ){
				if( $multistore_product->is_enabled_sync ){
					$multistore_product->set_ajax_transient( 'wc_multistore_delete_ajax_products' );
				}
				return;
			}
		}

		$multistore_product->delete_children();
	}

	public function run_scheduled_products_sync( $post_id ) {
		$wc_product = wc_get_product($post_id);
		$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
		$multistore_product = new $classname( $wc_product );
		$multistore_product->sync();
	}

	public function run_scheduled_products_trash( $post_id ) {
		$sites = WOO_MULTISTORE()->active_sites;
		foreach ( $sites as $site ){
			switch_to_blog($site->get_id());
			$child_product_id = wc_multistore_product_get_slave_product_id($post_id);
			if($child_product_id){
				wp_trash_post($child_product_id);
			}
			restore_current_blog();
		}
	}

	public function run_scheduled_products_untrash( $post_id ) {
		$wc_product = wc_get_product($post_id);
		$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
		$multistore_product = new $classname( $wc_product );
		$multistore_product->untrash_children();
	}

	public function run_scheduled_products_delete( $post_id ) {
		$sites = WOO_MULTISTORE()->sites;
		unset($sites[get_current_blog_id()]);
		foreach ( $sites as $site ){
			switch_to_blog($site->get_id());
			$child_product_id = wc_multistore_product_get_slave_product_id($post_id);
			if($child_product_id){
				wp_delete_post($child_product_id);
			}
			restore_current_blog();
		}
	}

	public function output_error(){
		$error = get_site_option('wc_multistore_product_errors');
		if ( ! empty( $error ) ) {
			echo '<div id="woocommerce_errors" class="error notice is-dismissible">';
			echo '<p>' . wp_kses_post( $error ) . '</p>';
			echo '</div>';
			delete_site_option( 'wc_multistore_product_errors' );
		}
	}

	public function remove_multistore_meta( $duplicate ){
		$duplicate->delete_meta_data('_woonet_settings');
		$duplicate->delete_meta_data('_woonet_network_main_product');
		$duplicate->delete_meta_data('_woonet_children_data');
		return $duplicate;
	}
}