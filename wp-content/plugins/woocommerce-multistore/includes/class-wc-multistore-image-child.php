<?php
/**
 * Child Attachment Handler
 *
 * This handles child attachment related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Attachment_Master
 */
class WC_Multistore_Image_Child extends  WC_Multistore_Abstract_Attachment_Child {

	public $idPrefix  = 1000000;

	public function __construct( $attachment ){
		if( !function_exists('media_sideload_image') ){
			require_once(ABSPATH . 'wp-admin' . '/includes/image.php');
			require_once(ABSPATH . 'wp-admin' . '/includes/file.php');
			require_once(ABSPATH . 'wp-admin' . '/includes/media.php');
		}

		if( is_numeric( $attachment ) ){

		}else{
			$this->data = $attachment;

			if( WOO_MULTISTORE()->settings['enable-global-image'] != 'yes' ){
				$child_attachment_id = wc_multistore_get_child_attachment_id( $attachment['ID'] );
				if( $child_attachment_id ){
					$this->attachment = get_post( $child_attachment_id );
				}else{
					$child_attachment_id =  $this->create();
					$this->attachment = get_post( $child_attachment_id );
				}
			}
		}
	}

	public function create() {
		return  media_sideload_image( $this->data['url'], 0, $this->data['post_content'],'id' );
	}


	public function save(){
		if( WOO_MULTISTORE()->settings['enable-global-image'] == 'yes' ){
			$child_attachment_id = $this->idPrefix . $this->data['ID'];
			if( ! is_multisite() ){
				$global_image_data     = array(
					'meta_data'    => $this->data['meta'],
					'uploads_dir'  => $this->data['wp_uploads_dir'],
					'src'          => $this->data['url'],
					'alt'          => $this->data['alt']
				);
				wc_multistore_update_global_image_metadata($this->data['ID'], $global_image_data );
			}
			return $child_attachment_id;
		}

		return parent::save();
	}

}