<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package suxnix
 */

/**
 *
 * suxnix Header
 */

function suxnix_check_header() {
    $suxnix_header_style = function_exists( 'get_field' ) ? get_field( 'header_style' ) : NULL;
    $suxnix_default_header_style = get_theme_mod( 'choose_default_header', 'header-style-1' );

    if ( $suxnix_header_style == 'header-style-1' && empty($_GET['s']) ) {
        get_template_part( 'template-parts/header/header-1' );
    }
    elseif ( $suxnix_header_style == 'header-style-2' && empty($_GET['s']) ) {
        get_template_part( 'template-parts/header/header-2' );
    }
    else {

        /** Default Header Style **/
        if ( $suxnix_default_header_style == 'header-style-2' ) {
            get_template_part( 'template-parts/header/header-2' );
        }
        else {
            get_template_part( 'template-parts/header/header-1' );
        }
    }

}
add_action( 'suxnix_header_style', 'suxnix_check_header', 10 );


/**
 * [suxnix_header_lang description]
 * @return [type] [description]
 */
function suxnix_header_lang_default() {
    $suxnix_header_lang = get_theme_mod( 'suxnix_header_lang', false );
    if ( $suxnix_header_lang ): ?>

    <ul>
        <li><a href="javascript:void(0)" class="lang__btn"><?php print esc_html__( 'English', 'suxnix' );?> <i class="fa-light fa-angle-down"></i></a>
        <?php do_action( 'suxnix_language' );?>
        </li>
    </ul>

    <?php endif;?>
<?php
}

/**
 * [suxnix_language_list description]
 * @return [type] [description]
 */
function _suxnix_language( $mar ) {
    return $mar;
}
function suxnix_language_list() {

    $mar = '';
    $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
    if ( !empty( $languages ) ) {
        $mar = '<ul>';
        foreach ( $languages as $lan ) {
            $active = $lan['active'] == 1 ? 'active' : '';
            $mar .= '<li class="' . $active . '"><a href="' . $lan['url'] . '">' . $lan['translated_name'] . '</a></li>';
        }
        $mar .= '</ul>';
    } else {
        //remove this code when send themeforest reviewer team
        $mar .= '<ul>';
        $mar .= '<li><a href="#">' . esc_html__( 'English', 'suxnix' ) . '</a></li>';
        $mar .= '<li><a href="#">' . esc_html__( 'Bangla', 'suxnix' ) . '</a></li>';
        $mar .= '<li><a href="#">' . esc_html__( 'French', 'suxnix' ) . '</a></li>';
        $mar .= ' </ul>';
    }
    print _suxnix_language( $mar );
}
add_action( 'suxnix_language', 'suxnix_language_list' );


// Header Logo
function suxnix_header_logo() { ?>
      <?php
        $suxnix_logo_on = function_exists( 'get_field' ) ? get_field( 'is_enable_sec_logo' ) : NULL;
        $suxnix_logo = get_template_directory_uri() . '/assets/img/logo/logo.png';
        $suxnix_logo_black = get_template_directory_uri() . '/assets/img/logo/secondary_logo.png';

        $suxnix_site_logo = get_theme_mod( 'logo', $suxnix_logo );
        $suxnix_secondary_logo = get_theme_mod( 'secondary_logo', $suxnix_logo_black );
      ?>

      <?php if ( !empty( $suxnix_logo_on ) ) : ?>
         <a class="secondary-logo" href="<?php print esc_url( home_url( '/' ) );?>">
             <img src="<?php print esc_url( $suxnix_secondary_logo );?>" alt="<?php print esc_attr__( 'Logo', 'suxnix' );?>" />
         </a>
      <?php else : ?>
         <a class="main-logo" href="<?php print esc_url( home_url( '/' ) );?>">
             <img src="<?php print esc_url( $suxnix_site_logo );?>" alt="<?php print esc_attr__( 'Logo', 'suxnix' );?>" />
         </a>
      <?php endif; ?>
   <?php
}

