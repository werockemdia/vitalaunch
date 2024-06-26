<?php

defined( 'ABSPATH' ) || exit;

?>

<div class="wc-multistore-column-container">
    <div>
        <div class="wc-multistore-td wc-multistore-td-site"><span title="Site" class="dashicons dashicons-admin-site-alt3"></span></div>
        <div class="wc-multistore-td"><span title="Publish" class="dashicons dashicons-saved"></span></div>
        <div class="wc-multistore-td"><span title="Sync" class="dashicons dashicons-controls-repeat"></span></div>
        <div class="wc-multistore-td"><span title="Sync stock" class="dashicons dashicons-products"></span></div>
        <div class="wc-multistore-td"><span title="Child Product URL" class="dashicons dashicons-admin-links"></span></div>
    </div>

    <?php
    $settings = $product->get_meta('_woonet_settings');
    $children_data = $product->get_meta('_woonet_children_data');
    foreach ($this->sites as $site){
        $publish_to_meta_value =  ( ! empty($settings) && isset($settings['_woonet_publish_to_' . $site->get_id()]) && $settings['_woonet_publish_to_' . $site->get_id()] == 'yes' ) ? '<span class="dashicons dashicons-yes wc-multistore-bg-success"></span>' : '<span class="dashicons dashicons-no wc-multistore-bg-danger"></span>';
        $inherit_meta_value = ( ! empty($settings) && isset($settings['_woonet_publish_to_' . $site->get_id() . '_child_inheir']) && $settings['_woonet_publish_to_' . $site->get_id() . '_child_inheir'] == 'yes' ) ? '<span class="dashicons dashicons-yes wc-multistore-bg-success"></span>' : '<span class="dashicons dashicons-no wc-multistore-bg-danger"></span>';
        $stock_meta_value = ( ! empty($settings) && isset($settings['_woonet_' . $site->get_id() . '_child_stock_synchronize']) && $settings['_woonet_' . $site->get_id() . '_child_stock_synchronize'] == 'yes' ) ? '<span class="dashicons dashicons-yes wc-multistore-bg-success"></span>' : '<span class="dashicons dashicons-no wc-multistore-bg-danger"></span>';
        ?>

        <div class="woonet-quick-edit-site-id-<?php echo $site->get_id() ?>">
            <div class="wc-multistore-td wc-multistore-td-site"><span title='<?php echo $site->get_name(); ?>'><?php echo substr($site->get_name(), 0, 25); ?></span></div>
            <div class="wc-multistore-td"><span><?php echo $publish_to_meta_value; ?></span></div>
            <div class="wc-multistore-td"><span><?php echo $inherit_meta_value; ?> </span></div>
            <div class="wc-multistore-td"><span><?php echo $stock_meta_value; ?> </span></div>
            <div class="wc-multistore-td">
                <?php if( ! empty($children_data) && !empty($children_data[$site->get_id()]) && !empty($children_data[$site->get_id()]['edit_link']) ): ?>
                    <a target="_blank" href="<?php echo $children_data[$site->get_id()]['edit_link'] ?>">edit </a>
                <?php else: ?>
                    <a target="_blank" href=""></a>
                <?php endif; ?>
            </div>
        </div>

    <?php } ?>
</div>