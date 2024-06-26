<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that show sync section.
 */
if ( ! function_exists( 'wcmps_display_content_copier' ) ) {
    add_action( 'wp_ajax_display_content_sync', 'wcmps_display_content_copier' );
    function wcmps_display_content_copier() {
        
        global $wpdb;
        
        $current_blog_id = get_current_blog_id();
        $current_item_id = (int) $_POST['item_id'];
        $type = sanitize_text_field( $_POST['type'] );
        $type_name = sanitize_text_field( $_POST['type_name'] );        
        
        $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );                                       
        ?>
            <div id="wcmps-sites">                                      
                <?php
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
                    
                    $synced = 0;
                    foreach ( $sites as $key => $value ) {
                        if ( $value->blog_id != get_current_blog_id() ) {                            
                            if ( isset( $blog_relationships[$value->blog_id] ) ) {                                
                                $synced = 1;
                            }
                        }
                    }
                    
                    $wcmps_disable = get_post_meta( $current_item_id, 'wcmps_disable', true );
                    
                    if ( ! $wcmps_disable ) {
                        if ( $synced ) {
                            ?><p><strong><?php esc_html_e( 'Status: ', 'wcmps' ); ?></strong><?php esc_html_e( 'Synced.', 'wcmps' ); ?></p><?php
                        } else {
                            ?><p><strong><?php esc_html_e( 'Status: ', 'wcmps' ); ?></strong><?php esc_html_e( 'Not synced.', 'wcmps' ); ?></p><?php
                        }
                    }
                ?>
                <input type="hidden" name="wcmps_disable" value="0" />
                <input type="checkbox" name="wcmps_disable" value="1"<?php echo ( $wcmps_disable ? ' checked="checked"' : '' ); ?> /><?php esc_html_e( 'Disable sync?', 'wcmps' ); ?>
            </div>
        <?php
        
        wp_die();
    }
}

/*
 * This is a function that add product in queue.
 * $post_id variable return current post id.
 */
if ( ! function_exists( 'wcmps_send_content_copier' ) ) {
    add_action( 'save_post', 'wcmps_send_content_copier' );
    function wcmps_send_content_copier( $post_id ) {
        
        if ( wp_is_post_revision( $post_id ) || get_post_type( $post_id ) != 'product' ) {
            return;
        }
        
        if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'inline-save' ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) ) {            
            $sync = 1;            
            if ( isset( $_POST['wcmps_disable'] ) ) {
                update_post_meta( $post_id, 'wcmps_disable', (int) $_POST['wcmps_disable'] );
                if ( $_POST['wcmps_disable'] ) {
                    $sync = 0;
                }
            } else {
                $sync = get_post_meta( $post_id, 'wcmps_disable', true );
            }
            
            if ( $sync ) {
                global $wpdb;        
                $current_user = wp_get_current_user();
                $current_blog_id = get_current_blog_id();        
                $blogs = array();        
                $wcmps_auto_sync = get_site_option( 'wcmps_auto_sync' );
                if ( $wcmps_auto_sync ) {
                    $wcmps_auto_sync_type = get_site_option( 'wcmps_auto_sync_type' );
                    if ( $wcmps_auto_sync_type == 'main-site-to-sub-sites' ) {
                        $wcmps_auto_sync_main_blog = get_site_option( 'wcmps_auto_sync_main_blog' );
                        if ( $wcmps_auto_sync_main_blog == $current_blog_id ) {
                            $wcmps_auto_sync_sub_blogs = get_site_option( 'wcmps_auto_sync_sub_blogs' );
                            if ( $wcmps_auto_sync_sub_blogs && $wcmps_auto_sync_sub_blogs != null ) {
                                foreach ( $wcmps_auto_sync_sub_blogs as $key => $value ) {
                                    if ( $value != $current_blog_id ) {
                                        $blogs[] = $value;
                                    }
                                }
                            } 
                        }
                    } else if ( $wcmps_auto_sync_type == 'sub-sites-to-main-site' ) { 
                        $wcmps_auto_sync_main_blog = get_site_option( 'wcmps_auto_sync_main_blog' );
                        if ( $wcmps_auto_sync_main_blog != $current_blog_id ) {
                            $blogs[] = $wcmps_auto_sync_main_blog;
                        }                
                    } else {
                        $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );
                        if ( $sites != null ) {
                            foreach ( $sites as $key => $value ) { 
                                if ( $value->blog_id != $current_blog_id ) {
                                    $blogs[] = $value->blog_id;
                                }
                            }
                        }
                    }
                }

                if ( $blogs != null ) {           
                    $queue = array();
                    $queue['source_blog_id'] = $current_blog_id;
                    $queue['source_item_id'] = $post_id;
                    $queue['type'] = 'post_type';
                    $queue['type_name'] = get_post_type( $post_id );
                    $queue['copy_media'] = 1;
                    $queue['copy_terms'] = 1;
                    $queue['destination_blogs'] = $blogs;
                    
                    $record = $wpdb->get_row( "SELECT * FROM ".$wpdb->base_prefix."wcmps_queue WHERE source_blog_id = $current_blog_id AND source_item_id = $post_id" );
                    if ( $record == null ) {
                        $wpdb->insert(
                            $wpdb->base_prefix . 'wcmps_queue',
                            array( 
                                'source_blog_id'    => $current_blog_id,
                                'source_item_id'    => $post_id,
                                'data'              => serialize( $queue ),                                         
                            )
                        );
                    }
                }
            }
        }        
    }
}

