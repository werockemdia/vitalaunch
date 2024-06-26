<?php
/**
 * Product Review Child Handler
 *
 * This handles product review child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Review_Child
 */
class WC_Multistore_Product_Review_Child extends WC_Multistore_Abstract_Comment_Child {
	public function save($master_product_id){
		parent::save($master_product_id);
		$this->set_review_rating();
		$this->set_review_verified();
	}

	public function set_review_rating(){
		if( ! empty( $this->data['comment_meta']['rating'] ) && !empty( $this->data['comment_meta']['rating'][0] ) ){
			$rating = $this->data['comment_meta']['rating'][0];
			update_comment_meta( $this->comment->comment_ID, 'rating', $rating );
		}else{
			delete_comment_meta($this->comment->comment_ID, 'rating' );
		}
	}

	public function set_review_verified(){
		if( ! empty( $this->data['comment_meta']['verified'] ) && !empty( $this->data['comment_meta']['verified'][0] ) ){
			$verified = $this->data['comment_meta']['verified'][0];
			update_comment_meta( $this->comment->comment_ID, 'verified', $verified );
		}else{
			delete_comment_meta($this->comment->comment_ID, 'verified' );
		}
	}
}