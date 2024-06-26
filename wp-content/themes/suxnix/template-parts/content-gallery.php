<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package suxnix
 */

    $gallery_images = function_exists('get_field') ? get_field('gallery_images') : '';
    $suxnix_show_blog_share = get_theme_mod('suxnix_show_blog_share', false);
    $suxnix_post_tags_width = $suxnix_show_blog_share ? 'col-xl-6 col-md-7' : 'col-12';

    // customSetPostViews
    if (function_exists('customSetPostViews')) {
        customSetPostViews(get_the_ID());
    }

?>

<?php if ( is_single() ): ?>

    <article id="post-<?php the_ID();?>" <?php post_class( 'blog--post--item mb-50 format-gallery' );?>>

        <?php if ( has_post_thumbnail() ): ?>
        <div class="blog--post--thumb blog-thumb-active">
            <?php foreach( $gallery_images as $key => $image ) :  ?>
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="blog--post--content blog-details-content">

            <div class="blog--tag">
                <?php $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                }?>
            </div>

            <h2 class="title"><?php the_title();?></h2>

            <!-- blog meta -->
            <?php get_template_part( 'template-parts/blog/blog-meta' ); ?>

            <div class="post-text">
                <?php the_content();?>
                <?php
                    wp_link_pages( [
                        'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'suxnix' ),
                        'after'       => '</div>',
                        'link_before' => '<span class="page-number">',
                        'link_after'  => '</span>',
                    ] );
                ?>
            </div>

            <?php if (!empty(get_the_tags())) : ?>
            <div class="blog-details-bottom">

                <div class="row">
                    <div class="<?php echo esc_attr($suxnix_post_tags_width); ?>">
                        <?php print suxnix_get_tag();?>
                    </div>
                    <?php if (!empty($suxnix_show_blog_share)) : ?>
                    <div class="col-xl-6 col-md-5">
                        <div class="post-share text-md-end">
                            <h5><?php echo esc_html__( 'Social Share', 'suxnix' ) ?></h5>
                            <?php suxnix_social_share(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
            <?php endif; ?>

        </div>
    </article>

<?php else: ?>


    <article id="post-<?php the_ID();?>" <?php post_class( 'blog--post--item mb-40 format-gallery' );?> >

        <?php if ( !empty( $gallery_images ) ): ?>
        <div class="blog--post--thumb blog-thumb-active">
            <?php foreach( $gallery_images as $key => $image ) :  ?>
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="blog--post--content">

            <div class="blog--tag">
                <?php $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                }?>
            </div>

            <h2 class="blog--post--title">
                <a href="<?php the_permalink();?>"><?php the_title();?></a>
            </h2>

            <!-- blog meta -->
            <?php get_template_part( 'template-parts/blog/blog-meta' ); ?>

            <div class="post-text">
                <?php the_excerpt(); ?>
            </div>

            <div class="blog--post--bottom">
                <div class="blog--post--avatar">
                    <div class="blog--avatar--img">
                        <img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ), ['size' => '40'] ) ); ?>" alt="<?php the_author(); ?>">
                    </div>
                    <div class="blog--avatar--info">
                        <p><?php echo esc_html__( 'By', 'suxnix' ) ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>"><?php the_author(); ?></a></p>
                    </div>
                </div>
                <div class="blog--read--more">
                    <a href="<?php the_permalink(); ?>"><i class="fas fa-arrow-right"></i><?php echo esc_html__( 'Read More','suxnix' ); ?></a>
                </div>
            </div>

        </div>
    </article>

<?php
endif;?>