/*
 * This is a function that run every minute and check products in queue with sync.
 */
if ( ! function_exists( 'wcmps_products_sync_callback' ) ) { 
    add_action( 'wp_ajax_wcmps_products_sync', 'wcmps_products_sync_callback' );
    function wcmps_products_sync_callback( $ajax = 1 ) {
        
        global $wpdb;
        
        $current_blog_id = get_current_blog_id();
        $queue_items = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix ."wcmps_queue" );
        if ( $queue_items != null ) {
            foreach ( $queue_items as $queue_item ) {
                
                $data = unserialize($queue_item->data);
                $wcmps_obj = new WCMPS();

                $source_blog_id = $data['source_blog_id'];            
                $source_item_id = $data['source_item_id'];
                $type = $data['type'];
                $type_name = $data['type_name'];
                $copy_media = $data['copy_media'];
                $copy_terms = $data['copy_terms'];
                $blogs = $data['destination_blogs'];            
                
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

                $wpdb->delete( 
                    $wpdb->base_prefix . 'wcmps_queue',
                    array( 
                        'id' => $queue_item->id,
                    )
                );
                
                if ( $source_blog_id != $current_blog_id ) {                
                    restore_current_blog();
                }
            }
        }
        
        if ( $ajax ) {
            wp_die();
        }
    }
}

/*
 * This is a function that call js in footer
 */
if ( ! function_exists( 'wcmps_admin_footer' ) ) { 
    add_action( 'admin_footer', 'wcmps_admin_footer' );
    function wcmps_admin_footer() {
        
        if ( isset( $_REQUEST['post'] ) && $_REQUEST['post'] ) {
            $post_id = (int) $_REQUEST['post'];
            $post_type = get_post_type( $post_id );
            if ( $post_type == 'product' ) {
                ?>
                    <script type="text/javascript">
                        var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
                        jQuery( document ).ready( function( $ ) {
                            var data = {
                                'action': 'wcmps_products_sync'
                            };
                            
                            $.post( ajax_url, data, function( response ) {                            
                            });

                            $( 'body' ).on( 'click', '.editor-post-publish-button', function() {
                                setTimeout( function() {
                                    location.reload();
                                }, 5000 );
                            });
                        });
                    </script>
                <?php
            }
        }
    }
}

/*
 * This is a function that call when order processing
 */
