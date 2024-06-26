<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that add network admin menu.
 * Menus are wcmps and Settings.
 */
if ( ! function_exists( 'wcmps_add_network_admin_menu' ) ) {
    add_action( 'network_admin_menu', 'wcmps_add_network_admin_menu' );
    function wcmps_add_network_admin_menu() {
        
        add_menu_page( esc_html__( 'WooCommerce Multisite Product Sync', 'wcmps' ), esc_html__( 'Product Sync', 'wcmps' ), 'manage_options', 'wcmps', 'wcmps_bulk_sync_callback', 'dashicons-update' );
        add_submenu_page( 'wcmps', esc_html__( 'Product Sync: Bulk Sync', 'wcmps' ), esc_html__( 'Bulk Sync', 'wcmps' ), 'manage_options', 'wcmps', 'wcmps_bulk_sync_callback' );
        add_submenu_page( 'wcmps', esc_html__( 'Product Sync: Settings', 'wcmps' ), esc_html__( 'Settings', 'wcmps' ), 'manage_options', 'wcmps-settings', 'wcmps_settings_callback' );
        add_submenu_page( 'wcmps', esc_html__( 'Licence Verification', 'wcmps' ), esc_html__( 'Licence Verification', 'wcmps' ), 'manage_options', 'wcmps_licence_verification', 'wcmps_licence_verification' );
    }
}

/*
 * This is a function that call plugin settings.
 */
