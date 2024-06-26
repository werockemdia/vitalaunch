<?php

/**
 * suxnix functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package suxnix
 */

if ( !function_exists( 'suxnix_setup' ) ):
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function suxnix_setup() {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on suxnix, use a find and replace
         * to change 'suxnix' to the name of your theme in all the template files.
         */
        load_theme_textdomain( 'suxnix', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( [
            'main-menu' => esc_html__( 'Main Menu', 'suxnix' ),
            'footer-menu' => esc_html__( 'Footer Menu', 'suxnix' ),
        ] );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support( 'html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ] );

        // Set up the WordPress core custom background feature.
        add_theme_support( 'custom-background', apply_filters( 'suxnix_custom_background_args', [
            'default-color' => 'ffffff',
            'default-image' => '',
        ] ) );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        //Enable custom header
        add_theme_support( 'custom-header' );

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        add_theme_support( 'custom-logo', [
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        ] );

        /**
         * Enable suporrt for Post Formats
         *
         * see: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support( 'post-formats', [
            'image',
            'audio',
            'video',
            'gallery',
            'quote',
        ] );

        // Add support for Block Styles.
        add_theme_support( 'wp-block-styles' );

        // Add support for full and wide align images.
        add_theme_support( 'align-wide' );

        // Add support for editor styles.
        add_theme_support( 'editor-styles' );

        // Add support for responsive embedded content.
        add_theme_support( 'responsive-embeds' );

        remove_theme_support( 'widgets-block-editor' );

        // Add support for WooCommerce.
        add_theme_support( 'woocommerce', array(
            'thumbnail_image_width' => 350,
            'gallery_thumbnail_image_width' => 350,
            'single_image_width' => 650,
        ));

        // Remove WooCommerce Default styles
        add_filter( 'woocommerce_enqueue_styles', '__return_false' );

        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );

        add_image_size( 'suxnix-case-details', 1170, 600, [ 'center', 'center' ] );
    }
endif;
add_action( 'after_setup_theme', 'suxnix_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function suxnix_content_width() {
    // This variable is intended to be overruled from themes.
    // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $GLOBALS['content_width'] = apply_filters( 'suxnix_content_width', 640 );
}
add_action( 'after_setup_theme', 'suxnix_content_width', 0 );



/**
 * Enqueue scripts and styles.
 */

define( 'SUXNIX_THEME_DIR', get_template_directory() );
define( 'SUXNIX_THEME_URI', get_template_directory_uri() );
define( 'SUXNIX_THEME_CSS_DIR', SUXNIX_THEME_URI . '/assets/css/' );
define( 'SUXNIX_THEME_JS_DIR', SUXNIX_THEME_URI . '/assets/js/' );
define( 'SUXNIX_THEME_INC', SUXNIX_THEME_DIR . '/inc/' );



// wp_body_open
if ( !function_exists( 'wp_body_open' ) ) {
    function wp_body_open() {
        do_action( 'wp_body_open' );
    }
}

/**
 * Implement the Custom Header feature.
 */
require SUXNIX_THEME_INC . 'custom-header.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require SUXNIX_THEME_INC . 'template-functions.php';

/**
 * Custom template helper function for this theme.
 */
require SUXNIX_THEME_INC . 'template-helper.php';

/**
 * initialize kirki customizer class.
 */
include_once SUXNIX_THEME_INC . 'kirki-customizer.php';
include_once SUXNIX_THEME_INC . 'class-suxnix-kirki.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
    require SUXNIX_THEME_INC . 'jetpack.php';
}


/**
 * include suxnix functions file
 */
require_once SUXNIX_THEME_INC . 'class-navwalker.php';
require_once SUXNIX_THEME_INC . 'class-tgm-plugin-activation.php';
require_once SUXNIX_THEME_INC . 'add_plugin.php';
require_once SUXNIX_THEME_INC . '/common/suxnix-breadcrumb.php';
require_once SUXNIX_THEME_INC . '/common/suxnix-scripts.php';
require_once SUXNIX_THEME_INC . '/common/suxnix-widgets.php';
if ( class_exists( 'WooCommerce' ) ) {
    require_once SUXNIX_THEME_INC . '/woocommerce/tp-woocommerce.php';
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function suxnix_pingback_header() {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'suxnix_pingback_header' );

// change textarea position in comment form
// ----------------------------------------------------------------------------------------
function suxnix_move_comment_textarea_to_bottom( $fields ) {
    $comment_field       = $fields[ 'comment' ];
    unset( $fields[ 'comment' ] );
    $fields[ 'comment' ] = $comment_field;
    return $fields;
}
add_filter( 'comment_form_fields', 'suxnix_move_comment_textarea_to_bottom' );


// suxnix_comment
if ( !function_exists( 'suxnix_comment' ) ) {
    function suxnix_comment( $comment, $args, $depth ) {
        $GLOBAL['comment'] = $comment;
        extract( $args, EXTR_SKIP );
        $args['reply_text'] = 'Reply';
        $replayClass = 'comment-depth-' . esc_attr( $depth );
        ?>
            <li id="comment-<?php comment_ID();?>">

                <div class="comments-box">

                    <?php if( get_comment_type($comment) === 'comment' ):?>
                    <div class="comments-avatar">
                        <?php print get_avatar( $comment, 110, null, null, [ 'class' => [] ] );?>
                    </div>
                    <?php endif; ?>

                    <div class="comment-text">
                        <div class="avatar-name mb-10">
                            <h6 class="name">
                                <?php print get_comment_author_link();?>
                                <?php comment_reply_link( array_merge($args,
                                    array(
                                        'reply_text' => __('<i class="fas fa-reply"></i> Reply', 'suxnix'),
                                        'depth'      => $depth,
                                        'max_depth'  => $args['max_depth']
                                    )
                                )); ?>
                            </h6>
                            <span class="date"><?php comment_time( get_option( 'date_format' ) );?></span>
                        </div>

                        <?php comment_text();?>

                    </div>
                </div>
        <?php
    }
}


/**
 * shortcode supports for removing extra p, spance etc
 *
 */
add_filter( 'the_content', 'suxnix_shortcode_extra_content_remove' );
/**
 * Filters the content to remove any extra paragraph or break tags
 * caused by shortcodes.
 *
 * @since 1.0.0
 *
 * @param string $content  String of HTML content.
 * @return string $content Amended string of HTML content.
 */
function suxnix_shortcode_extra_content_remove( $content ) {

    $array = [
        '<p>['    => '[',
        ']</p>'   => ']',
        ']<br />' => ']',
    ];
    return strtr( $content, $array );

}

// suxnix_search_filter_form
if ( !function_exists( 'suxnix_search_filter_form' ) ) {
    function suxnix_search_filter_form( $form ) {

        $form = sprintf(
            '<div class="sidebar-search-form position-relative"><form action="%s" method="get">
      	<input type="text" value="%s" required name="s" placeholder="%s">
      	<button type="submit"> <i class="fas fa-search"></i>  </button>
		</form></div>',
            esc_url( home_url( '/' ) ),
            esc_attr( get_search_query() ),
            esc_html__( 'Search', 'suxnix' )
        );

        return $form;
    }
    add_filter( 'get_search_form', 'suxnix_search_filter_form' );
}

add_action( 'admin_enqueue_scripts', 'suxnix_admin_custom_scripts' );

function suxnix_admin_custom_scripts() {
    wp_enqueue_media();
    wp_enqueue_style( 'customizer-style', get_template_directory_uri() . '/inc/css/customizer-style.css',array());
    wp_register_script( 'suxnix-admin-custom', get_template_directory_uri() . '/inc/js/admin_custom.js', [ 'jquery' ], '', true );
    wp_enqueue_script( 'suxnix-admin-custom' );
}


// Archive count on rightside
function suxnix_archive_count_on_rightside($links) {
    $links = str_replace('</a>&nbsp;(', '</a> <span class="float-right">', $links);
    $links = str_replace(')', '</span>', $links);
    return $links;
}
add_filter( 'get_archives_link', 'suxnix_archive_count_on_rightside' );


// Categories count on rightside
function suxnix_categories_count_on_rightside($links) {
	// For WooCommerce
	$links = str_replace('<span class="count">(', '</a> <span class="float-right">', $links);
	// For blog
	$links = str_replace('</a> (', '</a> <span class="float-right">', $links);
    $links = str_replace(')', '</span>', $links);
    return $links;
}
add_filter( 'wp_list_categories', 'suxnix_categories_count_on_rightside',10,2 );
