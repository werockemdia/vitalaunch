<?php

@ini_set( 'upload_max_size' , '64M' );

@ini_set( 'post_max_size', '64M');

@ini_set( 'max_execution_time', '300' );

if ( !defined( 'WP_DEBUG' ) ) {
	die( 'Direct access forbidden.' );
}

add_action( 'wp_enqueue_scripts', 'suxnix_child_enqueue_styles', 99 );

function suxnix_child_enqueue_styles() {
   wp_enqueue_style( 'parent-style', get_stylesheet_directory_uri() . '/style.css' );
   
  // wp_enqueue_script( 'my-custom-script',  'https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js', array('jquery'), null, true);
   // wp_enqueue_script( 'my-custom-script',  'http://code.jquery.com/jquery-latest.js', array('jquery'), null, true);
  /*?>
  <script>
   setTimeout(function() {
            alert();   
   
    <?php echo  wp_enqueue_script( 'my-custom-script',  'https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js', array('jquery'), null, true);
     echo wp_enqueue_script( 'my-custom-script',  'http://code.jquery.com/jquery-latest.js', array('jquery'), null, true); ?>
  }, 12000);
  
 
   </script>
<?php   
*/ }


/**
 * @snippet       Hide Products From Specific Category @ Shop
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.6.3
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  
add_action( 'woocommerce_product_query', 'bbloomer_hide_products_category_shop' );
   
function bbloomer_hide_products_category_shop( $q ) {
  
    $tax_query = (array) $q->get( 'tax_query' );
  
    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( 'subcriptions' ), // Category slug here
           'operator' => 'NOT IN'
    );
  
  
    $q->set( 'tax_query', $tax_query );
  
}

/**
 * @snippet       WooCommerce Max 1 Product @ Cart
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WCooCommerce 7
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  
add_filter( 'woocommerce_add_to_cart_validation', 'bbloomer_only_one_in_cart', 9999 );
   
function bbloomer_only_one_in_cart( $passed ) {
   wc_empty_cart();
   return $passed;
}



add_action( 'init', 'add_dropshipper_board_account_endpoint' );
function add_dropshipper_board_account_endpoint() {
    add_rewrite_endpoint( 'dropshipper-board', EP_PAGES );
    add_rewrite_endpoint( 'dropshipper-order', EP_PAGES );
    
  //  echo $_SESSION['prod_det'];
   
}

add_filter ( 'woocommerce_account_menu_items', 'custom_account_menu_items', 10 );
function custom_account_menu_items( $menu_links ){
    if ( current_user_can('dropshipper') ) {
        $menu_links = array_slice( $menu_links, 0,3 , true )
        + array( 'dropshipper-board' => __('Dropshipper Board'),  'dropshipper-order' => __('Dropshipper Order'))
        + array_slice( $menu_links, 3, NULL, true );
    }
    return $menu_links;
}

add_action( 'woocommerce_account_dropshipper-board_endpoint', 'dropshipper_board_account_endpoint_content' );
function dropshipper_board_account_endpoint_content() {
    if ( current_user_can('dropshipper') ) {
        
        
        // Add the widget for dropshippers
				$current_user = wp_get_current_user()->user_login;
				$product_count = 0;
				$total_sales = 0;
				$orders_processing = 0;
				$orders_completed = 0;
				$table_string = '';
				$query = new WP_Query( array(
						'post_type' => 'product',
						'meta_key' => 'woo_dropshipper',
						'meta_query' => array(
							array(
								'key' => 'woo_dropshipper',
								'value' => $current_user,
								'compare' => '=',
							)
						),
						'posts_per_page' => -1
					)
				);
				// The Loop
				if ( $query->have_posts() ) {
					$product_count = $query->post_count;
					while ( $query->have_posts() ) {
						$variations_string = '<strong>'. __('No Options', 'woocommerce-dropshippers') .'</strong>';
						$query->the_post();
						$price = get_post_meta( get_the_ID(), '_sale_price', true);
						$product_sales = (int)get_post_meta(get_the_ID(), 'total_sales', true);
						$total_sales += $product_sales;
						$prod = wc_get_product(get_the_ID());
						$url = get_permalink(get_the_ID());
						//var_dump($prod->get_attributes());
						$product_type = '';
						if(method_exists('WC_Product_Factory', 'get_product_type')){
							$product_type = WC_Product_Factory::get_product_type(get_the_ID());
						}
						else{
							$product_type = $prod->product_type;
						}
						if($product_type == 'variable'){
							$variations_string = '';
							$attrs = $prod->get_variation_attributes();
							if( is_array( $attrs ) && count( $attrs ) > 0 ) {
								foreach ($attrs as $key => $value) {
									$variations_string .= '<strong>' . $key . '</strong>';
									foreach ($value as $val) {
										$variations_string .= '<br/>&ndash; '. $val;
									}
									$variations_string .= "<br/>\n";
								}
							}
						}
						$table_string .= '<tr class="alternate" style="padding: 4px 7px 2px;">';
						$table_string .= '<td class="column-columnname" style="padding: 4px 7px 2px;"><strong>' . get_the_title() . '</strong><div class="row-actions"><span><a href="'.$url.'">'. __('Product Page', 'woocommerce-dropshippers') .'</a></span></div></td>';
						$table_string .= '<td class="column-columnname" style="padding: 4px 7px 2px;">' . $variations_string . '</td>';
						$table_string .= '<td class="column-columnname" style="padding: 4px 7px 2px;"> x' . $product_sales . '</td>';
						$table_string .= '</tr>';
					}
				} else {
					// no posts found
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				$woo_ver = woocommerce_dropshipper_get_woo_version_number();
				if($woo_ver >= 2.2){
					$query = new WP_Query(
						array(
							'post_type' => 'shop_order',
							'post_status' => array( 'wc-processing', 'wc-completed' ),
							'posts_per_page' => -1
						)
					);
				}
				else{
					$query = new WP_Query(
						array(
							'post_type' => 'shop_order',
							'post_status' => 'publish',
							'posts_per_page' => -1
						)
					);
				}

				// The Loop
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						/* actual product list of the dropshipper */
						$real_products = array();
						$query->the_post();
						$order = new WC_Order(get_the_ID());

						foreach ($order->get_items() as $item) {
							if(get_post_meta( $item["product_id"], 'woo_dropshipper', true) == $current_user){
								array_push($real_products, $item);
								break;
							}
						}
						if( (sizeof($real_products) > 0) && ($order->get_status() == "completed") ){
							$orders_completed++;
						}
						if( (sizeof($real_products) > 0) && ($order->get_status() == "processing") ){
							$orders_processing++;
						}
					}
				}
				else {
					// no posts found
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				
				?>
				<style>
				    .table_design table{
				        width:100%;
				    }
				    .table_design table tbody td{
				        text-align:left;
				    }
				    .table_design table th{
				        text-align:left;
				        color:#000;
				    }
				  .table_design .row-actions span a {
                        font-size: 13px;
                        color: #7f7fd3;
                    }
				</style>
				<div class="table_design">
				<div class="table table_shop_content">
					<p class="sub woocommerce_sub"><?php _e( 'Shop Content','woocommerce-dropshippers'); ?></p>
					<table>
					<tr class="first">
						<td class="first b b-products"><a href="#"><?php echo $product_count; ?></a></td>
						<td class="t products"><a href="#"><?php _e('Products','woocommerce-dropshippers'); ?></a></td>
					</tr>
					<tr class="first">
						<td class="first b b-products"><a href="<?php echo admin_url("admin.php?page=dropshipper_order_list_page") ?>"><?php echo $total_sales; ?></a></td>
						<td class="t products"><a href="<?php echo admin_url("admin.php?page=dropshipper_order_list_page") ?>"><?php _e('Sold','woocommerce-dropshippers'); ?></a></td>
					</tr>
					</table>
				</div>
				<div class="table table_orders">
					<p class="sub woocommerce_sub"><?php _e( 'Orders','woocommerce-dropshippers'); ?></p>
					<table>
					<tr class="first">
						<td class="b b-pending"><a href="<?php echo admin_url("admin.php?page=dropshipper_order_list_page") ?>"><?php echo $orders_processing ?></a></td>
						<td class="last t pending"><a href="<?php echo admin_url("admin.php?page=dropshipper_order_list_page") ?>"><?php _e('Processing','woocommerce-dropshippers'); ?></a></td>
					</tr>
					<tr class="first">
						<td class="b b-completed"><a href="<?php echo admin_url("admin.php?page=dropshipper_order_list_page") ?>"><?php echo $orders_completed; ?></a></td>
						<td class="last t completed"><a href="<?php echo admin_url("admin.php?page=dropshipper_order_list_page") ?>"><?php _e('Completed','woocommerce-dropshippers'); ?></a></td>
					</tr>
					</table>
				</div>
				<div class="table total_orders">
					<p class="sub woocommerce_sub"><?php _e( 'Total Earnings','woocommerce-dropshippers'); ?></p>
					<table>
					<tr class="first">
						<td class="last t"><a href="#"><?php _e('Total','woocommerce-dropshippers'); ?></a></td>
						<td class="b"><a href="#"><?php
							$dropshipper_earning = get_user_meta(get_current_user_id(), 'dropshipper_earnings', true);
							if(!$dropshipper_earning) $dropshipper_earning = 0;
							echo '<span class="artic-toberewritten">'. wc_price((float) $dropshipper_earning) .'</span><span class="artic-tobereconverted" style="display:none;">'. (float) $dropshipper_earning .'</span>';
						?></a></td>
					</tr>
					</table>
				</div>

				<div class="versions"></div>

				<table class="wp-list-table widefat fixed posts" cellspacing="0">
					<thead>
						<tr>
							<th id="co" class="manage-column column-columnname" scope="col"><?php echo __('Product','woocommerce-dropshippers'); ?></th>
							<th id="columnname" class="manage-column column-columnname" scope="col"><?php echo __('Options','woocommerce-dropshippers'); ?></th>
							<th width="40" id="columnname" class="manage-column column-columnname" scope="col"><?php echo __('Sold','woocommerce-dropshippers'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th class="manage-column column-columnname" scope="col"><?php echo __('Product','woocommerce-dropshippers'); ?></th>
							<th class="manage-column column-columnname" scope="col"><?php echo __('Options','woocommerce-dropshippers'); ?></th>
							<th class="manage-column column-columnname" scope="col"><?php echo __('Sold','woocommerce-dropshippers'); ?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php
							echo $table_string; 
						?>
					</tbody>
				</table>
				</div>
				<p></p>
				<?php
					$currency = get_user_meta(get_current_user_id(), 'dropshipper_currency', true);
					if(!$currency) $currency = 'USD';
					$cur_symbols = array(
						"USD" => '&#36;',
						"AUD" => '&#36;',
						"BDT" => '&#2547;&nbsp;',
						"BRL" => '&#82;&#36;',
						"BGN" => '&#1083;&#1074;.',
						"CAD" => '&#36;',
						"CLP" => '&#36;',
						"CNY" => '&yen;',
						"COP" => '&#36;',
						"CZK" => '&#75;&#269;',
						"DKK" => '&#107;&#114;',
						"EUR" => '&euro;',
						"HKD" => '&#36;',
						"HRK" => 'Kn',
						"HUF" => '&#70;&#116;',
						"ISK" => 'Kr.',
						"IDR" => 'Rp',
						"INR" => 'Rs.',
						"ILS" => '&#8362;',
						"JPY" => '&yen;',
						"KRW" => '&#8361;',
						"MYR" => '&#82;&#77;',
						"MXN" => '&#36;',
						"NGN" => '&#8358;',
						"NOK" => '&#107;&#114;',
						"NZD" => '&#36;',
						"PHP" => '&#8369;',
						"PLN" => '&#122;&#322;',
						"GBP" => '&pound;',
						"RON" => 'lei',
						"RUB" => '&#1088;&#1091;&#1073;.',
						"SGD" => '&#36;',
						"ZAR" => '&#82;',
						"SEK" => '&#107;&#114;',
						"CHF" => '&#67;&#72;&#70;',
						"TWD" => '&#78;&#84;&#36;',
						"THB" => '&#3647;',
						"TRY" => '&#84;&#76;',
						"VND" => '&#8363;',
					);
				?>
				<script type="text/javascript">
					jQuery.ajax({
						url:"https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20%28%22<?php echo get_woocommerce_currency() . $currency; ?>%22%29&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=cbfunc",
						dataType: 'jsonp',
						jsonp: 'callback',
						jsonpCallback: 'cbfunc'
					});
					function cbfunc(data) {
						var convRate = data.query.results.rate.Rate;
						var toRewrite = jQuery('.artic-toberewritten');
						jQuery('.artic-tobereconverted').each(function(i,j){
							toRewrite.eq(i).html('<?php echo $cur_symbols[$currency]; ?> '+ (parseFloat(jQuery(j).text())*convRate).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
						});
					}
					Number.prototype.format = function(n, x) {
						var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
						return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
					};
				</script>
				
				
				
				<?php
			
		
        
        
    }
}




/**
 * order dropship data
 */
add_action( 'woocommerce_account_dropshipper-order_endpoint', 'dropshipper_order_account_endpoint_content' );
function dropshipper_order_account_endpoint_content() {
    if ( current_user_can('dropshipper') ) 
    { ?>
        <div class="dropshippers-header" style="margin:0; padding:0; width:100%; height:100px; background: url('<?php echo plugins_url( 'images/headerbg.png', __FILE__ ) ?>'); background-repeat: repeat-x;">
	<img src="https://cabusinesspartner.com/wp-content/plugins/woocommerce-dropshippers/images/woocommerce-dropshippers-header.png" style="margin:0; padding:0; width:auto; height:100px;">
</div>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
<h1> <?php _e('Dropshipper Orders','woocommerce-dropshippers'); ?></h1>
<div id="input-dialog-template" style="display:none">
	<label for="input-dialog-date"><?php echo __('Date', 'woocommerce-dropshippers'); ?></label>
	<input type="text" name="input-dialog-date" id="input-dialog-date" style="width:100%">
	<label for="input-dialog-trackingnumber"><?php echo __('Tracking Number(s)', 'woocommerce-dropshippers'); ?></label>
	<textarea name="input-dialog-trackingnumber" id="input-dialog-trackingnumber" style="width:100%"></textarea>
	<label for="input-dialog-shippingcompany"><?php echo __('Shipping Company', 'woocommerce-dropshippers'); ?></label>
	<textarea name="input-dialog-shippingcompany" id="input-dialog-shippingcompany" style="width:100%"></textarea>
	<label for="input-dialog-notes"><?php echo __('Notes', 'woocommerce-dropshippers'); ?></label>
	<textarea name="input-dialog-notes" id="input-dialog-notes" style="width:100%"></textarea>
</div>
<script type="text/javascript">
jQuery( "#input-dialog-date" ).datepicker({ dateFormat: 'yy-mm-dd' });
function open_dropshipper_dialog(my_id) {
	jQuery('#input-dialog-date').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_date').html());
	jQuery('#input-dialog-trackingnumber').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_tracking_number').html());
	jQuery('#input-dialog-shippingcompany').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_shipping_company').html());
	jQuery('#input-dialog-notes').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_notes').html());
	jQuery('#input-dialog-template').dialog({
		title: '<?php echo __('Shipping Info','woocommerce-dropshippers'); ?>',
		buttons: [{
			text: '<?php echo __('Save','woocommerce-dropshippers'); ?>',
			click: function() {
				js_save_dropshipper_shipping_info(my_id, {
					date: jQuery('#input-dialog-date').val(),
					tracking_number: jQuery('#input-dialog-trackingnumber').val(),
					shipping_company: jQuery('#input-dialog-shippingcompany').val(),
					notes: jQuery('#input-dialog-notes').val()
				});
				jQuery( this ).dialog( "close" );
			}
		}]
	});
}
</script>
<?php
	global $woocommerce;
	$ajax_nonce = wp_create_nonce( "SpaceRubberDuck" );

	$table_string = ''; // the <table></table>
	$current_user = wp_get_current_user()->user_login;

	$actual_month = intval(date('m'));
	$actual_year = intval(date('Y'));
	$selected_month = $actual_month;
	$selected_year = $actual_year;
	if(isset($_GET['filtermonth'])){
		$filter_date = explode('-', $_GET['filtermonth']);
		if(count($filter_date == 2)){
			$selected_month = intval($filter_date[0]);
			$selected_year = intval($filter_date[1]);
		}
	}
	$user_date = wp_get_current_user()->user_registered;
	$user_month = intval(date('m', strtotime($user_date)));
	$user_year = intval(date('Y', strtotime($user_date)));

	$woo_ver = woocommerce_dropshipper_get_woo_version_number();
	if($woo_ver >= 2.2){
		$query = new WP_Query(
			array(
				'post_type' => 'shop_order',
				'post_status' => array( 'wc-processing', 'wc-completed' ),
				'monthnum' => $selected_month,
				'year' => $selected_year,
				'posts_per_page' => -1
			)
		);
	}
	else{
		$query = new WP_Query(
			array(
				'post_type' => 'shop_order',
				'post_status' => 'publish',
				'monthnum' => $selected_month,
				'year' => $selected_year,
				'posts_per_page' => -1
			)
		);
	}
	$order_count = 0;
	$options = get_option('woocommerce_dropshippers_options');
	$decimal_sep = wp_specialchars_decode(stripslashes(get_option('woocommerce_price_decimal_sep')), ENT_QUOTES);

	// The Loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			// actual product list of the dropshipper
			$real_products = array();

			$query->the_post();
			$order = new WC_Order(get_the_ID());
			$order_number = $order->get_order_number();
			$order_total = 0;
			$order_dropshippers = artic_dropshippers_get_post_meta(get_the_ID(), 'dropshippers', true);

			$tel = '';
			if(method_exists($order, 'get_billing_phone')){
				$tel = $order->get_billing_phone();
			}
			else{
				$tel = $order->billing_phone;
			}
			if(empty($tel)){
				$tel = '-';
			}
			foreach ($order->get_items() as $item) {
				if(get_post_meta( $item["product_id"], 'woo_dropshipper', true) == $current_user){
					array_push($real_products, $item);
				}
			}
			if( (sizeof($real_products) > 0) && ($order->get_status() == 'completed' || $order->get_status() == 'processing') ){
				$order_count++;
				$is_shipped = false;
				if(isset($order_dropshippers[wp_get_current_user()->user_login])){
					if($order_dropshippers[wp_get_current_user()->user_login] == "Shipped"){
						$is_shipped = true;
					}
				}
				$order_date = '';
				if(method_exists($order, 'get_date_created')){
					$order_date = date('Y-m-d H:i:s', strtotime($order->get_date_created()));
				}
				else{
					$order_date = $order->order_date;
				}
				$table_string .= '<tr class="alternate'. ($is_shipped?' is-shipped':'') .' tr-order-'.get_the_ID().'">';
				$table_string .= '<td class="column-columnname"><strong>' . $order_number . '</strong></td>';
				$table_string .= '<td class="column-columnname"><strong>' . $order_date . '</strong></td>';
				$table_string .= '<td class="column-columnname"><ul style="margin:0;">';
				$drop_total_price = 0;
				foreach ($real_products as $item) {
					$order_total += ( ((float) $item['line_total']) + ((float) $item['line_tax']) );

					if($item['variation_id'] > 0){
						$product_id = $item['variation_id'];
						$product_from_id = new WC_Product_Variation($product_id);
						$SKU = $product_from_id->get_sku();
						if(empty($SKU)){
							$product_from_id = new WC_Product($item['product_id']);
							$SKU = $product_from_id->get_sku();
						}
					}
					else{
						$product_id = $item['product_id'];
						$product_from_id = new WC_Product($product_id);
						$SKU = $product_from_id->get_sku();
					}
					if(empty($SKU)){
						$SKU = '';
					}
					else{
						$SKU = ' (' . $SKU . ')';
					}

					$my_meta = '';
					if(method_exists($item, 'get_meta_data')){ // new method for WooCommerce 3.1
						$forbidden_meta = array(
							'_wc_cog_item_cost',
							'_wc_cog_item_total_cost',
							'ph_item_est_delivery',
							'_alg_wc_cog_item_cost',
						);
						foreach ($item->get_meta_data() as $product_meta_key => $product_meta_value) {
							if(!empty($product_meta_value->id)){
								$display_key  = wc_attribute_label( $product_meta_value->key, $product_from_id );
								if(!in_array($display_key, $forbidden_meta)){
									if(is_string($product_meta_value->value)){
										$my_meta .= '<br/><small>' . $display_key . ': ' . $product_meta_value->value . '</small>' . "\n";
									}
								}
							}
						}
					}
					else{ // old method
						$meta = new WC_Order_Item_Meta( $item );
						$my_meta = $meta->display( true, true );
					}
					
					
					$my_item_post = get_post($item['product_id']);
					$drop_price = get_post_meta( $item['product_id'], '_dropshipper_price', true );
					if(!$drop_price){ $drop_price = 0;}
					$drop_price = (float) str_replace($decimal_sep, '.', ''.$drop_price);
					if($item['variation_id'] != 0){
						$drop_price = get_post_meta( $item['variation_id'], '_dropshipper_price', true );
						if(!$drop_price){ $drop_price = 0;}
						$drop_price = (float) str_replace($decimal_sep, '.', ''.$drop_price);
					}
					$my_item_post_title = __($my_item_post->post_title);
					if(isset($options['text_string']) && $options['text_string'] == "Yes"){
						$table_string .= "<li>" . $my_item_post_title . $SKU . ' ' . $my_meta . ' x' . $item['qty'] . ' ('. __('FULL PRICE:','woocommerce-dropshippers') .' <span class="artic-toberewritten">' . wc_price(( ((float) $item['line_total']) + ((float) $item['line_tax']) )) .'</span><span class="artic-tobereconverted" style="display:none;">'.( ((float) $item['line_total']) + ((float) $item['line_tax']) ).'</span> - '. __('MY EARNINGS:','woocommerce-dropshippers') .' <span class="artic-toberewritten">' . wc_price((float) $drop_price*$item['qty']) .'</span><span class="artic-tobereconverted" style="display:none;">'.((float) $drop_price*$item['qty']).'</span>)';
					}
					else{
						$table_string .= "<li>" . $my_item_post_title . $SKU . ' ' . $my_meta . ' x' . $item['qty'] . '('. __('MY EARNINGS:','woocommerce-dropshippers') .' <span class="artic-toberewritten">' . wc_price((float) $drop_price*$item['qty']) .'</span><span class="artic-tobereconverted" style="display:none;">'.((float) $drop_price*$item['qty']).'</span>)';
					}
					$table_string .= '</li>' . "\n";
					$drop_total_price += ($drop_price*$item['qty']);
				}
				$table_string .= '</ul></td>';
				$shipping_country = '';
				if(method_exists($order, 'get_shipping_country')){
					$shipping_country = $order->get_shipping_country();
				}
				else{
					$shipping_country = $order->shipping_country;
				}
				$table_string .= '<td class="column-columnname">' . $woocommerce->countries->countries[$shipping_country] .'<br/>'. $order->get_formatted_shipping_address() . '</td>';
				if($options['can_see_email'] == "Yes" || $options['can_see_phone'] == "Yes"){
					$table_string .= '<td class="column-columnname">';
					$billing_email = '';
					if(method_exists($order, 'get_billing_email')){
						$billing_email = $order->get_billing_email();
					}
					else{
						$billing_email = $order->billing_email;
					}
					
					if($options['can_see_email'] == "Yes"){
						$table_string .= $billing_email . '<br/>';
					}
					if($options['can_see_phone'] == "Yes"){
						$table_string .= 'Tel: '. $tel;
					}
					if($options['can_see_email'] == "Yes"){
						$table_string .= '<div class="row-actions"><span><a href="mailto:'.$billing_email.'">'. __('Send an Email','woocommerce-dropshippers') .'</a></span></div>';
					}
					$table_string .= '</td>';
				}
				$table_string .= '<td class="column-columnname">';
				if(isset($options['text_string']) && $options['text_string'] == "Yes"){
					$table_string .= __('FULL TOTAL:','woocommerce-dropshippers') .'<span class="artic-toberewritten">' . wc_price((float) $order_total) . '</span><span class="artic-tobereconverted" style="display:none;">'.((float) $order_total).'</span><br>';
				}
				$table_string .= __('MY EARNINGS:','woocommerce-dropshippers') . '<span class="artic-toberewritten">' . wc_price((float) $drop_total_price) . '</span><span class="artic-tobereconverted" style="display:none;">'.((float) $drop_total_price).'</span>';
				$table_string .= "</td>\n";
				$dropshipper_shipping_info = get_post_meta(get_the_ID(), 'dropshipper_shipping_info_'.get_current_user_id(), true);
				if(!$dropshipper_shipping_info){
					$dropshipper_shipping_info = array(
						'date' => '',
						'tracking_number' => '',
						'shipping_company' => '',
						'notes' => ''
					);
				}
				$table_string .= '<td class="column-columnname" id="dropshipper_shipping_info_'.get_the_ID().'">';
				$table_string .= '<strong>'. __('Date', 'woocommerce-dropshippers') .'</strong>: <span class="dropshipper_date">'. $dropshipper_shipping_info['date']. '</span><br/>';
				$table_string .= '<strong>'. __('Tracking Number(s)', 'woocommerce-dropshippers') .'</strong>: <span class="dropshipper_tracking_number">'. $dropshipper_shipping_info['tracking_number']. '</span><br/>';
				$table_string .= '<strong>'. __('Shipping Company', 'woocommerce-dropshippers') .'</strong>: <span class="dropshipper_shipping_company">'. $dropshipper_shipping_info['shipping_company']. '</span><br/>';
				$table_string .= '<strong>'. __('Notes', 'woocommerce-dropshippers') .'</strong>: <span class="dropshipper_notes">'. $dropshipper_shipping_info['notes']. '</span><br/>';
				if(!$is_shipped){
					$table_string .= '<button id="open_dropshipper_dialog_'. get_the_ID() .'" class="button button-primary" onclick="open_dropshipper_dialog('. get_the_ID() .')" style="margin-top:2px" >'. __('Edit Shipping Info','woocommerce-dropshippers') .'</button>';
				}
				$table_string .= '</td><td class="column-columnname">' . __( $order->get_status(), 'woocommerce' );
				if($is_shipped){
					$table_string .= '<br/>'. __('Shipped','woocommerce-dropshippers');
				}
				else{
					$table_string .= '<br/><button id="mark_dropshipped_'. get_the_ID() .'" class="button button-primary" onclick="js_dropshipped('. get_the_ID() .')" style="margin-top:2px" >'. __('Mark as Shipped','woocommerce-dropshippers') .'</button>';
				}
				$fake_ajax_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_dropshippers_get_slip&order_id=' . get_the_ID()), 'woocommerce_dropshippers_get_slip' );
				$table_string .= '<br/><button id="print_slip_'. get_the_ID() .'" class="button button-primary" onclick="js_print_slip(\''. $fake_ajax_url .'\')" style="margin-top:2px" >'. __('Print packing slip','woocommerce-dropshippers') .'</button>';
				$table_string .= '</td></tr>' . "\n";
			}
		}
		$table_string .= '</table>';
	} else {
		// no posts found
	}
	?>
	<?php
	/* Restore original Post Data */
	wp_reset_postdata();
	echo '<div class="wrap"><h2></h2></wrap>';
	echo '<div class="wrap">';
	echo '<p>'. __('Order count:','woocommerce-dropshippers') ." $order_count</p>";
	$options = get_option('woocommerce_dropshippers_options');
	?>
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
				<th class="manage-column column-cb column-columnname" style=""><h2><?php echo __('Shop Owner Billing Info','woocommerce-dropshippers'); ?></h2></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><p><?php echo (isset($options['billing_address'])?nl2br($options['billing_address']):''); ?></p></td>
			</tr>
		</tbody>
	</table>
	<hr>
	<form name="month" action="" method="GET"> 
		<label for="filtermonth"><?php echo __('Select month','woocommerce-dropshippers' ); ?>: </label>
		<input type="hidden" name="page" value="dropshipper_order_list_page" />
		<select name="filtermonth">
		<?php
			$first_ride = true;
			do{
				if($first_ride){
					$first_ride = false;
				}
				else{
					$user_month++;
					if($user_month == 13){
						$user_month = 1;
						$user_year++;
					}
				}
				$my_selected = '';
				if( ($user_month == $selected_month) && ($user_year == $selected_year) ){
					$my_selected = ' selected="selected"';
				}
				echo '<option value="'. sprintf('%02d', $user_month) .'-'. $user_year .'"'. $my_selected .'>'. sprintf('%02d', $user_month) .'-'. $user_year."</option>\n";
			}
			while( ($user_month != $actual_month) || ($user_year != $actual_year) );
		?>
		</select>
		<input class="button" type="submit" value="<?php echo __('Filter orders','woocommerce-dropshippers'); ?>" />
		<label for="hide-shipped" style="margin-left:20px;"><?php echo __('Hide Shipped','woocommerce-dropshippers'); ?></label>
		<input type="checkbox" id="hide-shipped" />
	</form>
	<hr/>
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
	<thead>
	<tr>
		<tr>
			<th id="co" class="manage-column column-cb column-columnname" scope="col" style=""><?php echo __('ID','woocommerce-dropshippers'); ?></th>
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php echo __('Date','woocommerce-dropshippers'); ?></th>
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php echo __('Product','woocommerce-dropshippers'); ?></th>
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php echo __('Client Info','woocommerce-dropshippers'); ?></th>
			<?php
				if($options['can_see_email'] == "Yes" || $options['can_see_phone'] == "Yes"){
					echo '<th id="columnname" class="manage-column column-columnname" scope="col">'. __('Contact Info','woocommerce-dropshippers') .'</th>';
				}
			?>
			<th class="manage-column column-columnname" scope="col"><?php echo __('Earnings','woocommerce-dropshippers'); ?></th>
			<th id="columnname" class="manage-column column-columnname" scope="col"><?php echo __('Shipping Info','woocommerce-dropshippers'); ?></th>
			<th id="columnname" class="manage-column column-columnname num" scope="col"><?php echo __('Status','woocommerce-dropshippers'); ?></th>
		</tr>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<tr>
			<th class="manage-column column-cb column-columnname" scope="col"><?php echo __('ID','woocommerce-dropshippers'); ?></th>
			<th class="manage-column column-columnname" scope="col"><?php echo __('Date','woocommerce-dropshippers'); ?></th>
			<th class="manage-column column-columnname" scope="col"><?php echo __('Product','woocommerce-dropshippers'); ?></th>
			<th class="manage-column column-columnname" scope="col"><?php echo __('Client Info','woocommerce-dropshippers'); ?></th>
			<?php
				if($options['can_see_email'] == "Yes" || $options['can_see_phone'] == "Yes"){
					echo '<th id="columnname" class="manage-column column-columnname" scope="col">'. __('Contact Info','woocommerce-dropshippers') .'</th>';
				}
			?>
			<th class="manage-column column-columnname" scope="col"><?php echo __('Earnings','woocommerce-dropshippers'); ?></th>
			<th class="manage-column column-columnname" scope="col"><?php echo __('Shipping Info','woocommerce-dropshippers'); ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php echo __('Status','woocommerce-dropshippers'); ?></th>
		</tr>
	</tr>
	</tfoot>

	<tbody>
		<?php
			echo $table_string;
		?>
	</tbody>
</table>
<script type="text/javascript">
	jQuery('#hide-shipped').change(function(){
		if(jQuery(this).is(':checked')){
			jQuery('.is-shipped').hide();
		}
		else{
			jQuery('.is-shipped').show();
		}
	});

	function js_print_slip(url){
		var newwindow = window.open(url, 'DropshipperSlip',
			'toolbar=no, scrollbars=yes, resizable=yes, width=400, height=400, width=600, height=400');
		newwindow.resizeTo(600,400);
		if (window.focus) {newwindow.focus()}
		return false;
	}
</script>
	<?php
	echo '</div>';
	$user_id = get_current_user_id();
	$currency = get_user_meta($user_id, 'dropshipper_currency', true);
	if(!$currency) $currency = 'USD';
	$cur_symbols = array(
		"USD" => '&#36;',
		"AUD" => '&#36;',
		"BDT" => '&#2547;&nbsp;',
		"BRL" => '&#82;&#36;',
		"BGN" => '&#1083;&#1074;.',
		"CAD" => '&#36;',
		"CLP" => '&#36;',
		"CNY" => '&yen;',
		"COP" => '&#36;',
		"CZK" => '&#75;&#269;',
		"DKK" => '&#107;&#114;',
		"EUR" => '&euro;',
		"HKD" => '&#36;',
		"HRK" => 'Kn',
		"HUF" => '&#70;&#116;',
		"ISK" => 'Kr.',
		"IDR" => 'Rp',
		"INR" => 'Rs.',
		"ILS" => '&#8362;',
		"JPY" => '&yen;',
		"KRW" => '&#8361;',
		"MYR" => '&#82;&#77;',
		"MXN" => '&#36;',
		"NGN" => '&#8358;',
		"NOK" => '&#107;&#114;',
		"NZD" => '&#36;',
		"PHP" => '&#8369;',
		"PLN" => '&#122;&#322;',
		"GBP" => '&pound;',
		"RON" => 'lei',
		"RUB" => '&#1088;&#1091;&#1073;.',
		"SGD" => '&#36;',
		"ZAR" => '&#82;',
		"SEK" => '&#107;&#114;',
		"CHF" => '&#67;&#72;&#70;',
		"TWD" => '&#78;&#84;&#36;',
		"THB" => '&#3647;',
		"TRY" => '&#84;&#76;',
		"VND" => '&#8363;',
	);
?>
<script type="text/javascript">
jQuery.ajax({
	url:"https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20%28%22<?php echo get_woocommerce_currency() . $currency; ?>%22%29&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=cbfunc",
	dataType: 'jsonp',
	jsonp: 'callback',
	jsonpCallback: 'cbfunc'
});
function cbfunc(data) {
	var convRate = data.query.results.rate.Rate;
	var toRewrite = jQuery('.artic-toberewritten');
	jQuery('.artic-tobereconverted').each(function(i,j){
		toRewrite.eq(i).html('<?php echo $cur_symbols[$currency]; ?> '+ (parseFloat(jQuery(j).text())*convRate).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
	});
}
Number.prototype.format = function(n, x) {
	var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
	return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};
</script>
<?php
    }
}

