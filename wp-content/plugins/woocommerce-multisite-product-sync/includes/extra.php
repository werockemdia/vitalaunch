<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that save acf special fields.
 * $post_id variable return current edit post id.
 */
if ( ! function_exists( 'wcmps_acf_save_post' ) ) {
    add_action( 'save_post', 'wcmps_acf_save_post' );
    function wcmps_acf_save_post( $post_id ) {
        
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        
        $post_type = get_post_type( $post_id );        
        if ( $post_type == 'acf-field-group' ) {          
            global $wpdb;            
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
}