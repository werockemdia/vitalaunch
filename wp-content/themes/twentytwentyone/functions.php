<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

// This theme requires WordPress 5.3 or later.
if ( version_compare( $GLOBALS['wp_version'], '5.3', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'twenty_twenty_one_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @return void
	 */
	function twenty_twenty_one_setup() {

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * This theme does not use a hard-coded <title> tag in the document head,
		 * WordPress will provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Add post-formats support.
		 */
		add_theme_support(
			'post-formats',
			array(
				'link',
				'aside',
				'gallery',
				'image',
				'quote',
				'status',
				'video',
				'audio',
				'chat',
			)
		);

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1568, 9999 );

		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary menu', 'twentytwentyone' ),
				'footer'  => esc_html__( 'Secondary menu', 'twentytwentyone' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		/*
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		$logo_width  = 300;
		$logo_height = 100;

		add_theme_support(
			'custom-logo',
			array(
				'height'               => $logo_height,
				'width'                => $logo_width,
				'flex-width'           => true,
				'flex-height'          => true,
				'unlink-homepage-logo' => true,
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );
		$background_color = get_theme_mod( 'background_color', 'D1E4DD' );
		if ( 127 > Twenty_Twenty_One_Custom_Colors::get_relative_luminance_from_hex( $background_color ) ) {
			add_theme_support( 'dark-editor-style' );
		}

		$editor_stylesheet_path = './assets/css/style-editor.css';

		// Note, the is_IE global variable is defined by WordPress and is used
		// to detect if the current browser is internet explorer.
		global $is_IE;
		if ( $is_IE ) {
			$editor_stylesheet_path = './assets/css/ie-editor.css';
		}

		// Enqueue editor styles.
		add_editor_style( $editor_stylesheet_path );

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => esc_html__( 'Extra small', 'twentytwentyone' ),
					'shortName' => esc_html_x( 'XS', 'Font size', 'twentytwentyone' ),
					'size'      => 16,
					'slug'      => 'extra-small',
				),
				array(
					'name'      => esc_html__( 'Small', 'twentytwentyone' ),
					'shortName' => esc_html_x( 'S', 'Font size', 'twentytwentyone' ),
					'size'      => 18,
					'slug'      => 'small',
				),
				array(
					'name'      => esc_html__( 'Normal', 'twentytwentyone' ),
					'shortName' => esc_html_x( 'M', 'Font size', 'twentytwentyone' ),
					'size'      => 20,
					'slug'      => 'normal',
				),
				array(
					'name'      => esc_html__( 'Large', 'twentytwentyone' ),
					'shortName' => esc_html_x( 'L', 'Font size', 'twentytwentyone' ),
					'size'      => 24,
					'slug'      => 'large',
				),
				array(
					'name'      => esc_html__( 'Extra large', 'twentytwentyone' ),
					'shortName' => esc_html_x( 'XL', 'Font size', 'twentytwentyone' ),
					'size'      => 40,
					'slug'      => 'extra-large',
				),
				array(
					'name'      => esc_html__( 'Huge', 'twentytwentyone' ),
					'shortName' => esc_html_x( 'XXL', 'Font size', 'twentytwentyone' ),
					'size'      => 96,
					'slug'      => 'huge',
				),
				array(
					'name'      => esc_html__( 'Gigantic', 'twentytwentyone' ),
					'shortName' => esc_html_x( 'XXXL', 'Font size', 'twentytwentyone' ),
					'size'      => 144,
					'slug'      => 'gigantic',
				),
			)
		);

		// Custom background color.
		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'd1e4dd',
			)
		);

		// Editor color palette.
		$black     = '#000000';
		$dark_gray = '#28303D';
		$gray      = '#39414D';
		$green     = '#D1E4DD';
		$blue      = '#D1DFE4';
		$purple    = '#D1D1E4';
		$red       = '#E4D1D1';
		$orange    = '#E4DAD1';
		$yellow    = '#EEEADD';
		$white     = '#FFFFFF';

		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => esc_html__( 'Black', 'twentytwentyone' ),
					'slug'  => 'black',
					'color' => $black,
				),
				array(
					'name'  => esc_html__( 'Dark gray', 'twentytwentyone' ),
					'slug'  => 'dark-gray',
					'color' => $dark_gray,
				),
				array(
					'name'  => esc_html__( 'Gray', 'twentytwentyone' ),
					'slug'  => 'gray',
					'color' => $gray,
				),
				array(
					'name'  => esc_html__( 'Green', 'twentytwentyone' ),
					'slug'  => 'green',
					'color' => $green,
				),
				array(
					'name'  => esc_html__( 'Blue', 'twentytwentyone' ),
					'slug'  => 'blue',
					'color' => $blue,
				),
				array(
					'name'  => esc_html__( 'Purple', 'twentytwentyone' ),
					'slug'  => 'purple',
					'color' => $purple,
				),
				array(
					'name'  => esc_html__( 'Red', 'twentytwentyone' ),
					'slug'  => 'red',
					'color' => $red,
				),
				array(
					'name'  => esc_html__( 'Orange', 'twentytwentyone' ),
					'slug'  => 'orange',
					'color' => $orange,
				),
				array(
					'name'  => esc_html__( 'Yellow', 'twentytwentyone' ),
					'slug'  => 'yellow',
					'color' => $yellow,
				),
				array(
					'name'  => esc_html__( 'White', 'twentytwentyone' ),
					'slug'  => 'white',
					'color' => $white,
				),
			)
		);

		add_theme_support(
			'editor-gradient-presets',
			array(
				array(
					'name'     => esc_html__( 'Purple to yellow', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $purple . ' 0%, ' . $yellow . ' 100%)',
					'slug'     => 'purple-to-yellow',
				),
				array(
					'name'     => esc_html__( 'Yellow to purple', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $yellow . ' 0%, ' . $purple . ' 100%)',
					'slug'     => 'yellow-to-purple',
				),
				array(
					'name'     => esc_html__( 'Green to yellow', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $green . ' 0%, ' . $yellow . ' 100%)',
					'slug'     => 'green-to-yellow',
				),
				array(
					'name'     => esc_html__( 'Yellow to green', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $yellow . ' 0%, ' . $green . ' 100%)',
					'slug'     => 'yellow-to-green',
				),
				array(
					'name'     => esc_html__( 'Red to yellow', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $red . ' 0%, ' . $yellow . ' 100%)',
					'slug'     => 'red-to-yellow',
				),
				array(
					'name'     => esc_html__( 'Yellow to red', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $yellow . ' 0%, ' . $red . ' 100%)',
					'slug'     => 'yellow-to-red',
				),
				array(
					'name'     => esc_html__( 'Purple to red', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $purple . ' 0%, ' . $red . ' 100%)',
					'slug'     => 'purple-to-red',
				),
				array(
					'name'     => esc_html__( 'Red to purple', 'twentytwentyone' ),
					'gradient' => 'linear-gradient(160deg, ' . $red . ' 0%, ' . $purple . ' 100%)',
					'slug'     => 'red-to-purple',
				),
			)
		);

		/*
		* Adds starter content to highlight the theme on fresh sites.
		* This is done conditionally to avoid loading the starter content on every
		* page load, as it is a one-off operation only needed once in the customizer.
		*/
		if ( is_customize_preview() ) {
			require get_template_directory() . '/inc/starter-content.php';
			add_theme_support( 'starter-content', twenty_twenty_one_get_starter_content() );
		}

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Add support for custom line height controls.
		add_theme_support( 'custom-line-height' );

		// Add support for link color control.
		add_theme_support( 'link-color' );

		// Add support for experimental cover block spacing.
		add_theme_support( 'custom-spacing' );

		// Add support for custom units.
		// This was removed in WordPress 5.6 but is still required to properly support WP 5.5.
		add_theme_support( 'custom-units' );

		// Remove feed icon link from legacy RSS widget.
		add_filter( 'rss_widget_feed_link', '__return_empty_string' );
	}
}
add_action( 'after_setup_theme', 'twenty_twenty_one_setup' );

/**
 * Registers widget area.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 *
 * @return void
 */
function twenty_twenty_one_widgets_init() {

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer', 'twentytwentyone' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'twentytwentyone' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'twenty_twenty_one_widgets_init' );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @global int $content_width Content width.
 *
 * @return void
 */
function twenty_twenty_one_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'twenty_twenty_one_content_width', 750 );
}
add_action( 'after_setup_theme', 'twenty_twenty_one_content_width', 0 );

