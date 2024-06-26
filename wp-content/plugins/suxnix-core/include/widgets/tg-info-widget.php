<?php
	/**
	 * Suxnix Footer full Widget
	 *
	 *
	 * @author 		ThemeGenix
	 * @category 	Widgets
	 * @package 	Suxnix/Widgets
	 * @version 	1.0.0
	 * @extends 	WP_Widget
	 */
	add_action('widgets_init', 'Suxnix_info_Widget');
	function Suxnix_info_Widget() {
		register_widget('Suxnix_info_Widget');
	}


	class Suxnix_info_Widget  extends WP_Widget{

		public function __construct(){
			parent::__construct('Suxnix_info_Widget',esc_html__('Suxnix Info','tpcore'),array(
				'description' => esc_html__('Suxnix Info Widget','tpcore'),
			));
		}

		public function widget($args, $instance){
			extract($args);
			extract($instance);

			print $before_widget;

			if ( ! empty( $title ) ) {
				print $before_title . apply_filters( 'widget_title', $title ) . $after_title;
			}
		?>

			<div class="footer-about">

				<?php if( !empty($image_box_image) ): ?>
				<div class="footer-logo logo">
					<a href="<?php print home_url(); ?>"><img src="<?php print $image_box_image; ?>" alt="<?php echo esc_attr__('Suxnix','tpcore') ?>"></a>
				</div>
				<?php endif; ?>

				<?php if( !empty($description) ): ?>
					<div class="footer-text">
						<p><?php print $description; ?></p>
					</div>
				<?php endif; ?>

				<div class="footer-social">
					<?php if( !empty($facebook) ): ?>
                        <a href="<?php print esc_url($facebook); ?>"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>

					<?php if( !empty($twitter) ): ?>
                        <a href="<?php print esc_url($twitter); ?>"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>

					<?php if( !empty($instagram) ): ?>
                        <a href="<?php print esc_url($instagram); ?>"><i class="fab fa-instagram"></i></a>
					<?php endif; ?>

					<?php if( !empty($youtube) ): ?>
                        <a href="<?php print esc_url($youtube); ?>"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>

				</div>
			</div>

            <?php print $after_widget; ?>

		<?php
		}


		/**
		 * widget function.
		 *
		 * @see WP_Widget
		 * @access public
		 * @param array $instance
		 * @return void
		 */
		public function form($instance){

			$title  = isset($instance['title'])? $instance['title']:'';
			$description  = isset($instance['description'])? $instance['description']:'';
			$author_img  = isset($instance['image_box_image'])? $instance['image_box_image']:'';

			$twitter  = isset($instance['twitter'])? $instance['twitter']:'';
			$facebook  = isset($instance['facebook'])? $instance['facebook']:'';
			$instagram  = isset($instance['instagram'])? $instance['instagram']:'';
			$youtube  = isset($instance['youtube'])? $instance['youtube']:'';

			?>
			<p>
				<label for="title"><?php esc_html_e('Title:','tpcore'); ?></label>
			</p>
			<input class="widefat" type="text" id="<?php print esc_attr($this->get_field_id('title')); ?>"  name="<?php print esc_attr($this->get_field_name('title')); ?>" value="<?php print esc_attr($title); ?>">

			<p>
				<button type="submit" class="button button-secondary" id="author_info_image">Upload Media</button>
				<input type="hidden" name="<?php print esc_attr($this->get_field_name('image_box_image')); ?>" class="image_er_link" value="<?php print $author_img ; ?>">
				<div class="author-image-show">
					<img src="<?php print $author_img ; ?>" alt="" width="150" height="auto">
				</div>
			</p>

			<p>
				<label for="title"><?php esc_html_e('Short Description:','tpcore'); ?></label>
			</p>

			<textarea class="widefat" rows="7" cols="15" id="<?php print esc_attr($this->get_field_id('description')); ?>" value="<?php print esc_attr($description); ?>" name="<?php print esc_attr($this->get_field_name('description')); ?>"><?php print esc_attr($description); ?></textarea>

			<p>
				<label for="title"><?php esc_html_e('Facebook:','tpcore'); ?></label>
			</p>
			<input class="widefat" type="text" id="<?php print esc_attr($this->get_field_id('facebook')); ?>"  name="<?php print esc_attr($this->get_field_name('facebook')); ?>" value="<?php print esc_attr($facebook); ?>">


			<p>
				<label for="title"><?php esc_html_e('Twitter:','tpcore'); ?></label>
			</p>
			<input class="widefat" type="text" id="<?php print esc_attr($this->get_field_id('twitter')); ?>"  name="<?php print esc_attr($this->get_field_name('twitter')); ?>" value="<?php print esc_attr($twitter); ?>">

			<p>
				<label for="title"><?php esc_html_e('Instagram:','tpcore'); ?></label>
			</p>
			<input class="widefat" type="text" id="<?php print esc_attr($this->get_field_id('instagram')); ?>"  name="<?php print esc_attr($this->get_field_name('instagram')); ?>" value="<?php print esc_attr($instagram); ?>">
			<p>
				<label for="title"><?php esc_html_e('Youtube:','tpcore'); ?></label>
			</p>
			<input class="widefat" type="text" id="<?php print esc_attr($this->get_field_id('youtube')); ?>"  name="<?php print esc_attr($this->get_field_name('youtube')); ?>" value="<?php print esc_attr($youtube); ?>">
			<p></p>

			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['description'] = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';

			$instance['facebook'] = ( ! empty( $new_instance['facebook'] ) ) ? strip_tags( $new_instance['facebook'] ) : '';
			$instance['twitter'] = ( ! empty( $new_instance['twitter'] ) ) ? strip_tags( $new_instance['twitter'] ) : '';
			$instance['instagram'] = ( ! empty( $new_instance['instagram'] ) ) ? strip_tags( $new_instance['instagram'] ) : '';
			$instance['youtube'] = ( ! empty( $new_instance['youtube'] ) ) ? strip_tags( $new_instance['youtube'] ) : '';

			$instance['image_box_image'] = ( ! empty( $new_instance['image_box_image'] ) ) ? strip_tags( $new_instance['image_box_image'] ) : '';

			return $instance;
		}
	}