// Change WooCommerce "Related products" text

add_filter('woocommerce_product_related_products_heading',function(){

   return 'You May Also Like';

});

function wooc_extra_register_fields() {?>


       <p class="form-row form-row-wide">

       <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>

       <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_first_name" id="reg_billing_full_name" placeholder="Jhon" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" required/>
        
       </p>
       <p class="form-row form-row-wide">

       <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>

       <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_last_name" id="reg_billing_last_name" placeholder="Deo" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" required/>
         
       </p>
       <p class="form-row form-row-wide">

       <label for="reg_billing_full_name"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>

       <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_phone" maxlength="10"  id="reg_billing_phone_name" placeholder="0123456789" value="<?php if ( ! empty( $_POST['billing_phone_name'] ) ) esc_attr_e( $_POST['billing_phone_name'] ); ?>" required/>
         <input type="hidden" name="user_flag"  value="0" />
       </p>
        

       <div class="clear"></div>

       <?php

 }

 add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );
 
 
 function wooc_save_extra_register_fields( $customer_id ) {

    if ( isset( $_POST['user_flag'] ) ) {

                 // Phone input filed which is used in WooCommerce

                 update_user_meta( $customer_id, 'user_flag', sanitize_text_field( $_POST['user_flag'] ) );

          }

      if ( isset( $_POST['billing_first_name'] ) ) {

                 // Phone input filed which is used in WooCommerce
                 $str = $_POST['billing_first_name'];
           
                 update_user_meta( $customer_id, 'first_name', sanitize_text_field( $str ) ); 
                 

          }
          if ( isset( $_POST['billing_last_name'] ) ) {

                 // Phone input filed which is used in WooCommerce
                 $str = $_POST['billing_last_name'];
           
                 update_user_meta( $customer_id, 'last_name', sanitize_text_field( $str ) ); 

          }
          if ( isset( $_POST['billing_phone'] ) ) {

                 // Phone input filed which is used in WooCommerce
                 $str = $_POST['billing_phone'];
           
                 update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $str ) ); 
                 

          }
          if ( isset( $_POST['coupon'] ) ) {

                 // Phone input filed which is used in WooCommerce
                 $str = $_POST['coupon'];
           
                 update_user_meta( $customer_id, 'coupon', sanitize_text_field( $str ) ); 
                 

          }
          

}

add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );
 
 // Redirect WooCommerce checkout page to a custom thank you page
add_action( 'woocommerce_thankyou', 'pfwp_redirect_woo_checkout');
function pfwp_redirect_woo_checkout( $order_id ){
    global $wpdb;
    $order = wc_get_order( $order_id );
    if ( ! $order_id )
        return;
        
        
//Featured image code start
 if (is_user_logged_in()) {
    // Get the current user ID
    $user_id = get_current_user_id();
     if (user_can($user_id, 'customer')) {
   
     $prefixcout =  get_user_meta( $user_id, 'primary_blog', true );
     $table_prefix = "wp_".$prefixcout."_postmeta";
         
    //$order_id = 4883;
 $order = wc_get_order( $order_id );
          

    
    
    
    foreach ( $order->get_items() as $item_id => $item ) {



            $product_id = $item->get_product_id(); 
           // echo "<br>". $product_id ;
        $vendor_id =  $user_id;
         $product_sku = get_post_meta($product_id, '_sku', true);

       // echo 'Product SKU: ' . $product_sku . '<br>';
        $product_id1 = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT post_id FROM {$table_prefix} WHERE meta_key = '_sku' AND meta_value = %s",
        $product_sku
    )
);
 $orders_meta = $wpdb->get_results("SELECT * FROM    wp_wc_orders_meta   WHERE meta_key LIKE '_stripe_customer_id' AND order_id = $order_id");

        $orders_meta_value =  $orders_meta[0]->meta_value;
          $subtotal = $item->get_subtotal();
        $wpdb->insert('vendor_details', array(
            'vendor_id' => $user_id,
            'vendor_cus_key' => $orders_meta_value,
            'price' => $subtotal,
            'sku' => $product_sku,
        ));
  
 //$new_image_url = 'https://site24.vitalaunch.io/wp-content/uploads/sites/123/2023/12/team4.png';
 $post_ids = $wpdb->get_results("SELECT * FROM `marge_img` WHERE `user_id` = '".$user_id."' AND `sku` = '".$product_sku."' ORDER BY ID DESC LIMIT 1 ");
 $new_image_id = $post_ids[0]->image_id;
//$new_image_id =  $_SESSION['image_id']; //1826 ;//attachment_url_to_postid($new_image_url); 
//echo $new_image_id.$table_prefix;

//update_post_meta($product_id1, '_thumbnail_id', $new_image_id); 
$wpdb->query(
    $wpdb->prepare(
        "UPDATE {$table_prefix} SET meta_value = %s WHERE post_id = %d AND meta_key = '_thumbnail_id'",
        $new_image_id,
        $product_id1
    )
);
//echo 'Featured image updated for product ID ' . $product_id1;//$new_image_id;
        
  
        if ($vendor_id1 ) { 
            $new_image_url = 'https://site24.vitalaunch.io/wp-content/uploads/sites/123/2024/01/Echinacea-2.png';

            // Update the product's featured image
         //   update_post_meta($product_id, '_thumbnail_id', attachment_url_to_postid($new_image_url));
        } 
    }
    
    
} else {
    echo 'User is not a vendor.';
}


    // Get user data
    $user_info = get_userdata($user_id);

    // Output user details
    /*echo 'Username: ' . $user_info->user_login . '<br>';
    echo 'User ID: ' . $user_info->ID . '<br>';
    echo 'Email: ' . $user_info->user_email . '<br>';*/
    // ... and other user data you may need
} else {
    echo 'User not logged in.';
}
//Featured image code end


    // Allow code execution only once 
    if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
        
        global $wpdb, $current_user;
       $m_value = $_SESSION['prod_det'];
      //try code 
      $user_id = get_current_user_id();
      
        
      $wpdb->insert('wp_postmeta', array(
          'meta_id' => '',
    'post_id' => $order_id,
    'meta_key' => 'order_quantity',
    'meta_value' => $m_value 
));
$label_val = $_SESSION['label_val'];
$wpdb->insert('wp_postmeta', array(
          'meta_id' => '',
    'post_id' => $order_id,
    'meta_key' => '_label',
    'meta_value' => $label_val 
));

 
    
      
        

