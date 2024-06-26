<?php
namespace TPCore\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Repeater;
use \Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Suxnix Core
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class TG_gallery extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tg-gallery';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Gallery', 'tpcore' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'tp-icon';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'tpcore' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'tpcore' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {

		// layout Panel
        $this->start_controls_section(
            'tg_layout',
            [
                'label' => esc_html__('Design Layout', 'tpcore'),
            ]
        );

        $this->add_control(
            'tg_design_style',
            [
                'label' => esc_html__('Select Layout', 'tpcore'),
                'type' => Controls_Manager::SELECT,
				'label_block' => true,
                'options' => [
                    'layout-1' => esc_html__('Layout 1', 'tpcore'),
                    'layout-2' => esc_html__('Layout 2', 'tpcore'),
                ],
                'default' => 'layout-1',
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
            'tg_gallery_section',
            [
                'label' => __( 'Gallery', 'tpcore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'tg_gallery_image',
            [
                'type' => Controls_Manager::MEDIA,
                'label' => __( 'Choose Image', 'tpcore' ),
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->add_control(
            'tg_gallery_slides',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => esc_html__( 'Gallery Item', 'tpcore' ),
                'default' => [
                    [
                        'tg_gallery_image' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],
                    [
                        'tg_gallery_image' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'medium_large',
                'separator' => 'before',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->end_controls_section();


		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'tpcore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_transform',
			[
				'label' => __( 'Text Transform', 'tpcore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'tpcore' ),
					'uppercase' => __( 'UPPERCASE', 'tpcore' ),
					'lowercase' => __( 'lowercase', 'tpcore' ),
					'capitalize' => __( 'Capitalize', 'tpcore' ),
				],
				'selectors' => [
					'{{WRAPPER}} .title' => 'text-transform: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		?>

		<?php if ( $settings['tg_design_style']  == 'layout-2' ):
            $this->add_render_attribute('title_args', 'class', 'title');
        ?>

			<div class="section-title text-center">
                <h2 class="title"><?php echo esc_html__('More Style Coming Soon :)','tpcore') ?></h2>
            </div>

		<?php else: ?>

		<script>
            jQuery(document).ready(function($){

				/*=============================================
					=         Instagram Active          =
				=============================================*/
				$('.instagram-active').slick({
					dots: false,
					infinite: true,
					speed: 1000,
					autoplay: true,
					arrows: false,
					swipe: false,
					slidesToShow: 5,
					slidesToScroll: 1,
					responsive: [
						{
						breakpoint: 1200,
							settings: {
								slidesToShow: 5,
								slidesToScroll: 1,
								infinite: true,
							}
						},
						{
						breakpoint: 992,
							settings: {
								slidesToShow: 4,
								slidesToScroll: 1
							}
						},
						{
						breakpoint: 767,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 1,
								arrows: false,
							}
						},
						{
						breakpoint: 575,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1,
								arrows: false,
							}
						},
					]
				});

            });
        </script>

		<!-- Footer-instagram -->
		<div class="footer-instagram">
			<div class="container">
				<div class="row g-0 instagram-active">
					<?php foreach ($settings['tg_gallery_slides'] as $item) :
						if ( !empty($item['tg_gallery_image']['url']) ) {
							$tg_gallery_image_url = !empty($item['tg_gallery_image']['id']) ? wp_get_attachment_image_url( $item['tg_gallery_image']['id'], $settings['thumbnail_size']) : $item['tg_gallery_image']['url'];
							$tg_gallery_image_alt = get_post_meta($item["tg_gallery_image"]["id"], "_wp_attachment_image_alt", true);
						}
					?>
					<div class="col-2">
						<div class="footer-insta-item">
							<a href="<?php echo esc_url($tg_gallery_image_url); ?>"><img src="<?php echo esc_url($tg_gallery_image_url); ?>" alt="<?php echo esc_attr($tg_gallery_image_alt); ?>"></a>
						</div>
					</div>
					<?php endforeach; ?>

				</div>
			</div>
		</div>
		<!-- Footer-instagram-end -->

		<?php endif; ?>

	<?php
	}


}

$widgets_manager->register( new TG_gallery() );