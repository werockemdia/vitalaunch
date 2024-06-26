<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="updated fade">
    <p><?php esc_html_e( 'WooMultistore plugin is inactive, please finish the ', 'woonet' ); ?>
        <a href="<?php echo network_admin_url('admin.php?page=woonet-woocommerce'); ?>"><?php esc_html_e( 'Setup Wizard', 'woonet' ); ?></a>
    </p>
</div>
