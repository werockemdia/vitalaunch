<?php

defined( 'ABSPATH' ) || exit;

?>

<?php $master_store = get_site_option('wc_multistore_master_store'); ?>

<div class='woonet-setup-wizard woonet-network-type woonet-pages'>
	<img src='<?php echo plugins_url( '/assets/images/computer.png' ,  dirname(__FILE__, 3 ) ); ?>' alt='Lock'/>

	<?php if ( ! empty( $master_store ) ): ?>
        <h1> Master Store </h1>
        <p> In order to change the master store you will have to reset the plugin.</p>
	<?php else: ?>
        <h1> Select Master Store </h1>
        <p> Your network consists of a master store and many child stores. Master store is used as the controller for all child stores. Below you can select the master store.  <a href='#' class='woonet-network-type-whats-difference-btn'> What's the difference? </a></p>
        <p style='display: none;' class='woonet-network-type-whats-difference'>
            Master store can create and sync products across the network, while child stores can only create their own products and receive synced products from the master store. You can view all orders from your child stores only on the master store. The master store also keeps track of stock and manages the synchronization of products. In any network, there can be only one master store.
        </p>
	<?php endif; ?>


	<?php if ( !empty($_SESSION['mstore_form_submit_messages']) ): ?>
		<?php foreach( $_SESSION['mstore_form_submit_messages'] as $error ): ?>
			<div class="error notice">
		        <p><?php _e( esc_html($error), 'woonet' ); ?></p>
		    </div>
		<?php endforeach; ?>
	<?php endif; ?>


    <?php if ( ! empty( $master_store ) ): ?>
        <?php
            switch_to_blog($master_store);
                $url = get_bloginfo('url');
                echo '<br />';
                echo '<p> Current Master Store: <a href="'.$url.'">' . $url . '</a></p>';
            restore_current_blog();
        ?>
    <?php else: ?>
        <form  class='network-type-form' autocomplete="off" action='<?php network_admin_url( 'admin.php?page=woonet-master-store' ); ?>' method='post'>
		    <?php wp_nonce_field( 'woonet_select_master_store'); ?>
            <p>
                <select name="wc_multistore_master_store">
				    <?php foreach ( WOO_MULTISTORE()->active_sites as $site ): ?>
                        <option value="<?php echo $site->get_id(); ?>" <?php selected( $site->get_id(),$master_store ); ?> ><?php echo $site->get_name(); ?></option>
				    <?php endforeach; ?>
                </select>
            </p>

            <p class='spacer'> </p>

            <button type='submit' class='button-primary'> Submit </button>
        </form>
    <?php endif; ?>

</div>