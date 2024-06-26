<?php

defined( 'ABSPATH' ) || exit;

?>



<div>
    <div>
        <?php if( $product->get_meta('_woonet_network_is_child_product_url')): ?>
            <span class="dashicons dashicons-products"></span> <a href="<?php echo $product->get_meta('_woonet_network_is_child_product_url'); ?> " target="_blank">Parent product</a>
        <?php else:  ?>
            <span class="dashicons dashicons-products"></span> <span class="dashicons dashicons-no"></span>
        <?php endif; ?>
    </div>
</div>