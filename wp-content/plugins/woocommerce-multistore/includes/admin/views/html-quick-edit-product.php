<?php
/**
 * Admin View: Quick Edit Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$toggle_publish_to = $this->settings['synchronize-by-default'];
$toggle_inherit_to = $this->settings['inherit-by-default'];
$toggle_stock = $this->settings['synchronize-stock'];
?>
<fieldset id="woonet-quick-edit-fields" class="woocommerce-multistore-fields inline-edit-col">

	<h4><?php _e( 'Multisite - Publish to', 'woonet' ); ?></h4>

	<div class="inline-edit-col">
		<p class="form-field no_label inline">
            <label for="woonet_toggle_all_sites"  class="wc-multistore-checkbox-label">
			    <input name="woonet_toggle_all_sites" id="woonet_toggle_all_sites" class="woonet_toggle_all_sites  inline" value="<?php echo $toggle_publish_to ?>"  type="checkbox" />
                <span class="checkmark"></span>
            </label>
			<b><span class="description"><?php _e( 'Publish to all Sites', 'woonet' ); ?></span></b>
		</p>
        <p class="form-field no_label inline">
            <label for="woonet_toggle_inherit_to"  class="wc-multistore-checkbox-label">
			    <input id="woonet_toggle_inherit_to" name="woonet_toggle_inherit_to" class="woonet_toggle_inherit_to inline" value="<?php echo $toggle_inherit_to ?>"  type="checkbox" />
                <span class="checkmark"></span>
            </label>
			<b><span class="description"><?php _e( 'Inherit all Sites', 'woonet' ); ?></span></b>
		</p>
        <p class="form-field no_label inline">
            <label for="woonet_toggle_stock_to"  class="wc-multistore-checkbox-label">
			    <input id="woonet_toggle_stock_to" name="woonet_toggle_stock_to" class="woonet_toggle_stock_to inline" <?php if( $toggle_stock == 'yes' ){ echo 'disabled '; echo 'checked'; } ?> value="<?php echo $toggle_stock ?>"  type="checkbox" />
                <span class="checkmark"></span>
            </label>
			<b><span class="description"><?php _e( 'Sync stock to all Sites', 'woonet' ); ?></span></b>
	        <?php if( $toggle_stock == 'yes' ): ?>
                <span class="tips" title="Stock fields are disabled when always maintain stock synchronization for re-published products is enabled. You can disable this on general settings page.">
                    <i class="dashicons dashicons-warning wc-multistore-warning-tip"></i>
                </span>
	        <?php endif; ?>
        </p>

		<div class="woonet_sites">
            <?php foreach ( $this->sites as $site ) : ?>

                <?php
	            if( ! $site->is_active() ){
                    continue;
                }
	            $publish_to = '_woonet_publish_to_' . $site->get_id();
	            $inherit = '_woonet_publish_to_' . $site->get_id() . '_child_inheir';
	            $stock = '_woonet_' . $site->get_id() . '_child_stock_synchronize';
                ?>
                <p class="form-field no_label _woonet_publish_to inline" data-group-id="<?php echo $site->get_id(); ?>">
                    <label for="<?php echo $publish_to; ?>"  class="wc-multistore-checkbox-label alignleft">
                        <input type="hidden" name="<?php echo $publish_to; ?>" />
                        <input type="checkbox" id="<?php echo $publish_to; ?>" class ="<?php echo $publish_to; ?> _woonet_publish_to" />
                        <span class="checkmark"></span>
                    </label>
                    <span class="checkbox-title woomulti-store-name"><?php echo $site->get_name(); ?> <span class="warning"><b>Warning:</b> By deselecting this shop the product is unassigned, but not deleted from the shop, which should be done manually.</span></span>
                    <br class="clear">

                    <label for="<?php echo $inherit; ?>"  class="wc-multistore-checkbox-label alignleft pl">
                        <input type="hidden" name="<?php echo $inherit; ?>" />
                        <input type="checkbox" id="<?php echo $inherit; ?>" class = "<?php echo $inherit; ?> _woonet_inherit_to">
                        <span class="checkmark"></span>
                    </label>
                    <span class="checkbox-title">Child product inherit Parent products changes</span>
                    <br class="clear">

                    <label for="<?php echo $stock; ?>"  class="wc-multistore-checkbox-label alignleft pl">
                        <input type="hidden" name="<?php echo $stock; ?>" />
                        <input type="checkbox" id="<?php echo $stock; ?>" class = "<?php echo $stock; ?> _woonet_sync_stock" <?php echo 'yes' == $this->settings['synchronize-stock'] ? 'disabled="disabled"' :  ''; ?> />
                        <span class="checkmark"></span>
                    </label>
                    <span class="checkbox-title">If checked, any stock change will synchronize across product tree.</span>
                    <br class="clear">

                </p>

            <?php endforeach; ?>
		</div>
	</div>

</fieldset>

<fieldset id="woonet-quick-edit-fields-slave" class="woocommerce-multistore-fields inline-edit-col">

	<p class="form-field _woonet_description inline">
		<span class="description"><?php _e( 'This product is a child product. Only parent products can be synced to other sites.', 'woonet' ); ?></span>
	</p>

</fieldset>

<input type="hidden" name="_is_master_product" value="" />
<input type="hidden" name="master_blog_id" value="" />
<input type="hidden" name="product_blog_id" value="" />
<input type="hidden" name="woocommerce_multisite_quick_edit" value="1" />
<input type="hidden" name="woocommerce_multisite_quick_edit_nonce" value="<?php echo wp_create_nonce( 'woocommerce_multisite_quick_edit_nonce' ); ?>" />
