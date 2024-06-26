<?php
/**
 * Abstract Child Attachment Handler
 *
 * This handles abstract child attachment related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Attachment_Child
 */
class WC_Multistore_Abstract_Attachment_Child{

	public $attachment;

	public $data;

	public function __construct( $attachment ){
		if( is_numeric( $attachment ) ){

		}else{
			$this->data = $attachment;

			$child_attachment_id = wc_multistore_get_child_attachment_id( $this->data['ID'] );
			if( $child_attachment_id ){
				$this->attachment = get_post( $child_attachment_id );
			}else{
				$child_attachment_id = $this->create();
				$this->attachment = get_post( $child_attachment_id );
			}
		}
	}

	public function create(){
		$file_array         = array();
		$file_array['name'] = basename( current( explode( '?', $this->data['url'] ) ) );

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $this->data['url'] );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return new WP_Error('wc_multistore_invalid_remote_file_url',sprintf( __( 'Error getting remote file %s.', 'woonet' ), $this->data['url'] ) . ' '. sprintf( __( 'Error: %s', 'woonet' ), $file_array['tmp_name']->get_error_message() ), array( 'status' => 400 ) );
		}

		return media_handle_sideload( $file_array );
	}


	public function save(){
		$args = array();
		$args['ID'] = $this->attachment->ID;
		$args['post_title'] = $this->data['post_title'];
		$args['post_content'] = $this->data['post_content'];


		wp_update_post( $args );
		update_post_meta( $this->attachment->ID, '_wp_attachment_image_alt', $this->data['alt'] );
		update_post_meta( $this->attachment->ID, '_woonet_master_attachment_id', $this->data['ID'] );

		return $this->attachment->ID;
	}

}