<?php 
/** 
 * The main template file
 *
 * @package  WordPress
 * @subpackage  tpcore
 */
get_header(); 

$post_column = is_active_sidebar( 'portfolio-sidebar' ) ? 8 : 10;
$post_column_center = is_active_sidebar( 'portfolio-sidebar' ) ? '' : 'justify-content-center';

?>


      <section class="services__details">
         <div class="container">
			<?php 
                if( have_posts() ):
                while( have_posts() ): the_post();
                    $project_details_image = function_exists('get_field') ? get_field('project_details_image') : '';
                    $project_info_repeater = function_exists('get_field') ? get_field('project_info_repeater') : '';
            ?> 
            <div class="row <?php echo esc_attr($post_column_center); ?>">
                  <div class="col-lg-<?php echo esc_attr($post_column); ?>">
                  	<?php if ( !empty($project_details_image['url']) ) : ?>
                     <div class="services__details__thumb">
                        <img src="<?php echo esc_url($project_details_image['url']); ?>" alt="<?php echo esc_attr($project_details_image['alt']); ?>" title="<?php echo esc_attr($project_details_image['title']); ?>" title="<?php echo esc_attr($project_details_image['title']); ?>">
                     </div>
                     <?php endif; ?>
                     <div class="services__details__content">
                        <?php the_content(); ?>
                     </div>
                  </div>
                  <?php if ( is_active_sidebar('portfolio-sidebar') ): ?> 
                  <div class="col-lg-4">
                     <aside class="services__sidebar">
                     	<?php dynamic_sidebar( 'portfolio-sidebar' ); ?>
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



<?php get_footer();  ?>