/**
 * Enqueues scripts and styles.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @global bool       $is_IE
 * @global WP_Scripts $wp_scripts
 *
 * @return void
 */
function twenty_twenty_one_scripts() {
	// Note, the is_IE global variable is defined by WordPress and is used
	// to detect if the current browser is internet explorer.
	global $is_IE, $wp_scripts;
	if ( $is_IE ) {
		// If IE 11 or below, use a flattened stylesheet with static values replacing CSS Variables.
		wp_enqueue_style( 'twenty-twenty-one-style', get_template_directory_uri() . '/assets/css/ie.css', array(), wp_get_theme()->get( 'Version' ) );
	} else {
		// If not IE, use the standard stylesheet.
		wp_enqueue_style( 'twenty-twenty-one-style', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );
	}

	// RTL styles.
	wp_style_add_data( 'twenty-twenty-one-style', 'rtl', 'replace' );

	// Print styles.
	wp_enqueue_style( 'twenty-twenty-one-print-style', get_template_directory_uri() . '/assets/css/print.css', array(), wp_get_theme()->get( 'Version' ), 'print' );

	// Threaded comment reply styles.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Register the IE11 polyfill file.
	wp_register_script(
		'twenty-twenty-one-ie11-polyfills-asset',
		get_template_directory_uri() . '/assets/js/polyfills.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		array( 'in_footer' => true )
	);

	// Register the IE11 polyfill loader.
	wp_register_script(
		'twenty-twenty-one-ie11-polyfills',
		null,
		array(),
		wp_get_theme()->get( 'Version' ),
		array( 'in_footer' => true )
	);
	wp_add_inline_script(
		'twenty-twenty-one-ie11-polyfills',
		wp_get_script_polyfill(
			$wp_scripts,
			array(
				'Element.prototype.matches && Element.prototype.closest && window.NodeList && NodeList.prototype.forEach' => 'twenty-twenty-one-ie11-polyfills-asset',
			)
		)
	);

	// Main navigation scripts.
	if ( has_nav_menu( 'primary' ) ) {
		wp_enqueue_script(
			'twenty-twenty-one-primary-navigation-script',
			get_template_directory_uri() . '/assets/js/primary-navigation.js',
			array( 'twenty-twenty-one-ie11-polyfills' ),
			wp_get_theme()->get( 'Version' ),
			array(
				'in_footer' => false, // Because involves header.
				'strategy'  => 'defer',
			)
		);
	}

	// Responsive embeds script.
	wp_enqueue_script(
		'twenty-twenty-one-responsive-embeds-script',
		get_template_directory_uri() . '/assets/js/responsive-embeds.js',
		array( 'twenty-twenty-one-ie11-polyfills' ),
		wp_get_theme()->get( 'Version' ),
		array( 'in_footer' => true )
	);
}
add_action( 'wp_enqueue_scripts', 'twenty_twenty_one_scripts' );

/**
 * Enqueues block editor script.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @return void
 */
function twentytwentyone_block_editor_script() {

	wp_enqueue_script( 'twentytwentyone-editor', get_theme_file_uri( '/assets/js/editor.js' ), array( 'wp-blocks', 'wp-dom' ), wp_get_theme()->get( 'Version' ), array( 'in_footer' => true ) );
}

add_action( 'enqueue_block_editor_assets', 'twentytwentyone_block_editor_script' );

/**
 * Fixes skip link focus in IE11.
 *
 * This does not enqueue the script because it is tiny and because it is only for IE11,
 * thus it does not warrant having an entire dedicated blocking script being loaded.
 *
 * @since Twenty Twenty-One 1.0
 * @deprecated Twenty Twenty-One 1.9 Removed from wp_print_footer_scripts action.
 *
 * @link https://git.io/vWdr2
 */
function twenty_twenty_one_skip_link_focus_fix() {

	// If SCRIPT_DEBUG is defined and true, print the unminified file.
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		echo '<script>';
		include get_template_directory() . '/assets/js/skip-link-focus-fix.js';
		echo '</script>';
	} else {
		// The following is minified via `npx terser --compress --mangle -- assets/js/skip-link-focus-fix.js`.
		?>
		<script>
		/(trident|msie)/i.test(navigator.userAgent)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",(function(){var t,e=location.hash.substring(1);/^[A-z0-9_-]+$/.test(e)&&(t=document.getElementById(e))&&(/^(?:a|select|input|button|textarea)$/i.test(t.tagName)||(t.tabIndex=-1),t.focus())}),!1);
		</script>
		<?php
	}
}

/**
 * Enqueues non-latin language styles.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @return void
 */
function twenty_twenty_one_non_latin_languages() {
	$custom_css = twenty_twenty_one_get_non_latin_css( 'front-end' );

	if ( $custom_css ) {
		wp_add_inline_style( 'twenty-twenty-one-style', $custom_css );
	}
}
add_action( 'wp_enqueue_scripts', 'twenty_twenty_one_non_latin_languages' );

// SVG Icons class.
require get_template_directory() . '/classes/class-twenty-twenty-one-svg-icons.php';

// Custom color classes.
require get_template_directory() . '/classes/class-twenty-twenty-one-custom-colors.php';
new Twenty_Twenty_One_Custom_Colors();

// Enhance the theme by hooking into WordPress.
require get_template_directory() . '/inc/template-functions.php';

// Menu functions and filters.
require get_template_directory() . '/inc/menu-functions.php';

// Custom template tags for the theme.
require get_template_directory() . '/inc/template-tags.php';

// Customizer additions.
require get_template_directory() . '/classes/class-twenty-twenty-one-customize.php';
new Twenty_Twenty_One_Customize();

// Block Patterns.
require get_template_directory() . '/inc/block-patterns.php';

// Block Styles.
require get_template_directory() . '/inc/block-styles.php';

// Dark Mode.
require_once get_template_directory() . '/classes/class-twenty-twenty-one-dark-mode.php';
new Twenty_Twenty_One_Dark_Mode();

/**
 * Enqueues scripts for the customizer preview.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @return void
 */