// try code

        // Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );

        // Get the order key
        $order_key = $order->get_order_key();

        // Get the order number
        $order_key = $order->get_order_number();

        if($order->is_paid())
            $paid = __('yes');
        else
            $paid = __('no');

        // Loop through order items
        foreach ( $order->get_items() as $item_id => $item ) {

            // Get the product object
            $product = $item->get_product();

            // Get the product Id
           $product_id = $product->get_id().'<br>';

            // Get the product name
            $product_id = $item->get_name();
            $item_sku = $product->get_sku();
            $item_quantity = $item->get_quantity();
            global $wpdb, $current_user;
            
             $product_order_item_id = $wpdb->get_results("SELECT  order_item_id FROM wp_woocommerce_order_items  WHERE  `order_id` = '".$order_id."'");
             
            
            $product_order_item_id =  $product_order_item_id[0]->order_item_id;
            $product_order_item_id_meta = $wpdb->get_results("SELECT *  FROM `wp_woocommerce_order_itemmeta` WHERE `order_item_id` = '".$product_order_item_id."' AND `meta_key` LIKE 'Product Type' ");
            
            $m_value = $product_order_item_id_meta[0]->meta_value;
           
            $data_store = WC_Data_Store::load( 'order-item' );
	        $data_store->get_order_id_by_order_item_id( $item_id );
            
            //echo $user_id = get_current_user_id();
            $prefixcout =  get_user_meta( $user_id, 'primary_blog', true );
            $tblprefix = $wpdb->prefix.$prefixcout.'_postmeta';
           // $m_value = $_SESSION['prod_det'];
          
            if($m_value == 'Label'){
            $product_ids = $product->get_id();   
            $product_post_id = $wpdb->get_results("SELECT * FROM $tblprefix WHERE  `meta_key` LIKE 'label_$item_sku'  LIMIT  1 ");
            
            $current_meta_value = $product_post_id[0]->meta_value;
            $total_qty_meta = $item_quantity+$current_meta_value;  
            
            
           $wpdb->query($wpdb->prepare("DELETE FROM `wp_54_postmeta` WHERE `meta_key` LIKE '%label%' "));
            
            if(empty($current_meta_value)){
          
            $wpdb->insert($tblprefix, array(
            'post_id' => $product->get_id(),
            'meta_key' => 'label_'.$item_sku,
            'meta_value' =>  $item_quantity
            ));
           
            
            $wpdb->insert('wp_postmeta', array(
            'post_id' => $product->get_id(),
            'meta_key' => '_label',
            'meta_value' =>  $item_quantity
            ));
           
            }else{
                
                $wpdb->query($wpdb->prepare("UPDATE wp_postmeta SET meta_value= $total_qty_meta WHERE post_id=$product_ids AND meta_key='label_$item_sku'"));
                $wpdb->query($wpdb->prepare("UPDATE $tblprefix SET meta_value= $total_qty_meta WHERE meta_key='label_$item_sku'"));
                
            }
           
            
            $product_post_id = $wpdb->get_results("SELECT post_id FROM wp_postmeta WHERE (meta_key = '_sku' AND meta_value = '". $item_sku ."')");
            
            $current_product_id = $product_post_id[0]->post_id;
            
            $product_qty = $wpdb->get_results("SELECT `meta_value`  FROM wp_postmeta WHERE `post_id` = '".$current_product_id."' AND `meta_key` LIKE '_stock';");
            
            $old_qty = $product_qty[0]->meta_value;
            
             $total_qty = $item_quantity+$old_qty;  
             
            $wpdb->query($wpdb->prepare("UPDATE wp_postmeta SET meta_value='".$total_qty."' WHERE post_id='".$current_product_id."' AND meta_key='_stock'"));
            $wpdb->query($wpdb->prepare("UPDATE wp_postmeta SET meta_value='instock' WHERE post_id='".$current_product_id."' AND meta_key='_stock_status'")); 
            
           $product_ids = $product->get_id();
     
            
             //if($m_value >= 50){
                
                $post_ids = $wpdb->get_results("SELECT * FROM `marge_img` WHERE `user_id` = '".$user_id."' AND `sku` = '".$item_sku."' ORDER BY ID DESC LIMIT 1 ");
                
                foreach($post_ids as $post_id){
                    $img = $post_id->image_url;
                    add_post_meta( $order_id, 'marge_img', $img );
                   
                }
                
            // }
            }if($m_value == "Sample"){
                $product_post_id = $wpdb->get_results("SELECT post_id FROM wp_postmeta WHERE (meta_value = '". $item_sku ."')");
            
                 $current_product_id = $product_post_id[0]->post_id;
                $product_post_id = $wpdb->get_results("SELECT * FROM wp_postmeta  WHERE post_id='".$current_product_id."' AND meta_key='_stock' LIMIT  1 ");
            
                $current_meta_value = $product_post_id[0]->meta_value;
                
                
                $ch_posts_id = $wpdb->get_results("SELECT post_id FROM $tblprefix WHERE (meta_value = '". $item_sku ."')");
            
                 $ch_post_id = $ch_posts_id[0]->post_id;
                $wpdb->query($wpdb->prepare("UPDATE $tblprefix SET meta_value='".$current_meta_value."' WHERE post_id='".$ch_post_id."' AND meta_key='_stock'"));
            }
           
        }


       
    }
    $custom_label_price = get_post_meta($order_id, '_label', TRUE);
    if(!empty($custom_label_price)){
        $url = 'https://vitalaunch.io/thank-you/?order_id=$order_id';
        //wp_safe_redirect( $url );
    }else{
    $url = 'https://vitalaunch.io/shop/';
    if ( ! $order->has_status( 'failed' ) ) {
        wp_safe_redirect( $url );
        exit;
    }
    }
}

add_action( 'woocommerce_order_status_completed','callback_function_name' );


//add_action( 'woocommerce_order_status_completed', 'wpdesk_set_user_role_after_purchase' );

function callback_function_name($order_id){

	$order = new WC_Order( $order_id );
	
	foreach ( $order->get_items() as $item_id => $item ) {

            // Get the product object
            $product = $item->get_product();

            // Get the product Id
           $product_id = $product->get_id().'<br>';

            // Get the product name
           
            $item_sku = $product->get_sku();
            $item_quantity = $item->get_quantity();
            global $wpdb;
            
             $product_order_item_id = $wpdb->get_results("SELECT  order_item_id FROM wp_woocommerce_order_items  WHERE  `order_id` = '".$order_id."'");
            
            
            $product_order_item_id =  $product_order_item_id[0]->order_item_id;
            $product_order_item_id_meta = $wpdb->get_results("SELECT *  FROM `wp_woocommerce_order_itemmeta` WHERE `order_item_id` = '".$product_order_item_id."' AND `meta_key` LIKE 'Product Type' ");
            
            $m_value = $product_order_item_id_meta[0]->meta_value;
          
            $data_store = WC_Data_Store::load( 'order-item' );
	        $data_store->get_order_id_by_order_item_id( $item_id );
	        
	        $product_order_ids =  $wpdb->get_results("SELECT * FROM wp_wc_orders WHERE  `id` = $order_id");
            echo $user_id = $product_order_ids[0]->customer_id;
            
          
            $prefixcout =  get_user_meta( $user_id, 'primary_blog', true );
            $tblprefix = $wpdb->prefix.$prefixcout.'_postmeta';
           
           
           // $m_value = $_SESSION['prod_det'];
            
            if($m_value == 'Label'){
            $product_ids = $product->get_id();   
            $product_post_id = $wpdb->get_results("SELECT * FROM $tblprefix WHERE  `meta_key` LIKE 'label_$item_sku'");
            
            $current_meta_value = $product_post_id[0]->meta_value;
            //$total_qty_meta = $item_quantity+$current_meta_value;  
            
           
           $product_post_labels = $wpdb->get_results("SELECT * FROM $tblprefix WHERE  `meta_key` LIKE 'labels_$item_sku'");
            
           $current_meta_labels = $product_post_labels[0]->meta_value;
           echo '<pre>';
           
            if(empty($current_meta_labels)){
                
                $wpdb->insert($tblprefix, array(
                'post_id' => $product->get_id(),
                'meta_key' => 'labels_'.$item_sku,
                'meta_value' =>  $current_meta_value
                ));
               
            }else{
                
                $wpdb->query($wpdb->prepare("UPDATE $tblprefix SET meta_value= $current_meta_value WHERE post_id=$product_ids AND meta_key='labels_$item_sku'"));
            }
            
           
            }
           
        }
}



add_action( 'woocommerce_archive_description', 'woocommerce_category_image', 2 );
function woocommerce_category_image() {

    
        global  $current_user;
       if ( is_user_logged_in() ) { 

        
            echo '<h1>Welcome, '.$current_user->display_name.'!</h1>';
            echo '<p>Pick a Product you want to customize</p>';
            ?>
            <a href=<?php echo get_field("banner_link", 10) ?> ><img src=<?php echo get_field("banner", 10) ?> /></a>
            
            <?php
       }
        
    
}


function my_account_new_endpoints() {
    add_rewrite_endpoint('mystore', EP_PAGES);
}
 add_action( 'init', 'my_account_new_endpoints' );



function mystore_endpoint_content() {
       // Check rows exists.
       ?>
        <div class="mystore tab">
            <button class="tablinks" onclick="openCity(event, 'tab1')" id="defaultOpen">If you don't have domain.</button>
            <button class="tablinks" onclick="openCity(event, 'tab5')" >If you have domain.</button>
        </div>
        <div id='tab1' class="tabcontent">
    
    <?php
    echo "<ceter><h3>Store Details</h3></center>";
    //echo "Hello, If you would like to sell your own affiliate product then you need to start with your own store setup.</br>";
    $user = wp_get_current_user();
    $user_info = get_userdata($user->ID);
    $email = $user_info->user_email;
    $use_flag = get_user_meta($user->ID, 'user_flag', True);
     
    global $wpdb;    
    $result = $wpdb->get_results( "SELECT domain FROM wp_blogs INNER JOIN wp_registration_log ON wp_blogs.blog_id = wp_registration_log.blog_id WHERE email='".$email."'");
    //print_r($result);
    $store_own = $result[0]->domain;
    
    //echo $wpdb->last_query;
   
    if($store_own == '' && $use_flag != 0){
    echo "Please Setup your store first to experience your store features.";    
    echo '<br><a class="startbutton" href="https://vitalaunch.io/wp-signup.php"><button>Start Store Setup</button></a>';
    }else{ ?>  
    
    Your store is ready:- <a target="_blank" href="https://<?php echo $store_own; ?>"><?php echo $store_own; ?></a>  
    

    <hr>
    <div class="row">
        <div class="col-md-6">
     <h6>Dashboard management instructions</h6>
    
    <p>
        <strong>Step 1 :</strong> Go to your site <a target="_blank" href="https://<?php echo $store_own; ?>/wp-admin">https://<?php echo $store_own; ?>/wp-admin</a><br>
        <strong>Step 2 :</strong> Login with your current credential.<br>
        <strong>Step 3 :</strong> Explore your dashboard.
        <br>
        <h6>OR</h6>
        Access Your Dashboard <br><a target="_blank" class="startbutton" href="https://<?php echo $store_own; ?>/wp-admin"><button>Access Dashboard</button></a>
    </p>
    </div>
   
    </div>
    
    <hr>
    <div class="row">
        <div class="col-md-6">
    <h6>API/DNS Record Integration Instructions </h6>
    
    Setup Your Domain By Click on below Link<br>
    <a class="startbutton" target="_blank" href="https://<?php echo $store_own; ?>/wp-admin/tools.php?page=domainmapping"><button>Domain Setup</button></a><br><br>
    </div>
     <div class="col-md-6">
        <video style="width:100%;height:300px;" loop="true" autoplay="autoplay" muted>
<source src="<?php echo the_field('video', 'option'); ?>" />
</video>
    </div>
    </div>
    
    
   <?php }
    
   echo '<style>.startbutton button {
	background-color: #0d9b4d;
	color: white;
	padding: 10px 41px 10px 40px;
	border-radius: 10px;
	margin-top: 20px;
	border: 1px solid #0d9b4d;
}</style>';
?>
</div>
<div id='tab5' class="tabcontent">
    
    <?php the_field('tab2', 'option'); ?>
    

</div>
<?php 
    $user = wp_get_current_user();
    $user_info = get_userdata($user->ID);
    $email = $user_info->user_email;
    $use_flag = get_user_meta($user->ID, 'user_flag', True);
    if($use_flag == 0){ ?>
    <style>
        .upgrade_plan h2 {
    background-color: rgba(0,0,0,0.6);
    color: #fff;
    display: inline;
    padding: 1%;
    font-size: 2em;
    color: #fff;
    text-transform: capitalize;
    text-align: center;
    position: absolute;
    
}
.upgrade_plan {
	position: absolute;
	top: 25%;
	/* vertical-align: middle; */
	/* display: inline; */
	text-align: center;
	right: 15%;
	width: 80%;
}
.woocommerce-MyAccount-content {
	background: rgba(11, 17, 11, 0.3);
	user-select: none;
}
video, .mystore.tab {
	background: rgba(11, 17, 11, 0.3);
	opacity: .1;
}
#tab1, #tab2, .mystore.tab {
  pointer-events: none;
}
h3, .startbutton, h6 {
	opacity: .1;
}
@media screen and (max-width: 768px) {
  .upgrade_plan h2 {
	top: 54%;
	left: 50%;
}
@media screen and  (min-width: 769px) and (max-width: 920px) {
  .upgrade_plan h2 {
	top: 54%;
	left: 68%;
}
}
    </style>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
   <div class="upgrade_plan">
    <h2><i class="fas fa-lock"> </i> Please upgrade your plan for using this functionality</h2> 
  
</div>
<?php } ?>
<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>
<?php
    return;
}

add_action('woocommerce_account_mystore_endpoint', 'mystore_endpoint_content');



// function resourcelibrary_new_endpoints() {
//     add_rewrite_endpoint('resourcelibrary', EP_PAGES);
// }
//  add_action( 'init', 'resourcelibrary_new_endpoints' );



// function resourcelibrary_endpoint_content() {
//     echo '<h2>Help Center</h2>
//     <ul class="resouceList">
//     <li><a href="https://vitalaunch.io/category-help-center/getting-started/">GETTING STARTED</a></li>
//     <li><a href="https://vitalaunch.io/category-help-center/orders/">ORDERS</a></li>
//     <li><a href="https://vitalaunch.io/category-help-center/our-products/">OUR PRODUCTS</a></li>
//     <li><a href="https://vitalaunch.io/category-help-center/shipping/">SHIPPING</a></li>
//     <li><a href="https://vitalaunch.io/category-help-center/billing-payments/">BILLING & PAYMENTS</a></li>
//     <li><a href="https://vitalaunch.io/category-help-center/design-plans/">DESIGN PLANS</a></li>
//     <li><a href="https://vitalaunch.io/category-help-center/vitalaunch-updates/">VITALAUNCH UPDATES</a></li>
//     <li><a href="https://vitalaunch.io/category-help-center/about-vitalaunch/">ABOUT VITALAUNCH</a></li>
//     </ul>';
    
//     echo '<style>
//     ul.resouceList {
//     padding: 20px 20px;
// }
// ul.resouceList li {
//     padding: 5px 0;
// }
// </style>';
// }

// add_action('woocommerce_account_resourcelibrary_endpoint', 'resourcelibrary_endpoint_content');




function my_account_rename_items($menu_items){
    
 
    $menu_items['mystore'] = __('My Store', 'my_text_domain');
     // Insert back the logout item.
   
    return $menu_items;
}
add_filter('woocommerce_account_menu_items', 'my_account_rename_items');


function my_custom_my_account_menu_items( $items ) {
    $user_id = $current_user->ID;
      
          $items = array(
        'dashboard'         => __( 'Dashboard', 'woocommerce' ),
        'orders'            => __( 'Orders', 'woocommerce' ),
        'subscriptions'            => __( 'Subscriptions', 'woocommerce' ),
        //'downloads'       => __( 'Downloads', 'woocommerce' ),
        'edit-address'    => __( 'Addresses', 'woocommerce' ),
        'payment-methods' => __( 'Payment Methods', 'woocommerce' ),
        'edit-account'      => __( ' Account details', 'woocommerce' ),
        'help-center'      => __( ' Resources Library', 'woocommerce' ),
        'wishlist'      => 'Wishlist',
        'mystore'      => __( 'My Store', 'woocommerce' ),
        'customer-logout'   => __( 'Log out', 'woocommerce' ),
    );
      
    return $items;
}

add_filter( 'woocommerce_account_menu_items', 'my_custom_my_account_menu_items' );

function my_cookie_check1() { ?>


<?php }

function my_cookie_check() { ?>
 <script src="https://code.jquery.com/jquery-latest.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
 <script>
      

    
jQuery(document).ready(function(){ 
     
   
jQuery('input.new_pro').click(function() {
   
var id11 = jQuery(this).val();
 
$value = '_'+id11;
jQuery.ajax({
                type: 'POST',
                url: "/wp-admin/admin-ajax.php",
                data: {
    action:'get_data', //this value is first parameter of add_action
    text:  $value,
},
beforeSend : function(){
             
    },
 
 
                success: function(result){
                    console.log("hai")
                     
                }
            });
            
           
});
 $(".flaticon-shopping-cart-1").after(" Add To Cart");
 
 var variationCustomLabelFee = jQuery("dd.variation-CustomLabelFee").text();
 
 if(variationCustomLabelFee  === ''){
     
     jQuery("dt.variation-CustomLabelFee").remove();
     jQuery("dt.variation-CustomLabelFee").css("display", "none");
      if($('.coupon_question').is(":checked")){   
     jQuery("dt.variation-CustomLabelFee").remove();
      }
      
}
jQuery( document ).on( 'updated_checkout', function() { 
    
   var variationCustomLabelFee = jQuery("dd.variation-CustomLabelFee").text();
 
 if(variationCustomLabelFee  === ''){
     jQuery("dt.variation-CustomLabelFee").remove();
     jQuery("dt.variation-CustomLabelFee").css("display", "none");
      if($('.coupon_question').is(":checked")){   
     jQuery("dt.variation-CustomLabelFee").remove();
     jQuery("dd.variation-CustomLabelFee p").remove();
     
 }
}

});
 jQuery("#elementor-menu-cart__toggle_button").click(function() {
    var variationCustomLabelFee = jQuery("dd.variation-CustomLabelFee").text();
 
     if(variationCustomLabelFee  === ''){
         jQuery("dt.variation-CustomLabelFee").remove();
         jQuery("dt.variation-CustomLabelFee").css("display", "none");
     }
  });
});
</script>
<script>
    jQuery(document).ready(function($) {
        <?php
        foreach( WC()->cart->get_cart() as $cart_item ){
        $id = $cart_item['product_id'];
         $product = $cart_item['data'];
         $sku =  $product->sku ;
         $label_imgs = $cart_item['label_imgs'];
         $label_price = $cart_item['label_price'];
        }
        ?>
        var label_imgs = <?php echo $label_imgs ?>;
       if(label_imgs != ''){
       $('form.checkout').submit(function() {
            // Show loader and text
            $('<div class="loader-overlay"><div class="loader"></div></br><p>Your order is being proccesed, you will be redirected to the catalog when it is done</p></div>').appendTo('body');

            // Optionally, you might want to disable the checkout button to prevent double submission
            $('button[type="submit"]').attr('disabled', 'disabled');
       });
       } 
    });
</script>
   <?php
}
add_action('wp_head', 'my_cookie_check',10);
function pr_disable_admin_notices() {
        global $wp_filter;
            if ( is_user_admin() ) {
                if ( isset( $wp_filter['user_admin_notices'] ) ) {
                                unset( $wp_filter['user_admin_notices'] );
                }
            } elseif ( isset( $wp_filter['admin_notices'] ) ) {
                        unset( $wp_filter['admin_notices'] );
            }
            if ( isset( $wp_filter['all_admin_notices'] ) ) {
                        unset( $wp_filter['all_admin_notices'] );
            }
    }