// Header Sticky Logo
function suxnix_header_sticky_logo() {?>
    <?php
        $suxnix_logo_black = get_template_directory_uri() . '/assets/img/logo/secondary_logo.png';
        $suxnix_secondary_logo = get_theme_mod( 'secondary_logo', $suxnix_logo_black );
    ?>
      <a class="sticky-logo" href="<?php print esc_url( home_url( '/' ) );?>">
          <img src="<?php print esc_url( $suxnix_secondary_logo );?>" alt="<?php print esc_attr__( 'Logo', 'suxnix' );?>" />
      </a>
    <?php
}

// Mobile Menu Logo
function suxnix_mobile_logo() {

    $mobile_menu_logo = get_template_directory_uri() . '/assets/img/logo/secondary_logo.png';
    $mobile_logo = get_theme_mod('mobile_logo', $mobile_menu_logo);

    ?>

    <a href="<?php print esc_url( home_url( '/' ) ); ?>">
        <img src="<?php print esc_url( $mobile_logo ); ?>" alt="<?php print esc_attr__( 'Logo', 'suxnix' );?>" />
    </a>

<?php }


/**
 * [suxnix_header_social_profiles description]
 * @return [type] [description]
 */
function suxnix_header_social_profiles() {
    $suxnix_header_fb_url = get_theme_mod( 'suxnix_header_fb_url', __( '#', 'suxnix' ) );
    $suxnix_header_twitter_url = get_theme_mod( 'suxnix_header_twitter_url', __( '#', 'suxnix' ) );
    $suxnix_header_linkedin_url = get_theme_mod( 'suxnix_header_linkedin_url', __( '#', 'suxnix' ) );
    ?>
    <ul>
        <?php if ( !empty( $suxnix_header_fb_url ) ): ?>
          <li><a href="<?php print esc_url( $suxnix_header_fb_url );?>"><span><i class="flaticon-facebook"></i></span></a></li>
        <?php endif;?>

        <?php if ( !empty( $suxnix_header_twitter_url ) ): ?>
            <li><a href="<?php print esc_url( $suxnix_header_twitter_url );?>"><span><i class="flaticon-twitter"></i></span></a></li>
        <?php endif;?>

        <?php if ( !empty( $suxnix_header_linkedin_url ) ): ?>
            <li><a href="<?php print esc_url( $suxnix_header_linkedin_url );?>"><span><i class="flaticon-linkedin"></i></span></a></li>
        <?php endif;?>
    </ul>

<?php
}

function suxnix_footer_social_profiles() {
    $suxnix_footer_fb_url = get_theme_mod( 'suxnix_footer_fb_url', __( '#', 'suxnix' ) );
    $suxnix_footer_twitter_url = get_theme_mod( 'suxnix_footer_twitter_url', __( '#', 'suxnix' ) );
    $suxnix_footer_instagram_url = get_theme_mod( 'suxnix_footer_instagram_url', __( '#', 'suxnix' ) );
    $suxnix_footer_linkedin_url = get_theme_mod( 'suxnix_footer_linkedin_url', __( '#', 'suxnix' ) );
    $suxnix_footer_youtube_url = get_theme_mod( 'suxnix_footer_youtube_url', __( '#', 'suxnix' ) );
    ?>

        <ul>
        <?php if ( !empty( $suxnix_footer_fb_url ) ): ?>
            <li>
                <a href="<?php print esc_url( $suxnix_footer_fb_url );?>">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </li>
        <?php endif;?>

        <?php if ( !empty( $suxnix_footer_twitter_url ) ): ?>
            <li>
                <a href="<?php print esc_url( $suxnix_footer_twitter_url );?>">
                    <i class="fab fa-twitter"></i>
                </a>
            </li>
        <?php endif;?>

        <?php if ( !empty( $suxnix_footer_instagram_url ) ): ?>
            <li>
                <a href="<?php print esc_url( $suxnix_footer_instagram_url );?>">
                    <i class="fab fa-instagram"></i>
                </a>
            </li>
        <?php endif;?>

        <?php if ( !empty( $suxnix_footer_linkedin_url ) ): ?>
            <li>
                <a href="<?php print esc_url( $suxnix_footer_linkedin_url );?>">
                    <i class="fab fa-linkedin"></i>
                </a>
            </li>
        <?php endif;?>

        <?php if ( !empty( $suxnix_footer_youtube_url ) ): ?>
            <li>
                <a href="<?php print esc_url( $suxnix_footer_youtube_url );?>">
                    <i class="fab fa-youtube"></i>
                </a>
            </li>
        <?php endif;?>
        </ul>
<?php
}

