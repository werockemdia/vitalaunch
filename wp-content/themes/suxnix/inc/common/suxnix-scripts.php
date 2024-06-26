<?php

/**
 * suxnix_scripts description
 * @return [type] [description]
 */
function suxnix_scripts() {


    /**
     * ALL CSS FILES
    */
    wp_enqueue_style( 'suxnix-fonts', suxnix_fonts_url(), array(), '1.0.0' );
    if( is_rtl() ){
        wp_enqueue_style( 'bootstrap-rtl', SUXNIX_THEME_CSS_DIR.'bootstrap.rtl.min.css', array() );
    }else{
        wp_enqueue_style( 'bootstrap', SUXNIX_THEME_CSS_DIR.'bootstrap.min.css', array() );
    }
    wp_enqueue_style( 'animate', SUXNIX_THEME_CSS_DIR . 'animate.min.css', [] );
    wp_enqueue_style( 'magnific-popup', SUXNIX_THEME_CSS_DIR . 'magnific-popup.css', [] );
    wp_enqueue_style( 'font-awesome-free', SUXNIX_THEME_CSS_DIR . 'fontawesome-all.min.css', [] );
    wp_enqueue_style( 'flaticon', SUXNIX_THEME_CSS_DIR . 'flaticon.css', [] );
    wp_enqueue_style( 'slick', SUXNIX_THEME_CSS_DIR . 'slick.css', [] );
    wp_enqueue_style( 'suxnix-default', SUXNIX_THEME_CSS_DIR . 'default.css', [] );
    wp_enqueue_style( 'suxnix-core', SUXNIX_THEME_CSS_DIR . 'suxnix-core.css', [] );
    wp_enqueue_style( 'suxnix-unit', SUXNIX_THEME_CSS_DIR . 'suxnix-unit.css', [] );
    wp_enqueue_style( 'suxnix-woo', SUXNIX_THEME_CSS_DIR . 'woo.css', [] );
    wp_enqueue_style( 'suxnix-shop', SUXNIX_THEME_CSS_DIR . 'suxnix-shop.css', [] );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    wp_enqueue_style( 'suxnix-style', get_stylesheet_uri() );
    wp_enqueue_style( 'suxnix-responsive', SUXNIX_THEME_CSS_DIR . 'responsive.css', [] );


    // ALL JS FILES
    wp_enqueue_script( 'bootstrap-bundle', SUXNIX_THEME_JS_DIR . 'bootstrap.min.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'isotope-pkgd', SUXNIX_THEME_JS_DIR . 'isotope.pkgd.min.js', [ 'imagesloaded' ], '', true );
    wp_enqueue_script( 'magnific-popup', SUXNIX_THEME_JS_DIR . 'jquery.magnific-popup.min.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'paroller-min', SUXNIX_THEME_JS_DIR . 'jquery.paroller.min.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'easypiechart', SUXNIX_THEME_JS_DIR . 'jquery.easypiechart.min.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'inview-min', SUXNIX_THEME_JS_DIR . 'jquery.inview.min.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'easing-js', SUXNIX_THEME_JS_DIR . 'jquery.easing.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'slick', SUXNIX_THEME_JS_DIR . 'slick.min.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'wow', SUXNIX_THEME_JS_DIR . 'wow.min.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'suxnix-main', SUXNIX_THEME_JS_DIR . 'main.js', [ 'jquery' ], false, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_scripts' );

/*
Register Fonts
*/
function suxnix_fonts_url() {
    $font_url = '';

    /*
    Translators: If there are characters in your language that are not supported
    by chosen font(s), translate this to 'off'. Do not translate into your own language.
    */
    if ( 'off' !== _x( 'on', 'Google font: on or off', 'suxnix' ) ) {
        $font_url = add_query_arg( 'family', urlencode( 'Oswald:400,500,600,700|Roboto:400,500,700' ), "//fonts.googleapis.com/css" );
    }
    return $font_url;
}