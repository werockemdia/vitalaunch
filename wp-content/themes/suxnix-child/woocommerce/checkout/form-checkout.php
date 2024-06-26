<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'suxnix' ) ) );
	return;
}

//$mrg_img = get_post_meta('$p_id', '_mrg_img', 1);
//if($mrg_img != 1){
foreach( WC()->cart->get_cart() as $cart_item ){
    $id = $cart_item['product_id'];
     $product = $cart_item['data'];
     $sku =  $product->sku ;
     $label_imgs = $cart_item['label_imgs'];
     $label_price = $cart_item['label_price'];
     $uploadimg = $cart_item['image_url'];
}

if(!empty($uploadimg)){
    
    ?>
    <script>
    jQuery(document).ready(function($) {
        var uploadimg = '<?php echo $uploadimg ?>';
        $("#uploadimg").val(uploadimg);
        });
    
</script>
    <?php
}
if(!empty($label_price)){
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('form.checkout').submit(function() {
           
            // Show loader and text
            $("<div class="loader-overlay"><div class="loader"></div></br><p>Your order is being proccesed, you' will be redirected to the catalog when it is done</p></div>").appendTo('body');

            // Optionally, you might want to disable the checkout button to prevent double submission
            $('button[type="submit"]').attr('disabled', 'disabled');
        });
    });
