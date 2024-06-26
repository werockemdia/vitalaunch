<?php
/**
 * Admin View: Bulk Edit Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$toggle_publish_to = $this->settings['synchronize-by-default'];
$toggle_inherit_to = $this->settings['inherit-by-default'];
$toggle_stock = $this->settings['synchronize-stock'];
?>

<fieldset id="woonet-bulk-edit-fields" class="woocommerce-multistore-fields inline-edit-col">

    <h4>Multisite - Publish to</h4>

    <div class="inline-edit-col">

        <p class="form-field no_label woonet_toggle_all_sites inline">
            <label class="alignleft" style="width: auto; ">
                <select class="_woonet_global_publish_to" name="_woonet_global_publish_to">
                    <option value="">— Use Product Settings —</option>
                    <option value="yes" <?php selected(isset($toggle_publish_to) && $toggle_publish_to == 'yes'); ?> >Yes</option>
                    <option value="no">No</option>
                </select>
                <span class="checkbox-title woomulti-store-name">Toggle all Sites</span>
            </label>
            <br class="clear">

            <label class="alignleft pl" style="width: auto; padding-left: 30px;">
                <select class="_woonet_global_inherit" name="_woonet_global_inherit">
                    <option value="">— Use Product Settings —</option>
                    <option value="yes" <?php selected(isset($toggle_inherit_to) && $toggle_inherit_to == 'yes'); ?> >Yes</option>
                    <option value="no">No</option>
                </select>
                <span class="checkbox-title">Toggle all Child product inherit Parent products changes</span>
            </label>
            <br class="clear">

            <label class="alignleft pl" style="width: auto; padding-left: 30px;">
                <select class="_woonet_global_stock" name="_woonet_global_stock" <?php if( $toggle_stock == 'yes' ){ echo 'disabled'; } ?> >
                    <option value="">— Use Product Settings —</option>
                    <option value="yes" <?php selected(isset($toggle_stock) && $toggle_stock == 'yes'); ?> >Yes</option>
                    <option value="no">No</option>
                </select>
                <span class="checkbox-title">Toggle all Child stock sync</span>
                <?php if( $toggle_stock == 'yes' ): ?>
                    <span class="tips" data-tip="Stock fields are disabled when always maintain stock synchronization for re-published products is enabled. You can disable this on general settings page.">
                        <i class="dashicons dashicons-warning wc-multistore-warning-tip"></i>
                    </span>
                <?php endif; ?>
            </label>
            <br class="clear">
        </p>

        <div class="woonet_sites">
			<?php foreach ( $this->sites as $site ): ?>
                <?php
				$publish_to = '_woonet_publish_to_' . $site->get_id();
				$inherit = '_woonet_publish_to_' . $site->get_id() . '_child_inheir';
				$stock = '_woonet_' . $site->get_id() . '_child_stock_synchronize';
                ?>
                <p class="form-field no_label _woonet_publish_to inline" data-group-id="<?php echo $site->get_id(); ?>>">
                    <label class="alignleft">
                        <select name="<?php echo $publish_to ?>" class="_woonet_publish_to">
                            <option value="">— Use Product Settings —</option>
                            <option value="yes" <?php selected(isset($toggle_publish_to) && $toggle_publish_to == 'yes'); ?> >Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="checkbox-title woomulti-store-name"><?php echo $site->get_url(); ?><span class="warning"><b>Warning:</b> By deselecting this shop the product is unassigned, but not deleted from the shop, which should be done manually.</span></span>
                    </label>
                    <br class="clear">

                    <label class="alignleft pl">
                        <select name="<?php echo $inherit; ?>" class="_woonet_inherit">
                            <option value="">— Use Product Settings —</option>
                            <option value="yes" <?php selected(isset($toggle_inherit_to) && $toggle_inherit_to == 'yes'); ?> >Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="checkbox-title">Child product inherit Parent products changes</span>
                    </label>
                    <br class="clear">

                    <label class="alignleft pl">
                        <select name="<?php echo $stock; ?>" class="_woonet_stock <?php if( $toggle_stock == 'yes' ){ echo 'disabled'; } ?>">
                            <option value="">— Use Product Settings —</option>
                            <option value="yes" <?php selected(isset($toggle_stock) && $toggle_stock == 'yes'); ?> >Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="checkbox-title">If checked, any stock change will synchronize across product tree.</span>
                    </label>
                    <br class="clear">
                </p>
			<?php endforeach; ?>
        </div>
    </div>

</fieldset>