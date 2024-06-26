<?php

// shop page hooks
// breadcrumb remove
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
// remove ordering
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
// remove shop sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// content-product hooks--
// action remove
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);


// Single product
add_action( 'woocommerce_single_product_summary', 'suxnix_woo_rating', 0 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 15 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'suxnix_details_wishlish', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 41 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
if( function_exists( 'suxnix_blog_social_share' ) ){
add_action( 'woocommerce_single_product_summary', 'suxnix_blog_social_share', 50 );
}

function suxnix_details_wishlish(){
    if( class_exists( 'TInvWL_Admin_TInvWL' ) ){
     echo do_shortcode( '[ti_wishlists_addtowishlist]' );
    }
}

// woocommerce mini cart content
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start();
    ?>
    <div class="header-mini-cart">
        <?php woocommerce_mini_cart(); ?>
    </div>
    <?php $fragments['.header-mini-cart'] = ob_get_clean();
    return $fragments;
});

// woocommerce mini cart count icon
if ( ! function_exists( 'suxnix_header_add_to_cart_fragment' ) ) {
    function suxnix_header_add_to_cart_fragment( $fragments ) {
        ob_start();
        ?>
        <span class="mini-cart-count" id="tp-cart-item">
            <?php echo esc_html(WC()->cart->cart_contents_count); ?>
        </span>
        <?php
        $fragments['#tp-cart-item'] = ob_get_clean();

        return $fragments;
    }
}
add_filter( 'woocommerce_add_to_cart_fragments', 'suxnix_header_add_to_cart_fragment' );


// product-content
if( !function_exists('suxnix_loop_product_thumbnail') ) {
    function suxnix_loop_product_thumbnail( ) {
        global $product;
        ?>
        <div class="home-shop-item inner-shop-item">
           <div class="home-shop-thumb aa">

                <?php if( has_post_thumbnail() ) { ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                <?php }else{ 
                $image = site_url() .'/wp-content/uploads/2024/01/placeholder-product.webp'; 
  echo '<img src="'.$image.'" alt="" />';
                 } ?>

                <div class="on-sale-wrap">
                    <?php woocommerce_show_product_loop_sale_flash(); ?>
                </div>

           </div>

            <div class="home-shop-content">
                <?php

                    global $post;
                    $terms = get_the_terms( $post->ID, 'product_cat' );
                    foreach ($terms as $term) {
                        $product_cat_name = $term->name;
                        $product_cat_id = $term->term_id;
                        break;
                    }

                ?>
                <div class="shop-item-cat">
                    <?php print '<a href=" '.get_category_link($product_cat_id).' " >'.$product_cat_name.'</a>'; ?>
                </div>
                <h4 class="title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
                <div class="home-shop-price">
                    <?php echo woocommerce_template_loop_price();?>
                </div>
                <div class="home-shop-rating">
                    <?php suxnix_woo_rating(); ?>
                </div>
                <div class="shop-content-bottom">
                    <?php woocommerce_template_loop_add_to_cart(); ?>
                    <a href="<?php echo esc_url(wc_get_checkout_url()) . '?add-to-cart=' . $product->get_id();?>" class="btn btn-two"><?php echo esc_html__('Buy Now','suxnix') ?></a>
                </div>
            </div>

        </div>
        <?php
    }
}
add_action( 'woocommerce_before_shop_loop_item', 'suxnix_loop_product_thumbnail', 10 );

add_filter( 'woosq_button_html', 'suxnix_woosq_button_html', 10, 2 );
function suxnix_woosq_button_html( $output , $prodid ) {
    return $output = '<a href="#" class="icon-btn woosq-btn woosq-btn-' . esc_attr( $prodid ) . ' ' . get_option( 'woosq_button_class' ) . '" data-id="' . esc_attr( $prodid ) . '" data-effect="mfp-3d-unfold"><i class="fal fa-eye"></i></a>';
}



// add to cart button
function woocommerce_template_loop_add_to_cart( $args = array() ) {
    global $product;

        if ( $product ) {
            $defaults = array(
                'quantity'   => 1,
                'class'      => implode(
                    ' ',
                    array_filter(
                        array(
                            'cart-button icon-btn',
                            'product_type_' . $product->get_type(),
                            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                            $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
                        )
                    )
                ),
                'attributes' => array(
                    'data-product_id'  => $product->get_id(),
                    'data-product_sku' => $product->get_sku(),
                    'aria-label'       => $product->add_to_cart_description(),
                    'rel'              => 'nofollow',
                ),
            );

            $args = wp_parse_args( $args, $defaults );

            if ( isset( $args['attributes']['aria-label'] ) ) {
                $args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
            }
        }

        echo sprintf( '<a href="%s" data-quantity="%s" class="add-cart-btn %s" %s>%s</a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : 'cart-button icon-btn' ),
            isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '','<i class="flaticon-shopping-cart-1"></i>'
        );
}


/**
 * [suxnix_woo_rating description]
 * @return [type] [description]
 */


function suxnix_woo_rating() {
    global $product;
    $rating = $product->get_average_rating();
    $review = 'Rating ' . $rating . ' out of 5';
    $html   = '';
    $html   .= '<div class="details-rating shop-single-rating" title="' . $review . '">';
    for ( $i = 0; $i <= 4; $i ++ ) {
        if ( $i < floor( $rating ) ) {
            $html .= '<i class="fas fa-star"></i>';
        } else {
            $html .= '<i class="far fa-star"></i>';
        }
    }
    $html .= '<span class="rating-count">( ' . $rating . ' out of 5 )</span>';
    $html .= '</div>';
    print suxnix_woo_rating_html( $html );
}

function suxnix_woo_rating_html( $html ) {
    return $html;
}

add_action( 'wp_footer' , 'custom_quantity_fields_script' );
function custom_quantity_fields_script(){
    ?>
        <script type='text/javascript'>
            jQuery( function( $ ) {
                if ( ! String.prototype.getDecimals ) {
                    String.prototype.getDecimals = function() {
                        var num = this,
                            match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
                        if ( ! match ) {
                            return 0;
                        }
                        return Math.max( 0, ( match[1] ? match[1].length : 0 ) - ( match[2] ? +match[2] : 0 ) );
                    }
                }
                // Quantity "plus" and "minus" buttons
                $( document.body ).on( 'click', '.plus, .minus', function() {
                    var $qty        = $( this ).closest( '.quantity' ).find( '.qty'),
                        currentVal  = parseFloat( $qty.val() ),
                        max         = parseFloat( $qty.attr( 'max' ) ),
                        min         = parseFloat( $qty.attr( 'min' ) ),
                        step        = $qty.attr( 'step' );

                    // Format values
                    if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
                    if ( max === '' || max === 'NaN' ) max = '';
                    if ( min === '' || min === 'NaN' ) min = 0;
                    if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

                    // Change the value
                    if ( $( this ).is( '.plus' ) ) {
                        if ( max && ( currentVal >= max ) ) {
                            $qty.val( max );
                        } else {
                            $qty.val( ( currentVal + parseFloat( step )).toFixed( step.getDecimals() ) );
                        }
                    } else {
                        if ( min && ( currentVal <= min ) ) {
                            $qty.val( min );
                        } else if ( currentVal > 0 ) {
                            $qty.val( ( currentVal - parseFloat( step )).toFixed( step.getDecimals() ) );
                        }
                    }

                    // Trigger change event
                    $qty.trigger( 'change' );
                });
            });
        </script>

    <?php
}