</script>
    <?php
}
if(!empty($uploadimg)){
$u_id = get_current_user_id(); 
 $curl_handle = curl_init();
            
            $url = "https://api.pacdora.com/open/v1/user/projects?userId=".$u_id;
                     
                    $crl = curl_init();
                    $data = array("appid" => "8c5f9c28d30f5dbd",  "appkey" => "3acf8da0a0a7dde8");
                    curl_setopt($crl, CURLOPT_URL, $url);
                    curl_setopt($crl, CURLOPT_FRESH_CONNECT, $data);
                    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($crl, CURLOPT_HTTPHEADER, array("appid: 8c5f9c28d30f5dbd", "appkey: 3acf8da0a0a7dde8"));
                     
                    $response = curl_exec($crl);
                    
                    curl_close($crl);
                    $response_data = json_decode($response);
                    $user_data = $response_data->data;
            
                    // Extract only first 5 user data (or 5 array elements)
                    $user_data = array_slice($user_data, 0, 4);
                     $staticImagePath =  $user_data[0]->screenshot;
                      $api_project_id =  $user_data[0]->id;
                     $staticImagePath = 'https:'.$staticImagePath;
                     
                     
                   /* ?>
                    <script>
                    jQuery(document).ready(function($){ 
                        var intervalId = window.setInterval(function(){
                          // call your function here
                         var a = $(".woocommerce-product-gallery__wrapper .oldimg").attr('data-pacdora-id');
                         alert(a);
                        }, 5000);
                        
                            
                    });  
                    </script>
                    <?php */
                    
            //if(isset($_POST['Submit1']))
            
            if(!empty($staticImagePath)){  
                
               // echo "<script>console.log('checking');</script>";
                   // $staticImagePath = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/background.png" ; // Path to the static image
                    //$staticImagePath = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/" .$_POST['static_path'];
                     //$staticImagePath = strval($staticImagePath);
             
                    //if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $targetDir = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/";
                        
                        $user_id = get_current_user_id();
                 if (user_can($user_id, 'customer')) {
               // echo 'User is a vendor.'.$user_id . "<br/>";
                 
                 $site_id =  get_user_meta( $user_id, 'primary_blog', true );
                 }
                 
                        
                        switch_to_blog($site_id); 
                     
                        if (!file_exists($targetDir)) {
                            mkdir($targetDir, 0777, true);
                        }
                     
                        $targetFile2 = $targetDir . basename($_FILES["file"]["name"]);
                        
                       // $mergedFile = $targetDir ."merged_image-". basename($_FILES["file"]["name"]);
                     
                        move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile2);
                        
                     /*$ss1 = $_POST['static_path'];
                         if($ss1 == "background.png"){ mergeImages($staticImagePath, $targetFile2, $mergedFile, 7.9, 200 , 0); }
                         else if($ss1 == "powder-min.png"){ mergeImages($staticImagePath, $targetFile2, $mergedFile, 1.1, 110,170); }
                         else if($ss1 == "Capsulles-min.png"){ mergeImages($staticImagePath, $targetFile2, $mergedFile, 1.4, 250,320); }
                          else if($ss1 == "capsule-original.png"){ 
                              $thirdImage = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/capsule-worked.png"; 
                              mergeImagesWithThirdImage($staticImagePath, $targetFile2, $mergedFile, $thirdImage, 0.9, 70, 20); }
                         
                         else {
                      // mergeImages($staticImagePath, $targetFile2, $mergedFile, 7.9, 200);
                       mergeImages($staticImagePath, $targetFile2, $mergedFile, 1.2, 250,270); }
                     
                        echo "Merged image saved as: " . $mergedFile;
                        
                        $mergedFileame  =  "merged_image-".basename($_FILES["file"]["name"]); */
                        global $wpdb, $product;
                        $user_id = get_current_user_id();
                        
                        
                        $fileName = basename($_FILES["file"]["name"]);
                        $img2 = "https://vitalaunch.io/wp-content/uploads/merged_image-".basename($_FILES["file"]["name"]);
            				$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            				$url =$actual_link."/?qty=2&img='".$staticImagePath."'" ;
            				
            
                            $title = "Some Image Title";
                            $alt_text = "Some Alt Text";
                            /*
                            require_once(ABSPATH . 'wp-admin/includes/media.php');
                            require_once(ABSPATH . 'wp-admin/includes/file.php');
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            */
                            // sideload the image --- requires the files above to work correctly
                            global $wpdb, $product;
                            $user_id = get_current_user_id();
                            //$id = get_the_id();
                            
                            $src = media_sideload_image( $staticImagePath, null, null, 'src' ); 
                            
                            // convert the url to image id
                           $image_id = attachment_url_to_postid( $src );
                          
                            $_SESSION['image_id']= $image_id;
                              
                            if( $image_id ) {
                            
                                // make sure the post exists
                                $image = get_post( $image_id );
                            
                               // if( $image) {
                            
                                    // Add title to image
                                   /* wp_update_post( array (
                                        'ID'         => $image_id,
                                        'post_title' => "Some Image Title",
                                        'post_author' => $user_id 
                                    ) );*/
                            
                                    // Add Alt text to image
                                    //update_post_meta($image->ID, '_wp_attachment_image_alt', $alt_text);
                                    $im_id = $image_id;
                                    $wpdb->insert('marge_img', array(
                                        'user_id' => $user_id,//$user_id,
                                        'sku'  => $sku, 
                                        'product_id' => $id,
                                        'image_url' => $staticImagePath, 
                                        'image_id' => $image_id,
                                        'Label' => 'Label',
                                    ));
                                    $prefixcout =  get_user_meta( $user_id, 'primary_blog', true );
                            
                                   $tale ='wp_"'.$prefixcout.'"_postmeta';
                            
                                    $meta_values = $wpdb->get_results("SELECT * FROM $tale   WHERE meta_key = 'sku' AND meta_value = '".$sku."' ORDER BY ID DESC LIMIT 1");
                                   
                                    $p_id =  $meta_values[0]->post_id;  
                                    //update_post_meta('$p_id', '_thumbnail_id', $image_id);
                                    
                    				//update_post_meta('$p_id', '_mrg_img', 1);
                                //}
                            }
                            restore_current_blog();
                           // wp_redirect($url );
                   // }
                   
         $postParameter = array(
            'projectIds' => $api_project_id,
            
        );
        //echo $postParameter['projectIds'];
         $url = "https://api.pacdora.com/open/v1/user/projects/export/pdf/";
        $json = json_encode($postParameter);
        $crl = curl_init();
        $data = array("appid" => "8c5f9c28d30f5dbd",  "appkey" => "3acf8da0a0a7dde8");
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_FRESH_CONNECT, $data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "POST");
        $data = "projectIds[0]=".$api_project_id;
        curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($crl, CURLOPT_HTTPHEADER, array("appid: 8c5f9c28d30f5dbd", "appkey: 3acf8da0a0a7dde8"));
        $response = curl_exec($crl);
        curl_close($crl);
        $response_data = json_decode($response);
        //$user_data = $response_data->data; 
        //$user_data = array_slice($user_data, 0, 4);
         $user_data = json_decode($response, true);
        
        //$filePath =  $user_data['data']->filePath;
        $user_data_arr = $user_data['data'];
         $taskIdty = $user_data_arr[0]['taskId'];
        //$taskIdty =  $user_data[0]->taskId;
        
        $taskId =374971;
        $endpoint = "https://api.pacdora.com/open/v1/user/projects/export/pdf?taskId=".$taskId;
        
        // Your App ID and App Key
        $app_id = "8c5f9c28d30f5dbd";
        $app_key = "3acf8da0a0a7dde8";
        
        // Initialize cURL session
        $curl = curl_init();
        
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "appId: $app_id",
                "appKey: $app_key",
                "Content-Type: application/json" // Adjust this according to the API's requirements
            )
        ));
        
        // Execute the request
        $response = curl_exec($curl);
        
        // Check for errors
        if ($response === false) {
            //echo "Error: " . curl_error($curl);
            // Handle error as needed
        } else {
            // Parse JSON response
            $user_data = json_decode($response, true);
            $filePath =  $user_data['data']->filePath;
            $user_data_arr = $user_data['data'];
           $user_pdf_path = $user_data_arr['filePath'];
            
             
    
            $upload_dir = "uploads/"; // Directory where you want to save uploaded files
            $file_name = basename($user_pdf_path);
            $source_file = $user_pdf_path;
             $target_path_rand = rand();
            $target_path = $upload_dir .$target_path_rand. $file_name;
        
            // Check if file already exists
            if (file_exists($target_path)) {
               // echo "Sorry, file already exists.";
            } else {
                // Read the file content and save it using file_put_contents()
                $file_content = file_get_contents($source_file);
                if (file_put_contents($target_path, $file_content) !== false) {
                   // echo "The file " . $file_name . " has been uploaded.";
                    
                    $wpdb->insert('marge_img', array(
                                                'user_id' => $user_id,//$user_id,
                                                'sku'  => $sku, 
                                                'product_id' => $id,
                                                'image_url' => $staticImagePath, 
                                                'image_id' => $image_id,
                                                'pdf' => $target_path,
                                                'Label' => 'Label',
                                            ));
                    
                    
                } else {
                   // echo "Sorry, there was an error uploading your file.";
                }
            }
        }
                    
            }
}

            ?>
<style>
    .order-review-wrapper {
    background: #fff;
    border: 0px;
}
.woocommerce-billing-fields input {
    background: #fff;
}
.woocommerce-billing-fields select {
    background: #fff;
}
.wc-stripe-elements-field, .wc-stripe-iban-element-field {
    border: 1px solid;
}
button#place_order {
    padding: 14px;
    background: #000;
}
button#place_order:hover {
    padding: 14px;
    background: #000;
}
div#stripe-payment-data p {
    display: none;
}
ul.woocommerce-SavedPaymentMethods.wc-saved-payment-methods {
    list-style: none;
    padding: 0px;
}
</style>
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="row" id="customer_details">
			<div class="col-lg-7">
				<div id="customer_form_details">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				    
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			</div>

			<div class="col-lg-5">
				<div class="cart-wrapper">
					<div class="order-review-wrapper">
						<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

						<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'suxnix' ); ?></h3>

						<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

						<div id="order_review" class="woocommerce-checkout-review-order">
							<?php do_action( 'woocommerce_checkout_order_review' ); ?>
						</div>

						<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
					</div>
				</div>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
