<?php

if ( !defined( 'WP_DEBUG' ) ) {
	die( 'Direct access forbidden.' );
}

add_action( 'wp_enqueue_scripts', 'suxnix_child_enqueue_styles', 99 );

function suxnix_child_enqueue_styles() {
   wp_enqueue_style( 'parent-style', get_stylesheet_directory_uri() . '/style.css' );
}


/**
 * @snippet       Hide Products From Specific Category @ Shop
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.6.3
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  



/**
function custom_field_in_cart() { 
    global $woocommerce; 
    echo '<form action="https://developerlab.live/checkout/" method="POST" id="orderqty">';
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
add_action('woocommerce_after_cart', 'custom_cart_page_text');
 
function custom_cart_page_text() {
    ?>
    <style>
        .product_quty label {
	font-size: 20px;
	font-weight: 700;
	padding-left: 20px;
	color: black;
}
    </style>
    <?php
    echo ' <div class="product_quty"><input type="radio" value="simple" name="cus_radio" class="new_pro" /><label>Sample Order</label><br><input type="radio" value="bulk" name="cus_radio" class="new_pro" /><label>Manual Order</label><br /> </div>';
   
}
 
 
if (!session_id()) {
    session_start();
}
 
 
 
 
 


add_action( 'wp_ajax_nopriv_get_data', 'get_data' );
add_action( 'wp_ajax_get_data', 'get_data' );
 
function get_data() {
 
$prod_det = $_POST['text'];
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
