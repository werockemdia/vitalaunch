<?php
/**
 * HTML Template for Licence Form Edit.
 *
 * This displays the admin licence edit page.
 *
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">

	<?php
	$this->display_errors();
	$this->display_messages();
	?>

    <div id="icon-settings" class="icon32"></div>
    <div id="form_data">
        <h2 class="subtitle"><?php esc_html_e( 'Software License', 'woonet' ); ?></h2>
        <div class="postbox">
            <form id="form_data" name="wc_multistore_edit_license_form" method="post">
				<?php wp_nonce_field( 'wc_multistore_edit_license', 'wc_multistore_edit_license_nonce' ); ?>
                <div class="section section-text ">
                    <h4 class="heading"><?php esc_html_e( 'License Key', 'woonet' ); ?></h4>
                    <div class="option">
                        <div class="controls">
                            <p><b><?php echo esc_html_e( substr( $this->get()['key'], 0, 20 ) ); ?>-xxxxxxxx-xxxxxxxx</b> &nbsp;&nbsp;&nbsp;
                                <input type="submit" name="submit_wc_multistore_edit_license_form" class="button-secondary" title="Deactivate" value="Deactivate">
                            </p>
                        </div>
                        <div class="explain"><?php esc_html_e( 'You can generate more keys from', 'woonet' ); ?> <a href="https://woomultistore.com/premium-plugins/my-account/" target="_blank">My Account</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