add_action( 'admin_print_scripts', 'pr_disable_admin_notices' );

// admin_init action works better than admin_menu in modern wordpress (at least v5+)
add_action( 'admin_init', 'my_remove_menu_pages' );
function my_remove_menu_pages() {

   remove_menu_page('edit.php?post_type=sa_slider'); //sa slider
   remove_menu_page('tinvwl'); // ti wishlist
   //remove_menu_page('eael-settings'); //essential addons
   remove_menu_page('tools.php'); // tools
   //remove_menu_page('wp-mail-smtp'); // wp-mail-smtp
   remove_menu_page('mailchimp-for-wp'); // mailchimp for wp
   remove_menu_page('wc-admin&path=/payments/overview'); // payments
   remove_menu_page('wc-admin&path=/analytics/overview'); // analaytics
   remove_menu_page('wc-admin&path=/marketing'); // marketing
   remove_menu_page('edit-comments.php'); // comments
   //remove_menu_page('elementor'); // elementor
   //remove_menu_page('edit.php?post_type=acf-field-group'); // Acf
   //remove_menu_page('plugins.php'); // Plugin
  
}


add_action( 'admin_menu', 'rename_woocoomerce_admin_menu', 999 );

function rename_woocoomerce_admin_menu()
{
    global $menu;

    // Pinpoint menu item
    $woo = recursive_array_search_php( 'WooCommerce', $menu );
    $products = recursive_array_search_php( 'Products', $menu );

    // Validate
    if( !$woo )
        return;

    $menu[$woo][0] = 'DropShipping';
    $menu[$products][0] = 'Catalog Management';
}



// http://www.php.net/manual/en/function.array-search.php#91365
function recursive_array_search_php( $needle, $haystack )
{
    foreach( $haystack as $key => $value )
    {
        $current_key = $key;
        if(
            $needle === $value
            OR (
                is_array( $value )
                && recursive_array_search_php( $needle, $value ) !== false
            )
        )
        {
            return $current_key;
        }
    }
    return false;
}

add_action( 'admin_menu', 'linked_url' );
    function linked_url() {
    add_menu_page( 'linked_url', 'Dropshipper Stores', 'read', 'network/sites.php', '', 'dashicons-text', 30 );
    add_menu_page( 'linked_url', 'Network Orders', 'read', 'https://vitalaunch.io/wp-admin/network/admin.php?page=network-orders', '', 'dashicons-text', 30 );
    }

    add_action( 'admin_menu' , 'linkedurl_function' );
    function linkedurl_function() {
    global $menu;
    $menu[1][2] = "https://vitalaunch.io/wp-admin/network/sites.php";
    $menu[1][3] = "https://vitalaunch.io/wp-admin/network/admin.php?page=network-orders";
}

add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );

function my_custom_js() {
    ?>
    
    <script type="text/javascript" >
 /*     setTimeout(function() {
            alert();   
   loadJs("http://code.jquery.com/jquery-latest.js");
   loadJs("https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js");
  }, 12000);
 function loadjs(filename) {
    var fileref=document.createElement('script');
    fileref.setAttribute("type","text/javascript");
    fileref.setAttribute("src", filename);
} */
        
     
    $(document).ready(function(){
       $('input.new_pro').click(function() {
   
var id11 = $(this).val();
 alert('hi');
$value = '_'+id11;
$.ajax({
                type: 'POST',
                url: "/wp-admin/admin-ajax.php",
                data: {
    action:'get_data', //this value is first parameter of add_action
    text:  $value,
},
beforeSend : function(){
             
    },
 
 
                success: function(result){
                    console.log("hai")
                     
                }
            });
            
           
});
      
       $("#site-address").keydown(function(){
        //alert($(this).val());
        $("input[name=target_name]").val($(this).val());
        
      });
      $("#site-title").keydown(function(){
        //alert($(this).val());
        $("input[name=target_title]").val($(this).val());
        $("select[name=source_id] option:eq(1)").attr("selected", "selected");
      });
      $("#add-site").keydown(function(){
         $(".ns-cloner-button").submit();
      });
    });
    $(document).ready(function(){
       $(".checkout_fill").click(function(){
        //alert("hi");
        $( "#orderqty" ).trigger( "submit" );
       });
    });
    

$(document).ready(function(){
    var bb = window.location.href;
    
   var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};
    $(".input-text.qty.text").attr("readonly","TRUE");
    
    var val1 = $(".pofw-product-options-wrapper .options-list .choice input:first-child").val();
    var val2 = $(".pofw-product-options-wrapper .options-list .choice:nth-child(2) input:nth-child(1)").val();
    
    var qty = getUrlParameter('qty');
     
    
    if(qty  == 1){
    $("#pofw_option_value_"+val1).attr("checked","TRUE");
    var value1 = $("label[for=pofw_option_value_"+val1+"] .pofw-price").html();
    var value2 = value1.replace('+','');
    
    }else if(qty  == 2){
    $("#pofw_option_value_"+val2).attr("checked","TRUE");
    var value1 = $("label[for=pofw_option_value_"+val2+"] .pofw-price").html();
    var value2 = value1.replace('+','');
    
    }
     else{
    $("#pofw_option_value_"+val1).attr("checked","TRUE");
    var value1 = $("label[for=pofw_option_value_"+val1+"] .pofw-price").html();
    var value2 = value1.replace('+','');
    
    }
    $(".inner-shop-details-content .woocommerce-Price-amount.amount").replaceWith("<span class='woocommerce-Price-amount amount'>"+value2+"</span");
    $(".pofw-option").change(function(){
        
        var valuee = $(this).val();
        
        
        if(valuee === val1){
            
            var value1 = $("label[for=pofw_option_value_"+val1+"] .pofw-price").html();
            currLoc = window.location.href.split('?')[0];
            var url = value1.replace('+','');
            window.location.href = currLoc+'/?qty=1';
        }
        if(valuee === val2){
            
            var value1 = $("label[for=pofw_option_value_"+val2+"] .pofw-price").html();
            currLoc = window.location.href.split('?')[0];
            var url = value1.replace('+','');
           window.location.href = currLoc+'/?qty=2';
        }
        var value2 = value1.replace('+','');
        $(".inner-shop-details-content .woocommerce-Price-amount.amount").replaceWith("<span class='woocommerce-Price-amount amount'>"+value2+"</span");
        $(".woocommerce-Price-amount.amount").replaceWith("<span class='woocommerce-Price-amount amount'>"+value2+"</span");
    });
   
	
 });

</script>
    <?php
}
// Add hook for admin <head></head>
//add_action( 'admin_head', 'my_custom_js' );
// Add hook for front-end <head></head>
add_action( 'wp_head', 'my_custom_js' );

/**
function custom_field_in_cart() { 
    global $woocommerce; 
    echo '<form action="https://vitalaunch.io/checkout/" method="POST" id="orderqty">';
    echo '<div class="woocommerce-additional-fields">'; 
    echo '<div class="woocommerce-additional-field-wrapper">'; 
    echo '<input type="radio" name="order_qty" id="bulk" value="bulk">';
    echo '<label for="additional_field">Bulk</label>'; 
    echo '<input type="radio" name="order_qty" id="sample" value="sample">';
    echo '<label for="additional_field">sample</label>';
    echo '</div>'; 
    echo '</div>'; 
    echo '<input type="submit" name="submit" id="submit" value="submit">';
    echo '</form>';
} 
add_action( 'woocommerce_after_cart_table', 'custom_field_in_cart' );

    function save_custom_field_in_cart_item_data( $cart_item_data, $product_id ) { 
        if( isset( $_POST['order_qty'] ) ) { 
            $cart_item_data[ 'order_qty' ] = $_POST['order_qty']; 
            WC()->session->set( 'order_qty', $_POST['order_qty'] ); 
        } 
        return $cart_item_data; 
    } 
    add_action( 'woocommerce_add_cart_item_data', 'save_custom_field_in_cart_item_data', 10, 2 ); 
    
        function display_custom_field_in_order_review( $item_id, $values, $cart_item_key ) { 
        if( isset( $values['order_qty'] ) ) { 
            echo '<p><strong>Order Quantity:</strong> ' . $values['order_qty'] . '</p>'; 
        }
       
    } 
    add_action( 'woocommerce_checkout_create_order_line_item', 'display_custom_field_in_order_review', 10, 3 ); 
    
   
/*function custom_field_in_cart() { 
    global $woocommerce; 
 
    echo '<div class="woocommerce-additional-fields">'; 
    echo '<div class="woocommerce-additional-field-wrapper">'; 
    echo '<label for="additional_field">Additional Field:</label>'; 
    echo '<input type="text" name="additional_field" id="additional_field" value="">'; 
    echo '</div>'; 
    echo '</div>'; 
} 
add_action( 'woocommerce_before_cart_table', 'custom_field_in_cart' );
    function save_custom_field_in_cart_item_data( $cart_item_data, $product_id ) { 
        if( isset( $_POST['additional_field'] ) ) { 
            $cart_item_data[ 'additional_field' ] = $_POST['additional_field']; 
            WC()->session->set( 'additional_field', $_POST['additional_field'] ); 
        } 
        return $cart_item_data; 
    } 
    add_action( 'woocommerce_add_cart_item_data', 'save_custom_field_in_cart_item_data', 10, 2 ); 
        function display_custom_field_in_order_review( $item_id, $values, $cart_item_key ) { 
        if( isset( $values['additional_field'] ) ) { 
            echo '<p><strong>Additional Field:</strong> ' . $values['additional_field'] . '</p>'; 
        } 
    } 
    add_action( 'woocommerce_checkout_create_order_line_item', 'display_custom_field_in_order_review', 10, 3 ); 
    */

if (!session_id()) {
    session_start();
}
 
 
 
 
 


add_action( 'wp_ajax_nopriv_get_data', 'get_data' );
add_action( 'wp_ajax_get_data', 'get_data' );
 
function get_data() {
 
echo $prod_det = $_POST['text'];

$_SESSION['prod_det'] = $prod_det;
 
 
}
function custom_text_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'In stock' :
            $translated_text = 'Product inventory';
            break;
         case 'Stock' :
            $translated_text = 'Product inventory';
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'custom_text_strings', 20, 3 );
/* Update cart amounts when changing quantities */
add_action( 'wp_footer', 'wphelp_update_cart_when_changing_quantities' );
function wphelp_update_cart_when_changing_quantities() {
     
     ?>
     <div id="loader-wrapper" style="display:none;">
    <div id="loader"> </div>
    <p style="  position: absolute; margin-top: 200px; font-weight: 800; ">Please wait your Label is uloading on vitalaunch </p>
</div>
<style>
    /* Loader Styles */
#loader-wrapper {
    display: none ;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

#loader {
    border: 16px solid #3498db;
    border-top: 16px solid #ffffff;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

</style>
     <?php
     
if (is_cart()) :
?>
<script>
jQuery('div.woocommerce').on('change', '.qty', function(){
jQuery("[name='update_cart']").prop("disabled", false);
jQuery("[name='update_cart']").trigger("click"); 
});


   jQuery('.qty_button.plus').on('click', function (e) {
       alert('hi');
        e.preventDefault();
        alert('hi');
        var numProduct = Number(jQuery(this).next().val());
        if (numProduct > 1) {
            jQuery(this).next().val(numProduct - 1);
            jQuery('[name=update_cart]').prop({'disabled': false, 'aria-disabled': false });
        }
    });
    $('.btn-product-down').on('click', function (e) {
        e.preventDefault();
        var numProduct = Number($(this).prev().val());
            $(this).prev().val(numProduct + 1);
            $('[name=update_cart]').prop({'disabled': false, 'aria-disabled': false });
        }
    }); 
    jQuery(document).ready(function(){
        alert('email');
//var email = getUrlParameter('email');
    
        //jQuery("#email").val("email");
    });
</script>

 
 
<?php
endif;
}

/**

* Add a custom field to the checkout page

*/

//add_action('woocommerce_before_checkout_billing_form', 'custom_checkout_field');

function custom_checkout_field($checkout)

{
$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$order_qty = $_GET['order_qty'];
echo '<div id="custom_checkout_field"><h3>' . __('Please Provide The Custom Data') . '</h3>';

woocommerce_form_field('custom_field_name', array(

'type' => 'text',
	'required' => 'true',

'class' => array(

'my-field-class form-row-wide'

) ,

'label' => __('Custom Field') ,

'placeholder' => __('Enter Custom Data') ,
'value' => $order_qty,

) 			   ,

$checkout->get_value('custom_field_name'));

echo '</div>';

}

add_action( 'wp_footer', 'single_add_to_cart_event_text_replacement' );
function single_add_to_cart_event_text_replacement() {
    global $product;

    
    ?>
    <style>
.product_quty label {
	font-size: 20px;
	font-weight: 700;
	padding-left: 20px;
	color: black;
}
.tooltip {
	position: absolute;
	display: contents;
	border-bottom: 1px dotted black;
	color: #000;
	font-size: 14px;
	font-weight: 700;
}

.tooltip .tooltiptext {
	visibility: hidden;
	width: 25%;
	background-color: white;
	color: #000;
	text-align: left;
	border-radius: 6px;
	padding: 5px 0;
	position: absolute;
	z-index: 1;
	padding: 20px 20px 20px 20px;
	margin-left: 10px;
	font-weight: 400;
}
.tooltiptext b {
	font-weight: 700;
}
.tooltip:hover .tooltiptext {
  visibility: visible;
}
.product_quty label {
	margin-right: 6px;
}
.tooltip:hover {
	cursor: pointer;
}
.tooltip .question {
	background-color: #e9e9e9;
	padding: 1px 7px 1px 7px;
	border: 1px solid #bfbcbc;
	border-radius: 20px;
}
.options-list .pofw-price {
	display: none;
}
    </style>
        <script type="text/javascript">
        
            (function($){
                $('.add-cart-btn.cart-button').click( function(){
                    $(this).text('<?php _e( "Adding...", "woocommerce" ); ?>');
                });
            })(jQuery);
            $(".e-eicon-cart-medium").on('click', function(){
                
                var quantity1 =  $('.product-quantity').html();
                var T_quantity = quantity1.replace(' ×','');
                
                
                var subtotal =  $('.elementor-menu-cart__subtotal bdi').html();
                var T_subtotal = subtotal.replace('<span class="woocommerce-Price-currencySymbol">$</span>','');
                
                
                var price = T_subtotal / T_quantity;
                 $(".elementor-menu-cart__product-price .woocommerce-Price-amount.amount bdi").replaceWith("<span class='woocommerce-Price-currencySymbol'>$</span>"+price+".00");
            });
            var val1 = $(".pofw-product-options-wrapper .options-list .choice input:first-child").val();
            var val2 = $(".pofw-product-options-wrapper .options-list .choice:nth-child(2) input:nth-child(1)").val();
            
             $("label[for=pofw_option_value_"+val1+"] .pofw-price").after('    <div class="tooltip"><span class="question">?</span><span class="tooltiptext"><b>Sample order</b><br><br>Sample order is meant for Store owners only.<br>You can order samples at the base (production) price before selling them to ensure you are offering your customers the best. </span></div>    ');
             $("label[for=pofw_option_value_"+val2+"] .pofw-price").after('    <div class="tooltip"><span class="question">?</span><span class="tooltiptext"><b>Label order</b><br><br>Label Order is meant for your customers.<br>This is an easy workaround for Store owners selling Vitalaunch products outside Shopify, or Wordpress for example, want to send their customers some gifts.</span></div>    ');
            $("#eael_accept_tnc").attr("checked", "TRUE");
            $("#eael_accept_tnc").hide();
        </script>
    <?php
}
function ts_redirect_login( $redirect) {
    return '/shop';
}
add_filter( 'woocommerce_login_redirect', 'ts_redirect_login' );


add_action( 'admin_head', 'load_admin_style' );
function load_admin_style() {
     $user = wp_get_current_user();
$allowed_roles = array( 'administrator');
if( array_intersect($allowed_roles, $user->roles ) ) {  
}else{
?>
<style>
.inline-edit-wrapper .stock_fields {
	display: none !important;
}
.inline-edit-group.manage_stock_field {
	display: none !important;
}

</style>
<?php
}
?>
<style>
.sku_keys {
	display: grid;
	width: 100%;
	position: ;
}

    .core-updates {
	display: none;
}
.order_label_img {
	border-left: 1px solid;
}
.order_label_img tr td {
	width: 311px;
}
.order_label_img tr td{
	border-right: 1px solid;
	border-bottom: 1px solid;
	text-align: center;
	padding: 10px;
}
.order_label_img tr th {
	border-right: 1px solid;
	border-bottom: 1px solid;
	border-top: 1px solid;
	padding: 10px;
	text-align: center;
}
</style>
<?php
}

function woocommerce_quantity_input_min_callback( $min, $product ) {
    $qty_val = $_GET['qty'];
    if($qty_val == 1){
        $min = 1;  
    }else if($qty_val == 2){
        $min = 50;  
    }else{
        $min = 1;  
    }
    return $min;
}
add_filter( 'woocommerce_quantity_input_min', 'woocommerce_quantity_input_min_callback', 10, 2 );
function custom_redirect() {            if( is_shop() && ! is_user_logged_in() ) {
        wp_redirect( home_url('/my-account') ); 
        exit();
    }   
}
add_action("template_redirect","custom_redirect");

function login_shortcode() { 
  if(is_user_logged_in()){
      ?>
      <style>
          /*.f_l_name #menu{height:45px;}*/
            .f_l_name #menu ul,.f_l_name #menu li{margin:0;padding:0;list-style:none;}
         
            .f_l_name #menu li {
  position: relative;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 45px;
    width: 53px;
    font-size: 26px;
    font-weight: normal;
}
ul.dashMenu li {
    width: 230px !IMPORTANT;
    background: #fff !important;
    right: 0px;
}
            /*.f_l_name #menu li ul li {float:left;display:inline;position:relative;font:bold 13px Arial;border-bottom: 1px solid #ccc;border-radius: 6px;}*/
            .f_l_name #menu a {
    display: block;
    text-transform: uppercase !important;
    text-align: center;
    font-family: 'Oswald', sans-serif;
    font-size: 23px !important;
    margin: -7px 9px 0px;
    letter-spacing: -2px !important;
}
            
            .f_l_name #menu input{display:none;margin:0 0;padding:0 0;width:100%;height:45px;opacity:0;cursor:pointer}
            .f_l_name #menu label{font:bold 30px Arial;display:none;width:35px;height:45px;line-height:45px;text-align:center;color:#fff}
            .f_l_name #menu label span{font-size:13px;position:absolute;left:35px}
            /*.f_l_name #menu ul li ul{height:auto;overflow:hidden;position:absolute;z-index:99;color:#333;margin-top: 20px;border-radius: 5px;}*/
            /*.f_l_name #menu ul li ul li a{color:#333;width:180px;height:35px;line-height:35px;background:#f4f4f4;}*/
            
           
            .f_l_name #menu ul li ul li a:hover{background:#0d9b4d;color:white;}
            .elementor-element.elementor-element-e877eeb.e-con-full.e-flex.e-con.e-child {
	display: block !important;
}
           /* @media screen and (max-width: 600px){
            .f_l_name #menu{position:relative}
            .f_l_name #menu ul{background:#838383;position:absolute; top:100%;right:0;left:0;z-index:3;height:auto;display:none;}
            .f_l_name #menu ul.menus{width:100%;position:static;border:none}
            .f_l_name #menu li{display:block;float:none;width:auto;text-align:left}
            .f_l_name #menu li a{color:#fff}.f_l_name #menu a.prett.f_l_name #menu a.prett
            .f_l_name #menu li a:hover{color:#333}
            .f_l_name #menu li:hover{background:#BABABA;color:#333;}
            .f_l_name #menu li:hover > a.prett,#menu a.prett:hover{background:#BABABA;color:#333;}
            .f_l_name #menu ul.menus a{background:#BABABA;}
            .f_l_name #menu ul.menus a:hover{background:#fff;}
            .f_l_name #menu input,.f_l_name #menu label{position:absolute;top:0;left:0;display:block}
            .f_l_name #menu input{z-index:4;}
            .f_l_name #menu ul li ul li a{width:100%;}
        */
