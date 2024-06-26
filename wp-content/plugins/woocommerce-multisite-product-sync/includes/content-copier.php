<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that remove content copier meta box.
 */
if ( ! function_exists( 'wcmps_remove_meta_box' ) ) {
    add_action( 'do_meta_boxes', 'wcmps_remove_meta_box' );
    function wcmps_remove_meta_box() {
        
        remove_meta_box( 'wcmps-content-copier', 'product', 'advanced' );
    }
}

/*
 * This is a function that create meta box for products.
 * Access user role wise which is set in settings
 */
if ( ! function_exists( 'wcmps_add_meta_boxes' ) ) {
    add_action( 'add_meta_boxes', 'wcmps_add_meta_boxes' );
    function wcmps_add_meta_boxes() {
        
        add_meta_box( 'wcmps_status', esc_html__( 'Product Sync Status', 'wcmps' ), 'wcmps_status_callback', 'product', 'side' );
    }
}

/*
 * This is a function that call copier meta box.
 * $post variable return current edit product data.
 */
if ( ! function_exists( 'wcmps_status_callback' ) ) {
    function wcmps_status_callback( $post ) {
        
        $post_status = get_post_status( get_the_ID() );
        if ( $post_status == 'publish' || $post_status == 'future' || $post_status == 'private' ) {
            ?>
                <div id="wcmps-content"></div>
                <script type="text/javascript">
                    var wcmps_ajaxurl = '<?php echo admin_url( '/admin-ajax.php' ); ?>';
                    jQuery( document ).ready( function( $ ) {  
                        var show_cc_data = {};
                        show_cc_data.action = 'display_content_sync';
                        var type = 'post_type';
                        var type_name = '<?php echo get_post_type(); ?>';
                        var item_id = '<?php echo get_the_ID(); ?>';
                        if ( type && type_name && item_id ) {
                            show_cc_data.type = type;
                            show_cc_data.type_name = type_name;
                            show_cc_data.item_id = item_id;
                            $.post( wcmps_ajaxurl, show_cc_data, function( response ) {
                                $( '#wcmps-content' ).html( response );
                            });
                        }
                    });
                </script>
            <?php
        } else {
            $wcmps_disable = get_post_meta( get_the_ID(), 'wcmps_disable', true );
            ?>
                <input type="hidden" name="wcmps_disable" value="0" />
                <input type="checkbox" name="wcmps_disable" value="1"<?php echo ( $wcmps_disable ? ' checked="checked"' : '' ); ?> /><?php esc_html_e( 'Disable sync?', 'wcmps' ); ?>
            <?php
        }
    }
}