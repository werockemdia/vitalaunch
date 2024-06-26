<?php

defined( 'ABSPATH' ) || exit;

/**
 * WC_Multistore_Image_Hooks_Child
 **/
class WC_Multistore_Image_Hooks_Child{

    public $master_site;

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
		if( WOO_MULTISTORE()->site->get_type() == 'master' ){ return; }

        if( is_multisite() ){
            $this->master_site = get_site_option('wc_multistore_master_store');
        }

		$this->includes();
		$this->hooks();
	}


	/**
	 * Load required files
	 */
	public function includes(){
		if(  WOO_MULTISTORE()->settings['enable-global-image'] != 'yes' ){ return; }
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}


	/**
	 * Load hooks
	 */
	public function hooks(){
		if(  WOO_MULTISTORE()->settings['enable-global-image'] != 'yes' ){ return; }

		// Attachment
		add_action('wp_ajax_query-attachments', array( $this, 'ajax_query_attachments' ), 0 );
		add_action('wp_ajax_get-attachment',  array( $this, 'ajax_get_attachment' ), 0 );
		add_action('wp_ajax_send-attachment-to-editor', array( $this, 'ajax_send_attachment_to_editor' ), 0 );
		add_filter('wp_get_attachment_image_src', array( $this, 'attachment_image_src' ), 99, 4 );
		add_filter('media_view_strings', array( $this, 'media_strings') );
		remove_filter('the_content', 'wp_filter_content_tags');
		add_filter('the_content', array( $this, 'filter_content_tags' ) );
		remove_filter('woocommerce_short_description', 'wp_filter_content_tags' );
		add_filter('woocommerce_short_description', array( $this, 'filter_content_tags' ) );

		// Thumbnail
		add_action('save_post', array( $this, 'save_thumbnail_meta' ), 99);
		add_filter('admin_post_thumbnail_html', array( $this, 'admin_post_thumbnail_html' ), 99, 3);
		add_filter('post_thumbnail_html', array( $this, 'post_thumbnail_html' ), 99, 5);

		// Woocommerce
		add_action('woocommerce_product_get_image', array( $this, 'filter_woocommerce_content_tags'), 99, 2 );
		add_action('woocommerce_available_variation', array( $this, 'available_variation'), 99, 1 );
		remove_action( 'product_cat_edit_form_fields', array( WC_Admin_Taxonomies::get_instance(), 'edit_category_fields' ), 10 );
		remove_action( 'manage_product_cat_custom_column', array( WC_Admin_Taxonomies::get_instance(), 'product_cat_column' ), 10, 3 );
		remove_action('wp_ajax_woocommerce_load_variations', array( 'WC_AJAX','load_variations') );
		add_filter('manage_product_cat_custom_column', array( $this, 'product_cat_column'), 99, 3 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 11 );
		add_action( 'wp_ajax_woocommerce_load_variations', array( $this, 'load_variations' ) );
	}


	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts(){
		if( get_current_screen()->post_type != 'product' ){ return; }
		wp_register_script( 'wc-multistore-global-image-js', plugins_url( '/assets/js/wc-multistore-global-image.js',  dirname( __FILE__, 2 ) ), array('media-views'), WOO_MSTORE_VERSION );
		wp_enqueue_script( 'wc-multistore-global-image-js' );
	}


	/**
	 *
	 */
	public function ajax_query_attachments(){
		$query = isset($_REQUEST['query']) // csrf ok
			? (array) wp_unslash($_REQUEST['query']) // csrf ok
			: [];

		if ( ! empty( $query['global_image'] ) ) {
            if(is_multisite()){
	            switch_to_blog( $this->master_site );
	            add_filter('wp_prepare_attachment_for_js', array( $this, 'prepare_attachment_for_js' ), 0);
            }else{
                $wc_multistore_image_api_child = new WC_Multistore_Image_Api_Child();
                $result = $wc_multistore_image_api_child->ajax_query_attachments($_REQUEST);
	            $posts          = $result['data']['data']['posts'];
	            $total_posts    = $result['data']['data']['total_posts'];
	            $max_pages      = $result['data']['data']['max_pages'];

	            header( 'X-WP-Total: ' . (int) $total_posts );
	            header( 'X-WP-TotalPages: ' . (int) $max_pages );

	            wp_send_json_success( $posts );
            }
		}

		wp_ajax_query_attachments();
	}

	/**
	 *
	 */
	public function ajax_get_attachment(){
		$attachmentId   = (int) wp_unslash($_REQUEST['id']);
		$idPrefix       = 1000000;

		if ($this->id_prefix_included_in_attachment_id( $attachmentId, $idPrefix ) ) {
			$attachmentId = $this->strip_site_id_prefix_from_attachment_id( $idPrefix, $attachmentId );
			$_REQUEST['id'] = $attachmentId;

            if(is_multisite()){
	            switch_to_blog( $this->master_site );
	            add_filter('wp_prepare_attachment_for_js', array( $this, 'prepare_attachment_for_js' ), 0);
	            restore_current_blog();
            }else{
	            $wc_multistore_image_api_child = new WC_Multistore_Image_Api_Child();
	            $result = $wc_multistore_image_api_child->ajax_get_attachment($_REQUEST);
	            $attachment     = $result['data']['data'];
	            wp_send_json_success( $attachment );
            }

		}

		wp_ajax_get_attachment();
	}

	/**
	 * @param $response
	 *
	 * @return mixed
	 */
	public function prepare_attachment_for_js( $response ){
		$idPrefix = 1000000;

		$response['id'] = (int) ($idPrefix.$response['id']); // Unique ID, must be a number.
		$response['nonces']['update'] = false;
		$response['nonces']['edit'] = false;
		$response['nonces']['delete'] = false;
		$response['editLink'] = false;

		return $response;
	}

	/**
	 *
	 */
	public function ajax_send_attachment_to_editor(){
		$attachment     = wp_unslash( $_POST['attachment'] ); // csrf ok
		$attachmentId   = (int) $attachment['id'];
		$idPrefix       = 1000000;

		if ($this->id_prefix_included_in_attachment_id($attachmentId, $idPrefix)) {
			$attachment['id'] = $this->strip_site_id_prefix_from_attachment_id($idPrefix, $attachmentId);
			$_POST['attachment'] = wp_slash($attachment);

            if(is_multisite()){
	            switch_to_blog($this->master_site);

	            add_filter( 'mediaSendToEditor', array( $this, 'media_send_to_editor' ), 10, 2);
            }else{
	            $wc_multistore_image_api_child = new WC_Multistore_Image_Api_Child();
	            $result = $wc_multistore_image_api_child->ajax_send_attachment_to_editor($_REQUEST);
	            $html  = $result['data']['data'];

	            wp_send_json_success( $html );
            }

		}

		wp_ajax_send_attachment_to_editor();
	}

	/**
	 * @param $html
	 * @param $id
	 *
	 * @return array|string|string[]
	 */
	public function media_send_to_editor( $html, $id){
		$idPrefix = 1000000;
		$newId = $idPrefix.$id; // Unique ID, must be a number.

		$search = 'wp-image-'.$id;
		$replace = 'wp-image-'.$newId;

		return str_replace($search, $replace, $html);
	}

	/**
	 * @param $image
	 * @param $attachmentId
	 * @param $size
	 * @param $icon
	 *
	 * @return mixed
	 */
	public function attachment_image_src( $image, $attachmentId, $size, $icon ){
        if( is_multisite() ){
	        $attachmentId   = (int) $attachmentId;
	        $idPrefix       = 1000000;

	        if (!$this->id_prefix_included_in_attachment_id($attachmentId, $idPrefix)) {
		        return $image;
	        }

	        $attachmentId = $this->strip_site_id_prefix_from_attachment_id($idPrefix, $attachmentId);
	        switch_to_blog( $this->master_site );
	        $image = wp_get_attachment_image_src($attachmentId, $size, $icon);
	        restore_current_blog();

	        return $image;
        }else{
	        $attachmentId   = (int) $attachmentId;
	        $idPrefix       = 1000000;

	        if ( ! $this->id_prefix_included_in_attachment_id( $attachmentId, $idPrefix ) ) {
		        return $image;
	        }

	        $attachmentId   = $this->strip_site_id_prefix_from_attachment_id( $idPrefix, $attachmentId );

	        $global_image_data = wc_multistore_get_global_image_metadata($attachmentId);


	        if( ! $global_image_data || empty( $global_image_data ) ){
		        return $image;
	        }

	        $image_data     = $global_image_data;

	        $image          = $this->image_downsize( $attachmentId, $image_data, $size );

	        if ( ! $image ) {
		        $src = false;

		        if ( $icon ) {
			        $src = wp_mime_type_icon( $attachmentId );

			        if ( $src ) {
				        $icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/media' );

				        $src_file               = $icon_dir . '/' . wp_basename( $src );
				        list( $width, $height ) = wp_getimagesize( $src_file );
			        }
		        }

		        if ( $src && $width && $height ) {
			        $image = array( $src, $width, $height, false );
		        }
	        }

	        return apply_filters( 'wp_get_attachment_image_src', $image, $attachmentId, $size, $icon );
        }
	}

	public function image_downsize( $attachment_id, $image_data, $size ){
		$file               = $image_data['meta_data']['file'];
		$uploads            = $image_data['uploads_dir'];

		$is_image = true;

		$out = apply_filters( 'image_downsize', false, $attachment_id, $size );

		if ( $out ) {
			return $out;
		}

		$img_url          = $this->get_attachment_url( $file, $uploads, $attachment_id );
		$meta             = $image_data['meta_data'];
		$width            = 0;
		$height           = 0;
		$is_intermediate  = false;
		$img_url_basename = wp_basename( $img_url );

		// If the file isn't an image, attempt to replace its URL with a rendered image from its meta.
		// Otherwise, a non-image type could be returned.
		if ( ! $is_image ) {
			if ( ! empty( $meta['sizes']['full'] ) ) {
				$img_url          = str_replace( $img_url_basename, $meta['sizes']['full']['file'], $img_url );
				$img_url_basename = $meta['sizes']['full']['file'];
				$width            = $meta['sizes']['full']['width'];
				$height           = $meta['sizes']['full']['height'];
			} else {
				return false;
			}
		}

		// Try for a new style intermediate size.
		$intermediate = $this->image_get_intermediate_size( $meta, $uploads, $attachment_id, $size );

		if ( $intermediate ) {
			$img_url         = str_replace( $img_url_basename, $intermediate['file'], $img_url );
			$width           = $intermediate['width'];
			$height          = $intermediate['height'];
			$is_intermediate = true;
		} elseif ( 'thumbnail' === $size ) {
			// Fall back to the old thumbnail.
			$thumb_file = $image_data['meta_data']['thumb'];
			$info       = null;

			if ( $thumb_file ) {
				$info = wp_getimagesize( $thumb_file );
			}

			if ( $thumb_file && $info ) {
				$img_url         = str_replace( $img_url_basename, wp_basename( $thumb_file ), $img_url );
				$width           = $info[0];
				$height          = $info[1];
				$is_intermediate = true;
			}
		}

		if ( ! $width && ! $height && isset( $meta['width'], $meta['height'] ) ) {
			// Any other type: use the real image.
			$width  = $meta['width'];
			$height = $meta['height'];
		}

		if ( $img_url ) {
			// We have the actual image size, but might need to further constrain it if content_width is narrower.
			list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );

			return array( $img_url, $width, $height, $is_intermediate );
		}

		return false;
	}

	public function get_attachment_url( $file, $uploads, $attachmentId ){
		$url = '';
		// Get attached file.
		if ( $file ) {
			// Get upload directory.
			if ( $uploads ) {
				// Check that the upload base exists in the file location.
				if ( 0 === strpos( $file, $uploads['basedir'] ) ) {
					// Replace file location with url location.
					$url = str_replace( $uploads['basedir'], $uploads['baseurl'], $file );
				} elseif ( false !== strpos( $file, 'wp-content/uploads' ) ) {
					// Get the directory name relative to the basedir (back compat for pre-2.7 uploads).
					$url = trailingslashit( $uploads['baseurl'] . '/' . _wp_get_attachment_relative_path( $file ) ) . wp_basename( $file );
				} else {
					// It's a newly-uploaded file, therefore $file is relative to the basedir.
					$url = $uploads['baseurl'] . "/$file";
				}
			}
		}

		$url = apply_filters( 'wp_get_attachment_url', $url, $attachmentId );

		if ( ! $url ) {
			return false;
		}

		return $url;
	}

	public function image_get_intermediate_size( $imagedata, $uploads, $post_id, $size ){

		if ( ! $size || ! is_array( $imagedata ) || empty( $imagedata['sizes'] ) ) {
			return false;
		}

		$data = array();

		// Find the best match when '$size' is an array.
		if ( is_array( $size ) ) {
			$candidates = array();

			if ( ! isset( $imagedata['file'] ) && isset( $imagedata['sizes']['full'] ) ) {
				$imagedata['height'] = $imagedata['sizes']['full']['height'];
				$imagedata['width']  = $imagedata['sizes']['full']['width'];
			}

			foreach ( $imagedata['sizes'] as $_size => $data ) {
				// If there's an exact match to an existing image size, short circuit.
				if ( (int) $data['width'] === (int) $size[0] && (int) $data['height'] === (int) $size[1] ) {
					$candidates[ $data['width'] * $data['height'] ] = $data;
					break;
				}

				// If it's not an exact match, consider larger sizes with the same aspect ratio.
				if ( $data['width'] >= $size[0] && $data['height'] >= $size[1] ) {
					// If '0' is passed to either size, we test ratios against the original file.
					if ( 0 === $size[0] || 0 === $size[1] ) {
						$same_ratio = wp_image_matches_ratio( $data['width'], $data['height'], $imagedata['width'], $imagedata['height'] );
					} else {
						$same_ratio = wp_image_matches_ratio( $data['width'], $data['height'], $size[0], $size[1] );
					}

					if ( $same_ratio ) {
						$candidates[ $data['width'] * $data['height'] ] = $data;
					}
				}
			}

			if ( ! empty( $candidates ) ) {
				// Sort the array by size if we have more than one candidate.
				if ( 1 < count( $candidates ) ) {
					ksort( $candidates );
				}

				$data = array_shift( $candidates );
				/*
				* When the size requested is smaller than the thumbnail dimensions, we
				* fall back to the thumbnail size to maintain backward compatibility with
				* pre 4.6 versions of WordPress.
				*/
			} elseif ( ! empty( $imagedata['sizes']['thumbnail'] ) && $imagedata['sizes']['thumbnail']['width'] >= $size[0] && $imagedata['sizes']['thumbnail']['width'] >= $size[1] ) {
				$data = $imagedata['sizes']['thumbnail'];
			} else {
				return false;
			}

			// Constrain the width and height attributes to the requested values.
			list( $data['width'], $data['height'] ) = image_constrain_size_for_editor( $data['width'], $data['height'], $size );

		} elseif ( ! empty( $imagedata['sizes'][ $size ] ) ) {
			$data = $imagedata['sizes'][ $size ];
		}

		// If we still don't have a match at this point, return false.
		if ( empty( $data ) ) {
			return false;
		}

		// Include the full filesystem path of the intermediate file.
		if ( empty( $data['path'] ) && ! empty( $data['file'] ) && ! empty( $imagedata['file'] ) ) {
			$file_url     = $this->get_attachment_url( $imagedata['file'], $uploads, $post_id );
			$data['path'] = path_join( dirname( $imagedata['file'] ), $data['file'] );
			$data['url']  = path_join( dirname( $file_url ), $data['file'] );
		}

		return $data;
		//return apply_filters( 'image_get_intermediate_size', $data, $post_id, $size );
	}

	/**
	 * @param $strings
	 *
	 * @return mixed
	 */
	public function media_strings($strings){
		$strings['globalImageTitle'] = esc_html__('Global Image', 'woonet');

		return $strings;
	}

	/**
	 * @param $content
	 *
	 * @return array|mixed|string|string[]
	 */
	public function filter_content_tags( $content ){

		if ( ! preg_match_all('/<img [^>]+>/', $content, $matches ) ) {
			return $content;
		}

		$selectedImages = $attachmentIds = [];

		foreach ( $matches[0] as $image ) {
			$hasSrcset = strpos($image, ' srcset=') !== false;
			$hasClassId = preg_match('/wp-image-(\d+)/i', $image, $classId);
			$attachmentId = !$hasSrcset && $hasClassId
				? absint($classId[1])
				: null;
			if ($attachmentId) {
				// If exactly the same image tag is used more than once, overwrite it.
				// All identical tags will be replaced later with 'str_replace()'.
				$selectedImages[$image] = $attachmentId;
				// Overwrite the ID when the same image is included more than once.
				$attachmentIds[$attachmentId] = true;
			}
		}

		if (count($attachmentIds) > 1) {
			// Warm the object cache with post and meta information for all found
			// images to avoid making individual database calls.
			_prime_post_caches(array_keys($attachmentIds), false);
		}

		$idPrefix = 1000000;

		foreach ($selectedImages as $image => $attachmentId) {
			if (!$this->id_prefix_included_in_attachment_id($attachmentId, $idPrefix)) {
				$imageMeta = wp_get_attachment_metadata($attachmentId);
				$content = str_replace(
					$image,
					wp_image_add_srcset_and_sizes($image, $imageMeta, $attachmentId),
					$content
				);
				continue;
			}

			$globalAttachmentId = $this->strip_site_id_prefix_from_attachment_id($idPrefix, $attachmentId);

            if(is_multisite()){
	            switch_to_blog($this->master_site);
	            $imageMeta = wp_get_attachment_metadata($globalAttachmentId);
	            $content = str_replace($image, wp_image_add_srcset_and_sizes($image, $imageMeta, $attachmentId), $content);
	            restore_current_blog();
            }else{
	            $data               = array(
		            'attachment_id' => $globalAttachmentId,
		            'image'         => $image,
		            'attachmentId'  => $attachmentId,
		            'content'       => $content
	            );
	            $wc_multistore_image_api_child = new WC_Multistore_Image_Api_Child();
	            $result = $wc_multistore_image_api_child->make_content_images_responsive($data);
	            $content                = $result['data']['data'];
            }

		}

		return $content;
	}

	/**
	 * @param $image
	 * @param $product
	 *
	 * @return array|false|mixed|string|string[]
	 */
	public function filter_woocommerce_content_tags( $image, $product ){
		$idPrefix = 1000000;

		if ( ! $this->id_prefix_included_in_attachment_id( $product->get_image_id(), $idPrefix ) ) {
			return $image;
		}

		$globalAttachmentId = $this->strip_site_id_prefix_from_attachment_id( $idPrefix, $product->get_image_id() );

        if(is_multisite()){
	        switch_to_blog($this->master_site);
	        $imageMeta = wp_get_attachment_metadata($globalAttachmentId);
	        $image = str_replace($image, wp_image_add_srcset_and_sizes($image, $imageMeta, $globalAttachmentId), $image);
	        restore_current_blog();
        }else{
            $global_image_data = wc_multistore_get_global_image_metadata($globalAttachmentId);
	        if ( empty( $global_image_data ) || empty( $global_image_data['meta_data'] ) ) {
		        return false;
	        }
	        $data       = $global_image_data['meta_data'];
	        $upload_dir = $global_image_data['uploads_dir'];
	        $data       =  apply_filters( 'wp_get_attachment_metadata', $data, $globalAttachmentId );

	        $image =  str_replace( $image, $this->image_add_srcset_and_sizes( $image, $data, $globalAttachmentId, $upload_dir ), $image );
        }


		return $image;

	}

	function image_add_srcset_and_sizes( $image, $image_meta, $attachment_id, $upload_dir ) {
		// Ensure the image meta exists.
		if ( empty( $image_meta['sizes'] ) ) {
			return $image;
		}

		$image_src         = preg_match( '/src="([^"]+)"/', $image, $match_src ) ? $match_src[1] : '';
		list( $image_src ) = explode( '?', $image_src );

		// Return early if we couldn't get the image source.
		if ( ! $image_src ) {
			return $image;
		}

		// Bail early if an image has been inserted and later edited.
		if ( preg_match( '/-e[0-9]{13}/', $image_meta['file'], $img_edit_hash ) &&
		     strpos( wp_basename( $image_src ), $img_edit_hash[0] ) === false ) {

			return $image;
		}

		$width  = preg_match( '/ width="([0-9]+)"/', $image, $match_width ) ? (int) $match_width[1] : 0;
		$height = preg_match( '/ height="([0-9]+)"/', $image, $match_height ) ? (int) $match_height[1] : 0;

		if ( $width && $height ) {
			$size_array = array( $width, $height );
		} else {
			$size_array = wp_image_src_get_dimensions( $image_src, $image_meta, $attachment_id );
			if ( ! $size_array ) {
				return $image;
			}
		}

		$srcset = $this->calculate_image_srcset( $size_array, $image_src, $image_meta, $upload_dir, $attachment_id  );

		if ( $srcset ) {
			// Check if there is already a 'sizes' attribute.
			$sizes = strpos( $image, ' sizes=' );

			if ( ! $sizes ) {
				$sizes = wp_calculate_image_sizes( $size_array, $image_src, $image_meta, $attachment_id );
			}
		}

		if ( $srcset && $sizes ) {
			// Format the 'srcset' and 'sizes' string and escape attributes.
			$attr = sprintf( ' srcset="%s"', esc_attr( $srcset ) );

			if ( is_string( $sizes ) ) {
				$attr .= sprintf( ' sizes="%s"', esc_attr( $sizes ) );
			}

			// Add the srcset and sizes attributes to the image markup.
			return preg_replace( '/<img ([^>]+?)[\/ ]*>/', '<img $1' . $attr . ' />', $image );
		}

		return $image;
	}

	function calculate_image_srcset( $size_array, $image_src, $image_meta, $upload_dir, $attachment_id = 0 ) {

		$image_meta = apply_filters( 'wp_calculate_image_srcset_meta', $image_meta, $size_array, $image_src, $attachment_id );

		if ( empty( $image_meta['sizes'] ) || ! isset( $image_meta['file'] ) || strlen( $image_meta['file'] ) < 4 ) {
			return false;
		}

		$image_sizes = $image_meta['sizes'];

		// Get the width and height of the image.
		$image_width  = (int) $size_array[0];
		$image_height = (int) $size_array[1];

		// Bail early if error/no width.
		if ( $image_width < 1 ) {
			return false;
		}

		$image_basename = wp_basename( $image_meta['file'] );


		if ( ! isset( $image_sizes['thumbnail']['mime-type'] ) || 'image/gif' !== $image_sizes['thumbnail']['mime-type'] ) {
			$image_sizes[] = array(
				'width'  => $image_meta['width'],
				'height' => $image_meta['height'],
				'file'   => $image_basename,
			);
		} elseif ( strpos( $image_src, $image_meta['file'] ) ) {
			return false;
		}

		// Retrieve the uploads sub-directory from the full size image.
		$dirname = _wp_get_attachment_relative_path( $image_meta['file'] );

		if ( $dirname ) {
			$dirname = trailingslashit( $dirname );
		}


		$image_baseurl = trailingslashit( $upload_dir['baseurl'] ) . $dirname;

		/*
		 * If currently on HTTPS, prefer HTTPS URLs when we know they're supported by the domain
		 * (which is to say, when they share the domain name of the current request).
		 */
		if ( is_ssl() && 'https' !== substr( $image_baseurl, 0, 5 ) && parse_url( $image_baseurl, PHP_URL_HOST ) === $_SERVER['HTTP_HOST'] ) {
			$image_baseurl = set_url_scheme( $image_baseurl, 'https' );
		}

		/*
		 * Images that have been edited in WordPress after being uploaded will
		 * contain a unique hash. Look for that hash and use it later to filter
		 * out images that are leftovers from previous versions.
		 */
		$image_edited = preg_match( '/-e[0-9]{13}/', wp_basename( $image_src ), $image_edit_hash );

		/**
		 * Filters the maximum image width to be included in a 'srcset' attribute.
		 *
		 * @since 4.4.0
		 *
		 * @param int   $max_width  The maximum image width to be included in the 'srcset'. Default '2048'.
		 * @param int[] $size_array {
		 *     An array of requested width and height values.
		 *
		 *     @type int $0 The width in pixels.
		 *     @type int $1 The height in pixels.
		 * }
		 */
		$max_srcset_image_width = apply_filters( 'max_srcset_image_width', 2048, $size_array );

		// Array to hold URL candidates.
		$sources = array();

		/**
		 * To make sure the ID matches our image src, we will check to see if any sizes in our attachment
		 * meta match our $image_src. If no matches are found we don't return a srcset to avoid serving
		 * an incorrect image. See #35045.
		 */
		$src_matched = false;

		/*
		 * Loop through available images. Only use images that are resized
		 * versions of the same edit.
		 */
		foreach ( $image_sizes as $image ) {
			$is_src = false;

			// Check if image meta isn't corrupted.
			if ( ! is_array( $image ) ) {
				continue;
			}

			// If the file name is part of the `src`, we've confirmed a match.
			if ( ! $src_matched && false !== strpos( $image_src, $dirname . $image['file'] ) ) {
				$src_matched = true;
				$is_src      = true;
			}

			// Filter out images that are from previous edits.
			if ( $image_edited && ! strpos( $image['file'], $image_edit_hash[0] ) ) {
				continue;
			}

			/*
			 * Filters out images that are wider than '$max_srcset_image_width' unless
			 * that file is in the 'src' attribute.
			 */
			if ( $max_srcset_image_width && $image['width'] > $max_srcset_image_width && ! $is_src ) {
				continue;
			}

			// If the image dimensions are within 1px of the expected size, use it.
			if ( wp_image_matches_ratio( $image_width, $image_height, $image['width'], $image['height'] ) ) {
				// Add the URL, descriptor, and value to the sources array to be returned.
				$source = array(
					'url'        => $image_baseurl . $image['file'],
					'descriptor' => 'w',
					'value'      => $image['width'],
				);

				// The 'src' image has to be the first in the 'srcset', because of a bug in iOS8. See #35030.
				if ( $is_src ) {
					$sources = array( $image['width'] => $source ) + $sources;
				} else {
					$sources[ $image['width'] ] = $source;
				}
			}
		}

		/**
		 * Filters an image's 'srcset' sources.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $sources {
		 *     One or more arrays of source data to include in the 'srcset'.
		 *
		 *     @type array $width {
		 *         @type string $url        The URL of an image source.
		 *         @type string $descriptor The descriptor type used in the image candidate string,
		 *                                  either 'w' or 'x'.
		 *         @type int    $value      The source width if paired with a 'w' descriptor, or a
		 *                                  pixel density value if paired with an 'x' descriptor.
		 *     }
		 * }
		 * @param array $size_array     {
		 *     An array of requested width and height values.
		 *
		 *     @type int $0 The width in pixels.
		 *     @type int $1 The height in pixels.
		 * }
		 * @param string $image_src     The 'src' of the image.
		 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
		 * @param int    $attachment_id Image attachment ID or 0.
		 */
		$sources = apply_filters( 'wp_calculate_image_srcset', $sources, $size_array, $image_src, $image_meta, $attachment_id );

		// Only return a 'srcset' value if there is more than one source.
		if ( ! $src_matched || ! is_array( $sources ) || count( $sources ) < 2 ) {
			return false;
		}

		$srcset = '';

		foreach ( $sources as $source ) {
			$srcset .= str_replace( ' ', '%20', $source['url'] ) . ' ' . $source['value'] . $source['descriptor'] . ', ';
		}

		return rtrim( $srcset, ', ' );
	}


	/**
	 * @param $available_variation
	 *
	 * @return false|mixed
	 */
	function available_variation( $available_variation ){
		$idPrefix = 1000000;

		if ( ! $this->id_prefix_included_in_attachment_id( $available_variation['image_id'], $idPrefix ) ) {
			return $available_variation;
		}

		$globalAttachmentId = $this->strip_site_id_prefix_from_attachment_id( $idPrefix, $available_variation['image_id'] );

        if( is_multisite() ){
	        switch_to_blog($this->master_site);
	        $image = wc_get_product_attachment_props( $globalAttachmentId );
	        restore_current_blog();
        }else{
	        $global_image_data = wc_multistore_get_global_image_metadata($globalAttachmentId);

	        if( ! $global_image_data || empty( $global_image_data ) ){
		        return $available_variation;
	        }

	        $image_data = $global_image_data;
	        $upload_dir = $global_image_data['uploads_dir'];

	        $image = $this->get_product_attachment_props( $image_data, $upload_dir, $globalAttachmentId );
        }


		$available_variation['image'] = $image;

		return $available_variation;
	}

	function get_product_attachment_props( $image_data, $upload_dir, $globalAttachmentId ){
		$props      = array(
			'title'   => '',
			'caption' => '',
			'url'     => '',
			'alt'     => '',
			'src'     => '',
			'srcset'  => false,
			'sizes'   => false,
		);

		$idPrefix = 1000000;
		$image_alt  = $image_data['alt'];
		$image_data = $image_data['meta_data'];

		if ( $image_data ) {
			$props['title']   = wp_strip_all_tags( $image_data['image_meta']['title'] );
			$props['caption'] = '';
			$props['url']     = $this->get_attachment_url( $image_data['file'], $upload_dir, $globalAttachmentId );

			// Alt text.
			$alt_text = array( wp_strip_all_tags( $image_alt ), $props['caption'], wp_strip_all_tags( $image_data['image_meta']['title'] ) );

//			if ( $product && $product instanceof WC_Product ) {
//				$alt_text[] = wp_strip_all_tags( get_the_title( $product->get_id() ) );
//			}

			$alt_text     = array_filter( $alt_text );
			$props['alt'] = isset( $alt_text[0] ) ? $alt_text[0] : '';

			// Large version.
			$full_size           = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
			$src                 = wp_get_attachment_image_src( $idPrefix.$globalAttachmentId, $full_size );
			$props['full_src']   = $src[0];
			$props['full_src_w'] = $src[1];
			$props['full_src_h'] = $src[2];

			// Gallery thumbnail.
			$gallery_thumbnail                = wc_get_image_size( 'gallery_thumbnail' );
			$gallery_thumbnail_size           = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
			$src                              = wp_get_attachment_image_src( $idPrefix.$globalAttachmentId, $gallery_thumbnail_size );
			$props['gallery_thumbnail_src']   = $src[0];
			$props['gallery_thumbnail_src_w'] = $src[1];
			$props['gallery_thumbnail_src_h'] = $src[2];

			// Thumbnail version.
			$thumbnail_size       = apply_filters( 'woocommerce_thumbnail_size', 'woocommerce_thumbnail' );
			$src                  = wp_get_attachment_image_src( $idPrefix.$globalAttachmentId, $thumbnail_size );
			$props['thumb_src']   = $src[0];
			$props['thumb_src_w'] = $src[1];
			$props['thumb_src_h'] = $src[2];

			// Image source.
			$image_size      = apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' );
			$src             = wp_get_attachment_image_src( $idPrefix.$globalAttachmentId, $image_size );
			$props['src']    = $src[0];
			$props['src_w']  = $src[1];
			$props['src_h']  = $src[2];
			$props['srcset'] = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $idPrefix.$globalAttachmentId, $image_size ) : false;
			$props['sizes']  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $idPrefix.$globalAttachmentId, $image_size ) : false;
		}
		return $props;
	}

	/**
	 * @param $postId
	 */
	public function save_thumbnail_meta( $postId ){
		$idPrefix = 1000000;

		$attachmentId = (int)filter_input(
			INPUT_POST,
			'_thumbnail_id',
			FILTER_SANITIZE_NUMBER_INT
		);

		if ( ! $attachmentId ) {
			return;
		}

		if ( $this->id_prefix_included_in_attachment_id( $attachmentId, $idPrefix ) ) {
			update_post_meta( $postId, '_thumbnail_id', $attachmentId );
		}
	}

	/**
	 * @param $content
	 * @param $postId
	 * @param $attachmentId
	 *
	 * @return array|mixed|string|string[]
	 */
	public function admin_post_thumbnail_html( $content, $postId, $attachmentId ){
		$attachmentId = (int)$attachmentId;
		$idPrefix = 1000000;

		if (false === $this->id_prefix_included_in_attachment_id($attachmentId, $idPrefix)) {
			return $content;
		}

		$post = get_post($postId);
		$attachmentId = $this->strip_site_id_prefix_from_attachment_id($idPrefix, $attachmentId);

        if( is_multisite() ){
	        switch_to_blog($this->master_site);
	        $content = _wp_post_thumbnail_html($attachmentId, $post);
	        restore_current_blog();
        }else{
	        $data           = array(
		        'thumbnail_id'  => $attachmentId,
		        'post'          => null,
	        );
	        $wc_multistore_image_api_child = new WC_Multistore_Image_Api_Child();
	        $result = $wc_multistore_image_api_child->admin_post_thumbnail_html($data);
	        $content  = $result['data']['data'];
        }


		$search = 'value="' . $attachmentId . '"';
		$replace = 'value="' . $idPrefix . $attachmentId . '"';
		$content = str_replace($search, $replace, $content);

		$post = get_post($postId);
		$postTypeObject = null;

		$removeImageLabel = _x('Remove featured image', 'post', 'woonet');
		if ($post !== null) {
			$postTypeObject = get_post_type_object($post->post_type);
		}
		if ($postTypeObject !== null) {
			$removeImageLabel = $postTypeObject->labels->remove_featured_image;
		}

		return $this->replace_remove_post_thumbnail_markup(
			$removeImageLabel,
			$content
		);
	}

	/**
	 * @param $html
	 * @param $postId
	 * @param $attachmentId
	 * @param $size
	 * @param $attr
	 *
	 * @return mixed
	 */
	public function post_thumbnail_html( $html, $postId, $attachmentId, $size, $attr ){
		$attachmentId   = (int)$attachmentId;
		$idPrefix       = 1000000;

		if ($this->id_prefix_included_in_attachment_id($attachmentId, $idPrefix)) {
			$attachmentId = $this->strip_site_id_prefix_from_attachment_id($idPrefix, $attachmentId);
            if(is_multisite()){
	            switch_to_blog($this->master_site);
	            $html = wp_get_attachment_image($attachmentId, $size, false, $attr);
	            restore_current_blog();
            }else{
	            $data           = array(
		            'attachment_id' => $attachmentId,
		            'size'          => $size,
		            'icon'          => false,
		            'attr'          => $attr,
	            );

	            $wc_multistore_image_api_child = new WC_Multistore_Image_Api_Child();
	            $result = $wc_multistore_image_api_child->post_thumbnail_html($data);
	            $html  = $result['data']['data'];
            }

		}

		return $html;
	}

	/**
	 * @param $product_id
	 */
	public function save_gallery_ids( $product_id ){
		$productType = WC_Product_Factory::get_product_type( $product_id );
		$requestProductType = filter_input(
			INPUT_POST,
			'product-type',
			FILTER_SANITIZE_STRING
		);

		$requestProductType and $productType = sanitize_title( stripslashes( $requestProductType ) );

		$productType = $productType ?: 'simple';
		$classname = WC_Product_Factory::get_product_classname( $product_id, $productType );

		$product = new $classname( $product_id );
		$attachmentIds = filter_input(
			INPUT_POST,
			'product_image_gallery',
			FILTER_SANITIZE_STRING
		);
		update_post_meta( $product->get_id(), '_product_image_gallery', $attachmentIds );
	}




	/**
	 * @param $columns
	 * @param $column
	 * @param $id
	 *
	 * @return mixed|string
	 */
	public function product_cat_column( $columns, $column, $id ) {
		if ( 'thumb' === $column ) {
			// Prepend tooltip for default category.
			$default_category_id = absint( get_option( 'default_product_cat', 0 ) );

			if ( $default_category_id === $id ) {
				$columns .= wc_help_tip( __( 'This is the default category and it cannot be deleted. It will be automatically assigned to products with no category.', 'woocommerce' ) );
			}

			$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_image_src( $thumbnail_id );
				$image = $image[0];
			} else {
				$image = wc_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605 .
			$image    = str_replace( ' ', '%20', $image );
			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'woocommerce' ) . '" class="wp-post-image" height="48" width="48" />';
		}
		if ( 'handle' === $column ) {
			$columns .= '<input type="hidden" name="term_id" value="' . esc_attr( $id ) . '" />';
		}
		return $columns;
	}

	/**
	 * @param $term
	 */
	public function edit_category_fields( $term ) {

		$display_type = get_term_meta( $term->term_id, 'display_type', true );
		$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id );
			$image = $image[0];
		} else {
			$image = wc_placeholder_img_src();
		}
		?>
		<tr class="form-field term-display-type-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Display type', 'woocommerce' ); ?></label></th>
			<td>
				<select id="display_type" name="display_type" class="postform">
					<option value="" <?php selected( '', $display_type ); ?>><?php esc_html_e( 'Default', 'woocommerce' ); ?></option>
					<option value="products" <?php selected( 'products', $display_type ); ?>><?php esc_html_e( 'Products', 'woocommerce' ); ?></option>
					<option value="subcategories" <?php selected( 'subcategories', $display_type ); ?>><?php esc_html_e( 'Subcategories', 'woocommerce' ); ?></option>
					<option value="both" <?php selected( 'both', $display_type ); ?>><?php esc_html_e( 'Both', 'woocommerce' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="form-field term-thumbnail-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Thumbnail', 'woocommerce' ); ?></label></th>
			<td>
				<div id="product_cat_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
					<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woocommerce' ); ?></button>
					<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'woocommerce' ); ?></button>
				</div>
				<script type="text/javascript">

                    // Only show the "remove image" button when needed
                    if ( '0' === jQuery( '#product_cat_thumbnail_id' ).val() ) {
                        jQuery( '.remove_image_button' ).hide();
                    }

                    // Uploading files
                    var file_frame;

                    jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                        event.preventDefault();

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                            file_frame.open();
                            return;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.downloadable_file = wp.media({
                            title: '<?php esc_html_e( 'Choose an image', 'woocommerce' ); ?>',
                            button: {
                                text: '<?php esc_html_e( 'Use image', 'woocommerce' ); ?>'
                            },
                            multiple: false
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                            var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
                            var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

                            jQuery( '#product_cat_thumbnail_id' ).val( attachment.id );
                            jQuery( '#product_cat_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
                            jQuery( '.remove_image_button' ).show();
                        });

                        // Finally, open the modal.
                        file_frame.open();
                    });

                    jQuery( document ).on( 'click', '.remove_image_button', function() {
                        jQuery( '#product_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
                        jQuery( '#product_cat_thumbnail_id' ).val( '' );
                        jQuery( '.remove_image_button' ).hide();
                        return false;
                    });

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Load variations via AJAX.
	 */
	public function load_variations() {
		ob_start();

		check_ajax_referer( 'load-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) ) {
			wp_die( -1 );
		}

		// Set $post global so its available, like within the admin screens.
		global $post;

		$loop           = 0;
		$product_id     = absint( $_POST['product_id'] );
		$post           = get_post( $product_id ); // phpcs:ignore
		$product_object = wc_get_product( $product_id );
		$per_page       = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
		$page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$variations     = wc_get_products(
			array(
				'status'  => array( 'private', 'publish' ),
				'type'    => 'variation',
				'parent'  => $product_id,
				'limit'   => $per_page,
				'page'    => $page,
				'orderby' => array(
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				),
				'return'  => 'objects',
			)
		);

		if ( $variations ) {
			wc_render_invalid_variation_notice( $product_object );

			foreach ( $variations as $variation_object ) {
				$variation_id   = $variation_object->get_id();
				$variation      = get_post( $variation_id );
				$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.
				include WOO_MSTORE_PATH. '/includes/admin/views/html-variation-admin.php';
				$loop++;
			}
		}
		wp_die();
	}

	/**
	 * @param $replace
	 * @param $subject
	 *
	 * @return array|string|string[]
	 */
	public function replace_remove_post_thumbnail_markup( $replace, $subject ){
		$search = '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail"></a></p>';
		$replace = sprintf(
			'<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail">%s</a></p>',
			$replace
		);

		return str_replace($search, $replace, $subject);
	}

	/**
	 * @param $attachmentId
	 * @param $idPrefix
	 *
	 * @return bool
	 */
	private function id_prefix_included_in_attachment_id( $attachmentId, $idPrefix ){
		return false !== strpos((string)$attachmentId, (string)$idPrefix);
	}

	/**
	 * @param $idPrefix
	 * @param $attachmentId
	 *
	 * @return int
	 */
	private function strip_site_id_prefix_from_attachment_id( $idPrefix, $attachmentId ){
		return (int)str_replace($idPrefix, '', (string)$attachmentId);
	}


}