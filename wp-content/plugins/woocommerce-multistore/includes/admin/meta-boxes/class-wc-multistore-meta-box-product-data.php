<?php
/**
 * Product Data
 *
 * Displays the product data box.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Multistore_Meta_Box_Product_Data Class.
 */
class WC_Multistore_Meta_Box_Product_Data {

	protected $sites;

	protected $settings;
	
	protected $child_fields;
	
	protected $master_fields;

	public function __construct(){
		$this->sites = WOO_MULTISTORE()->active_sites;
		$this->settings = WOO_MULTISTORE()->settings;
		if( ! wc_multistore_min_user_role() ){ return; }
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }

		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_multistore_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_multistore_panel' ) );
		wp_enqueue_style( 'wc-multistore-product-css', WOO_MSTORE_URL . '/assets/css/wc-multistore-product.css' );
		wp_enqueue_script( 'wc-multistore-product-js', WOO_MSTORE_URL . '/assets/js/wc-multistore-product.js', array( 'jquery' ) );
	}

	/**
	 * Multistore Panel
	 */
	public function add_multistore_tab() {
		printf(
			'<li class="woonet_tab"><a href="#woonet_data" rel="woonet_data"><span>%s</span></a></li>',
			__( 'Select Vendor', 'woonet' )
		);
	}

	public function add_multistore_panel() {
		global $product_object;

		$this->child_fields = $this->get_child_fields();
		$this->master_fields = $this->get_master_fields();

		if( WOO_MULTISTORE()->site->get_type() == 'master' ){
			require_once WOO_MSTORE_SINGLE_INCLUDES_PATH.'admin/meta-boxes/views/html-product-data-master.php';
		}else{
			if( wc_multistore_is_child_product($product_object) ){
				require_once WOO_MSTORE_SINGLE_INCLUDES_PATH.'admin/meta-boxes/views/html-product-data-child.php';
			}
		}
	}

	public function get_child_fields(){
		global $product_object;
		$parent_link = '';

		if( $product_object->get_meta('_woonet_network_is_child_product_url') ){
			$parent_url = $product_object->get_meta('_woonet_network_is_child_product_url');
			$parent_link = 'This product is a child product. Only parent products can be synced to other sites. <a href="'.$parent_url.'" target="_blank">Parent product</a>';
		}

		$fields[] = array(
			'id'      => '_woonet_title',
			'label'   => '&nbsp;',
			'type'    => 'heading',
			'no_save' => true,
		);

		$fields[] = array(
			'class'   => '_woonet_description inline',
			'label'   => __( $parent_link, 'woonet' ),
			'type'    => 'description',
			'no_save' => true,
		);

		$fields[] = array(
			'id'      => '_woonet_title',
			'label'   => '&nbsp;',
			'type'    => 'heading',
			'no_save' => true,
		);

		return $fields;
	}

	public function get_master_fields(){
		global $product_object;

		$settings = $product_object->get_meta('_woonet_settings');

		$woonet_toggle_all_sites = $this->settings['synchronize-by-default'];
		if( $product_object->get_meta('_woonet_network_main_product', true ) ){
			$woonet_toggle_all_sites = 'no';
		}
		$fields[] = array(
			'id'          => 'woonet_toggle_all_sites',
			'class'       => 'woonet_toggle_all_sites inline',
			'label'       => '',
			'description' => __( 'Toggle all Sites', 'woonet' ),
			'type'        => 'checkbox',
			'checked'     => $woonet_toggle_all_sites == 'yes',
			'value'       => 'yes',
			//'no_save'     => true,
		);


		$woonet_child_product_inherit_by_default = $this->settings['inherit-by-default'];
		if( $product_object->get_meta('_woonet_network_main_product', true ) ){
			$woonet_child_product_inherit_by_default = 'no';
		}
		$fields[] = array(
			'id'          => 'woonet_toggle_child_product_inherit_updates',
			'class'       => '_woonet_child_inherit_updates inline',
			'label'       => '',
			'description' => __( 'Toggle all Child product inherit Parent products changes', 'woonet' ),
			'type'        => 'checkbox',
			'checked'     => $woonet_child_product_inherit_by_default == 'yes',
			'value'       => '',
			'no_save'     => true,
		);

		$woonet_synchronize_stock_by_default = $this->settings['synchronize-stock'];
		$info_span = '';
		if($woonet_synchronize_stock_by_default == 'yes'){
			$info_span = '<span class="tips" data-tip="Stock fields are disabled when always maintain stock synchronization for re-published products is enabled. You can disable this on general settings page."><i class="dashicons dashicons-warning wc-multistore-warning-tip"></i></span>';
		}
		$fields[] = array(
			'id'          => 'woonet_toggle_stock_updates',
			'class'       => 'woonet_toggle_stock_to inline ',
			'label'       => '',
			'description' => __( 'Toggle all Synchronize stock', 'woonet' ) . $info_span,
			'type'        => 'checkbox',
			'checked'     => $woonet_synchronize_stock_by_default == 'yes',
			'disabled'    => $woonet_synchronize_stock_by_default == 'yes',
			'value'       => '',
			'no_save'     => true,
		);

		$fields[] = array(
			'id'      => '_woonet_title',
			'label'   => __( 'Publish to', 'woonet' ),
			'type'    => 'heading',
			'no_save' => true,
		);


		foreach ( $this->sites as $site ) {
			$publish_to        = ( ! empty($settings) && isset($settings['_woonet_publish_to_' . $site->get_id()]) && $settings['_woonet_publish_to_' . $site->get_id()] == 'yes' );

			$fields[] = array(
				'id'                => '_woonet_publish_to_' . $site->get_id(),
				'class'             => '_woonet_publish_to inline',
				'label'             => '',
				'description'       => '<b>' . $site->get_name() . '</b><span class="warning">' . __( '<b>Notice:</b> Shop Deselected.', 'woonet' ) . '</span>',
				'type'              => 'checkbox',
				'disabled'          => '',
				'checked'           => $publish_to,
				'custom_attribute'  => 'data-group-id=' . $site->get_id(),
				'save_callback'     => array( $this, 'field_process_publish_to' ),
			);

			$inherit_class = ' ';
			if ( ! $publish_to ) {
				$inherit_class .= 'default_hide';
			}

			$_woonet_child_inherit_updates   = ( ! empty($settings) && isset($settings['_woonet_publish_to_' . $site->get_id() . '_child_inheir']) && $settings['_woonet_publish_to_' . $site->get_id() . '_child_inheir'] == 'yes' );
			$fields[] = array(
				'id'          => '_woonet_publish_to_' . $site->get_id() . '_child_inheir',
				'class'       => 'group_' . $site->get_id() . ' _woonet_publish_to_child_inheir inline indent' . $inherit_class,
				'label'       => '',
				'description' => __( 'Child product inherit Parent products changes', 'woonet' ),
				'type'        => 'checkbox',
				'value'       => 'yes',
				'checked'     => $_woonet_child_inherit_updates,
				'disabled'    => '',
				'no_save'     => true,
			);

			$_woonet_child_stock_synchronize = ( ! empty($settings) && isset($settings['_woonet_' . $site->get_id() . '_child_stock_synchronize']) && $settings['_woonet_' . $site->get_id() . '_child_stock_synchronize'] == 'yes' );
			$fields[] = array(
				'id'          => '_woonet_' . $site->get_id() . '_child_stock_synchronize',
				'class'       => '_woonet_sync_stock group_' . $site->get_id() . ' _woonet_child_stock_synchronize inline indent ',
				'label'       => '',
				'description' => __( 'If checked, any stock change will synchronize across product tree.', 'woonet' ),
				'type'        => 'checkbox',
				'value'       => 'yes',
				'checked'     => $woonet_synchronize_stock_by_default == 'yes' || $_woonet_child_stock_synchronize,
				'disabled'    => $woonet_synchronize_stock_by_default == 'yes',
				'no_save'     => true,
			);
		}

		return apply_filters( 'WOO_MSTORE_admin_product\define_fields\product_fields', $fields );
	}

}