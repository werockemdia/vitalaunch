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
class TG_ingredient extends Widget_Base {

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
		return 'ingredient';
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
		return __( 'Ingredient', 'tpcore' );
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
            'tp_layout',
            [
                'label' => esc_html__('Design Layout', 'tpcore'),
            ]
        );
        $this->add_control(
            'tg_design_style',
            [
                'label' => esc_html__('Select Layout', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'layout-1' => esc_html__('Layout 1', 'tpcore'),
                    'layout-2' => esc_html__('Layout 2', 'tpcore'),
                ],
                'default' => 'layout-1',
            ]
        );

        $this->end_controls_section();

        // tp_section_title
        $this->start_controls_section(
            'tp_section_title',
            [
                'label' => esc_html__('Title & Content', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_section_title_show',
            [
                'label' => esc_html__( 'Section Title & Content', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tg_caption',
            [
                'label' => esc_html__('Sub Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('.. Increased Energy With Suxnix ..', 'tpcore'),
                'placeholder' => esc_html__('Type Caption Text', 'tpcore'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tg_title',
            [
                'label' => esc_html__('Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Suxnix Ingredients', 'tpcore'),
                'placeholder' => esc_html__('Type Heading Text', 'tpcore'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tg_description',
            [
                'label' => esc_html__('Description', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Suxinx section description here', 'tpcore'),
                'placeholder' => esc_html__('Type section description here', 'tpcore'),
            ]
        );

        $this->add_control(
            'tg_title_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'tpcore'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'h1' => [
                        'title' => esc_html__('H1', 'tpcore'),
                        'icon' => 'eicon-editor-h1'
                    ],
                    'h2' => [
                        'title' => esc_html__('H2', 'tpcore'),
                        'icon' => 'eicon-editor-h2'
                    ],
                    'h3' => [
                        'title' => esc_html__('H3', 'tpcore'),
                        'icon' => 'eicon-editor-h3'
                    ],
                    'h4' => [
                        'title' => esc_html__('H4', 'tpcore'),
                        'icon' => 'eicon-editor-h4'
                    ],
                    'h5' => [
                        'title' => esc_html__('H5', 'tpcore'),
                        'icon' => 'eicon-editor-h5'
                    ],
                    'h6' => [
                        'title' => esc_html__('H6', 'tpcore'),
                        'icon' => 'eicon-editor-h6'
                    ]
                ],
                'default' => 'h2',
                'toggle' => false,
            ]
        );

        $this->add_responsive_control(
            'tp_align',
            [
                'label' => esc_html__('Alignment', 'tpcore'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'text-left' => [
                        'title' => esc_html__('Left', 'tpcore'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'text-center' => [
                        'title' => esc_html__('Center', 'tpcore'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'text-right' => [
                        'title' => esc_html__('Right', 'tpcore'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => false,
            ]
        );
        $this->end_controls_section();


        // Ingredient group
        $this->start_controls_section(
            'tg_ingredient',
            [
                'label' => esc_html__('Ingredient List', 'tpcore'),
                'description' => esc_html__( 'Control all the style settings from Style tab', 'tpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'tg_ingredient_transition', [
                'label' => esc_html__('Animation Delay (ms)', 'tpcore'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => esc_html__('.2', 'tpcore'),
            ]
        );

        $repeater->add_control(
            'tg_ingredient_icon_type',
            [
                'label' => esc_html__('Select Icon Type', 'tpcore'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'image' => esc_html__('Image', 'tpcore'),
                    'icon' => esc_html__('Icon', 'tpcore'),
                ],
            ]
        );

        $repeater->add_control(
            'tg_ingredient_image',
            [
                'label' => esc_html__('Upload Icon Image', 'tpcore'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'tg_ingredient_icon_type' => 'image'
                ]

            ]
        );

        if (tp_is_elementor_version('<', '2.6.0')) {
            $repeater->add_control(
                'tg_ingredient_icon',
                [
                    'show_label' => false,
                    'type' => Controls_Manager::ICON,
                    'label_block' => true,
                    'default' => 'fa fa-star',
                    'condition' => [
                        'tg_ingredient_icon_type' => 'icon'
                    ]
                ]
            );
        } else {
            $repeater->add_control(
                'tg_ingredient_selected_icon',
                [
                    'show_label' => false,
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'icon',
                    'label_block' => true,
                    'default' => [
                        'value' => 'fas fa-star',
                        'library' => 'solid',
                    ],
                    'condition' => [
                        'tg_ingredient_icon_type' => 'icon'
                    ]
                ]
            );
        }

        $repeater->add_control(
            'tg_ingredient_title', [
                'label' => esc_html__('Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'basic' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Multi Fruits Flavour', 'tpcore'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'tg_ingredient_description',
            [
                'label' => esc_html__('Description', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'A thing added to something else in order to complete or enhance it.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tg_ingredient_list',
            [
                'label' => esc_html__('Ingredient List', 'tpcore'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'tg_ingredient_title' => esc_html__('Helps You Stick To Your Diet', 'tpcore'),
                    ],
                    [
                        'tg_ingredient_title' => esc_html__('Only 3g Net Carbs In Every Jar', 'tpcore')
                    ],
                    [
                        'tg_ingredient_title' => esc_html__('Ingredients To Fuel Your Body', 'tpcore')
                    ],
                    [
                        'tg_ingredient_title' => esc_html__('Clean Ingredients Only', 'tpcore')
                    ]
                ],
                'condition' => [
                    'tg_design_style' => 'layout-1'
                ]
            ]
        );

        $this->add_responsive_control(
            'tg_ingredient_align',
            [
                'label' => esc_html__( 'Alignment', 'tpcore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'text-left' => [
                        'title' => esc_html__( 'Left', 'tpcore' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'text-center' => [
                        'title' => esc_html__( 'Center', 'tpcore' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'text-right' => [
                        'title' => esc_html__( 'Right', 'tpcore' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'toggle' => true,
                'separator' => 'before',
            ]
        );
        $this->end_controls_section();

        // _tg_image
		$this->start_controls_section(
            '_tg_image',
            [
                'label' => esc_html__('Thumbnail', 'tpcore'),
            ]
        );

        $this->add_control(
            'tg_image',
            [
                'label' => esc_html__( 'Choose Image', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'tg_image_size',
                'default' => 'full',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->add_control(
            'tg_image_shape',
            [
                'label' => esc_html__( 'Choose Image Shape', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'tg_shape_size',
                'default' => 'full',
                'exclude' => [
                    'custom'
                ]
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

        <?php if ( $settings['tg_design_style']  == 'layout-2' ) : ?>

            <div class="section-title text-center">
                <h2 class="title"><?php echo esc_html__('Coming Soon','tpcore') ?></h2>
            </div>

        <?php else:

            if ( !empty($settings['tg_image']['url']) ) {
                $tg_image = !empty($settings['tg_image']['id']) ? wp_get_attachment_image_url( $settings['tg_image']['id'], $settings['tg_image_size_size']) : $settings['tg_image']['url'];
                $tg_image_alt = get_post_meta($settings["tg_image"]["id"], "_wp_attachment_image_alt", true);
            }

            if ( !empty($settings['tg_image_shape']['url']) ) {
                $tg_image_shape = !empty($settings['tg_image_shape']['id']) ? wp_get_attachment_image_url( $settings['tg_image_shape']['id'], $settings['tg_shape_size_size']) : $settings['tg_image_shape']['url'];
                $tg_image_alt = get_post_meta($settings["tg_image_shape"]["id"], "_wp_attachment_image_alt", true);
            }

            $this->add_render_attribute('title_args', 'class', 'title');

        ?>

            <!-- Ingredients-area -->
            <section id="ingredient" class="ingredients-area">
                <div class="container">
                    <div class="row align-items-center justify-content-center">

                        <?php if (!empty($settings['tg_image'])) : ?>
                            <div class="col-xl-5 col-lg-6 col-md-7">
                                <div class="ingredients-img">
                                    <img src="<?php echo esc_url($tg_image); ?>" alt="<?php echo esc_attr($tg_image_alt); ?>">
                                    <img src="<?php echo esc_url($tg_image_shape); ?>" alt="<?php echo esc_attr($tg_image_alt); ?>" class="shape">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-xl-7 col-lg-9">
                            <div class="ingredients-items-wrap">

                                <?php if ( !empty($settings['tp_section_title_show']) ) : ?>
                                    <div class="section-title mb-60">

                                        <?php if ( !empty($settings['tg_caption']) ) : ?>
                                            <p class="sub-title"><?php echo tp_kses( $settings['tg_caption'] ); ?></p>
                                        <?php endif; ?>

                                        <?php
                                            if ( !empty($settings['tg_title' ]) ) :
                                                printf( '<%1$s %2$s>%3$s</%1$s>',
                                                tag_escape( $settings['tg_title_tag'] ),
                                                $this->get_render_attribute_string( 'title_args' ),
                                                tp_kses( $settings['tg_title' ] )
                                            );
                                        endif;
                                        ?>

                                        <?php if ( !empty($settings['tg_description']) ) : ?>
                                            <p class="desc"><?php echo tp_kses( $settings['tg_description'] ); ?></p>
                                        <?php endif; ?>

                                    </div>
                                <?php endif; ?>

                                <div class="row justify-content-center justify-content-lg-start">

                                    <?php foreach ($settings['tg_ingredient_list'] as $item) : ?>
                                    <div class="col-md-6 col-sm-8">
                                        <div class="ingredients-item wow fadeInUp" data-wow-delay="<?php echo esc_attr($item['tg_ingredient_transition']) ?>s">
                                            <div class="ingredients-thumb">
                                                <?php if($item['tg_ingredient_icon_type'] !== 'image') : ?>
                                                    <?php if (!empty($item['tg_ingredient_icon']) || !empty($item['tg_ingredient_selected_icon']['value'])) : ?>
                                                        <?php tp_render_icon($item, 'tg_ingredient_icon', 'tg_ingredient_selected_icon'); ?>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <?php if (!empty($item['tg_ingredient_image']['url'])): ?>
                                                        <img class="light" src="<?php echo $item['tg_ingredient_image']['url']; ?>" alt="<?php echo get_post_meta(attachment_url_to_postid($item['tg_ingredient_image']['url']), '_wp_attachment_image_alt', true); ?>">
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ingredients-content">

                                                <?php if (!empty($item['tg_ingredient_title' ])): ?>
                                                    <h5 class="title"><?php echo tp_kses( $item['tg_ingredient_title'] ); ?></h5>
                                                <?php endif; ?>

                                                <?php if (!empty($item['tg_ingredient_description'])): ?>
                                                    <p><?php echo tp_kses( $item['tg_ingredient_description'] ); ?></p>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Ingredients-area-end -->

        <?php endif; ?>

        <?php
	}
}

$widgets_manager->register( new TG_ingredient() );