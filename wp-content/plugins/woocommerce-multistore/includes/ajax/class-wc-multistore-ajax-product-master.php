<?php
/**
 * Ajax product master handler.
 *
 * This handles ajax product master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Product_Master
 */
class WC_Multistore_Ajax_Product_Master {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }

		add_action( 'wp_ajax_wc_multistore_inline_save_ajax', array( $this, 'wc_multistore_inline_save_ajax' ) );
		add_action( 'wp_ajax_wc_multistore_cancel_inline_save_ajax', array( $this, 'wc_multistore_cancel_inline_save_ajax' ) );
		add_action( 'wp_ajax_wc_multistore_inline_save_background', array( $this, 'wc_multistore_inline_save_background' ) );

		add_action( 'wp_ajax_wc_multistore_ajax_sync', array( $this, 'wc_multistore_ajax_sync' ) );
		add_action( 'wp_ajax_wc_multistore_cancel_ajax_sync', array( $this, 'wc_multistore_cancel_ajax_sync' ) );

		add_action( 'wp_ajax_wc_multistore_ajax_trash', array( $this, 'wc_multistore_ajax_trash' ) );
		add_action( 'wp_ajax_wc_multistore_cancel_ajax_trash', array( $this, 'wc_multistore_cancel_ajax_trash' ) );

		add_action( 'wp_ajax_wc_multistore_ajax_untrash', array( $this, 'wc_multistore_ajax_untrash' ) );
		add_action( 'wp_ajax_wc_multistore_cancel_ajax_untrash', array( $this, 'wc_multistore_cancel_ajax_untrash' ) );

		add_action( 'wp_ajax_wc_multistore_ajax_delete', array( $this, 'wc_multistore_ajax_delete' ) );
		add_action( 'wp_ajax_wc_multistore_cancel_ajax_delete', array( $this, 'wc_multistore_cancel_ajax_delete' ) );

		add_action( 'wp_ajax_nopriv_wc_multistore_delete_sync_data_from_master', array( $this, 'wc_multistore_delete_sync_data_from_master' ) );



		add_action( 'wp_ajax_woosl_setup_get_process_list', array( $this, 'woosl_setup_get_process_list' ) );
		add_action( 'wp_ajax_woosl_setup_process_batch', array( $this, 'woosl_setup_process_batch' ) );