/**
 * [suxnix_mobile_social_profiles description]
 * @return [type] [description]
 */
function suxnix_mobile_social_profiles() {
    $suxnix_mobile_fb_url           = get_theme_mod('suxnix_mobile_fb_url', __('#','suxnix'));
    $suxnix_mobile_twitter_url      = get_theme_mod('suxnix_mobile_twitter_url', __('#','suxnix'));
    $suxnix_mobile_instagram_url    = get_theme_mod('suxnix_mobile_instagram_url', __('#','suxnix'));
    $suxnix_mobile_linkedin_url     = get_theme_mod('suxnix_mobile_linkedin_url', __('#','suxnix'));
    $suxnix_mobile_youtube_url      = get_theme_mod('suxnix_mobile_youtube_url', __('#','suxnix'));
    ?>

    <ul class="clearfix">
        <?php if (!empty($suxnix_mobile_fb_url)): ?>
        <li class="facebook">
            <a href="<?php print esc_url($suxnix_mobile_fb_url); ?>"><i class="fab fa-facebook-f"></i></a>
        </li>
        <?php endif; ?>

        <?php if (!empty($suxnix_mobile_twitter_url)): ?>
        <li class="twitter">
            <a href="<?php print esc_url($suxnix_mobile_twitter_url); ?>"><i class="fab fa-twitter"></i></a>
        </li>
        <?php endif; ?>

        <?php if (!empty($suxnix_mobile_instagram_url)): ?>
        <li class="instagram">
            <a href="<?php print esc_url($suxnix_mobile_instagram_url); ?>"><i class="fab fa-instagram"></i></a>
        </li>
        <?php endif; ?>

        <?php if (!empty($suxnix_mobile_linkedin_url)): ?>
        <li class="linkedin">
            <a href="<?php print esc_url($suxnix_mobile_linkedin_url); ?>"><i class="fab fa-linkedin-in"></i></a>
        </li>
        <?php endif; ?>

        <?php if (!empty($suxnix_mobile_youtube_url)): ?>
        <li class="youtube">
            <a href="<?php print esc_url($suxnix_mobile_youtube_url); ?>"><i class="fab fa-youtube"></i></a>
        </li>
        <?php endif; ?>
    </ul>

<?php
}


/**
 * [suxnix_header_menu description]
 * @return [type] [description]
 */
function suxnix_header_menu() {
    ?>
    <?php
        wp_nav_menu( [
            'theme_location' => 'main-menu',
            'menu_class'     => 'navigation',
            'container'      => '',
            'fallback_cb'    => 'suxnix_Navwalker_Class::fallback',
            //'walker'         => new suxnix_Navwalker_Class,
        ] );
    ?>
    <?php
}

/**
 * [suxnix_header_menu description]
 * @return [type] [description]
 */
function suxnix_mobile_menu() {
    ?>
    <?php
        $suxnix_menu = wp_nav_menu( [
            'theme_location' => 'main-menu',
            'menu_class'     => 'navigation',
            'container'      => '',
            'fallback_cb'    => false,
            'echo'           => false,
        ] );

    $suxnix_menu = str_replace( "menu-item-has-children", "menu-item-has-children has-children", $suxnix_menu );
        echo wp_kses_post( $suxnix_menu );
    ?>
    <?php
}

/**
 * [suxnix_search_menu description]
 * @return [type] [description]
 */