if ( ! function_exists( 'wcmps_woocommerce_order_status_processing' ) ) {
    add_action( 'woocommerce_order_status_processing', 'wcmps_woocommerce_order_status_processing', 20, 1 );
    function wcmps_woocommerce_order_status_processing( $order_id ) {
        
        $wcmps_stock_sync = get_site_option( 'wcmps_stock_sync' );
        $wcmps_stock_sync_status = get_site_option( 'wcmps_stock_sync_status' );
        if ( is_admin() && $wcmps_stock_sync && $wcmps_stock_sync_status == 'processing' ) {
            wcmps_stock_sync( $order_id );
        }
    }
}

/*
 * This is a function that call when order completed
 */
if ( ! function_exists( 'wcmps_woocommerce_order_status_completed' ) ) {
    add_action( 'woocommerce_order_status_completed', 'wcmps_woocommerce_order_status_completed', 20, 1 );
    function wcmps_woocommerce_order_status_completed( $order_id ) {
        
        $wcmps_stock_sync = get_site_option( 'wcmps_stock_sync' );
        $wcmps_stock_sync_status = get_site_option( 'wcmps_stock_sync_status' );
        if ( is_admin() && $wcmps_stock_sync && $wcmps_stock_sync_status == 'completed' ) {
            wcmps_stock_sync( $order_id );
        }
    }
}

/*
 * This is a function that auto sync stock when order place
 */
if ( ! function_exists( 'wcmps_woocommerce_thankyou' ) ) {
    add_action( 'woocommerce_thankyou', 'wcmps_woocommerce_thankyou', 20, 1 );
    function wcmps_woocommerce_thankyou( $order_id ) {

        $sync = get_post_meta( $order_id, 'wcmps_sync', true );
        $wcmps_stock_sync = get_site_option( 'wcmps_stock_sync' );
        if ( $wcmps_stock_sync && ! $sync ) {
            update_post_meta( $order_id, 'wcmps_sync', 1 );
            ?>
                <script type="text/javascript">
                    var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
                    jQuery( document ).ready( function( $ ) {
                        var data = {
                            'action': 'wcmps_auto_stock_update',
                            'order_id': <?php echo $order_id; ?>
                        };

                        $.post( ajax_url, data, function( response ) {
                        });
                    });
                </script>
            <?php
        }
    }
}

/*
 * This is a function that ajax auto sync stock when order place
 */
if ( ! function_exists( 'wcmps_auto_stock_update_callback' ) ) {
    add_action( 'wp_ajax_wcmps_auto_stock_update', 'wcmps_auto_stock_update_callback', 20 );
    add_action( 'wp_ajax_nopriv_wcmps_auto_stock_update', 'wcmps_auto_stock_update_callback', 20 );
    function wcmps_auto_stock_update_callback() {
        
        $order_id = ( isset( $_POST['order_id'] ) ? (int) $_POST['order_id'] : 0 );
        if ( $order_id ) {
            wcmps_stock_sync( $order_id );
        }
        
        wp_die();
    }
}

/*
 * This is a function that sync stock
 */
