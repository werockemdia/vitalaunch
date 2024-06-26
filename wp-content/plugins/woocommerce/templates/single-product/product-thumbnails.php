<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.1
 */

defined( 'ABSPATH' ) || exit;
 $user = wp_get_current_user();
    $user_info = get_userdata($user->ID);
    $email = $user_info->user_email;
     
     
    global $wpdb;    
    $result = $wpdb->get_results( "SELECT domain FROM wp_blogs INNER JOIN wp_registration_log ON wp_blogs.blog_id = wp_registration_log.blog_id WHERE email='".$email."'");
    //print_r($result);
    $store_own = $result[0]->domain;
    $qty = $_GET['qty'];
    if($qty == 2 ){ 
        if($store_own == ''){ 
            ?>
            <script>
    $(document).ready(function(){
    $(".product-quantity-button").hide();
    });
    </script>
            <?php
         // Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
            if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
            	return;
            }
            
            global $product;
            
            $attachment_ids = $product->get_gallery_image_ids();
            
            if ( $attachment_ids && $product->get_image_id() ) {
            	foreach ( $attachment_ids as $attachment_id ) {
            		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
            	}
            } 
            
        } if($store_own != ''){
    ?>
     <script src="https://cdn.pacdora.com/Pacdora-v1.0.1.js"></script>
    <script>
    $(document).ready(function(){
    $(".woocommerce-product-gallery__image").hide();
    });
    </script>
    
    <div data-pacdora-ui="3d" id = "oldimg" style="width:600px; height:600px;"></div>
    <div id="downloadImage3d_url" data-pacdora-ui="3d-preview" style="width:200px; height:200px; display:none;"></div>
    <button id="downloadImage3d" onclick="downloadImage3d_url()"> <i class="fa fa-download" aria-hidden="true"></i>
 </button>
 
    <style>
       #downloadImage3d {
    	float: right;
        color: #000;
    	position: relative;
    	margin-bottom: 200px;
    	margin-top: -40px;
    	margin-right: 50px;
    	background-color: transparent;
    	border: none;
        }
    </style>
    <script>
    jQuery(document).ready(function(){
        $("#downloadImage3d").hide();
    
    jQuery('.dsg_t').click(function(){
            $("#downloadImage3d").show();
    });
    });
    </script>
    <script type="text/javascript">
    document.getElementById('downloadImage3d');
    function downloadImage3d_url() {
        const url = document.getElementById('downloadImage3d_url').firstChild.getAttribute("src");
        const btn = document.getElementById('downloadImage3d');
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            console.log('ABC')
            downloadImage3d(url);
        })
                        
    }
    function downloadImage3d(url) {
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
    </script>
<?php } 
        
    } else{ 
    
    // Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$attachment_ids = $product->get_gallery_image_ids();

if ( $attachment_ids && $product->get_image_id() ) {
	foreach ( $attachment_ids as $attachment_id ) {
		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
}

 } ?>

