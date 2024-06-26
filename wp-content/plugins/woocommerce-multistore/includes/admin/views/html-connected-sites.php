<?php

defined( 'ABSPATH' ) || exit;

$sites = WOO_MULTISTORE()->sites;

?>
<div class='woonet-pages'>
	<h1>  Network Sites </h1>
	<p> A list of sites connected to the network. </p>
	<div class="error notice" style='display: none;'></div>
	<div class="notice-success notice" style='display: none;'></div>

    <?php if ( ! is_multisite() ): ?>
	    <a class='add-to-network-btn' href='<?php echo admin_url( 'admin.php?page=woonet-connect-child' ); ?>'> Add </a>
    <?php endif; ?>
		
	<?php if ( ! empty( $sites ) ) : ?>

	<table class='woonet-sites-table'> 
		<tr> 
			<th> Site </th>
			<th> Status </th>
			<th> Date Added </th>
			<th> Connection Status </th>
			<th> Action </th>
		</tr>

		<?php foreach ( $sites as $site ) : ?>
			<tr class="wc-multistore-connected-site-id-<?php echo $site->get_id(); ?>">
                <!--Site Name-->
				<td>
                    <?php if ( is_multisite() ): ?>
                        <a href="<?php echo $site->get_url(); ?>"><?php echo $site->get_url(); ?></a>
                    <?php else: ?>
                        <input type="text" value="<?php echo $site->get_name(); ?>" class="wc_multistore_child_site_name" style="width: 500px;">
                    <?php endif; ?>
				</td>

                <!--Site Status-->
				<td>
                    <?php if( $site->is_active() ) : ?>
                        Active
                    <?php else: ?>
                        Inactive
                   <?php endif; ?>
				 </td>

                <!--Site Date-->
				<td>
                    <?php echo $site->get_date_added(); ?>
                </td>

                <!--Site Connection-->
				<td style="max-width:150px" class="wc-multistore-site-connection">
                    <?php
                    if ( is_multisite() ){
	                    if( $site->is_active() ) {
                            echo 'Child site version ' . WOO_MSTORE_VERSION;
	                    }else{
		                    echo 'Child site is disabled';
                        }
                    }else{
	                    if( $site->is_active() ) {
                            echo 'Waiting response from child site';
	                    }
                    }
                    ?>
				 </td>

                <!--Site Actions-->
				<td>
					<?php if ( ! is_multisite() ): ?>
					<form action='<?php echo admin_url( 'admin.php?page=woonet-connected-sites' ); ?>' method='POST'>
						<?php wp_nonce_field( 'woonet_delete_site' ); ?>
						<input type="hidden" value='<?php echo $site->get_id(); ?>' name="__key">
						<button type='submit' class='button-secondary' name='submit' value='remove' onclick='return confirm("Do you really want to delete the site?");'> Remove </button>
						<?php if ( $site->is_active() ) : ?>
								<button type='submit' class='button-primary woomulti-deactivate-button' name='submit' value='deactivate' onclick='return confirm("Do you really want to deactivate the site? Deactivated sites are hidden from the sync options, but the settings will be preserved.");'> Deactivate </button>
                        <?php else:	?>
								<button type='submit' class='button-primary' name='submit' value='activate' onclick='return confirm("Do you really want to activate the site?");'> Activate </button>
						<?php endif; ?>
                        <button type='button' class='wc_multistore_save_child_site button-primary' name='submit' value='<?php echo $site->get_id(); ?>' onclick='return confirm("Do you really want to save the changes?");'> Save </button>
					</form>
					<?php endif; ?>
                </td>

			</tr>
		<?php endforeach; ?>

	</table>
	<?php else : ?>
		<p class='woonet-sites-empty'> Follow the <a href='<?php echo admin_url( 'admin.php?page=woonet-woocommerce' ); ?>'> Setup Wizard </a> to add a new site. </p>
	<?php endif; ?>
</div>
