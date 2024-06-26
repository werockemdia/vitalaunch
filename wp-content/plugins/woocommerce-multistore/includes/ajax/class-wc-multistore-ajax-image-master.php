<?php
/**
 * Ajax Image master handler.
 *
 * This handles ajax image master related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Multistore_Ajax_Image_Master
 */
class WC_Multistore_Ajax_Image_Master {
	function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) { return; }

		add_action( 'wp_ajax_wc_multistore_ajax_query_attachments', array( $this, 'wc_multistore_ajax_query_attachments' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_ajax_query_attachments', array( $this, 'wc_multistore_ajax_query_attachments' ) );

		add_action( 'wp_ajax_wc_multistore_ajax_get_attachment', array( $this, 'wc_multistore_ajax_get_attachment' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_ajax_get_attachment', array( $this, 'wc_multistore_ajax_get_attachment' ) );

		add_action( 'wp_ajax_wc_multistore_ajax_send_attachment_to_editor', array( $this, 'wc_multistore_ajax_send_attachment_to_editor' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_ajax_send_attachment_to_editor', array( $this, 'wc_multistore_ajax_send_attachment_to_editor' ) );

		add_action( 'wp_ajax_wc_multistore_make_content_images_responsive', array( $this, 'wc_multistore_make_content_images_responsive' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_make_content_images_responsive', array( $this, 'wc_multistore_make_content_images_responsive' ) );

		add_action( 'wp_ajax_wc_wc_multistore_admin_post_thumbnail_html', array( $this, 'wc_multistore_admin_post_thumbnail_html' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_admin_post_thumbnail_html', array( $this, 'wc_multistore_admin_post_thumbnail_html' ) );


		add_action( 'wp_ajax_wc_wc_multistore_post_thumbnail_html', array( $this, 'wc_multistore_post_thumbnail_html' ) );
		add_action( 'wp_ajax_nopriv_wc_multistore_post_thumbnail_html', array( $this, 'wc_multistore_post_thumbnail_html' ) );
	}

	public function wc_multistore_ajax_query_attachments(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		add_filter('wp_prepare_attachment_for_js', function ($response){
			$site_id = 1000000;
			$response['id'] = (int) ($site_id.$response['id']); // Unique ID, must be a number.
			$response['nonces']['update'] = false;
			$response['nonces']['edit'] = false;
			$response['nonces']['delete'] = false;
			$response['editLink'] = false;

			return $response;
		}, 0);


		$query = isset( $_REQUEST['data']['query'] ) ? (array) $_REQUEST['data']['query'] : array();
		$keys  = array(
			's',
			'order',
			'orderby',
			'posts_per_page',
			'paged',
			'post_mime_type',
			'post_parent',
			'author',
			'post__in',
			'post__not_in',
			'year',
			'monthnum',
		);

		foreach ( get_taxonomies_for_attachments( 'objects' ) as $t ) {
			if ( $t->query_var && isset( $query[ $t->query_var ] ) ) {
				$keys[] = $t->query_var;
			}
		}

		$query              = array_intersect_key( $query, array_flip( $keys ) );
		$query['post_type'] = 'attachment';

		if (
			MEDIA_TRASH &&
			! empty( $_REQUEST['data']['query']['post_status'] ) &&
			'trash' === $_REQUEST['data']['query']['post_status']
		) {
			$query['post_status'] = 'trash';
		} else {
			$query['post_status'] = 'inherit';
		}

		if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) ) {
			$query['post_status'] .= ',private';
		}

		// Filter query clauses to include filenames.
		if ( isset( $query['s'] ) ) {
			add_filter( 'posts_clauses', '_filter_query_attachment_filenames' );
		}

		$query             = apply_filters( 'ajax_query_attachments_args', $query );
		$attachments_query = new WP_Query( $query );

		$posts       = array_map( 'wp_prepare_attachment_for_js', $attachments_query->posts );
		$posts       = array_filter( $posts );
		$total_posts = $attachments_query->found_posts;

		if ( $total_posts < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query['paged'] );

			$count_query = new WP_Query();
			$count_query->query( $query );
			$total_posts = $count_query->found_posts;
		}

		$posts_per_page = (int) $attachments_query->get( 'posts_per_page' );

		$max_pages = $posts_per_page ? ceil( $total_posts / $posts_per_page ) : 0;

		$posts = array(
			'total_posts'  => $total_posts,
			'max_pages'    => $max_pages,
			'posts'		   => $posts
		);

		$result = array(
			'status' => 'success',
			'data' => $posts
		);

		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_ajax_get_attachment(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		add_filter('wp_prepare_attachment_for_js', function ($response){
			$site_id = 1000000;
			$response['id'] = (int) ($site_id.$response['id']); // Unique ID, must be a number.
			$response['nonces']['update'] = false;
			$response['nonces']['edit'] = false;
			$response['nonces']['delete'] = false;
			$response['editLink'] = false;

			return $response;
		}, 0);

		if ( ! isset( $_REQUEST['data']['id'] ) ) {
			wp_send_json_error();
		}

		$id = absint( $_REQUEST['data']['id'] );
		if ( ! $id ) {
			wp_send_json_error();
		}

		$post = get_post( $id );
		if ( ! $post ) {
			wp_send_json_error();
		}

		if ( 'attachment' !== $post->post_type ) {
			wp_send_json_error();
		}

		$attachment = wp_prepare_attachment_for_js( $id );


		$result = array(
			'status' => 'success',
			'data' => $attachment
		);

		echo wp_json_encode($result);
		wp_die();
	}


	public function wc_multistore_ajax_send_attachment_to_editor(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		add_filter('mediaSendToEditor', function ( $html, $id ){
			$idPrefix = 1000000;
			$newId = $idPrefix.$id; // Unique ID, must be a number.

			$search = 'wp-image-'.$id;
			$replace = 'wp-image-'.$newId;

			return str_replace($search, $replace, $html);

		}, 10, 2);

		$attachment = wp_unslash( $_REQUEST['data']['attachment'] );

		$id = (int) $attachment['id'];

		$post = get_post( $id );

		if ( ! $post ) {
			wp_send_json_error();
		}

		if ( 'attachment' !== $post->post_type ) {
			wp_send_json_error();
		}

		// If this attachment is unattached, attach it. Primarily a back compat thing.
		$insert_into_post_id = (int) $_REQUEST['data']['post_id'];

		if ( 0 == $post->post_parent && $insert_into_post_id ) {
			wp_update_post(
				array(
					'ID'          => $id,
					'post_parent' => $insert_into_post_id,
				)
			);
		}

		$url = empty( $attachment['url'] ) ? '' : $attachment['url'];
		$rel = ( strpos( $url, 'attachment_id' ) || get_attachment_link( $id ) == $url );

		remove_filter( 'media_send_to_editor', 'image_media_send_to_editor' );
		if ( 'image' === substr( $post->post_mime_type, 0, 5 ) ) {
			$align = isset( $attachment['align'] ) ? $attachment['align'] : 'none';
			$size  = isset( $attachment['image-size'] ) ? $attachment['image-size'] : 'medium';
			$alt   = isset( $attachment['image_alt'] ) ? $attachment['image_alt'] : '';

			// No whitespace-only captions.
			$caption = isset( $attachment['post_excerpt'] ) ? $attachment['post_excerpt'] : '';
			if ( '' === trim( $caption ) ) {
				$caption = '';
			}

			$title = ''; // We no longer insert title tags into <img> tags, as they are redundant.

			$html  = get_image_send_to_editor( $id, $caption, $title, $align, $url, $rel, $size, $alt );
		} elseif ( wp_attachment_is( 'video', $post ) || wp_attachment_is( 'audio', $post ) ) {
			$html = stripslashes_deep( $_POST['html'] );
		} else {
			$html = isset( $attachment['post_title'] ) ? $attachment['post_title'] : '';
			$rel  = $rel ? ' rel="attachment wp-att-' . $id . '"' : ''; // Hard-coded string, $id is already sanitized.

			if ( ! empty( $url ) ) {
				$html = '<a href="' . esc_url( $url ) . '"' . $rel . '>' . $html . '</a>';
			}
		}

		$idPrefix = 1000000;
		$newId = $idPrefix.$id; // Unique ID, must be a number.

		$search = 'wp-image-'.$id;
		$replace = 'wp-image-'.$newId;

		$html = str_replace( $search, $replace, $html );

		/** This filter is documented in wp-admin/includes/media.php */
		$html = apply_filters( 'media_send_to_editor', $html, $id, $attachment );


		$result = array(
			'status' => 'success',
			'data' => $html
		);

		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_make_content_images_responsive(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$attachment_id = (int) $_POST['data']['attachment_id'];

		if ( ! $attachment_id ) {
			$post = get_post();

			if ( ! $post ) {
				return false;
			}

			$attachment_id = $post->ID;
		}

		$data = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );

		if ( ! $data ) {
			return false;
		}

		/**
		 * Filters the attachment meta data.
		 *
		 * @since 2.1.0
		 *
		 * @param array $data          Array of meta data for the given attachment.
		 * @param int   $attachment_id Attachment post ID.
		 */
		$data =  apply_filters( 'wp_get_attachment_metadata', $data, $attachment_id );

		$content = str_replace( $_POST['data']['image'], wp_image_add_srcset_and_sizes( $_POST['data']['image'], $data, $_POST['data']['attachmentId'] ), $_POST['data']['content'] );


		$result = array(
			'status' => 'success',
			'data' => $content
		);

		echo wp_json_encode($result);
		wp_die();
	}

	public function wc_multistore_admin_post_thumbnail_html(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$thumbnail_id   = $_REQUEST['data']['thumbnail_id'];
		$post           = $_REQUEST['data']['post'];

		$_wp_additional_image_sizes = wp_get_additional_image_sizes();

		$post               = get_post( $post );
		$post_type_object   = get_post_type_object( $post->post_type );
		$set_thumbnail_link = '<p class="hide-if-no-js"><a href="%s" id="set-post-thumbnail"%s class="thickbox">%s</a></p>';
		$upload_iframe_src  = get_upload_iframe_src( 'image', $post->ID );

		$content = sprintf(
			$set_thumbnail_link,
			esc_url( $upload_iframe_src ),
			'', // Empty when there's no featured image set, `aria-describedby` attribute otherwise.
			esc_html( $post_type_object->labels->set_featured_image )
		);

		if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
			$size = isset( $_wp_additional_image_sizes['post-thumbnail'] ) ? 'post-thumbnail' : array( 266, 266 );

			$size = apply_filters( 'admin_post_thumbnail_size', $size, $thumbnail_id, $post );

			$thumbnail_html = wp_get_attachment_image( $thumbnail_id, $size );

			if ( ! empty( $thumbnail_html ) ) {
				$content  = sprintf(
					$set_thumbnail_link,
					esc_url( $upload_iframe_src ),
					' aria-describedby="set-post-thumbnail-desc"',
					$thumbnail_html
				);
				$content .= '<p class="hide-if-no-js howto" id="set-post-thumbnail-desc">' . __( 'Click the image to edit or update' ) . '</p>';
				$content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail">' . esc_html( $post_type_object->labels->remove_featured_image ) . '</a></p>';
			}
		}

		$content .= '<input type="hidden" id="_thumbnail_id" name="_thumbnail_id" value="' . esc_attr( $thumbnail_id ? $thumbnail_id : '-1' ) . '" />';

		$content = apply_filters( 'admin_post_thumbnail_html', $content, $post->ID, $thumbnail_id );


		$result = array(
			'status' => 'success',
			'data' => $content
		);

		echo wp_json_encode($result);
		wp_die();
	}


	public function wc_multistore_post_thumbnail_html(){
		if( empty( $_REQUEST['key']) || $_REQUEST['key'] != WOO_MULTISTORE()->sites[$_REQUEST['key']]->get_id() ){
			return array(
				'status' => 'failed',
				'message' => 'You do not have sufficient permissions'
			);
		}

		$html  = '';
		$attachment_id  = $_POST['data']['attachment_id'];
		$size           = $_POST['data']['size'];
		$icon           = $_POST['data']['icon'];
		$attr           = $_POST['data']['attr'];

		$image          = wp_get_attachment_image_src( $attachment_id, $size, $icon );

		if ( $image ) {
			list( $src, $width, $height ) = $image;

			$attachment = get_post( $attachment_id );
			$hwstring   = image_hwstring( $width, $height );
			$size_class = $size;

			if ( is_array( $size_class ) ) {
				$size_class = implode( 'x', $size_class );
			}

			$default_attr = array(
				'src'   => $src,
				'class' => "attachment-$size_class size-$size_class",
				'alt'   => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
			);

			// Add `loading` attribute.
			if ( wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ) ) {
				$default_attr['loading'] = wp_get_loading_attr_default( 'wp_get_attachment_image' );
			}

			$attr = wp_parse_args( $attr, $default_attr );

			// If the default value of `lazy` for the `loading` attribute is overridden
			// to omit the attribute for this image, ensure it is not included.
			if ( array_key_exists( 'loading', $attr ) && ! $attr['loading'] ) {
				unset( $attr['loading'] );
			}

			// Generate 'srcset' and 'sizes' if not already present.
			if ( empty( $attr['srcset'] ) ) {
				$image_meta = wp_get_attachment_metadata( $attachment_id );

				if ( is_array( $image_meta ) ) {
					$size_array = array( absint( $width ), absint( $height ) );
					$srcset     = wp_calculate_image_srcset( $size_array, $src, $image_meta, $attachment_id );
					$sizes      = wp_calculate_image_sizes( $size_array, $src, $image_meta, $attachment_id );

					if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
						$attr['srcset'] = $srcset;

						if ( empty( $attr['sizes'] ) ) {
							$attr['sizes'] = $sizes;
						}
					}
				}
			}

			$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment, $size );

			$attr = array_map( 'esc_attr', $attr );
			$html = rtrim( "<img $hwstring" );

			foreach ( $attr as $name => $value ) {
				$html .= " $name=" . '"' . $value . '"';
			}

			$html .= ' />';
		}

		$html = apply_filters( 'wp_get_attachment_image', $html, $attachment_id, $size, $icon, $attr );

		$result = array(
			'status' => 'success',
			'data' => $html
		);

		echo wp_json_encode($result);
		wp_die();
	}

}