<?php
/**
 * Abstract Child Comment Handler
 *
 * This handles abstract child comment related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Abstract_Comment_Child
 */
class WC_Multistore_Abstract_Comment_Child{

	public $comment;

	public $data;

	public function __construct( $comment ){
		if( is_numeric( $comment ) ){

		}else{
			$child_comment_id = wc_multistore_get_child_comment_id( $comment['comment_ID'], $comment['master_blog_id'] );
			if( $child_comment_id ){
				$this->comment = get_comment( $child_comment_id );
			}else{
				$child_comment_id = wp_insert_comment( $comment['comment_content']);
				$this->comment = get_comment( $child_comment_id );
			}

			$this->data = $comment;
		}

	}


	public function save($master_product_id){
		$meta_key = 'wc_multistore_parent_id_'.$this->data['comment_ID'].'_sid_'.$this->data['master_blog_id'];
		$args = array(
			'comment_ID' => $this->comment->comment_ID,
			'comment_post_ID' => $master_product_id,
			'comment_author' => $this->data['comment_author'],
			'comment_content' => $this->data['comment_content'],
			'comment_author_email' => $this->data['comment_author_email'],
			'comment_author_url' => $this->data['comment_author_url'],
			'comment_author_IP' => $this->data['comment_author_IP'],
			'comment_date' => $this->data['comment_date'],
			'comment_approved' => $this->data['comment_approved'],
		);
		wp_update_comment($args);
		update_comment_meta( $this->comment->comment_ID, $meta_key, 1 );
	}

}