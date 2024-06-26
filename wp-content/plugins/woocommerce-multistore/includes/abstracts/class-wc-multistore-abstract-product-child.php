<?php
/**
 * Abstract Child Product Handler
 *
 * This handles abstract child product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Product_Child
 */
class WC_Multistore_Abstract_Product_Child {

	public $wc_product;

	public $data;

	public $settings;

	public $site_settings;

	public $duplicate_sku = false;


	public function __construct( $product ) {
		$product = apply_filters( 'wc_multistore_child_product_data', $product );

		if ( is_a( $product, 'WC_Product' ) ) {
			$this->wc_product = wc_get_product($product);
		} else {
			$slave_id = wc_multistore_product_get_slave_product_id( $product['ID'], $product['sku'] );
			if ( $slave_id ) {
				$class_name       = WC_Product_Factory::get_classname_from_product_type( $product['product_type'] );
				$this->wc_product = new $class_name($slave_id);
			} else {
				$class_name       = WC_Product_Factory::get_classname_from_product_type( $product['product_type'] );
				$this->wc_product = new $class_name();
			}

			$this->data = $product;
		}

		$this->site_settings = wc_multistore_get_site_settings();
		$this->load_settings();
		$this->save_settings();
	}

	public function update() {
		$this->settings  = $this->data['settings'];

		if ( $this->has_stock_sync_enabled()  ) {
			$this->wc_product->set_manage_stock( $this->data['manage_stock'] );

			if( isset( $this->data['stock_quantity'] ) ){
				$this->wc_product->set_stock_quantity( $this->data['stock_quantity'] );
			}else{
				$this->wc_product->set_stock_quantity( null );
			}

			$this->wc_product->set_stock_status( $this->data['stock_status'] );
			$this->wc_product->set_low_stock_amount( $this->data['low_stock_amount'] );

			if ( $this->site_settings['child_inherit_changes_fields_control__allow_backorders'] == 'yes' ) {
				$this->wc_product->set_backorders( $this->data['backorders'] );
			}
		}

		if ( $this->wc_product->get_id() == 0 || ( $this->wc_product->get_id() > 0 && $this->has_publish_changes_enabled() )  ) {

			if ( $this->site_settings['child_inherit_changes_fields_control__title'] == 'yes' ) {
				$this->wc_product->set_name( $this->data['post_title'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__slug'] == 'yes' ) {
				$this->wc_product->set_slug( $this->data['slug'] );
			}

			if( isset( $this->data['post_date'] ) ){
				$this->wc_product->set_date_created( $this->data['post_date'] );
			}

			if( isset( $this->data['date_modified'] ) ){
				$this->wc_product->set_date_modified( $this->data['date_modified'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__status'] == 'yes' ) {
				$this->wc_product->set_status( $this->data['post_status'] );
			}else{
				if( $this->wc_product->get_id() == 0 ){
					$this->wc_product->set_status( 'draft' );
				}
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__featured'] == 'yes' ) {
				$this->wc_product->set_featured( $this->data['featured'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__catalogue_visibility'] == 'yes' ) {
				$this->wc_product->set_catalog_visibility( $this->data['catalog_visibility'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__description'] == 'yes' ) {
				$this->wc_product->set_description( $this->data['description'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__short_description'] == 'yes' ) {
				$this->wc_product->set_short_description( $this->data['post_excerpt'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__sku'] == 'yes' ) {
				if( apply_filters( 'wc_multistore_set_sku', true, $this->data, $this->wc_product ) ){
					if( empty($this->data['sku']) && WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' ){
						$this->duplicate_sku = true;
					}

					if( !empty($this->data['sku']) && ! wc_product_has_unique_sku( $this->wc_product->get_id() , $this->data['sku'] ) ){

						$this->duplicate_sku = true;
					}else{
						$this->wc_product->set_sku( $this->data['sku'] );
					}
				}
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__price'] == 'yes' ) {
				$this->wc_product->set_price( $this->data['price'] );
				$this->wc_product->set_regular_price( $this->data['regular_price'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__sale_price'] == 'yes' ) {
				$this->wc_product->set_sale_price( $this->data['sale_price'] );

				if( isset( $this->data['date_on_sale_from'] ) ){
					$this->wc_product->set_date_on_sale_from( $this->data['date_on_sale_from'] );
				}else{
					$this->wc_product->set_date_on_sale_from( '' );
				}

				if( isset( $this->data['date_on_sale_to'] ) ){
					$this->wc_product->set_date_on_sale_to( $this->data['date_on_sale_to'] );
				}else{
					$this->wc_product->set_date_on_sale_to( '' );
				}
			}

			$this->wc_product->set_total_sales( $this->data['total_sales'] );

			$this->wc_product->set_tax_status( $this->data['tax_status'] );

			$this->wc_product->set_tax_class( $this->data['tax_class'] );

			$this->wc_product->set_sold_individually( $this->data['sold_individually'] );

			$this->wc_product->set_weight( $this->data['weight'] );

			$this->wc_product->set_length( $this->data['length'] );

			$this->wc_product->set_width( $this->data['width'] );

			$this->wc_product->set_height( $this->data['height'] );

			if ( $this->site_settings['child_inherit_changes_fields_control__upsell'] == 'yes' ) {
				$this->wc_product->set_upsell_ids( $this->get_upsell_ids() );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__cross_sells'] == 'yes' ) {
				$this->wc_product->set_cross_sell_ids( $this->get_cross_sell_ids() );
			}

			$this->wc_product->set_parent_id( $this->get_parent_id() );

			if ( $this->site_settings['child_inherit_changes_fields_control__purchase_note'] == 'yes' ) {
				$this->wc_product->set_purchase_note( $this->data['purchase_note'] );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__attributes'] == 'yes' ) {
				$this->wc_product->set_attributes( $this->get_attributes() );
			}

			if( $this->site_settings['child_inherit_changes_fields_control__default_variations'] == 'yes' ){
				if( isset( $this->data['default_attributes'] ) ){
					$this->wc_product->set_default_attributes( $this->data['default_attributes'] );
				}else{
					$this->wc_product->set_default_attributes( array() );
				}
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__menu_order'] == 'yes' ) {
				$this->wc_product->set_menu_order( $this->data['menu_order'] );
			}

			if ( apply_filters( 'WOO_MSTORE_SYNC/sync_child/sync_post_password', true ) ) {
				$this->wc_product->set_post_password( $this->data['post_password'] );
			}

			$this->wc_product->set_virtual( $this->data['virtual'] );

			$this->wc_product->set_downloadable( $this->data['downloadable'] );

			if ( $this->site_settings['child_inherit_changes_fields_control__product_cat'] == 'yes' ) {
				$this->wc_product->set_category_ids( $this->get_category_ids() );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__product_tag'] == 'yes' ) {
				$this->wc_product->set_tag_ids( $this->get_tag_ids() );
			}

			$this->wc_product->set_downloads( $this->get_downloads() );

			if( $this->site_settings['child_inherit_changes_fields_control__shipping_class'] == 'yes' ){
				$this->wc_product->set_shipping_class_id($this->get_shipping_class_id());
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__product_image'] == 'yes' ) {
				$this->wc_product->set_image_id( $this->get_image_id() );
			}

			if ( $this->site_settings['child_inherit_changes_fields_control__product_gallery'] == 'yes' ) {
				$this->wc_product->set_gallery_image_ids( $this->get_gallery_image_ids() );
			}

			$this->wc_product->set_download_limit( $this->data['download_limit'] );

			$this->wc_product->set_download_expiry( $this->data['download_expiry'] );

			if( $this->site_settings['child_inherit_changes_fields_control__reviews'] == 'yes' ){
				$this->wc_product->set_reviews_allowed( $this->data['reviews_allowed']);

				if( isset( $this->data['rating_counts'] ) ){
					$this->wc_product->set_rating_counts($this->data['rating_counts']);
				}else{
					$this->wc_product->set_rating_counts(array());
				}

				$this->wc_product->set_review_count($this->data['review_count']);
			}

		}

		$this->set_meta_data();
	}

	public function save() {
		if( $this->duplicate_sku ){
			if( is_multisite() ){
				$site = WOO_MULTISTORE()->sites[get_current_blog_id()];
			}else{
				$site = WOO_MULTISTORE()->site;
			}

			return array(
				'status' => 'failed',
				'message' => 'Failed to sync #'. $this->data['ID'].' to '. $site->get_url() .  ' - Invalid or duplicate sku',
				'code' => '500'
			);
		}

		global $WC_Multistore_Product_Hooks_Master;
		global $WC_Multistore_Stock_Hooks_Child;
		global $WC_Multistore_Stock_Hooks_Master;

		remove_action('woocommerce_update_product', array( $WC_Multistore_Product_Hooks_Master,'update_master_product' ) );
		remove_action( 'woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Child, 'update_child_product_stock' ) );
		remove_action( 'woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Child, 'update_child_variation_stock' ) );
		remove_action( 'woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Master, 'update_child_product_stock' ) );
		remove_action( 'woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Master, 'update_child_variation_stock' ) );

		do_action( 'wc_multistore_before_child_product_save', $this->wc_product, $this->data );

		$this->wc_product->save();

		$this->set_reviews();

		do_action( 'wc_multistore_child_product_saved', $this->wc_product, $this->data );


		add_action('woocommerce_update_product', array( $WC_Multistore_Product_Hooks_Master,'update_master_product' ), 10, 2 );
		add_action( 'woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Child, 'update_child_product_stock' ) );
		add_action( 'woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Child, 'update_child_variation_stock' ) );
		add_action( 'woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Master, 'update_master_product_stock' ) );
		add_action( 'woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Master, 'update_master_variation_stock' ) );

		return $this->get_sync_data();
	}

	public function load_settings() {
		$this->settings = $this->wc_product->get_meta('_woonet_settings' );

		if(!is_array($this->settings)){
			$this->settings = array();
		}
	}

	public function save_settings() {
		if( ! empty( $this->data['settings'] ) ){
			$this->settings = $this->data['settings'];
		}
		$this->wc_product->update_meta_data('_woonet_settings', $this->settings );
	}

	public function set_meta_data() {
		// set child product
		if( WOO_MULTISTORE()->settings['sync-by-sku'] == 'yes' ){
			$this->wc_product->delete_meta_data( '_woonet_network_is_child_product_id');
			$this->wc_product->update_meta_data( '_woonet_network_is_child_product_sku', $this->data['sku'] );
		}else{
			$this->wc_product->delete_meta_data( '_woonet_network_is_child_product_sku');
			$this->wc_product->update_meta_data( '_woonet_network_is_child_product_id', $this->data['ID'] );
		}

		$this->wc_product->update_meta_data( '_woonet_network_is_child_product_url', $this->data['edit_link'] );
		// update meta
		foreach ( $this->data['meta'] as $meta_key => $meta_value ) {
			if( empty( $meta_value )  ){
				$this->wc_product->delete_meta_data( $meta_key );
			}else{
				$this->wc_product->update_meta_data( $meta_key, $meta_value );
			}
		}
	}

	public function set_reviews(){
		if( ! $this->data ){
			return;
		}

		if( $this->site_settings['child_inherit_changes_fields_control__reviews'] != 'yes' ) {
			return;
		}

		$product_reviews = $this->data['reviews'];
		if( empty($product_reviews) ){
			return;
		}

		foreach ( $product_reviews as $product_review){
			$wc_multistore_product_review_child = new WC_Multistore_Product_Review_Child($product_review);
			$wc_multistore_product_review_child->save($this->wc_product->get_id());
		}
	}

	public function get_upsell_ids() {
		$upsells    = $this->data['upsell_ids'];
		$upsell_ids = array();

		if ( ! empty( $upsells ) ) {
			foreach ( $upsells as $upsell_id => $upsell_sku ) {
				$child_upsell_id = wc_multistore_product_get_slave_product_id( $upsell_id, $upsell_sku );
				if( ! empty( $child_upsell_id ) ){
					$upsell_ids[]    = $child_upsell_id;
				}
			}
		}

//		if ( ! empty( $upsells ) ) {
//			foreach ( $upsells as $upsell ) {
//				$type                        = ucfirst( $upsell['product_type'] );
//				$classname                   = 'WC_Multistore_Product_' . $type . '_Child';
//				$wc_multistore_product_child = new $classname( $upsell );
//				$wc_multistore_product_child->update();
//				$child_upsell_data = $wc_multistore_product_child->save();
//				$upsell_ids[]    = $child_upsell_data['id'];
//			}
//		}

		return $upsell_ids;
	}

	public function get_cross_sell_ids() {
		$cross_sells    = $this->data['cross_sell_ids'];
		$cross_sell_ids = array();

		if ( ! empty( $cross_sells ) ) {
			foreach ( $cross_sells as $cross_sell_id => $cross_sell_sku ) {
				$child_cross_sell_id = wc_multistore_product_get_slave_product_id( $cross_sell_id, $cross_sell_sku );
				if( !empty( $child_cross_sell_id ) ){
					$cross_sell_ids[]    = $child_cross_sell_id;
				}
			}
		}


//		if ( ! empty( $cross_sells ) ) {
//			foreach ( $cross_sells as $cross_sell ) {
//				$type                        = ucfirst( $cross_sell['product_type'] );
//				$classname                   = 'WC_Multistore_Product_' . $type . '_Child';
//				$wc_multistore_product_child = new $classname( $cross_sell );
//				$wc_multistore_product_child->update();
//				$child_cross_sell_data = $wc_multistore_product_child->save();
//				$cross_sell_ids[]    = $child_cross_sell_data['id'];
//			}
//		}

		return $cross_sell_ids;
	}

	public function get_parent_id() {
		if( $this->data['post_parent'] == 0 ){
			return $this->data['post_parent'];
		}

		return wc_multistore_product_get_slave_product_id( $this->data['post_parent'], $this->data['sku'] );
	}

	public function get_image_id() {
		if( empty($this->data['image_id']) ){
			return $this->data['image_id'];
		}

		$wc_multistore_image_child = new WC_Multistore_Image_Child($this->data['image_id']);

		return $wc_multistore_image_child->save();
	}

	public function get_gallery_image_ids() {
		$gallery_image_ids = array();
		if ( empty( $this->data['gallery_image_ids'] ) ) {
			return $gallery_image_ids;
		}

		foreach ( $this->data['gallery_image_ids'] as $image ) {
			if ( empty( $image) ) {
				$slave_image_id = '';
			} else {
				$wc_multistore_image_child = new WC_Multistore_Image_Child($image);
				$slave_image_id = $wc_multistore_image_child->save();
			}

			$gallery_image_ids[] = $slave_image_id;

		}

		return $gallery_image_ids;
	}

	public function get_category_ids() {
		if( empty($this->data['category_ids']) ){
			return array();
		}

		$category_ids         = array();
		$product_category_ids = $this->data['category_ids'];
		foreach ( $product_category_ids as $product_category_id => $categories ) {
			foreach ( $categories as $category ) {
				$wc_multistore_child_category = new WC_Multistore_Product_Category_Child( $category );
				$wc_multistore_child_category->update();
			}

			$child_category_id = wc_multistore_get_child_term_id( $product_category_id );
			$category_ids[]    = $child_category_id;
		}

		return $category_ids;
	}

	public function get_tag_ids() {
		if( empty($this->data['tag_ids']) ){
			return array();
		}

		$tag_ids         = array();
		$product_tag_ids = $this->data['tag_ids'];
		foreach ( $product_tag_ids as $tag ) {
			$wc_multistore_product_tag_child = new WC_Multistore_Product_Tag_Child( $tag );
			$wc_multistore_product_tag_child->update();
			$tag_ids[] = $wc_multistore_product_tag_child->term->term_id;
		}

		return $tag_ids;
	}

	public function get_downloads(){
		if( empty($this->data['downloads']) ){
			return array();
		}

		$downloads = $this->data['downloads'];
		$downloads_array = array();
		foreach ($downloads as $download){
			$wc_multistore_product_download_child = new WC_Multistore_Product_Download_Child($download);
			$wc_multistore_product_download_child->save();
			$downloads_array[] = $wc_multistore_product_download_child->wc_download;
		}

		return $downloads_array;
	}

	public function get_shipping_class_id(){
		$shipping_class = $this->data['shipping_class'];
		if( $shipping_class == 0 || $shipping_class == 1 ){
			return $shipping_class;
		}

		$wc_multistore_product_shipping_class_child = new WC_Multistore_Product_Shipping_Class_Child($shipping_class);
		return $wc_multistore_product_shipping_class_child->term->term_id;
	}

	public function get_attributes() {
		if ( empty( $this->data['product_attributes'] ) ) {
			return array();
		}

		$product_attributes_array = array();
		foreach ( $this->data['product_attributes'] as $product_attribute ) {
			if ( empty( $product_attribute ) ) {
				continue;
			}
			$wc_multistore_attribute_child = new WC_Multistore_Product_Attribute_Child( $product_attribute );

			$product_attribute = $wc_multistore_attribute_child->save();
			$product_attributes_array[ $product_attribute->get_name() ] = $product_attribute;
		}

		return $product_attributes_array;
	}

	public function has_publish_enabled(){
		if(is_multisite()){
			$publish_to = '_woonet_publish_to_' . get_current_blog_id();
		}else{
			$publish_to = '_woonet_publish_to_' . WOO_MULTISTORE()->site->get_id();
		}

		if( ! isset( $this->settings[$publish_to] ) ){
			return false;
		}

		if( $this->settings[$publish_to] != 'yes' ){
			return false;
		}

		return true;
	}

	public function has_publish_changes_enabled(){
		if(is_multisite()){
			$inherit = '_woonet_publish_to_' . get_current_blog_id() . '_child_inheir';
		}else{
			$inherit = '_woonet_publish_to_' . WOO_MULTISTORE()->site->get_id() . '_child_inheir';
		}

		if( ! isset( $this->settings[$inherit] ) ){
			return false;
		}

		if( $this->settings[$inherit] != 'yes' ){
			return false;
		}

		return true;
	}


	public function has_stock_sync_enabled(){
		if(is_multisite()){
			$site = WOO_MULTISTORE()->sites[get_current_blog_id()];
		}else{
			$site = WOO_MULTISTORE()->site;
		}

		if( is_multisite() ){
			$stock = '_woonet_' . get_current_blog_id() . '_child_stock_synchronize';
		}else{
			$stock = '_woonet_' . $site->get_id() . '_child_stock_synchronize';
		}

		if( $site->settings['override__synchronize-stock'] == 'yes' ){
			return false;
		}

		if( ! isset( $this->settings[$stock] ) ){
			return false;
		}

		if( $this->settings[$stock] != 'yes' ){
			return false;
		}

		return true;
	}

	public function update_stock($qty) {
		$this->wc_product->set_stock_quantity( $qty );
		$this->save();
	}

	public function get_sync_data(){
		$post_type_object = get_post_type_object( 'product' );
		$data = array(
			'status' => 'success',
			'id' => $this->wc_product->get_id(),
			'sku' => $this->wc_product->get_sku('edit'),
			'edit_link' => admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $this->wc_product->get_id() ) )
		);

		return $data;
	}

	public function get_ajax_stock_data(){
		$data = array();

		if( ! $this->has_stock_sync_enabled() ){
			return $data;
		}

		if( ! $this->has_publish_enabled() ){
			return $data;
		}

		$data['action'] = 'wc_multistore_master_receive_stock';
		$data['stock_quantity'] = $this->wc_product->get_stock_quantity('edit');
		$data['master_product_id'] = $this->wc_product->get_meta('_woonet_network_is_child_product_id');
		$data['master_product_sku'] = $this->wc_product->get_sku('edit');
		$data['blog_id'] =  WOO_MULTISTORE()->site->get_id();
		if( is_multisite() ){
			switch_to_blog( get_site_option('wc_multistore_master_store') );
			$data['ajax_url'] = admin_url( 'admin-ajax.php' );
			restore_current_blog();
		}else{
			$master_data = get_site_option('wc_multistore_master_connect');
			$data['ajax_url'] = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
		}


		return $data;
	}

	public function sync_stock_to_master(){
		if( ! $this->has_publish_enabled() ){
			return;
		}

		if( ! $this->has_stock_sync_enabled() ){
			return;
		}

		if( is_multisite() ){
			$current_site_id = get_current_blog_id();

			switch_to_blog(get_site_option('wc_multistore_master_store'));
			$master_product_id = wc_multistore_product_get_master_product_id( $this->wc_product->get_meta('_woonet_network_is_child_product_id'), $this->wc_product->get_sku() );
			$wc_product = wc_get_product( $master_product_id );
			$classname = wc_multistore_get_product_class_name('master', $wc_product->get_type());
			$wc_multistore_master_product = new $classname($wc_product);
			$wc_multistore_master_product->update_stock( $this->wc_product->get_stock_quantity('edit'), $current_site_id );

			restore_current_blog();
		}else{
			$wc_multistore_stock_api_child = new WC_Multistore_Stock_Api_Child();
			$args = array(
				'master_product_id' => $this->wc_product->get_meta('_woonet_network_is_child_product_id'),
				'master_product_sku' => $this->wc_product->get_sku('edit'),
				'stock_quantity' => $this->wc_product->get_stock_quantity('edit'),
				'blog_id' => WOO_MULTISTORE()->site->get_id(),
			);
			$wc_multistore_stock_api_child->sync_stock_to_master( $args, WOO_MULTISTORE()->site->get_id() );
		}

	}

	public function trash(){
		$id = $this->wc_product->get_id();
		$result = $this->wc_product->delete();

		if( $result ){
			return array(
				'status' => 'success',
				'id' => $id
			);
		}else{
			return array(
				'status' => 'failed',
				'id' => $id,
				'message' => 'Could not trash product'
			);
		}
	}

	public function untrash(){
		$id = $this->wc_product->get_id();
		$result = wp_untrash_post($this->wc_product->get_id());

		if( $result ){
			return array(
				'status' => 'success',
				'id' => $id
			);
		}else{
			return array(
				'status' => 'failed',
				'id' => $id,
				'message' => 'Could not restore product'
			);
		}
	}

	public function delete(){
		global $WC_Multistore_Product_Hooks_Child;
		global $WC_Multistore_Product_Hooks_Master;

		remove_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Child, 'before_delete_post' ), 20 );
		remove_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Master, 'before_delete_post' ), 20 );

		$id = $this->wc_product->get_id();
		$result = $this->wc_product->delete(true);

		add_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Child, 'before_delete_post' ), 20 );
		add_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Master, 'before_delete_post' ), 20 );

		if( $result ){
			return array(
				'status' => 'success',
				'id' => $id
			);
		}else{
			return array(
				'status' => 'failed',
				'id' => $id,
				'message' => 'Could not delete product'
			);
		}

	}

	public function delete_sync_data_from_master(){
		if( is_multisite() ){
			$blog_id = get_current_blog_id();

			switch_to_blog( get_site_option('wc_multistore_master_store') );
			$master_product_id = wc_multistore_product_get_master_product_id( $this->wc_product->get_meta('_woonet_network_is_child_product_id'), $this->wc_product->get_sku() );
			$wc_product = wc_get_product( $master_product_id );
			if( ! $wc_product ){
				restore_current_blog();
				return array(
					'status' => 'failed',
					'id' => $this->wc_product->get_id(),
					'message' => 'Master product not found'
				);
			}
			$classname = wc_multistore_get_product_class_name('master', $wc_product->get_type());
			$wc_multistore_master_product = new $classname($wc_product);
			$result = $wc_multistore_master_product->delete_sync_data( $blog_id );

			restore_current_blog();
		}else{
			$wc_multistore_child_product_api = new WC_Multistore_Product_Api_Child();
			$result = $wc_multistore_child_product_api->send_delete_sync_data_from_master(  $this->wc_product->get_meta('_woonet_network_is_child_product_id'), $this->wc_product->get_meta('_woonet_network_is_child_product_sku') );
		}

		if( $result ){
			return array(
				'status' => 'success',
				'id' => $this->wc_product->get_id()
			);
		}else{
			return array(
				'status' => 'failed',
				'id' => $this->wc_product->get_id(),
				'message' => 'Could not delete product'
			);
		}
	}
}