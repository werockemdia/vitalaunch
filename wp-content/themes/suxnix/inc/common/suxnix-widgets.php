<?php

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function suxnix_widgets_init() {

    $footer_style_2_switch = get_theme_mod( 'footer_style_2_switch', false );
    $footer_style_3_switch = get_theme_mod( 'footer_style_3_switch', false );
    $footer_style_4_switch = get_theme_mod( 'footer_style_4_switch', false );

    /**
     * Blog sidebar
     */
    register_sidebar( [
        'name'          => esc_html__( 'Blog Sidebar', 'suxnix' ),
        'id'            => 'blog-sidebar',
        'before_widget' => '<div id="%1$s" class="sidebar-widget widget mb-40 %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="sidebar-title mb-25"><h3 class="title">',
        'after_title'   => '</h3></div>',
    ] );


    $footer_widgets = get_theme_mod( 'footer_widget_number', 4 );

    // Footer Default
    for ( $num = 1; $num <= $footer_widgets; $num++ ) {
        register_sidebar( [
            'name'          => sprintf( esc_html__( 'Footer %1$s', 'suxnix' ), $num ),
            'id'            => 'footer-' . $num,
            'description'   => sprintf( esc_html__( 'Footer Column %1$s', 'suxnix' ), $num ),
            'before_widget' => '<div id="%1$s" class="footer-widget column-'.$num.' %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="fw-title">',
            'after_title'   => '</h4>',
        ] );
    }

    // Product Sidebar
    register_sidebar(array(
        'name' => esc_html__('Product Sidebar', 'suxnix'),
        'id' => 'product-sidebar',
        'before_widget' => '<div id="%1$s" class="product-widgets side-cat %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="sidebar-title">',
        'after_title' => '</h4>',
    ));

}
add_action( 'widgets_init', 'suxnix_widgets_init' );