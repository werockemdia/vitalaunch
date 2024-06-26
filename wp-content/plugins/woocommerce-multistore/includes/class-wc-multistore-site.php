<?php
/**
 * Site handler.
 *
 * This handles site functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Site
 */
class WC_Multistore_Site {

	/**
	 * @var int|mixed
	 */
	private $id;

	/**
	 * @var mixed|string|void
	 */
	private $name;

	/**
	 * @var mixed|string|void
	 */
	private $url;

	/**
	 * @var mixed|string|void
	 */
	private $type;

	/**
	 * @var array|object|string[]
	 */
	public $settings;

	/**
	 * @var
	 */
	private $is_active;


	private $date_added;

	/**
	 * @var
	 */
	private $messages;

	/**
	 * @var string[]
	 */
	private $defaults =  array(
		// Product
		'child_inherit_changes_fields_control__title'                           => 'yes',
		'child_inherit_changes_fields_control__description'                     => 'yes',
		'child_inherit_changes_fields_control__short_description'               => 'yes',
		'child_inherit_changes_fields_control__price'                           => 'yes',
		'child_inherit_changes_fields_control__product_tag'                     => 'yes',
		'child_inherit_changes_fields_control__default_variations'              => 'yes', // default attributes ( wrong name )
		'child_inherit_changes_fields_control__reviews'                         => 'yes',
		'child_inherit_changes_fields_control__slug'                            => 'yes',
		'child_inherit_changes_fields_control__purchase_note'                   => 'yes',
		'child_inherit_changes_fields_control__status'                          => 'yes',
		'child_inherit_changes_fields_control__featured'                        => 'yes',
		'child_inherit_changes_fields_control__catalogue_visibility'            => 'yes',
		'child_inherit_changes_fields_control__sale_price'	                    => 'yes',
		'child_inherit_changes_fields_control__sku'                             => 'yes',
		'child_inherit_changes_fields_control__product_image'                   => 'yes',
		'child_inherit_changes_fields_control__product_gallery'                 => 'yes',
		'child_inherit_changes_fields_control__allow_backorders'                => 'yes',
		'child_inherit_changes_fields_control__menu_order'                      => 'yes',
		'child_inherit_changes_fields_control__shipping_class'                  => 'yes',
		'child_inherit_changes_fields_control__upsell'                          => 'no',
		'child_inherit_changes_fields_control__cross_sells'                     => 'no',

		// Attributes
		'child_inherit_changes_fields_control__attributes'                      => 'yes',
		'child_inherit_changes_fields_control__attribute_name'                  => 'yes',
		'child_inherit_changes_fields_control__attribute_term_name'             => 'yes',
		'child_inherit_changes_fields_control__attribute_term_slug'             => 'yes',
		'child_inherit_changes_fields_control__attribute_term_description'      => 'yes',

		// Variations
		'child_inherit_changes_fields_control__variations'                      => 'yes',
		'child_inherit_changes_fields_control__variations_data'                 => 'yes',
		'child_inherit_changes_fields_control__variations_sku'                  => 'yes',
		'child_inherit_changes_fields_control__variations_status'               => 'yes',
		'child_inherit_changes_fields_control__variations_stock'                => 'yes',
		'child_inherit_changes_fields_control__variations_price'                => 'yes',
		'child_inherit_changes_fields_control__variations_sale_price'           => 'yes',

		// Product Category
		'child_inherit_changes_fields_control__product_cat'                     => 'yes',
		'child_inherit_changes_fields_control__category_slug'                   => 'yes',
		'child_inherit_changes_fields_control__category_name'                   => 'yes',
		'child_inherit_changes_fields_control__category_description'            => 'no',
		'child_inherit_changes_fields_control__category_image'                  => 'no',
		'child_inherit_changes_fields_control__category_meta'                   => 'yes',

		// Rest API
		'child_inherit_changes_fields_control__synchronize_rest_by_default'     =>  'yes',

		// Import order
		'child_inherit_changes_fields_control__import_order'                    => 'yes',

		// Stock
		'override__synchronize-stock'                                           => 'no',
	);

	public function __construct( $data = false ){
		if( $data ){
			$this->set_id( $data['id'] );
		}

		$this->load( $data );
	}

	public function set_id( $id ){
		$this->id = $id;
	}

