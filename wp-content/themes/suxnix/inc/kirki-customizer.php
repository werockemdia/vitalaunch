<?php
/**
 * suxnix customizer
 *
 * @package suxnix
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Added Panels & Sections
 */
function suxnix_customizer_panels_sections( $wp_customize ) {

    //Add panel
    $wp_customize->add_panel( 'suxnix_customizer', [
        'priority' => 10,
        'title'    => esc_html__( 'Suxnix Customizer', 'suxnix' ),
    ] );

    /**
     * Customizer Section
     */
    $wp_customize->add_section( 'header_right_setting', [
        'title'       => esc_html__( 'Header Right Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 10,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'header_side_setting', [
        'title'       => esc_html__( 'Side Info Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 11,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'mobile_menu_setting', [
        'title'       => esc_html__( 'Mobile Menu Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 12,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'section_header_logo', [
        'title'       => esc_html__( 'Header Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 13,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'breadcrumb_setting', [
        'title'       => esc_html__( 'Breadcrumb Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 15,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'blog_setting', [
        'title'       => esc_html__( 'Blog Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 16,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'footer_setting', [
        'title'       => esc_html__( 'Footer Settings', 'suxnix' ),
        'description' => '',
        'priority'    => 17,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'color_setting', [
        'title'       => esc_html__( 'Color Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 18,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( '404_page', [
        'title'       => esc_html__( '404 Page', 'suxnix' ),
        'description' => '',
        'priority'    => 19,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'typo_setting', [
        'title'       => esc_html__( 'Typography Setting', 'suxnix' ),
        'description' => '',
        'priority'    => 20,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );

    $wp_customize->add_section( 'slug_setting', [
        'title'       => esc_html__( 'Slug Settings', 'suxnix' ),
        'description' => '',
        'priority'    => 21,
        'capability'  => 'edit_theme_options',
        'panel'       => 'suxnix_customizer',
    ] );
}

add_action( 'customize_register', 'suxnix_customizer_panels_sections' );


/*
Header Right Settings
*/
function _header_right_fields( $fields ) {

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_show_header_right',
        'label'    => esc_html__( 'Show Header Right', 'suxnix' ),
        'section'  => 'header_right_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_preloader',
        'label'    => esc_html__( 'Preloader ON/OFF', 'suxnix' ),
        'section'  => 'header_right_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_backtotop',
        'label'    => esc_html__( 'Back To Top ON/OFF', 'suxnix' ),
        'section'  => 'header_right_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_show_heder_search',
        'label'    => esc_html__( 'Show Header Search', 'suxnix' ),
        'section'  => 'header_right_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_show_hamburger_btn',
        'label'    => esc_html__( 'Show Hamburger Button', 'suxnix' ),
        'section'  => 'header_right_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    return $fields;
}
add_filter( 'kirki/fields', '_header_right_fields' );

/*
Mobile Menu Settings
*/
function _mobile_menu_fields( $fields ) {

    $fields[] = [
        'type'        => 'image',
        'settings'    => 'mobile_logo',
        'label'       => esc_html__( 'Mobile Menu Logo', 'suxnix' ),
        'description' => esc_html__( 'Upload Your Logo.', 'suxnix' ),
        'section'     => 'mobile_menu_setting',
        'default'     => get_template_directory_uri() . '/assets/img/logo/secondary_logo.png',
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_show_mobile_social',
        'label'    => esc_html__( 'Show Mobile Menu Social', 'suxnix' ),
        'section'  => 'mobile_menu_setting',
        'default'  => 1,
        'priority' => 12,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    // Mobile section social
    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_mobile_fb_url',
        'label'    => esc_html__( 'Facebook URL', 'suxnix' ),
        'section'  => 'mobile_menu_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 12,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_mobile_twitter_url',
        'label'    => esc_html__( 'Twitter URL', 'suxnix' ),
        'section'  => 'mobile_menu_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 12,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_mobile_instagram_url',
        'label'    => esc_html__( 'Instagram URL', 'suxnix' ),
        'section'  => 'mobile_menu_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 12,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_mobile_linkedin_url',
        'label'    => esc_html__( 'Linkedin URL', 'suxnix' ),
        'section'  => 'mobile_menu_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 12,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_mobile_youtube_url',
        'label'    => esc_html__( 'Youtube URL', 'suxnix' ),
        'section'  => 'mobile_menu_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 12,
    ];

    return $fields;
}
add_filter( 'kirki/fields', '_mobile_menu_fields' );


/*
Header Settings
 */
function _header_header_fields( $fields ) {
    $fields[] = [
        'type'        => 'radio-image',
        'settings'    => 'choose_default_header',
        'label'       => esc_html__( 'Select Header Style', 'suxnix' ),
        'section'     => 'section_header_logo',
        'placeholder' => esc_html__( 'Select an option...', 'suxnix' ),
        'priority'    => 10,
        'multiple'    => 1,
        'choices'     => [
            'header-style-1'   => get_template_directory_uri() . '/inc/img/header/header-1.png',
            'header-style-2' => get_template_directory_uri() . '/inc/img/header/header-2.png',
        ],
        'default'     => 'header-style-1',
    ];

    $fields[] = [
        'type'        => 'image',
        'settings'    => 'logo',
        'label'       => esc_html__( 'Header Logo', 'suxnix' ),
        'description' => esc_html__( 'Upload Your Logo.', 'suxnix' ),
        'section'     => 'section_header_logo',
        'default'     => get_template_directory_uri() . '/assets/img/logo/logo.png',
    ];

    $fields[] = [
        'type'        => 'image',
        'settings'    => 'secondary_logo',
        'label'       => esc_html__( 'Header Secondary Logo', 'suxnix' ),
        'description' => esc_html__( 'Header Logo Black', 'suxnix' ),
        'section'     => 'section_header_logo',
        'default'     => get_template_directory_uri() . '/assets/img/logo/secondary_logo.png',
    ];

    $fields[] = [
        'type'        => 'image',
        'settings'    => 'preloader_logo',
        'label'       => esc_html__( 'Preloader Logo', 'suxnix' ),
        'description' => esc_html__( 'Upload Preloader Logo.', 'suxnix' ),
        'section'     => 'section_header_logo',
        'default'     => get_template_directory_uri() . '/assets/img/favicon.png',
    ];

    return $fields;
}
add_filter( 'kirki/fields', '_header_header_fields' );

/*
Header Side Info
 */
function _header_side_fields( $fields ) {
    // side info settings
    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_side_hide',
        'label'    => esc_html__( 'Side Info ON/OFF', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];
    $fields[] = [
        'type'     => 'textarea',
        'settings' => 'suxnix_extra_title',
        'label'    => esc_html__( 'Side Title', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => wp_kses_post( 'Getting all of the <span>Nutrients</span> you need simply cannot be done without supplements.', 'suxnix' ),
        'priority' => 10,
    ];
    $fields[] = [
        'type'     => 'textarea',
        'settings' => 'suxnix_extra_about_text',
        'label'    => esc_html__( 'Side Description Text', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => esc_html__( 'Nam libero tempore, cum soluta nobis eligendi cumque quod placeat facere possimus assumenda omnis dolor repellendu sautem temporibus officiis', 'suxnix' ),
        'priority' => 10,
    ];

    // Contact
    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_contact_number',
        'label'    => esc_html__( 'Phone Number', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => esc_html__( '+1 599 162 4545', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_contact_mail',
        'label'    => esc_html__( 'Email Address', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => esc_html__( 'suxnix@gmail.com', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'     => 'textarea',
        'settings' => 'suxnix_office_address',
        'label'    => esc_html__( 'Office Address', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => wp_kses_post( '5689 Lotaso Terrace, Culver City, <br> CA, United States', 'suxnix' ),
        'priority' => 10,
    ];

    // Sidebar Social
    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_sidebar_fb_url',
        'label'    => esc_html__( 'Facebook URL', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_sidebar_twitter_url',
        'label'    => esc_html__( 'Twitter URL', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_sidebar_instagram_url',
        'label'    => esc_html__( 'Instagram URL', 'suxnix' ),
        'section'  => 'header_side_setting',
        'default'  => esc_html__( '#', 'suxnix' ),
        'priority' => 10,
    ];

    return $fields;
}
add_filter( 'kirki/fields', '_header_side_fields' );

/*
_header_page_title_fields
 */
function _header_page_title_fields( $fields ) {
    // Breadcrumb Setting
    $fields[] = [
        'type'        => 'image',
        'settings'    => 'breadcrumb_bg_img',
        'label'       => esc_html__( 'Breadcrumb Background Image', 'suxnix' ),
        'description' => esc_html__( 'Breadcrumb Background Image', 'suxnix' ),
        'section'     => 'breadcrumb_setting',
        'default'     => get_template_directory_uri() . '/assets/img/bg/video_bg.jpg',
    ];
    $fields[] = [
        'type'        => 'color',
        'settings'    => 'suxnix_breadcrumb_bg_color',
        'label'       => __( 'Breadcrumb BG Color', 'suxnix' ),
        'description' => esc_html__( 'This is a Breadcrumb bg color control.', 'suxnix' ),
        'section'     => 'breadcrumb_setting',
        'default'     => '#090909',
        'priority'    => 10,
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'breadcrumb_info_switch',
        'label'    => esc_html__( 'Breadcrumb Info switch', 'suxnix' ),
        'section'  => 'breadcrumb_setting',
        'default'  => '1',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    return $fields;
}
add_filter( 'kirki/fields', '_header_page_title_fields' );

/*
Header Social
 */
function _header_blog_fields( $fields ) {
// Blog Setting
    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_blog_btn_switch',
        'label'    => esc_html__( 'Blog Button ON/OFF', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => '1',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_blog_cat',
        'label'    => esc_html__( 'Blog Category Meta ON/OFF', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_blog_author',
        'label'    => esc_html__( 'Blog Author Meta ON/OFF', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => '1',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_blog_date',
        'label'    => esc_html__( 'Blog Date Meta ON/OFF', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => '1',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_blog_comments',
        'label'    => esc_html__( 'Blog Comments Meta ON/OFF', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => '1',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_show_blog_share',
        'label'    => esc_html__( 'Show Blog Share', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_blog_btn',
        'label'    => esc_html__( 'Blog Button text', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => esc_html__( 'Read More', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'breadcrumb_blog_title',
        'label'    => esc_html__( 'Blog Title', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => esc_html__( 'Blog', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'breadcrumb_blog_title_details',
        'label'    => esc_html__( 'Blog Details Title', 'suxnix' ),
        'section'  => 'blog_setting',
        'default'  => esc_html__( 'Blog Details', 'suxnix' ),
        'priority' => 10,
    ];
    return $fields;
}
add_filter( 'kirki/fields', '_header_blog_fields' );

/*
Footer
 */
function _header_footer_fields( $fields ) {
    // Footer Setting
    $fields[] = [
        'type'        => 'radio-image',
        'settings'    => 'choose_default_footer',
        'label'       => esc_html__( 'Choose Footer Style', 'suxnix' ),
        'section'     => 'footer_setting',
        'default'     => '5',
        'placeholder' => esc_html__( 'Select an option...', 'suxnix' ),
        'priority'    => 10,
        'multiple'    => 1,
        'choices'     => [
            'footer-style-1'   => get_template_directory_uri() . '/inc/img/footer/footer-1.png',
        ],
        'default'     => 'footer-style-1',
    ];

    $fields[] = [
        'type'        => 'select',
        'settings'    => 'footer_widget_number',
        'label'       => esc_html__( 'Widget Number', 'suxnix' ),
        'section'     => 'footer_setting',
        'default'     => '4',
        'placeholder' => esc_html__( 'Select an option...', 'suxnix' ),
        'priority'    => 10,
        'multiple'    => 1,
        'choices'     => [
            '4' => esc_html__( 'Widget Number 4', 'suxnix' ),
            '3' => esc_html__( 'Widget Number 3', 'suxnix' ),
            '2' => esc_html__( 'Widget Number 2', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'        => 'image',
        'settings'    => 'suxnix_footer_bg',
        'label'       => esc_html__( 'Footer Background Image.', 'suxnix' ),
        'description' => esc_html__( 'Footer Background Image.', 'suxnix' ),
        'section'     => 'footer_setting',
    ];

    $fields[] = [
        'type'        => 'color',
        'settings'    => 'suxnix_footer_bg_color',
        'label'       => __( 'Footer BG Color', 'suxnix' ),
        'description' => esc_html__( 'This is a Footer bg color control.', 'suxnix' ),
        'section'     => 'footer_setting',
        'default'     => '#0A0A0A',
        'priority'    => 10,
    ];

    $fields[] = [
        'type'     => 'switch',
        'settings' => 'suxnix_show_footer_shape',
        'label'    => esc_html__( 'Show Footer Shape', 'suxnix' ),
        'section'  => 'footer_setting',
        'default'  => '0',
        'priority' => 10,
        'choices'  => [
            'on'  => esc_html__( 'Enable', 'suxnix' ),
            'off' => esc_html__( 'Disable', 'suxnix' ),
        ],
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_copyright',
        'label'    => esc_html__( 'CopyRight', 'suxnix' ),
        'section'  => 'footer_setting',
        'default'  => wp_kses_post( 'Copyright © 2022 Suxnix All Rights Reserved.', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'        => 'image',
        'settings'    => 'footer_payment_method_img',
        'label'       => esc_html__( 'Footer Payment Method Card', 'suxnix' ),
        'description' => esc_html__( 'Payment Method Card', 'suxnix' ),
        'section'     => 'footer_setting',
    ];

    return $fields;
}
add_filter( 'kirki/fields', '_header_footer_fields' );

// color
function suxnix_color_fields( $fields ) {
    // Color Settings
    $fields[] = [
        'type'        => 'color',
        'settings'    => 'suxnix_color_option',
        'label'       => __( 'Theme Color', 'suxnix' ),
        'description' => esc_html__( 'This is a Theme color control.', 'suxnix' ),
        'section'     => 'color_setting',
        'default'     => '#2b4eff',
        'priority'    => 10,
    ];
    // Color Settings
    $fields[] = [
        'type'        => 'color',
        'settings'    => 'suxnix_color_option_2',
        'label'       => __( 'Primary Color', 'suxnix' ),
        'description' => esc_html__( 'This is a Primary color control.', 'suxnix' ),
        'section'     => 'color_setting',
        'default'     => '#f2277e',
        'priority'    => 10,
    ];
     // Color Settings
    $fields[] = [
        'type'        => 'color',
        'settings'    => 'suxnix_color_option_3',
        'label'       => __( 'Secondary Color', 'suxnix' ),
        'description' => esc_html__( 'This is a Secondary color control.', 'suxnix' ),
        'section'     => 'color_setting',
        'default'     => '#30a820',
        'priority'    => 10,
    ];
     // Color Settings
    $fields[] = [
        'type'        => 'color',
        'settings'    => 'suxnix_color_option_3_2',
        'label'       => __( 'Secondary Color 2', 'suxnix' ),
        'description' => esc_html__( 'This is a Secondary color 2 control.', 'suxnix' ),
        'section'     => 'color_setting',
        'default'     => '#ffb352',
        'priority'    => 10,
    ];
     // Color Settings
    $fields[] = [
        'type'        => 'color',
        'settings'    => 'suxnix_color_scrollup',
        'label'       => __( 'ScrollUp Color', 'suxnix' ),
        'description' => esc_html__( 'This is a ScrollUp colo control.', 'suxnix' ),
        'section'     => 'color_setting',
        'default'     => '#2b4eff',
        'priority'    => 10,
    ];

    return $fields;
}
add_filter( 'kirki/fields', 'suxnix_color_fields' );

// 404
function suxnix_404_fields( $fields ) {
    // 404 settings
    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_error_text',
        'label'    => esc_html__( '404 Text', 'suxnix' ),
        'section'  => '404_page',
        'default'  => esc_html__( '404', 'suxnix' ),
        'priority' => 10,
    ];
    $fields[] = [
        'type'     => 'textarea',
        'settings' => 'suxnix_error_title',
        'label'    => esc_html__( 'Not Found Title', 'suxnix' ),
        'section'  => '404_page',
        'default'  => esc_html__( 'Sorry, the page you are looking for could not be found', 'suxnix' ),
        'priority' => 10,
    ];
    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_error_link_text',
        'label'    => esc_html__( '404 Link Text', 'suxnix' ),
        'section'  => '404_page',
        'default'  => esc_html__( 'Back To Home', 'suxnix' ),
        'priority' => 10,
    ];
    return $fields;
}
add_filter( 'kirki/fields', 'suxnix_404_fields' );


/**
 * Added Fields
 */
function suxnix_typo_fields( $fields ) {
    // typography settings
    $fields[] = [
        'type'        => 'typography',
        'settings'    => 'typography_body_setting',
        'label'       => esc_html__( 'Body Font', 'suxnix' ),
        'section'     => 'typo_setting',
        'default'     => [
            'font-family'    => '',
            'variant'        => '',
            'font-size'      => '',
            'line-height'    => '',
            'letter-spacing' => '0',
            'color'          => '',
        ],
        'priority'    => 10,
        'transport'   => 'auto',
        'output'      => [
            [
                'element' => 'body',
            ],
        ],
    ];

    $fields[] = [
        'type'        => 'typography',
        'settings'    => 'typography_h_setting',
        'label'       => esc_html__( 'Heading h1 Fonts', 'suxnix' ),
        'section'     => 'typo_setting',
        'default'     => [
            'font-family'    => '',
            'variant'        => '',
            'font-size'      => '',
            'line-height'    => '',
            'letter-spacing' => '0',
            'color'          => '',
        ],
        'priority'    => 10,
        'transport'   => 'auto',
        'output'      => [
            [
                'element' => 'h1',
            ],
        ],
    ];

    $fields[] = [
        'type'        => 'typography',
        'settings'    => 'typography_h2_setting',
        'label'       => esc_html__( 'Heading h2 Fonts', 'suxnix' ),
        'section'     => 'typo_setting',
        'default'     => [
            'font-family'    => '',
            'variant'        => '',
            'font-size'      => '',
            'line-height'    => '',
            'letter-spacing' => '0',
            'color'          => '',
        ],
        'priority'    => 10,
        'transport'   => 'auto',
        'output'      => [
            [
                'element' => 'h2',
            ],
        ],
    ];

    $fields[] = [
        'type'        => 'typography',
        'settings'    => 'typography_h3_setting',
        'label'       => esc_html__( 'Heading h3 Fonts', 'suxnix' ),
        'section'     => 'typo_setting',
        'default'     => [
            'font-family'    => '',
            'variant'        => '',
            'font-size'      => '',
            'line-height'    => '',
            'letter-spacing' => '0',
            'color'          => '',
        ],
        'priority'    => 10,
        'transport'   => 'auto',
        'output'      => [
            [
                'element' => 'h3',
            ],
        ],
    ];

    $fields[] = [
        'type'        => 'typography',
        'settings'    => 'typography_h4_setting',
        'label'       => esc_html__( 'Heading h4 Fonts', 'suxnix' ),
        'section'     => 'typo_setting',
        'default'     => [
            'font-family'    => '',
            'variant'        => '',
            'font-size'      => '',
            'line-height'    => '',
            'letter-spacing' => '0',
            'color'          => '',
        ],
        'priority'    => 10,
        'transport'   => 'auto',
        'output'      => [
            [
                'element' => 'h4',
            ],
        ],
    ];

    $fields[] = [
        'type'        => 'typography',
        'settings'    => 'typography_h5_setting',
        'label'       => esc_html__( 'Heading h5 Fonts', 'suxnix' ),
        'section'     => 'typo_setting',
        'default'     => [
            'font-family'    => '',
            'variant'        => '',
            'font-size'      => '',
            'line-height'    => '',
            'letter-spacing' => '0',
            'color'          => '',
        ],
        'priority'    => 10,
        'transport'   => 'auto',
        'output'      => [
            [
                'element' => 'h5',
            ],
        ],
    ];

    $fields[] = [
        'type'        => 'typography',
        'settings'    => 'typography_h6_setting',
        'label'       => esc_html__( 'Heading h6 Fonts', 'suxnix' ),
        'section'     => 'typo_setting',
        'default'     => [
            'font-family'    => '',
            'variant'        => '',
            'font-size'      => '',
            'line-height'    => '',
            'letter-spacing' => '0',
            'color'          => '',
        ],
        'priority'    => 10,
        'transport'   => 'auto',
        'output'      => [
            [
                'element' => 'h6',
            ],
        ],
    ];
    return $fields;
}

add_filter( 'kirki/fields', 'suxnix_typo_fields' );


/**
 * Added Fields
 */
function suxnix_slug_setting( $fields ) {
    // slug settings
    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_ev_slug',
        'label'    => esc_html__( 'Event Slug', 'suxnix' ),
        'section'  => 'slug_setting',
        'default'  => esc_html__( 'ourevent', 'suxnix' ),
        'priority' => 10,
    ];

    $fields[] = [
        'type'     => 'text',
        'settings' => 'suxnix_port_slug',
        'label'    => esc_html__( 'Portfolio Slug', 'suxnix' ),
        'section'  => 'slug_setting',
        'default'  => esc_html__( 'ourportfolio', 'suxnix' ),
        'priority' => 10,
    ];

    return $fields;
}

add_filter( 'kirki/fields', 'suxnix_slug_setting' );


/**
 * This is a short hand function for getting setting value from customizer
 *
 * @param string $name
 *
 * @return bool|string
 */
function SUXNIX_THEME_option( $name ) {
    $value = '';
    if ( class_exists( 'suxnix' ) ) {
        $value = Kirki::get_option( suxnix_get_theme(), $name );
    }

    return apply_filters( 'SUXNIX_THEME_option', $value, $name );
}

/**
 * Get config ID
 *
 * @return string
 */
function suxnix_get_theme() {
    return 'suxnix';
}