ul.dashMenu {
    position: relative;
    z-index: 99;
    height: 45px;
    background: #fff;
    box-shadow: rgba(0, 0, 0, 0.07) 0px 0px 30px;
    border-radius: 15px !important;
    transform: translate3d(-71px, 60px, 0px);
}
ul.dashMenu li a {
    color: #000;
    width: 180px;
    background: #fff;
    display: block;
    text-transform: uppercase !important;
    font-weight: 400;
    text-align: left !important;
}
ul.dashMenu li {
    height: 0px !important;
    background: #fff;
    border: 0px !important;
    border-radius: 0px !important;
    padding:20px 20px !important;
}
.f_l_name #menu ul.dashMenu li a {
    color: #000;
    width: 180px;
    background: #fff;
    display: block;
    text-transform: capitalize !important;
    font-weight: 400 !important;
    text-align: left !important;
    padding: 0px 8px;
    font-size: 18px !important;
    letter-spacing: 0px !important;
    margin: 0px !important;
    font-family: "Poppins", Sans-serif !important;
}
      </style>
      <script>
      jQuery(document).ready(function($){
          var nav = $('.f_l_name #menu > ul > li');
            nav.find('li').hide();
            nav.click(function () {
                nav.not(this).find('li').hide();
                $(this).find('li').slideToggle();
            });
            $(function() {  
                $('.f_l_name #menu input').click(function () { 
                $('.f_l_name #menu ul').slideToggle() 
            });
                });
            $(".woocommerce-MyAccount-navigation-link--dashboard").html('<a href="/shop/">Catalog<div></div></a>');
      });
      </script>
      

      <?php
      $current_user = wp_get_current_user();
      $full_f_name = $current_user->user_firstname;
      $first_name = substr($full_f_name, 0, 1);
      $full_l_name = $current_user->user_lastname;
      $last_name = substr($full_l_name, 0, 1);
       $full_nicename = $current_user->user_nicename;
      $nicename = substr($full_nicename, 0, 1);
      $user_id = $current_user->ID;
      $user_flag = get_user_meta($user_id, 'user_flag', TRUE);
     $sc =  WC_Subscriptions_Manager::get_users_subscriptions(get_current_user_id()); 

    foreach($sc as $scs){
        $s_id = $scs['order_id']+1;
    }

      if(!empty($first_name)){
     $message =   "<div class='f_l_name'><a href='/my-account'>".$first_name . ' ' . $last_name."</a></div>" ;
      }else{
           $message =   "<div class='f_l_name'><a href='/my-account'>".$nicename ."</a></div>" ;
      }
     
         $message = '<div class="f_l_name">
     <nav id="menu">
        <input type="checkbox"/>
        <label>&#8801;<span>Navigation</span></label>
        <ul>
        
        <li><div id="prettList"><a class="prett" href="#" title="Drop Menu">'.$first_name . ' ' . $last_name.'</a></div>
        <ul class="dashMenu">
        <li><a href="/shop/" title="Catalog">Catalog</a></li>
        <li><a href="/my-account/orders" title="Orders">Orders</a></li>
        <li class="cus_sub"><a href="/my-account/view-subscription/'.$s_id.'" title="Subscriptions">Subscriptions</a></li>
       
        <li><a href="/my-account/edit-address" title="Addresses">Addresses</a></li>
        <li><a href="/my-account/payment-methods" title="Payment Methods">Payment Methods</a></li>
        <li><a href="/my-account/edit-account" title=" Account details"> Account details</a></li>
         <li><a href="/help-center/" title=" Resources Library"> Resources Library</a></li>
         
        <li><a href="/my-account/mystore" title="My Store">My Store</a> </li>
       
        <li><a href="'. wp_logout_url( home_url()).'" title="Logout">Logout</a></li>
        
        </ul>
        </li>
        </ul>
        </nav></div>' ;
     
    
  }else{
      
      $message = '<a class="logBtn" href="https://vitalaunch.io/login/">Log in</a> <a class="singupBtn" href="/register/">Sign up</a>';
  }
return $message;
}
// register shortcode
add_shortcode('login_short_code', 'login_shortcode');

//* Add Logged In/Out class to <body> with WordPress
add_filter( 'body_class', 'login_status_body_class' );
function login_status_body_class( $classes ) {
	
  if (is_user_logged_in()) {
    $classes[] = 'logged-in';
  } else {
    $classes[] = 'logged-out';
  }
  return $classes;
	
}


// Our custom post type function
function create_posttype() {
  
    register_post_type( 'help-center',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Help Center' ),
                'singular_name' => __( 'Help Center' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'help-center'),
            'show_in_rest' => true,
  
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );

/*
* Creating a function to create our CPT
*/
  
function custom_post_type() {
  
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Help Center', 'Post Type General Name', 'twentytwentyone' ),
        'singular_name'       => _x( 'Help Center', 'Post Type Singular Name', 'twentytwentyone' ),
        'menu_name'           => __( 'Help Center', 'twentytwentyone' ),
        'parent_item_colon'   => __( 'Parent Help Center', 'twentytwentyone' ),
        'all_items'           => __( 'All Help Center', 'twentytwentyone' ),
        'view_item'           => __( 'View Help Center', 'twentytwentyone' ),
        'add_new_item'        => __( 'Add New Help Center', 'twentytwentyone' ),
        'add_new'             => __( 'Add New', 'twentytwentyone' ),
        'edit_item'           => __( 'Edit Help Center', 'twentytwentyone' ),
        'update_item'         => __( 'Update Help Center', 'twentytwentyone' ),
        'search_items'        => __( 'Search Help Center', 'twentytwentyone' ),
        'not_found'           => __( 'Not Found', 'twentytwentyone' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwentyone' ),
    );
      
// Set other options for Custom Post Type
      
    $args = array(
        'label'               => __( 'help center', 'twentytwentyone' ),
        'description'         => __( 'Help Center news and reviews', 'twentytwentyone' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'genres' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,
  
    );
      
    // Registering your Custom Post Type
    register_post_type( 'help-center', $args );
  register_taxonomy("categories", array("help-center"), array("hierarchical" => true, "label" => "Categories", "singular_label" => "Category", "rewrite" => array( 'slug' => 'category-help-center', 'with_front'=> false )));
}
  
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
  
add_action( 'init', 'custom_post_type', 0 );





function my_custom_jss() { ?>
<script>
    jQuery(document).ready(function()
    {
    
	$("#load-img").hide();
	
     jQuery('#register1').click(function()
     {
         

		    var reg_username = jQuery('#reg_username').val();

        var reg_email = jQuery('#reg_email').val(); 

         var reg_password = jQuery('#reg_password').val();

        var subscription_plan = jQuery('#subscription_plan').val(); 
       
       // if (reg_username && reg_email && reg_password && subscription_plan  ) {
        
            jQuery("#load-img").show();
         jQuery("#register1").hide();
			var vv = jQuery('#subscription_plan').val();

			jQuery.ajax({

					type: 'POST',

					url: "/wp-admin/admin-ajax.php",

					data: {

		action:'sendtoproduct', //this value is first parameter of add_action

		text:  vv,

	},

	beforeSend : function(){

		},

               
					success: function(result){

						console.log("hai")

						window.location.href= "/checkout/?&reg_email="+reg_email+"&reg_user="+reg_username+"&reg_pass="+reg_password;

					}

				});

       /* } else {

          alert('All Fields Requried. Please fill them in.');

        }*/

		});
   // $(".pofw-option").change(function(){
        // Change src attribute of image
       /* var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;
        
            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');
        
                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };
		let ser = getUrlParameter('qty');
	
		var label_img = '';
		if(ser == 2){
		<?php
	//	$label_img = get_field('label');
		?>
		var label_img = '<?php echo $label_img ?>';
		
		if(label_img){
       // $(".woocommerce-product-gallery__image").attr("data-thumb", label_img);
	   // $(".woocommerce-product-gallery__image a img").attr("srcset", label_img);
	   // $(".flex-control-nav.flex-control-thumbs").css("display", "none !important");
		}
		}
		var cart_label = $("tr.cart_item:nth-child(1) .variation-ProductType p").html();
		var rowCount = $('.woocommerce-cart-form__contents tbody tr').length;
		if(cart_label == 'Label'){
		<?php /*
		global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        
        foreach($items as $item =>z $values) { 
            $_product =  wc_get_product( $values['data']->get_id()); 
            $_id = $_product->id;
            $_img_url = get_field('label', $_id);
            if(!empty($_img_url)){
            ?>
            var label_img_url = '<?php echo $_img_url ?>';
            
           // $(".product-thumbnail a img").attr("srcset", label_img_url);
       <?php } } */?>*/
		}
    //});
    });
</script>
<?php }
add_action( 'wp_head', 'my_custom_jss' );
 
 
add_action('wp_ajax_sendtoproduct', 'sendtoproduct');
add_action('wp_ajax_nopriv_sendtoproduct', 'sendtoproduct');
function sendtoproduct()
{	
    $product_id = $_POST['text'];
 
// Quantity of the product to add to the cart
$quantity = 1; 
// Add the product to the cart
$cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
 
// Optionally, you can redirect the user to the cart page after adding the product
if ($cart_item_key) {
    $cart_url = wc_get_cart_url();
    wp_safe_redirect($cart_url);
    exit;
}
}

function register_subscription_shortcode()
{	
    $register_subscription_shortcode = '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <p> <label for="reg_password"><?php esc_html_e( "Select a Subscription", "woocommerce" ); ?>&nbsp;<span class="required">*</span></label></p>
        <select name="subscription_plan " id="subscription_plan" class="woocommerce-Input woocommerce-Input--text input-text">
        <option value="1699">Basic Plan</option>
        <option value="1700">Pro Plan</option>
    </select>
        </p>';
        return $register_subscription_shortcode; 
}
add_shortcode('register_subscriptions', 'register_subscription_shortcode');

add_shortcode( 'wc_reg_form_bbloomer', 'bbloomer_separate_registration_form' );
     
function bbloomer_separate_registration_form() {
   if ( is_user_logged_in() ) return '<p>You are already registered</p>';
   ob_start();
   do_action( 'woocommerce_before_customer_login_form' );
   $html = wc_get_template_html( 'myaccount/form-login.php' );
   $dom = new DOMDocument();
   $dom->encoding = 'utf-8';
   $dom->loadHTML( utf8_decode( $html ) );
   $xpath = new DOMXPath( $dom );
   $form = $xpath->query( '//form[contains(@class,"register")]' );
   $form = $form->item( 0 );
   echo $dom->saveXML( $form );
   return ob_get_clean();
}
function custom_registration_redirect() {
    
    return home_url('/shop/');
    
}
add_action('woocommerce_registration_redirect', 'custom_registration_redirect', 2);

remove_action('load-update-core.php','wp_update_plugins');
add_filter('pre_site_transient_update_plugins','__return_null');

function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','remove_core_updates');
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');

/* Add to the functions.php file of your theme/plugin */add_filter( 'woocommerce_order_button_text', 'wc_custom_order_button_text' ); 
function wc_custom_order_button_text() {
    return __( 'Place Order', 'woocommerce' ); 
    

}

add_filter( 'woocommerce_checkout_fields' , 'ahmadyani_checkout_field_defaults', 20 );function ahmadyani_checkout_field_defaults( $fields ) {
    $reg_email = $_GET['reg_email'];
    $reg_user = $_GET['reg_user'];
    $reg_pass = $_GET['reg_pass'];
    
    $fields['billing']['billing_email']['default'] = $reg_email;
    //$fields['billing']['account_username']['default'] = $reg_user;
    //$fields['billing']['account_password']['default'] = $reg_pass;
    
    return $fields;
}

add_filter( 'woocommerce_product_categories_widget_args', 'woo_product_cat_widget_args' );

 

function woo_product_cat_widget_args( $cat_args ) {

                $cat_args['exclude'] = array('50');

                return $cat_args;

}
add_action('template_redirect', 'redirect_user_role');
function redirect_user_role() {
   
       $url      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url_path = parse_url($url, PHP_URL_PATH);
$basename = pathinfo($url_path, PATHINFO_BASENAME);
    if ( $basename == "my-account" ) { wp_redirect('https://vitalaunch.io/login/'); }
    
    
}


add_filter( 'manage_woocommerce_page_wc-orders_columns', 'add_wc_order_list_custom_column' );
function add_wc_order_list_custom_column( $columns ) {
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;

        if( $key ===  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['my-column1'] = __( 'Tracking ID','theme_domain');
            $reordered_columns['my-column2'] = __( 'Carrier Service','theme_domain');
        }
    }
    return $reordered_columns;
}

add_action('manage_woocommerce_page_wc-orders_custom_column', 'display_wc_order_list_custom_column_content', 10, 2);
function display_wc_order_list_custom_column_content( $column, $order ){
    switch ( $column )
    {
        case 'my-column1' :
            // Get custom order metadata
            
            $value = $order->get_id();
            if ( ! empty($value) ) {
                
                // Get order notes
                $order_notes = wc_get_order_notes( array(
                    'order_id'  => $value,
                    'order_by'  => 'date_created',
                    'order'     => 'ASC',
                ));
                // Notes is NOT empty
                if ( ! empty( $order_notes ) ) {
                    foreach ( $order_notes as $order_note ) {
                        // PHP 8
                        if ( str_contains( $order_note->content, 'tracking number' ) ) {
                            $track = $order_note->content;
                        }
                    }
                }
                
                $tracking = explode(' ',$track);
                $trackingnumber = end($tracking);
                if ( ! empty($trackingnumber) ) {
                    echo $trackingnumber;
                }else{
                    echo "Not Generated";
                }
            }
            // For testing (to be removed) - Empty value case
            else {
                echo '<small>(<em>no value</em>)</small>';
            }
            break;

        case 'my-column2' :
            // Get custom order metadata
            /*$value = $order->get_meta('_the_meta_key2');
            /*if ( ! empty($value) ) {
                echo $value;
            }
            // For testing (to be removed) - Empty value case
            /*else {
               // echo '<small>(<em>no value</em>)</small>';
            }*/
            
             $value = $order->get_id();
            if ( ! empty($value) ) {
                
                // Get order notes
                $order_notes = wc_get_order_notes( array(
                    'order_id'  => $value,
                    'order_by'  => 'date_created',
                    'order'     => 'ASC',
                ));
                // Notes is NOT empty
                if ( ! empty( $order_notes ) ) {
                    foreach ( $order_notes as $order_note ) {
                        // PHP 8
                        if ( str_contains( $order_note->content, 'tracking number' ) ) {
                            $track = $order_note->content;
                        }
                    }
                }
                
                //$tracking = explode(' ',$track);
                $shipped = explode("shipped via",$track);
                $shippe = explode("on",$shipped[1]);
                //$trackingnumber = end($tracking);
                if ( ! empty($shippe) ) {
                    echo $shippe[0];
                    
                }else{
                    echo "Not Generated";
                }
            }
            break;
    }
}
 function custom_pre_get_posts_query( $q ) {

        // Do your cart logic here

        // Get ids of products which you want to hide
        
        $producthidevaluearray=array();
        $args = array( 'post_type' => 'product', 'posts_per_page' => -1);
        $the_query = new WP_Query( $args ); 
        if ( $the_query->have_posts() ) : 
            while ( $the_query->have_posts() ) : $the_query->the_post(); 
            $producthidevalue  = get_field('test');
            if($producthidevalue=='no'){
                $p_ids = get_the_ID();
                array_push($producthidevaluearray, $p_ids);
            }
            endwhile;
        wp_reset_postdata();
        else:  
        endif; 
       
        $q->set( 'post__not_in', $producthidevaluearray );

    }
    add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );
    
 
add_filter( 'woocommerce_related_products', 'exclude_related_products', 10, 3 );
function exclude_related_products( $related_posts, $product_id, $args ){
    // HERE set your product IDs to exclude
     $targeted_products=array();
        $args = array( 'post_type' => 'product', 'posts_per_page' => -1);
        $the_query = new WP_Query( $args ); 
        if ( $the_query->have_posts() ) : 
            while ( $the_query->have_posts() ) : $the_query->the_post(); 
            $producthidevalue  = get_field('test');
            if($producthidevalue=='no'){
                $p_ids = get_the_ID();
                array_push($targeted_products, $p_ids);
            }
            endwhile;
        wp_reset_postdata();
        else:  
        endif; 
        

    return array_diff( $related_posts, $targeted_products );
}

