<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Image_Hooks_Master
 **/
class WC_Multistore_Image_Hooks_Master{

	/**
	 * Class instance.
	 *
	 * @var WC_Multistore_Image instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		if( ! WOO_MULTISTORE()->license->is_active() ){ return; }
		if( ! WOO_MULTISTORE()->setup->is_complete ){ return; }
		if( ! WOO_MULTISTORE()->data->is_up_to_date ){ return; }
		if( ! WOO_MULTISTORE()->site->get_type() == 'master' ){ return; }

		$this->hooks();
	}

	/**
	 * Load hooks
	 */
	public function hooks(){
		if(  WOO_MULTISTORE()->settings['enable-global-image'] != 'yes' ){ return; }

		// Product Description
		add_filter( 'wc_multistore_single_get_description' , array( $this, 'wc_multistore_single_get_description' ) );

		// Product Short Description
		add_filter( 'wc_multistore_single_get_short_description' , array( $this, 'wc_multistore_single_get_short_description' ) );
	}



	/**
	 * @param $content
	 *
	 * @return array|mixed|string|string[]
	 */
	public function wc_multistore_single_get_description( $content ){
		if ( ! preg_match_all('/<img [^>]+>/', $content, $matches ) ) {
			return $content;
		}

		$selectedImages = $attachmentIds = [];

		foreach( $matches[0] as $image ) {
			$hasClassId     = preg_match('/wp-image-(\d+)/i', $image, $classId );
			$attachmentId   = $hasClassId ? absint( $classId[1] ) : null;

			if ( $attachmentId ) {
				$selectedImages[$image] = $attachmentId;
				$attachmentIds[$attachmentId] = true;
			}
		}

		if ( count( $attachmentIds ) > 1) {
			_prime_post_caches(array_keys($attachmentIds), false);
		}

		$idPrefix = 1000000;

		foreach ( $selectedImages as $image => $attachmentId ) {
			$newAttachmentId = $idPrefix.$attachmentId;
			$search  = 'wp-image-'.$attachmentId;
			$replace = 'wp-image-'.$newAttachmentId;

			$content = str_replace(
				$search,
				$replace,
				$content
			);
		}

		return $content;
	}

	/**
	 * @param $content
	 *
	 * @return array|mixed|string|string[]
	 */
	public function wc_multistore_single_get_short_description( $content ){
		if ( ! preg_match_all('/<img [^>]+>/', $content, $matches ) ) {
			return $content;
		}

		$selectedImages = $attachmentIds = [];

		foreach( $matches[0] as $image ) {
			$hasClassId     = preg_match('/wp-image-(\d+)/i', $image, $classId );
			$attachmentId   = $hasClassId ? absint( $classId[1] ) : null;

			if ( $attachmentId ) {
				$selectedImages[$image] = $attachmentId;
				$attachmentIds[$attachmentId] = true;
			}
		}

		if ( count( $attachmentIds ) > 1) {
			_prime_post_caches(array_keys($attachmentIds), false);
		}

		$idPrefix = 1000000;

		foreach ( $selectedImages as $image => $attachmentId ) {
			$newAttachmentId = $idPrefix.$attachmentId;
			$search  = 'wp-image-'.$attachmentId;
			$replace = 'wp-image-'.$newAttachmentId;

			$content = str_replace(
				$search,
				$replace,
				$content
			);
		}

		return $content;
	}

}