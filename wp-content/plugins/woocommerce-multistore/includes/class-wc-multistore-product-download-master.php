<?php
/**
 * Product Download Master Handler
 *
 * This handles product download master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Product_Download_Master
 */
class WC_Multistore_Product_Download_Master {
	public $wc_download;

	public $data;

	public function __construct( $wc_download ) {
		$this->wc_download = $wc_download;
		$this->data       = $this->set_data();
	}

	public function set_data(){
		$data = $this->wc_download->get_data();
		if( ! empty( $data )  && ! empty( $data['file'] ) ){
			$data['is_local'] = 'no';
			if( str_contains( $data['file'], WOO_MSTORE_INSTANCE ) ){
				$data['is_local'] = 'yes';
				$attachment_id = wc_multistore_get_attachment_by_url($data['file']);
				if( $attachment_id ){
					$attachment = get_post($attachment_id);
					$wc_multistore_attachment_master = new WC_Multistore_Attachment_Master($attachment);
					$data['file'] = $wc_multistore_attachment_master->data;
				}
			}
		}
		return $data;
	}
}