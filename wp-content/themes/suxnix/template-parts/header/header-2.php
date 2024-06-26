<?php

	/**
	* Template part for displaying header layout one
	*
	* @link https://developer.wordpress.org/themes/basics/template-hierarchy/
	*
	* @package suxnix
	*/

    // Header Right
    $suxnix_show_header_right = get_theme_mod( 'suxnix_show_header_right', false );
    $suxnix_show_heder_search = get_theme_mod( 'suxnix_show_heder_search', false );
    $suxnix_show_hamburger_btn = get_theme_mod( 'suxnix_show_hamburger_btn', false );
    $suxnix_show_mobile_social = get_theme_mod( 'suxnix_show_mobile_social', true );

    $suxnix_menu_width = $suxnix_show_header_right ? 'navbar-wrap main-menu d-none d-xl-flex' : 'navbar-wrap main-menu menu-right d-none d-xl-flex';

?>


<!-- header-area -->
<header id="home">
    <div id="header-top-fixed"></div>
    <div id="sticky-header" class="menu-area">
        <div class="container custom-container">
            <div class="row">
                <div class="col-12">
                    <div class="mobile-nav-toggler"><i class="flaticon-layout"></i></div>
                    <div class="menu-wrap">
                        <nav class="menu-nav">
                            <div class="logo">
                                <?php suxnix_header_sticky_logo(); ?>
                            </div>
                            <div class="<?php echo esc_attr($suxnix_menu_width) ?>">
                                <?php suxnix_header_menu(); ?>
                            </div>
                            <?php if ( !empty($suxnix_show_header_right) ) : ?>
                            <div class="header-action d-none d-sm-block">
                                <ul>

                                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                    <li class="header-shop-cart">
                                        <a href="<?php echo wc_get_cart_url(); ?>" class="cart-count"><i class="flaticon-shopping-cart"></i>
                                            <span id="tp-cart-item" class="mini-cart-count"><?php echo esc_html(WC()->cart->cart_contents_count); ?></span>
                                        </a>
                                        <div class="header-mini-cart">
                                            <?php woocommerce_mini_cart(); ?>
                                        </div>
                                    </li>
                                    <?php endif; ?>

                                    <?php if ( !empty($suxnix_show_heder_search) ) : ?>
                                        <li class="header-search"><a href="#"><i class="flaticon-search"></i></a></li>
                                    <?php endif; ?>

                                    <?php if ( !empty($suxnix_show_hamburger_btn) ) : ?>
                                        <li class="offCanvas-btn d-none d-xl-block"><a href="#" class="navSidebar-button"><i class="flaticon-layout"></i></a>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </div>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <!-- Mobile Menu  -->
                    <div class="mobile-menu">
                        <nav class="menu-box">
                            <div class="close-btn"><i class="fas fa-times"></i></div>
                            <div class="nav-logo">
                                <?php suxnix_mobile_logo(); ?>
                            </div>

                            <div class="menu-outer">
                               <?php suxnix_mobile_menu(); ?>
                            </div>

                            <?php if (!empty( $suxnix_show_mobile_social )) : ?>
                            <div class="social-links">
                                <?php suxnix_mobile_social_profiles(); ?>
                            </div>
                            <?php endif; ?>

                        </nav>
                    </div>
                    <div class="menu-backdrop"></div>
                    <!-- End Mobile Menu -->
                </div>
            </div>
        </div>
    </div>

    <!-- header-search -->
    <div class="search-popup-wrap" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="search-wrap text-center">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="search-form">
                            <form method="get" action="<?php print esc_url(home_url('/')); ?>">
                                <input type="text" name="s" value="<?php print esc_attr( get_search_query() ) ?>" placeholder="<?php print esc_attr__('Enter your keyword...', 'suxnix'); ?>">
                                <button class="search-btn"><i class="flaticon-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="search-backdrop"></div>
    <!-- header-search-end -->

</header>
<!-- header-area-end -->

<?php get_template_part( 'template-parts/header/header-side-info' ); ?>