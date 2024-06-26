<?php
/**
 * Abstract Master Comment Handler
 *
 * This handles Abstract master comment related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Comment_Master
 */
class WC_Multistore_Abstract_Comment_Master{

	public $comment;

	public $data;


	public function __construct( $comment ) {
		$this->comment = $comment;
		$this->data = $this->set_data();
	}

	public function set_data(){
		$data = array();
		$data['master_blog_id'] = get_current_blog_id();
		$data['comment_ID'] = $this->comment->comment_ID;
		$data['comment_post_ID'] = $this->comment->comment_post_ID;
		$data['comment_author'] = $this->comment->comment_author;
		$data['comment_author_email'] = $this->comment->comment_author_email;
		$data['comment_author_url'] = $this->comment->comment_author_url;
		$data['comment_author_IP'] = $this->comment->comment_author_IP;
		$data['comment_date'] = $this->comment->comment_date;
		$data['comment_date_gmt'] = $this->comment->comment_date_gmt;
		$data['comment_content'] = $this->comment->comment_content;
		$data['comment_karma'] = $this->comment->comment_karma;
		$data['comment_approved'] = $this->comment->comment_approved;
		$data['comment_agent'] = $this->comment->comment_agent;
		$data['comment_type'] = $this->comment->comment_type;
		$data['comment_parent'] = $this->comment->comment_parent;
		$data['comment_meta'] = get_comment_meta($this->comment->comment_ID);

		return $data;
	}
}