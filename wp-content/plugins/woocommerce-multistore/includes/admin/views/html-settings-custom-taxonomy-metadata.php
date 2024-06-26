<?php

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
    <div id="icon-settings" class="icon32"></div>
    <h2 class='woonet-general-setitngs-header'><?php esc_html_e( 'Custom Taxonomy & Metadata Settings', 'woonet' ); ?></h2>
    <form id="form_data" name="form" method="post">
        <table class="form-table wc-multistore-custom-tax-meta-table">
            <tbody>
                <tr>
                    <!--Custom Taxonomy-->
	                <?php if ( WOO_MULTISTORE()->settings['sync-custom-taxonomy'] == 'yes' ) : ?>
                        <td>
                            <table class="form-table wc-multistore-custom-tax-meta-table">
                                <tbody>
                                    <tr>
                                        <td colspan="2"><h2>Select the custom taxonomies you want to sync with the child sites.</h2></td>
                                    </tr>

                                    <?php if ( ! empty( $woo_mstore_custom_taxonomies ) ) : ?>

                                        <?php	$saved_taxonomy = get_site_option( 'wc_multistore_custom_taxonomy', array() ); ?>
                                        <tr>
                                            <td>Taxonomy</td>
                                            <td>Site</td>
                                        </tr>
                                        <?php foreach ( $woo_mstore_custom_taxonomies as $tax ) : ?>
                                            <tr>
                                                <td><?php echo esc_html_e( $tax ); ?></td>
                                                <td>
                                                    <a href='#' class='woonet-taxonomy-select-all-sites'> Select All </a> <br />
                                                    <?php
                                                    foreach ( WOO_MULTISTORE()->active_sites as $site ) {
                                                        if ( isset( $saved_taxonomy[ $tax ][ $site->get_id() ] ) ) {
                                                            $checked = 'checked="checked"';
                                                        } else {
                                                            $checked = '';
                                                        }

                                                        $name = "__wc_multistore_custom_taxonomy[{$tax}][{$site->get_id()}]";
                                                        ?>
                                                        <label class="wc-multistore-checkbox-label">
                                                            <input type='checkbox' name='<?php echo esc_attr( $name ); ?>' value='yes' <?php echo esc_attr( $checked ); ?>  />
                                                            <span class="checkmark"></span>
                                                        </label>
	                                                    <?php echo esc_html_e( trim( str_replace( array( 'http://', 'https://' ), '', $site->get_url() ), '/' ) ); ?>

                                                        <br />
                                                        <?php
                                                    }

                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <tr>
                                            <p style='display: inline-block;' class='notice notice-info'> No custom taxonomy is defined on your site. Once defined, they will be listed here. </p>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </td>
	                <?php endif; ?>
                    <!-- Custom Meta-->
	                <?php if ( WOO_MULTISTORE()->settings['sync-custom-metadata'] == 'yes' ) : ?>
                        <td>
                            <table class="form-table wc-multistore-custom-tax-meta-table">
                                <tbody>
                                    <tr>
                                        <td colspan="2"><h2>Select the custom meta keys you want to sync with the child sites.</h2></td>
                                    </tr>

                                    <?php if ( ! empty( $woo_mstore_custom_meta_keys ) ) : ?>

                                        <?php $saved_meta_keys = get_site_option( 'wc_multistore_custom_metadata', array() ); ?>
                                            <tr>
                                                <td>Meta Key</td>
                                                <td>Site</td>
                                            </tr>

                                        <?php foreach ( $woo_mstore_custom_meta_keys as $meta_key ) : ?>
                                            <tr>
                                                <td><?php echo esc_html_e( $meta_key ); ?></td>
                                                <td>
                                                    <a href='#' class='woonet-taxonomy-select-all-sites'> Select All </a> <br />
                                                    <?php
                                                    foreach ( WOO_MULTISTORE()->active_sites as $site ) {
                                                        if ( isset( $saved_meta_keys[ $meta_key ][ $site->get_id() ] ) ) {
                                                            $checked = 'checked="checked"';
                                                        } else {
                                                            $checked = '';
                                                        }

                                                        $name = "__wc_multistore_custom_metadata[{$meta_key}][{$site->get_id()}]";
                                                        ?>
                                                        <label class="wc-multistore-checkbox-label">
                                                            <input type='checkbox' name='<?php echo esc_attr( $name ); ?>' value='yes' <?php echo esc_attr( $checked ); ?>  />
                                                            <span class="checkmark"></span>
                                                        </label>

	                                                    <?php echo esc_html_e( trim( str_replace( array( 'http://', 'https://' ), '', $site->get_url() ), '/' ) ); ?>
                                                        <br />
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <tr>
                                            <p style='display: inline-block;' class='notice notice-info'> No custom meta key is defined on your site. Once defined, they will be listed here. </p>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </td>
	                <?php endif; ?>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary"
                   value="<?php esc_html_e( 'Save Settings', 'woonet' ); ?>">
        </p>
		<?php wp_nonce_field( 'mstore_form_submit_taxonomies', '_mstore_form_submit_taxonomies_nonce' ); ?>
    </form>
</div>
