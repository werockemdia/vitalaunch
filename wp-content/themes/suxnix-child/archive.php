<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package suxnix
 */

get_header();

$blog_column_lg = is_active_sidebar( 'blog-sidebar' ) ? 8 : 12;

?>

<div class="blog-area-new pt-120 pb-120">
    <div class="container">
        <div class="row justify-content-center">
			<div class="col-lg-<?php print esc_attr( $blog_column_lg );?>">
				<div class="postbox__wrapper">
				<?php if ( have_posts() ): ?>
				
					<header class="page-header d-none">
						<?php
							the_archive_title( '<h1 class="page-title">', '</h1>' );
							the_archive_description( '<div class="archive-description">', '</div>' );
						?>
					</header><!-- .page-header -->
					<?php
						/* Start the Loop */
						while ( have_posts() ):
							the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/
							get_template_part( 'template-parts/content', get_post_type() );
						endwhile;
					?>

					<nav aria-label="Page navigation pagination-wrap">
						<?php suxnix_pagination( '<i class="fas fa-angle-double-left"></i>', '<i class="fas fa-angle-double-right"></i>', '', [ 'class' => 'page-link next' ] );?>
					</nav>
					<?php
						else:
							get_template_part( 'template-parts/content', 'none' );
						endif;
					?>
				</div>
			</div>
			<?php if ( is_active_sidebar( 'blog-sidebar' ) ): ?>
		        <div class="col-lg-4 col-md-7">
		        	<aside class="blog-sidebar pl-20">
						<?php get_sidebar();?>
	            	</aside>
	            </div>
			<?php endif;?>
        </div>
    </div>
</div>
<?php
get_footer();