if ( ! function_exists( 'wcmps_settings_callback' ) ) {
    function wcmps_settings_callback() {
        
        global $wpdb;
        
        $current_blog_id = get_current_blog_id();
        $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );
        if ( isset( $_POST['wcmps_submit'] ) ) {
            if ( isset( $_POST['wcmps_auto_sync'] ) ) {
                update_site_option( 'wcmps_auto_sync', (int) $_POST['wcmps_auto_sync'] );
            }

            if ( isset( $_POST['wcmps_auto_sync_type'] ) ) {
                update_site_option( 'wcmps_auto_sync_type', sanitize_text_field( $_POST['wcmps_auto_sync_type'] ) );
            }
            
            if ( isset( $_POST['wcmps_auto_sync_sub_blogs'] ) ) {
                if ( is_array( $_POST['wcmps_auto_sync_sub_blogs'] ) && $_POST['wcmps_auto_sync_sub_blogs'] != null ) {
                    foreach ( $_POST['wcmps_auto_sync_sub_blogs'] as $key => $value ) {
                        $_POST['wcmps_auto_sync_sub_blogs'][$key] = (int) $value;
                    }

                    update_site_option( 'wcmps_auto_sync_sub_blogs', $_POST['wcmps_auto_sync_sub_blogs'] );
                } else {
                    update_site_option( 'wcmps_auto_sync_sub_blogs', (int) $_POST['wcmps_auto_sync_sub_blogs'] );
                }
            }
            
            if ( isset( $_POST['wcmps_auto_sync_main_blog'] ) ) {
                update_site_option( 'wcmps_auto_sync_main_blog', (int) $_POST['wcmps_auto_sync_main_blog'] );
            }

            if ( isset( $_POST['wcmps_stock_sync'] ) ) {
                update_site_option( 'wcmps_stock_sync', (int) $_POST['wcmps_stock_sync'] );
            }

            if ( isset( $_POST['wcmps_stock_sync_status'] ) ) {
                update_site_option( 'wcmps_stock_sync_status', sanitize_text_field( $_POST['wcmps_stock_sync_status'] ) );
            }

            if ( isset( $_POST['wcmps_old'] ) ) {
                update_site_option( 'wcmps_old', (int) $_POST['wcmps_old'] );
            }

            if ( isset( $_POST['wcmps_product_delete'] ) ) {
                update_site_option( 'wcmps_product_delete', (int) $_POST['wcmps_product_delete'] );
            }

            if ( isset( $_POST['wcmps_exclude_product_meta_data'] ) ) {
                update_site_option( 'wcmps_exclude_product_meta_data', sanitize_text_field( $_POST['wcmps_exclude_product_meta_data'] ) );
            }

            if ( $sites != null ) {
                foreach ( $sites as $key => $value ) {
                    $blog_id = $value->blog_id;
                    if ( $blog_id != $current_blog_id ) {
                        switch_to_blog( $blog_id );
                    }

                    global $wpdb;

                    $args = array(
                        'post_type'         => 'acf-field-group',
                        'post_status'       => 'publish',
                        'posts_per_page'    => -1,
                    );
                    $posts = get_posts( $args );
                    if ( $posts != null ) {
                        foreach ( $posts as $post ) {
                            $post_id = $post->ID;
                            $fields = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."posts WHERE post_parent=".$post_id );
                            if ( $fields != null ) {
                                $types = array(
                                    'image',
                                    'file',
                                    'page_link',
                                    'post_object',
                                    'relationship',
                                    'taxonomy',
                                    'gallery',
                                );
                
                                foreach ( $fields as $field ) {
                                    $filed_key = $field->post_excerpt;
                                    $field_data = unserialize( $field->post_content );
                                    $cf = $wpdb->get_row( "SELECT * FROM ".$wpdb->base_prefix."wcmps_cf WHERE filed_key='$filed_key'" );                
                                    if ( in_array( $field_data['type'], $types ) ) {                      
                                        if ( $cf != null ) {
                                            $wpdb->update(
                                                $wpdb->base_prefix . 'wcmps_cf',
                                                array( 
                                                    'filed_key'     => $field->post_excerpt,
                                                    'field_type'    => $field_data['type'],
                                                    'field_data'    => $field->post_content,                           
                                                ),
                                                array( 
                                                    'id' => $cf->id, 
                                                )
                                            );
                                        } else {                    
                                            $wpdb->insert(
                                                $wpdb->base_prefix . 'wcmps_cf',
                                                array( 
                                                    'filed_key'     => $field->post_excerpt,
                                                    'field_type'    => $field_data['type'],
                                                    'field_data'    => $field->post_content,                             
                                                )
                                            ); 
                                        }
                                    } else {
                                        if ( $cf != null ) {
                                            $wpdb->delete( 
                                                $wpdb->base_prefix . 'wcmps_cf',
                                                array( 
                                                    'id' => $cf->id, 
                                                )
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ( $blog_id != $current_blog_id ) {
                        restore_current_blog();
                    }
                }
            }

            ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e( 'Settings saved.', 'wcmps' ); ?></p>
                </div>
            <?php
        }
        
        $wcmps_auto_sync = get_site_option( 'wcmps_auto_sync' );
        $wcmps_auto_sync_type = get_site_option( 'wcmps_auto_sync_type' );
        $wcmps_auto_sync_main_blog = get_site_option( 'wcmps_auto_sync_main_blog' );
        $wcmps_auto_sync_sub_blogs = get_site_option( 'wcmps_auto_sync_sub_blogs' );
        if ( ! $wcmps_auto_sync_sub_blogs || $wcmps_auto_sync_sub_blogs == null ) {
            $wcmps_auto_sync_sub_blogs = array();
        }
        
        $wcmps_stock_sync = get_site_option( 'wcmps_stock_sync' );
        $wcmps_stock_sync_status = get_site_option( 'wcmps_stock_sync_status' );        
        $wcmps_licence = get_site_option( 'wcmps_licence' );
        $wcmps_old = get_site_option( 'wcmps_old' );
        $product_delete = get_site_option( 'wcmps_product_delete' );
        $exclude_product_meta_data = get_site_option( 'wcmps_exclude_product_meta_data' );
        ?>
        <div class="wrap">      
            <h2><?php esc_html_e( 'Settings', 'wcmps' ); ?></h2>
            <hr>
            <?php
                if ( $wcmps_licence ) {
                    ?>
                        <form method="post">                
                            <table class="form-table">
                                <tbody>                        
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Auto Sync?', 'wcmps' ); ?></th>
                                        <td>
                                            <input type="hidden" name="wcmps_auto_sync" value="0" />
                                            <input type="checkbox" name="wcmps_auto_sync" value="1"<?php echo ( $wcmps_auto_sync ? ' checked="checked"' : '' ); ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label><?php esc_html_e( 'Auto Sync Type', 'wcmps' ); ?></label></th>
                                        <td>
                                            <fieldset>
                                                <label>
                                                    <input type="radio" name="wcmps_auto_sync_type" value="all-sites"<?php echo ( $wcmps_auto_sync_type == 'all-sites' ? ' checked="checked"' : '' ); ?> /><?php esc_html_e( 'All sites', 'wcmps' ); ?>
                                                </label>
                                                <label>
                                                    <input type="radio" name="wcmps_auto_sync_type" value="main-site-to-sub-sites"<?php echo ( $wcmps_auto_sync_type == 'main-site-to-sub-sites' ? ' checked="checked"' : '' ); ?> /><?php esc_html_e( 'Main site to sub sites', 'wcmps' ); ?>
                                                </label>
                                                <label>
                                                    <input type="radio" name="wcmps_auto_sync_type" value="sub-sites-to-main-site"<?php echo ( $wcmps_auto_sync_type == 'sub-sites-to-main-site' ? ' checked="checked"' : '' ); ?> /><?php esc_html_e( 'Sub site to main site', 'wcmps' ); ?>
                                                </label>
                                            </fieldset>                                
                                        </td>
                                    </tr>                        
                                    <tr class="wcmps-hide-show"<?php echo ( ( $wcmps_auto_sync_type == 'sub-sites-to-main-site' || $wcmps_auto_sync_type == 'all-sites' ) ? ' style="display:none"' : '' );?>>
                                        <th scope="row"><?php esc_html_e( 'Sub Sites', 'wcmps' ); ?></th>
                                        <td>
                                            <label><input class="wcmps-check-uncheck" type="checkbox" /><?php esc_html_e( 'All', 'wcmps' ); ?></label>
                                            <p class="description"><?php esc_html_e( 'Select/Deselect all sites.', 'wcmps' ); ?></p>
                                            <br>
                                            <fieldset class="wcmps-sites">  
                                                <input type="hidden" name="wcmps_auto_sync_sub_blogs" value="0" />
                                                <?php
                                                    if ( $sites != null ) {
                                                        foreach ( $sites as $key => $value ) { 
                                                            if ( ! is_main_site( $value->blog_id ) ) {
                                                                $blog_details = get_blog_details( $value->blog_id );
                                                                ?>
                                                                    <label><input name="wcmps_auto_sync_sub_blogs[]" type="checkbox" value="<?php echo intval( $value->blog_id ); ?>"<?php echo ( in_array( $value->blog_id, $wcmps_auto_sync_sub_blogs ) ? ' checked="checked"' : '' ); ?>><?php echo $value->domain; echo $value->path; echo ' ('.$blog_details->blogname.')'; ?></label><br>
                                                                <?php
                                                            } else {
                                                                ?><input type="hidden" name="wcmps_auto_sync_main_blog" value="<?php echo intval( $value->blog_id ); ?>"/><?php
                                                            }
                                                        }
                                                    }
                                                ?>                                                                          				
                                            </fieldset>                                
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Stock Sync?', 'wcmps' ); ?></th>
                                        <td>
                                            <input type="hidden" name="wcmps_stock_sync" value="0" />
                                            <input type="checkbox" name="wcmps_stock_sync" value="1"<?php echo ( $wcmps_stock_sync ? ' checked="checked"' : '' ); ?> />                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Stock Sync On', 'wcmps' ); ?></th>
                                        <td>
                                            <fieldset>
                                                <label>
                                                    <input type="radio" name="wcmps_stock_sync_status" value="processing"<?php echo ( $wcmps_stock_sync_status == 'processing' ? ' checked="checked"' : '' ); ?> /><?php esc_html_e( 'Order Processing', 'wcmps' ); ?>
                                                </label>
                                                <label>
                                                    <input type="radio" name="wcmps_stock_sync_status" value="completed"<?php echo ( $wcmps_stock_sync_status == 'completed' ? ' checked="checked"' : '' ); ?> /><?php esc_html_e( 'Order Completed', 'wcmps' ); ?>
                                                </label>                                                
                                            </fieldset> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Old Products Check?', 'wcmps' ); ?></th>
                                        <td>
                                            <input type="hidden" name="wcmps_old" value="0" />
                                            <input type="checkbox" name="wcmps_old" value="1"<?php echo ( $wcmps_old ? ' checked="checked"' : '' ); ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Sync On Product Delete?', 'wcmps' ); ?></th>
                                        <td>
                                            <input type="hidden" name="wcmps_product_delete" value="0" />
                                            <input type="checkbox" name="wcmps_product_delete" value="1"<?php echo ( $product_delete ? ' checked="checked"' : '' ); ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Exclude Product Meta Data', 'wcmps' ); ?></th>
                                        <td>
                                            <input type="text" name="wcmps_exclude_product_meta_data" value="<?php echo esc_html( $exclude_product_meta_data ); ?>" class="regular-text" />
                                            <p class="description"><?php esc_html_e( 'Add product meta key by comma separated.', 'wcmps' ); ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="submit"><input name="wcmps_submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'wcmps' ); ?>" type="submit"></p>
                        </form>
                        <script type="text/javascript">
                            jQuery( document ).ready( function( $ ) { 
                                $( '.wcmps-check-uncheck' ).on( 'change', function() {
                                    var checked = $( this ).prop( 'checked' );
                                    $( '.wcmps-sites input[type="checkbox"]' ).each( function() {
                                        if ( checked ) {
                                            $( this ).prop( 'checked', true );
                                        } else {
                                            $( this ).prop( 'checked', false );
                                        }
                                    });                   
                                });

                                $( 'input[type="radio"][name="wcmps_auto_sync_type"]' ).on( 'change', function() {
                                    var type = $( this ).val();
                                    if ( type == 'main-site-to-sub-sites' ) {
                                        $( '.wcmps-hide-show' ).show();     
                                    } else {
                                        $( '.wcmps-hide-show' ).hide();
                                    }
                                });   
                            });
                        </script>
                    <?php
                } else {
                    ?>
                        <div class="notice notice-error is-dismissible">
                            <p><?php esc_html_e( 'Please verify purchase code.', 'wcmps' ); ?></p>
                        </div>
                    <?php
                }
            ?>
        </div>
        <?php
    }
}

/*
 * This is a function that verify product licence.
 */
if ( ! function_exists( 'wcmps_licence_verification' ) ) {
    function wcmps_licence_verification() {
        
        if ( isset( $_POST['verify'] ) ) {
            if ( isset( $_POST['wcmps_purchase_code'] ) ) {
                update_site_option( 'wcmps_purchase_code', sanitize_text_field( $_POST['wcmps_purchase_code'] ) );
                
                $data = array(
                    'sku'           => '20137238',
                    'purchase_code' => $_POST['wcmps_purchase_code'],
                    'domain'        => site_url(),
                    'status'        => 'verify',
                    'type'          => 'oi',
                );

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, 'https://www.obtaininfotech.com/extension/' );
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
                $json_response = curl_exec( $ch );
                curl_close ($ch);
                
                $response = json_decode( $json_response );
                if ( isset( $response->success ) ) {
                    if ( $response->success ) {
                        update_site_option( 'wcmps_licence', 1 );
                    }
                }
            }
        } else if ( isset( $_POST['unverify'] ) ) {
            if ( isset( $_POST['wcmps_purchase_code'] ) ) {
                $data = array(
                    'sku'           => '20137238',
                    'purchase_code' => $_POST['wcmps_purchase_code'],
                    'domain'        => site_url(),
                    'status'        => 'unverify',
                    'type'          => 'oi',
                );

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, 'https://www.obtaininfotech.com/extension/' );
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
                $json_response = curl_exec( $ch );
                curl_close ($ch);

                $response = json_decode( $json_response );
                if ( isset( $response->success ) ) {
                    if ( $response->success ) {
                        update_site_option( 'wcmps_purchase_code', '' );
                        update_site_option( 'wcmps_licence', 0 );
                    }
                }
            }
        }    
        
        $wcmps_purchase_code = get_site_option( 'wcmps_purchase_code' );
        ?>
            <div class="wrap">      
                <h2><?php esc_html_e( 'Licence Verification', 'wcmps' ); ?></h2>
                <hr>
                <?php
                    if ( isset( $response->success ) ) {
                        if ( $response->success ) {                            
                             ?>
                                <div class="notice notice-success is-dismissible">
                                    <p><?php echo $response->message; ?></p>
                                </div>
                            <?php
                        } else {
                            update_site_option( 'wcmps_licence', 0 );
                            ?>
                                <div class="notice notice-error is-dismissible">
                                    <p><?php echo $response->message; ?></p>
                                </div>
                            <?php
                        }
                    }
                ?>
                <form method="post">
                    <table class="form-table">                    
                        <tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Purchase Code', 'wcmps' ); ?></th>
                                <td>
                                    <input name="wcmps_purchase_code" type="text" class="regular-text" value="<?php echo esc_html( $wcmps_purchase_code ); ?>" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p>
                        <input type='submit' class='button-primary' name="verify" value="<?php esc_html_e( 'Verify', 'wcmps' ); ?>" />
                        <input type='submit' class='button-primary' name="unverify" value="<?php esc_html_e( 'Unverify', 'wcmps' ); ?>" />
                    </p>
                </form>   
            </div>
        <?php
    }
}

if ( ! function_exists( 'wcmps_bulk_sync_callback' ) ) {
    function wcmps_bulk_sync_callback() {
        
        global  $wpdb;
        
        $current_blog_id = get_current_blog_id();
        $page_url = network_admin_url( '/admin.php?page=wcmps' );        
        $wcmps_content_type = 'product';
        $wcmps_source_blog = ( isset( $_REQUEST['wcmps_source_blog'] ) ? (int) $_REQUEST['wcmps_source_blog'] : 0 );
        $wcmps_record_per_page = ( isset( $_REQUEST['wcmps_record_per_page'] ) ? (int) $_REQUEST['wcmps_record_per_page'] : 10 );        
        $wcmps_records = ( isset( $_REQUEST['wcmps_records'] ) ? $_REQUEST['wcmps_records'] : array() );
        $wcmps_destination_blogs = ( isset( $_REQUEST['wcmps_destination_blogs'] ) ? $_REQUEST['wcmps_destination_blogs'] : array() );
        $copy_media = ( isset( $_REQUEST['copy_media'] ) ? (int) $_REQUEST['copy_media'] : 0 );
        $copy_terms = ( isset( $_REQUEST['copy_terms'] ) ? (int) $_REQUEST['copy_terms'] : 0 );
        
        if ( isset( $_REQUEST['wcmps_submit'] ) ) {
            if ( $wcmps_records != null ) {
                foreach ( $wcmps_records as $wcmps_record ) {
                    $source_blog_id = $wcmps_source_blog;            
                    $source_item_id = (int) $wcmps_record;
                    $type = 'post_type';
                    $type_name = $wcmps_content_type;
                    $blogs = $wcmps_destination_blogs;
                    
                    $wcmps_disable = get_post_meta( $source_item_id, 'wcmps_disable', true );
                    if ( ! $wcmps_disable ) {
                        $wcmps_obj = new WCMPS();
                        
                        if ( $source_blog_id != $current_blog_id ) {                
                            switch_to_blog( $source_blog_id );
                        }
                        
                        $taxonomies = array();
                        $taxonomy_objects = get_object_taxonomies( $type_name );
                        if ( $taxonomy_objects != null && $copy_terms ) {
                            foreach ( $taxonomy_objects as $taxonomy_object ) {
                                $post_terms = wp_get_post_terms( $source_item_id, $taxonomy_object );
                                if ( $post_terms ) {
                                    $taxonomies[$taxonomy_object] = $post_terms;
                                }
                            }                    
                        }
                        
                        foreach ( $blogs as $blog ) {
                            $destination_blog_id = (int) $blog;
                            $destination_post_id = $wcmps_obj->copy_post( $source_item_id, $source_blog_id, $type, $type_name, $destination_blog_id, $copy_media, $copy_terms );

                            if ( $taxonomies != null && $destination_post_id ) {
                                foreach ( $taxonomies as $taxonomy => $terms ) {
                                    if ( $terms != null ) {
                                        $destination_terms = array();
                                        foreach ( $terms as $term ) {
                                            $destination_term_id = $wcmps_obj->copy_term( $term, $source_blog_id, 'taxonomy', $taxonomy, $destination_blog_id );
                                            if ( $destination_term_id ) {
                                                $destination_terms[] = (int) $destination_term_id;
                                            }
                                        }

                                        if ( $destination_terms != null ) {
                                            $wcmps_obj->set_destination_post_terms( $destination_post_id, $destination_terms, $taxonomy, $destination_blog_id );
                                        }
                                    }
                                }
                            }
                        }
                    
                        if ( $source_blog_id != $current_blog_id ) {                
                            restore_current_blog();
                        }
                    }
                }
                
                ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e( 'Products successfully synced.', 'wcmps' ); ?></p>
                    </div>
                <?php
            }
        }
        
        $wcmps_licence = get_site_option( 'wcmps_licence' );
        ?>
            <div class="wrap">      
                <h2><?php esc_html_e( 'Bulk Sync', 'wcmps' ); ?></h2>
                <hr>
                <?php
                    if ( $wcmps_licence ) {
                        ?>
                            <form method="post" action="<?php echo $page_url; ?>">                
                                <table class="form-table">
                                    <tbody>                        
                                        <tr>
                                            <th scope="row"><?php esc_html_e( 'Source Site', 'wcmps' ); ?></th>
                                            <td>     
                                                <select name="wcmps_source_blog" required="required">
                                                <?php
                                                    $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );
                                                    $blog_list = array();
                                                    if ( $sites != null ) {
                                                        ?><option value=""><?php esc_html_e( 'Select source site', 'wcmps' ); ?></option><?php
                                                        foreach ( $sites as $key => $value ) {
                                                            $blog_list[$value->blog_id] = $value->domain;
                                                            $selected = '';
                                                            if ( $wcmps_source_blog == $value->blog_id ) {
                                                                $selected = ' selected="$selected"';
                                                            }

                                                            $blog_details = get_blog_details( $value->blog_id );                                            
                                                            ?>
                                                                <option value="<?php echo intval( $value->blog_id ); ?>"<?php echo $selected; ?>><?php echo $value->domain; echo $value->path; echo ' ('.$blog_details->blogname.')'; ?></option>                                                
                                                            <?php
                                                        }
                                                    }
                                                ?> 
                                                </select>
                                            </td>
                                        </tr>    
                                        <tr>
                                            <th scope="row"><?php esc_html_e( 'Number of products per page', 'wcmps' ); ?></th>
                                            <td>
                                                <select name="wcmps_record_per_page">
                                                <?php 
                                                    $number_options = array( 5, 10, 25, 50 );
                                                    foreach ( $number_options as $number_option ) {
                                                        $selected = '';
                                                        if ( $wcmps_record_per_page == $number_option ) {
                                                            $selected = ' selected="$selected"';
                                                        }
                                                        ?><option value="<?php echo intval( $number_option ); ?>"<?php echo $selected; ?>><?php echo $number_option; ?></option><?php
                                                    }
                                                ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="submit">
                                    <input name="submit" class="button button-secondary" value="<?php esc_html_e( 'Filter', 'wcmps' ); ?>" type="submit">
                                    &nbsp;&nbsp;&nbsp;&nbsp;<a class="button button-secondary" href="<?php echo $page_url; ?>"><?php esc_html_e( 'Clear', 'wcmps' ); ?></a>
                                </p>
                            </form>
                        <?php
                    } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p><?php esc_html_e( 'Please verify purchase code.', 'wcmps' ); ?></p>
                            </div>
                        <?php
                    }
                    
                    if ( $wcmps_content_type && $wcmps_source_blog ) {
                        if ( $wcmps_source_blog != get_current_blog_id() ) {
                            $wcmps_source_blog = (int) $wcmps_source_blog;
                            switch_to_blog( $wcmps_source_blog );
                        }

                        $paged = ( isset( $_REQUEST['paged'] ) ) ? (int) $_REQUEST['paged'] : 1;
                        $args = array(
                            'post_type'         => $wcmps_content_type,
                            'posts_per_page'    => $wcmps_record_per_page,
                            'paged'             => $paged,
                        );

                        $add_args = array(                       
                            'wcmps_source_blog'      => $wcmps_source_blog,
                            'wcmps_record_per_page'  => $wcmps_record_per_page,
                        );

                        if ( isset( $_REQUEST['s'] ) ) {
                            $args['s'] = sanitize_text_field( $_REQUEST['s'] );
                            $add_args['s'] = sanitize_text_field( $_REQUEST['s'] );
                        }
                        
                        $records = new WP_Query( $args );
                        ?>
                        <form method="post">
                            <p class="search-box wcmps-search-box">
                                <label class="screen-reader-text" for="post-search-input"><?php esc_html_e( 'Search Products:', 'wcmps' ); ?></label>
                                <input id="post-search-input" name="s" value="<?php echo ( isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : ''  ); ?>" type="search">
                                <input id="search-submit" class="button" value="<?php esc_html_e( 'Search Products', 'wcmps' ); ?>" type="submit">
                            </p>                       
                            <table class="wp-list-table widefat fixed striped">
                                <thead>
                                    <tr>
                                        <td class="manage-column column-cb check-column"><input type="checkbox"></td>
                                        <th><?php esc_html_e( 'Title', 'wcmps' ); ?></th>                                  
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td class="manage-column column-cb check-column"><input type="checkbox"></td>
                                        <th><?php esc_html_e( 'Title', 'wcmps' ); ?></th>                                   
                                    </tr>
                                </tfoot>
                                <tbody>
                                <?php
                                    if ( $records->have_posts() ) {
                                        while ( $records->have_posts() ) {
                                            $records->the_post();
                                            ?>
                                            <tr>
                                                <th class="check-column"><input type="checkbox" name="wcmps_records[]" value="<?php echo get_the_ID(); ?>"></th>
                                                <td class="title column-title page-title">
                                                    <strong><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></strong>
                                                    <?php
                                                    $type = 'post_type';
                                                    $type_name = get_post_type();
                                                    $current_blog_id = get_current_blog_id();
                                                    $current_item_id = get_the_ID();
                                                    $current_relationship = $wpdb->get_row( "SELECT * FROM ".$wpdb->base_prefix."wcmps_relationships WHERE type='$type' AND type_name='$type_name' AND ((source_item_id='$current_item_id' AND source_blog_id='$current_blog_id') || (destination_item_id='$current_item_id' AND destination_blog_id='$current_blog_id'))" );
                                                    $blog_relationships = array();
                                                    if ( $current_relationship != null ) {
                                                        $relationship_id = $current_relationship->relationship_id;
                                                        $relationships = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."wcmps_relationships WHERE relationship_id='$relationship_id'");
                                                        if ( $relationships != null ) {
                                                            foreach ( $relationships as $relationship ) {
                                                                if ( $current_blog_id == $relationship->source_blog_id && $current_item_id == $relationship->source_item_id ) {
                                                                    $blog_relationships[$relationship->destination_blog_id] = $relationship->destination_item_id;
                                                                } else if ( $current_blog_id == $relationship->destination_blog_id && $current_item_id == $relationship->destination_item_id ) {
                                                                    $blog_relationships[$relationship->source_blog_id] = $relationship->source_item_id;
                                                                } else {
                                                                    if ( ! isset( $blog_relationships[$relationship->source_blog_id] ) ) {
                                                                        $blog_relationships[$relationship->source_blog_id] = $relationship->source_item_id;
                                                                    }

                                                                    if ( ! isset( $blog_relationships[$relationship->destination_blog_id] ) ) {
                                                                        $blog_relationships[$relationship->destination_blog_id] = $relationship->destination_item_id;
                                                                    }
                                                                }
                                                            }
                                                        }                            
                                                    }
                                                    if ( $blog_relationships != null ) {
                                                        echo '<b>'; esc_html_e( 'Synced: ', 'wcmps' ); echo '</b>';
                                                        $count_blog_list = count( $blog_relationships );
                                                        $count_blog = 0;
                                                        foreach ( $blog_relationships as $key => $value ) {
                                                            $blog_details = get_blog_details( $key );
                                                            echo $blog_list[$key]; echo $blog_details->path; echo ' ('.$blog_details->blogname.')';
                                                            if ( $count_blog != ( $count_blog_list - 1) ) {
                                                                echo ', ';
                                                            }
                                                            $count_blog ++;
                                                        }
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                        <tr class="no-items">                                       
                                            <td class="colspanchange" colspan="2"><?php esc_html_e( 'No records found.', 'wcmps' ); ?></td>
                                        </tr>
                                    <?php
                                }
                                $big = 999999999;                            
                                ?>
                                </tbody>
                            </table>
                            <div class="wcmps-pagination">
                                <span class="pagination-links">
                                    <?php
                                    $total = $records->max_num_pages;                                
                                    $paginate_url = network_admin_url( '/admin.php?wcmps&paged=%#%' );
                                    echo paginate_links( array(
                                        'base'      => str_replace( $big, '%#%', $paginate_url ),
                                        'format'    => '?paged=%#%',
                                        'current'   => max( 1, $paged ),
                                        'total'     => $total,
                                        'add_args'  => $add_args,    
                                        'prev_text' => '&laquo;',
                                        'next_text' => '&laquo;',
                                    ) );
                                    ?>
                                </span>
                            </div>
                            <br class="clear">
                            <input type="hidden" name="wcmps_content_type" value="<?php echo esc_html( $wcmps_content_type ); ?>">
                            <input type="hidden" name="wcmps_source_blog" value="<?php echo intval( $wcmps_source_blog ); ?>">
                            <input type="hidden" name="wcmps_record_per_page" value="<?php echo intval( $wcmps_record_per_page ); ?>">
                            <?php wp_reset_postdata(); ?>
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Destination Sites', 'wcmps' ); ?></th>
                                        <td>
                                            <label><input class="wcmps-check-uncheck" type="checkbox" /><?php esc_html_e( 'All', 'wcmps' ); ?></label>
                                            <p class="description"><?php esc_html_e( 'Select/Deselect all sites.', 'wcmps' ); ?></p>
                                            <br>
                                            <fieldset class="wcmps-sites">                                            
                                                <?php                                                                                       
                                                    if ( $sites != null ) {
                                                        foreach ( $sites as $key => $value ) { 
                                                            if ( $wcmps_source_blog != $value->blog_id ) {
                                                                $blog_details = get_blog_details( $value->blog_id );
                                                                ?>
                                                                    <label><input name="wcmps_destination_blogs[]" type="checkbox" value="<?php echo intval( $value->blog_id ); ?>"><?php echo $value->domain; echo $value->path; echo ' ('.$blog_details->blogname.')'; ?></label><br>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                ?>                                                                          				
                                            </fieldset>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Extra Options', 'wcmps' ); ?></th>
                                        <td>
                                            <fieldset>
                                                <label><input value="1" type="checkbox" name="copy_media"> <?php esc_html_e( 'Sync media (Attachments)', 'wcmps' ); ?></label><br>
                                                <label><input value="1" type="checkbox" name="copy_terms"> <?php esc_html_e( 'Sync terms (Categories, Tags, Attributes, Shipping Classes)', 'wcmps' ); ?></label>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="submit"><input name="wcmps_submit" class="button button-primary" value="<?php esc_html_e( 'Sync', 'wcmps' ); ?>" type="submit"></p>
                        </form>
                        <?php                    
                        if ( $wcmps_source_blog != get_current_blog_id() ) {
                            restore_current_blog();
                        }
                    }
                ?>
                <style>
                    .wcmps-pagination {
                        color: #555;
                        cursor: default;
                        float: right;
                        height: 28px;
                        margin-top: 3px;
                    }

                    .wcmps-pagination .page-numbers {
                        background: #e5e5e5;
                        border: 1px solid #ddd;
                        display: inline-block;
                        font-size: 16px;
                        font-weight: 400;
                        line-height: 1;
                        min-width: 17px;
                        padding: 3px 5px 7px;
                        text-align: center;
                        text-decoration: none;
                    }

                    .wcmps-pagination .page-numbers.current {
                        background: #f7f7f7;
                        border-color: #ddd;
                        color: #a0a5aa;
                        height: 16px;
                        margin: 6px 0 4px;
                    }

                    .wcmps-pagination a.page-numbers:hover {
                        background: #00a0d2;
                        border-color: #5b9dd9;
                        box-shadow: none;
                        color: #fff;
                        outline: 0 none;
                    }

                    .wcmps-search-box {
                        margin-bottom: 8px !important;
                    }

                    @media screen and (max-width:782px) {
                        .wcmps-pagination {
                            float: none;
                            height: auto;
                            text-align: center;
                            margin-top: 7px;
                        }

                        .wcmps-search-box {
                            margin-bottom: 20px !important;
                        }
                    }
                </style>
                <script>
                    jQuery( document ).ready( function( $ ) {
                        $( '.wcmps-check-uncheck' ).on( 'change', function() {
                            var checked = $( this ).prop( 'checked' );
                            $( '.wcmps-sites input[type="checkbox"]' ).each( function() {
                                if ( checked ) {
                                    $( this ).prop( 'checked', true );
                                } else {
                                    $( this ).prop( 'checked', false );
                                }
                            });                   
                        });
                    });
                </script>
            </div>
        <?php
    }
}