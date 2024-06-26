<?php
/**
 * The main template file
 *
 * @package  WordPress
 * @subpackage  tpcore
 */
get_header();

$post_column = is_active_sidebar( 'services-sidebar' ) ? 8 : 8;
$post_column_center = is_active_sidebar( 'services-sidebar' ) ? '' : 'justify-content-center';

?>


<!-- services-details-area -->
<section class="services-details-area">
    <div class="container">
        <?php if( have_posts() ) : while( have_posts() ) : the_post();
            $project_details_image = function_exists('get_field') ? get_field('project_details_image') : '';
            $project_info_repeater = function_exists('get_field') ? get_field('project_info_repeater') : '';
        ?>
        <div class="row <?php echo esc_attr($post_column_center); ?>">
            <div class="col-lg-<?php echo esc_attr($post_column); ?>">
                <div class="services-details-wrap">
                    <div class="services-details-thumb">
                        <?php the_post_thumbnail(); ?>
                    </div>
                    <div class="services-details-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <?php if ( is_active_sidebar('services-sidebar') ): ?>
            <div class="col-lg-4">
                <aside class="services-sidebar">
                    <?php dynamic_sidebar( 'services-sidebar' ); ?>
                </aside>
            </div>
          <?php endif; ?>

        </div>
        <?php
            endwhile; wp_reset_query();
            endif;
        ?>
    </div>
</section>
<!-- services-details-area-end -->

<?php get_footer();  ?>