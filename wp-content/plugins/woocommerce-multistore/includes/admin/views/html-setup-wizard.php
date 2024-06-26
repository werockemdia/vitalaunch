<?php

defined( 'ABSPATH' ) || exit;

?>
<div class='woonet-setup-wizard woonet-network-type'>
    <img src='<?php echo plugins_url( '/assets/images/checklist.png', dirname( __FILE__, 3 ) ); ?>' alt='Lock'/>
    <h1> Setup Wizard </h1>
    <p>Thank you for installing the plugin. The wizard will help you to get started with the plugin.</p>

	<?php if ( ! empty( $_SESSION['mstore_form_submit_messages'] ) ): ?>
		<?php foreach ( $_SESSION['mstore_form_submit_messages'] as $error ): ?>
            <div class="error notice">
                <p><?php _e( esc_html( $error ), 'woonet' ); ?></p>
            </div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php
	$network_type                   = get_option( 'wc_multistore_network_type' );
	$wc_multistore_license          = get_site_option( 'wc_multistore_license' );
	$master_store                   = get_site_option( 'wc_multistore_master_store' );
	$wc_multistore_child_sites      = get_option( 'wc_multistore_sites' );
	$woonet_master_connect          = get_option( 'wc_multistore_master_connect' );
	?>

    <ul class='wizard-checkelist'>
        <?php if( is_multisite() ) : ?>

            <!--Select Master Store-->
            <li>
		        <?php if ( ! empty( $master_store ) ): ?>
                    <img src='<?php echo plugins_url( '/assets/images/checked.png', dirname( __FILE__, 3 ) ); ?>'/>
		        <?php else: ?>
                    <img src='<?php echo plugins_url( '/assets/images/checked_unchecked.png', dirname( __FILE__, 3 ) ); ?>'/>
		        <?php endif; ?>
                <a href='<?php echo network_admin_url( 'admin.php?page=woonet-master-store' ); ?>'><h3> Select Master Store </h3>
                </a>
            </li>

            <!--Manage License-->
            <li>
                <?php if( ! empty( $master_store ) ): ?>
                    <?php if ( ! empty( $wc_multistore_license ) ): ?>
                        <img src='<?php echo plugins_url( '/assets/images/checked.png', dirname( __FILE__, 3 ) ); ?>'/>
                    <?php else: ?>
                        <img src='<?php echo plugins_url( '/assets/images/checked_unchecked.png', dirname( __FILE__, 3 ) ); ?>'/>
                    <?php endif; ?>
                    <a href='<?php echo network_admin_url( 'admin.php?page=woonet-license-key' ); ?>'>
                        <?php if ( ! empty( $wc_multistore_license ) ): ?>
                            <h3> Manage License Key </h3>
                        <?php else: ?>
                            <h3> Enter License Key </h3>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </li>

        <?php else: ?>

            <!--Select Network Type-->
            <li>
		        <?php if ( ! empty( $network_type ) ): ?>
                    <img src='<?php echo plugins_url( '/assets/images/checked.png', dirname( __FILE__, 3 ) ); ?>'/>
		        <?php else: ?>
                    <img src='<?php echo plugins_url( '/assets/images/checked_unchecked.png', dirname( __FILE__, 3 ) ); ?>'/>
		        <?php endif; ?>
                <a href='<?php echo network_admin_url( 'admin.php?page=woonet-network-type' ); ?>'><h3> Select Network Type </h3>
                </a>
            </li>

            <!--Manage License-->
            <li>
		        <?php if ( ( ! empty( $network_type ) && $network_type == 'child' ) || ! empty( $wc_multistore_license ) ): ?>
                    <img src='<?php echo plugins_url( '/assets/images/checked.png', dirname( __FILE__, 3 ) ); ?>'/>
		        <?php else: ?>
                    <img src='<?php echo plugins_url( '/assets/images/checked_unchecked.png', dirname( __FILE__, 3 ) ); ?>'/>
		        <?php endif; ?>

                <a href='<?php echo network_admin_url( 'admin.php?page=woonet-license-key' ); ?>'>
			        <?php if ( ! empty( $network_type ) && $network_type == 'master' && ! empty( $wc_multistore_license ) ): ?>
                        <h3> Manage License Key </h3>
			        <?php else: ?>
                        <h3> Enter License Key </h3>
			        <?php endif; ?>
                </a>
            </li>

            <!--Connect To Master-->
	        <?php if ( $network_type == 'child' ): ?>
                <li>
			        <?php if ( ! empty( $woonet_master_connect ) ): ?>
                        <img src='<?php echo plugins_url( '/assets/images/checked.png', dirname( __FILE__, 3 ) ); ?>'/>
                        <a href='<?php echo network_admin_url( 'admin.php?page=woonet-connect-master' ); ?>'><h3> Master Site </h3></a>
			        <?php else: ?>
                        <img src='<?php echo plugins_url( '/assets/images/checked_unchecked.png', dirname( __FILE__, 3 ) ); ?>'/>
                        <a href='<?php echo network_admin_url( 'admin.php?page=woonet-connect-master' ); ?>'><h3> Connect to Master Site </h3></a>
			        <?php endif; ?>

                </li>
	        <?php endif; ?>

            <!--Connect Child Sites-->
	        <?php if ( $network_type == 'master' ): ?>
                <li>
			        <?php if ( ! empty( $wc_multistore_child_sites ) ): ?>
                        <img src='<?php echo plugins_url( '/assets/images/checked.png', dirname( __FILE__, 3 ) ); ?>'/>
			        <?php else: ?>
                        <img src='<?php echo plugins_url( '/assets/images/checked_unchecked.png', dirname( __FILE__, 3 ) ); ?>'/>
			        <?php endif; ?>
                    <a href='<?php echo network_admin_url( 'admin.php?page=woonet-connect-child' ); ?>'><h3> Connect Child Sites </h3></a>
                </li>
	        <?php endif; ?>

        <?php endif; ?>
    </ul>
</div>