function twentytwentyone_customize_preview_init() {
	wp_enqueue_script(
		'twentytwentyone-customize-helpers',
		get_theme_file_uri( '/assets/js/customize-helpers.js' ),
		array(),
		wp_get_theme()->get( 'Version' ),
		array( 'in_footer' => true )
	);

	wp_enqueue_script(
		'twentytwentyone-customize-preview',
		get_theme_file_uri( '/assets/js/customize-preview.js' ),
		array( 'customize-preview', 'customize-selective-refresh', 'jquery', 'twentytwentyone-customize-helpers' ),
		wp_get_theme()->get( 'Version' ),
		array( 'in_footer' => true )
	);
}
add_action( 'customize_preview_init', 'twentytwentyone_customize_preview_init' );

/**
 * Enqueues scripts for the customizer.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @return void
 */
function twentytwentyone_customize_controls_enqueue_scripts() {

	wp_enqueue_script(
		'twentytwentyone-customize-helpers',
		get_theme_file_uri( '/assets/js/customize-helpers.js' ),
		array(),
		wp_get_theme()->get( 'Version' ),
		array( 'in_footer' => true )
	);
}
add_action( 'customize_controls_enqueue_scripts', 'twentytwentyone_customize_controls_enqueue_scripts' );

/**
 * Calculates classes for the main <html> element.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @return void
 */
function twentytwentyone_the_html_classes() {
	/**
	 * Filters the classes for the main <html> element.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @param string The list of classes. Default empty string.
	 */
	$classes = apply_filters( 'twentytwentyone_html_classes', '' );
	if ( ! $classes ) {
		return;
	}
	echo 'class="' . esc_attr( $classes ) . '"';
}

/**
 * Adds "is-IE" class to body if the user is on Internet Explorer.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @return void
 */
function twentytwentyone_add_ie_class() {
	?>
	<script>
	if ( -1 !== navigator.userAgent.indexOf( 'MSIE' ) || -1 !== navigator.appVersion.indexOf( 'Trident/' ) ) {
		document.body.classList.add( 'is-IE' );
	}
	</script>
	<?php
}
add_action( 'wp_footer', 'twentytwentyone_add_ie_class' );

if ( ! function_exists( 'wp_get_list_item_separator' ) ) :
	/**
	 * Retrieves the list item separator based on the locale.
	 *
	 * Added for backward compatibility to support pre-6.0.0 WordPress versions.
	 *
	 * @since 6.0.0
	 */
	function wp_get_list_item_separator() {
		/* translators: Used between list items, there is a space after the comma. */
		return __( ', ', 'twentytwentyone' );
	}
endif;


function pr_disable_admin_notices() {
        global $wp_filter;
            if ( is_user_admin() ) {
                if ( isset( $wp_filter['user_admin_notices'] ) ) {
                                unset( $wp_filter['user_admin_notices'] );
                }
            } elseif ( isset( $wp_filter['admin_notices'] ) ) {
                        unset( $wp_filter['admin_notices'] );
            }
            if ( isset( $wp_filter['all_admin_notices'] ) ) {
                        unset( $wp_filter['all_admin_notices'] );
            }
    }
add_action( 'admin_print_scripts', 'pr_disable_admin_notices' );

// admin_init action works better than admin_menu in modern wordpress (at least v5+)
add_action( 'admin_init', 'my_remove_menu_pages' );
function my_remove_menu_pages() {

   remove_menu_page('edit.php?post_type=sa_slider'); //sa slider
   remove_menu_page('tinvwl'); // ti wishlist
   remove_menu_page('eael-settings'); //essential addons
   //remove_menu_page('tools.php'); // tools
   remove_menu_page('wp-mail-smtp'); // wp-mail-smtp
   remove_menu_page('mailchimp-for-wp'); // mailchimp for wp
   remove_menu_page('ns-cloner'); //ns-cloner
   remove_menu_page('ns-cloner'); //ns-cloner
   remove_menu_page('wc-admin&path=/payments/connect'); // payments
   remove_menu_page('wc-admin&path=/analytics/overview'); // analaytics
   remove_menu_page('wc-admin&path=/marketing'); // marketing
}

add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );

function remove_tab($tabs){
   //unset($tabs['general']); // it is to remove general tab
     unset($tabs['inventory']); // it is to remove inventory tab
    //unset($tabs['advanced']); // it is to remove advanced tab
    //unset($tabs['linked_product']); // it is to remove linked_product tab
    //unset($tabs['attribute']); // it is to remove attribute tab
    //unset($tabs['variations']); // it is to remove variations tab
    return($tabs);
}
add_filter('woocommerce_product_data_tabs', 'remove_tab', 10, 1);

function custom_text_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'In stock' :
            $translated_text = 'Label inventory';
            break;
         case 'Stock' :
            $translated_text = 'Label inventory';
            break;
        case 'Out of stock' :
            $translated_text = 'Out of Label inventory';
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'custom_text_strings', 20, 3 );
global $current_user;
if ($current_user->role[0]!=='administrator'){
add_filter( 'manage_edit-product_columns', 'bbloomer_admin_products_visibility_column', 9999 );
 
function bbloomer_admin_products_visibility_column( $columns ){
   $columns['labelinventory'] = 'Label inventory';
   return $columns;
}
 
add_action( 'manage_product_posts_custom_column', 'bbloomer_admin_products_visibility_column_content', 10, 2 );
 
function bbloomer_admin_products_visibility_column_content( $column, $product_id ){
    global $wpdb;
    if ( $column == 'labelinventory' ) {
        $user_id = get_current_user_id();
        $prefixcout =  get_user_meta( $user_id, 'primary_blog', true );
        $tblprefix = $wpdb->prefix.'postmeta';
        $product = wc_get_product( $product_id );
        $sku = $product->get_sku();
        $product_post_id = $wpdb->get_results("SELECT * FROM $tblprefix WHERE  `meta_key` LIKE 'labels_$sku'   LIMIT  1 ");
        if(!empty($product_post_id )){
        echo 'Label inventory<b>('.$product_post_id[0]->meta_value.')</b>';
        }else{
            echo 'Label inventory<b>(0)</b>';
        }
    }
}


add_filter( 'manage_edit-product_columns', 'bbloomer_admin_products_product_inventory_column', 9999 );
 
function bbloomer_admin_products_product_inventory_column( $columns ){
   $columns['productinventory'] = 'Product inventory';
   return $columns;
}
 
add_action( 'manage_product_posts_custom_column', 'bbloomer_admin_products_product_inventory_column_content', 10, 2 );
 
function bbloomer_admin_products_product_inventory_column_content( $column, $product_id ){
    global $wpdb;
    if ( $column == 'productinventory' ) {
        
        $product = wc_get_product( $product_id );
        $sku = $product->get_sku();
        $stock_quantity = $product->get_stock_quantity();
        if($stock_quantity == 0){
            echo 'Out of Stock';
        }
        else if($stock_quantity > 0 && $stock_quantity < 10 ){
            echo 'Low Stock';
        }else{
            echo 'In Stock';
        }
        
        
    }
}

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    .wp-list-table.posts #is_in_stock, .manage-column.column-is_in_stock {
	    display: none;
    }
    .wp-list-table.posts .is_in_stock.column-is_in_stock {
	    display: none;
    }
    #labelinventory {
	width: 10%;
}
.tablenav .actions select option:nth-child(3) {
	display: none;
}
#productinventory {
	width: 15%;
}
.woocommerce-billing-fields__field-wrapper input, .create-account input {
	border: 1px solid #ccc !important;
	border-radius: 10px;
}
.notice.notice-success.is-dismissible.jpum-notice {
	display: none;
}
#dashboard-widgets-wrap, .welcome-panel-column-container, .welcome-panel-header-image p, #welcome-panel a, .welcome-panel .welcome-panel-column-container {
	display: none;
}
.welcome-panel-content {
	height: auto !important;
	min-height: auto;
}
#menu-dashboard .wp-submenu.wp-submenu-wrap, #menu-comments, #toplevel_page_woocommerce ul.wp-submenu.wp-submenu-wrap li:nth-child(4),  #toplevel_page_woocommerce ul.wp-submenu.wp-submenu-wrap li:nth-child(6),  #toplevel_page_woocommerce ul.wp-submenu.wp-submenu-wrap li:nth-child(7),  #toplevel_page_woocommerce ul.wp-submenu.wp-submenu-wrap li:nth-child(10), #toplevel_page_woocommerce-marketing, #menu-tools ul.wp-submenu.wp-submenu-wrap li:nth-child(2), #menu-tools ul.wp-submenu.wp-submenu-wrap li:nth-child(3), #menu-tools ul.wp-submenu.wp-submenu-wrap li:nth-child(4), #menu-tools ul.wp-submenu.wp-submenu-wrap li:nth-child(5), #wp-admin-bar-my-sites, #wp-admin-bar-updraft_admin_node{
	display: none;
}
ul#adminmenu:first-child ,  #adminmenuback, #adminmenuwrap {
	width: 250px ;
	background-color: #F3F4F7;
	padding: 2px;
}
#wpcontent, #wpfooter {
	margin-left: 250px;
}
ul#adminmenu:first-child li {
	background-color: #fff;
	margin-top: 10px;
	border-radius: 6px;
}
ul#adminmenu:first-child li a {
	color: #000;
}
ul#adminmenu:first-child div.wp-menu-image::before {
	color: #000 !important;
	color: rgba(240,246,252,.6);
}
ul#adminmenu:first-child .current  div.wp-menu-image::before {
	color: #fff !important;
	color: rgba(240,246,252,.6);
}
#wpcontent #wpbody {
	width: 98%;
	float: right;
}
ul#adminmenu:first-child .wp-menu-open {
	background-color: #0d9b4d !important;
	border-radius: 6px;
}
ul#adminmenu:first-child .wp-menu-open div.wp-menu-image::before {
	color: #fff !important;
	color: rgba(240,246,252,.6);
}