//		add_action( 'wp_ajax_inline-save', array( $this, 'network_products_inline_save' ), - PHP_INT_MAX );
	}


	public function wc_multistore_inline_save_ajax(){
		if ( empty( $_POST['sku'] ) && WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Product is missing sku while sync by sku is enabled'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_inline_save_ajax' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		if( isset( $_REQUEST['post_ID'] ) ){
			$product = array(
				'post_ID' => 	$_REQUEST['post_ID'],
				'total_sites' => $_REQUEST['total_sites'],
				'selected_sites' => $_REQUEST['selected_sites']
			);
			$transient = 'wc_multistore_quick_edit_ajax_save' . uniqid();
			set_site_transient($transient, $product, 4 * HOUR_IN_SECONDS );

			if( $product['selected_sites'] > 0 ){
				$wc_product = wc_get_product($product['post_ID']);
				$current_site_id = $product['selected_sites'][0];
				$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
				$wc_multistore_master_product = new $classname($wc_product);
				$response = $wc_multistore_master_product->sync_to($product['selected_sites'][0]);

				if(empty($response)){
					$response = array(
						'status' => 'failed',
						'message' => 'failed',
						'code' => '500',
					);
				}

				unset($product['selected_sites'][0]);
				$product['selected_sites'] = array_values($product['selected_sites']);

				set_site_transient($transient, $product, 4 * HOUR_IN_SECONDS );

				if( empty( $product['selected_sites'] ) ){
					delete_site_transient($transient );

					$data = array(
						'status' => 'completed',
						'site_id' => $current_site_id,
						'result' => $response,
						'transient' => $transient,
					);
					echo wp_json_encode($data);
					wp_die();
				}else{
					$data = array(
						'status' => 'pending',
						'site_id' => $current_site_id,
						'result' => $response,
						'transient' => $transient,
						'progress' => ($product['total_sites'] - count($product['selected_sites']) ) / $product['total_sites']  * 100
					);
					echo wp_json_encode($data);
					wp_die();
				}
			}
		}else{
			$product = get_site_transient($_REQUEST['transient']);

			if( $product['selected_sites'] > 0 ){
				$wc_product = wc_get_product($product['post_ID']);
				$current_site_id = $product['selected_sites'][0];
				$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
				$wc_multistore_master_product = new $classname($wc_product);
				$response =  $wc_multistore_master_product->sync_to($product['selected_sites'][0]);

				if( empty($response) ){
					$response = array(
						'status' => 'failed',
						'message' => 'failed',
						'code' => '500',
					);
				}

				unset($product['selected_sites'][0]);
				$product['selected_sites'] = array_values($product['selected_sites']);

				set_site_transient($_REQUEST['transient'], $product, 4 * HOUR_IN_SECONDS );

				if(empty($product['selected_sites'])){
					delete_site_transient($_REQUEST['transient'] );

					$data = array(
						'status' => 'completed',
						'site_id' => $current_site_id,
						'result' => $response,
						'transient' => $_REQUEST['transient']
					);
					echo wp_json_encode($data);
					wp_die();
				}else{
					$data = array(
						'status' => 'pending',
						'site_id' => $current_site_id,
						'result' => $response,
						'transient' => $_REQUEST['transient'],
						'progress' => ($product['total_sites'] - count($product['selected_sites']) ) / $product['total_sites']  * 100
					);
					echo wp_json_encode($data);
					wp_die();
				}
			}else{
				delete_site_transient($_REQUEST['transient'] );

				$data = array(
					'status' => 'completed',
				);
				echo wp_json_encode($data);
				wp_die();
			}
		}
	}

	public function wc_multistore_cancel_inline_save_ajax(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_inline_save_ajax' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		delete_transient( $_REQUEST['transient']);
		echo wp_json_encode('success');
		wp_die();
	}

	public function wc_multistore_inline_save_background(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_inline_save_background' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		if( WOO_MULTISTORE()->settings['sync-method'] == 'background' ){
			$product_id = $_REQUEST['post_ID'];
			$wc_product = wc_get_product($product_id);

			$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
			$wc_multistore_master_product = new $classname($wc_product);
			$wc_multistore_master_product->set_scheduler('wc_multistore_scheduled_products');

			$data = array(
				'result' => 'completed'
			);
			echo wp_json_encode($data);
			wp_die();
		}
	}

	public function wc_multistore_ajax_sync(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_ajax_sync' ) ) {
			delete_transient( $_REQUEST['transient']);
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		$products = get_transient( $_REQUEST['transient'] );

		if( empty( $products ) ){
			$data = array(
				'status' => 'completed'
			);

			delete_transient( $_REQUEST['transient']);

			echo wp_json_encode($data);
			wp_die();
		}

		foreach ( $products as $product_id => $product ){

			if( empty( $product['sites'] ) ){
				$data = array(
					'status' => 'completed'
				);

				delete_transient( $_REQUEST['transient']);

				echo wp_json_encode($data);
				wp_die();
			}

			$sites = $product['sites'];
			$total_sites = count($sites);
			$wc_product = wc_get_product($product_id);

			$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
			$wc_multistore_master_product = new $classname($wc_product);

			foreach ( $sites as $key => $site_id ){
				$response = $wc_multistore_master_product->sync_to($site_id);

				$data = array(
					'product_id' => $product_id,
					'status' => 'success',
					'site_id' => $site_id,
					'percentage' => 1 / $total_sites * 100,
					'result' => $response
				);

				unset($products[$product_id]['sites'][$key]);

				if( empty($products[$product_id]['sites']) ){
					unset($products[$product_id]);
				}

				set_transient( $_REQUEST['transient'], $products, 4 * HOUR_IN_SECONDS );

				echo wp_json_encode($data);

				wp_die();
			}
		}
	}

	public function wc_multistore_cancel_ajax_sync(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_ajax_sync' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}


		delete_transient( $_REQUEST['transient']);
		echo wp_json_encode('success');
		wp_die();
	}

	public function wc_multistore_ajax_trash(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_ajax_trash' ) ) {
			delete_transient( $_REQUEST['transient']);
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		$products = get_transient( $_REQUEST['transient'] );

		if( empty( $products ) ){
			$data = array(
				'status' => 'completed'
			);

			delete_transient( $_REQUEST['transient']);

			echo wp_json_encode($data);
			wp_die();
		}

		foreach ( $products as $product_id => $product ){

			if( empty( $product['sites'] ) ){
				$data = array(
					'status' => 'completed'
				);

				delete_transient( $_REQUEST['transient']);

				echo wp_json_encode($data);
				wp_die();
			}

			$sites = $product['sites'];
			$total_sites = count($sites);
			foreach ( $sites as $key => $site_id ){
				$wc_product = wc_get_product($product_id);
				$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
				$wc_multistore_master_product = new $classname($wc_product);

				$response = $wc_multistore_master_product->trash_to($site_id);

				$data = array(
					'product_id' => $product_id,
					'status' => 'success',
					'percentage' => 1 / $total_sites * 100,
					'result' => $response
				);

				unset($products[$product_id]['sites'][$key]);

				if( empty($products[$product_id]['sites']) ){
					unset($products[$product_id]);
				}

				set_transient( $_REQUEST['transient'], $products, 4 * HOUR_IN_SECONDS );

				echo wp_json_encode($data);

				wp_die();
			}
		}
	}

	public function wc_multistore_cancel_ajax_trash(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_ajax_sync' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		delete_transient( $_REQUEST['transient']);
		echo wp_json_encode('success');
		wp_die();
	}

	public function wc_multistore_ajax_untrash(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_ajax_untrash' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		$products = get_transient( $_REQUEST['transient'] );

		if( empty( $products ) ){
			$data = array(
				'status' => 'completed'
			);

			delete_transient( $_REQUEST['transient']);

			echo wp_json_encode($data);
			wp_die();
		}

		foreach ( $products as $product_id => $product ){

			if( empty( $product['sites'] ) ){
				$data = array(
					'status' => 'completed'
				);

				delete_transient( $_REQUEST['transient']);

				echo wp_json_encode($data);
				wp_die();
			}

			$sites = $product['sites'];
			$total_sites = count($sites);
			foreach ( $sites as $key => $site_id ){
				$wc_product = wc_get_product($product_id);
				$classname = wc_multistore_get_product_class_name( 'master', $wc_product->get_type() );
				$wc_multistore_master_product = new $classname($wc_product);

				$response = $wc_multistore_master_product->untrash_to($site_id);

				$data = array(
					'product_id' => $product_id,
					'status' => 'success',
					'percentage' => 1 / $total_sites * 100,
					'result' => $response
				);

				unset($products[$product_id]['sites'][$key]);

				if( empty($products[$product_id]['sites']) ){
					unset($products[$product_id]);
				}

				set_transient( $_REQUEST['transient'], $products, 4 * HOUR_IN_SECONDS );

				echo wp_json_encode($data);

				wp_die();
			}
		}
	}

	public function wc_multistore_cancel_ajax_untrash(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_ajax_sync' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		delete_transient( $_REQUEST['transient']);
		echo wp_json_encode('success');
		wp_die();
	}

	public function wc_multistore_ajax_delete(){
		$products = get_transient( $_REQUEST['transient'] );

		if( empty( $products ) ){
			$data = array(
				'status' => 'completed'
			);

			delete_transient( $_REQUEST['transient']);

			echo wp_json_encode($data);
			wp_die();
		}

		foreach ( $products as $product_id => $product ){

			if( empty( $product['sites'] ) ){
				$data = array(
					'status' => 'completed'
				);

				delete_transient( $_REQUEST['transient']);

				echo wp_json_encode($data);
				wp_die();
			}

			$sites = $product['sites'];
			$total_sites = count($sites);
			foreach ( $sites as $key => $site_id ){
				if( is_multisite() ) {
					switch_to_blog( $site_id );
					$child_id = wc_multistore_product_get_slave_product_id($product_id, $product['sku']);
					$wc_product = wc_get_product($child_id);
					if( $wc_product ){
						$classname = wc_multistore_get_product_class_name( 'child', $wc_product->get_type() );
						$wc_multistore_child_product = new $classname($wc_product);
						$response = $wc_multistore_child_product->delete();
					}
					restore_current_blog();
				}else{
					$args = array(
						'product_id' => $product_id,
						'product_sku' => $product['sku'],
					);
					$wc_multistore_product_api_master = new WC_Multistore_Product_Api_Master();
					$response = $wc_multistore_product_api_master->send_delete_product_data_to_child($args, $site_id);
				}

				$data = array(
					'product_id' => $product_id,
					'status' => 'success',
					'percentage' => 1 / $total_sites * 100,
					'result' => $response
				);

				unset($products[$product_id]['sites'][$key]);

				if( empty($products[$product_id]['sites']) ){
					unset($products[$product_id]);
				}

				set_transient( $_REQUEST['transient'], $products, 4 * HOUR_IN_SECONDS );

				echo wp_json_encode($data);

				wp_die();
			}
		}
	}

	public function wc_multistore_cancel_ajax_delete(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wc_multistore_ajax_sync' ) ) {
			$data = array(
				'status' => 'failed',
				'message' => 'Insufficient permissions'
			);
			echo wp_json_encode($data);
			wp_die();
		}

		delete_transient( $_REQUEST['transient']);
		echo wp_json_encode('success');
		wp_die();
	}

	public function wc_multistore_delete_sync_data_from_master(){
		if( empty($_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			$result = array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);

			echo wp_json_encode( $result );
			wp_die();
		}

		if(WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes'){
			$master_product_id = wc_get_product_id_by_sku( $_REQUEST['sku'] );
		}else{
			$master_product_id = $_REQUEST['id'];
		}

		$wc_product = wc_get_product($master_product_id);

		$classname = wc_multistore_get_product_class_name('master', $wc_product->get_type());
		$wc_multistore_master_product = new $classname($wc_product);
		$result = $wc_multistore_master_product->delete_sync_data( $_REQUEST['key'] );


		echo wp_json_encode($result);
		wp_die();
	}










		/**
	 * Ajax handler for Quick Edit saving a post from a list table.
	 * wp-admin/includes/ajax-actions.php:wp_ajax_inline_save
	 */
	public function network_products_inline_save() {
		if (
			empty( $_REQUEST['screen'] )
			||
			! in_array(
				$_REQUEST['screen'],
				array(
					'woocommerce_page_woonet-woocommerce-products',
					'woocommerce_page_woonet-woocommerce-products-network',
					'multistore_page_woonet-woocommerce-products',
					'multistore_page_woonet-woocommerce-products-network',
				)
			)
		) {
			return;
		}

		global $mode;

		if ( isset( $_REQUEST['master_blog_id'] ) ) {
			$blog_id = $_REQUEST['master_blog_id'];
		} elseif ( isset( $_REQUEST['product_blog_id'] ) ) {
			$blog_id = $_REQUEST['product_blog_id'];
		} else {
			die();
		}

		switch_to_blog( intval( $blog_id ) );

		check_ajax_referer( 'inlineeditnonce', '_inline_edit' );

		if ( ! isset( $_POST['post_ID'] ) || ! ( $post_ID = (int) $_POST['post_ID'] ) ) {
			wp_die();
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_ID ) ) {
				wp_die( __( 'Sorry, you are not allowed to edit this page.' ) );
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_ID ) ) {
				wp_die( __( 'Sorry, you are not allowed to edit this post.' ) );
			}
		}

		if ( $last = wp_check_post_lock( $post_ID ) ) {
			$last_user      = get_userdata( $last );
			$last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );
			printf( $_POST['post_type'] == 'page' ? __( 'Saving is disabled: %s is currently editing this page.' ) : __( 'Saving is disabled: %s is currently editing this post.' ), esc_html( $last_user_name ) );
			wp_die();
		}

		$data = &$_POST;

		$post = get_post( $post_ID, ARRAY_A );

		// Since it's coming from the database.
		$post = wp_slash( $post );

		$data['content'] = $post['post_content'];
		$data['excerpt'] = $post['post_excerpt'];

		// Rename.
		$data['user_ID'] = get_current_user_id();

		if ( isset( $data['post_parent'] ) ) {
			$data['parent_id'] = $data['post_parent'];
		}

		// Status.
		if ( isset( $data['keep_private'] ) && 'private' == $data['keep_private'] ) {
			$data['visibility']  = 'private';
			$data['post_status'] = 'private';
		} else {
			$data['post_status'] = $data['_status'];
		}

		if ( empty( $data['comment_status'] ) ) {
			$data['comment_status'] = 'closed';
		}
		if ( empty( $data['ping_status'] ) ) {
			$data['ping_status'] = 'closed';
		}

		// Exclude terms from taxonomies that are not supposed to appear in Quick Edit.
		if ( ! empty( $data['tax_input'] ) ) {
			foreach ( $data['tax_input'] as $taxonomy => $terms ) {
				$tax_object = get_taxonomy( $taxonomy );
				/** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php */
				if ( ! apply_filters( 'quick_edit_show_taxonomy', $tax_object->show_in_quick_edit, $taxonomy, $post['post_type'] ) ) {
					unset( $data['tax_input'][ $taxonomy ] );
				}
			}
		}

		// Hack: wp_unique_post_slug() doesn't work for drafts, so we will fake that our post is published.
		if ( ! empty( $data['post_name'] ) && in_array( $post['post_status'], array( 'draft', 'pending' ) ) ) {
			$post['post_status'] = 'publish';
			$data['post_name']   = wp_unique_post_slug( $data['post_name'], $post['ID'], $post['post_status'], $post['post_type'], $post['post_parent'] );
		}

		// Update the post.
		edit_post();

		// restore_current_blog();

		require_once WOO_MSTORE_PATH . '/multisite/include/class-wc-multistore-network-products-list-table.php';
		$wp_list_table = new WC_Multistore_Network_Products_List_Table();

		$mode = $_POST['post_view'] === 'excerpt' ? 'excerpt' : 'list';

		$item = array(
			'id'         => intval( $_REQUEST['post_ID'] ),
			'post_title' => $_REQUEST['post_title'],
			'date'       => null,
			'blog_id'    => intval( $blog_id ),
		);
		$wp_list_table->display_rows( array( (object) $item ) );

		wp_die();
	}

	function woosl_setup_get_process_list() {
		$site_id = intval( $_POST['site_id'] );

		switch_to_blog( $site_id );

		// get all products
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => '-1',
			'fields'         => 'ids',
		);

		$custom_query = new WP_Query( $args );

		$post_list = $custom_query->get_posts();

		restore_current_blog();

		$response           = array();
		$response['status'] = 'completed';
		$response['data']   = $post_list;

		echo json_encode( $response );
		die();
	}

	function woosl_setup_process_batch() {
		$site_id = intval( $_POST['site_id'] );
		$batch   = (array) $_POST['batch'];

		switch_to_blog( $site_id );

		foreach ( $batch as $post_id ) {
			// check if the product include the required meta fields
			$is_main_product  = get_post_meta( $post_id, '_woonet_network_main_product', true );
			$is_child_product = get_post_meta( $post_id, '_woonet_network_is_child_product_id', true );

			if ( ! empty( $is_child_product ) || ! empty( $is_main_product ) ) {
				continue;
			}

			// add as main product
			update_post_meta( $post_id, '_woonet_network_main_product', 'true' );
		}

		restore_current_blog();

		$response           = array();
		$response['status'] = 'completed';

		echo json_encode( $response );
		die();
	}
}