add_action('woocommerce_after_add_to_cart_button','cmk_additional_button');
function cmk_additional_button() {
    ?> 
    
    
    
    <style>
       .cus_button .button.alt {
        width: 100%;
    font-size: 14px;
    height: 50px;
    background-color: #000;
    color: white;
    border-radius: 10px;
    text-align: center;
    vertical-align: middle !important;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor:pointer;
    margin:10px 0px;
        }
 .row.cus_button .col-lg-6 {
    padding: 0 8px;
}
.row.cus_button {
    width: 100%;
}
.con_content_1, .con_content_2 {
    background: #fff;
    padding: 15px 0px;
    margin: 20px 0px;
    border-radius: 20px;
    border: 2px solid #9999997d;
}
.cont {
    padding: 10px 20px;
}
.cont b {
    color: #2d2d2d;
    font-weight: 600;
    font-size: 18px;
}
.cont p {
    color: #777;
    font-size: 16px;
    padding: 5px 0px;
    margin-bottom
}
a.canvaBtn {
	background: #FFAB17;
	color: #fff;
	padding: 3px 7px 4px 7px;
	font-size: 16px;
	border-radius: 30px;
}
.canvasDiv {
    margin-bottom: 20px;
}

.uploadBtn button {
    background: #FFAB17;
    color: #fff;
    padding: 5px 20px;
    font-size: 14px;
    border-radius: 30px;
    border: 0px;
}
.con_content_2 label {
    color: #2d2d2d;
    font-size: 16px;
    padding-left: 10px;
    font-weight: 600;
}
.con_content_2 {
    width: 100%;
}
.inputDiv {
    margin: 10px 0;
    display: flex;accept
    align-items: center;
}

.inputDiv .tooltip .question {
    margin-left: 10px;
    font-size: 12px;
}
.inputDiv .tooltip .tooltiptext {
    left: 77%;
    width: 20%;
}
.uploadBtn div {
	background: #FFAB17;
	color: white;
	padding: 5px 15px 5px 15px;
	border-radius: 30px;
	text-align: right;
	float: left;
	/* margin-right: 7px; */
	font-weight: normal;
	
}
@media screen and (max-width: 762px) {

  .inputDiv label {
    font-size: 12px;
    font-weight: 500;
}
.inputDiv .tooltip .question {
    margin-left: 10px;
    font-size: 10px;
}
}
  </style>
    <script>
        $(document).ready(function(){
            
            
            $(".con_content_1").hide();
            $(".con_content_2").hide();
            $(".row.cus_button").hide();
          $(".content_1").click(function(){
            $(".con_content_1").show();
             $(".con_content_2").hide();
          });
          $(".content_2").click(function(){
            $(".con_content_2").show();
             $(".con_content_1").hide();
          });
          $(".cus_label_cls").click(function(){
            var c_value = $(this).val();
           $('select[name="attribute_label"] option').removeAttr("selected");
            $('select[name="attribute_label"] option[value="'+c_value+'"]').attr('selected','selected');
            
          });r
           var getUrlParameterr = function getUrlParameterr(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;
        
            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');
        
                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };
		let serr = getUrlParameterr('qty');
		 $('.product-thumbnail').after('<b>Test</b>'); 
	
		if(serr == 2){
		     //$(".cart-btn.product-quantity-button").hide();
		     
		    $("#simple_pro").attr("style","display:none;");
         $(".row.cus_button").show();
         var getUrlParameter = function getUrlParameter(sParam) {
                    var sPageURL = window.location.search.substring(1),
                        sURLVariables = sPageURL.split('&'),
                        sParameterName,
                        i;
                
                    for (i = 0; i < sURLVariables.length; i++) {
                        sParameterName = sURLVariables[i].split('=');
                
                        if (sParameterName[0] === sParam) {
                            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                        }
                    }
                    return false;
                };
             
                var a = getUrlParameter('img');
               
         /*
                    $(".single-product .wp-post-image").attr("src", "https://vitalaunch.io/wp-content/uploads/2024/02/MicrosoftTeams-image-1.png");
                    $(".single-product .wp-post-image").attr("src", "https://vitalaunch.io/wp-content/uploads/2024/02/MicrosoftTeams-image-1.png");
	                $(".single-product .wp-post-image").attr("data-src", "https://vitalaunch.io/wp-content/uploads/2024/02/MicrosoftTeams-image-1.png");
	                $(".single-product .wp-post-image").attr("data-large_image", "https://vitalaunch.io/wp-content/uploads/2024/02/MicrosoftTeams-image-1.png");
	                $(".single-product .wp-post-image").attr("srcset", "https://vitalaunch.io/wp-content/uploads/2024/02/MicrosoftTeams-image-1.png");
	                $(".single-product .wp-post-image a").attr("href", "https://vitalaunch.io/wp-content/uploads/2024/02/MicrosoftTeams-image-1.png");
          if(a){
                   $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-image", "url("+a+")");
                   $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-position", "43px 220px");
	                $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-repeat", "no-repeat");
	                $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-size", "89% 52%");
	                
	               
	                $(".single-product .woocommerce-product-gallery__image").css("background-image", "url("+a+")");
                   $(".single-product .woocommerce-product-gallery__image").css("background-position", "43px 220px");
	                $(".single-product .woocommerce-product-gallery__image").css("background-repeat", "no-repeat");
	                $(".single-product .woocommerce-product-gallery__image").css("background-size", "89% 52%");
	                
	                $(".single-product .flex-control-nav.flex-control-thumbs").css("display", "none");
	                 $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
          }else{
	                
	                $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
	                $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-image", "url('https://vitalaunch.io/wp-content/uploads/2024/02/cus2.png')");
	   
	                $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-position-x", "-125px");
	                $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-position-y", "166px");
	                $(".single-product .woocommerce-product-gallery__image").css("background-image", "url('https://vitalaunch.io/wp-content/uploads/2024/02/cus2.png')");
	                $(".single-product .woocommerce-product-gallery__image").css("background-position-x", "-125px");
	                $(".single-product .woocommerce-product-gallery__image").css("background-position-y", "166px");
	                $(".single-product  .flex-control-nav.flex-control-thumbs").css("display", "none");
          }
	   */
	   
	          var bb = window.location.href;
  var bb= $('#static_path').val();
     
          if(a){
                   $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", a);
                    $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", a);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-src", a);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-large_image", a);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("srcset", a);
	                $(".single-product .woocommerce-product-gallery .wp-post-image a").attr("href", a);
	                
	                $(".single-product .flex-control-nav.flex-control-thumbs").css("display", "none");
	                 $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
          }
           else if(   bb == "Gummies1.png"){
           //else if(   bb == "https://vitalaunch.io/product/gummies/?qty=2"){
              var cc ="https://vitalaunch.io/wp-content/uploads/2024/02/Gummies1.png";
               //$('.canvasDiv a').removeAttr("href");
                //$('.canvasDiv a').attr("href","https://www.canva.com/design/DAF85j5Vb-A/FDw1zU-Jsy9EqG-n5UFDBw/edit?utm_content=DAF85j5Vb-A&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton");
            
              // $(".single-product .woocommerce-product-gallery").attr("style","background-image: url('https://vitalaunch.io/wp-content/uploads/2024/02/Gummies-1.png'); background-position: 84px 250px;background-size: contain;");
              $('#static_path').val('Gummies1.png');
               $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
                    $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-large_image", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("srcset", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image a").attr("href", cc);
	                
	                $(".single-product .flex-control-nav.flex-control-thumbs").css("display", "none");
	                 $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
          }
           //else if(   bb == "https://vitalaunch.io/product/capsules/?qty=2"){
           else if(   bb == "Capsulles-min.png"){
              var cc ="https://vitalaunch.io/wp-content/uploads/2024/02/Capsulles-min.png";
              // $('.canvasDiv a').removeAttr("href");
              //  $('.canvasDiv a').attr("href","https://www.canva.com/design/DAF85gZGA7s/cbk7v2Y13N3WHtMzV8EiDg/edit?utm_content=DAF85gZGA7s&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton");
             // $(".single-product .woocommerce-product-gallery").attr("style","background-image: url('https://vitalaunch.io/wp-content/uploads/2024/02/Gummies-1.png'); background-position: 84px 250px;background-size: contain;");
               $('#static_path').val('Capsulles-min.png');
               $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
                    $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-large_image", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("srcset", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image a").attr("href", cc);
	                
	                $(".single-product .flex-control-nav.flex-control-thumbs").css("display", "none");
	                 $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
          }
          else if(   bb == "powder-min.png"){
          // else if(   bb == "https://vitalaunch.io/product/powder/?qty=2"){
               
              var cc ="https://vitalaunch.io/wp-content/uploads/2024/02/powder-min.png";
             // $('.canvasDiv a').removeAttr("href");
               // $('.canvasDiv a').attr("href","https://www.canva.com/design/DAF85k9HDs0/mDpkDkcFKnUPAlO3Wa9olA/edit?utm_content=DAF85k9HDs0&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton");
             
              //$(".single-product .woocommerce-product-gallery").attr("style","background-image: url('https://vitalaunch.io/wp-content/uploads/2024/02/Gummies-1.png'); background-position: 84px 250px;background-size: contain;");
                $('#static_path').val('powder-min.png');
               $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
                    $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-large_image", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("srcset", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image a").attr("href", cc);
	                
	                $(".single-product .flex-control-nav.flex-control-thumbs").css("display", "none");
	                 $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
          }
          else if( bb = "capsule-original.png" ){
                var cc ="https://vitalaunch.io/wp-content/uploads/capsule-original.png";
             // $('.canvasDiv a').removeAttr("href");
              //  $('.canvasDiv a').attr("href","https://www.canva.com/design/DAF-E6oMmUc/80Mz_iTQ1Ihkv6YpGj8mCg/view?utm_content=DAF-E6oMmUc&utm_campaign=designshare&utm_medium=link&utm_source=publishsharelink&mode=preview");
             
              //$(".single-product .woocommerce-product-gallery").attr("style","background-image: url('https://vitalaunch.io/wp-content/uploads/2024/02/Gummies-1.png'); background-position: 84px 250px;background-size: contain;");
                $('#static_path').val('capsule-original.png');
               $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
                    $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-src", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-large_image", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("srcset", cc);
	                $(".single-product .woocommerce-product-gallery .wp-post-image a").attr("href", cc);
	                
	                $(".single-product .flex-control-nav.flex-control-thumbs").css("display", "none");
	                 $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
              
          }
          
          
          else{
              
               
	                
	                /* $(".single-product .woocommerce-product-gallery__image a").removeAttr("href");
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", "https://vitalaunch.io/wp-content/uploads/2024/02/background.png");
                    $(".single-product .woocommerce-product-gallery .wp-post-image").attr("src", "https://vitalaunch.io/wp-content/uploads/2024/02/background.png");
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-src", "https://vitalaunch.io/wp-content/uploads/2024/02/background.png");
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("data-large_image", "https://vitalaunch.io/wp-content/uploads/2024/02/background.png");
	                $(".single-product .woocommerce-product-gallery .wp-post-image").attr("srcset", "https://vitalaunch.io/wp-content/uploads/2024/02/background.png");
	                $(".single-product .woocommerce-product-gallery .wp-post-image a").attr("href", "https://vitalaunch.io/wp-content/uploads/2024/02/background.png");
	                
	                 $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-image", "url('https://vitalaunch.io/wp-content/uploads/2024/02/cus2.png')");
	   
	                $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-position-x", "-125px");
	                $(".single-product .woocommerce-product-gallery__image--placeholder").css("background-position-y", "166px");
	                $(".single-product .woocommerce-product-gallery__image").css("background-image", "url('https://vitalaunch.io/wp-content/uploads/2024/02/cus2.png')");
	                $(".single-product .woocommerce-product-gallery__image").css("background-position-x", "-135px");
	                $(".single-product .woocommerce-product-gallery__image").css("background-position-y", "216px");
	                $(".single-product  .flex-control-nav.flex-control-thumbs").css("display", "none");*/
          }
	       
		}
		$(".cus_labels").change(function(){
       /* <?php /*$p_id = get_the_id(); 
        //unset ($_SESSION["la"]);
        
        $s_id =  $_SESSION["la"];
        ?>
        var p_id = <?php echo $p_id ?>;
        var s_id = <?php echo $s_id ?>;
        var valuee = $(this).val();
       
        if(valuee == 100){
            
           alert(1);
            currLoc = window.location.href.split('?')[0];
            <?php $_SESSION["la"] = ""; 
            unset ($_SESSION["la"]);
            session_destroy(); 
            session_start();
            $_SESSION["la"] = "100";
            $s_id =  $_SESSION["la"]; ?>
            var s_id = <?php echo $s_id ?>;
             alert(s_id);
            //window.location.href = 'https://vitalaunch.io/cart/?add-to-cart='+p_id+'&qty=2&la=100';
        }
        else if(valuee == 150){
            alert(2);
           currLoc = window.location.href.split('?')[0];
         
           <?php $_SESSION["la"] = ""; 
           unset ($_SESSION["la"]);
            session_destroy(); 
            session_start();
           $_SESSION["la"] = "150"; 
           $s_id =  $_SESSION["la"]; ?>
            var s_id = <?php echo $s_id ?>;
             alert(s_id);
          // window.location.href = 'https://vitalaunch.io/cart/?add-to-cart='+p_id+'&qty=2&la=150';
        }
        else if(valuee == 200){
            alert(3);
            currLoc = window.location.href.split('?')[0];
            <?php $_SESSION["la"] = ""; 
            unset ($_SESSION["la"]);
             session_destroy(); 
            session_start();
            $_SESSION["la"] = "200"; 
            $s_id =  $_SESSION["la"]; ?>
            var s_id = <?php echo $s_id */ ?>;
             alert(s_id);
           // window.location.href = 'https://vitalaunch.io/cart/?add-to-cart='+p_id+'&qty=2&la=200';
        }
        
          var id11 = jQuery(this).val();
        var new11 = jQuery('.cart-btn').val();
        var qty = jQuery('.qty').val();
         
         alert(id11);
         if(id11 == 100)
         { var label_val = 5; }
         else if(id11 == 150)
         {
            var label_val = 10; 
         }
         else {
             var label_val = 15; 
         }
            $value = '_'+id11;
            jQuery.ajax({
                            type: 'POST',
                            url: "/wp-admin/admin-ajax.php",
                            data: {
                action:'get_data1', //this value is first parameter of add_action
                text:  $value,
                new11: new11,
                qty : qty,
                label_val: label_val 
            },
            beforeSend : function(){
                },
            
                            success: function(result){
                                window.location.href="https://vitalaunch.io/cart/?label_value="+id11;
                                console.log(new11)
                                console.log(id11)
                            }
                        });*/
                 var id11 = jQuery(this).val();
                 
                jQuery(".label_price").val(id11);
    });
    $(".dsg_t").click(function(){
        jQuery(".label_imgs").val(1);
    });
    $("#loader").hide(); 
    	$("#approve").on('click',function() {
    	    
           $("#imgupload").click();
           $("#loader").show();
           $('#loader-wrapper').removeAttr("style");
           $('#loader-wrapper').attr("style","display","block");
        setTimeout(function() {  
            setTimeout(function() {
                $("#loader").hide();
                $('#loader-wrapper').hide();
            }, 3000);  
        }, 3000);
        
           
        });
        $(".step5").hide(); 
    	$(".btn.dsg_t").on('click',function() {
    	     $(".step5").show();
    	});
        function readURL(input) {
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            //$('.avatar-preview-img').attr('src', e.target.result );
            $('#getFile').attr('value', e.target.result );
            $('.getFileimg').attr('src', e.target.result );
            $('#uploadimg').attr('value', e.target.result );
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#getFile").change(function() {
    readURL(this);
});

        });
        
    </script>
    <?php
    $productID = get_the_ID();
   // if($productID == "4746"){
    
	?>
	<div class="row cus_button">
	    <?php
	    $user = wp_get_current_user();
    $user_info = get_userdata($user->ID);
    $email = $user_info->user_email;
    $use_flag = get_user_meta($user->ID, 'user_flag', True);
     
     $qty_get = $_GET['qty'];
     if( $qty_get == 2){
    global $wpdb;    
    $result = $wpdb->get_results( "SELECT domain FROM wp_blogs INNER JOIN wp_registration_log ON wp_blogs.blog_id = wp_registration_log.blog_id WHERE email='".$email."'");
    //print_r($result);
    $store_own = $result[0]->domain;
    if($store_own != '' && empty($use_flag) ){ ?>
	 <style>
	     button.cart-btn.product-quantity-button {
    display: block;
}
.row.cus_button {
        	display: flex !important;
        }
	 </style>
	 <?php }else if($store_own == '' && $use_flag == 0 ){ ?>
	 <style>
	     button.cart-btn.product-quantity-button {
            display: none ;
        }
        .row.cus_button {
        	display: flex !important;
        }
        
	 </style>
	 <?php }else if($store_own == '' && $use_flag == 1 ){ ?>
	 <style>
	     button.cart-btn.product-quantity-button {
            display: block !important;
        }
	 </style>
	 <?php }else{ }
    if($store_own == '' && $use_flag == 0){ 
    ?>
    <style>
        .row.cus_button {
        	display: flex !important;
        }
        
    </style>
	    <div class = "col-lg-6">
	        <div  class="button alt content_1">Customize your Label</div>
	        
	    </div>
	    <div class = "col-lg-6 cus_button_vita">
	        <div  class="button alt content_2">Customize your Label by vitalaunch </div>
	    </div>
	  <?php }
	  else if($store_own == '' && $use_flag == 1){ 
    ?>
    <style>
        .row.cus_button {
        	display: flex !important;
        }
        
    </style>
	    <div class = "col-lg-6">
	        <div  class="button alt content_1">Customize your Label</div>
	        
	    </div>
	    <div class = "col-lg-6 cus_button_vita">
	        <div  class="button alt content_2">Customize your Label by vitalaunch </div>
	    </div>
	  <?php }
	  else if($store_own != '' && empty($use_flag)){ 
    ?>
    <style>
        .row.cus_button {
        	display: flex !important;
        }
        
    </style>
	    <div class = "col-lg-6">
	        <div  class="button alt content_1">Customize your Label</div>
	        
	    </div>
	    <div class = "col-lg-6 cus_button_vita">
	        <div  class="button alt content_2">Customize your Label by vitalaunch </div>
	    </div>
	  <?php }
	  else if($store_own == '' && empty($use_flag)){ 
    echo "<p style='color:red;'>Please Setup your store first to experience your store features.";    
    echo '<br><a class="startbutton" style="color:red;" href="https://vitalaunch.io/my-account/mystore" target="_blank">Start Store Setup</a>';
    }else { ?>
    <style>
        .row.cus_button {
        	display: flex !important;
        }
        
    </style>
	    <div class = "col-lg-6">
	        <div  class="button alt content_1">Customize your Label</div>
	        
	    </div>
	    <div class = "col-lg-6 cus_button_vita">
	        <div  class="button alt content_2">Customize your Label by vitalaunch </div>
	    </div><?php }?>
	 </div>
	 <?php //if($store_own != '' || $use_flag == 0){ ?>
	 
	 <div class="con_content_1">
	     <div class="cont">
	   
	    <?php $step_1= get_field('step_1:');
	    if(!empty($step_1)){
	        echo '<p>'.$step_1.'</p>';
	    }else{
	    ?>
	    <b>Step 1: Customize the Label</b>
	    <p>Design Your Label Online Using Canva </p>
	    
	    <?php } $canvasDiv= get_field('canvas');
	    if(empty($canvasDiv)){ ?>
	    <div class="canvasDiv">
            <a target="_blank" href="https://www.canva.com/design/DAF6sfO0Pbw/D_vLEd8lVrd5ZqorPGp_eA/view?utm_content=DAF6sfO0Pbw&utm_campaign=designshare&utm_medium=link&utm_source=publishsharelink&mode=preview" class="canvaBtn">Design Your Label Online Using Canva</a><br>
          </div> 
        <?php }else{ ?>
        <div class="canvasDiv">
            <a target="_blank" href="<?php echo $canvasDiv; ?>" class="canvaBtn">Design Your Label Online Using Canva</a><br>
          </div> 
        <?php } ?>
        
         <?php $step_2= get_field('step_2:');
	    if(!empty($step_2)){
	        echo '<p>'.$step_2.'</p>';
	    }else{
	    ?>
	    <b>Step 2:</b>
        <p>If you like your label on Canva, download it on PNG format</p>
        <?php } ?>
        <!--<b>Before uploading:</b>
        <ul>
            <li>Check that label size is: 2.5"(H) x 6"(W)</li>
            <li>Check required and allowed content</li>
            <li>Check colors and bleeding</li>
            <li>Place your business name and legal address on the label</li>
        </ul>
        <p>The label design has to pass the verification process otherwise fulfilment will be delayed. Learn more about our verification process.</p>-->
        
         <div class="dsg_text">
             <?php $step_3= get_field('step_3:');
    	    if(!empty($step_3)){
    	        echo '<p>'.$step_3.'</p>';
    	    }else{
    	    ?>
    	    <b>Step 3:</b>
            <p>Click on the "test my label" button. Then, proceed by clicking on the "add images" button to upload the file you downloaded from Canva.<span style = "color:red;"> Please refrain from altering the label's size within this tool.</span>
Then click on "Save and Render" </p>
            <?php } ?>
             <script src="https://cdn.pacdora.com/Pacdora-v1.0.1.js"></script>
               <?php $u_id = get_current_user_id(); 
                $model_id = get_field('model_id');
                 ?>
                <script>
                    
                      (
                
                        async () => {
                
                          await Pacdora.init({
                
                            userId: '<?php echo $u_id; ?>',
                            appId: '8c5f9c28d30f5dbd',
                            appKey: '3acf8da0a0a7dde8',
                            modelId: '<?php echo $model_id; ?>',
                            theme:'#339999',
                            doneBtn: 'Save and Render',
                            isDelay: false,
                            id: "",
                            design: 'save'
                
                          });
                          const btn = document.getElementById('designbtn');
                          btn.innerHTML = 'Design Online';
                
                        })()
                        
                </script>
        
               <script>
                Pacdora.$on( 'design: save',
                data =>
                {
                console.log('save event triggered')
                },'test'
                )
                
                </script>
                </br>
                <div class="btn dsg_t" data-pacdora-ui="design-btn" data-save-screenshot="true" data-screenshot-width="1000" >
                            test my label
                </div>
            

        </div>
        
        <div class="uploadBtn">
        
         <?php $step_4= get_field('step_4:');
	    if(!empty($step_4)){
	        echo '<p>'.$step_4.'</p>';
	    }else{
	    ?>
	    <b>Step 4: </b></br>
        <p>You can hover your cursor over the bottle to view a 3D preview of your products's appearance. If you APPROVE this label, kindly upload the file in PNG format (the one you got from CanvaI) by clicking on this button.</p>
        <?php } ?>
        <!-- <div style="display:block;width:120px; height:30px;" onclick="document.getElementById('getFile').click()">Upload PDF</div>
         <input type='file' name="uploadpdf" id="getFile" style="display:none">-->
         <?php $id11 = get_the_ID();
         $mergeimage= get_post_meta($id11,'mergeimage',true);
         if($mergeimage == "Basic")
         { $mergeimage = "background.png"; }
         else  if($mergeimage == "Powder")
         { $mergeimage = "powder-min.png"; }
         else  if($mergeimage == "Capsulles")
         { $mergeimage = "Capsulles-min.png"; }
         else  if($mergeimage == "Shadow")
         { $mergeimage = "capsule-original.png"; }
         else { $mergeimage = "Gummies1.png"; }
         ?>
         <!--<form  action="" enctype="multipart/form-data" method="post">
             <div style="display:block;width:120px; height:30px;" onclick="document.getElementById('getFile').click(); ">Upload PNG</div>
         <input type="hidden" name="static_path" id="static_path" value="<?php echo $mergeimage;?>" />
            <input type="file" name="file" id="getFile" style="display:none"><br/>
            <div id="loader" style="display: none;"> </div>

            <input id="imgupload" type="submit" value="Upload" name="Submit1" style="display:none"> <br/>
        </form>-->
        <!--<button data-pacdora-ui="design-btn" class="upload-btn" type="button" id="designbtn" style="height: 50px; width: 30%; color: #fff; background-color:#399; border: none; cursor: pointer;"> Loading ... </button>-->
        
        <!--<div data-pacdora-ui="design-btn" class="upload-btn" id="designbtn">Upload</div>-->
        
                <div class="ulpadbtn"  style="display:block;" onclick="document.getElementById('image_upload').click()">Upload PNG file</div>
            <input type='file' name="image_upload" id="image_upload" style="display:none" accept="image/png, image/jpeg">
            <!-- Add this to the HTML where you have your file upload form and another input field -->
            
            <script>
            document.getElementById('image_upload').addEventListener('change', function() {
                var formData = new FormData();
                formData.append('action', 'handle_image_upload');
                formData.append('image_upload', this.files[0]);
            
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Set the URL of the uploaded image to the other input field
                            document.getElementById('image_url').value = response.url;
                        } else {
                            // Handle error
                            alert('Error uploading image: ' + response.error);
                        }
                    } else {
                        // Handle error
                        alert('Request failed. Status: ' + xhr.status);
                    }
                };
                xhr.send(formData);
            });
            </script>

         </div>
        <!---<div class="step5">
            
             <?php /* $step_5= get_field('step_5:');
    	    if(!empty($step_5)){
    	        echo '<p>'.$step_5.'</p>';
    	    }else{
    	        echo '<b>Step 5: </b></br>';
    	    }
    	    */?>
           <div class="btn" id ="approve" >
                        Approve 
            </div>
            <div class="btn" onClick="window.location.reload();" >
                        Deny
            </div> 
            
        </div>-->
        <div class = "step6">
            <?php $step_5= get_field('step_5:');
    	    if(!empty($step_5)){
    	        echo $step_5;
    	    }else{
    	        echo '<b>Step 5: </b></br>';
    	        echo "<p>Please proceed with the purchase of the labels. Remember that the verification team needs to ensure that your label complies with FDA guidelines. We will be sending you updates via email</p>";
        
    	    }
    	    ?>
    	    <div class="btn cus_add_to_cart" id ="cus_add_to_cart" >
                        Add To Cart 
            </div>
             <?php
	        $user = wp_get_current_user();
            $user_info = get_userdata($user->ID);
            $email = $user_info->user_email;
            $use_flag = get_user_meta($user->ID, 'user_flag', True);
            if($use_flag == 0){ 
                echo '<p style="color:red;">Please upgade your plan for using this functionality.</p>';
            } 
            ?>
            
            
        </div>



         <style>
      .parent {
        position: relative;
        top: 0;
        left: 0;
      }
      .image1 {
        position: relative;
        top: 0;
        left: 0;
        border: 1px solid #000000;
      }
      .image2 {
	position: absolute;
	top: 220px;
	left: 166px;
	border: 1px solid #000000;
	width: 43%;
	height: 230px;
}
    </style>