ul#adminmenu:first-child li.menu-top:hover, ul#adminmenu:first-child li.opensub > a.menu-top, ul#adminmenu:first-child li > a.menu-top:focus {

	background-color: #0d9b4d !important;
	color: #fff;
}
ul#adminmenu:first-child li.menu-top:hover .wp-menu-image::before {
	color: #fff !important;
}
ul#adminmenu:first-child .wp-submenu {
	left: 250px;
	background-color: #F3F4F7;
	padding-right: 10px;
	padding-bottom: 16px;
	border-radius: 4px;
	padding-left: 16px;
}
ul#adminmenu:first-child .wp-submenu a:focus, ul#adminmenu:first-child .wp-submenu a:hover, ul#adminmenu:first-child a:hover, ul#adminmenu:first-child li.menu-top  a:focus, ul#adminmenu:first-child li.current a.menu-top {
	color: #fff;
	background-color: #0d9b4d;
	border-radius: 5px;
	padding: ;
}
ul#adminmenu:first-child li:nth-child(2){
    display: none;
}
ul#adminmenu:first-child .wp-menu-separator {
	display: none !important;
}
ul#adminmenu:first-child li.wp-has-submenu.wp-not-current-submenu:focus-within::after, ul#adminmenu:first-child #menu-appearance:hover:active:focus-within{
    color: #fff;
}
.wp-submenu.wp-submenu-wrap .current {
	color: #000 !important;
}
.wp-submenu.wp-submenu-wrap .current:hover {
	color: #fff !important;
}
.wp-menu-open .wp-submenu.wp-submenu-wrap {
	left: 0 !important;
	width: 227px !important;
}
#adminmenu li.wp-has-submenu.wp-not-current-submenu:focus-within::after {
  border-right-color: #fff !important;
}
  </style>';
}
}

