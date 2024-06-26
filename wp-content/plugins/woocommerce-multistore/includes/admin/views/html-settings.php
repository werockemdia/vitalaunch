<div class="wrap">
    <div id="icon-settings" class="icon32"></div>
    <h2 class='woonet-general-setitngs-header'><?php esc_html_e( 'General Settings', 'woonet' ); ?></h2>

    <div class='woonet-additional-settings'>
		<?php if ( $this->settings['sync-custom-taxonomy'] == 'yes' ) : ?>
            <a class='button button-primary'
               href="<?php echo esc_url( network_admin_url( 'admin.php?page=woonet-set-taxonomy' ) ); ?>"
               class='Shipping options'>Set Taxonomy</a>
		<?php endif; ?>
		<?php if ( $this->settings['sync-custom-metadata'] == 'yes' ) : ?>
            <a class='button button-primary'
               href="<?php echo esc_url( network_admin_url( 'admin.php?page=woonet-set-taxonomy#sec-metadata' ) ); ?>"
               class='Shipping options'>Set Metadata</a>
		<?php endif; ?>
    </div>

    <form id="form_data" name="form" method="post">
        <br/>
		<?php

		// Sort sites
		if ( isset( $this->settings['blog_tab_order'] ) && $this->sites ) {
			$blog_tab_order = array();
			foreach ( $this->settings['blog_tab_order'] as $key => $blog_id ) {
				$blog_tab_order[ $blog_id ] = $blog_id;
			}
			//$this->sites = array_replace( $blog_tab_order, $this->sites );
		}

		echo '<div id="fields-control">';

		    echo '<ul>';
		            echo '<li><a href="#tabs-general">General Settings</a><input type="hidden" name="general_settings" value="general_settings" /></li>';
                    if ( $this->sites ) {
                        foreach ( $this->sites as $site ) {
                            if( ! $site->is_active() ){
                                continue;
                            }
                            echo '<li class="sortable"><a href="#tabs-'.$site->get_id().'">'.$site->get_name().' Settings </a><input type="hidden" name="blog_tab_order[]" value="'.$site->get_id().'" /></li>';
                        }
                    }
		    echo '</ul>';

		echo '<div id="tabs-general"><h3>General Settings</h3>'; ?>

        <table class="form-table">
            <tbody>

            <tr valign="top">
                <th scope="row">
                    <label class="wc-multistore-select-switch <?php if($this->settings['synchronize-by-default'] == 'yes'){ echo 'selected'; } ?>" for="synchronize-by-default">
                        <span></span>
                    </label>
                    <select class="wc-multistore-select" id="synchronize-by-default" name="synchronize-by-default">
                        <option value="yes" <?php selected( 'yes', $this->settings['synchronize-by-default'] ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( 'no', $this->settings['synchronize-by-default'] ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                    </select>
                </th>
                <td>
                    <label><?php esc_html_e( 'Synchronize new products with all child sites by default', 'woonet' ); ?>
                        <span class='tips' data-tip='<?php esc_html_e( 'When a new product is published, it is automatically synchronized with all child sites. You can still control this at a product level.', 'woonet' ); ?>'>
                            <span class="dashicons dashicons-info"></span>
                        </span>
                    </label>
                    <div>
                        <label class="wc-multistore-checkbox-label">
                            <input type="checkbox" id="inherit-by-default" name="inherit-by-default" value="yes" <?php checked( 'yes', $this->settings['inherit-by-default'] ); disabled( 'no', $this->settings['synchronize-by-default'] ); ?>>
                            <span class="checkmark"></span>
                        </label>
	                    <?php esc_html_e( 'Child product inherit Parent products changes', 'woonet' ); ?>
                    </div>
                </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['synchronize-rest-by-default'] == 'yes'){ echo 'selected'; } ?>" for="synchronize-rest-by-default">
                    <span></span>
                </label>
                <select class="wc-multistore-select" id="synchronize-rest-by-default" name="synchronize-rest-by-default">
                    <option value="no" <?php selected( 'no', $this->settings['synchronize-rest-by-default'] ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                    <option value="yes" <?php selected( 'yes', $this->settings['synchronize-rest-by-default'] ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label><?php esc_html_e( 'Synchronize new products added via API with all child sites by default', 'woonet' ); ?>
                    <span class='tips' data-tip='<?php esc_html_e( 'When a new product is published via API, it is automatically synchronized with all child sites. You can still control this at a product level.', 'woonet' ); ?>'>
                        <span class="dashicons dashicons-info"></span>
                    </span>
                </label>
                <div>
                    <label class="wc-multistore-checkbox-label">
                        <input type="checkbox" id="inherit-rest-by-default" name="inherit-rest-by-default" value="yes" <?php checked( 'yes', $this->settings['inherit-rest-by-default'] ); disabled( 'no', $this->settings['synchronize-rest-by-default'] ); ?>>
                        <span class="checkmark"></span>
                    </label>
	                <?php esc_html_e( 'Child product inherit Parent products changes', 'woonet' ); ?>
                </div>

            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['synchronize-stock'] == 'yes'){ echo 'selected'; } ?>" for="synchronize-stock">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="synchronize-stock">
                    <option value="yes" <?php selected( 'yes', $this->settings['synchronize-stock'] ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( 'no', $this->settings['synchronize-stock'] ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label><?php esc_html_e( 'Always maintain stock synchronization for re-published products', 'woonet' ); ?>
                    <span class='tips'
                          data-tip='<?php esc_html_e( 'Stock updates either manually or on checkout will also change other shops that have the product.', 'woonet' ); ?>'><span
                                class="dashicons dashicons-info"></span></span></label>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['sync-by-sku'] == 'yes'){ echo 'selected'; } ?> <?php if( $this->settings['sync-by-sku'] == 'yes' ){ echo 'disabled'; } ?>" for="sync-by-sku">
                    <span></span>
                </label>
                <select class="wc-multistore-select <?php if( $this->settings['sync-by-sku'] == 'yes' ){ echo 'disabled'; } ?>" name="sync-by-sku" >
                    <option value="yes" <?php selected( $this->settings['sync-by-sku'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( $this->settings['sync-by-sku'], 'no' ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label>
				    <?php esc_html_e( 'Sync by SKU', 'woonet' ); ?>
				    <?php if( $this->settings['sync-by-sku'] != 'yes' ): ?>
                        <span class='tips' data-tip='<?php esc_html_e( 'Choose YES if you want to switch to sync by SKU. Note that all existing product sync will be replaced by SKU sync and that any products without SKU will not sync. This choice can\'t be undone after saving.', 'woonet' ); ?>'>
                            <span class="dashicons dashicons-info"></span>
                        </span>

                        <span class='tips' data-tip='WARNING! Please read the documentation on how to use this option before you change it.'>
                            <span class="dashicons dashicons-warning wc-multistore-warning-tip"></span>
                        </span>

                        <span class='tips' data-tip='Sync by Sku Documentation'>
                            <a href="https://woomultistore.com/sku-sync-documentation/" target="_blank"><span class="dashicons dashicons-book wc-multistore-doc-tip"></span></a>
                        </span>
				    <?php else: ?>
                        <span class='tips' data-tip='Sync by sku can only be changed by resetting the plugin. All child products need to be deleted manually.'>
                            <span class="dashicons dashicons-warning wc-multistore-warning-tip"></span>
                        </span>
				    <?php endif; ?>
                </label>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['synchronize-trash'] == 'yes'){ echo 'selected'; } ?>" for="synchronize-trash">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="synchronize-trash">
                    <option value="yes" <?php selected( 'yes', $this->settings['synchronize-trash'] ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( 'no', $this->settings['synchronize-trash'] ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label><?php esc_html_e( 'Trash the child product when the parent product is trashed', 'woonet' ); ?>
                    <span class='tips'
                          data-tip='<?php esc_html_e( 'When parent product is trashed, trash the child products too.', 'woonet' ); ?>'><span
                                class="dashicons dashicons-info"></span></span></label>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['sequential-order-numbers'] == 'yes'){ echo 'selected'; } ?>" for="sequential-order-numbers">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="sequential-order-numbers">
                    <option value="yes" <?php selected( 'yes', $this->settings['sequential-order-numbers'] ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( 'no', $this->settings['sequential-order-numbers'] ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label>
				    <?php esc_html_e( 'Use sequential order numbers across the multisite environment', 'woonet' ); ?>
                    <span class='tips' data-tip='<?php esc_html_e( 'Order numbers will be created in sequence across the network for invoices and orders.', 'woonet' ); ?>'>
                      <span class="dashicons dashicons-info"></span>
                    </span>

                    <span class='tips' data-tip='<?php esc_html_e( 'WARNING! If you later deactivate this, the order numbers will revert back to the default WooCommerce order numbers.', 'woonet' ); ?>'>
                      <span class="dashicons dashicons-warning wc-multistore-warning-tip"></span>
                    </span>
                </label>
            </td>
        </tr>

        

	    <?php if( is_multisite() ): ?>
        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['network-user-info'] == 'yes'){ echo 'selected'; } ?>" for="network-user-info">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="network-user-info">
                    <option value="yes" <?php selected( $this->settings['network-user-info'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( $this->settings['network-user-info'], 'no' ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label>
				    <?php esc_html_e( 'Show customers orders from all stores in My Account', 'woonet' ); ?>
                    <span class='tips'
                          data-tip='<?php esc_html_e( 'When enabled, customers will see orders from the whole network under My Account page.', 'woonet' ); ?>'>
										  <span class="dashicons dashicons-info"></span>
									</span>
                </label>
            </td>
        </tr>
	    <?php endif; ?>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['sync-coupons'] == 'yes'){ echo 'selected'; } ?>" for="sync-coupons">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="sync-coupons">
                    <option value="yes" <?php selected( $this->settings['sync-coupons'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( $this->settings['sync-coupons'], 'no' ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label><?php esc_html_e( 'Sync coupons', 'woonet' ); ?>
                    <span class='tips'
                          data-tip='<?php esc_html_e( 'Sync coupon codes across the network.', 'woonet' ); ?>'><span
                                class="dashicons dashicons-info"></span>
									</span>
                </label>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['sync-custom-taxonomy'] == 'yes'){ echo 'selected'; } ?>" for="sync-custom-taxonomy">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="sync-custom-taxonomy">
                    <option value="yes" <?php selected( $this->settings['sync-custom-taxonomy'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( $this->settings['sync-custom-taxonomy'], 'no' ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label><?php esc_html_e( 'Sync custom taxonomy', 'woonet' ); ?>
                    <span class='tips'
                          data-tip='<?php esc_html_e( 'If enabled you can click a new button "Set Taxonomy". From there you can select which custom taxonomy will be synced with the child sites.', 'woonet' ); ?>'>
									<span class="dashicons dashicons-info"></span></span></label>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['sync-custom-metadata'] == 'yes'){ echo 'selected'; } ?>" for="sync-custom-metadata">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="sync-custom-metadata">
                    <option value="yes" <?php selected( $this->settings['sync-custom-metadata'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( $this->settings['sync-custom-metadata'], 'no' ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label><?php esc_html_e( 'Sync custom metadata ', 'woonet' ); ?>
                    <span class='tips' data-tip='<?php esc_html_e( 'If enabled you can click a new button "Set Metadata". From there you can select which custom metadata will be synced with the child sites.', 'woonet' ); ?>'>
                        <span class="dashicons dashicons-info"></span>
                    </span>
                </label>
            </td>
        </tr>



        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['enable-global-image'] == 'yes'){ echo 'selected'; } ?>" for="enable-global-image">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="enable-global-image">
                    <option value="yes" <?php selected( $this->settings['enable-global-image'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( $this->settings['enable-global-image'], 'no' ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label><?php esc_html_e( 'Enable global image', 'woonet' ); ?>
                    <span class='tips'
                          data-tip='<?php esc_html_e( 'When enabled, product images and product category images will not be uploaded on child sites. Child products and product categories will use the images uploaded on master site', 'woonet' ); ?>'><span
                                class="dashicons dashicons-info"></span>
									</span>

                    <span class='tips' data-tip='<?php esc_html_e( 'Global Image Documentation', 'woonet' ); ?>'>
                        <a href="https://woomultistore.com/global-images-for-woomultistore-woocommerce-multistore/" target="_blank"><span class="dashicons dashicons-book wc-multistore-doc-tip"></span></a>
                    </span>
                </label>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label class="wc-multistore-select-switch <?php if($this->settings['enable-order-import'] == 'yes'){ echo 'selected'; } ?>" for="enable-order-import">
                    <span></span>
                </label>
                <select class="wc-multistore-select" name="enable-order-import">
                    <option value="yes" <?php selected( $this->settings['enable-order-import'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'woonet' ); ?></option>
                    <option value="no" <?php selected( $this->settings['enable-order-import'], 'no' ); ?>><?php esc_html_e( 'No', 'woonet' ); ?></option>
                </select>
            </th>
            <td>
                <label>
				    <?php esc_html_e( 'Enable order import', 'woonet' ); ?>
                    <span class='tips' data-tip='<?php esc_html_e( 'If enabled, orders from the child sites will be imported to the main site.', 'woonet' ); ?>'>
                        <span class="dashicons dashicons-info"></span>
                    </span>
                    <span class='tips' data-tip='<?php esc_html_e( 'Import Order Documentation', 'woonet' ); ?>'>
                        <a href="https://woomultistore.com/order-import-documentation/" target="_blank"><span class="dashicons dashicons-book wc-multistore-doc-tip"></span></a>
                    </span>
                </label>
            </td>
        </tr>

            <tr valign="top">
                <th scope="row">
                    <select name="sync-method">
                        <option value="ajax" <?php selected( 'ajax', $this->settings['sync-method'] ); ?>><?php esc_html_e( 'Ajax', 'woonet' ); ?></option>
                        <option value="background" <?php selected( 'background', $this->settings['sync-method'] ); ?>><?php esc_html_e( 'Background', 'woonet' ); ?></option>
                    </select>
                </th>
                <td>
                    <label><?php esc_html_e( 'Sync Method', 'woonet' ); ?>
                        <span class='tips' data-tip='<?php esc_html_e( 'Ajax sync will sync the products instantly after a product update and will display a dialog in admin area. Background sync will display a dialog as well but the products will be scheduled to run in background. Stock sync from checkout or when orders are updated is instant in both cases.', 'woonet' ); ?>'>
                            <span class="dashicons dashicons-info"></span>
                        </span>
                    </label>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <select name="publish-capability">
                        <option value="super-admin" <?php selected( 'super-admin', $this->settings['publish-capability'] ); ?>><?php esc_html_e( 'Super Admin', 'woonet' ); ?></option>
                        <option value="administrator" <?php selected( 'administrator', $this->settings['publish-capability'] ); ?>><?php esc_html_e( 'Administrator', 'woonet' ); ?></option>
                        <option value="shop_manager" <?php selected( 'shop_manager', $this->settings['publish-capability'] ); ?>><?php esc_html_e( 'Shop Manager', 'woonet' ); ?></option>
                    </select>
                </th>
                <td>
                    <label><?php esc_html_e( 'Minimum user role to allow MultiStore Publish', 'woonet' ); ?>
                        <span class='tips'
                              data-tip='<?php esc_html_e( 'Set the user role that has access to Multisite features.', 'woonet' ); ?>'>
										  <span class="dashicons dashicons-info"></span>
									</span>
                    </label>
                </td>
            </tr>
            
        </tbody>
        </table>

        <?php echo '</div>';

		if ( $this->sites ) {
			foreach ( $this->sites as $site ) {
				if( ! $site->is_active() ){
					continue;
				}

				$blog_id       = $site->get_id();
				$blog_name     = $site->get_name();
				$site_settings = $site->get_settings();

				echo '<div id="tabs-'.$site->get_id().'"><h3>'.$site->get_name().' Settings</h3>';

				echo '<table class="form-table"><tbody>';

				/**
				 * Product settings section.
				 */
				echo '<tr valign="top"><th scope="row">';
				echo "<h2 style='font-size:1em;'> Product Settings </h2>";
				echo '</th><td> Bellow you can customize which data should be inherited by the child products.';
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__title';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Title', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__slug';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Slug', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__status';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Status', 'woonet' ),
					__( 'Product status can be: Published, Pending Review, Draft or custom product status.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__description';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Description', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__short_description';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Short description', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__featured';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Featured', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__catalogue_visibility';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Catalogue visibility', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__price';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Regular price', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__sale_price';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Sale price', 'woonet' ),
					__( 'Sale start date and sale end date are also part of this option', 'woonet' )
				);
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__product_tag';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Tags', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__default_variations';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Default Form Values ( Default Attributes )', 'woonet' ),
					__( 'Default attributes for variable products.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__sku';

				if( $this->settings['sync-by-sku'] != 'yes' ){
					echo '<tr valign="top"><th scope="row">';

					printf(
						'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
						($site_settings[ $option_name ] == 'yes') ? "selected" : "",
						"sites[{$blog_id}][{$option_name}]",
					);

					printf(
						'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
						"sites[{$blog_id}][{$option_name}]",
						selected( $site_settings[ $option_name ], 'yes', false ),
						__( 'Yes', 'woonet' ),
						selected( $site_settings[ $option_name ], 'no', false ),
						__( 'No', 'woonet' )
					);
					echo '</th><td>';
					printf(
						'<label>%s</label>',
						__( 'SKU', 'woonet' ),
//						__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
					);
					echo '</td></tr>';
                }else{
					echo '<tr valign="top"><th scope="row">';

					printf(
						'<label class="wc-multistore-select-switch disabled selected" for="%s"><span></span></label>',
						"sites[{$blog_id}][{$option_name}]",
					);

					printf(
						'<select class="wc-multistore-select" name="%s" ><option value="yes" selected = "selected">%s</option><option value="no">%s</option></select>',
						"sites[{$blog_id}][{$option_name}]",
						__( 'Yes', 'woonet' ),
						__( 'No', 'woonet' )
					);
					echo '</th><td>';
					printf(
						'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info wc-multistore-warning-tip"></span></span></label>',
						__( 'SKU', 'woonet' ),
						__( 'When sync by sku is enabled, this option is enabled by default.', 'woonet' )
					);
					echo '</td></tr>';
                }

				$option_name = 'child_inherit_changes_fields_control__product_image';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Image', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__product_gallery';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Gallery', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__reviews';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Reviews', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__purchase_note';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Purchase note', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				/**
				 * Shipping Class
				 **/
				$option_name = 'child_inherit_changes_fields_control__shipping_class';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="woomulti_option_with_warning wc-multistore-select"  name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Shipping class', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';
				/** Shipping class end **/

				$option_name = 'child_inherit_changes_fields_control__upsell';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="woomulti_option_with_warning wc-multistore-select"  name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Upsells', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__cross_sells';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="woomulti_option_with_warning wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Cross-sells', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';
				/** Sync Cross-sells end **/

				$option_name = 'child_inherit_changes_fields_control__allow_backorders';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Allow backorders.', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__menu_order';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Menu order.', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';


				/**
				 * Attributes settings section.
				 */
				echo '<tr valign="top"><th scope="row">';
				echo "<h2 style='font-size:1em;'> Attributes Settings </h2>";
				echo '</th><td>';
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__attributes';
				$are_attributes_enabled = ($site_settings[ $option_name ] == 'yes') ? '' : 'hidden' ;

				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select wc-multistore-attributes-settings-switch" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Attributes', 'woonet' ),
					__( 'Enable or disable attributes. Attributes are required for variations to sync', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__attribute_name';
				echo '<tr valign="top" class="wc-multistore-attributes-settings '.$are_attributes_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
                    '<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Attribute name', 'woonet' ),
					__( 'This option only has effect on subsequent attribute sync. When the attribute is created the name will sync automatically as it is a required field.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__attribute_term_name';
				echo '<tr valign="top" class="wc-multistore-attributes-settings '.$are_attributes_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Attribute term name', 'woonet' ),
					__( 'This option only has effect on subsequent attribute term sync. When the attribute term is created the name will sync automatically as it is a required field.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__attribute_term_slug';
				echo '<tr valign="top" class="wc-multistore-attributes-settings '.$are_attributes_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Attribute term slug', 'woonet' ),
					__( 'This option only has effect on subsequent attribute term sync. When the attribute term is created the name will sync automatically as it is a required field.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__attribute_term_description';
				echo '<tr valign="top" class="wc-multistore-attributes-settings '.$are_attributes_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Attribute term description', 'woonet' ),
				);
				echo '</td></tr>';

				/**
				 * Variations settings section.
				 */
				echo '<tr valign="top"><th scope="row">';
				echo "<h2 style='font-size:1em;'> Variations Settings </h2>";
				echo '</th><td>';
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__variations';
				$are_variations_enabled = ($site_settings[ $option_name ] == 'yes') ? '' : 'hidden' ;
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select wc-multistore-variations-settings-switch" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Variations', 'woonet' ),
					__( 'Enable or disable variations', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__variations_data';
				echo '<tr valign="top" class="wc-multistore-variations-settings '.$are_variations_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Data', 'woonet' ),
					__( 'Data refers to all variation data with the exception of status, stock, sku, regular price and sale price which have separate settings bellow', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__variations_status';
				echo '<tr valign="top" class="wc-multistore-variations-settings  '.$are_variations_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Status', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit parent variations</b> being active.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__variations_stock';
				echo '<tr valign="top" class="wc-multistore-variations-settings '.$are_variations_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Stock', 'woonet' ),
					__( 'Manage stock, stock quantity, stock status, low stock amount and allow backorders are also a part of this option', 'woonet' )
				);
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__variations_sku';

				if( $this->settings['sync-by-sku'] != 'yes' ){
					echo '<tr valign="top" class="wc-multistore-variations-settings '.$are_variations_enabled.'"><th scope="row">';

					printf(
						'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
						($site_settings[ $option_name ] == 'yes') ? "selected" : "",
						"sites[{$blog_id}][{$option_name}]",
					);

					printf(
						'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
						"sites[{$blog_id}][{$option_name}]",
						selected( $site_settings[ $option_name ], 'yes', false ),
						__( 'Yes', 'woonet' ),
						selected( $site_settings[ $option_name ], 'no', false ),
						__( 'No', 'woonet' )
					);
					echo '</th><td>';
					printf(
						'<label>%s</label>',
						__( 'SKU', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit parent variations</b> being active.', 'woonet' )
					);
					echo '</td></tr>';
                }else{
					echo '<tr valign="top" class="wc-multistore-variations-settings '.$are_variations_enabled.'"><th scope="row">';

					printf(
						'<label class="wc-multistore-select-switch disabled selected" for="%s"><span></span></label>',
						"sites[{$blog_id}][{$option_name}]",
					);

					printf(
						'<select class="wc-multistore-select disabled"  name="%s"><option value="yes" selected = "selected">%s</option><option value="no">%s</option></select>',
						"sites[{$blog_id}][{$option_name}]",
						__( 'Yes', 'woonet' ),
						__( 'No', 'woonet' )
					);
					echo '</th><td>';
					printf(
						'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info wc-multistore-warning-tip"></span></span></label>',
						__( 'SKU', 'woonet' ),
					__( 'When sync by sku is enabled, this option is enabled by default.', 'woonet' )
					);
					echo '</td></tr>';
                }

				$option_name = 'child_inherit_changes_fields_control__variations_price';
				echo '<tr valign="top" class="wc-multistore-variations-settings '.$are_variations_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Regular price', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit parent variations</b> being active.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__variations_sale_price';
				echo '<tr valign="top" class="wc-multistore-variations-settings '.$are_variations_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Sale price', 'woonet' ),
					__( 'Sale start date and sale end date are also part of this option', 'woonet' )
				);
				echo '</td></tr>';

				/**
				 * Categories settings section.
				 */
				echo '<tr valign="top"><th scope="row">';
				echo "<h2 style='font-size:1em;'> Categories Settings </h2>";
				echo '</th><td>';
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__product_cat';
				$are_categories_enabled = ($site_settings[ $option_name ] == 'yes') ? '' : 'hidden' ;

				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select wc-multistore-categories-settings-switch" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Categories', 'woonet' ),
					__( 'Enable or disable categories', 'woonet' )
				);
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__category_name';
				echo '<tr valign="top" class="wc-multistore-categories-settings '.$are_categories_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Name', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__category_slug';
				echo '<tr valign="top" class="wc-multistore-categories-settings '.$are_categories_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Slug', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__category_description';
				echo '<tr valign="top" class="wc-multistore-categories-settings '.$are_categories_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Description', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';


				$option_name = 'child_inherit_changes_fields_control__category_image';
				echo '<tr valign="top" class="wc-multistore-categories-settings '.$are_categories_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Image', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__category_meta';
				echo '<tr valign="top" class="wc-multistore-categories-settings '.$are_categories_enabled.'"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s</label>',
					__( 'Meta data', 'woonet' ),
//					__( 'This works in conjunction with <b>Child product inherit Parent products changes</b> being active on individual product page.', 'woonet' )
				);
				echo '</td></tr>';

				/**
				 * REST API settings section.
				 */
				echo '<tr valign="top"><th scope="row">';
				echo "<h2 style='font-size:1em;'> REST API Settings </h2>";
				echo '</th><td>';
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__synchronize_rest_by_default';

				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Synchronize new products added via API', 'woonet' ),
					__( 'Enable or disable this global option for this site.', 'woonet' )
				);
				echo '</td></tr>';

				/**
				 * Import Order settings section.
				 */
				echo '<tr valign="top"><th scope="row">';
				echo "<h2 style='font-size:1em;'> Import Order Settings </h2>";
				echo '</th><td>';
				echo '</td></tr>';

				$option_name = 'child_inherit_changes_fields_control__import_order';

				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Import Order', 'woonet' ),
					__( 'Enable or disable this global option for this site.', 'woonet' )
				);
				echo '</td></tr>';

				/**
				 * Override default settings section.
				 */
				echo '<tr valign="top"><th scope="row">';
				echo "<h2 style='font-size:1em;'> Override General Settings </h2>";
				echo '</th><td>';
				echo '</td></tr>';

				/**
				 * Override general settings
				 */
				/** stock sync */
				$option_name = 'override__synchronize-stock';
				echo '<tr valign="top"><th scope="row">';

				printf(
					'<label class="wc-multistore-select-switch %s" for="%s"><span></span></label>',
					($site_settings[ $option_name ] == 'yes') ? "selected" : "",
					"sites[{$blog_id}][{$option_name}]",
				);

				printf(
					'<select class="wc-multistore-select" name="%s"><option value="yes" %s>%s</option><option value="no" %s>%s</option></select>',
					"sites[{$blog_id}][{$option_name}]",
					selected( $site_settings[ $option_name ], 'yes', false ),
					__( 'Yes', 'woonet' ),
					selected( $site_settings[ $option_name ], 'no', false ),
					__( 'No', 'woonet' )
				);
				echo '</th><td>';
				printf(
					'<label>%s<span class="tips" data-tip="%s"><span class="dashicons dashicons-info"></span></span></label>',
					__( 'Disable stock sync.', 'woonet' ),
					__( 'Disable stock sync for this site.', 'woonet' )
				);
				echo '</td></tr>';
				/** end override stock sync */

				do_action( 'woo_mstore/options/options_output/child_inherit_changes_fields_control', $blog_id );

				echo '</tbody></table>';

				echo '</div>';
			}

		}

		echo '</div>';

		?>

		<?php do_action( 'woo_mstore/options/options_output' ); ?>

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary"
                   value="<?php esc_html_e( 'Save Settings', 'woonet' ); ?>">
        </p>

		<?php wp_nonce_field( 'mstore_form_submit', 'mstore_form_nonce' ); ?>
        <input type="hidden" name="wc_multistore_form_submit" value="true"/>

    </form>
</div>