<script>
        jQuery(document).ready(function($){ 
            jQuery(".cart-btn").on('click',function() {
                //location.reload(true);
            });
             jQuery(".cus_add_to_cart").on('click',function() {
            <?php
	        $user = wp_get_current_user();
            $user_info = get_userdata($user->ID);
            $email = $user_info->user_email;
            $use_flag = get_user_meta($user->ID, 'user_flag', True);
            if($use_flag != 0){ ?>
                $('.cart-btn').click();
            <?php } ?>
            });
        });
        </script>
            <?php

if(isset($_POST['Submit1'])){ 
$img2 = "https://vitalaunch.io/wp-content/themes/suxnix-child/images/".$fileName;
?>
    <div class="parent">
      <img class="image1" src="https://vitalaunch.io/wp-content/uploads/2024/02/MicrosoftTeams-image-1.png" />
      <img class="image2" src="<?php echo $img2; ?>" />
    </div>
<?php } 


?>

	 </div>
	 </div>
	 <div class="con_content_2">
	      <div class="cont">
	     <form method="post" >
	    <?php
	   if( have_rows('label_variation') ){

        // Loop through rows.
        while( have_rows('label_variation') ) { the_row();
            ?>
            <div class="inputDiv"><input type="radio" class="cus_labels" name="cus_labels" value="<?php echo the_sub_field('label_variation_price'); ?>">
            <label > <?php echo the_sub_field('label_variation_text'); ?></label><div class="tooltip"><span class="question">?</span><span class="tooltiptext"><?php echo the_sub_field('label_variation_hover_text'); ?></span></div></div>
            <?php
        }
	   }else{
	   ?>
	    <div class="inputDiv"><input type="radio" class="cus_labels" name="cus_labels" value="100">
        <label > Design 5 labels from Vitalaunch &nbsp;&nbsp;$100</label><div class="tooltip"><span class="question">?</span><span class="tooltiptext"><b>Design 5 labels from Vitalaunch</b><br><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent feugiat mi vitae odio fermentum, vel blandit est faucibus. Donec porttitor diam et est cursus lobortis.</span></div></div>
       <div class="inputDiv"> <input  type="radio" class="cus_labels" name="cus_labels" value="150">
        <label > Design 10 labels from Vitalaunch &nbsp;&nbsp;$150</label><div class="tooltip"><span class="question">?</span><span class="tooltiptext"><b>Design 10 labels from Vitalaunch</b><br><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent feugiat mi vitae odio fermentum, vel blandit est faucibus. Donec porttitor diam et est cursus lobortis.</span></div></div>
        <div class="inputDiv"><input  type="radio" class="cus_labels" name="cus_labels" value="200">
        <label > Design 15 labels from Vitalaunch &nbsp;&nbsp;$200</label><div class="tooltip"><span class="question">?</span><span class="tooltiptext"><b>Design 15 labels from Vitalaunch</b><br><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent feugiat mi vitae odio fermentum, vel blandit est faucibus. Donec porttitor diam et est cursus lobortis.</span></div></div>
        <?php } ?>
        </form>
        </div>
	 </div>

<?php
	 }
    // }
}

function prefix_add_discount_line( $cart ) {

  $discount = $_GET['label_value'];
if(!empty($discount)){
  $cart->add_fee( __( 'Product label ', 'yourtext-domain' ) , +$discount );
}
}
//add_action( 'woocommerce_cart_calculate_fees', 'prefix_add_discount_line' );

function mergeImages($staticImagePath, $imagePath2, $outputPath, $zoomFactor = 1.0, $marginTop = 0 , $marginLeft = 0) {
    $image1Info = getimagesize($staticImagePath);
    $image1Width = $image1Info[0];
    $image1Height = $image1Info[1];
 
    $image1 = @imagecreatefromstring(file_get_contents($staticImagePath));
 
    if (!$image1) {
        die('Error loading static image');
    }
 
    $image2Info = getimagesize($imagePath2);
    $image2Width = $image2Info[0];
    $image2Height = $image2Info[1];
 
    $image2 = @imagecreatefromstring(file_get_contents($imagePath2));
 
    if (!$image2) {
        die('Error loading uploaded image');
    }
 
    // Calculate center position with margin
    $positionX = ($image1Width - $image2Width * $zoomFactor) / 2 + $marginLeft;
    $positionY = ($image1Height - $image2Height * $zoomFactor) / 2 + $marginTop ;
 
    // Create a blank image with the size of the larger image
    $mergedImage = imagecreatetruecolor($image1Width, $image1Height);
 
    // Fill the merged image with a transparent background
    $transparent = imagecolorallocatealpha($mergedImage, 0, 0, 0, 127);
    imagefill($mergedImage, 0, 0, $transparent);
    imagesavealpha($mergedImage, true);
 
    // Copy the uploaded image onto the merged image with zoom, centered position, and margin
    imagecopyresampled($mergedImage, $image2, $positionX, $positionY, 0, 0, $image2Width * $zoomFactor, $image2Height * $zoomFactor, $image2Width, $image2Height);
 
    // Copy the static image onto the merged image (to act as a mask)
    imagecopyresampled($mergedImage, $image1, 0, 0, 0, 0, $image1Width, $image1Height, $image1Width, $image1Height);
 
    // Save the merged image
    imagejpeg($mergedImage, $outputPath);
 
    // Free up memory
    imagedestroy($image1);
    imagedestroy($image2);
    imagedestroy($mergedImage);
}

function mergeImagesWithThirdImage($staticImagePath, $imagePath2, $outputPath, $thirdImagePath, $zoomFactor = 1.0, $marginTop = 0, $marginLeft = 0) {
    $image1Info = getimagesize($staticImagePath);
    $image1Width = $image1Info[0];
    $image1Height = $image1Info[1];

    $image1 = @imagecreatefromstring(file_get_contents($staticImagePath));

    if (!$image1) {
        die('Error loading static image');
    }

    $image2Info = getimagesize($imagePath2);
    $image2Width = $image2Info[0];
    $image2Height = $image2Info[1];

    $image2 = @imagecreatefromstring(file_get_contents($imagePath2));

    if (!$image2) {
        die('Error loading uploaded image');
    }

    $thirdImageInfo = getimagesize($thirdImagePath);
    $thirdImageWidth = $thirdImageInfo[0];
    $thirdImageHeight = $thirdImageInfo[1];

    $thirdImage = @imagecreatefromstring(file_get_contents($thirdImagePath));

    if (!$thirdImage) {
        die('Error loading third image');
    }

    // Calculate center position with margin
    $positionX = ($image1Width - $image2Width * $zoomFactor) / 2 + $marginLeft;
    $positionY = ($image1Height - $image2Height * $zoomFactor) / 2 + $marginTop;

    // Create a blank image with the size of the larger image
    $mergedImage = imagecreatetruecolor($image1Width, $image1Height);

    // Copy the static image onto the merged image
    imagecopy($mergedImage, $image1, 0, 0, 0, 0, $image1Width, $image1Height);

    // Copy the uploaded image onto the merged image with zoom, centered position, and margin
    imagecopyresampled($mergedImage, $image2, $positionX, $positionY, 0, 0, $image2Width * $zoomFactor, $image2Height * $zoomFactor, $image2Width, $image2Height);

    // Add the third image at the top
    imagecopyresampled($mergedImage, $thirdImage, 0, 0, 0, 0, $image1Width, $image1Height, $thirdImageWidth, $thirdImageHeight);

    // Save the merged image
    imagejpeg($mergedImage, $outputPath);

    // Free up memory
    imagedestroy($image1);
    imagedestroy($image2);
    imagedestroy($thirdImage);
    imagedestroy($mergedImage);
}


function custom_new_product_image( $_product_img, $cart_item, $cart_item_key ) {
    global $wpdb;
   $product_id = $cart_item['product_id'];
    $quantity = $cart_item['quantity'];
     
    
    if($quantity >= 50){
        $user_id = get_current_user_id();
        $post_ids = $wpdb->get_results("SELECT * FROM `marge_img` WHERE `user_id` = '".$user_id."' AND `product_id` = '".$product_id."' ORDER BY ID DESC LIMIT 1 ");
       
        foreach($post_ids as $post_id){
            ?><img src='<?php echo $post_id->image_url; ?>' /><?php
        }
        
    }
    else {
        $featured_image_id = get_post_thumbnail_id($product_id);
    $featured_image_url = wp_get_attachment_url($featured_image_id);

        ?><img src="<?php echo $featured_image_url; ?>"/>   
        <?php
    }
    
   
    
    return ;
}

add_filter( 'woocommerce_cart_item_thumbnail', 'custom_new_product_image', 10, 3 );

 
 /*
 function add_custom_submenu_page() {
    add_submenu_page(
        'woocommerce',
        'Custom Orders Page',
        'Custom Orders Page',
        'manage_woocommerce',
        'custom-orders-page',
        'custom_orders_page_callback'
    );
}
add_action('admin_menu', 'add_custom_submenu_page');

function custom_orders_page_callback() {
    // Your custom content for the orders page goes here
    echo '<div class="wrap"><h2>Custom Orders Page</h2><p>This is your custom content.</p></div>';
}
// Example: Add custom content before the orders table
function custom_content_before_orders_table() {
    echo '<p>This is custom content before the orders table.</p>';
}
add_action('woocommerce_before_account_orders', 'custom_content_before_orders_table'); 




// Add custom order details after billing address
function custom_order_details_billing($order) {
    echo '<div class="custom-order-details">';
    echo '<h4>Custom Billing Details</h4>';
    
    // Your custom content for billing details goes here
    
    echo '</div>';
}
add_action('woocommerce_admin_order_data_after_billing_address', 'custom_order_details_billing', 10, 1); 

// Add custom order details after shipping address
function custom_order_details_shipping($order) {
    echo '<div class="custom-order-details">';
    echo '<h4>Product Type</h4>';
    echo '<select><option>Sample</option><option>Label</option></select>';
    
    // Your custom content for shipping details goes here
    
    echo '</div>';
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'custom_order_details_shipping', 10, 1);
*/

    function cloudways_display_order_data_in_admin( $order ){  $order_id = $order->id;  
    $order = wc_get_order( $order_id );

    $optional_fee_exists = false;
    //->get_fees() 
       $fee_counter = 0;
       
    $key_1_value = get_post_meta( $order->id, 'marge_img', true );
    $_label = get_post_meta( $order->id, '_label', true );
    // Check if the custom field has a value.
    if (empty( $_label ) ) { ?>
    
    	<div class="order_data_column">
                    <h4><?php _e( ' Label Image', 'woocommerce' ); echo $order_meta_value;?></h4>
                    <div class="address">
                    <?php
                        global $wpdb;
                        $order_id = $order->id;
                        //$order_id = $_REQUEST['id']; 
                        $img_url = get_post_meta($order_id, 'marge_img', TRUE);
                        $uploadimg = get_post_meta($order_id, 'uploadimg', TRUE);
                        $order = wc_get_order( $order_id );
                        $items = $order->get_items();
                        $user = $order->get_user();
                        $user_id = $order->get_user_id();
                        foreach ( $items as $item ) {
                            $product_id = $item->get_product_id();
                            $product = wc_get_product($item->get_product_id());
                            $item_sku = $product->get_sku();
                        }
                        $user_ID = get_current_user_id();
                         $post_ids = $wpdb->get_results("SELECT * FROM `marge_img` WHERE  `user_id` = '".$user_id."' AND `sku` = '".$item_sku."' ORDER BY ID DESC LIMIT 1 ");
                         
                         $post_ids_sku = $post_ids[0]->pdf;
                    ?>
                     <?php if(!empty($img_url)){ ?>
                    <img src = "<?php
                    echo $img_url; ?> " style="width:200px;"/>
                    <a href="<?php
                    echo $img_url; ?> " style="width:200px;" target="_blank">Preview</a>
                    <?php } ?>
                    <?php /* if(!empty($post_ids_sku)){ ?>
                    <button id="downloadImage"> Download Image </button>
                    <?php } */ ?>
                   
                   
                    <?php if(!empty($uploadimg)){ ?>
                    <button id="downloaduploadimg"> Download Label</button>
                    <?php } ?>
                    <style>
                        .address a {
                        	display: block;
                        	margin-bottom: 20px;
                        	margin-top: 10px;
                        }
                        .order_data_column {
                        	width: 40% !important;
                        }
                    </style>
                    <script type="text/javascript">
                       
                        const btn1 = document.getElementById('downloaduploadimg');
                        const url1 = "<?php echo $uploadimg; ?>";
                         
                        btn1.addEventListener('click', (event) => {
                           
                          event.preventDefault();
                          console.log('ABC')
                          downloadImage(url1);
                        })
                        
                        function downloadImage(url) {
                          fetch(url, {
                            mode : 'no-cors',
                          })
                            .then(response => response.blob())
                            .then(blob => {
                            let blobUrl = window.URL.createObjectURL(blob);
                            let a = document.createElement('a');
                            a.download = url.replace(/^.*[\\\/]/, '');
                            a.href = blobUrl;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                          })
                        }
                        
                        
                        
                        
                        /*function downloadImage(url) {
                          fetch(url, {
                            mode : 'no-cors',
                          })
                            .then(response => response.blob())
                            .then(blob => {
                            let blobUrl = window.URL.createObjectURL(blob);
                            let a = document.createElement('a');
                            a.download = url.replace(/^.*[\\\/]/, '');
                            a.href = blobUrl;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                          })
                        }*/
                    </script>
                    </div>
                </div>
    <?php }  
     //echo '<pre>';
      //      print_r($order);
      //      die('hello');
       /*foreach ( $order as $item_fee ) {
           
           $fee_counter++;
    
           echo $fee_name = $item_fee->get_name();
           
           //echo $fee_= $item_fee->get_total();
    
           if ( !empty($fee_name) ) {
    
               ?>
               <div class="order_data_column">
                    <h4><?php _e( ' Label Image', 'woocommerce' ); echo $order_meta_value;?></h4>
                    <div class="address">
                    <?php
                        $order_id = $order->id;
                        //$order_id = $_REQUEST['id']; 
                        $img_url = get_post_meta($order_id, 'marge_img', TRUE);
                    ?>
                    <img src = "
                    https://vitalaunch.io/wp-content/uploads/<?php
                    echo $img_url; ?> " style="width:200px;"/>
                    <a href="
                    https://vitalaunch.io/wp-content/uploads/<?php
                    echo $img_url; ?> " style="width:200px;" target="_blank">Preview</a>
                    </div>
                </div>
               <?php
    
           }else{
               echo 'hello';
           }
       }*/
       
    ?>

<?php  }
    add_action( 'woocommerce_admin_order_data_after_order_details', 'cloudways_display_order_data_in_admin' );
    /*function global_notice_meta_box() {

    add_meta_box(
        'label_upload',
        __( 'Label Upload', 'sitepoint' ),
        'global_notice_meta_box_callback'
    );

}
function global_notice_meta_box_callback() {
    // global $post; // OPTIONALLY USE TO ACCESS ORDER POST
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js" ></script>
    <script>
        $(document).ready(function () {
        $('.repeater').repeater({
            // (Optional)
            // start with an empty list of repeaters. Set your first (and only)
            // "data-repeater-item" with style="display:none;" and pass the
            // following configuration flag
            initEmpty: false,
            // (Optional)
            // "defaultValues" sets the values of added items.  The keys of
            // defaultValues refer to the value of the input's name attribute.
            // If a default value is not specified for an input, then it will
            // have its value cleared.
            defaultValues: {
                'text-input': 'foo'
            },
            // (Optional)
            // "show" is called just after an item is added.  The item is hidden
            // at this point.  If a show callback is not given the item will
            // have $(this).show() called on it.
            show: function () {
                $(this).slideDown();
            },
            // (Optional)
            // "hide" is called when a user clicks on a data-repeater-delete
            // element.  The item is still visible.  "hide" is passed a function
            // as its first argument which will properly remove the item.
            // "hide" allows for a confirmation step, to send a delete request
            // to the server, etc.  If a hide callback is not given the item
            // will be deleted.
            hide: function (deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            },
            // (Optional)
            // You can use this if you need to manually re-index the list
            // for example if you are using a drag and drop library to reorder
            // list items.
            ready: function (setIndexes) {
            },
            // (Optional)
            // Removes the delete button from the first list item,
            // defaults to false.
            isFirstItemUndeletable: true
        })
    });
    </script>
    <form class="">

	<div class='repeater'>
		<div data-repeater-list="group-a">
			<div data-repeater-item>
			    <h4>Label Image</h4>
				<input type="file" name="label_upload_img_1"  />
				<input data-repeater-delete type="button" value="Delete" />
			</div>
			
		</div>
		<input data-repeater-create type="button" value="Add Image" />
	</div>
</form>
    <?php
}

add_action( 'add_meta_boxes', 'global_notice_meta_box' );

*/