if ( ! function_exists( 'wcmps_stock_sync' ) ) {
    function wcmps_stock_sync( $order_id ) {
        
        $order = wc_get_order( $order_id );
        $items = $order->get_items();
        if ( $items != null ) {
            foreach ( $items as $item ) {
                $data = $item->get_data();
                $post_id = $data['product_id'];                
                $sync = 1;            
                if ( get_post_meta( $post_id, 'wcmps_disable', true ) ) {
                    $sync = 0;
                }
                
                if ( $sync ) {
                    global $wpdb;
                    $current_blog_id = get_current_blog_id();        
                    $blogs = array();        
                    $wcmps_auto_sync = get_site_option( 'wcmps_auto_sync' );
                    if ( $wcmps_auto_sync ) {
                        $wcmps_auto_sync_type = get_site_option( 'wcmps_auto_sync_type' );
                        if ( $wcmps_auto_sync_type == 'main-site-to-sub-sites' ) {
                            $wcmps_auto_sync_main_blog = get_site_option( 'wcmps_auto_sync_main_blog' );
                            if ( $wcmps_auto_sync_main_blog == $current_blog_id ) {
                                $wcmps_auto_sync_sub_blogs = get_site_option( 'wcmps_auto_sync_sub_blogs' );
                                if ( $wcmps_auto_sync_sub_blogs && $wcmps_auto_sync_sub_blogs != null ) {
                                    foreach ( $wcmps_auto_sync_sub_blogs as $key => $value ) {
                                        if ( $value != $current_blog_id ) {
                                            $blogs[] = $value;
                                        }
                                    }
                                } 
                            }
                        } else if ( $wcmps_auto_sync_type == 'sub-sites-to-main-site' ) {
                            $wcmps_auto_sync_main_blog = get_site_option( 'wcmps_auto_sync_main_blog' );
                            if ( $wcmps_auto_sync_main_blog != $current_blog_id ) {
                                $blogs[] = $wcmps_auto_sync_main_blog;
                            }                
                        } else {
                            $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );
                            if ( $sites != null ) {
                                foreach ( $sites as $key => $value ) { 
                                    if ( $value->blog_id != $current_blog_id ) {
                                        $blogs[] = $value->blog_id;
                                    }
                                }
                            }
                        }
                    }
                    
                    if ( $blogs != null ) {
                        $queue = array();
                        $queue['source_blog_id'] = $current_blog_id;
                        $queue['source_item_id'] = $post_id;
                        $queue['type'] = 'post_type';
                        $queue['type_name'] = get_post_type( $post_id );
                        $queue['copy_media'] = 1;
                        $queue['copy_terms'] = 1;
                        $queue['destination_blogs'] = $blogs;
                        
                        $record = $wpdb->get_row( "SELECT * FROM ".$wpdb->base_prefix."wcmps_queue WHERE source_blog_id = $current_blog_id AND source_item_id = $post_id" );
                        if ( $record == null ) {
                            $wpdb->insert(
                                $wpdb->base_prefix . 'wcmps_queue',
                                array( 
                                    'source_blog_id'    => $current_blog_id,
                                    'source_item_id'    => $post_id,
                                    'data'              => serialize( $queue ),                                         
                                )
                            );
                        }
                    }
                }
            }
            
            wcmps_products_sync_callback(0);
        }
    }
}

// sync on product delete
if ( ! function_exists( 'wcmps_wp_trash_post' ) ) {
    add_action( 'wp_trash_post', 'wcmps_wp_trash_post' );
    function wcmps_wp_trash_post( $post_id ) {
        
        global $wpdb;
        
        $product_delete = get_site_option( 'wcmps_product_delete' );
        $post = get_post( $post_id );
        if ( $product_delete && $post != null && $post->post_type == 'product' ) {
            $source_item_id = $post_id;
            $source_blog_id = get_current_blog_id();
            $type = 'post_type';
            $type_name = 'product';
            $blog_relationships = array();
            $current_relationship = $wpdb->get_row( "SELECT * FROM ".$wpdb->base_prefix."wcmps_relationships WHERE type='$type' AND type_name='$type_name' AND ((source_item_id='$source_item_id' AND source_blog_id='$source_blog_id') || (destination_item_id='$source_item_id' AND destination_blog_id='$source_blog_id'))" );
            if ( $current_relationship != null ) {
                $relationship_id = $current_relationship->relationship_id;
                $relationships = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."wcmps_relationships WHERE relationship_id='$relationship_id'");
                if ( $relationships != null ) {
                    foreach ( $relationships as $relationship ) {
                        if ( $source_blog_id == $relationship->source_blog_id && $source_item_id == $relationship->source_item_id ) {
                            $blog_relationships[$relationship->destination_blog_id] = $relationship->destination_item_id;
                        } else if ( $source_blog_id == $relationship->destination_blog_id && $source_item_id == $relationship->destination_item_id ) {
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
                foreach ( $blog_relationships as $destination_blog_id => $destination_item_id ) {
                    switch_to_blog( $destination_blog_id );
                    
                    wp_delete_post( $destination_item_id, true );
                    
                    restore_current_blog();
                }
            }
        }
    }
}