function suxnix_header_search_menu() {
    ?>
    <?php
        wp_nav_menu( [
            'theme_location' => 'header-search-menu',
            'menu_class'     => '',
            'container'      => '',
            'fallback_cb'    => 'suxnix_Navwalker_Class::fallback',
            'walker'         => new suxnix_Navwalker_Class,
        ] );
    ?>
    <?php
}

/**
 * [suxnix_footer_menu description]
 * @return [type] [description]
 */
function suxnix_footer_menu() {
    wp_nav_menu( [
        'theme_location' => 'footer-menu',
        'menu_class'     => 'm-0',
        'container'      => '',
        'fallback_cb'    => 'suxnix_Navwalker_Class::fallback',
        'walker'         => new suxnix_Navwalker_Class,
    ] );
}


/**
 * [suxnix_category_menu description]
 * @return [type] [description]
 */
function suxnix_category_menu() {
    wp_nav_menu( [
        'theme_location' => 'category-menu',
        'menu_class'     => 'cat-submenu m-0',
        'container'      => '',
        'fallback_cb'    => 'suxnix_Navwalker_Class::fallback',
        'walker'         => new suxnix_Navwalker_Class,
    ] );
}

/**
 *
 * suxnix footer
 */
add_action( 'suxnix_footer_style', 'suxnix_check_footer', 10 );

function suxnix_check_footer() {
    $suxnix_footer_style = function_exists( 'get_field' ) ? get_field( 'footer_style' ) : NULL;
    $suxnix_default_footer_style = get_theme_mod( 'choose_default_footer', 'footer-style-1' );

    get_template_part( 'template-parts/footer/footer-1' );

}

// suxnix_copyright_text
function suxnix_copyright_text() {
   print get_theme_mod( 'suxnix_copyright', wp_kses_post( 'Copyright © 2022 Suxnix All Rights Reserved.', 'suxnix' ) );
}


/**
 *
 * pagination
 */
if ( !function_exists( 'suxnix_pagination' ) ) {

    function _suxnix_pagi_callback( $pagination ) {
        return $pagination;
    }

    //page navegation
    function suxnix_pagination( $prev, $next, $pages, $args ) {
        global $wp_query, $wp_rewrite;
        $menu = '';
        $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

        if ( $pages == '' ) {
            global $wp_query;
            $pages = $wp_query->max_num_pages;

            if ( !$pages ) {
                $pages = 1;
            }

        }

        $pagination = [
            'base'      => add_query_arg( 'paged', '%#%' ),
            'format'    => '',
            'total'     => $pages,
            'current'   => $current,
            'prev_text' => $prev,
            'next_text' => $next,
            'type'      => 'array',
        ];

        //rewrite permalinks
        if ( $wp_rewrite->using_permalinks() ) {
            $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );
        }

        if ( !empty( $wp_query->query_vars['s'] ) ) {
            $pagination['add_args'] = ['s' => get_query_var( 's' )];
        }

        $pagi = '';
        if ( paginate_links( $pagination ) != '' ) {
            $paginations = paginate_links( $pagination );
            $pagi .= '<ul class="pagination">';
            foreach ( $paginations as $key => $pg ) {
                $pagi .= '<li class="page-item">' . $pg . '</li>';
            }
            $pagi .= '</ul>';
        }

        print _suxnix_pagi_callback( $pagi );
    }
}


