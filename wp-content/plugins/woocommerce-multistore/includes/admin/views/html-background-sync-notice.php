<?php
/**
 * Admin View: Background sync notice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap wc-multistore-background-sync-notice-container">
	<div class="wc-multistore-background-sync-notice-header">
		<h2><?php _e( 'WooMultistore Background Sync' ); ?></h2>
		<p class="about-description"><?php _e( 'The following product/s have been scheduled to sync in the background' ); ?></p>
	</div>

	<div class="wc-multistore-background-sync-notice-products">
		<?php foreach ($products as $product_id => $product ): ?>
			<div class="wc-multistore-background-sync-notice-scheduler-container" id="wc-multistore-product-ajax-id-<?php echo $product_id; ?>">
				<span class="wc-multistore-background-sync-notice-name"><?php echo $product['name'] ?></span>
				<?php if( empty($product['thumbnail']) ): ?>
					<span class="wc-multistore-background-sync-notice-image"><?php echo wc_placeholder_img() ?></span>
				<?php else: ?>
					<span class="wc-multistore-background-sync-notice-image"><?php echo wp_get_attachment_image($product['thumbnail']); ?></span>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="wc-multistore-background-sync-notice-close-sync-screen">
		<a data-attr='5' href="#"> Close (5) </a>
	</div>
</div>
