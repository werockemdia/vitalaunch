<?php
/**
 * Image api Child handler.
 *
 * This handles image api child related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Image_Api_Child
 */
class WC_Multistore_Image_Api_Child extends WC_Multistore_Request {

	public $admin_url;

	public $child_site;

	public function __construct() {

		if( is_multisite() ){
			$master_store = get_site_option('wc_multistore_master_store');
			$this->child_site = WOO_MULTISTORE()->site;

			switch_to_blog($master_store);
			$this->admin_url = get_bloginfo('url') . '/wp-admin/admin-ajax.php';
			restore_current_blog();
		}else{
			$master_data = get_site_option('wc_multistore_master_connect');
			if( !empty($master_data) ){
				$this->admin_url = $master_data['master_url'] . '/wp-admin/admin-ajax.php';
				$this->child_site = WOO_MULTISTORE()->site;
			}
		}

	}

	public function ajax_query_attachments( $args ){
		$body['action'] = 'wc_multistore_ajax_query_attachments';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function ajax_get_attachment( $args ){
		$body['action'] = 'wc_multistore_ajax_get_attachment';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function ajax_send_attachment_to_editor( $args ){
		$body['action'] = 'wc_multistore_ajax_send_attachment_to_editor';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function make_content_images_responsive( $args ){
		$body['action'] = 'wc_multistore_make_content_images_responsive';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function admin_post_thumbnail_html( $args ){
		$body['action'] = 'wc_multistore_admin_post_thumbnail_html';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}

	public function post_thumbnail_html( $args ){
		$body['action'] = 'wc_multistore_post_thumbnail_html';
		$body['key'] = $this->child_site->get_id();
		$body['data'] = $args;

		$args = array(
			'method' => 'POST',
			'body' => $body
		);

		return $this->send( $this->admin_url, $args);
	}


}