/*add_action('woocommerce_add_to_cart', 'custome_add_to_cart');
$cnt=2;
function custome_add_to_cart() {
    
    global $woocommerce;
      echo $_SESSION["la"];
      die('hi');
    WC()->cart->add_to_cart( $product_id, '1', '0', array(), $custom_data );
    

}*/
 


/** add handling fee **/
function df_add_handling_fee( $cart_object ) {

global $woocommerce;

// $specialfeecat = 3711; // category id for the special fee
$spfee = 0.00; // initialize special fee
$discount = $_GET['label_value'];
$spfeeperprod = $_GET['label_value']; //special fee per product

//Getting Cart Contents. 
$cart = $woocommerce->cart->get_cart();
//Calculating Quantity
foreach($cart as $cart_val => $cid){
   $qty += $cid['quantity']; 
}
foreach ( $cart_object->cart_contents as $key => $value ) {

    $proid = $value['product_id']; //get the product id from cart
    //$quantiy = $value['quantity']; //get quantity from cart
    $itmprice = $value['data']->price; //get product price
    $label_price = $value['label_price'];

    $terms = get_the_terms( $proid, 'product_cat' ); //get taxonamy of the prducts
    if ( $terms && ! is_wp_error( $terms ) ) :
        foreach ( $terms as $term ) {
            //$catid = $term->term_id;
            //if($specialfeecat == $catid ) {
                $spfee =  $label_price;
            //}
        }
    endif;  
}
if(!empty($spfee )) {
if($spfee > 0 ) {

    $woocommerce->cart->add_fee( 'Custom Label Fee', $spfee, true, 'standard' );
}
}
}

add_action( 'woocommerce_cart_calculate_fees', 'df_add_handling_fee' );

add_action( 'wp_ajax_nopriv_get_data1', 'get_data1' );
add_action( 'wp_ajax_get_data1', 'get_data1' );
function get_data1() {
$_SESSION['new_data'] = $_POST['text'];

 $_SESSION['label_val'] = $_POST['label_val'];
echo $_SESSION['new_data'] ;
 
 $product_id = $_POST['new11'];
 
//echo $product_id = 4746; 

$quantity =  $_POST['qty']; 
$variation_id = 0; 
$cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id); 
if ($cart_item_key) {  
    $cart_url = wc_get_cart_url(); 
    $redirect_url = add_query_arg(array('param' => 'value'), $cart_url); 
    wp_safe_redirect($redirect_url);
   // exit;
} else { 
    echo "Product couldn't be added to the cart.";
}

}

add_filter( 'woocommerce_my_account_my_orders_columns', 'additional_my_account_orders_column', 10, 1 );
function additional_my_account_orders_column( $columns ) {
    $new_columns = [];

    foreach ( $columns as $key => $name ) {
        $new_columns[ $key ] = $name;

        if ( 'order-status' === $key ) {
            $new_columns['order-items'] = __( 'Order Type', 'woocommerce' );
        }
    }
    return $new_columns;
}

add_action( 'woocommerce_my_account_my_orders_column_order-items', 'additional_my_account_orders_column_content', 10, 1 );
function additional_my_account_orders_column_content( $order ) {
    $details = array();
    global $wpdb;
    $order_id = $order->get_id();
    /*$product_order_item_id = $wpdb->get_results("SELECT  order_item_id FROM wp_woocommerce_order_items  WHERE  `order_id` = '".$order_id."'");
        $product_order_item_id =  $product_order_item_id[0]->order_item_id;
        $product_order_item_id_meta = $wpdb->get_results("SELECT *  FROM `wp_woocommerce_order_itemmeta` WHERE `order_item_id` = '".$product_order_item_id."' AND `meta_key` LIKE 'Product Type' ");
        echo  $m_value = $product_order_item_id_meta[0]->meta_value;*/
    foreach( $order->get_items() as $item )
        //echo $order_id = $item->get_id();
        $product_order_item_id = $wpdb->get_results("SELECT  order_item_id FROM wp_woocommerce_order_items  WHERE  `order_id` = '".$order_id."'");
        $product_order_item_id =  $product_order_item_id[0]->order_item_id;
        $product_order_item_id_meta = $wpdb->get_results("SELECT *  FROM `wp_woocommerce_order_itemmeta` WHERE `order_item_id` = '".$product_order_item_id."' AND `meta_key` LIKE 'Product Type' ");
        $m_value = $product_order_item_id_meta[0]->meta_value;
        $details[] = $m_value;

    echo count( $details ) > 0 ? implode( '<br>', $details ) : '&ndash;';
}














 



function global_notice_meta_box() {
    $order_id = $_REQUEST['id'];  
$order = wc_get_order($order_id); 
if ($order) { 
    $billing_first_name = $order->get_billing_first_name();
    $billing_last_name = $order->get_billing_last_name();
 $order_meta_value = get_post_meta($order_id, '_label', true); 
     
    $customer_name = $billing_first_name ." Order " .$order_meta_value . " Labels" ;

     
} else {
    //echo 'Order not found.';
}


    add_meta_box(
        'label_upload',
        __( $customer_name, 'sitepoint' ),
        'global_notice_meta_box_callback'
    );

}
  
function global_notice_meta_box_callback() {
    // global $post; // OPTIONALLY USE TO ACCESS ORDER POST
    ?>
 
<form id="image-upload-form" method="post" action="#" enctype="multipart/form-data">
	<div class='repeater'>
	    <table class='order_label_img'>
          <tr>
            <th>Label Image</th>
            <th>Label Status</th>
            <th>Label Image Comment</th>
          </tr>
 <?php 
  $order_id = $_REQUEST['id']; 
    $order_meta_value = get_post_meta($order_id, '_label', true); 
     
    for($i=0; $i<$order_meta_value; $i++ ){
        $name = "label_upload_img_" . $i;
        $upload_name = "selected_image_id" .$i;
         $upload_imgname =  $i;
         
         $ret_image1 = "_label_upload_images" .$i;
         $ret_image = get_post_meta($order_id, $ret_image1, true);
         
         $image_html = wp_get_attachment_image($ret_image ); 
         $flag1 = "_flag" .$i;
          $flagnote ="_flag" .$i."_note";
          $flag = get_post_meta($order_id, $flag1, true);
          $flag_note = get_post_meta($order_id, $flagnote, true);
         if($flag == "2")
         {
             $imgstatus =" Vendor Approved the label";
         }
         else if($flag == "3")
         {
             $imgstatus =" Vendor Declined the Label";
         }
         else 
         { $imgstatus ="";} 
         
        
         
    ?>
 <div data-repeater-item>
<tr>
    <td>
    <h4>Label Image  <?php echo $i+1; ?> </h4>

  <p> <?php echo $image_html; ?></p>
    <input type="hidden" name="<?php echo $upload_name; ?>" id="<?php echo $upload_name; ?>" value="<?php echo $ret_image; ?>" />
    <input type="hidden" name="<?php echo $flag1; ?>" id="<?php echo $flag; ?>" value="<?php echo $flag; ?>" />
<input type="button" value="Change Image" name="upload_img" id="<?php echo $upload_imgname; ?>" class="upload_img_button" /> 
 
<input type="button" value="Delete" name="delete_img" class="delete-img-button" data-attachment-id="<?php echo $ret_image; ?>" data-label = "<?php echo $ret_image1; ?>" data-attach_details = "<?php echo $upload_name; ?>" data-order_id = <?php echo $_REQUEST['id']; ?>  />
</td>
<td>
<p>   <?php echo $imgstatus; ?> </p>
</td>
<td>
    <?php
    if(!empty($flag_note)){ ?>
    
    <p><?php echo $flag_note; ?></p>
    <?php } ?>
</td>
</tr>
</div>
<?php } ?>
</table>
</div>
 
</div>
<?php wp_nonce_field('label_upload_nonce', 'label_upload_nonce'); ?>
</form>
<script>
    jQuery(document).ready(function($){ 
        $('.upload_img_button').on('click', function(e){
            var elementId = $(this).attr('id');
            
      
            e.preventDefault();

            // Open the WordPress Media Library
            var imageUploader = wp.media({
                title: 'Choose or Upload an Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false // Set this to true if you want to allow multiple image selection
            });

            // Handle the selection of the image
            imageUploader.on('select', function(){
                var attachment = imageUploader.state().get('selection').first().toJSON();

                // Display the selected image details (you can modify this part according to your needs)
                console.log('Attachment ID: ' + attachment.id);
                console.log('Image URL: ' + attachment.url);
                console.log('Image Alt Text: ' + attachment.alt);
                var tempp = "#selected_image_id"+elementId;
                var tempp1 = "_flag"+elementId;
                
                $(tempp).val(attachment.id);
                 $('input[name='+tempp1+']').val(1);
                 $('input[name='+tempp1+']').removeAttr('id');
                 $('input[name='+tempp1+']').attr('id','1');
                 $('.wide .save_order').click();
 
            });

            // Open the Media Library dialog
            imageUploader.open();
            
        });
        
         $('.delete-img-button').on('click', function() {
             
            var attachmentId = $(this).data('attachment-id'); 
            var attach_label = $(this).data('label'); 
            var attach_details = $(this).data('attach_details'); 
            var order_id = $(this).data('order_id'); 
            
            
            alert(attach_details);
            if (confirm('Are you sure you want to delete this image?')) { 
                $.ajax({
                    type: 'POST',
                    url: ajaxurl, // WordPress AJAX endpoint
                    data: {
                        action: 'delete_attachment', // Action hook for the server-side function
                        attachment_id: attachmentId,
                        attach_label: attach_label,
                        order_id: order_id
                    },
                    success: function(response) {
                        console.log(response);  
                        attach_details = "#"+attach_details;
                        $(attach_details).val("");
                    },
                    error: function(error) {
                        console.error('AJAX error:', error); // Log any AJAX errors
                    }
                });
            }
        });
    });
</script>
<?php
}
 
add_action( 'add_meta_boxes', 'global_notice_meta_box' );



add_action('woocommerce_process_shop_order_meta', 'save_custom_meta_box_value');

function save_custom_meta_box_value($order_id) { 
    $label_total = get_post_meta($order_id, '_label', true);
    for($i=0;$i<$label_total;$i++)
    {
    $selected_image_info = "selected_image_id" .$i; 
    $info = $_POST[$selected_image_info]; 
   $label = "_label_upload_images" .$i;
  
   
     update_post_meta($order_id, $label, $info);
      $flag = "_flag".$i;
   
   $flagde = "_flag" .$i;
   $flaginfo = $_POST[$flagde];
     
     if( empty($flaginfo)  ){
         $default = 0;
     }
     else if( $flaginfo == 1)
     {
          $default = 1;
     }
       else if( $flaginfo == 2)
     {
          $default = 2;
     }
      else if( $flaginfo == 3)
     {
          $default = 3;
     }
     
     else { $default = 0;}
     
     
     update_post_meta($order_id, $flag, $default);
    }
    
      
} 
add_image_size('custom_small', 150, 150, true);





add_action( 'wp_ajax_nopriv_delete_attachment', 'delete_attachment' );
add_action( 'wp_ajax_delete_attachment', 'delete_attachment' );
 
function delete_attachment() {
 
 $attachment_id = $_POST['attachment_id'];
 $attach_label = $_POST['attach_label'];
 $order_id = $_POST['order_id'];
 update_post_meta($order_id, $attach_label, $attachment_id); 
 
}





function add_custom_meta_after_order_placed($order_id) { 
    $order = wc_get_order($order_id); 
    $label_val = $_SESSION['label_val'];
    if ($order) {
         $customer_id = $order->get_customer_id(); 
        add_post_meta($order_id, '_label', $label_val , true);

        
    }
}

// Output the Custom field in Product pages
add_action("woocommerce_before_add_to_cart_button", "options_on_single_product", 1);
function options_on_single_product(){
    ?>
        <label for="label_price">
            <input type="hidden" name="label_price" class="label_price" >  
             <input type="hidden" name="label_imgs" class="label_imgs" > 
             <input type="hidden" name="image_url" id="image_url" >
        </label> <br />
        
    <?php
}

// Stores the custom field value in Cart object
add_filter( 'woocommerce_add_cart_item_data', 'save_custom_product_field_data', 10, 2 );
function save_custom_product_field_data( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['label_price'] ) ) {
        $cart_item_data[ 'label_price' ] = esc_attr($_REQUEST['label_price']);
        // below statement make sure every add to cart action as unique line item
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    if( isset( $_REQUEST['label_imgs'] ) ) {
        $cart_item_data[ 'label_imgs' ] = esc_attr($_REQUEST['label_imgs']);
        // below statement make sure every add to cart action as unique line item
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    if( isset( $_REQUEST['image_url'] ) ) {
        $cart_item_data[ 'image_url' ] = esc_attr($_REQUEST['image_url']); 
        // below statement make sure every add to cart action as unique line item
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    
    return $cart_item_data;
}

// Outuput custom Item value in Cart and Checkout pages
add_filter( 'woocommerce_get_item_data', 'output_custom_product_field_data', 10, 2 );
function output_custom_product_field_data( $cart_data, $cart_item ) {
    
    if( isset( $cart_item['label_price'] ) ) {
        $cart_data[] = array(
            'key'       => __('Custom Label Fee', 'woocommerce'),
            'value'     => $cart_item['label_price'],
            'display'   => $cart_item['label_price'],
        );
        $label_price = $cart_item['label_price'];
        if($label_price == 100)
        {
            $label_count=5;
            
        }
        else if($label_price == 150)
        {
            $label_count=10;
        }else  if($label_price == 200)
        {
            $label_count=15;
        }
        else { $label_count=""; }
        $_SESSION['label_val'] = $label_count;
    }
     /*if( isset( $cart_item['label_imgs'] ) ) {
        $cart_data[] = array(
            'key'       => __('Custom Label imgs', 'woocommerce'),
            'value'     => $cart_item['label_imgs'],
            'display'   => $cart_item['label_imgs'],
        );
        $label_imgs = $cart_item['label_imgs'];
       */
    
    return $cart_data;
    
}
// Display custom cart item data in cart (optional)
add_filter( 'woocommerce_get_item_data', 'display_custom_item_data1', 10, 2 );
function display_custom_item_data1( $cart_item_data, $cart_item ) {
    if ( isset( $cart_item['file_upload']['title'] ) ){
        $cart_item_data[] = array(
            'name' => __( 'Image uploaded', 'woocommerce' ),
            'value' =>  str_pad($cart_item['file_upload']['title'], 16, 'X', STR_PAD_LEFT) . '…',
        );
    }
    return $cart_item_data;
}

// Save Image data as order item meta data
add_action( 'woocommerce_checkout_create_order_line_item', 'custom_field_update_order_item_meta1', 20, 4 );
function custom_field_update_order_item_meta1( $item, $cart_item_key, $values, $order ) {
    if ( isset( $values['file_upload'] ) ){
        $item->update_meta_data( 'uploadimg',  $values['file_upload'] );
    }
}



function add_custom_text_to_order_details_page($order_id) {
    // Output your custom text here
    ?>
    <style>
        .woocommerce-notice.woocommerce-notice--success.woocommerce-thankyou-order-received {
        	font-size: 40px;
        	font-weight: bold;
        	color: #000;
        }
        .startbutton {
        	font-size: 16px;
        	display: block;
        }
        .woocommerce-notice.woocommerce-notice--success.woocommerce-thankyou-order-received {
        	text-align: center;
        	margin-top: 90px;
        }
    </style>
    <?php
    echo '<div id="form_cus" data-tf-live="01HPQ2MB7MK2K9KNVH96YJTCTZ"></div><script src="//embed.typeform.com/next/embed.js"></script>';
}

add_action('woocommerce_thankyou', 'add_custom_text_to_order_details_page', 10, 1);


add_filter('woocommerce_thankyou_order_received_text', 'woo_change_order_received_text', 10, 2 );
function woo_change_order_received_text( $str, $order ) {
    // Get order total
    $order_total = $order->get_total();
    $percent = get_option( 'wc-custom-percent' ); // Percentage
    $order_saving = (float)($percent * $order_total / 100); // Bonus amount
   global $wp;
$percent_url = home_url( $wp->request );
$key = $_GET['key'];
    $new_str = $str . '  <a class="startbutton" href='. $percent_url.'/?key='.$key.'#form_cus><button>Please respond to the following form so we can design your label </button></a>';
    return $new_str;
}






add_action( 'wp_ajax_nopriv_get_data121', 'get_data121' );
add_action( 'wp_ajax_get_data121', 'get_data121' );
function get_data121() {
    $flag = $_POST['text'];
    $order_id = $_POST['order_id'];
    update_post_meta($order_id, $flag, '2');
    
}


add_action( 'wp_ajax_nopriv_get_decline', 'get_decline' );
add_action( 'wp_ajax_get_decline', 'get_decline' );
function get_decline() {
    $flag = $_POST['text'];
    
    $order_id = $_POST['order_id'];
     $flag_note = $_POST['flag_note'];
  $flag_noteeee = $flag.'_note';
    update_post_meta($order_id, $flag, '3');
    
}




add_filter ('woocommerce_add_to_cart_redirect', function( $url, $adding_to_cart ) {

    return wc_get_checkout_url();

}, 10, 2 ); 
// Function to handle image upload
function handle_image_upload() {
    if ( isset( $_FILES['image_upload'] ) ) {
        $uploaded_file = $_FILES['image_upload'];
        $upload_overrides = array( 'test_form' => false );

        // Upload the file securely
        $movefile = wp_handle_upload( $uploaded_file, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            // File successfully uploaded, return the URL
            echo json_encode( array(
                'success' => true,
                'url' => $movefile['url'] // URL of the uploaded image
            ) );
        } else {
            // Handle error
            echo json_encode( array(
                'success' => false,
                'error' => isset( $movefile['error'] ) ? $movefile['error'] : 'Unknown error'
            ) );
        }
    }
    // Ensure no further output is sent
    die();
}

// Hook the function to handle image upload
add_action( 'wp_ajax_handle_image_upload', 'handle_image_upload' );
add_action( 'wp_ajax_nopriv_handle_image_upload', 'handle_image_upload' );

add_action('woocommerce_after_order_notes', 'custom_checkout_field1');

function custom_checkout_field1($checkout)

{

echo '<div id="custom_checkout_field"><h2>' . __('') . '</h2>';

woocommerce_form_field('uploadimg', array(

'type' => 'hidden',

'class' => array(

'my-field-class form-row-wide'

) ,

'placeholder' => __('uploadimg') ,
//'value' => 'test',
) ,

$checkout->get_value('uploadimg'));

echo '</div>';

}
add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta');

function custom_checkout_field_update_order_meta($order_id)

{

if (!empty($_POST['uploadimg'])) {

update_post_meta($order_id, 'uploadimg',sanitize_text_field($_POST['uploadimg']));

}

}

add_action('woocommerce_checkout_order_processed', 'enroll_student', 10, 1);

function enroll_student($order_id)
{
    $product_id = array();
  $order = wc_get_order( $order_id ); 

  foreach ($order->get_items() as $item) {
    $product_id[] = $item->get_product_id();
    
  }
    if($product_id[0] == '1699' || $product_id[0] == '1700'){
        $user = wp_get_current_user();
        $user_info = get_userdata($user->ID);
        $email = $user_info->user_email;
        update_user_meta($user->ID, 'user_flag', 1);
    }
 }
 
 