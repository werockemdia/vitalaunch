<?php
/**
 * Troubleshoot handler.
 *
 * This handles troubleshoot functionality.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Multistore_Settings
 */
class WC_Multistore_Troubleshoot {
	public function __construct(){
		if( WOO_MULTISTORE()->site->get_type() != 'child' ){
			return;
		}
		$this->hooks();
	}

	public function hooks(){
		add_action( 'add_meta_boxes', array($this,'add_multistore_metabox') );
		add_action( 'woocommerce_admin_process_product_object', array($this,'save_multistore_metabox'), 10 );
		add_action( 'woocommerce_product_after_variable_attributes', array($this,'add_multistore_variation_metabox'), 10, 3 );
		add_action( 'woocommerce_admin_process_variation_object', array($this,'save_multistore_variation_metabox'), 10, 2 );
		$this->reset_post();


//		add_filter( 'is_protected_meta', '__return_false', 999 );
//		add_filter('acf/settings/remove_wp_meta_box', '__return_false');

		add_action('init', function (){
			if( isset($_GET['test']) ){
				global $wpdb;
				//$query = "DELETE FROM $wpdb->termmeta WHERE meta_key='_woonet_master_term_id'";
				//$query = "DELETE FROM $wpdb->postmeta WHERE meta_key='_woonet_network_is_child_product_id' AND meta_value='77360'";
				$query = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_woonet_network_is_child_product_id' AND meta_value='77382'";
				//$result = $wpdb->query($query);
				$result = $wpdb->get_results($query);
				echo '<pre>';
				print_r(count($result));
				echo '</pre>';
				echo '<pre>';
				print_r($result);
				echo '</pre>';
			}
		});
	}

	public function reset_post(){
		if( isset($_GET['multistoreresetpost']) ){
			global $wpdb;
			$post = $_GET['post'];
			$query = "DELETE FROM $wpdb->postmeta where post_id = {$post} AND meta_key like '%woonet%'";
			$wpdb->query($query);
		}
	}

	function add_multistore_metabox(){
		add_meta_box(
			'add_product_metabox_multistore',
			__( 'Woomultistore Data', 'woocommerce' ),
			array($this,'multistore_product_metabox_content'),
			'product',
			'normal',
			'high'
		);
	}

	//  Add custom metabox content
	function multistore_product_metabox_content( $post ){
		echo '<div class="panel-wrap">';
		global $product_object;

		woocommerce_wp_text_input(
			array(
				'id'                => "_woonet_network_is_child_product_id",
				'name'              => "_woonet_network_is_child_product_id",
				'value'             => $product_object->get_meta( '_woonet_network_is_child_product_id' ) < 0 ? '' : $product_object->get_meta( '_woonet_network_is_child_product_id' ),
				'label'             => __( 'Multistore Parent product ID', 'woocommerce' ),
				'description'       => __( 'Enter the parent product id.', 'woocommerce' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'wrapper_class'     => 'form-field',
			)
		);
		$current_url    = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url    = remove_query_arg( 'multistoreresetpost' ,$current_url );
		$current_url    = add_query_arg( array('multistoreresetpost' => '1'), $current_url );
		echo '<a href="' . $current_url . '"> reset product multistore data</a>';
		echo '</div>';
	}

	public function save_multistore_metabox($product){
		if( ! empty($_REQUEST['_woonet_network_is_child_product_id']) ){
			$product->update_meta_data( '_woonet_network_is_child_product_id', $_REQUEST['_woonet_network_is_child_product_id'] );
		}
	}

	public function add_multistore_variation_metabox( $loop, $variation_data, $variation ){
		$_woonet_network_is_child_product_id = isset( $variation_data['_woonet_network_is_child_product_id'] ) && $variation_data['_woonet_network_is_child_product_id'][0] < 0 ? '' : $variation_data['_woonet_network_is_child_product_id'][0];

		echo '<div>';
		woocommerce_wp_text_input(
			array(
				'id'                => "variable_woonet_network_is_child_product_id{$loop}",
				'name'              => "variable_woonet_network_is_child_product_id[{$loop}]",
				'value'             => $_woonet_network_is_child_product_id,
				'label'             => __( 'Multistore Parent variation ID', 'woocommerce' ),
				'description'       => __( 'Enter the parent variation id.', 'woocommerce' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'wrapper_class'     => 'form-field',
			)
		);
		echo '</div>';
	}

	public function save_multistore_variation_metabox($variation, $i){
		if( isset($_POST['variable_woonet_network_is_child_product_id'][ $i ]) ){
			$variation->update_meta_data( '_woonet_network_is_child_product_id', wc_clean( wp_unslash( $_POST['variable_woonet_network_is_child_product_id'][ $i ] ) ) );
		}
	}
}