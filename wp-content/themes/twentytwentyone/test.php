<?php /* Template Name: Test */
get_header(); 
$user_id = get_current_user_id();
 $args = array(
    'customer_id' => $user_id ,
    'limit' => -1, // to retrieve _all_ orders by this user
);
$orders = wc_get_orders($args);
    foreach ($orders as $customer_order) {
        $orderq = wc_get_order($customer_order);
        $order_id  = $orderq->get_id();
        
        $order = wc_get_order( $order_id );
        $items = $order->get_items();
        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            $product = new WC_Product($item['product_id']);
            $sku = $product->get_sku();
            $product_order_item_id = $wpdb->get_results("SELECT  order_item_id FROM wp_woocommerce_order_items  WHERE  `order_id` = '".$order_id."'");
            $product_order_item_id =  $product_order_item_id[0]->order_item_id;
            $product_order_item_id_meta = $wpdb->get_results("SELECT *  FROM `wp_woocommerce_order_itemmeta` WHERE `order_item_id` = '".$product_order_item_id."' AND `meta_key` LIKE 'Product Type' ");
            $order_type = $product_order_item_id_meta[0]->meta_value;
             get_post_meta($order_id,'marge_img', true);
            
        }
    }


echo $id  = attachment_url_to_postid( "https://vitalaunch.io/wp-content/uploads/merged_image-piyush123345689.png" );



///$prefixcout =  get_user_meta( $user_id, 'primary_blog', true );
        
        $blogs = $wpdb->get_results("SELECT * FROM   wp_blogs   WHERE domain LIKE 'site24.vitalaunch.io'");
        echo $blog_id =  $blogs[0]->blog_id;
        
        $get_users = get_users( array( 'blog_id' => $blog_id ) );
echo $user_id = $get_users[0]->ID;
/* global $current_user;
wp_get_current_user();
 $current_user =$current_user->user_login ;
require_once 'stripe/vendor/autoload.php';
 
$stripe = new \Stripe\StripeClient('sk_test_51MAqEySDzHJSF4TRKj90JrElt4c6novaqZ4a9BIPnZtLpxrxYp0b5rf7ZTyASgjGkC2dvFboyTjfkXLJ23RBhNYf00v1u6cfKG');
$stripe->paymentIntents->create([
  'amount' => 200,
  'currency' => 'usd',
   'customer'=> $cus_id,
//   'payment_method' => 'pi_3Ok1rUSDzHJSF4TR06s7aIDd',
   'description' => $current_user,
  'automatic_payment_methods' => ['enabled' => true],
]);*/
    $order = new WC_Order( 1949 );
   print_r($order);
 get_footer();
?>