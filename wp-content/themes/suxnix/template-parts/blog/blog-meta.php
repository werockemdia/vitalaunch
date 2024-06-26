<?php

/**
 * Template part for displaying post meta
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package suxnix
 */

$categories = get_the_terms( $post->ID, 'category' );
$suxnix_blog_date = get_theme_mod( 'suxnix_blog_date', true );
$suxnix_blog_comments = get_theme_mod( 'suxnix_blog_comments', true );
$suxnix_blog_author = get_theme_mod( 'suxnix_blog_author', true );
$suxnix_blog_cat = get_theme_mod( 'suxnix_blog_cat', false );

?>

<div class="blog--post--meta mb-20">
    <ul class="list-wrap">

        <?php if ( !empty($suxnix_blog_date) ): ?>
            <li><i class="far fa-calendar-alt"></i> <?php the_time( get_option('date_format') ); ?></li>
        <?php endif;?>

        <?php if ( !empty($suxnix_blog_cat) ): ?>
            <?php if ( !empty( $categories[0]->name ) ): ?>
                <li><i class="far fa-bookmark"></i><a href="<?php print esc_url(get_category_link($categories[0]->term_id)); ?>"><?php echo esc_html($categories[0]->name); ?></a></li>
            <?php endif;?>
        <?php endif;?>

        <?php if ( !empty($suxnix_blog_comments) ): ?>
            <li><i class="far fa-comments"></i> <a href="<?php comments_link();?>"><?php comments_number();?></a></li>
        <?php endif;?>

    </ul>
</div>