add_action( 'woocommerce_thankyou', 'pfwp_redirect_woo_checkoutt');
function pfwp_redirect_woo_checkoutt( $order_id ){
    $order = wc_get_order( $order_id );
    if ( ! $order_id ){
        return;
    }
    global $wpdb;
    $order = wc_get_order( $order_id );
    $items = $order->get_items();
    foreach ( $items as $item ) {
    $product_name = $item->get_name();
    $product_id = $item->get_product_id();
    $product_variation_id = $item->get_variation_id();
    $product = wc_get_product( $product_id );
    $sku = $product->get_sku();
    }
    $last_order = $wpdb->get_results("SELECT post_id FROM `wp_postmeta` ORDER BY meta_id DESC LIMIT 1");
        
    $last_order = $last_order[0]->post_id;
    
    $sku_key =  get_post_meta( $product_id, 'sku_key', true );
    global $wpdb;
            
             //$wpdb->prepare("INSERT INTO 'wp_postmeta'( 'post_id', 'meta_key', 'meta_value') VALUES ($post_id,'sku_key', $sku_key)");
             

            //get my table
            $tableUsers = 'wp_postmeta';
            
            $post_id = $last_order;
            $meta_key = 'sku_key_';
            $meta_value = $sku_key;
            
            $data = array( 
                'post_id'   => $post_id,
                'meta_key' => $meta_key,
                'meta_value'  => $meta_value,
            );
            $format = array( '%s', '%s', '%s', '%d' );
            $stmt = $wpdb->insert( $tableUsers, $data, $format );
            
    // Allow code execution only once 
    if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
        
        global $wpdb;
       $m_value = $_SESSION['prod_det'];
      //try code 
      $user_id = get_current_user_id();
        $prefixcout =  get_user_meta( $user_id, 'primary_blog', true );
        
// try code

        // Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );
        
        $order_status  = $order->get_status();
       $_stripe_flag = get_post_meta($order_id, '_stripe_flag', true);
       if(empty($_stripe_flag)){
        //echo "<pre>";
        //print_r($order);

        // Get the order key
        $order_key = $order->get_order_key();

        // Get the order number
        $order_key = $order->get_order_number();
        $SERVER_NAME = $_SERVER['SERVER_NAME'];
        $blog = $wpdb->get_results("SELECT * FROM `wp_blogs` WHERE `domain` LIKE '".$SERVER_NAME."'"); 
        
        $blog_meta_value =  $blog[0]->blog_id;
        $blog_meta_domain =  $blog[0]->domain;
        
        $get_users = get_users( array( 'blog_id' => $blog_meta_value ) );
        $users_id = $get_users[0]->ID;
        
        $customer = get_userdata($users_id);
        
        $orders_meta = $wpdb->get_results("SELECT * FROM vendor_details   WHERE vendor_id = $users_id ORDER BY ID DESC LIMIT 1 ");
        
        //print_r($orders_meta);
        //exit();
        
        $order_shipping = $order->shipping_total;
        
        $sku_val = $orders_meta[0]->sku;
        
        $orders_sku = $wpdb->get_results("SELECT * FROM `wp_postmeta` WHERE `meta_key` LIKE '_sku' AND `meta_value` LIKE '$sku_val'");
        
        
        $pro_pric = $orders_sku[0]->post_id;
        
        $main_product_price = $wpdb->get_results("SELECT * FROM `wp_postmeta` WHERE `post_id` = $pro_pric AND `meta_key` LIKE '_price'");

        
        $price_main = $main_product_price[0]->meta_value;

        //global $product;
        //$product = wc_get_product( $orders_sku[0]->post_id );   
        //echo $price = $product->get_price();
        //print_r();
        
        $server_name = explode('.',$SERVER_NAME);
        $count = count($server_name);
        
        $orders_meta_value =  $orders_meta[0]->vendor_cus_key;
        
        $orders_price =  $price_main + $order_shipping;
        
        
        $users = get_userdata($users_id);
        $users_ = $users->data->display_name;
        
        if($count == 3){
            
       /*
        require_once '/home/vitalaunch/public_html/stripe/vendor/autoload.php';
 
        /*$stripe = new \Stripe\StripeClient('sk_test_51MAqEySDzHJSF4TRKj90JrElt4c6novaqZ4a9BIPnZtLpxrxYp0b5rf7ZTyASgjGkC2dvFboyTjfkXLJ23RBhNYf00v1u6cfKG');
        $stripe->paymentIntents->create([
          'amount' => $orders_price*100,
          'currency' => 'usd',
           'customer'=> $orders_meta_value,
        //   'payment_method' => 'pi_3Ok1rUSDzHJSF4TR06s7aIDd', 
           'description' => $users_,
          'automatic_payment_methods' => ['enabled' => true],
        ]);*/
        //$stripe = new \Stripe\StripeClient('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');
       /* $stripe = new \Stripe\StripeClient('sk_test_51OXCHXK7WPEozkdwijMMxihFx7j0DpfXRhp6Nx4A9uve9Kwcm1ygFpLSyKjUGKHKjl0JmuTBbNXCJhWWNLaqQRoN0004ri4Qaq');
        
        $tst = $stripe->paymentMethods->all([
              'type' => 'card',
              'limit' => 3,
              'customer' => $orders_meta_value,
            ]);
             //print_r($tst);
            $cardid = $tst->data[0]->id;
            
            $tstt = $stripe->paymentIntents->create([
              'amount' => $orders_price*100,
              'currency' => 'usd',
               'customer'=> $orders_meta_value,
              // 'payment_method_types' => ['us_bank_account'],  
              // 'payment_method_options' => ['us_bank_account'],
               
                'payment_method'=> $cardid,
                'off_session'=> true,
                 'confirm'=> true,
                'description'=> $blog_meta_domain .' Vendore Changed',
              'automatic_payment_methods' => ['enabled' => true],
            ]);
            
            $pi_id = $tstt->id;

            $data = $stripe->paymentIntents->retrieve($pi_id, []);
            
            $chargedamount = $data->amount;
            $status_payment = $data->status;
        */
        }
        
        //$orders_price*100
        update_post_meta($order_id, 'vendor_charged_amount', $chargedamount);
        update_post_meta($order_id, 'status_payment_changed', $status_payment);
        update_post_meta($order_id, 'vendor_website', $blog_meta_domain);
        
        
        
       $items_count = count( $order->get_items() );
        // Loop through order items
        $i = 0;
        $items = $order->get_items();
        //foreach ( $order->get_items() as $item_id => $item ) {
        foreach ( $items as $item ) {
       
            // Get the product object
            $product = $item->get_product();

            // Get the product Id
           $product_id = $product->get_id().'<br>';

            // Get the product name
            $product_id = $item->get_name();
            $item_sku = $product->get_sku();
            $item_quantity = $item->get_quantity();
            global $wpdb;
            
            $data_store = WC_Data_Store::load( 'order-item' );
	        $data_store->get_order_id_by_order_item_id( $item_id );
            
            $id = wc_get_product( $product->get_id() );
            //$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
            //$img_id =  $product->get_image_id();
            //$uri_path = wp_get_attachment_image_url( $img_id );
            //$uri_segments = explode('/', $uri_path);
            
            //$userids = $uri_segments[6];
           $tblprefix = $wpdb->postmeta;
            //$m_value = $_SESSION['prod_det'];
            
        
           
           // if($m_value == '_bulk'){
            $product_ids = $product->get_id();  
           // $item_sku;
            $product_post_id = $wpdb->get_results("SELECT * FROM $tblprefix WHERE ( meta_key = 'label_$item_sku')");
            
           $current_meta_value = $product_post_id[0]->meta_value;
            $total_qty_meta = $current_meta_value-$item_quantity;  
            
            
            if($total_qty_meta >= 0){
               
             for($i = 0; $i < 1; $i++){  
                 if ($i == 0) {
            //$wpdb->query($wpdb->prepare("UPDATE `wp_63_postmeta` SET `meta_value` = '".$total_qty_meta."' WHERE `meta_key` = 'label_P6' LIMIT  1"));
            $wpdb->update( $tblprefix, array( 'meta_value' => $total_qty_meta),array('meta_key'=>'label_'.$item_sku));
             $wpdb->update( $tblprefix, array( 'meta_value' => $total_qty_meta),array('meta_key'=>'labels_'.$item_sku));
             
                 }
             }
            }
          $i++;  
        }
       }
        update_post_meta($order_id, '_stripe_flag', 1);
       
    }
    
}

function ts_redirect_login( $redirect) {
    return '/shop';
}
add_filter( 'woocommerce_login_redirect', 'ts_redirect_login' );


