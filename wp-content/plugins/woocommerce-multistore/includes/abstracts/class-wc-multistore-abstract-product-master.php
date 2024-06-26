<?php
/**
 * Abstract Master Product Handler
 *
 * This handles Abstract master product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Variable_Master
 */
class WC_Multistore_Abstract_Product_Master{


	public $depth;

	public $wc_product;

	public $data;

	public $settings;

	public $is_enabled_sync = false;

	/**
	 * Only metadata in the array will be synced by the plugin when sync
	 * all metadata is disabled.
	 *
	 * @since 3.0.4
	 * @var array
	 */
	protected $whitelisted_metadata = array(
//		'_product_version',
		'_wpcom_is_markdown',
//		'_wp_old_slug',
		'_button_text',
	);

	public function __construct( $wc_product, $depth = false ) {
		$this->depth = $depth;
		$this->wc_product = $wc_product;
		$this->load_settings();
		$this->data = $this->get_data();
	}


	public function load_settings(){
		$this->settings = $this->wc_product->get_meta( '_woonet_settings' );

		if(!is_array($this->settings)){
			$this->settings = array();
		}

		foreach ( WOO_MULTISTORE()->active_sites as $site ){
			$publish_to = '_woonet_publish_to_' . $site->get_id();
			$inherit = '_woonet_publish_to_' . $site->get_id() . '_child_inheir';
			$stock = '_woonet_' . $site->get_id() . '_child_stock_synchronize';
			if ( ! empty( $_REQUEST[$publish_to] ) ){
				$this->settings[$publish_to] = $_REQUEST[$publish_to];
			}

			if ( ! empty( $_REQUEST[$inherit] ) ){
				$this->settings[$inherit] = $_REQUEST[$inherit];
			}

			if ( ! empty( $_REQUEST[$stock] ) ){
				$this->settings[$stock] = $_REQUEST[$stock];
			}
			if( isset( $this->settings[$publish_to] ) && $this->settings[$publish_to] == 'yes' ){
				$this->is_enabled_sync = true;
			}
		}

		$this->wc_product->update_meta_data('_woonet_settings', $this->settings );
		$this->wc_product->update_meta_data('_woonet_network_main_product', true );
	}

	public function save_settings(){
		$this->wc_product->update_meta_data('_woonet_settings', $this->settings );
		$this->wc_product->update_meta_data('_woonet_network_main_product', true );
	}

