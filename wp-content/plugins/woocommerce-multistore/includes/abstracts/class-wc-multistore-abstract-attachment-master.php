<?php
/**
 * Abstract Master Attachment Handler
 *
 * This handles Abstract master attachment related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Attachment_Master
 */
class WC_Multistore_Abstract_Attachment_Master{

	public $attachment;

	public $data;


	public function __construct( $attachment ) {
		$this->attachment = $attachment;
		$this->data = $this->set_data();
	}

	public function set_data(){
		$data = array();
		$data['ID'] = $this->attachment->ID;
		$data['post_author'] = $this->attachment->post_author;
		$data['post_date'] = $this->attachment->post_date;
		$data['post_date_gmt'] = $this->attachment->post_date_gmt;
		$data['post_content'] = $this->attachment->post_content;
		$data['post_title'] = $this->attachment->post_title;
		$data['post_excerpt'] = $this->attachment->post_excerpt;
		$data['post_status'] = $this->attachment->post_status;
		$data['comment_status'] = $this->attachment->comment_status;
		$data['ping_status'] = $this->attachment->ping_status;
		$data['post_password'] = $this->attachment->post_password;
		$data['to_ping'] = $this->attachment->to_ping;
		$data['pinged'] = $this->attachment->pinged;
		$data['post_modified'] = $this->attachment->post_modified;
		$data['post_modified_gmt'] = $this->attachment->post_modified_gmt;
		$data['post_content_filtered'] = $this->attachment->post_content_filtered;
		$data['post_parent'] = $this->attachment->post_parent;
		$data['menu_order'] = $this->attachment->menu_order;
		$data['post_type'] = $this->attachment->post_type;
		$data['post_mime_type'] = $this->attachment->post_mime_type;
		$data['comment_count'] = $this->attachment->comment_count;
		$data['filter'] = $this->attachment->filter;
		$data['wp_uploads_dir'] = wp_get_upload_dir();;
		$data['meta'] = get_post_meta( $this->attachment->ID, '_wp_attachment_metadata', true );
		$data['alt'] = get_post_meta( $this->attachment->ID, '_wp_attachment_image_alt', true );
		$data['url'] = wp_get_attachment_url( $this->attachment->ID );


		return $data;
	}
}