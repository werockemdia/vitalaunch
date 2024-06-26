<?php
/**
 * Admin View: Multstore Reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="evcoe" class="wrap">
	<div id="icon-settings" class="icon32"></div>
	<h2><?php _e( 'Multistore Reset', 'woonet' ); ?></h2>
    <?php if(get_option( 'wc_multistore_network_type' ) == 'master') { ?>
    <div class="notice warning"><p><?php _e( 'If you want to delete synced products from the child stores you have to delete them manually.', 'woonet' ); ?></p></div>
    <?php } else { ?>
    <p></p>
    <?php } ?>
	<form id="form_data" name="form" method="post" action="admin.php?page=woonet-multistore-reset">

		<?php wp_nonce_field( 'woonet-multistore-reset', 'woonet-multistore-reset-nonce' ); ?>

		<table class="form-table ms-reset-table">
			<tbody>
				<tr valign="top">
					<th scope="row" class="label">
						<label><?php _e( 'Reset will delete all sync data and settings. It will reset WooMultistore to the state it was before you inserted the licence key. Please confirm that you want to reset. There is NO UNDO!', 'woonet' ); ?></label>
					</th>
				</tr>
					<td>
						<label><input type="checkbox" value="1" name="reset-confirm" /> <?php _e( 'I confirm that I want to reset WooMultistore.', 'woonet' ); ?></label>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="hidden" name="woo_multistore_form_submit" value="reset-multistore" />
			<input type="submit" name="Submit" class="button-primary woomulti-deactivate-button" value="<?php _e( 'Reset WooMultistore', 'woonet' ); ?>" />
		</p>
	</form>
</div>