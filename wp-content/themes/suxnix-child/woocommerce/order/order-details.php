<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;
if(!empty($_POST['submit'])){
    
    $flag_note = $_POST['flag_note'];
    $flag_note_name = $_POST['flag_note_name'];
    update_post_meta($order_id, $flag_note_name.'_note', $flag_note);
}

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$label = get_post_meta($order_id, '_label', true) ;
$emptyFlagCount = 0;  

for ($i = 0; $i < $label; $i++) {
    $flag_name = "_flag" . $i;
    $flag = get_post_meta($order_id, $flag_name, true);
    $flag_array[] = get_post_meta($order_id, $flag_name, true);

    if (empty($flag)) {
        $emptyFlagCount++;
    }
}

//echo 'Number of empty flags: ' . $emptyFlagCount.count($flag_array);

if( $emptyFlagCount != $label){

 
echo "<h3>Order Label " .$label ."</h3>"; 

if($label > 0 )
{
    echo '<div style="width:100%;height:auto;float:left;border:1px solid #ccc;border-radius:10px;" >';
    for($i=0;$i<$label;$i++)
    { //echo $label; 
    $flag_name = "_flag".$i;
    $flag_name_not = "_flag".$i;
    $flag = get_post_meta($order_id, $flag_name, true) ;
    if($flag == 1 || $flag == 2 || $flag == 3)
    { 
 
    $label_img = "_label_upload_images" . $i;
    $label_img = get_post_meta($order_id, $label_img, true) ;
    
    $image_html = wp_get_attachment_image($label_img ); 
         echo '<div style="width:30%;height:auto; float:left;border:0px solid #000;margin:10px;padding:10px;" class="main_div_image"><h4> Label Image'.  $i+1 .'</h4>'.$image_html;
         if (in_array("2", $flag_array)){
         $disable_status = "disabled";} else  { $disable_status = "enabled"; }
          if( $flag == "3"){
         $disable_status1 = "disabled";} else  { $disable_status1 = "enabled"; }
        echo '<p><input '.$disable_status. ' type="button" value="Accept" class="accept" data-id="'. $flag_name .'" data-flagval="'. $flag .'" data-orderid="'. $order_id .'"    /><input '.$disable_status1. ' '.$disable_status.'  type="button" value="Decline" class =" decline show-modal" data-id="'. $flag_name .'" data-orderid="'. $order_id .'"  /></p>'; 
        
        echo '</div>';
    }
    
    
    }
    echo '</div>';
}
}
else
{
    echo "<p style='color:#000;'> Please wait for label upload...</p> ";
}
?>
<div id="testmodal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Confirmation</h4>
                    </div>
                    <form method = "post" action= "" >
                    <div class="modal-body">
                        <p>Label Declined </p>
                        <p class="text-warning">
                            <textarea id="flag_note" name="flag_note" style="height: 149px; width:450px" ></textarea>
                            <input  type="hidden" name="flag_note_name" value="" class="flag_note_hide"    />
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" name="submit" class="btn btn-primary"  value="Save changes" />
                    </div>
                    </form>
                </div>
            </div>
        </div>
<?php
if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<style>
    .main_div_image img {
    height: 150px;
}
.modal-backdrop.show {
	opacity: 0 !important;
}
.modal-backdrop {
  position: inherit !important;;
}
.modal {
	top: 80px;
	
}
.close {
	background-color: transparent;
	border: none;
}
</style>
<script>
    jQuery(document).ready(function(){ 
     
   
jQuery('.decline').click(function() {
var id11 = jQuery(this).data('id');
jQuery(".flag_note_hide").val(id11);

$value =  id11;

var order_id = jQuery(this).data('orderid');

jQuery.ajax({
                type: 'POST',
                url: "/wp-admin/admin-ajax.php",
                data: {
    action:'get_decline', //this value is first parameter of add_action
    text:  $value,
    order_id:  order_id,
    
},
beforeSend : function(){
             
    },
 
 
                success: function(result){
                    console.log(flag_note)
                    
                }
            });
            
//}          
});
jQuery('.accept').click(function() {
   
var id11 = jQuery(this).data('id');
 
$value =  id11;
var order_id = jQuery(this).data('orderid');
alert("Label Accepted ");
jQuery.ajax({
                type: 'POST',
                url: "/wp-admin/admin-ajax.php",
                data: {
    action:'get_data121', //this value is first parameter of add_action
    text:  $value,
    order_id:  order_id,
},
beforeSend : function(){
             
    },
 
 
                success: function(result){
                    console.log("hai")
                     location.reload(true);
                }
            });
            
           
});
});
</script>
<script>
    jQuery(document).ready(function(){
  var show_btn=$('.show-modal');
  var show_btn=$('.show-modal');
  //$("#testmodal").modal('show');
  
    show_btn.click(function(){
      jQuery("#testmodal").modal('show');
  })
});

jQuery(function() {
        jQuery('#element').on('click', function( e ) {
            Custombox.open({
                target: '#testmodal-1',
                effect: 'fadein'
            });
            e.preventDefault();
        });
        jQuery('.btn-default').on('click', function( e ) {
            jQuery("#testmodal").modal('hide');
        });
        jQuery('.close').on('click', function( e ) {
            jQuery("#testmodal").modal('hide');
        });
    });
</script>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title"> <?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
