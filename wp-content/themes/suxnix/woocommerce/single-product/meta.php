<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<div class="inner-shop-details-bottom">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<div class="sku_wrapper"><b><?php esc_html_e( 'SKU :', 'suxnix' ); ?></b> <span class="sku"><?php echo wp_kses_post( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'suxnix' ); ?></span></div>

	<?php endif; ?>

	<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<div class="posted_in"><b>' . _n( 'Category :', 'Categories :', count( $product->get_category_ids() ), 'suxnix' ) . '</b> ', '</div>' ); ?>

	<?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<div class="tagged_as"><b>' . _n( 'Tag :', 'Tags :', count( $product->get_tag_ids() ), 'suxnix' ) . ' </b>', '</div>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
