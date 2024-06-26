<?php

/**
 * Template part for displaying footer layout one
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package suxnix
*/

$footer_bg_img = get_theme_mod( 'suxnix_footer_bg' );
$suxnix_footer_logo = get_theme_mod( 'suxnix_footer_logo' );
$suxnix_footer_top_space = function_exists('get_field') ? get_field('suxnix_footer_top_space') : '0';
$suxnix_copyright_center = $suxnix_footer_logo ? 'col-lg-4 offset-lg-4 col-md-6 text-right' : 'col-lg-12 text-center';
$suxnix_footer_bg_url_from_page = function_exists( 'get_field' ) ? get_field( 'suxnix_footer_bg' ) : '';
$suxnix_footer_bg_color_from_page = function_exists( 'get_field' ) ? get_field( 'suxnix_footer_bg_color' ) : '';
$footer_bg_color = get_theme_mod( 'suxnix_footer_bg_color' );

// Footer Instagram
$is_enable_footer_instagram = function_exists('get_field') ? get_field('is_enable_footer_instagram') : '';
$suxnix_show_footer_instagram = $is_enable_footer_instagram ? 'footer-area' : 'footer-area not-show-instagram';

// BG Image
$bg_img = !empty( $suxnix_footer_bg_url_from_page['url'] ) ? $suxnix_footer_bg_url_from_page['url'] : $footer_bg_img;

// BG Color
$bg_color = !empty( $suxnix_footer_bg_color_from_page ) ? $suxnix_footer_bg_color_from_page : $footer_bg_color;

// Footer Shape
$suxnix_show_footer_shape = get_theme_mod( 'suxnix_show_footer_shape', false );
$footer_payment_method_img = get_theme_mod('footer_payment_method_img');
$suxnix_copyright_center = $footer_payment_method_img ? 'col-md-7' : 'col-lg-12 text-center';

// footer_columns
$footer_columns = 0;
$footer_widgets = get_theme_mod( 'footer_widget_number', 4 );

for ( $num = 1; $num <= $footer_widgets; $num++ ) {
    if ( is_active_sidebar( 'footer-' . $num ) ) {
        $footer_columns++;
    }
}

switch ( $footer_columns ) {
case '1':
    $footer_class[1] = 'col-lg-12';
    break;
case '2':
    $footer_class[1] = 'col-lg-6 col-md-6';
    $footer_class[2] = 'col-lg-6 col-md-6';
    break;
case '3':
    $footer_class[1] = 'col-lg-4 col-md-6';
    $footer_class[2] = 'col-lg-4 col-md-6 col-sm-6';
    $footer_class[3] = 'col-lg-4 col-md-6 col-sm-6';
    break;
case '4':
    $footer_class[1] = 'col-lg-4 col-md-7';
    $footer_class[2] = 'col-lg-3 col-md-5 col-sm-6';
    $footer_class[3] = 'col-lg-2 col-md-5 col-sm-6';
    $footer_class[4] = 'col-lg-3 col-md-5';
    break;
default:
    $footer_class = 'col-xl-3 col-lg-4 col-sm-6';
    break;
}

?>


<!-- Footer-area -->
<footer class="<?php echo esc_attr($suxnix_show_footer_instagram); ?>">

    <?php if ( is_active_sidebar('footer-1') OR is_active_sidebar('footer-2') OR is_active_sidebar('footer-3') OR is_active_sidebar('footer-4') ): ?>
    <div class="footer-top-wrap" data-bg-color="<?php print esc_attr( $bg_color );?>" data-background="<?php print esc_url( $bg_img );?>">
        <div class="container">
            <div class="footer-widgets-wrap">
                <div class="row">
                    <?php
                        if ( $footer_columns < 4 ) {
                        print '<div class="col-lg-4 col-md-7">';
                        dynamic_sidebar( 'footer-1' );
                        print '</div>';

                        print '<div class="col-lg-3 col-md-5 col-sm-6">';
                        dynamic_sidebar( 'footer-2' );
                        print '</div>';

                        print '<div class="col-lg-2 col-md-5 col-sm-6">';
                        dynamic_sidebar( 'footer-3' );
                        print '</div>';

                        print '<div class="col-lg-3 col-md-5">';
                        dynamic_sidebar( 'footer-4' );
                        print '</div>';
                        } else {
                            for ( $num = 1; $num <= $footer_columns; $num++ ) {
                                if ( !is_active_sidebar( 'footer-' . $num ) ) {
                                    continue;
                                }
                                print '<div class="' . esc_attr( $footer_class[$num] ) . '">';
                                dynamic_sidebar( 'footer-' . $num );
                                print '</div>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>

        <?php if ( !empty($suxnix_show_footer_shape) ) : ?>
            <div class="footer-shape one">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/others/footer_shape01.png" alt="img" class="wow fadeInLeft" data-wow-delay=".3s" data-wow-duration="1s">
            </div>
            <div class="footer-shape two">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/others/footer_shape02.png" alt="img" class="wow fadeInRight" data-wow-delay=".3s" data-wow-duration="1s">
            </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <div class="copyright-wrap">
        <div class="container">
            <div class="row align-items-center">
                <div class="<?php print esc_attr($suxnix_copyright_center); ?>">
                    <div class="copyright-text">
                        <p><?php print suxnix_copyright_text(); ?></p>
                    </div>
                </div>
                <?php if (!empty($footer_payment_method_img)) : ?>
                <div class="col-md-5">
                    <div class="payment-card text-center text-md-end">
                        <img src="<?php echo esc_url($footer_payment_method_img); ?>" alt="card">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>
<!-- Footer-area-end -->