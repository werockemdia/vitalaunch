<?php

   /**
    * Template part for displaying header side information
    *
    * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
    *
    * @package suxnix
   */

    $suxnix_side_hide = get_theme_mod( 'suxnix_side_hide', false );
    $suxnix_extra_title = get_theme_mod( 'suxnix_extra_title', __( 'Getting all of the <span>Nutrients</span> you need simply cannot be done without supplements.', 'suxnix' ) );
    $suxnix_extra_about_text = get_theme_mod( 'suxnix_extra_about_text', __( 'Nam libero tempore, cum soluta nobis eligendi cumque quod placeat facere possimus assumenda omnis dolor repellendu sautem temporibus officiis', 'suxnix' ) );

    $suxnix_contact_number = get_theme_mod( 'suxnix_contact_number', __( '+1 599 162 4545', 'suxnix' ) );
    $suxnix_contact_mail = get_theme_mod( 'suxnix_contact_mail', __( 'suxnix@gmail.com', 'suxnix' ) );
    $suxnix_office_address = get_theme_mod( 'suxnix_office_address', __( '5689 Lotaso Terrace, Culver City, <br> CA, United States', 'suxnix' ) );

    $suxnix_sidebar_fb_url = get_theme_mod( 'suxnix_sidebar_fb_url', __( '#', 'suxnix' ) );
    $suxnix_sidebar_twitter_url = get_theme_mod( 'suxnix_sidebar_twitter_url', __( '#', 'suxnix' ) );
    $suxnix_sidebar_instagram_url = get_theme_mod( 'suxnix_sidebar_instagram_url', __( '#', 'suxnix' ) );
?>


<!-- offCanvas-start -->
<?php if (!empty( $suxnix_side_hide )) : ?>
<div class="offCanvas-wrap">
    <div class="offCanvas-toggle">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/close.png" alt="icon">
    </div>
    <div class="offCanvas-body">
        <div class="offCanvas-content">
            <h3 class="title"><?php echo wp_kses_post( $suxnix_extra_title ) ?></h3>
            <p><?php echo esc_html( $suxnix_extra_about_text ) ?></p>
        </div>
        <div class="offcanvas-contact">
            <h4 class="number"><?php echo esc_html( $suxnix_contact_number ) ?></h4>
            <h4 class="email"><?php echo esc_html( $suxnix_contact_mail ) ?></h4>
            <p><?php echo wp_kses_post( $suxnix_office_address ) ?></p>

            <ul class="offcanvas-social list-wrap">
                <?php if ( !empty( $suxnix_sidebar_fb_url ) ): ?>
                <li><a href="<?php print esc_url( $suxnix_sidebar_fb_url );?>"><i class="fab fa-facebook-f"></i></a></li>
                <?php endif;?>

                <?php if ( !empty( $suxnix_sidebar_twitter_url ) ): ?>
                    <li><a href="<?php print esc_url( $suxnix_sidebar_twitter_url );?>"><i class="fab fa-twitter"></i></a></li>
                <?php endif;?>

                <?php if ( !empty( $suxnix_sidebar_instagram_url ) ): ?>
                    <li><a href="<?php print esc_url( $suxnix_sidebar_instagram_url );?>"><i class="fab fa-instagram"></i></a></li>
                <?php endif;?>
            </ul>

        </div>
    </div>
</div>
<div class="offCanvas-overlay"></div>
<?php endif; ?>
<!-- offCanvas-end -->