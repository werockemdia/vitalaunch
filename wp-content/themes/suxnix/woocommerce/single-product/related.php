<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) : ?>
	<div class="related-products-wrap">
		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'suxnix' ) );

		if ( $heading ) :
			?>
			<h2 class="title"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>



		<?php
			$related_class = '';
			if (count($related_products) >= 4){
				$related_class = 'rel-product-slider-active';
			}
		?>
		<div class="row <?php print esc_attr($related_class); ?>">
		<?php foreach ( $related_products as $related_product ) : ?>

			<div class="col-xl-3 col-lg-4 col-sm-6">
				<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
				?>
			</div>

		<?php endforeach; ?>
		</div>

	</div>

	<?php
endif;

wp_reset_postdata();
