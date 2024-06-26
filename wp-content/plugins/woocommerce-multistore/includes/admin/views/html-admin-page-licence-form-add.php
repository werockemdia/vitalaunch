<?php
/**
 * HTML Template for Licence Form Add.
 *
 * This displays the admin licence add page.
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
    <h2><?php esc_html_e( 'Software Licence', 'woonet' ); ?><br />&nbsp;</h2>
    <form id="form_data" name="wc_multistore_add_license_form" method="post">
        <div class="postbox">
            <?php wp_nonce_field( 'wc_multistore_license', 'wc_multistore_license_nonce' ); ?>
            <div class="section section-text">
                <h4 class="heading"><?php esc_html_e( 'License Key', 'woonet' ); ?></h4>
                <div class="option">
                    <div class="controls">
                        <input type="text" value="" name="license_key" class="text-input">
                    </div>
                    <div class="explain"><?php esc_html_e( 'Enter the License Key you got when bought this product. If you lost the key, you can always retrieve it from', 'woonet' ); ?> <a href="https://woomultistore.com/premium-plugins/my-account/" target="_blank"><?php esc_html_e( 'My Account', 'woonet' ); ?></a><br />
                        <?php esc_html_e( 'More keys can be generate from', 'woonet' ); ?> <a href="https://woomultistore.com/premium-plugins/my-account/" target="_blank"><?php esc_html_e( 'My Account', 'woonet' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <p class="submit">
            <input type="submit" name="submit_wc_multistore_add_license_form" class="button-primary" value="<?php esc_html_e( 'Save', 'woonet' ); ?>">
        </p>
    </form>
</div>