add_action('wp_head','hook_custom_css');
function hook_custom_css()
{
    $outputcss='
        <style>
            .woocommerce-cart button.wc-block-cart-item__remove-link {
   
    			right: 0% !important; 
		}
	.wp-block-woocommerce-checkout.alignwide.wc-block-checkout {
    		margin: auto;
	}
	.wc-block-grid__product{
		border: 2px solid #ddd !important;
    		border-radius: 30px;
    		padding: 20px !important;
   		margin: 20px 8px !important;
    	
    		flex: 23% !important;
    		
	}
	.wc-block-grid__product-price.price,.wp-block-button.wc-block-grid__product-add-to-cart {
    text-align: left;
}
	.related.products ul.products li.product{
		border: 2px solid #ddd !important;
    		border-radius: 30px;
    		padding: 20px !important;
   		margin: 20px 8px !important;
    	
	}
	.related.products ul.products li.product {
    
    background: #ffffff;
}
.wc-block-grid__product-title,
.woocommerce section.related.products span.woocommerce-Price-amount.amount
,.wc-block-grid__product-price.price span.woocommerce-Price-amount.amount,
.woocommerce section.related.products ul.products li.product h2,
.woocommerce-shop ul.products li h2.woocommerce-loop-product__title{
    font-family: "Oswald", sans-serif !important;
    color: #222222 !important;
    margin-bottom: 1rem !important;
    font-weight: 500 !important;
    font-size: 20px !important;
    text-transform: uppercase;
    text-decoration: none;
    line-height: 1.3;
    text-align:left;
}
.input-text.qty.text {
    padding: 10px !important;
    line-height: 21px;
    border: 1px solid;
    border-radius: 4px;
}
a.wc-block-components-checkout-return-to-cart-button ,section.related.products ul.products li.product .button{
    background: #000;
    color: #fff;
    padding: 5px 15px;
    border-radius: 30px;
    
    text-align: center;
    font-size: 16px;
    font-weight: 400;
}
button.single_add_to_cart_button.button.alt {
	border: 1px solid #000;
    	background: #000;
display: inline-flex;
    align-items: center;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    padding: 12px 16px;
    min-width: 145px;
    justify-content: center;
    margin-bottom: 12px;
    margin-right: 12px;
    text-transform: uppercase;
}
.wc-block-grid__products .wc-block-grid__product-image img{
    height: 175px;
    object-fit: cover;
    width: 90px;
    margin: 0 auto;
}
.wc-block-grid__product .add_to_cart_button:hover,.wp-block-button:not(.is-style-outline) .wp-block-button__link:hover{
    background: #fcbe85 !important;
    color:#ffffff !important;
}
 .add_to_cart_button:hover,.product_type_simple:hover{
    background: #fcbe85 !important;
    color:#ffffff !important;  
 }
 .single_add_to_cart_button:hover{
    background: #fcbe85 !important;
    color:#ffffff !important;
    border-color: #fcbe85 !important;
 }
.tinv-wraper.tinv-wishlist {
    font-size: 100%;
    display: none;
}
.wp-block-button.wc-block-grid__product-add-to-cart a.wp-block-button__link.add_to_cart_button,a.added_to_cart.wc-forward {
    background: #000;
    color: #fff !important;
    padding: 5px 15px;
    border-radius: 30px;
    font-size: 16px;
    text-transform: capitalize;
    text-decoration: none;
    margin: 0px !important;
}
a.button.product_type_simple.add_to_cart_button.added {
    display: none;
}
p.stock.in-stock {
    margin-bottom: 10px;
}
.product_meta > span {
    display: block;
}
a{
    text-decoration:none;
}
.woocommerce-account article header.entry-header.alignwide{
   border-bottom: none !important;
}
.woocommerce-account article h1.entry-title,.woocommerce-page .entry-header{
    display:none;
}
.woocommerce-account article .entry-content .woocommerce{
    max-width: 95% !important;
}
.logged-in.woocommerce-account article .entry-content .woocommerce {
    width: 85% !important;
}

form.woocommerce-form.woocommerce-form-login.login, form.woocommerce-ResetPassword.lost_reset_password{
    width:100% !important;
    padding: 0px !important;
}
#customer_login h2 ,h2.woocommerce-order-details__title{
    color: #000000;
    font-family: "Oswald", Sans-serif;
    font-size: 30px !important;
    font-weight: 600;
    margin-bottom: 15px;
}
p.woocommerce-form-row.form-row:last-child {
    margin-top: 20px;
}
p.woocommerce-form-row.woocommerce-form-row--wide.form-row.form-row-wide {
    margin-bottom: 15px;
}
.wc-block-components-quantity-selector>.wc-block-components-quantity-selector__button--minus {
    border-radius: 4px 0 0 4px;
    order: 1;
    background: transparent !important;
    color: #000000 !important;
    border-right: 1px solid #333 !important;
}
.wc-block-components-quantity-selector>.wc-block-components-quantity-selector__button--plus{
    border-radius: 4px 0 0 4px;
    order: 1;
    background: transparent !important;
    color: #000000 !important;
    border-left: 1px solid #333 !important;
}
.wc-block-components-quantity-selector>.wc-block-components-quantity-selector__button--plus:focus,.wc-block-components-quantity-selector>.wc-block-components-quantity-selector__button--minus:focus{
   box-shadow:none !important; 
}
.woocommerce-order-received .woocommerce {
    max-width: 80% !important;
    margin: 0px;
}
.woocommerce-order-overview {
    margin-bottom: 2rem;
    list-style: none;
    padding: 0px;
    background: #FCBE85;
    margin-bottom: 2rem;
    list-style: none;
    padding: 19px;
    display: flex;
    justify-content: space-between;
}
.woocommerce-order-received table tr {
    height: 2rem;
}
section.woocommerce-order-details {
    padding: 20px;
    background: #F3F4F7;
    margin-top: 20px;
}
p.woocommerce-notice.woocommerce-notice--success{
    background: #F3F4F7;
    padding: 10px;
    font-size: 18px;
}
.woocommerce-order-overview {
    margin-bottom: 2rem;
    list-style: none;
    padding: 19px;
}
.woocommerce-order-overview li{
    font-size:16px;
}
.wc-block-components-quantity-selector input.wc-block-components-quantity-selector__input{
    font-size:16px !important;
}
.wc-block-components-quantity-selector input.wc-block-components-quantity-selector__input:focus{
    box-shadow:none !important;
}
div#customer_login button.woocommerce-button.button{
    background:#000 !important;
    color:#fff !important;
        border: none !important;
}
h1.woocommerce-products-header__title.page-title{
    display:none;
}
p.woocommerce-result-count {
    background: #f5f5f5;
    padding: 5px 20px !important;
    color: #000;
    font-weight: 500;
    border-radius: 5px;
    font-size: 18px;
}
form.woocommerce-ordering select{
    font-size:18px;
}
p.stock.in-stock {
    margin-bottom: 10px;
    font-size: 18px;
}
.product_meta > span {
    font-weight: bold;
}
.product_meta > span>* {
    font-weight: normal;
}
.wc-block-cart .wc-block-cart__submit-container {
    background: #000;
    border-radius: 50px;
}
.wc-block-cart .wc-block-cart__submit-container a:focus{
    box-shadow: none !important;
    outline: none !important;
}
button:focus,a:focus{
    outline:none !important;
}
button.wc-block-components-checkout-place-order-button:focus {
    box-shadow: none !important;
}

.entry-summary p.stock.out-of-stock {
    width: 36%;
}

.woocommerce-customer-details h2.woocommerce-column__title {
    color: #000000;
    font-family: "Oswald", sans-serif !important;
    font-size: 30px !important;
    font-weight: 600;
    margin-bottom: 15px;
}

.woocommerce-customer-details {
    padding: 20px !important;
    background: #F3F4F7;
    margin-top: 20px;
}
.woocommerce-checkout .entry-content{
  background: #f7f7f7;
    padding: 80px 0px;
}

.wc-block-components-title.wc-block-components-title{
  font-family: "Oswald", sans-serif !important;
}
.woocommerce-account .woocommerce-MyAccount-navigation li:hover {
    background: #0d9b4d;
    color: #ffffff;
	transition: all 0.3s ease-out 0s;
}

.woocommerce-account .woocommerce-MyAccount-navigation li.is-active {
    background: #0d9b4d !important;
    color: #fff;
}
.woocommerce-account article .woocommerce nav.woocommerce-MyAccount-navigation ul li a {
    font-size:16px !important;
    display: block;
}
.woocommerce-MyAccount-content a{
    color:#FAA432;
}
.woocommerce-account .woocommerce-MyAccount-content table.account-orders-table .button, 
.woocommerce-MyAccount-content .woocommerce_account_subscriptions a.button,
.woocommerce-MyAccount-content form.woocommerce-EditAccountForm.edit-account button.woocommerce-Button.button{
   background: #0d9b4d !important;
}
.woocommerce-account .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address-title h3 {
    display: block;
    
    margin-top: 0;
    text-transform: uppercase;
}
.woocommerce-account .woocommerce-EditAccountForm #account_display_name+span {
    font-size: 16px;
}
.woocommerce-MyAccount-content form.woocommerce-EditAccountForm.edit-account label{
    font-size: 16px !important;
}
.woocommerce-account .woocommerce-MyAccount-content .woocommerce-Addresses address {
    line-height: 1.8rem;
    font-style: normal;
}

