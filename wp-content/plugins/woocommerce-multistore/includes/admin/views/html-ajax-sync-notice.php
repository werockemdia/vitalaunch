<?php
/**
 * Admin View: Ajax notice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php

$title = 'WooMultistore Ajax Sync';

if( $action == 'wc_multistore_ajax_trash' ){
	$title = 'WooMultistore Ajax Trash';
}

if( $action == 'wc_multistore_ajax_untrash' ){
	$title = 'WooMultistore Ajax Untrash';
}

if( $action == 'wc_multistore_ajax_delete' ){
	$title = 'WooMultistore Ajax Delete';
}
?>
<div class="wrap wc-multistore-ajax-sync-notice-container" data-action="<?php echo $action; ?>" id="<?php echo $action; ?>" data-transient="<?php echo $transient; ?>">
    <div class="wc-multistore-ajax-sync-notice-header">
        <h2>WooMultistore Ajax Sync</h2>
        <p class="wc-multistore-ajax-sync-notice-description"><?php _e( 'Processing products in the queue. Please do not quit the browser while the process is running.' ); ?></p>
    </div>
    <div>
        <p style='display: none;' class="wc-multistore-ajax-sync-notice-completed">Sync completed</p>
        <p style='display: none;' class="wc-multistore-ajax-sync-notice-failed">Sync failed</p>
    </div>

    <div class="wc-multistore-ajax-sync-notice-products">
    <?php foreach ($products as $product_id => $product ): ?>
        <div class="wc-multistore-ajax-sync-notice-container" id="wc-multistore-ajax-sync-notice-product-id-<?php echo $product_id; ?>">
            <span class="wc-multistore-ajax-sync-notice-product-name"><?php echo $product['name'] ?></span>
            <?php if( empty($product['thumbnail']) ): ?>
            <span class="wc-multistore-ajax-sync-notice-product-image"><?php echo wc_placeholder_img() ?></span>
            <?php else: ?>
                <span class="wc-multistore-ajax-sync-notice-product-image"><?php echo wp_get_attachment_image($product['thumbnail']); ?></span>
            <?php endif; ?>
            <span class="wc-multistore-ajax-sync-notice-progress-bar"></span>
            <div class="wc-multistore-ajax-sync-notice-message"></div>
        </div>
    <?php endforeach; ?>
    </div>

    <?php wp_nonce_field($action, 'wc_multistore_ajax_sync_nonce' ); ?>


    <input type="submit" name="submit" id="submit" class="button button-primary wc-multistore-ajax-sync-notice-cancel-sync" value="Cancel Sync" data-action="<?php echo $cancel_action; ?>">

    <div class="wc-multistore-ajax-sync-notice-close" style="display: none;">
        <a data-attr='10' href="#"> Close (10) </a>
    </div>
</div>