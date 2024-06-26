<?php
namespace TPCore\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Suxnix Core
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class TP_Tutor_Course extends Widget_Base {

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
		return 'tp-tutor';
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
		return __( 'Tutor Course', 'tpcore' );
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
            'tp_sub_title',
            [
                'label' => esc_html__('Sub Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'basic' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('TP Sub Title', 'tpcore'),
                'placeholder' => esc_html__('Type Sub Heading Text', 'tpcore'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'tp_title',
            [
                'label' => esc_html__('Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('TP Title Here', 'tpcore'),
                'placeholder' => esc_html__('Type Heading Text', 'tpcore'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tp_desctiption',
            [
                'label' => esc_html__('Description', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('TP section description here', 'tpcore'),
                'placeholder' => esc_html__('Type section description here', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_title_tag',
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
                    'left' => [
                        'title' => esc_html__('Left', 'tpcore'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'tpcore'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'tpcore'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .tp-sec-box' => 'text-align: {{VALUE}};'
                ]
            ]
        );
        $this->end_controls_section();



		$this->start_controls_section(
            'tp_tutor_query',
            [
                'label' => esc_html__('Tutor Query', 'tpcore'),
            ]
        );

        $post_type = 'courses';
        $taxonomy = 'course-category';

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'tpcore'),
                'description' => esc_html__('Leave blank or enter -1 for all.', 'tpcore'),
                'type' => Controls_Manager::NUMBER,
                'default' => '3',
            ]
        );

        $this->add_control(
            'category',
            [
                'label' => esc_html__('Include Categories', 'tpcore'),
                'description' => esc_html__('Select a category to include or leave blank for all.', 'tpcore'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => tp_get_categories($taxonomy),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'exclude_category',
            [
                'label' => esc_html__('Exclude Categories', 'tpcore'),
                'description' => esc_html__('Select a category to exclude', 'tpcore'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => tp_get_categories($taxonomy),
                'label_block' => true
            ]
        );

        $this->add_control(
            'post__not_in',
            [
                'label' => esc_html__('Exclude Item', 'tpcore'),
                'type' => Controls_Manager::SELECT2,
                'options' => tp_get_all_types_post($post_type),
                'multiple' => true,
                'label_block' => true
            ]
        );

        $this->add_control(
            'offset',
            [
                'label' => esc_html__('Offset', 'tpcore'),
                'type' => Controls_Manager::NUMBER,
                'default' => '0',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => array(
			        'ID' => 'Post ID',
			        'author' => 'Post Author',
			        'title' => 'Title',
			        'date' => 'Date',
			        'modified' => 'Last Modified Date',
			        'parent' => 'Parent Id',
			        'rand' => 'Random',
			        'comment_count' => 'Comment Count',
			        'menu_order' => 'Menu Order',
			    ),
                'default' => 'date',
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'asc' 	=> esc_html__( 'Ascending', 'tpcore' ),
                    'desc' 	=> esc_html__( 'Descending', 'tpcore' )
                ],
                'default' => 'desc',

            ]
        );
        $this->add_control(
            'ignore_sticky_posts',
            [
                'label' => esc_html__( 'Ignore Sticky Posts', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'tpcore' ),
                'label_off' => esc_html__( 'No', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'content',
            [
                'label' => __('Content', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tpcore'),
                'label_off' => __('Hide', 'tpcore'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'content_limit',
            [
                'label' => __('Content Limit', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '14',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'content' => 'yes'
                ]
            ]
        );


        $this->end_controls_section();


        // layout Panel
        $this->start_controls_section(
            'tp_campaign',
            [
                'label' => esc_html__('Tutor - Layout', 'tpcore'),
            ]
        );
        $this->add_control(
            'tp_design_style',
            [
                'label' => esc_html__('Select Layout', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'layout-1' => esc_html__('Grid Style 1', 'tpcore'),
                    'layout-2' => esc_html__('Grid Style 2', 'tpcore'),
                    'layout-3' => esc_html__('Grid Style 3', 'tpcore'),
                    'layout-4' => esc_html__('Grid Style 4', 'tpcore'),
                ],
                'default' => 'layout-1',
            ]
        );
        $this->add_control(
            'tp_tutor_height',
            [
                'label' => esc_html__( 'Height', 'tpcore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tp-project-img img' => 'height: {{SIZE}}{{UNIT}};object-fit: cover;',
                ],
            ]
        );
        $this->add_control(
            'tp_tutor_dots',
            [
                'label' => esc_html__('Dots?', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'tpcore'),
                'label_off' => esc_html__('Hide', 'tpcore'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_tutor_arrow',
            [
                'label' => esc_html__('Arrow Icons?', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'tpcore'),
                'label_off' => esc_html__('Hide', 'tpcore'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_tutor_infinite',
            [
                'label' => esc_html__('Infinite?', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'tpcore'),
                'label_off' => esc_html__('No', 'tpcore'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_tutor_autoplay',
            [
                'label' => esc_html__('Autoplay?', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'tpcore'),
                'label_off' => esc_html__('No', 'tpcore'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_tutor_autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => '2500',
                'title' => esc_html__('Enter autoplay speed', 'tpcore'),
                'label_block' => true,
                'condition' => array(
                    'tp_tutor_autoplay' => 'yes',
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_tutor_filter',
            [
                'label' => esc_html__('Filter?', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'tpcore'),
                'label_off' => esc_html__('No', 'tpcore'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'tp_design_style' => 'layout-3',
                ),
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'exclude' => ['custom'],
                // 'default' => 'tp-campaign-thumb',
            ]
        );
        $this->add_control(
            'tp_tutor_pagination',
            [
                'label' => esc_html__( 'Pagination', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'no',
                // 'condition' => array(
                //     'tp_design_style' => 'layout-1',
                // ),
            ]
        );

        $this->add_control(
            'tp_btn_button_show',
            [
                'label' => esc_html__( 'Show Button', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'tp_design_style' => 'layout-3',
                ),
            ]
        );

        $this->add_control(
            'tp_btn_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Button Text', 'tpcore'),
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
                'condition' => [
                    'tp_btn_button_show' => 'yes'
                ],
            ]
        );


        $this->add_control(
            'tp_image_show',
            [
                'label' => esc_html__( 'Show Image', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tp_prcie_show',
            [
                'label' => esc_html__( 'Show Price', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tp_cat_show',
            [
                'label' => esc_html__( 'Show Category', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tp_author_show',
            [
                'label' => esc_html__( 'Show Author', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tp_lessons_show',
            [
                'label' => esc_html__( 'Show Lessons', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tp_duration_show',
            [
                'label' => esc_html__( 'Show Duration', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tp_rating_show',
            [
                'label' => esc_html__( 'Show Rating', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tp_students_show',
            [
                'label' => esc_html__( 'Show Students', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'course_list_difficulty_settings',
            [
                'label' => esc_html__( 'Show difficulty', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'course_list_wishlist_settings',
            [
                'label' => esc_html__( 'Show wishlist', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // tp_tutor_columns_section
        $this->start_controls_section(
            'tp_tutor_columns_section',
            [
                'label' => esc_html__('Tutor - Columns', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_tutor__for_desktop',
            [
                'label' => esc_html__( 'Columns for Desktop', 'tpcore' ),
                'description' => esc_html__( 'Screen width equal to or greater than 992px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    12 => esc_html__( '1 Columns', 'tpcore' ),
                    6 => esc_html__( '2 Columns', 'tpcore' ),
                    4 => esc_html__( '3 Columns', 'tpcore' ),
                    3 => esc_html__( '4 Columns', 'tpcore' ),
                    2 => esc_html__( '6 Columns', 'tpcore' ),
                    1 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '4',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            'tp_tutor__for_laptop',
            [
                'label' => esc_html__( 'Columns for Laptop', 'tpcore' ),
                'description' => esc_html__( 'Screen width equal to or greater than 768px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    12 => esc_html__( '1 Columns', 'tpcore' ),
                    6 => esc_html__( '2 Columns', 'tpcore' ),
                    4 => esc_html__( '3 Columns', 'tpcore' ),
                    3 => esc_html__( '4 Columns', 'tpcore' ),
                    2 => esc_html__( '6 Columns', 'tpcore' ),
                    1 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '4',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            'tp_tutor__for_tablet',
            [
                'label' => esc_html__( 'Columns for Tablet', 'tpcore' ),
                'description' => esc_html__( 'Screen width equal to or greater than 576px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    12 => esc_html__( '1 Columns', 'tpcore' ),
                    6 => esc_html__( '2 Columns', 'tpcore' ),
                    4 => esc_html__( '3 Columns', 'tpcore' ),
                    3 => esc_html__( '4 Columns', 'tpcore' ),
                    2 => esc_html__( '6 Columns', 'tpcore' ),
                    1 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '6',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            '
            ',
            [
                'label' => esc_html__( 'Columns for Mobile', 'tpcore' ),
                'description' => esc_html__( 'Screen width less than 576px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    12 => esc_html__( '1 Columns', 'tpcore' ),
                    6 => esc_html__( '2 Columns', 'tpcore' ),
                    4 => esc_html__( '3 Columns', 'tpcore' ),
                    3 => esc_html__( '4 Columns', 'tpcore' ),
                    5 => esc_html__( '5 Columns (For Carousel Item)', 'tpcore' ),
                    2 => esc_html__( '6 Columns', 'tpcore' ),
                    1 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '12',
                'style_transfer' => true,
            ]
        );

        $this->end_controls_section();

        // tp_tutor_slider_columns_section
		$this->start_controls_section(
            'tp_tutor_slider_columns_section',
            [
                'label' => esc_html__('Tutor - Columns for Carousel', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_tutor_slider_for_xl_desktop',
            [
                'label' => esc_html__( 'Columns for Extra Large Desktop', 'tpcore' ),
                'description' => esc_html__( 'Screen width equal to or greater than 1920px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => esc_html__( '1 Columns', 'tpcore' ),
                    2 => esc_html__( '2 Columns', 'tpcore' ),
                    3 => esc_html__( '3 Columns', 'tpcore' ),
                    4 => esc_html__( '4 Columns', 'tpcore' ),
                    5 => esc_html__( '5 Columns', 'tpcore' ),
                    6 => esc_html__( '6 Columns', 'tpcore' ),
                    7 => esc_html__( '7 Columns', 'tpcore' ),
                    8 => esc_html__( '8 Columns', 'tpcore' ),
                    9 => esc_html__( '9 Columns', 'tpcore' ),
                    10 => esc_html__( '10 Columns', 'tpcore' ),
                    11 => esc_html__( '10 Columns', 'tpcore' ),
                    12 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '3',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            'tp_tutor_slider_for_desktop',
            [
                'label' => esc_html__( 'Columns for Desktop', 'tpcore' ),
                'description' => esc_html__( 'Screen width equal to or greater than 1200px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => esc_html__( '1 Columns', 'tpcore' ),
                    2 => esc_html__( '2 Columns', 'tpcore' ),
                    3 => esc_html__( '3 Columns', 'tpcore' ),
                    4 => esc_html__( '4 Columns', 'tpcore' ),
                    5 => esc_html__( '5 Columns', 'tpcore' ),
                    6 => esc_html__( '6 Columns', 'tpcore' ),
                    7 => esc_html__( '7 Columns', 'tpcore' ),
                    8 => esc_html__( '8 Columns', 'tpcore' ),
                    9 => esc_html__( '9 Columns', 'tpcore' ),
                    10 => esc_html__( '10 Columns', 'tpcore' ),
                    11 => esc_html__( '10 Columns', 'tpcore' ),
                    12 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '3',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            'tp_tutor_slider_for_laptop',
            [
                'label' => esc_html__( 'Columns for Laptop', 'tpcore' ),
                'description' => esc_html__( 'Screen width equal to or greater than 992px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => esc_html__( '1 Columns', 'tpcore' ),
                    2 => esc_html__( '2 Columns', 'tpcore' ),
                    3 => esc_html__( '3 Columns', 'tpcore' ),
                    4 => esc_html__( '4 Columns', 'tpcore' ),
                    5 => esc_html__( '5 Columns', 'tpcore' ),
                    6 => esc_html__( '6 Columns', 'tpcore' ),
                    7 => esc_html__( '7 Columns', 'tpcore' ),
                    8 => esc_html__( '8 Columns', 'tpcore' ),
                    9 => esc_html__( '9 Columns', 'tpcore' ),
                    10 => esc_html__( '10 Columns', 'tpcore' ),
                    11 => esc_html__( '10 Columns', 'tpcore' ),
                    12 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '3',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            'tp_tutor_slider_for_tablet',
            [
                'label' => esc_html__( 'Columns for Tablet', 'tpcore' ),
                'description' => esc_html__( 'Screen width equal to or greater than 768px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => esc_html__( '1 Columns', 'tpcore' ),
                    2 => esc_html__( '2 Columns', 'tpcore' ),
                    3 => esc_html__( '3 Columns', 'tpcore' ),
                    4 => esc_html__( '4 Columns', 'tpcore' ),
                    5 => esc_html__( '5 Columns', 'tpcore' ),
                    6 => esc_html__( '6 Columns', 'tpcore' ),
                    7 => esc_html__( '7 Columns', 'tpcore' ),
                    8 => esc_html__( '8 Columns', 'tpcore' ),
                    9 => esc_html__( '9 Columns', 'tpcore' ),
                    10 => esc_html__( '10 Columns', 'tpcore' ),
                    11 => esc_html__( '10 Columns', 'tpcore' ),
                    12 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '2',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            'tp_tutor_slider_for_mobile',
            [
                'label' => esc_html__( 'Columns for Mobile', 'tpcore' ),
                'description' => esc_html__( 'Screen width less than 767', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => esc_html__( '1 Columns', 'tpcore' ),
                    2 => esc_html__( '2 Columns', 'tpcore' ),
                    3 => esc_html__( '3 Columns', 'tpcore' ),
                    4 => esc_html__( '4 Columns', 'tpcore' ),
                    5 => esc_html__( '5 Columns', 'tpcore' ),
                    6 => esc_html__( '6 Columns', 'tpcore' ),
                    7 => esc_html__( '7 Columns', 'tpcore' ),
                    8 => esc_html__( '8 Columns', 'tpcore' ),
                    9 => esc_html__( '9 Columns', 'tpcore' ),
                    10 => esc_html__( '10 Columns', 'tpcore' ),
                    11 => esc_html__( '10 Columns', 'tpcore' ),
                    12 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '1',
                'style_transfer' => true,
            ]
        );
        $this->add_control(
            'tp_tutor_slider_for_xs_mobile',
            [
                'label' => esc_html__( 'Columns for Extra Small Mobile', 'tpcore' ),
                'description' => esc_html__( 'Screen width less than 575px', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => esc_html__( '1 Columns', 'tpcore' ),
                    2 => esc_html__( '2 Columns', 'tpcore' ),
                    3 => esc_html__( '3 Columns', 'tpcore' ),
                    4 => esc_html__( '4 Columns', 'tpcore' ),
                    5 => esc_html__( '5 Columns', 'tpcore' ),
                    6 => esc_html__( '6 Columns', 'tpcore' ),
                    7 => esc_html__( '7 Columns', 'tpcore' ),
                    8 => esc_html__( '8 Columns', 'tpcore' ),
                    9 => esc_html__( '9 Columns', 'tpcore' ),
                    10 => esc_html__( '10 Columns', 'tpcore' ),
                    11 => esc_html__( '10 Columns', 'tpcore' ),
                    12 => esc_html__( '12 Columns', 'tpcore' ),
                ],
                'separator' => 'before',
                'default' => '1',
                'style_transfer' => true,
            ]
        );

        $this->end_controls_section();


        // style control


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

		if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        // include_categories
        $category_list = '';
        if (!empty($settings['category'])) {
            $category_list = implode(", ", $settings['category']);
        }
        $category_list_value = explode(" ", $category_list);

        // exclude_categories
        $exclude_categories = '';
        if(!empty($settings['exclude_category'])){
            $exclude_categories = implode(", ", $settings['exclude_category']);
        }
        $exclude_category_list_value = explode(" ", $exclude_categories);

        $post__not_in = '';
        if (!empty($settings['post__not_in'])) {
            $post__not_in = $settings['post__not_in'];
            $args['post__not_in'] = $post__not_in;
        }
        $posts_per_page = (!empty($settings['posts_per_page'])) ? $settings['posts_per_page'] : '-1';
        $orderby = (!empty($settings['orderby'])) ? $settings['orderby'] : 'post_date';
        $order = (!empty($settings['order'])) ? $settings['order'] : 'desc';
        $offset_value = (!empty($settings['offset'])) ? $settings['offset'] : '0';
        $ignore_sticky_posts = (! empty( $settings['ignore_sticky_posts'] ) && 'yes' == $settings['ignore_sticky_posts']) ? true : false ;


        // number
        $off = (!empty($offset_value)) ? $offset_value : 0;
        $offset = $off + (($paged - 1) * $posts_per_page);
        $p_ids = array();

        // build up the array
        if (!empty($settings['post__not_in'])) {
            foreach ($settings['post__not_in'] as $p_idsn) {
                $p_ids[] = $p_idsn;
            }
        }

        $args = array(
            'post_type' => 'courses',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'orderby' => $orderby,
            'order' => $order,
            'offset' => $offset,
            'paged' => $paged,
            'post__not_in' => $p_ids,
            'ignore_sticky_posts' => $ignore_sticky_posts
        );

        // exclude_categories
        if ( !empty($settings['exclude_category'])) {

            // Exclude the correct cats from tax_query
            $args['tax_query'] = array(
                array(
                    'taxonomy'	=> 'course-category',
                    'field'	 	=> 'slug',
                    'terms'		=> $exclude_category_list_value,
                    'operator'	=> 'NOT IN'
                )
            );

            // Include the correct cats in tax_query
            if ( !empty($settings['category'])) {
                $args['tax_query']['relation'] = 'AND';
                $args['tax_query'][] = array(
                    'taxonomy'	=> 'course-category',
                    'field'		=> 'slug',
                    'terms'		=> $category_list_value,
                    'operator'	=> 'IN'
                );
            }

        } else {
            // Include the cats from $cat_slugs in tax_query
            if (!empty($settings['category'])) {
                $args['tax_query'][] = [
                    'taxonomy' => 'course-category',
                    'field' => 'slug',
                    'terms' => $category_list_value,
                ];
            }
        }

        $filter_list = $settings['category'];

        // The Query
        $query = new \WP_Query($args);

        // var_dump($query);

        $carousel_args = [
            'arrows' => ('yes' === $settings['tp_tutor_arrow']),
            'dots' => ('yes' === $settings['tp_tutor_dots']),
            'autoplay' => ('yes' === $settings['tp_tutor_autoplay']),
            'autoplay_speed' => absint($settings['tp_tutor_autoplay_speed']),
            'infinite' => ('yes' === $settings['tp_tutor_infinite']),
            'for_xl_desktop' => absint($settings['tp_tutor_slider_for_xl_desktop']),
            'slidesToShow' => absint($settings['tp_tutor_slider_for_desktop']),
            'for_laptop' => absint($settings['tp_tutor_slider_for_laptop']),
            'for_tablet' => absint($settings['tp_tutor_slider_for_tablet']),
            'for_mobile' => absint($settings['tp_tutor_slider_for_mobile']),
            'for_xs_mobile' => absint($settings['tp_tutor_slider_for_xs_mobile']),
        ];
        $this->add_render_attribute('tp-carousel-campaign-data', 'data-settings', wp_json_encode($carousel_args));

        ?>

        <?php if ( $settings['tp_design_style']  == 'layout-2' ): ?>
         <section class="course__area">
            <div class="container">
               <div class="row">
                    <?php
                        global $authordata;
                        while ($query->have_posts()) : $query->the_post();
                        $terms = get_the_terms(get_the_ID(), 'course-category');
                        $profile_url = tutor_utils()->profile_url($authordata->ID);
                        $course_rating = tutor_utils()->get_course_rating();
                        $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                        $tutor_course_duration = get_tutor_course_duration_context(get_the_ID());
                        $course_duration = get_tutor_course_duration_context();
                        $course_students = tutor_utils()->count_enrolled_users_by_course();
                    ?>
                   <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                      <div class="course__item-2 transition-3 white-bg mb-30 fix">
                           <?php if ('yes' === $settings['tp_image_show']): ?>
                           <div class="course__thumb-2 p-relative w-img fix">
                                <a href="<?php print get_the_permalink() ?>">
                                    <?php echo get_the_post_thumbnail(get_the_ID(), $settings['thumbnail_size']); ?>
                                </a>

                                <div class="tutor-course-loop-header-meta">
                                    <?php
                                    $course_id     = get_the_ID();
                                    $is_wishlisted = tutor_utils()->is_wishlisted( $course_id );
                                    $has_wish_list = '';
                                    if ( $is_wishlisted ) {
                                        $has_wish_list = 'has-wish-listed';
                                    }
                                    $icon_class = 'tutor-icon-fav-line-filled';
                                    if ( $is_wishlisted ) {
                                        $icon_class = 'tutor-icon-fav-full-filled';
                                    }
                                    $action_class = '';
                                    if ( is_user_logged_in() ) {
                                        $action_class = apply_filters( 'tutor_wishlist_btn_class', 'tutor-course-wishlist-btn' );
                                    } else {
                                        $action_class = apply_filters( 'tutor_popup_login_class', 'cart-required-login' );
                                    }
                                    if ( 'yes' === $settings['course_list_difficulty_settings'] ) {
                                        echo '<span class="tutor-course-loop-level">' . get_tutor_course_level() . '</span>';
                                    }
                                    if ( 'yes' === $settings['course_list_wishlist_settings'] ) {
                                        ?>
                                        <span class="tutor-course-wishlist tutor-course-listing-item-head save-bookmark-btn"><a href="javascript:;" class="<?php echo esc_attr( $icon_class . ' ' . $action_class . ' ' .  $has_wish_list );?>" data-course-id="<?php echo esc_attr( $course_id );?>"></a> </span>
                                        <?php
                                    }
                                    ?>
                                </div>

                           </div>
                           <?php endif; ?>

                         <div class="course__content-2">
                            <div class="course__top-2 d-flex align-items-center justify-content-between">
                               <?php if ('yes' === $settings['tp_cat_show']): ?>
                               <?php if (!empty($terms)): ?>
                               <div class="course__tag-2">
                                  <?php foreach ($terms as $term) : ?>
                                    <a href="<?php echo get_term_link($term->slug, 'course-category'); ?>"><?php echo $term->name; ?></a>
                                   <?php endforeach; ?>
                               </div>
                               <?php endif; ?>
                               <?php endif; ?>

                               <?php if ('yes' === $settings['tp_prcie_show']): ?>
                               <div class="course__price-2">
                                  <span>
                                        <?php
                                            $course_id = get_the_ID();
                                            $default_price = apply_filters('tutor-loop-default-price', __('Free', 'micourse'));
                                            $price_html = '<span> ' . $default_price . '</span>';
                                            if (tutor_utils()->is_course_purchasable()) {

                                                $product_id = tutor_utils()->get_course_product_id($course_id);
                                                $product = wc_get_product($product_id);

                                                if ($product) {
                                                    $price_html = '<span> ' . $product->get_price_html() . ' </span>';
                                                }
                                            }
                                            echo $price_html;
                                        ?>
                                  </span>
                               </div>
                               <?php endif; ?>
                            </div>
                            <h3 class="course__title-2">
                               <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <?php if (!empty($settings['content'])):
                            $content_limit = (!empty($settings['content_limit'])) ? $settings['content_limit'] : '';
                            ?>
                            <p><?php print wp_trim_words(get_the_excerpt(get_the_ID()), $content_limit, ''); ?></p>
                            <?php endif; ?>

                            <div class="course__bottom-2 d-flex align-items-center justify-content-between">
                               <div class="course__action">
                                  <ul>
                                    <?php if ('yes' === $settings['tp_students_show']): ?>
                                     <li>
                                        <div class="course__action-item d-flex align-items-center">
                                           <div class="course__action-icon mr-5">
                                              <span>
                                                 <i class="tutor-icon-user-filled"></i>
                                              </span>
                                           </div>
                                           <div class="course__action-content">
                                            <span>
                                                <?php echo $course_students; ?>
                                            </span>
                                           </div>
                                        </div>
                                     </li>
                                     <?php endif; ?>


                                    <?php if ('yes' === $settings['tp_lessons_show']): ?>
                                     <li>
                                        <div class="course__action-item d-flex align-items-center">
                                           <div class="course__action-icon mr-5">
                                              <span>
                                                 <i class="far fa-book-alt"></i>
                                              </span>
                                           </div>
                                           <div class="course__action-content">
                                              <span><?php echo $tutor_lesson_count; ?></span>
                                           </div>
                                        </div>
                                     </li>
                                     <?php endif; ?>

                                     <?php if ('yes' === $settings['tp_rating_show']): ?>
                                     <li>
                                        <div class="course__action-item d-flex align-items-center">
                                           <div class="course__action-icon mr-5">
                                              <span>
                                                 <i class="tutor-icon-star-line-filled"></i>
                                              </span>
                                           </div>
                                           <div class="course__action-content">
                                              <span>
                                                    <?php
                                                        if ($course_rating->rating_avg >= 0) {
                                                            echo '<i class="icon_star"></i>' . apply_filters('tutor_course_rating_average', $course_rating->rating_avg) . '';

                                                            echo '<span class="rating-count-gap">(' . apply_filters('tutor_course_rating_count', $course_rating->rating_count) . ')</span>';
                                                        }
                                                    ?>
                                                </span>
                                           </div>
                                        </div>
                                     </li>
                                     <?php endif; ?>
                                  </ul>
                               </div>
                               <?php if ('yes' === $settings['tp_author_show']): ?>
                               <div class="course__tutor-2">
                                  <a href="#">
                                     <?php echo get_avatar(get_the_author_meta('ID'), 50) ?>
                                  </a>
                               </div>
                               <?php endif; ?>

                            </div>
                         </div>
                      </div>
                   </div>

                     <?php endwhile; wp_reset_query(); ?>
               </div>

                <?php if($settings['tp_tutor_pagination'] == 'yes' && '-1' != $settings['posts_per_page'] ) : ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="basic-pagination">
                                <?php
                                $big = 999999999; // need an unlikely integer

                                if (get_query_var('paged')) {
                                    $paged = get_query_var('paged');
                                } else if (get_query_var('page')) {
                                    $paged = get_query_var('page');
                                } else {
                                    $paged = 1;
                                }
                                echo paginate_links( array(
                                    'base'       => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
                                    'format'     => '?paged=%#%',
                                    'current'    => $paged,
                                    'total'      => $query->max_num_pages,
                                    'type'       =>'list',
                                    'prev_text'  =>'<i class="far fa-angle-left"></i>',
                                    'next_text'  =>'<i class="far fa-angle-right"></i>',
                                    'show_all'   => false,
                                    'end_size'   => 1,
                                    'mid_size'   => 4,
                                ) );
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
         </section>

        <?php elseif ($settings['tp_design_style'] === 'layout-3'): ?>

         <section class="course__area">
            <div class="container">
               <div class="row">
                    <?php
                        global $authordata;
                        while ($query->have_posts()) : $query->the_post();
                        $course_icon = function_exists( 'get_field' ) ? get_field( 'course_icon' ) : '';
                        $terms = get_the_terms(get_the_ID(), 'course-category');
                        $profile_url = tutor_utils()->profile_url($authordata->ID);
                        $course_rating = tutor_utils()->get_course_rating();
                        $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                        $tutor_course_duration = get_tutor_course_duration_context(get_the_ID());
                        $course_duration = get_tutor_course_duration_context();
                        $course_students = tutor_utils()->count_enrolled_users_by_course();
                    ?>
                   <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                       <?php if ('yes' === $settings['tp_image_show']): ?>
                       <div class="course__thumb-2 p-relative w-img fix">
                            <a href="<?php print get_the_permalink() ?>">
                                <?php echo get_the_post_thumbnail(get_the_ID(), $settings['thumbnail_size']); ?>
                            </a>

                            <div class="tutor-course-loop-header-meta">
                                <?php
                                $course_id     = get_the_ID();
                                $is_wishlisted = tutor_utils()->is_wishlisted( $course_id );
                                $has_wish_list = '';
                                if ( $is_wishlisted ) {
                                    $has_wish_list = 'has-wish-listed';
                                }
                                $icon_class = 'tutor-icon-fav-line-filled';
                                if ( $is_wishlisted ) {
                                    $icon_class = 'tutor-icon-fav-full-filled';
                                }
                                $action_class = '';
                                if ( is_user_logged_in() ) {
                                    $action_class = apply_filters( 'tutor_wishlist_btn_class', 'tutor-course-wishlist-btn' );
                                } else {
                                    $action_class = apply_filters( 'tutor_popup_login_class', 'cart-required-login' );
                                }
                                if ( 'yes' === $settings['course_list_difficulty_settings'] ) {
                                    echo '<span class="tutor-course-loop-level">' . get_tutor_course_level() . '</span>';
                                }
                                if ( 'yes' === $settings['course_list_wishlist_settings'] ) {
                                    ?>
                                    <span class="tutor-course-wishlist tutor-course-listing-item-head save-bookmark-btn"><a href="javascript:;" class="<?php echo esc_attr( $icon_class . ' ' . $action_class . ' ' .  $has_wish_list );?>" data-course-id="<?php echo esc_attr( $course_id );?>"></a> </span>
                                    <?php
                                }
                                ?>
                            </div>

                       </div>
                       <?php endif; ?>

                         <div class="course__item-3 white-bg transition-3 mb-30">
                            <div class="course__icon-3 mb-30">
                               <span>
                                  <i class="<?php echo $course_icon; ?>"></i>
                               </span>
                            </div>
                            <div class="course__content-3">
                               <h3 class="course__title-3">
                                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                               </h3>

                               <?php if (!empty($settings['content'])):
                                $content_limit = (!empty($settings['content_limit'])) ? $settings['content_limit'] : '';
                                ?>
                                <p><?php print wp_trim_words(get_the_excerpt(get_the_ID()), $content_limit, ''); ?></p>
                                <?php endif; ?>

                               <div class="course__meta d-flex align-items-center justify-content-between">
                                   <?php if ('yes' === $settings['tp_cat_show']): ?>
                                   <?php if (!empty($terms)): ?>
                                   <div class="course__tag-3">
                                      <?php foreach ($terms as $term) : ?>
                                        <a href="<?php echo get_term_link($term->slug, 'course-category'); ?>"><?php echo $term->name; ?></a>
                                       <?php endforeach; ?>
                                   </div>
                                   <?php endif; ?>
                                   <?php endif; ?>
                                  <div class="course__price-3">
                                     <span>
                                            <?php
                                                $course_id = get_the_ID();
                                                $default_price = apply_filters('tutor-loop-default-price', __('Free', 'micourse'));
                                                $price_html = '<span> ' . $default_price . '</span>';
                                                if (tutor_utils()->is_course_purchasable()) {

                                                    $product_id = tutor_utils()->get_course_product_id($course_id);
                                                    $product = wc_get_product($product_id);

                                                    if ($product) {
                                                        $price_html = '<span> ' . $product->get_price_html() . ' </span>';
                                                    }
                                                }
                                                echo $price_html;
                                            ?>
                                     </span>
                                  </div>
                               </div>
                               <div class="course__sort-info">
                                  <ul>
                                    <?php if ('yes' === $settings['tp_lessons_show']): ?>
                                     <li>
                                        <div class="course__lesson-3">
                                           <a href="#">
                                              <span>
                                                 <svg width="10" height="12" viewBox="0 0 10 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 1.91583C1 1.52025 1.43762 1.28133 1.77038 1.49524L8.12353 5.57941C8.42969 5.77623 8.42969 6.22377 8.12353 6.42059L1.77038 10.5048C1.43762 10.7187 1 10.4798 1 10.0842V1.91583Z" fill="#007A70" stroke="#007A70" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                 </svg>
                                              </span>
                                              <?php echo $tutor_lesson_count; ?> <?php echo esc_html__('videos','tpcore'); ?>
                                           </a>
                                        </div>
                                     </li>
                                     <?php endif; ?>

                                     <?php if ('yes' === $settings['tp_rating_show']): ?>
                                     <li>
                                        <div class="course__review-3">
                                           <a href="#">
                                              <span>
                                                 <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7.03658 0.866411L8.09247 2.99539C8.23645 3.29176 8.62041 3.57603 8.94437 3.63046L10.8582 3.95102C12.082 4.15666 12.37 5.0518 11.4881 5.93484L10.0003 7.43481C9.74828 7.68883 9.6103 8.17874 9.68829 8.52954L10.1142 10.3864C10.4502 11.8561 9.67629 12.4246 8.38643 11.6565L6.59263 10.5859C6.26867 10.3924 5.73473 10.3924 5.40476 10.5859L3.61096 11.6565C2.3271 12.4246 1.54719 11.85 1.88315 10.3864L2.3091 8.52954C2.3871 8.17874 2.24911 7.68883 1.99714 7.43481L0.509303 5.93484C-0.3666 5.0518 -0.084631 4.15666 1.13923 3.95102L3.05302 3.63046C3.37099 3.57603 3.75494 3.29176 3.89893 2.99539L4.95481 0.866411C5.53075 -0.288804 6.46664 -0.288804 7.03658 0.866411Z" fill="#FFAE10"/>
                                                 </svg>
                                              </span>
                                               <?php
                                                    if ($course_rating->rating_avg >= 0) {
                                                        echo '<i class="icon_star"></i>' . apply_filters('tutor_course_rating_average', $course_rating->rating_avg) . '';

                                                        echo '<span class="rating-count-gap">(' . apply_filters('tutor_course_rating_count', $course_rating->rating_count) . ')</span>';
                                                    }
                                                ?>
                                                <?php echo esc_html__('Reviews','tpcore'); ?>
                                           </a>
                                        </div>
                                     </li>
                                     <?php endif; ?>

                                     <?php if ('yes' === $settings['tp_author_show']): ?>
                                     <li>
                                        <div class="course__tutor-3">
                                           <a href="#">
                                              <?php echo get_avatar(get_the_author_meta('ID'), 50) ?>
                                           </a>
                                        </div>
                                     </li>
                                     <?php endif; ?>
                                  </ul>
                               </div>

                               <?php if ('yes' === $settings['tp_cat_show']): ?>
                               <div class="course__join">
                                  <a href="<?php the_permalink(); ?>" class="tp-btn-5 tp-btn-10"><?php echo tp_kses($settings['tp_btn_text']); ?></a>
                               </div>
                               <?php endif; ?>

                            </div>
                         </div>
                      </div>

                         <?php endwhile; wp_reset_query(); ?>
                   </div>

                <?php if($settings['tp_tutor_pagination'] == 'yes' && '-1' != $settings['posts_per_page'] ) : ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="basic-pagination">
                                <?php
                                $big = 999999999; // need an unlikely integer

                                if (get_query_var('paged')) {
                                    $paged = get_query_var('paged');
                                } else if (get_query_var('page')) {
                                    $paged = get_query_var('page');
                                } else {
                                    $paged = 1;
                                }
                                echo paginate_links( array(
                                    'base'       => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
                                    'format'     => '?paged=%#%',
                                    'current'    => $paged,
                                    'total'      => $query->max_num_pages,
                                    'type'       =>'list',
                                    'prev_text'  =>'<i class="far fa-angle-left"></i>',
                                    'next_text'  =>'<i class="far fa-angle-right"></i>',
                                    'show_all'   => false,
                                    'end_size'   => 1,
                                    'mid_size'   => 4,
                                ) );
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
         </section>

        <?php elseif ($settings['tp_design_style'] === 'layout-4'): ?>
         <section class="course__areaW">
            <div class="container">
               <div class="row">
                  <div class="col-xxl-12">
                     <div class="course__filter masonary-menu text-center mb-30">
                        <div class="nav-tabs">
                        <?php
                        $i = 1;
                        foreach ( $filter_list as $list ): ?>
                            <?php if ( $i === 1 ): $i++; ?>
                            <button class="active nav-link" data-filter="*"><?php echo esc_html__( 'See All','tocore' ); ?></button>
                            <button class="nav-link" data-filter=".<?php echo esc_attr( $list ); ?>"><?php echo esc_html( $list ); ?></button>
                            <?php else: ?>
                            <button class="nav-link" data-filter=".<?php echo esc_attr( $list ); ?>"><?php echo esc_html( $list ); ?></button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row grid">
                    <?php
                        global $authordata;
                        while ($query->have_posts()) : $query->the_post();
                        $course_icon = function_exists( 'get_field' ) ? get_field( 'course_icon' ) : '';
                        $terms = get_the_terms(get_the_ID(), 'course-category');
                        $profile_url = tutor_utils()->profile_url($authordata->ID);
                        $course_rating = tutor_utils()->get_course_rating();
                        $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                        $tutor_course_duration = get_tutor_course_duration_context(get_the_ID());
                        $course_duration = get_tutor_course_duration_context();
                        $course_students = tutor_utils()->count_enrolled_users_by_course();

                        $item_classes = '';
                        $item_cat_names = '';
                        $item_cats = get_the_terms( get_the_ID(), 'course-category' );
                        if( !empty($item_cats) ):
                            $count = count($item_cats) - 1;
                            foreach($item_cats as $key => $item_cat) {
                                $item_classes .= $item_cat->slug . ' ';
                                $item_cat_names .= ( $count > $key ) ? $item_cat->name  . ', ' : $item_cat->name;
                            }
                        endif;
                    ?>
                   <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 grid-item <?php echo $item_classes; ?>">
                      <div class="course__item-2 transition-3 white-bg mb-30 fix">
                           <?php if ('yes' === $settings['tp_image_show']): ?>
                           <div class="course__thumb-2 p-relative w-img fix">
                                <a href="<?php print get_the_permalink() ?>">
                                    <?php echo get_the_post_thumbnail(get_the_ID(), $settings['thumbnail_size']); ?>
                                </a>

                                <div class="tutor-course-loop-header-meta">
                                    <?php
                                    $course_id     = get_the_ID();
                                    $is_wishlisted = tutor_utils()->is_wishlisted( $course_id );
                                    $has_wish_list = '';
                                    if ( $is_wishlisted ) {
                                        $has_wish_list = 'has-wish-listed';
                                    }
                                    $icon_class = 'tutor-icon-fav-line-filled';
                                    if ( $is_wishlisted ) {
                                        $icon_class = 'tutor-icon-fav-full-filled';
                                    }
                                    $action_class = '';
                                    if ( is_user_logged_in() ) {
                                        $action_class = apply_filters( 'tutor_wishlist_btn_class', 'tutor-course-wishlist-btn' );
                                    } else {
                                        $action_class = apply_filters( 'tutor_popup_login_class', 'cart-required-login' );
                                    }
                                    if ( 'yes' === $settings['course_list_difficulty_settings'] ) {
                                        echo '<span class="tutor-course-loop-level">' . get_tutor_course_level() . '</span>';
                                    }
                                    if ( 'yes' === $settings['course_list_wishlist_settings'] ) {
                                        ?>
                                        <span class="tutor-course-wishlist tutor-course-listing-item-head save-bookmark-btn"><a href="javascript:;" class="<?php echo esc_attr( $icon_class . ' ' . $action_class . ' ' .  $has_wish_list );?>" data-course-id="<?php echo esc_attr( $course_id );?>"></a> </span>
                                        <?php
                                    }
                                    ?>
                                </div>

                           </div>
                           <?php endif; ?>

                         <div class="course__content-2">
                            <div class="course__top-2 d-flex align-items-center justify-content-between">
                               <?php if ('yes' === $settings['tp_cat_show']): ?>
                               <?php if (!empty($terms)): ?>
                               <div class="course__tag-2">
                                  <?php foreach ($terms as $term) : ?>
                                    <a href="<?php echo get_term_link($term->slug, 'course-category'); ?>"><?php echo $term->name; ?></a>
                                   <?php endforeach; ?>
                               </div>
                               <?php endif; ?>
                               <?php endif; ?>

                               <?php if ('yes' === $settings['tp_prcie_show']): ?>
                               <div class="course__price-2">
                                  <span>
                                        <?php
                                            $course_id = get_the_ID();
                                            $default_price = apply_filters('tutor-loop-default-price', __('Free', 'micourse'));
                                            $price_html = '<span> ' . $default_price . '</span>';
                                            if (tutor_utils()->is_course_purchasable()) {

                                                $product_id = tutor_utils()->get_course_product_id($course_id);
                                                $product = wc_get_product($product_id);

                                                if ($product) {
                                                    $price_html = '<span> ' . $product->get_price_html() . ' </span>';
                                                }
                                            }
                                            echo $price_html;
                                        ?>
                                  </span>
                               </div>
                               <?php endif; ?>
                            </div>
                            <h3 class="course__title-2">
                               <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <?php if (!empty($settings['content'])):
                            $content_limit = (!empty($settings['content_limit'])) ? $settings['content_limit'] : '';
                            ?>
                            <p><?php print wp_trim_words(get_the_excerpt(get_the_ID()), $content_limit, ''); ?></p>
                            <?php endif; ?>

                            <div class="course__bottom-2 d-flex align-items-center justify-content-between">
                               <div class="course__action">
                                  <ul>
                                    <?php if ('yes' === $settings['tp_students_show']): ?>
                                     <li>
                                        <div class="course__action-item d-flex align-items-center">
                                           <div class="course__action-icon mr-5">
                                              <span>
                                                 <i class="tutor-icon-user-filled"></i>
                                              </span>
                                           </div>
                                           <div class="course__action-content">
                                            <span>
                                                <?php echo $course_students; ?>
                                            </span>
                                           </div>
                                        </div>
                                     </li>
                                     <?php endif; ?>


                                    <?php if ('yes' === $settings['tp_lessons_show']): ?>
                                     <li>
                                        <div class="course__action-item d-flex align-items-center">
                                           <div class="course__action-icon mr-5">
                                              <span>
                                                 <i class="far fa-book-alt"></i>
                                              </span>
                                           </div>
                                           <div class="course__action-content">
                                              <span><?php echo $tutor_lesson_count; ?></span>
                                           </div>
                                        </div>
                                     </li>
                                     <?php endif; ?>

                                     <?php if ('yes' === $settings['tp_rating_show']): ?>
                                     <li>
                                        <div class="course__action-item d-flex align-items-center">
                                           <div class="course__action-icon mr-5">
                                              <span>
                                                 <i class="tutor-icon-star-line-filled"></i>
                                              </span>
                                           </div>
                                           <div class="course__action-content">
                                              <span>
                                                    <?php
                                                        if ($course_rating->rating_avg >= 0) {
                                                            echo '<i class="icon_star"></i>' . apply_filters('tutor_course_rating_average', $course_rating->rating_avg) . '';

                                                            echo '<span class="rating-count-gap">(' . apply_filters('tutor_course_rating_count', $course_rating->rating_count) . ')</span>';
                                                        }
                                                    ?>
                                                </span>
                                           </div>
                                        </div>
                                     </li>
                                     <?php endif; ?>
                                  </ul>
                               </div>
                               <?php if ('yes' === $settings['tp_author_show']): ?>
                               <div class="course__tutor-2">
                                  <a href="#">
                                     <?php echo get_avatar(get_the_author_meta('ID'), 50) ?>
                                  </a>
                               </div>
                               <?php endif; ?>

                            </div>
                         </div>
                      </div>
                   </div>
                    <?php endwhile; wp_reset_query(); ?>
               </div>
            </div>
         </section>

        <?php else: ?>

         <section class="course__area">
            <div class="container">
               <div class="row">
                    <?php
                        global $authordata;
                        while ($query->have_posts()) : $query->the_post();
                        $terms = get_the_terms(get_the_ID(), 'course-category');
                        $profile_url = tutor_utils()->profile_url($authordata->ID);
                        $course_rating = tutor_utils()->get_course_rating();
                        $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                        $tutor_course_duration = get_tutor_course_duration_context(get_the_ID());
                    ?>
                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                        <div class="course__item white-bg transition-3 mb-30">
                           <?php if ('yes' === $settings['tp_image_show']): ?>
                           <div class="course__thumb p-relative w-img fix">
                                <a href="<?php print get_the_permalink() ?>">
                                    <?php echo get_the_post_thumbnail(get_the_ID(), $settings['thumbnail_size']); ?>
                                </a>

                                <div class="tutor-course-loop-header-meta">
                                    <?php
                                    $course_id     = get_the_ID();
                                    $is_wishlisted = tutor_utils()->is_wishlisted( $course_id );
                                    $has_wish_list = '';
                                    if ( $is_wishlisted ) {
                                        $has_wish_list = 'has-wish-listed';
                                    }
                                    $icon_class = 'tutor-icon-fav-line-filled';
                                    if ( $is_wishlisted ) {
                                        $icon_class = 'tutor-icon-fav-full-filled';
                                    }
                                    $action_class = '';
                                    if ( is_user_logged_in() ) {
                                        $action_class = apply_filters( 'tutor_wishlist_btn_class', 'tutor-course-wishlist-btn' );
                                    } else {
                                        $action_class = apply_filters( 'tutor_popup_login_class', 'cart-required-login' );
                                    }
                                    if ( 'yes' === $settings['course_list_difficulty_settings'] ) {
                                        echo '<span class="tutor-course-loop-level">' . get_tutor_course_level() . '</span>';
                                    }
                                    if ( 'yes' === $settings['course_list_wishlist_settings'] ) {
                                        ?>
                                        <span class="tutor-course-wishlist tutor-course-listing-item-head save-bookmark-btn"><a href="javascript:;" class="<?php echo esc_attr( $icon_class . ' ' . $action_class . ' ' .  $has_wish_list );?>" data-course-id="<?php echo esc_attr( $course_id );?>"></a> </span>
                                        <?php
                                    }
                                    ?>
                                </div>

                           </div>
                           <?php endif; ?>

                           <div class="course__content p-relative">
                              <?php if ('yes' === $settings['tp_prcie_show']): ?>
                              <div class="course__price">
                                <span>
                                    <?php
                                        $course_id = get_the_ID();
                                        $default_price = apply_filters('tutor-loop-default-price', __('Free', 'micourse'));
                                        $price_html = '<span> ' . $default_price . '</span>';
                                        if (tutor_utils()->is_course_purchasable()) {

                                            $product_id = tutor_utils()->get_course_product_id($course_id);
                                            $product = wc_get_product($product_id);

                                            if ($product) {
                                                $price_html = '<span> ' . $product->get_price_html() . ' </span>';
                                            }
                                        }
                                        echo $price_html;
                                    ?>
                                </span>
                              </div>
                              <?php endif; ?>
                              <?php if ('yes' === $settings['tp_cat_show']): ?>
                              <?php if (!empty($terms)): ?>
                              <div class="course__tag">
                                <?php foreach ($terms as $term) : ?>
                                    <a href="<?php echo get_term_link($term->slug, 'course-category'); ?>"><?php echo $term->name; ?></a>
                                <?php endforeach; ?>
                              </div>
                              <?php endif; ?>
                              <?php endif; ?>

                              <h3 class="course__title">
                                 <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                              </h3>

                                <?php if (!empty($settings['content'])):
                                $content_limit = (!empty($settings['content_limit'])) ? $settings['content_limit'] : '';
                                ?>
                                <p><?php print wp_trim_words(get_the_excerpt(get_the_ID()), $content_limit, ''); ?></p>
                                <?php endif; ?>

                              <div class="course__bottom d-sm-flex align-items-center justify-content-between">
                                <?php if ('yes' === $settings['tp_author_show']): ?>
                                 <div class="course__tutor">
                                    <a href="#"><?php echo get_avatar(get_the_author_meta('ID'), 50) ?> <?php echo get_the_author_meta('display_name', get_the_author_meta('ID')); ?></a>
                                 </div>
                                 <?php endif; ?>
                                 <?php if ('yes' === $settings['tp_lessons_show']): ?>
                                 <div class="course__lesson">
                                    <a href="<?php print get_the_permalink() ?>"><svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M1 12.2V4.49999C1 1.7 1.70588 1 4.52941 1H9.47059C12.2941 1 13 1.7 13 4.49999V11.5C13 11.598 13 11.696 12.9929 11.794" stroke="#49535B" stroke-linecap="round" stroke-linejoin="round"/>
                                       <path d="M3.01176 10.0999H13V12.5498C13 13.9008 11.8918 14.9998 10.5294 14.9998H3.47059C2.10824 14.9998 1 13.9008 1 12.5498V12.0948C1 10.9959 1.90353 10.0999 3.01176 10.0999Z" stroke="#49535B" stroke-linecap="round" stroke-linejoin="round"/>
                                       <path d="M4.17647 4.5H9.82353" stroke="#49535B" stroke-linecap="round" stroke-linejoin="round"/>
                                       <path d="M4.17647 6.94995H7.70589" stroke="#49535B" stroke-linecap="round" stroke-linejoin="round"/>
                                       </svg>
                                       <?php echo $tutor_lesson_count; ?> <?php print esc_html__('lessons', 'tpcore'); ?>
                                    </a>
                                 </div>
                                 <?php endif; ?>
                              </div>
                           </div>
                        </div>
                    </div>
                     <?php endwhile; wp_reset_query(); ?>
               </div>

                <?php if($settings['tp_tutor_pagination'] == 'yes' && '-1' != $settings['posts_per_page'] ) : ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="basic-pagination">
                                <?php
                                $big = 999999999; // need an unlikely integer

                                if (get_query_var('paged')) {
                                    $paged = get_query_var('paged');
                                } else if (get_query_var('page')) {
                                    $paged = get_query_var('page');
                                } else {
                                    $paged = 1;
                                }
                                echo paginate_links( array(
                                    'base'       => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
                                    'format'     => '?paged=%#%',
                                    'current'    => $paged,
                                    'total'      => $query->max_num_pages,
                                    'type'       =>'list',
                                    'prev_text'  =>'<i class="far fa-angle-left"></i>',
                                    'next_text'  =>'<i class="far fa-angle-right"></i>',
                                    'show_all'   => false,
                                    'end_size'   => 1,
                                    'mid_size'   => 4,
                                ) );
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
         </section>


    	<?php endif; ?>

       <?php
	}

}

$widgets_manager->register( new TP_Tutor_Course() );