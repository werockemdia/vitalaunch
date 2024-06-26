<?php

defined( 'ABSPATH' ) || exit;

?>
<div class='woonet-setup-wizard woonet-license-key'>
	<img src='<?php echo plugins_url( '/assets/images/connect.png' ,  dirname(__FILE__, 3) ); ?>' alt='Lock'/>
	<?php $master_site = get_option('wc_multistore_master_connect'); ?>
	<?php if ( ! $master_site ) :?>
		<h1> Connect to Master Site </h1>
		<p> Please enter the code that you generated from the master site. </p>
		<div class="error notice" style='display: none;'>
	    </div>
	    <div class="notice-success notice" style='display: none;'>
	    </div>
		<form  autocomplete="off" action='#' method='GET' id='woonet-add-master-site'>
			<?php wp_nonce_field('wc_multistore_connect_master_site', 'wc_multistore_connect_master_site_nonce'); ?>
			<input type='text' value='' placeholder="Connect Code" id="wc_multistore_connect_master_code">
			<button type='button' class='button-primary button-connect'> Add </button>
		</form>
	<?php else: ?>
		<h1> Connected to Master Site </h1>
		<p> Once disconnected, child site will no longer receive updates from the master site. You should also delete the site from the master site.  </p>

		<div class="error notice" style='display: none;'>
	    </div>
	    <div class="notice-success notice" style='display: none;'>
	    </div>
        <br />
        <p> Master site <a target='_blank' href='<?php echo $master_site['master_url']; ?>'><?php echo $master_site['master_url']; ?></a></p>
        <?php
            $wc_multistore_site_api_child = new WC_Multistore_Site_Api_Child();
            $connection = $wc_multistore_site_api_child->get_master_status();
            if ( ! empty( $connection[ 'status' ] ) && $connection[ 'status' ] == 'success' ) {
                ?>
                    <p> Connection status: <strong style='color:green;'> Active</strong>. Last checked on <?php echo date('Y-m-d H:i:s A'); ?> (website time)</p>
                <?php
            } else {
                ?>
                    <p> Connection status: <strong style='color:red;'> failed</strong>. Last checked on <?php echo date('Y-m-d H:i:s A'); ?> (website time) </p>
                <?php
            }
        ?>
        <form  autocomplete="off" action='#' method='GET' id='woonet-delete-master-site'>
            <?php wp_nonce_field('wc_multistore_delete_master_site', 'wc_multistore_delete_master_site_nonce'); ?>
            <button type='button' class='button-primary button-disconnect' style='width: 100%;'> Disconnect </button>
        </form>

        <br style="clear: both;">
        <br />
        <h1> Change Master Site </h1>
        <p> Please make sure that the new master site is a clone of the previous master site. This functionality is only intended to allow quick migration of the master site from staging to live  </p>
		<?php if ( !empty($_SESSION['mstore_form_submit_messages']) ): ?>
			<?php foreach( $_SESSION['mstore_form_submit_messages'] as $error ): ?>
                <div class="error notice">
                    <p><?php _e( esc_html($error), 'woonet' ); ?></p>
                </div>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( !empty($_SESSION['mstore_form_submit_success_messages']) ): ?>
			<?php foreach( $_SESSION['mstore_form_submit_success_messages'] as $msg ): ?>
                <div class="success notice">
                    <p><?php _e( esc_html($msg), 'woonet' ); ?></p>
                </div>
			<?php endforeach; ?>
		<?php endif; ?>
        <form action='<?php echo admin_url( 'admin.php?page=woonet-connect-master' ); ?>' method='POST'>
            <input type="text" value="<?php echo str_replace(	array('http://','https://',	),'', $master_site['master_url']); ?>" name="wc_multistore_master_url">
            <?php wp_nonce_field( 'woonet_save_master_site' ); ?>
            <button type='submit' class='button-primary' name='submit' style="width: 100%;" value='save' onclick='return confirm("Do you really want to change the master site?");'> Save </button>
        </form>

	<?php endif; ?>
</div>