	private function load( $data ){
		if( ! $data ){
			if( is_multisite() ){
				$data = get_option( 'wc_multistore_site', array() );
				$master_store = get_site_option( 'wc_multistore_master_store', array() );
				if( $master_store == get_current_blog_id() ){
					$data['type'] = 'master';
				}else{
					$data['type'] = 'child';
				}

				if( empty($data['id']) ){
					$this->set_id( get_current_blog_id() );
				}else{
					$this->set_id( $data['id'] );
				}
			}else{
				$data = array();
				$type = get_option('wc_multistore_network_type');
				if( $type == 'master' ){
					$this->set_id('master');
				}else{
					$child_data = get_option('wc_multistore_master_connect');
					if( empty($child_data) ){
						$this->set_id('');
					}else{
						$this->set_id($child_data['id']);
					}
				}
				$site_data = get_option( 'wc_multistore_site' );
				$data['name'] = get_bloginfo('url');
				$data['type'] = $type;
				$data['url'] = get_bloginfo('url');

				if( empty($site_data['settings']) ){
					$data['settings'] = $this->defaults;
				}else{
					$data['settings'] = $site_data['settings'];
				}
			}
		}

		// Set name
		if( ! empty( $data['name'] ) ){
			$this->set_name($data['name']);
		}else{
			if( is_multisite() ){
				switch_to_blog($this->id);
				$this->set_name( get_bloginfo('name') );
				restore_current_blog();
			}else{
				$this->set_name( $data['url'] );
			}
		}

		// Set type
		if( ! empty( $data['type'] ) ){
			$this->set_type($data['type']);
		}else{
			$this->set_type( '' );
		}

		// Set url
		if( ! empty( $data['url'] ) ){
			$this->set_url($data['url']);
		}else{
			if( is_multisite() ){
				switch_to_blog($this->id);
				$this->set_url( get_bloginfo('url') );
				restore_current_blog();
			}else{
				$this->set_url( get_bloginfo('url') );
			}
		}

		// Set settings
		if( ! empty( $data['settings'] ) ){
			$this->set_settings($data['settings']);
		}else{
			$this->set_settings( $this->defaults );
		}

		if( ! empty( $data['is_active'] ) ){
			$this->set_is_active($data['is_active']);
		}else{
			$this->set_is_active( 'yes' );
		}

		if( ! empty( $data['date_added'] ) ){
			$this->set_date_added($data['date_added']);
		}
	}

	public function set_name( $name ){
		$this->name = $name;
	}

	public function set_type($type){
		$this->type = $type;
	}


	public function set_url( $url ){
		$this->url = $url;
	}


	public function set_settings( $settings ){
		$settings = wp_parse_args( $settings, $this->defaults );

		$this->settings = $settings;
	}

	public function set_is_active( $is_active ){
		$this->is_active = $is_active;
	}

	public function set_date_added( $date ){
		$this->date_added = $date;
	}

	public function save(){
		$site_data = $this->get_data();

		WOO_MULTISTORE()->sites[$this->id] = $this;
		if( is_multisite() ){
			switch_to_blog($this->id);
			update_option('wc_multistore_site', $site_data );
			restore_current_blog();
		}else{
			if( WOO_MULTISTORE()->site->get_type() == 'master' ){
				WOO_MULTISTORE()->sites[$this->id] = $this;
				$sites = get_site_option('wc_multistore_sites');
				$sites[$this->id] = $this->get_data();
				update_site_option('wc_multistore_sites', $sites);
			}else{
				update_site_option('wc_multistore_site', $site_data);
			}

		}

		do_action('wc_multistore_site_saved', $site_data, $this );
	}

	public function deactivate(){
		$sites = get_site_option('wc_multistore_sites');
		$sites[$this->get_id()]['is_active'] = 'no';
		WOO_MULTISTORE()->sites[$this->get_id()]->set_is_active('no');
		update_site_option('wc_multistore_sites', $sites);
	}

	public function activate(){
		$sites = get_site_option('wc_multistore_sites');
		$sites[$this->get_id()]['is_active'] = 'yes';
		WOO_MULTISTORE()->sites[$this->get_id()]->set_is_active('yes');
		update_site_option('wc_multistore_sites', $sites);
	}

	public function delete(){
		$sites = get_site_option('wc_multistore_sites');
		unset(WOO_MULTISTORE()->sites[$this->get_id()]);
		unset($sites[$this->get_id()]);

		update_site_option('wc_multistore_sites', $sites );
	}

	public function get_id(){
		return $this->id;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_type(){
		return $this->type;
	}

	public function get_url(){
		return rtrim( $this->url, '/' );
	}

	public function get_settings(){
		return $this->settings;
	}

	public function get_data(){
		return array(
			'id' => $this->id,
			'name' => $this->name,
			'url'  => $this->url,
			'is_active' => $this->is_active,
			'type'  => $this->type,
			'settings'  => $this->settings,
			'date_added'  => $this->date_added,
		);
	}

	public function is_active(){
		return $this->is_active != 'no';
	}

	public function get_date_added(){
		if(!empty($this->date_added)){
			return date( 'Y/m/d', $this->date_added );
		}
		return $this->date_added;
	}

	public function is_woocommerce_active(){
		$is_woocommerce_active = false;

		if( is_multisite() ){
			switch_to_blog( $this->id );
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_woocommerce_active = true;
		}

		if( is_multisite() ){
			restore_current_blog();
		}

		return $is_woocommerce_active;
	}

	public function reset(){
		global $wpdb;

		if( $this->get_type() == 'master' ){
			WOO_MULTISTORE()->license->deactivate();

			if (is_multisite()){

			}else{

			}
		}else{
			if (is_multisite()){

			}else{

			}
		}
	}
}