@media only screen and (max-width:1024px){
    .elementor-1173 .elementor-element.elementor-element-3ef2fb44{
        padding:30px;
    }
    .elementor-1173 .e-con-inner .elementor-element.e-con-full.e-flex.e-con.e-child {
    margin-bottom: 40px;
}
}
@media only screen and (min-width:769px) and (max-width:990px){
    .related.products ul.products li.product{
        min-height:500px;
    }
    .wc-block-grid__product {
    
    flex: 50% !important;
    max-width: 48% !important;
}
}
@media only screen and (max-width:768px){
.wc-block-grid__product {
    
    flex: 47% !important;
    max-width: 47% !important;
}
    .woocommerce .related.products ul.products, .woocommerce-page .related.products ul.products {
    
    flex-direction: row;
    
}
.related.products ul.products li.product,.woocommerce .related.products ul.products[class*=columns-] li.product, .woocommerce-page .related.products ul.products[class*=columns-] li.product {
  
    padding: 20px !important;
    
}
.woocommerce-order-overview{
    display:block;
}
}
@media only screen and (max-width:767px){
  .wc-block-grid__product {
    
    flex: 100% !important;
    max-width:100% !important;
}  
}

    </style>';
    echo $outputcss;
     
}

add_action( 'admin_head', 'load_admin_style' );
function load_admin_style() {
?>
<style>
.wp-list-table #sku_key {
	width: 100px;
}
table.wp-list-table .column-sku {
	width: 19%;
}
.inline-edit-wrapper .stock_fields {
	display: none !important;
}
.inline-edit-group.manage_stock_field, #menu-posts, #menu-media, #toplevel_page_wpcf7, #toplevel_page_elementor, #menu-posts-elementor_library, #menu-plugins, #menu-tools, #toplevel_page_edit-post_type-acf-field-group  {
	display: none !important;
}

</style>
<?php
}
function wpdocs_pingbackurl_example() {
		if ( is_shop() || is_single() ) {
		    global $wpdb;
           $table = $wpdb->prefix.'posts';
           $table_meta = $wpdb->prefix.'postmeta';
           $product_post_ids = $wpdb->get_results("SELECT * FROM $table WHERE post_type = 'product'");
           
            foreach ($product_post_ids as $product_post_id) {
             $product_post_id = $product_post_id->ID; 
             
            $product_post_id_class =    '.post-'.$product_post_id ;      
            $product_post_meta = $wpdb->get_results("SELECT * FROM $table_meta WHERE post_id = '".$product_post_id."' AND meta_key  = '_sku'");
            $product_post_meta_sku = $product_post_meta[0]->meta_value;
            $product_post_meta_label = $wpdb->get_results("SELECT * FROM $table_meta WHERE  `meta_key` LIKE 'labels_$product_post_meta_sku' ");
           
            $product_post_meta_label_inventory = $product_post_meta_label[0]->meta_value;
            if(empty($product_post_meta_label_inventory)){
            if($product_post_meta_label_inventory <= 0){
                ?>
                <script>
			jQuery(document).ready(function(){
			    
			   var classs = '<?php echo  $product_post_id_class; ?>';
			   jQuery(classs +" .woocommerce-loop-product__link").append("out of stock");
               jQuery(classs +" .ajax_add_to_cart").css("display", "none");
               jQuery(classs +" .quantity").append("<p>out of stock</p>");
               jQuery(classs +" .single_add_to_cart_button").css("display", "none");
               jQuery(classs +" .stock.in-stock").css("display", "none");
               
                //jQuery(classs).remove();
              
            });
            </script>
                <?php
            }
            }
            } ?>
		    <script>
		        jQuery(document).ready(function(){
		        var len = jQuery("ul.products  li").length;
		        if(len < 9){
		            jQuery(".page-numbers").css("display", "none");
		            jQuery(".woocommerce-result-count").css("display", "none");
		        }
		        });
		        </script>
	<?php	}
	}
add_action( 'wp_head', 'wpdocs_pingbackurl_example' );
remove_action('load-update-core.php','wp_update_plugins');
add_filter('pre_site_transient_update_plugins','__return_null');

function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','remove_core_updates');
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');

add_filter( 'manage_woocommerce_page_wc-orders_columns', 'add_wc_order_list_custom_column' );
function add_wc_order_list_custom_column( $columns ) {
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;

        if( $key ===  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['my-column1'] = __( 'Tracking ID','theme_domain');
            //$reordered_columns['my-column2'] = __( 'Title2','theme_domain');
        }
    }
    return $reordered_columns;
}

add_action('manage_woocommerce_page_wc-orders_custom_column', 'display_wc_order_list_custom_column_content', 10, 2);
function display_wc_order_list_custom_column_content( $column, $order ){
    switch ( $column )
    {
        case 'my-column1' :
            // Get custom order metadata
            
            $value = $order->get_id();
            if ( ! empty($value) ) {
                
                // Get order notes
                $order_notes = wc_get_order_notes( array(
                    'order_id'  => $value,
                    'order_by'  => 'date_created',
                    'order'     => 'ASC',
                ));
                // Notes is NOT empty
                if ( ! empty( $order_notes ) ) {
                    foreach ( $order_notes as $order_note ) {
                        // PHP 8
                        if ( str_contains( $order_note->content, 'tracking number' ) ) {
                            $track = $order_note->content;
                        }
                    }
                }
                
                $tracking = explode(' ',$track);
                $trackingnumber = end($tracking);
                if ( ! empty($trackingnumber) ) {
                    echo $trackingnumber;
                }else{
                    echo "Not Generated";
                }
            }
            // For testing (to be removed) - Empty value case
            else {
                echo '<small>(<em>no value</em>)</small>';
            }
            break;

        /*case 'my-column2' :
            // Get custom order metadata
            $value = $order->get_meta('_the_meta_key2');
            if ( ! empty($value) ) {
                echo $value;
            }
            // For testing (to be removed) - Empty value case
            else {
                echo '<small>(<em>no value</em>)</small>';
            }
            break;*/
    }
}
function cloudways_display_order_data_in_admin( $order ){  ?>
        <div class="order_data_column">
            <h4><?php _e( ' Label Image', 'woocommerce' ); ?></h4>
            <div class="address">
            <?php
                global $wpdb;
                $order_id = $order->id;
                $item_sku = array();
                  $order = wc_get_order( $order_id ); 
                
                  foreach ($order->get_items() as $item) {
                    $product = wc_get_product($item->get_product_id());
                    $item_sku[] = $product->get_sku();
                  }
                
                $sku = $item_sku[0];
               $user_id = get_current_user_id();
                $post_ids = $wpdb->get_results("SELECT * FROM `marge_img` WHERE `user_id` = '".$user_id."' AND `sku` = '".$sku."' ORDER BY ID DESC LIMIT 1 ");
              
                foreach($post_ids as $post_id){
                    $img = $post_id->image_id;
                    //$img_url = get_post_meta($order_id, 'marge_img', TRUE);
                    $imgurldesktop = wp_get_attachment_image_url( $img)
                    ?>
                    <img src = "<?php echo $imgurldesktop; ?> " style="width:200px;"/>
                    <a href="<?php echo $imgurldesktop; ?> " style="width:200px;" target="_blank">Preview</a>
            <?php } ?>
            </div>
            
        </div>
    <?php }
    add_action( 'woocommerce_admin_order_data_after_order_details', 'cloudways_display_order_data_in_admin' );
    
    
    // Add new column(s) to the "My Orders" table in the account.