	public function save(){
		global $WC_Multistore_Product_Hooks_Master;
		global $WC_Multistore_Stock_Hooks_Master;
		global $WC_Multistore_Stock_Hooks_Child;

		remove_action('woocommerce_update_product', array( $WC_Multistore_Product_Hooks_Master,'update_master_product' ) );
		remove_action('woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Master,'update_master_product_stock' ) );
		remove_action('woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Master,'update_master_variation_stock' ) );
		remove_action('woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Child,'update_child_product_stock' ) );
		remove_action('woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Child,'update_child_variation_stock' ) );

		$this->wc_product->save();

		add_action('woocommerce_update_product', array( $WC_Multistore_Product_Hooks_Master,'update_master_product' ), 10, 2 );
		add_action('woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Master,'update_master_product_stock' ) );
		add_action('woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Master,'update_master_variation_stock' ) );
		add_action('woocommerce_product_set_stock', array( $WC_Multistore_Stock_Hooks_Child,'update_child_product_stock' ) );
		add_action('woocommerce_variation_set_stock', array( $WC_Multistore_Stock_Hooks_Child,'update_child_variation_stock' ) );
	}


	public function get_data() {
		$data = array();
		$data['_woomulti_version']        = defined( 'WOO_MSTORE_VERSION' ) ? WOO_MSTORE_VERSION : '';
		$data['blog_id']                  = get_current_blog_id();
		$data['_woomulti_sync_init_time'] = time();
		$data['product_type']             = $this->wc_product->get_type();
		$post_type_object                 = get_post_type_object( 'product' );
		$data['edit_link']                = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $this->wc_product->get_id() ) );

		$data['post_type']          = 'product';
		$data['post_status']        = $this->wc_product->get_status('edit');
		$data['post_author']        = get_current_user_id();
		$data['post_title']         = $this->wc_product->get_name('edit');
		$data['post_content']       = $this->wc_product->get_description('edit');
		$data['post_excerpt']       = $this->wc_product->get_short_description('edit');
		$data['post_parent']        = $this->wc_product->get_parent_id('edit');
		$data['comment_status']     = $this->wc_product->get_reviews_allowed() ? 'open' : 'closed';
		$data['ping_status']        = 'closed';
		$data['menu_order']         = $this->wc_product->get_menu_order('edit');
		$data['post_password']      = $this->wc_product->get_post_password( 'edit' );
		$data['post_date']          = ( $this->wc_product->get_date_created( 'edit' ) ) ? gmdate( 'Y-m-d H:i:s', $this->wc_product->get_date_created( 'edit' )->getOffsetTimestamp() ) : '';
		$data['post_date_gmt']      = ( $this->wc_product->get_date_created( 'edit' ) ) ? gmdate( 'Y-m-d H:i:s', $this->wc_product->get_date_created( 'edit' )->getTimestamp() ) : '';
		$data['post_name']          = $this->wc_product->get_slug( 'edit' );

		$data['ID']                 = $this->wc_product->get_id();
		$data['slug']               = $this->wc_product->get_slug('edit');
		$data['date_created']       = ( $this->wc_product->get_date_created( 'edit' ) ) ? gmdate( 'Y-m-d H:i:s', $this->wc_product->get_date_created( 'edit' )->getOffsetTimestamp() ) : '';
		$data['date_modified']      = ( $this->wc_product->get_date_modified( 'edit' ) ) ? gmdate( 'Y-m-d H:i:s', $this->wc_product->get_date_modified( 'edit' )->getOffsetTimestamp() ) : '';
		$data['status']             = $this->wc_product->get_status('edit');
		$data['featured']           = $this->wc_product->get_featured('edit');
		$data['catalog_visibility'] = $this->wc_product->get_catalog_visibility('edit');
		$data['description']        = $this->wc_product->get_description('edit');
		$data['short_description']  = $this->wc_product->get_short_description('edit');
		$data['sku']                = $this->wc_product->get_sku('edit');
		$data['price']              = $this->wc_product->get_price('edit');
		$data['regular_price']      = $this->wc_product->get_regular_price('edit');
		$data['sale_price']         = $this->wc_product->get_sale_price('edit');
		$data['date_on_sale_from']  = $this->wc_product->get_date_on_sale_from( 'edit' ) ? $this->wc_product->get_date_on_sale_from( 'edit' )->getOffsetTimestamp() : false;
		$data['date_on_sale_to']    = $this->wc_product->get_date_on_sale_to( 'edit' ) ? $this->wc_product->get_date_on_sale_to( 'edit' )->getOffsetTimestamp() : false;
		$data['total_sales']        = $this->wc_product->get_total_sales('edit');
		$data['tax_status']         = $this->wc_product->get_tax_status('edit');
		$data['tax_class']          = $this->wc_product->get_tax_class('edit');
		$data['manage_stock']       = $this->wc_product->get_manage_stock('edit');
		$data['stock_quantity']     = $this->wc_product->get_stock_quantity('edit');
		$data['stock_status']       = $this->wc_product->get_stock_status('edit');
		$data['backorders']         = $this->wc_product->get_backorders('edit');
		$data['low_stock_amount']   = $this->wc_product->get_low_stock_amount('edit');
		$data['sold_individually']  = $this->wc_product->get_sold_individually('edit');
		$data['weight']             = $this->wc_product->get_weight('edit');
		$data['length']             = $this->wc_product->get_length('edit');
		$data['width']              = $this->wc_product->get_width('edit');
		$data['height']             = $this->wc_product->get_height('edit');
		$data['upsell_ids']         = $this->get_upsell();
		$data['cross_sell_ids']     = $this->get_crosssell();
		$data['parent_id']          = $this->wc_product->get_parent_id('edit');
		$data['reviews_allowed']    = $this->wc_product->get_reviews_allowed('edit');
		$data['purchase_note']      = $this->wc_product->get_purchase_note('edit');
		$data['product_attributes'] = $this->get_attributes();
		$data['default_attributes'] = $this->wc_product->get_default_attributes('edit');
		$data['menu_order']         = $this->wc_product->get_menu_order('edit');
		$data['post_password']      = $this->wc_product->get_post_password('edit');
		$data['virtual']            = $this->wc_product->get_virtual('edit');
		$data['downloadable']       = $this->wc_product->get_downloadable('edit');
		$data['category_ids']       = $this->get_categories();
		$data['tag_ids']            = $this->get_tags();
		$data['shipping_class']     = $this->get_shipping_class();
		$data['downloads']          = $this->get_downloads();
		$data['image_id']           = $this->get_image();
		$data['gallery_image_ids']  = $this->get_gallery();
		$data['download_limit']     = $this->wc_product->get_download_limit('edit');
		$data['download_expiry']    = $this->wc_product->get_download_expiry('edit');
		$data['rating_counts']      = $this->wc_product->get_rating_counts('edit');
		$data['average_rating']     = $this->wc_product->get_average_rating('edit');
		$data['review_count']       = $this->wc_product->get_review_count('edit');
		$data['reviews']            = $this->get_reviews();


		$data['settings']           = $this->settings;
		$data['meta']               = $this->get_meta();

		$data['wp_uploads_dir']     = wp_get_upload_dir();

		return apply_filters('wc_multistore_master_product_data', $data, $this->wc_product );
	}

	public function get_attributes() {
		$product_attributes       = array();

		if ( empty ( $this->wc_product->get_attributes('edit') ) ) {
			return $product_attributes;
		}

		foreach ( $this->wc_product->get_attributes('edit') as $product_attribute ) {
			if(  empty( $product_attribute ) ){
				continue;
			}

			$wc_multistore_attribute_master = new WC_Multistore_Product_Attribute_Master( $product_attribute );
			$product_attributes[] = $wc_multistore_attribute_master->data;
		}

		return $product_attributes;
	}

	public function get_upsell() {
		$upsells     = array();
		$upsell_ids = $this->wc_product->get_upsell_ids('edit');

		if( empty($upsell_ids) ){
			return $upsells;
		}

		foreach ( $upsell_ids as $upsell_id ){
			$sku = get_post_meta($upsell_id, '_sku' , true);
			$upsells[$upsell_id] = $sku;
		}

		return $upsells;

		if( $this->depth ){
			return $upsells;
		}

		$upsell_ids = $this->wc_product->get_upsell_ids('edit');
		if ( $upsell_ids ) {
			foreach ( $upsell_ids as $upsell_id ) {
				$upsell_product = wc_get_product( $upsell_id );

				$type = ucfirst($upsell_product->get_type());
				$classname = 'WC_Multistore_Product_'. $type . '_Master';
				$wc_multistore_product_master = new $classname( $upsell_product, true );

				$upsells[] = $wc_multistore_product_master->data;
			}
		}

		return $upsells;
	}

	public function get_crosssell() {
		$cross_sells     = array();
		$cross_sell_ids = $this->wc_product->get_cross_sell_ids('edit');

		if( empty($cross_sell_ids) ){
			return $cross_sells;
		}

		foreach ( $cross_sell_ids as $cross_sell_id ){
			$sku = get_post_meta($cross_sell_id, '_sku', true);
			$cross_sells[$cross_sell_id] = $sku;
		}

		return $cross_sells;

		if( $this->depth ){
			return $cross_sells;
		}

		$cross_sell_ids = $this->wc_product->get_cross_sell_ids('edit');
		if ( $cross_sell_ids ) {
			foreach ( $cross_sell_ids as $cross_sell_id ) {
				$cross_sell_product = wc_get_product( $cross_sell_id );

				$type = ucfirst($cross_sell_product->get_type());
				$classname = 'WC_Multistore_Product_'. $type . '_Master';
				$wc_multistore_product_master = new $classname( $cross_sell_product, true );

				$cross_sells[] = $wc_multistore_product_master->data;
			}
		}

		return $cross_sells;
	}

	public function get_shipping_class() {
		$product_shipping_class_id = $this->wc_product->get_shipping_class_id('edit');
		if( $product_shipping_class_id == 0 || $product_shipping_class_id == 1 ){
			return $product_shipping_class_id;
		}
		$term = get_term_by('id', $this->wc_product->get_shipping_class_id('edit'), 'product_shipping_class' );
		$wc_multistore_shipping_class_master = new WC_Multistore_Product_Shipping_Class_Master( $term );
		return $wc_multistore_shipping_class_master->data;
	}

	public function get_tags() {
		$terms       = get_the_terms( $this->wc_product->get_id(), 'product_tag' );
		$terms_array = array();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$wc_multistore_master_tag = new WC_Multistore_Product_Tag_Master( $term );
				$terms_array[] = (array) apply_filters(	'WOO_MSTORE_SYNC/process_json/tag',	(array) $wc_multistore_master_tag->data, $this->wc_product->get_id() );
			}
		}

		return $terms_array;
	}

	public function get_categories() {
		$product_category_ids = $this->wc_product->get_category_ids('edit');
		if( empty( $product_category_ids ) ){
			return $product_category_ids;
		}

		$product_category_ids_ = array();
		foreach ( $product_category_ids as $cat_id ) {
			$cat = get_term($cat_id);
			$ancestors = get_ancestors( $cat_id, 'product_cat' );
			$ancestors = array_reverse( $ancestors );

			if ( ! empty( $ancestors ) ) {
				foreach ( $ancestors as $ancestor_id ) {
					$wc_multistore_master_ancestor_term = new WC_Multistore_Product_Category_Master( get_term( $ancestor_id ) );
					$product_category_ids_[ $cat_id ][] = $wc_multistore_master_ancestor_term->data;
				}
			}

			$wc_multistore_master_term = new WC_Multistore_Product_Category_Master( $cat );

			$product_category_ids_[ $cat->term_id ][] = apply_filters( 'WOO_MSTORE_SYNC/process_json/cat',	$wc_multistore_master_term->data,	$cat->term_id );
		}

		return $product_category_ids_;
	}

	public function get_meta() {
		$_whitelisted_meta = array();

		foreach ( $this->get_whitelisted_meta() as $meta_key ) {
			$value = $this->wc_product->get_meta($meta_key);
			$_whitelisted_meta[$meta_key] = $value;
		}

		return apply_filters( 'WOO_MSTORE_SYNC/process_json/meta', $_whitelisted_meta, $this->wc_product->get_id(), $this->wc_product );
	}

	public function get_whitelisted_meta() {
		return apply_filters('wc_multistore_whitelisted_meta_keys', $this->whitelisted_metadata, $this->wc_product );
	}

	public function get_downloads() {
		$wc_product_downloads =  $this->wc_product->get_downloads('edit');

		if( empty( $wc_product_downloads ) ){
			return $wc_product_downloads;
		}

		$wc_product_downloads_data = array();
		foreach ($wc_product_downloads as $wc_product_download){
			$wc_multistore_product_download_master = new WC_Multistore_Product_Download_Master($wc_product_download);
			$wc_product_downloads_data[] = $wc_multistore_product_download_master->data;
		}


		return $wc_product_downloads_data;
	}

	public function get_image() {
		$image_id = $this->wc_product->get_image_id('edit');
		if( ! $image_id ){
			return $image_id;
		}
		$attachment = get_post( $image_id );

		if( ! $attachment ){
			return $image_id;
		}

		$wc_multistore_image_master = new WC_Multistore_Image_Master($attachment);
		return $wc_multistore_image_master->data;
	}

	public function get_gallery() {
		$gallery        = array();
		$gallery_images = $this->wc_product->get_gallery_image_ids('edit');

		if( empty( $gallery_images) ){
			return $gallery_images;
		}

		foreach ( $gallery_images as $id ) {
			if( ! $id ){
				continue;
			}else{
				$attachment = get_post( $id );

				if( ! $attachment ){
					continue;
				}

				$wc_multistore_image_master = new WC_Multistore_Image_Master($attachment);
				$gallery[] = $wc_multistore_image_master->data;
			}
		}

		return $gallery;
	}

	public function get_reviews() {
		$wp_comments = get_comments( array(	'post_id' => $this->wc_product->get_id() ) );
		if( empty( $wp_comments ) ){
			return false;
		}

		$reviews = array();
		foreach ( $wp_comments as $wp_comment ){
			$wc_multistore_product_review_master = new WC_Multistore_Product_Review_Master($wp_comment);
			$reviews[] = $wc_multistore_product_review_master->data;
		}

		return $reviews;
	}

	public function should_publish_to( $site ){
		$publish_to = '_woonet_publish_to_' . $site;

		if( ! isset( $this->settings[$publish_to] ) ){
			return false;
		}

		return $this->settings[$publish_to] == 'yes';
	}

	public function should_publish_changes_to( $site ){
		$inherit = '_woonet_publish_to_' . $site . '_child_inheir';

		if( ! isset( $this->settings[$inherit] ) ){
			return false;
		}

		return $this->settings[$inherit] == 'yes';
	}

	public function should_sync_stock_to( $site ){
		$sites = WOO_MULTISTORE()->active_sites;

		if( $sites[$site]->settings['override__synchronize-stock'] == 'yes' ){
			return false;
		}

		if( ! $this->should_publish_to($site) ){
			return false;
		}

		$stock = '_woonet_' . $site . '_child_stock_synchronize';

		if( ! isset( $this->settings[$stock] ) ){
			return false;
		}

		if( $this->settings[$stock] != 'yes' ) {
			return false;
		}

		return true;
	}

	public function sync() {
		$sites = WOO_MULTISTORE()->active_sites;
		$results = array();
		foreach ( $sites as $site ) {
			if( $this->should_publish_to( $site->get_id() ) ){
				$results[] = $this->sync_to($site->get_id());
			}
		}

		return $results;
	}

	public function sync_to($site_id) {
		if( $this->should_publish_to( $site_id ) ) {
			if( is_multisite() ){
				switch_to_blog( $site_id );
				$classname = wc_multistore_get_product_class_name( 'child', $this->wc_product->get_type() );
				$wc_multistore_child_product = new $classname( $this->data );
				$wc_multistore_child_product->update();
				$result = $wc_multistore_child_product->save();
				$result['data'] = $result;
				restore_current_blog();
			}else{
				$wc_multistore_product_api_master = new WC_Multistore_Product_Api_Master();
				$response = $wc_multistore_product_api_master->send_product_data_to_child($this->data, $site_id);
				$result = $response;
			}

			return $this->update_children_data($result, $site_id);
		}
	}

	public function update_children_data($result, $site_id){
		if( ! empty( $result['data'] ) ){
			$children_data = $this->get_children_data();
			$children_data[$site_id] = $result['data'];
			$this->wc_product->update_meta_data('_woonet_children_data', $children_data);
			update_post_meta($this->wc_product->get_id(), '_woonet_children_data', $children_data );
		}

		return $result;
	}

	public function update_stock($qty, $site_id){
		if( ! $this->should_sync_stock_to( $site_id ) ){
			return;
		}

		if( ! $this->should_publish_to( $site_id ) ){
			return;
		}

		$this->wc_product->set_stock_quantity($qty);
		$this->save();
		$this->sync_stock( $site_id );
	}

	public function sync_stock( $site_id = false ) {
		$sites = WOO_MULTISTORE()->active_sites;

		if( $site_id ){
			unset( $sites[ $site_id ] );
		}

		foreach ( $sites as $site ) {
			if( $this->should_sync_stock_to( $site->get_id() ) ){
				$this->sync_stock_to( $site->get_id() );
			}
		}
	}

	public function sync_stock_to($site_id) {
		if( $this->should_sync_stock_to( $site_id ) ) {
			if( is_multisite() ){
				switch_to_blog( $site_id );
				$classname                = wc_multistore_get_product_class_name( 'child', $this->wc_product->get_type() );
				$multistore_child_product = new $classname( $this->data );
				$multistore_child_product->update_stock($this->wc_product->get_stock_quantity('edit'));
				restore_current_blog();
			}else{
				$wc_multistore_stock_api_master = new WC_Multistore_Stock_Api_Master();
				$args = array(
					'master_product_id' => $this->data['ID'],
					'master_product_sku' => $this->data['sku'],
					'stock_quantity' => $this->wc_product->get_stock_quantity('edit')
				);
				$wc_multistore_stock_api_master->sync_stock_to( $args, $site_id );
			}


			return array(
				'status' => 'success'
			);
		}
	}

	public function get_children_data(){
		$children_data = get_post_meta($this->wc_product->get_id(), '_woonet_children_data', true);
		if( empty($children_data) ){
			$children_data = array();
		}
		return $children_data;
	}

	public function get_ajax_stock_data(){
		$sites = WOO_MULTISTORE()->active_sites;
		$data = array();

		foreach ( $sites as $site ){
			if( $this->should_sync_stock_to( $site->get_id() ) ) {
				$product_data = array();
				$product_data['action'] = 'wc_multistore_child_receive_stock';
				$product_data['stock_quantity'] = $this->wc_product->get_stock_quantity('edit');
				$product_data['master_product_id'] = $this->wc_product->get_id();
				$product_data['master_product_sku'] = $this->wc_product->get_sku('edit');
				$product_data['master_blog_id'] =  get_current_blog_id();
				$product_data['blog_id'] =  $site->get_id();
				$product_data['ajax_url'] = $site->get_url().'/wp-admin/admin-ajax.php';
				$data[] = $product_data;
			}
		}

		return $data;
	}

	public function trash_children(){
		$sites = WOO_MULTISTORE()->active_sites;
		foreach ( $sites as $site ) {
			if( $this->should_publish_to( $site->get_id() ) ){
				$this->trash_to($site->get_id());
			}
		}
	}

	public function trash_to($site_id) {
		if( $this->should_publish_to( $site_id ) ) {
			if( is_multisite() ){
				switch_to_blog( $site_id );
				$classname                = wc_multistore_get_product_class_name( 'child', $this->wc_product->get_type() );
				$multistore_child_product = new $classname( $this->data );
				$result = $multistore_child_product->trash();
				restore_current_blog();
			}else{
				$wc_multistore_product_api_master = new WC_Multistore_Product_Api_Master();
				$response = $wc_multistore_product_api_master->send_trash_product_data_to_child($this->data, $site_id);
				$result = $response['data'];
			}

			return $result;
		}
	}

	public function untrash_children(){
		$sites = WOO_MULTISTORE()->active_sites;
		foreach ( $sites as $site ) {
			if( $this->should_publish_to( $site->get_id() ) ){
				$this->untrash_to($site->get_id());
			}
		}
	}

	public function untrash_to($site_id) {
		if( $this->should_publish_to( $site_id ) ) {
			if( is_multisite() ){
				switch_to_blog( $site_id );
				$classname                = wc_multistore_get_product_class_name( 'child', $this->wc_product->get_type() );
				$multistore_child_product = new $classname( $this->data );
				$result = $multistore_child_product->untrash();
				restore_current_blog();
			}else{
				$wc_multistore_product_api_master = new WC_Multistore_Product_Api_Master();
				$response = $wc_multistore_product_api_master->send_untrash_product_data_to_child($this->data, $site_id);
				$result = $response['data'];
			}

			return $result;
		}
	}

	public function delete_children(){
		$sites = WOO_MULTISTORE()->active_sites;
		foreach ( $sites as $site ) {
			if( $this->should_publish_to( $site->get_id() ) ){
				$this->delete_to($site->get_id());
			}
		}
	}

	public function delete_to($site_id) {
		if( $this->should_publish_to( $site_id ) ) {
			if( is_multisite() ) {
				global $WC_Multistore_Product_Hooks_Child;
				global $WC_Multistore_Product_Hooks_Master;
				remove_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Child, 'before_delete_post' ), PHP_INT_MAX );
				remove_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Master, 'before_delete_post' ), 20 );

				switch_to_blog( $site_id );
				$classname                  = wc_multistore_get_product_class_name( 'child', $this->wc_product->get_type() );
				$multistore_child_product   = new $classname( $this->data );
				$result         = $multistore_child_product->delete();
				restore_current_blog();

				global $WC_Multistore_Product_Hooks_Child;
				add_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Child, 'before_delete_post' ), PHP_INT_MAX );
				add_action( 'before_delete_post', array( $WC_Multistore_Product_Hooks_Master, 'before_delete_post' ), 20 );
			}else{
				$args = array(
					'product_id' => $this->wc_product->get_id(),
					'product_sku' => $this->wc_product->get_sku('edit'),
				);
				$wc_multistore_product_api_master = new WC_Multistore_Product_Api_Master();
				$response = $wc_multistore_product_api_master->send_delete_product_data_to_child($args,$site_id);
				$result = $response['data'];
			}

			return $result;
		}
	}

	public function delete_sync_data($site_id) {
		$children_data = $this->wc_product->get_meta('_woonet_children_data');
		if( is_array($children_data) ){
			unset($children_data[$site_id]);
			update_post_meta($this->wc_product->get_id(), '_woonet_children_data', $children_data );
		}
	}

	public function set_ajax_transient($transient_name) {
		$data = get_transient( $transient_name);
		if( ! $data ){
			$data = array();
		}

		$data[$this->wc_product->get_id()] = array();
		$data[$this->wc_product->get_id()]['master_blog'] = get_current_blog_id();
		$data[$this->wc_product->get_id()]['sku'] = $this->wc_product->get_sku('edit');
		$data[$this->wc_product->get_id()]['thumbnail'] = $this->wc_product->get_image_id('edit');
		$data[$this->wc_product->get_id()]['name'] = $this->wc_product->get_name('edit');

		foreach ( WOO_MULTISTORE()->active_sites as $site ){
			if($this->should_publish_to($site->get_id())){
				$data[$this->wc_product->get_id()]['sites'][] = $site->get_id();
			}
		}


		if ( set_transient( $transient_name, $data, 4 * HOUR_IN_SECONDS ) ) {
			return true;
		}
	}

	public function set_scheduler_transient($action) {
		$data = get_transient( $action);
		if( ! $data ){
			$data = array();
		}

		$data[$this->wc_product->get_id()] = array();
		$data[$this->wc_product->get_id()]['master_blog'] = get_current_blog_id();
		$data[$this->wc_product->get_id()]['sku'] = $this->wc_product->get_sku('edit');
		$data[$this->wc_product->get_id()]['thumbnail'] = $this->wc_product->get_image_id('edit');
		$data[$this->wc_product->get_id()]['name'] = $this->wc_product->get_name('edit');


		if ( set_transient( $action, $data, 4 * HOUR_IN_SECONDS ) ) {
			return true;
		}
	}

	public function set_scheduler($hook) {
		$scheduled_actions = as_get_scheduled_actions(
			array(
				'hook'   => $hook,
				'args'   => array( 'id' => $this->wc_product->get_id(), 'sku' => $this->wc_product->get_sku('edit') ),
				'group'  => 'WooMultistore Product Sync',
				'status' => ActionScheduler_Store::STATUS_PENDING,
			),
			'ids'
		);

		if ( count( $scheduled_actions ) >= 1 ) {
			return;
		}

		as_enqueue_async_action($hook, array( $this->wc_product->get_id() ),'WooMultistore Product Sync' );
	}
}