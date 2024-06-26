<?php
/**
 * Admin View: Quick edit notice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap wc-multistore-quick-edit-notice-container <?php echo $action; ?>" data-action="<?php echo $action; ?>">

	<div class="wc-multistore-quick-edit-notice-header">
        <?php if ( $action == 'wc_multistore_inline_save_ajax' ): ?>
		    <h2><?php _e( 'WooMultistore Ajax Sync' ); ?></h2>
            <p class="about-description"><?php _e( 'Syncing product, please do not close the window.' ); ?></p>
        <?php else: ?>
            <h2><?php _e( 'WooMultistore Background Sync' ); ?></h2>
            <p class="about-description"><?php _e( 'Product has been scheduled to sync in the background.' ); ?></p>
        <?php endif; ?>
	</div>

	<div class="wc-multistore-quick-edit-notice-products">
        <div class="wc-multistore-quick-edit-notice-product-container" id="">
            <span class="wc-multistore-quick-edit-notice-name">Product name</span>
            <span class="wc-multistore-quick-edit-notice-image"><img src="" alt=""></span>
            <?php if ($action == 'wc_multistore_inline_save_ajax'): ?>
            <span class="wc-multistore-quick-edit-notice-progress-bar"></span>
            <?php endif; ?>
        </div>
	</div>

	<?php wp_nonce_field( $action, 'wc_multistore_quick_edit_nonce'); ?>

    <div class="wc-multistore-quick-edit-notice-message"></div>

	<?php if ( $action == 'wc_multistore_inline_save_ajax' ): ?>
        <input type="submit" name="submit" id="submit" class="button button-primary wc-multistore-quick-edit-notice-cancel-sync" value="Cancel Sync" data-transient="">
	<?php endif; ?>

	<?php if ( $action == 'wc_multistore_inline_save_ajax' ): ?>
        <div class="wc-multistore-quick-edit-notice-close-sync-screen" style="display: none;">
            <a data-attr='5' href="#"> Close (5) </a>
        </div>
	<?php else: ?>
        <div class="wc-multistore-quick-edit-notice-close-sync-screen">
            <a data-attr='5' href="#"> Close (5) </a>
        </div>
	<?php endif; ?>
</div>
