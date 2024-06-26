<?php
/**
 * Breadcrumbs for suxnix theme.
 *
 * @package     suxnix
 * @author      ThemeGenix
 * @copyright   Copyright (c) 2022, ThemeGenix
 * @link        https://www.themepure.net
 * @since       suxnix 1.0.0
 */


function suxnix_breadcrumb_func() {
    global $post;
    $breadcrumb_class = '';
    $breadcrumb_show = 1;

    if ( is_front_page() && is_home() ) {
        $title = get_theme_mod('breadcrumb_blog_title', __('Blog','suxnix'));
        $breadcrumb_class = 'home_front_page';
    }
    elseif ( is_front_page() ) {
        $title = get_theme_mod('breadcrumb_blog_title', __('Blog','suxnix'));
        $breadcrumb_show = 0;
    }
    elseif ( is_home() ) {
        if ( get_option( 'page_for_posts' ) ) {
            $title = get_the_title( get_option( 'page_for_posts') );
        }
    }
    elseif ( is_single() && 'post' == get_post_type() ) {
      $title = get_the_title();
    }
    elseif ( is_single() && 'product' == get_post_type() ) {
        $title = get_theme_mod( 'breadcrumb_product_details', __( 'Shop', 'suxnix' ) );
    }
    elseif ( is_single() && 'courses' == get_post_type() ) {
      $title = esc_html__( 'Course Details', 'suxnix' );
    }
    elseif ( is_search() ) {

        $title = esc_html__( 'Search Results for : ', 'suxnix' ) . get_search_query();
    }
    elseif ( is_404() ) {
        $title = esc_html__( 'Page not Found', 'suxnix' );
    }
    elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
        $title = get_theme_mod( 'breadcrumb_shop', __( 'Shop', 'suxnix' ) );
    }
    elseif ( is_archive() ) {
        $title = get_the_archive_title();
    }
    else {
        $title = get_the_title();
    }


    $_id = get_the_ID();

    if ( is_single() && 'product' == get_post_type() ) {
        $_id = $post->ID;
    }
    elseif ( function_exists("is_shop") AND is_shop()  ) {
        $_id = wc_get_page_id('shop');
    }
    elseif ( is_home() && get_option( 'page_for_posts' ) ) {
        $_id = get_option( 'page_for_posts' );
    }

    $is_breadcrumb = function_exists( 'get_field' ) ? get_field( 'is_it_invisible_breadcrumb', $_id ) : '';
    if( !empty($_GET['s']) ) {
      $is_breadcrumb = null;
    }

      if ( empty( $is_breadcrumb ) && $breadcrumb_show == 1 ) {

        $bg_img_from_page = function_exists('get_field') ? get_field('breadcrumb_background_image',$_id) : '';
        $hide_bg_img = function_exists('get_field') ? get_field('hide_breadcrumb_background_image',$_id) : '';

        // get_theme_mod
        $bg_img = get_theme_mod( 'breadcrumb_bg_img' );
        $breadcrumb_info_switch = get_theme_mod( 'breadcrumb_info_switch', true );

        if ( $hide_bg_img && empty($_GET['s']) ) {
            $bg_img = '';
        } else {
            $bg_img = !empty( $bg_img_from_page ) ? $bg_img_from_page['url'] : $bg_img;
        }?>

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg <?php print esc_attr( $breadcrumb_class );?>" data-background="<?php print esc_attr($bg_img);?>">
            <div class="container">
                <div class="row justify-content-center">
                    <?php if (!empty($breadcrumb_info_switch)) : ?>
                    <div class="col-xl-10">
                        <div class="breadcrumb-content text-center">
                            <h2 class="title"><?php echo wp_kses_post( $title ); ?></h2>
                            <nav aria-label="breadcrumb" class="breadcrumb">
                                <?php if(function_exists('bcn_display')) {
                                    bcn_display();
                                } ?>
                            </nav>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="video-shape one"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/others/video_shape01.png" alt="shape"></div>
            <div class="video-shape two"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/others/video_shape02.png" alt="shape"></div>
        </section>
        <!-- breadcrumb-area-end -->

        <?php
      }
}

add_action( 'suxnix_before_main_content', 'suxnix_breadcrumb_func' );