function filter_woocommerce_account_orders_columns( $columns ) {
    $columns['custom-column'] = __( 'New Column 1', 'woocommerce' );
    $columns['custom-column2'] = __( 'New Column 2', 'woocommerce' );

    return $columns;
}
add_filter( 'woocommerce_account_orders_columns', 'filter_woocommerce_account_orders_columns', 10, 1 );


    // Display Fields
    add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
    // Save Fields
    add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
    function woocommerce_product_custom_fields()
    {
        global $woocommerce, $post;
        echo '<div class="product_custom_field">';
        // Custom Product Text Field
        $post_id = $post->ID;
        $producthidevalue  = get_post_meta($post_id, 'product_label_show', TRUE);
        $sku_key  = get_post_meta($post_id, 'sku_key', TRUE);
        ?><p>Product Label Show:</p> 
		
        <p class="form-field _custom_product_text_field_field ">
		
		
        
        <label for="html">Product show in Frontend</label>
        <input type="radio" id="yes" name="product_label" value="yes" <?php if($producthidevalue=="yes"){ echo 'checked'; } ?> ><br>
        
        <label for="html">Product hide in Frontend</label>
        <input type="radio" id="no" name="product_label" value="no" <?php if($producthidevalue=="no"){ echo 'checked'; } ?>>
        
        </p>
        <p> Sku Key:<br>
        <label for="html">Sku Key</label>
       
        <input type="text" id="sky_key" name="sku_key" value="<?php echo $sku_key; ?>">
        </p>
        <?php
        echo '</div>';
    }
    
        function woocommerce_product_custom_fields_save($post_id)
    {
        // Custom Product Text Field
        $woocommerce_custom_product_text_field = $_POST['product_label'];
        $sku_key = $_POST['sku_key'];
        if (!empty($woocommerce_custom_product_text_field)){
            update_post_meta($post_id, 'product_label_show', esc_attr($woocommerce_custom_product_text_field));
        }
        if (!empty($sku_key)){
            update_post_meta($post_id, 'sku_key', esc_attr($sku_key));
            
            
        }
   
    }
// Add this code to your theme functions.php file or a custom plugin
add_filter( 'woocommerce_shipstation_export_custom_field_2', 'shipstation_custom_field_2' );
 
function shipstation_custom_field_2() {
	return 'sku_key'; // Replace this with the key of your custom field
}
 
// This is for custom field 3
add_filter( 'woocommerce_shipstation_export_custom_field_3', 'shipstation_custom_field_3' );
 
function shipstation_custom_field_3() {
	return 'sku_key'; // Replace this with the key of your custom field
}
function custom_pre_get_posts_query( $q ) {

        // Do your cart logic here

        // Get ids of products which you want to hide
        
        $producthidevaluearray=array();
        $args = array( 'post_type' => 'product', 'posts_per_page' => -1);
        $the_query = new WP_Query( $args ); 
        if ( $the_query->have_posts() ) : 
            while ( $the_query->have_posts() ) : $the_query->the_post(); 
            $post_id = get_the_ID();
            $producthidevalue  = get_post_meta($post_id, 'product_label_show', TRUE);
            
            if($producthidevalue=='no'){
                $p_ids = get_the_ID();
                array_push($producthidevaluearray, $p_ids);
            }
            endwhile;
        wp_reset_postdata();
        else:  
        endif; 
       
        $q->set( 'post__not_in', $producthidevaluearray );

    }
    add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );
    
    // replace WordPress Howdy in WordPress 3.3
function replace_howdy( $wp_admin_bar ) {
   $my_account=$wp_admin_bar->get_node('my-account');
   $newtitle = str_replace( 'Howdy,', 'Logged in as', $my_account->title );                 $wp_admin_bar->add_node( array(
 'id' => 'my-account',
       'title' => $newtitle,
   ) );
}
add_filter( 'admin_bar_menu', 'replace_howdy',25 );
function custom_welcome_message( $translated_text, $text, $domain ) {
    if ( $text === 'Welcome to WordPress!' ) {
        $translated_text = 'Welcome to Vitalaunch';
    }
    return $translated_text;
}
add_filter( 'gettext', 'custom_welcome_message', 20, 3 );
add_filter( 'gettext', 'change_woocommerce_menu_text', 20, 3 );
function change_woocommerce_menu_text( $translated_text, $text, $domain ) {
    if ( is_admin() ) {
        switch ( $translated_text ) {
            case 'WooCommerce':
                $translated_text = __( 'E-Commerce', 'woocommerce' );
                break;
            // Add more cases for other menu items if needed
        }
    }
    return $translated_text;
}
add_filter( 'site_transient_update_plugins', 'disable_plugin_updates' );
function disable_plugin_updates( $value ) {
    if ( isset( $value ) && is_object( $value ) ) {
        unset( $value->response );
    }
    return $value;
}
add_filter( 'site_transient_update_themes', 'disable_theme_updates' );
function disable_theme_updates( $value ) {
    if ( isset( $value ) && is_object( $value ) ) {
        $value->response = array();
    }
    return $value;
}
/*
// Add a new widget to the dashboard using a custom function
function wpmudev_add_dashboard_widgets() {
	wp_add_dashboard_widget(
		'wpmudev_dashboard_widget', // Widget slug
		'My Custom Dashboard Widget', // Widget title
		'wpmudev_new_dashboard_widget_function' // Function name to display the widget
	);
}
// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action( 'wp_dashboard_setup', 'wpmudev_add_dashboard_widgets' );

// Initialize the function to output the contents of your new dashboard widget
function wpmudev_new_dashboard_widget_function() {
	echo do_shortcode('[woocommerce_reports]');
}
*/

add_filter( 'manage_edit-product_columns', 'bbloomer_admin_products_product_inventory_column_sku_key', 9999 );
 
function bbloomer_admin_products_product_inventory_column_sku_key( $columns ){
   $columns['sku_key'] = 'SKU Key';
   //return $columns;
   return array_slice( $columns, 0, 4, true ) + array( 'sku_key' => 'SKU Key' ) + array_slice( $columns, 4, count( $columns ) - 4, true );

}
 
add_action( 'manage_product_posts_custom_column', 'bbloomer_admin_products_product_inventory_column_content_sku_key', 10, 2 );
 
function bbloomer_admin_products_product_inventory_column_content_sku_key( $column, $product_id ){
    global $wpdb;
    if ( $column == 'sku_key' ) {
        
        $product = wc_get_product( $product_id );
        echo $sku_key  = get_post_meta($product_id, 'sku_key', TRUE);
        
        
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'add_csku_on_orders_and_emails', 2, 4 );
function add_csku_on_orders_and_emails( $item, $cart_item_key, $values, $order ) {
        
        $sku_key =  get_post_meta( $item->get_product_id(), 'sku_key', true );
        $item->add_meta_data( 'sku_key', $sku_key ); // add it as custom order item meta data
    
}
