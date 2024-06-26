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
class TP_Testimonial extends Widget_Base {

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
		return 'testimonial';
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
		return __( 'Testimonial', 'tpcore' );
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

        // _tg_background
        $this->start_controls_section(
            '_tg_background_section',
            [
                'label' => esc_html__('Background', 'tpcore'),
            ]
        );

        $this->add_control(
            'tg_bg_image',
            [
                'label' => esc_html__( 'Choose Background', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->end_controls_section();

        // Review group
        $this->start_controls_section(
            'review_list',
            [
                'label' => esc_html__( 'Review List', 'tpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'review_content',
            [
                'label' => esc_html__( 'Review Content', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 10,
                'default' => '“Becoming more involved in administration within the (MidMichigan) health system over the years, I had been researching options for further education that would assist in this transition and fit my busy schedule”',
                'placeholder' => esc_html__( 'Type your review content here', 'tpcore' ),
            ]
        );

        $repeater->add_control(
            'reviewer_image',
            [
                'label' => esc_html__( 'Reviewer Image', 'tpcore' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'reviewer_image_size',
                'default' => 'thumbnail',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $repeater->add_control(
            'reviewer_name', [
                'label' => esc_html__( 'Reviewer Name', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Janeta Cooper' , 'tpcore' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'reviews_list',
            [
                'label' => esc_html__( 'Review List', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' =>  $repeater->get_controls(),
                'default' => [
                    [
                        'reviewer_name' => esc_html__( 'Janeta Cooper', 'tpcore' ),
                        'review_content' => esc_html__( '“Becoming more involved in administration within the (MidMichigan) health system over the years, I had been researching options for further education that would assist in this transition and fit my busy schedule”', 'tpcore' ),
                    ],
                    [
                        'reviewer_name' => esc_html__( 'Lempor Kooper', 'tpcore' ),
                        'review_content' => esc_html__( '“Becoming more involved in administration within the (MidMichigan) health system over the years, I had been researching options for further education that would assist in this transition and fit my busy schedule”', 'tpcore' ),
                    ],
                    [
                        'reviewer_name' => esc_html__( 'Zonalos Neko', 'tpcore' ),
                        'review_content' => esc_html__( '“Becoming more involved in administration within the (MidMichigan) health system over the years, I had been researching options for further education that would assist in this transition and fit my busy schedule”', 'tpcore' ),
                    ],

                ],
                'title_field' => '{{{ reviewer_name }}}',
            ]
        );

        $this->end_controls_section();


        // TAB_STYLE
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

        <script>
            jQuery(document).ready(function($){

                /*=============================================
                    =          Data Background               =
                =============================================*/
                $("[data-background]").each(function () {
                    $(this).css("background-image", "url(" + $(this).attr("data-background") + ")")
                })

                /*=============================================
                    =         Testimonial Active          =
                =============================================*/
                $('.testimonial-active').slick({
                    dots: true,
                    infinite: true,
                    speed: 1000,
                    autoplay: true,
                    arrows: true,
                    slidesToShow: 1,
                    prevArrow: '<button type="button" class="slick-prev"><i class="flaticon-left-arrow"></i></button>',
                    nextArrow: '<button type="button" class="slick-next"><i class="flaticon-right-arrow"></i></button>',
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1200,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                infinite: true,
                            }
                        },
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: false,
                            }
                        },
                        {
                            breakpoint: 767,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: false,
                            }
                        },
                        {
                            breakpoint: 575,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: false,
                            }
                        },
                    ]
                });

            });
        </script>

        <!-- testimonial-area -->
        <section class="testimonial-area testimonial-bg" data-background="<?php echo esc_url($settings['tg_bg_image']['url']); ?>">
            <div class="testimonial-overlay"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-8 col-xl-9 col-lg-11">
                        <div class="testimonial-active">

                            <?php foreach ($settings['reviews_list'] as $item) :
                                if ( !empty($item['reviewer_image']['url']) ) {
                                    $tg_reviewer_image = !empty($item['reviewer_image']['id']) ? wp_get_attachment_image_url( $item['reviewer_image']['id'], $item['reviewer_image_size_size']) : $item['reviewer_image']['url'];
                                    $tg_reviewer_image_alt = get_post_meta($item["reviewer_image"]["id"], "_wp_attachment_image_alt", true);
                                }
                            ?>
                            <div class="testimonial-item text-center">
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p><?php echo esc_html( $item['review_content'] ); ?></p>
                                <div class="testimonial-avatar-wrap">

                                    <?php if ( !empty($tg_reviewer_image) ) : ?>
                                        <div class="testi-avatar-img">
                                            <img src="<?php echo esc_url($tg_reviewer_image); ?>" alt="<?php echo esc_url($tg_reviewer_image_alt); ?>">
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( !empty($item['reviewer_name']) ) : ?>
                                    <div class="testi-avatar-info">
                                        <h5 class="name"><?php echo tp_kses($item['reviewer_name']); ?></h5>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>

                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- testimonial-area-end -->

        <?php
	}
}

$widgets_manager->register( new TP_Testimonial() );