// header top bg color
function suxnix_breadcrumb_bg_color() {
    $color_code = get_theme_mod( 'suxnix_breadcrumb_bg_color', '#222' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $color_code != '' ) {
        $custom_css = '';
        $custom_css .= ".breadcrumb-bg.gray-bg{ background: " . $color_code . "}";

        wp_add_inline_style( 'suxnix-breadcrumb-bg', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_breadcrumb_bg_color' );

// breadcrumb-spacing top
function suxnix_breadcrumb_spacing() {
    $padding_px = get_theme_mod( 'suxnix_breadcrumb_spacing', '160px' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $padding_px != '' ) {
        $custom_css = '';
        $custom_css .= ".breadcrumb-spacing{ padding-top: " . $padding_px . "}";

        wp_add_inline_style( 'suxnix-breadcrumb-top-spacing', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_breadcrumb_spacing' );

// breadcrumb-spacing bottom
function suxnix_breadcrumb_bottom_spacing() {
    $padding_px = get_theme_mod( 'suxnix_breadcrumb_bottom_spacing', '160px' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $padding_px != '' ) {
        $custom_css = '';
        $custom_css .= ".breadcrumb-spacing{ padding-bottom: " . $padding_px . "}";

        wp_add_inline_style( 'suxnix-breadcrumb-bottom-spacing', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_breadcrumb_bottom_spacing' );

// scrollup
function suxnix_scrollup_switch() {
    $scrollup_switch = get_theme_mod( 'suxnix_scrollup_switch', false );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $scrollup_switch ) {
        $custom_css = '';
        $custom_css .= "#scrollUp{ display: none !important;}";

        wp_add_inline_style( 'suxnix-scrollup-switch', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_scrollup_switch' );

// theme color
function suxnix_custom_color() {
    $color_code = get_theme_mod( 'suxnix_color_option', '#2b4eff' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $color_code != '' ) {
        $custom_css = '';
        $custom_css .= ".demo-class { background-color: " . $color_code . "}";

        $custom_css .= ".demo-class { color: " . $color_code . "}";

        $custom_css .= ".demo-class { border-color: " . $color_code . "}";
        $custom_css .= ".demo-class { border-left-color: " . $color_code . "}";
        $custom_css .= ".demo-class { stroke: " . $color_code . "}";
        $custom_css .= ".demo-class { border-color: " . $color_code . "}";
        wp_add_inline_style( 'suxnix-custom', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_custom_color' );


// theme color
function suxnix_custom_color_primary() {
    $color_code = get_theme_mod( 'suxnix_color_option_2', '#f2277e' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $color_code != '' ) {
        $custom_css = '';
        $custom_css .= ".demo-class { background-color: " . $color_code . "}";

        $custom_css .= ".demo-class { color: " . $color_code . "}";

        $custom_css .= ".demo-class { border-left-color: " . $color_code . "}";
        wp_add_inline_style( 'suxnix-custom', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_custom_color_primary' );

// theme color
function suxnix_custom_color_scrollup() {
    $color_code = get_theme_mod( 'suxnix_color_scrollup', '#2b4eff' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $color_code != '' ) {
        $custom_css = '';
        $custom_css .= ".demo-class { color: " . $color_code . "}";
        $custom_css .= ".demo-class { stroke: " . $color_code . "}";
        wp_add_inline_style( 'suxnix-custom', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_custom_color_scrollup' );

// theme color
function suxnix_custom_color_secondary() {
    $color_code = get_theme_mod( 'suxnix_color_option_3', '#30a820' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $color_code != '' ) {
        $custom_css = '';
        $custom_css .= ".demo-class { background-color: " . $color_code . "}";

        $custom_css .= ".demo-class { color: " . $color_code . "}";

        $custom_css .= ".asdf { border-color: " . $color_code . "}";
        wp_add_inline_style( 'suxnix-custom', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_custom_color_secondary' );

// theme color
function suxnix_custom_color_secondary_2() {
    $color_code = get_theme_mod( 'suxnix_color_option_3_2', '#ffb352' );
    wp_enqueue_style( 'suxnix-custom', SUXNIX_THEME_CSS_DIR . 'suxnix-custom.css', [] );
    if ( $color_code != '' ) {
        $custom_css = '';
        $custom_css .= ".demo-class { background-color: " . $color_code . "}";

        $custom_css .= ".demo-class { color: " . $color_code . "}";

        $custom_css .= ".demo-class { border-color: " . $color_code . "}";
        wp_add_inline_style( 'suxnix-custom', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'suxnix_custom_color_secondary_2' );


// suxnix_kses_intermediate
function suxnix_kses_intermediate( $string = '' ) {
    return wp_kses( $string, suxnix_get_allowed_html_tags( 'intermediate' ) );
}

function suxnix_get_allowed_html_tags( $level = 'basic' ) {
    $allowed_html = [
        'b'      => [],
        'i'      => [],
        'u'      => [],
        'em'     => [],
        'br'     => [],
        'abbr'   => [
            'title' => [],
        ],
        'span'   => [
            'class' => [],
        ],
        'strong' => [],
        'a'      => [
            'href'  => [],
            'title' => [],
            'class' => [],
            'id'    => [],
        ],
    ];

    if ($level === 'intermediate') {
        $allowed_html['a'] = [
            'href' => [],
            'title' => [],
            'class' => [],
            'id' => [],
        ];
        $allowed_html['div'] = [
            'class' => [],
            'id' => [],
        ];
        $allowed_html['img'] = [
            'src' => [],
            'class' => [],
            'alt' => [],
        ];
        $allowed_html['del'] = [
            'class' => [],
        ];
        $allowed_html['ins'] = [
            'class' => [],
        ];
        $allowed_html['bdi'] = [
            'class' => [],
        ];
        $allowed_html['i'] = [
            'class' => [],
            'data-rating-value' => [],
        ];
    }

    return $allowed_html;
}



// WP kses allowed tags
// ----------------------------------------------------------------------------------------
function suxnix_kses($raw){

   $allowed_tags = array(
      'a'                         => array(
         'class'   => array(),
         'href'    => array(),
         'rel'  => array(),
         'title'   => array(),
         'target' => array(),
      ),
      'abbr'                      => array(
         'title' => array(),
      ),
      'b'                         => array(),
      'blockquote'                => array(
         'cite' => array(),
      ),
      'cite'                      => array(
         'title' => array(),
      ),
      'code'                      => array(),
      'del'                    => array(
         'datetime'   => array(),
         'title'      => array(),
      ),
      'dd'                     => array(),
      'div'                    => array(
         'class'   => array(),
         'title'   => array(),
         'style'   => array(),
      ),
      'dl'                     => array(),
      'dt'                     => array(),
      'em'                     => array(),
      'h1'                     => array(),
      'h2'                     => array(),
      'h3'                     => array(),
      'h4'                     => array(),
      'h5'                     => array(),
      'h6'                     => array(),
      'i'                         => array(
         'class' => array(),
      ),
      'img'                    => array(
         'alt'  => array(),
         'class'   => array(),
         'height' => array(),
         'src'  => array(),
         'width'   => array(),
      ),
      'li'                     => array(
         'class' => array(),
      ),
      'ol'                     => array(
         'class' => array(),
      ),
      'p'                         => array(
         'class' => array(),
      ),
      'q'                         => array(
         'cite'    => array(),
         'title'   => array(),
      ),
      'span'                      => array(
         'class'   => array(),
         'title'   => array(),
         'style'   => array(),
      ),
      'iframe'                 => array(
         'width'         => array(),
         'height'     => array(),
         'scrolling'     => array(),
         'frameborder'   => array(),
         'allow'         => array(),
         'src'        => array(),
      ),
      'strike'                 => array(),
      'br'                     => array(),
      'strong'                 => array(),
      'data-wow-duration'            => array(),
      'data-wow-delay'            => array(),
      'data-wallpaper-options'       => array(),
      'data-stellar-background-ratio'   => array(),
      'ul'                     => array(
         'class' => array(),
      ),
      'svg' => array(
           'class' => true,
           'aria-hidden' => true,
           'aria-labelledby' => true,
           'role' => true,
           'xmlns' => true,
           'width' => true,
           'height' => true,
           'viewbox' => true, // <= Must be lower case!
       ),
       'g'     => array( 'fill' => true ),
       'title' => array( 'title' => true ),
       'path'  => array( 'd' => true, 'fill' => true,  ),
      );

   if (function_exists('wp_kses')) { // WP is here
      $allowed = wp_kses($raw, $allowed_tags);
   } else {
      $allowed = $raw;
   }

   return $allowed;
}