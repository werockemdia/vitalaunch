<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a class that manage core functionality.
 */
if ( ! class_exists( 'WCMPS' ) ) {
    class WCMPS {
        
        public function __construct() {
            
        }
        
        public function copy_post( $source_item_id = 0, $source_blog_id = 0, $type = '', $type_name = '', $destination_blog_id = 0, $copy_media = 0, $copy_terms = 0, $sub_parent_id = 0 ) {
        
            if ( $source_item_id && $source_blog_id && $type && $type_name && $destination_blog_id ) {
                $current_blog_id = get_current_blog_id();
                if ( $source_blog_id != $current_blog_id ) {                
                    switch_to_blog( $source_blog_id );
                }

                global $wpdb;
                $item = get_post( $source_item_id );
                if ( $item != null ) {
                    $current_user = wp_get_current_user();
                    $post_name = $item->post_name;
                    $post_data = array(
                        'post_author'           => $current_user->ID,
                        'post_date'             => $item->post_date,
                        'post_content'          => $item->post_content,
                        'post_title'            => wp_strip_all_tags( $item->post_title ),
                        'post_excerpt'          => $item->post_excerpt,            
                        'post_status'           => $item->post_status,
                        'comment_status'        => $item->comment_status,
                        'ping_status'           => $item->ping_status,
                        'post_password'         => $item->post_password,
                        'to_ping'               => $item->to_ping,
                        'pinged'                => $item->pinged,
                        'post_content_filtered' => $item->post_content_filtered,                    
                        'menu_order'            => $item->menu_order,
                        'post_type'             => $item->post_type,
                        'post_mime_type'        => $item->post_mime_type,                    
                    );
                    
                    $sub_items = get_children(
                        array(
                            'post_parent'   => $item->ID,
                            'post_type'     => 'product_variation',
                            'numberposts'   => -1,
                            'post_status'   => 'publish',
                        )
                    );
                    
                    $custom_fields = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."wcmps_cf" );
                    $special_custom_fields = array();
                    $_product_image_gallery = get_post_meta( $source_item_id, '_product_image_gallery', true );
                    if ( $_product_image_gallery != null ) {
                        $special_custom_fields['_product_image_gallery'] = array(
                            'type'      => 'image',
                            'ids'       => true,
                            'ids_type'  => 'comma',
                        );
                    }
                    if ( $custom_fields != null ) {
                        foreach ( $custom_fields as $custom_field ) {
                            $special_custom_fields[$custom_field->filed_key] = array(
                                'type'  => $custom_field->field_type,
                                'data'  => $custom_field->field_data,
                            );
                        }
                    }

                    $postmeta_fields = array();                
                    $postmetas = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."postmeta WHERE post_id=$source_item_id" );
                    if ( $postmetas != null ) {
                        foreach ( $postmetas as $postmeta ) {
                            if ( isset( $special_custom_fields[$postmeta->meta_key] ) ) {
                                $special_custom_field = $special_custom_fields[$postmeta->meta_key];                            
                                if ( $copy_media ) {
                                    if ( $special_custom_field['type'] == 'image' || $special_custom_field['type'] == 'file' ) {
                                        if ( isset( $special_custom_field['ids'] ) ) {
                                            $cf_attachment_ids = get_post_meta( $source_item_id, $postmeta->meta_key, true );
                                            if ( $cf_attachment_ids ) {
                                                $cf_attachment_ids = explode( ',', $cf_attachment_ids );
                                                foreach( $cf_attachment_ids as $cf_attachment_id ) {
                                                    $cf_attachment = get_post( $cf_attachment_id );
                                                    $cf_attachment_path = get_attached_file( $cf_attachment_id );                                
                                                    if ( $cf_attachment_path != null ) {
                                                        $postmeta_fields[$postmeta->meta_key][] = array(
                                                            'post_title'                => wp_strip_all_tags( $cf_attachment->post_title ),
                                                            'post_content'              => $cf_attachment->post_content,                            
                                                            'post_excerpt'              => $cf_attachment->post_excerpt,
                                                            'wp_attachment_image_alt'   => get_post_meta( $cf_attachment_id, '_wp_attachment_image_alt', true ),
                                                            'post_name'                 => $cf_attachment->post_name, 
                                                            'path'                      => $cf_attachment_path,
                                                        );
                                                    }  
                                                }                 
                                            } else {
                                                $postmeta_fields[$postmeta->meta_key] = '';
                                            }                                        
                                        } else {
                                            $cf_attachment_id = get_post_meta( $source_item_id, $postmeta->meta_key, true );
                                            if ( $cf_attachment_id ) {
                                                $cf_attachment = get_post( $cf_attachment_id );
                                                $cf_attachment_path = get_attached_file( $cf_attachment_id );                                
                                                if ( $cf_attachment_path != null ) {
                                                    $postmeta_fields[$postmeta->meta_key] = array(
                                                        'post_title'                => wp_strip_all_tags( $cf_attachment->post_title ),
                                                        'post_content'              => $cf_attachment->post_content,                            
                                                        'post_excerpt'              => $cf_attachment->post_excerpt,
                                                        'wp_attachment_image_alt'   => get_post_meta( $cf_attachment_id, '_wp_attachment_image_alt', true ),
                                                        'post_name'                 => $cf_attachment->post_name, 
                                                        'path'                      => $cf_attachment_path,
                                                    );
                                                }                    
                                            } else {
                                                $postmeta_fields[$postmeta->meta_key] = '';
                                            }
                                        }
                                    }
                                }                            
                            } else if ( $postmeta->meta_key == '_default_attributes' ) {
                                $postmeta_fields[$postmeta->meta_key] = get_post_meta( $source_item_id, '_default_attributes', true );
                            } else {
                                $postmeta_fields[$postmeta->meta_key] = get_post_meta( $source_item_id, $postmeta->meta_key, true );
                            }
                        }
                    }              

                    $thumbnail_id = get_post_meta( $source_item_id, '_thumbnail_id', true );
                    if ( $thumbnail_id && $copy_media ) {
                        $thumbnail = get_post( $thumbnail_id );
                        $thumbnail_path = get_attached_file( $thumbnail_id );                                
                        if ( $thumbnail_path != null ) {
                            $postmeta_fields['_thumbnail_id'] = array(
                                'post_title'                => wp_strip_all_tags( $thumbnail->post_title ),
                                'post_content'              => $thumbnail->post_content,                            
                                'post_excerpt'              => $thumbnail->post_excerpt,
                                'wp_attachment_image_alt'   => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ),
                                'post_name'                 => $thumbnail->post_name, 
                                'path'                      => $thumbnail_path,
                            );
                        }                    
                    } else {
                        unset($postmeta_fields['_thumbnail_id']);
                    }
                    
                    $exclude_product_meta_data = get_site_option( 'wcmps_exclude_product_meta_data' );
                    if ( $exclude_product_meta_data ) {
                        $exclude_meta_data = explode( ',', $exclude_product_meta_data );
                        if ( is_array( $exclude_meta_data ) && $postmeta_fields != null ) {
                            foreach ( $postmeta_fields as $product_meta_key => $product_meta_value ) {
                                if ( in_array( $product_meta_key, $exclude_meta_data ) ) {
                                    unset( $postmeta_fields[$product_meta_key] );
                                }
                            }
                        }
                        
                        if ( is_array( $exclude_meta_data ) && $post_data != null ) {
                            foreach ( $post_data as $post_data_key => $post_data_value ) {
                                if ( in_array( $post_data_key, $exclude_meta_data ) ) {
                                    unset( $post_data[$post_data_key] );
                                }
                            }
                        }
                    }
                    
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

                    if ( $blog_relationships == null ) {
                        $relationship_id = uniqid();
                    }

                    $post_data['post_parent'] = 0;
                    if ( $sub_parent_id ) {
                        $post_data['post_parent'] = $sub_parent_id;
                    }
                    
                    $postmeta_children_ids = array();
                    if ( isset( $postmeta_fields['_children'] ) && $postmeta_fields['_children'] != null ) {
                        $postmeta_children_ids = get_post_meta( $source_item_id, '_children', true );
                        $postmeta_fields['_children'] = $postmeta_children_ids;
                    }
                    
                    if ( is_array($postmeta_children_ids) && $postmeta_children_ids != null ) {
                        $children_ids = array();
                        foreach ( $postmeta_children_ids as $postmeta_children_id ) {
                            $children_id = $this->copy_post( $postmeta_children_id, $source_blog_id, $type, $type_name, $destination_blog_id );
                            if ( $children_id ) {
                                $children_ids[] = (int) $children_id;
                            }
                        }
                        if ( $children_ids != null ) {
                            $postmeta_fields['_children'] = $children_ids;
                        }
                    }

                    $postmeta_upsell_ids = array();
                    if ( isset( $postmeta_fields['_upsell_ids'] ) && $postmeta_fields['_upsell_ids'] != null ) {
                        $postmeta_upsell_ids = get_post_meta( $source_item_id, '_upsell_ids', true );
                        $postmeta_fields['_upsell_ids'] = $postmeta_upsell_ids;
                    }
                    
                    if ( is_array($postmeta_upsell_ids) && $postmeta_upsell_ids != null ) {
                        $upsell_ids = array();
                        foreach ( $postmeta_upsell_ids as $postmeta_upsell_id ) {
                            $upsell_id = $this->copy_post( $postmeta_upsell_id, $source_blog_id, $type, $type_name, $destination_blog_id );
                            if ( $upsell_id ) {
                                $upsell_ids[] = (int) $upsell_id;
                            }
                        }
                        if ( $upsell_ids != null ) {
                            $postmeta_fields['_upsell_ids'] = $upsell_ids;
                        }
                    }
                    
                    $postmeta_crosssell_ids = array();
                    if ( isset( $postmeta_fields['_crosssell_ids'] ) && $postmeta_fields['_crosssell_ids'] != null ) {
                        $postmeta_crosssell_ids = get_post_meta( $source_item_id, '_crosssell_ids', true );
                        $postmeta_fields['_crosssell_ids'] = $postmeta_crosssell_ids;
                    }
                    
                    if ( is_array($postmeta_crosssell_ids) && $postmeta_crosssell_ids != null ) {
                        $crosssell_ids = array();
                        foreach ( $postmeta_crosssell_ids as $postmeta_crosssell_id ) {
                            $crosssell_id = $this->copy_post( $postmeta_crosssell_id, $source_blog_id, $type, $type_name, $destination_blog_id );
                            if ( $crosssell_id ) {
                                $crosssell_ids[] = (int) $crosssell_id;
                            }
                        }
                        if ( $crosssell_ids != null ) {
                            $postmeta_fields['_crosssell_ids'] = $crosssell_ids;
                        }
                    }
                    
                    $downloadable_files = ( isset( $postmeta_fields['_downloadable_files'] ) ? $postmeta_fields['_downloadable_files'] : null );
                    $postmeta_fields['_downloadable_files'] = $downloadable_files;

                    $product_attributes = ( isset( $postmeta_fields['_product_attributes'] ) ? $postmeta_fields['_product_attributes'] : null );
                    $postmeta_fields['_product_attributes'] = $product_attributes;

                    if ( $copy_terms ) {
                        if ( is_array($product_attributes) && $product_attributes != null ) {
                            foreach( $product_attributes as $product_attribute_key => $product_attribute_value ) {
                                $product_attribute = str_replace( 'pa_', '', $product_attribute_key );
                                $attribute_taxonomies = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."woocommerce_attribute_taxonomies WHERE attribute_name='$product_attribute'" );
                                if ( $attribute_taxonomies != null ) {
                                    $this->copy_attribute_taxonomy( $attribute_taxonomies, $destination_blog_id );
                                }
                            }
                        }

                        $this->set_attribute_taxonomies( $destination_blog_id );
                    }

                    unset($postmeta_fields['_wc_average_rating']);
                    unset($postmeta_fields['_wc_review_count']);
                    unset($postmeta_fields['_wc_rating_count']);
                    
                    $wcmps_stock_sync = get_site_option( 'wcmps_stock_sync' );
                    if ( ! $wcmps_stock_sync ) {
                        unset( $postmeta_fields['_stock'] );
                    }
                    
                    if ( $source_blog_id != $current_blog_id ) {                
                        restore_current_blog();
                    }

                    switch_to_blog( $destination_blog_id );
                        $synced = 1;
                        if ( isset( $blog_relationships[$destination_blog_id] ) && get_post( $blog_relationships[$destination_blog_id] ) != null ) {
                            $destination_item_id = $blog_relationships[$destination_blog_id];
                            $wcmps_disable = get_post_meta( $destination_item_id, 'wcmps_disable', true );
                            if ( $wcmps_disable ) {
                                $synced = 0;
                            } else {
                                $post_data['ID'] = $destination_item_id;
                                wp_update_post( $post_data );
                            }
                        } else {
                            $destination_item_not_exists = 1;
                            $wcmps_old = get_site_option( 'wcmps_old' );
                            if ( $wcmps_old ) {                            
                                $check_destination_item_args = array(
                                    'name'              => $post_name,
                                    'post_type'         => $post_data['post_type'],
                                    'posts_per_page'    => 1,
                                );
                                $check_destination_item = get_posts( $check_destination_item_args );
                                if ( $check_destination_item != null && $check_destination_item[0]->post_name == $post_name ) {
                                    $destination_item_id = $check_destination_item[0]->ID;
                                    if( $destination_item_id ) {
                                        $post_data['ID'] = $destination_item_id;
                                        wp_update_post( $post_data );
                                        $wpdb->insert(
                                            $wpdb->base_prefix . 'wcmps_relationships',
                                            array( 
                                                'source_item_id'        => $source_item_id,
                                                'source_blog_id'        => $source_blog_id,
                                                'destination_item_id'   => $destination_item_id,
                                                'destination_blog_id'   => $destination_blog_id,
                                                'relationship_id'       => $relationship_id,
                                                'type'                  => $type,
                                                'type_name'             => $type_name,
                                            )
                                        ); 

                                        $destination_item_not_exists = 0;
                                    }
                                }
                            }

                            if ( $destination_item_not_exists ) {
                                $destination_item_id = wp_insert_post( $post_data ); 
                                if( $destination_item_id ) {
                                    $wpdb->insert(
                                        $wpdb->base_prefix . 'wcmps_relationships',
                                        array( 
                                            'source_item_id'        => $source_item_id,
                                            'source_blog_id'        => $source_blog_id,
                                            'destination_item_id'   => $destination_item_id,
                                            'destination_blog_id'   => $destination_blog_id,
                                            'relationship_id'       => $relationship_id,
                                            'type'                  => $type,
                                            'type_name'             => $type_name,
                                        )
                                    );                       
                                }
                            }
                        }

                        if ( $destination_item_id && $postmeta_fields && $synced ) {
                            if ( $postmeta_fields != null ) {
                                foreach ( $postmeta_fields as $field_key => $field_value ) {
                                    if ( isset( $special_custom_fields[$field_key] ) ) {
                                        $special_custom_field = $special_custom_fields[$field_key];
                                        if ( $special_custom_field['type'] == 'image' || $special_custom_field['type'] == 'file' ) {
                                            if ( isset( $special_custom_field['ids'] ) ) {
                                                $postmeta_field_values = $postmeta_fields[$field_key];
                                                $ids = array();
                                                foreach( $postmeta_field_values as $postmeta_field_value ) {
                                                    if ( isset( $postmeta_field_value['path'] ) && $postmeta_field_value != null ) { 
                                                        $check_attachment_args = array(
                                                            'name'              => $postmeta_field_value['post_name'],
                                                            'post_type'         => 'attachment',
                                                            'post_status'       => 'inherit',
                                                            'posts_per_page'    => 1,
                                                        );
                                                        $check_attachment = get_posts( $check_attachment_args );
                                                        if ( $check_attachment && $check_attachment[0]->post_name == $postmeta_field_value['post_name'] ) {       
                                                            $attach_id = $check_attachment[0]->ID;
                                                            //update_post_meta( $destination_item_id, $field_key, $attach_id );
                                                            $ids[] = $attach_id;
                                                            $update_attachment = array(
                                                                'ID'            => $attach_id,
                                                                'post_title'    => $postmeta_field_value['post_title'],
                                                                'post_content'  => $postmeta_field_value['post_content'],
                                                                'post_excerpt'  => $postmeta_field_value['post_excerpt'],
                                                            );                                            
                                                            wp_update_post( $update_attachment );
                                                            update_post_meta( $attach_id, '_wp_attachment_image_alt', $postmeta_field_value['wp_attachment_image_alt'] );
                                                        } else {                                        
                                                            $file = $postmeta_field_value['path'];
                                                            $upload_file = wp_upload_bits( basename( $file ), null, file_get_contents( $file ) );
                                                            if ( ! $upload_file['error'] ) {
                                                                $filetype = wp_check_filetype( basename( $file ), null );
                                                                $attachment = array(         
                                                                    'post_mime_type' => $filetype['type'],
                                                                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),                                            
                                                                    'post_status'    => 'inherit'
                                                                );
                                                                $attach_id = wp_insert_attachment( $attachment, $upload_file['file'] );
                                                                if ( $attach_id ) {
                                                                    //update_post_meta( $destination_item_id, $field_key, $attach_id );
                                                                    $ids[] = $attach_id;
                                                                    require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                                                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                                                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_file['file'] );
                                                                    wp_update_attachment_metadata( $attach_id, $attach_data ); 

                                                                    $update_attachment = array(
                                                                        'ID'            => $attach_id,
                                                                        'post_title'    => $postmeta_field_value['post_title'],
                                                                        'post_content'  => $postmeta_field_value['post_content'],
                                                                        'post_excerpt'  => $postmeta_field_value['post_excerpt'],
                                                                    );                                            
                                                                    wp_update_post( $update_attachment );
                                                                    update_post_meta( $attach_id, '_wp_attachment_image_alt', $postmeta_field_value['wp_attachment_image_alt'] );
                                                                } 
                                                            } 
                                                        }
                                                    } 
                                                }

                                                if ( $ids != null ) {
                                                    update_post_meta( $destination_item_id, $field_key, implode( ',', $ids ) );
                                                } else {
                                                    delete_post_meta( $destination_item_id, $field_key );
                                                }
                                            } else {
                                                $postmeta_field_value = $postmeta_fields[$field_key];
                                                if ( isset( $postmeta_field_value['path'] ) && $postmeta_field_value != null ) { 
                                                    $check_attachment_args = array(
                                                        'name'              => $postmeta_field_value['post_name'],
                                                        'post_type'         => 'attachment',
                                                        'post_status'       => 'inherit',
                                                        'posts_per_page'    => 1,
                                                    );
                                                    $check_attachment = get_posts( $check_attachment_args );
                                                    if ( $check_attachment && $check_attachment[0]->post_name == $postmeta_field_value['post_name'] ) {       
                                                        $attach_id = $check_attachment[0]->ID;
                                                        update_post_meta( $destination_item_id, $field_key, $attach_id );
                                                        $update_attachment = array(
                                                            'ID'            => $attach_id,
                                                            'post_title'    => $postmeta_field_value['post_title'],
                                                            'post_content'  => $postmeta_field_value['post_content'],
                                                            'post_excerpt'  => $postmeta_field_value['post_excerpt'],
                                                        );                                            
                                                        wp_update_post( $update_attachment );
                                                        update_post_meta( $attach_id, '_wp_attachment_image_alt', $postmeta_field_value['wp_attachment_image_alt'] );
                                                    } else {                                        
                                                        $file = $postmeta_field_value['path'];
                                                        $upload_file = wp_upload_bits( basename( $file ), null, file_get_contents( $file ) );
                                                        if ( ! $upload_file['error'] ) {
                                                            $filetype = wp_check_filetype( basename( $file ), null );
                                                            $attachment = array(         
                                                                'post_mime_type' => $filetype['type'],
                                                                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),                                            
                                                                'post_status'    => 'inherit'
                                                            );
                                                            $attach_id = wp_insert_attachment( $attachment, $upload_file['file'] );
                                                            if ( $attach_id ) {
                                                                update_post_meta( $destination_item_id, $field_key, $attach_id );
                                                                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                                                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                                                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                                                $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_file['file'] );
                                                                wp_update_attachment_metadata( $attach_id, $attach_data ); 

                                                                $update_attachment = array(
                                                                    'ID'            => $attach_id,
                                                                    'post_title'    => $postmeta_field_value['post_title'],
                                                                    'post_content'  => $postmeta_field_value['post_content'],
                                                                    'post_excerpt'  => $postmeta_field_value['post_excerpt'],
                                                                );                                            
                                                                wp_update_post( $update_attachment );
                                                                update_post_meta( $attach_id, '_wp_attachment_image_alt', $postmeta_field_value['wp_attachment_image_alt'] );
                                                            } 
                                                        } 
                                                    }
                                                } else {
                                                    delete_post_meta( $destination_item_id, $field_key );
                                                }
                                            }
                                        }
                                    } else {
                                        update_post_meta( $destination_item_id, $field_key, $field_value );
                                    }
                                }
                                
                                if ( ! isset( $postmeta_fields['_sale_price'] ) ) {
                                    delete_post_meta( $destination_item_id, '_sale_price' );
                                }
                            }

                            if ( $copy_media ) {
                                if ( isset( $postmeta_fields['_thumbnail_id'] ) ) {
                                    $postmeta_field_value = $postmeta_fields['_thumbnail_id'];
                                    if ( isset( $postmeta_field_value['path'] ) && $postmeta_field_value != null ) { 
                                        $check_attachment_args = array(
                                            'name'              => $postmeta_field_value['post_name'],
                                            'post_type'         => 'attachment',
                                            'post_status'       => 'inherit',
                                            'posts_per_page'    => 1,
                                        );
                                        $check_attachment = get_posts( $check_attachment_args );
                                        if ( $check_attachment && $check_attachment[0]->post_name == $postmeta_field_value['post_name'] ) {       
                                            $attach_id = $check_attachment[0]->ID;
                                            update_post_meta( $destination_item_id, '_thumbnail_id', $attach_id );
                                            $update_attachment = array(
                                                'ID'            => $attach_id,
                                                'post_title'    => $postmeta_field_value['post_title'],
                                                'post_content'  => $postmeta_field_value['post_content'],
                                                'post_excerpt'  => $postmeta_field_value['post_excerpt'],
                                            );                                            
                                            wp_update_post( $update_attachment );
                                            update_post_meta( $attach_id, '_wp_attachment_image_alt', $postmeta_field_value['wp_attachment_image_alt'] );
                                        } else {                                        
                                            $file = $postmeta_field_value['path'];
                                            $upload_file = wp_upload_bits( basename( $file ), null, file_get_contents( $file ) );
                                            if ( ! $upload_file['error'] ) {
                                                $filetype = wp_check_filetype( basename( $file ), null );
                                                $attachment = array(         
                                                    'post_mime_type' => $filetype['type'],
                                                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),                                            
                                                    'post_status'    => 'inherit'
                                                );
                                                $attach_id = wp_insert_attachment( $attachment, $upload_file['file'] );
                                                if ( $attach_id ) {
                                                    update_post_meta( $destination_item_id, '_thumbnail_id', $attach_id );
                                                    require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_file['file'] );
                                                    wp_update_attachment_metadata( $attach_id, $attach_data ); 

                                                    $update_attachment = array(
                                                        'ID'            => $attach_id,
                                                        'post_title'    => $postmeta_field_value['post_title'],
                                                        'post_content'  => $postmeta_field_value['post_content'],
                                                        'post_excerpt'  => $postmeta_field_value['post_excerpt'],
                                                    );                                            
                                                    wp_update_post( $update_attachment );
                                                    update_post_meta( $attach_id, '_wp_attachment_image_alt', $postmeta_field_value['wp_attachment_image_alt'] );
                                                } 
                                            } 
                                        }
                                    }
                                } else {
                                    delete_post_meta( $destination_item_id, '_thumbnail_id' );
                                }
                            }
                        }
                    restore_current_blog();

                    if ( $sub_items != null && $synced ) {
                        foreach( $sub_items as $sub_item ) {
                            $this->copy_post( $sub_item->ID, $source_blog_id, 'post_type', 'product_variation', $destination_blog_id, 1, 1, $destination_item_id );                                             
                        }
                    }

                    return $destination_item_id;
                }
            }
        }
        
        public function copy_term( $term = null, $source_blog_id = 0, $type = '', $type_name = '', $destination_blog_id = 0 ) {
        
            if ( $term != null && $source_blog_id && $type && $type_name && $destination_blog_id ) {
                $current_blog_id = get_current_blog_id();
                if ( $source_blog_id != $current_blog_id ) {                
                    switch_to_blog( $source_blog_id );
                }

                global $wpdb;
                $source_item_id = $term->term_id;
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

                if ( $blog_relationships == null ) {
                    $relationship_id = uniqid();
                }
                
                $thumbnail_data = array();
                $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                if ( $thumbnail_id ) {
                    $thumbnail = get_post( $thumbnail_id );
                    $thumbnail_path = get_attached_file( $thumbnail_id );
                    if ( $thumbnail_path != null ) {
                        $thumbnail_data = array(
                            'post_title'                => wp_strip_all_tags( $thumbnail->post_title ),
                            'post_content'              => $thumbnail->post_content,                            
                            'post_excerpt'              => $thumbnail->post_excerpt,
                            'wp_attachment_image_alt'   => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ),
                            'post_name'                 => $thumbnail->post_name, 
                            'path'                      => $thumbnail_path,
                        );
                    }
                }
                
                $item_parent_id = 0;
                if ( $term->parent ) {
                    $term_parent = get_term( $term->parent, $term->taxonomy );
                    if ( $term_parent != null ) {
                        $item_parent_id = $this->copy_term( $term_parent, $source_blog_id, $type, $type_name, $destination_blog_id );
                    }
                }

                if ( $source_blog_id != $current_blog_id ) {                
                    restore_current_blog();
                }

                switch_to_blog( $destination_blog_id );
                    if ( isset( $blog_relationships[$destination_blog_id] ) ) {
                        $destination_item_id = $blog_relationships[$destination_blog_id];    
                        wp_update_term(
                            $destination_item_id,                            
                            $term->taxonomy,
                            array(
                                'name'          => $term->name,
                                'description'   => $term->description,
                                'parent'        => $item_parent_id,
                            )
                        ); 
                    } else {
                        $insert_term = wp_insert_term(
                            $term->name,
                            $term->taxonomy,
                            array(
                                'description'   => $term->description,
                                'parent'        => $item_parent_id,
                            )
                        );

                        if ( is_wp_error( $insert_term ) ) {  
                            if ( isset( $insert_term->error_data ) ) {
                                $error_data = $insert_term->error_data;
                                if ( isset( $error_data['term_exists'] ) ) {
                                    $destination_item_id = $error_data['term_exists'];                               
                                }
                            }
                        } else {
                            if ( isset( $insert_term['term_id'] ) ) {
                                $destination_item_id = $insert_term['term_id'];
                            }
                        }

                        if( $destination_item_id ) {
                            $wpdb->insert(
                                $wpdb->base_prefix . 'wcmps_relationships',
                                array( 
                                    'source_item_id'        => $source_item_id,
                                    'source_blog_id'        => $source_blog_id,
                                    'destination_item_id'   => $destination_item_id,
                                    'destination_blog_id'   => $destination_blog_id,
                                    'relationship_id'       => $relationship_id,
                                    'type'                  => $type,
                                    'type_name'             => $type_name,
                                )
                            );                       
                        }                               
                    }
                    
                    if ( $thumbnail_data != null ) {
                        $check_attachment_args = array(
                            'name'              => $thumbnail_data['post_name'],
                            'post_type'         => 'attachment',
                            'post_status'       => 'inherit',
                            'posts_per_page'    => 1,
                        );
                        $check_attachment = get_posts( $check_attachment_args );
                        $attach_id = 0;
                        if ( $check_attachment && $check_attachment[0]->post_name == $thumbnail_data['post_name'] ) {
                            $attach_id = $check_attachment[0]->ID;
                            $update_attachment = array(
                                'ID'            => $attach_id,
                                'post_title'    => $thumbnail_data['post_title'],
                                'post_content'  => $thumbnail_data['post_content'],
                                'post_excerpt'  => $thumbnail_data['post_excerpt'],
                            );                                            
                            wp_update_post( $update_attachment );
                            update_post_meta( $attach_id, '_wp_attachment_image_alt', $thumbnail_data['wp_attachment_image_alt'] );
                        } else {
                            $file = $thumbnail_data['path'];
                            $upload_file = wp_upload_bits( basename( $file ), null, file_get_contents( $file ) );
                            if ( ! $upload_file['error'] ) {
                                $filetype = wp_check_filetype( basename( $file ), null );
                                $attachment = array(         
                                    'post_mime_type' => $filetype['type'],
                                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),                                            
                                    'post_status'    => 'inherit'
                                );
                                $attach_id = wp_insert_attachment( $attachment, $upload_file['file'] );
                                if ( $attach_id ) {
                                    require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_file['file'] );
                                    wp_update_attachment_metadata( $attach_id, $attach_data ); 

                                    $update_attachment = array(
                                        'ID'            => $attach_id,
                                        'post_title'    => $thumbnail_data['post_title'],
                                        'post_content'  => $thumbnail_data['post_content'],
                                        'post_excerpt'  => $thumbnail_data['post_excerpt'],
                                    );                                            
                                    wp_update_post( $update_attachment );
                                    update_post_meta( $attach_id, '_wp_attachment_image_alt', $thumbnail_data['wp_attachment_image_alt'] );
                                } 
                            } 
                        }
                        
                        if ( $attach_id ) {
                            update_term_meta( $destination_item_id, 'thumbnail_id', $attach_id );
                        }
                    }
                    
                restore_current_blog();

                return $destination_item_id;
            }
        }
        
        public function set_destination_post_terms( $destination_post_id = 0, $destination_terms = array(), $taxonomy = '', $destination_blog_id = 0 ) {
        
            if ( $destination_post_id && $destination_terms != null && $taxonomy && $destination_blog_id ) {
                switch_to_blog( $destination_blog_id );
                    wp_set_post_terms( $destination_post_id, $destination_terms, $taxonomy ); 
                restore_current_blog();
            }
        }
        
        public function copy_attribute_taxonomy( $attribute_taxonomies, $destination_blog_id ) {
        
            switch_to_blog( $destination_blog_id );
                global $wpdb;
                $product_attribute = $attribute_taxonomies->attribute_name; 
                $attribute_taxonomy = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."woocommerce_attribute_taxonomies WHERE attribute_name='$product_attribute'" );
                if ( $attribute_taxonomy != null ) {
                    $wpdb->update( 
                        $wpdb->prefix."woocommerce_attribute_taxonomies", 
                        array(
                            'attribute_label'   => $attribute_taxonomies->attribute_label,
                            'attribute_type'    => $attribute_taxonomies->attribute_type,
                            'attribute_orderby' => $attribute_taxonomies->attribute_orderby,
                            'attribute_public'  => $attribute_taxonomies->attribute_public,
                        ), 
                        array(
                            'attribute_id' => $attribute_taxonomy->attribute_id, 
                        )                   
                    );
                } else {
                    $wpdb->insert( 
                        $wpdb->prefix."woocommerce_attribute_taxonomies", 
                        array(
                            'attribute_name'    => $attribute_taxonomies->attribute_name,
                            'attribute_label'   => $attribute_taxonomies->attribute_label,
                            'attribute_type'    => $attribute_taxonomies->attribute_type,
                            'attribute_orderby' => $attribute_taxonomies->attribute_orderby,
                            'attribute_public'  => $attribute_taxonomies->attribute_public,
                        )                  
                    );
                } 
            restore_current_blog();
        } 
        
        public function set_attribute_taxonomies( $destination_blog_id ) {
        
            switch_to_blog( $destination_blog_id );
                global $wpdb;
                $attribute_taxonomies = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."woocommerce_attribute_taxonomies" );
                if ( $attribute_taxonomies != null ) {
                    update_option( '_transient_wc_attribute_taxonomies', $attribute_taxonomies );
                } else {
                    delete_option( '_transient_wc_attribute_taxonomies' );
                }
            restore_current_blog();
        }
    }
}