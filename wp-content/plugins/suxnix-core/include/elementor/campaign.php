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
class TP_Campaign extends Widget_Base {

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
		return 'tp-campaign';
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
		return __( 'Campaign', 'tpcore' );
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
            'tp_campaign_query',
            [
                'label' => esc_html__('Campaign Query', 'tpcore'),
            ]
        );

        $post_type = 'campaign';
        $taxonomy = 'campaign_category';

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
            'tp_campaign_btn_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Button Text', 'tpcore'),
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
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
                'label' => esc_html__('Campaign - Layout', 'tpcore'),
            ]
        );
        $this->add_control(
            'tp_design_style',
            [
                'label' => esc_html__('Select Layout', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'layout-1' => esc_html__('Default with Pagination', 'tpcore'),
                    'layout-2' => esc_html__('Slider Donation', 'tpcore'),
                    'layout-3' => esc_html__('Tab Donation', 'tpcore'),
                    'layout-4' => esc_html__('Live Donation', 'tpcore'),
                ],
                'default' => 'layout-1',
            ]
        );
        $this->add_control(
            'tp_campaign_height',
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
            'tp_campaign_dots',
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
            'tp_campaign_arrow',
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
            'tp_campaign_infinite',
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
            'tp_campaign_autoplay',
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
            'tp_campaign_autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => '2500',
                'title' => esc_html__('Enter autoplay speed', 'tpcore'),
                'label_block' => true,
                'condition' => array(
                    'tp_campaign_autoplay' => 'yes',
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_campaign_filter',
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
                'name' => 'campaign_thumb_size', // // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'exclude' => ['custom'],
                // 'default' => 'tp-campaign-thumb',
            ]
        );
        $this->add_control(
            'tp_campaign_pagination',
            [
                'label' => esc_html__( 'Pagination', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => array(
                    'tp_design_style' => 'layout-1',
                ),
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



        $this->end_controls_section();

        // tp_campaign_columns_section
        $this->start_controls_section(
            'tp_campaign_columns_section',
            [
                'label' => esc_html__('Campaign - Columns', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_campaign__for_desktop',
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
            'tp_campaign__for_laptop',
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
            'tp_campaign__for_tablet',
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

        // tp_campaign_slider_columns_section
		$this->start_controls_section(
            'tp_campaign_slider_columns_section',
            [
                'label' => esc_html__('Campaign - Columns for Carousel', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_campaign_slider_for_xl_desktop',
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
            'tp_campaign_slider_for_desktop',
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
            'tp_campaign_slider_for_laptop',
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
            'tp_campaign_slider_for_tablet',
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
            'tp_campaign_slider_for_mobile',
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
            'tp_campaign_slider_for_xs_mobile',
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
            'post_type' => 'campaign',
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
                    'taxonomy'	=> 'campaign_category',
                    'field'	 	=> 'slug',
                    'terms'		=> $exclude_category_list_value,
                    'operator'	=> 'NOT IN'
                )
            );

            // Include the correct cats in tax_query
            if ( !empty($settings['category'])) {
                $args['tax_query']['relation'] = 'AND';
                $args['tax_query'][] = array(
                    'taxonomy'	=> 'campaign_category',
                    'field'		=> 'slug',
                    'terms'		=> $category_list_value,
                    'operator'	=> 'IN'
                );
            }

        } else {
            // Include the cats from $cat_slugs in tax_query
            if (!empty($settings['category'])) {
                $args['tax_query'][] = [
                    'taxonomy' => 'campaign_category',
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
            'arrows' => ('yes' === $settings['tp_campaign_arrow']),
            'dots' => ('yes' === $settings['tp_campaign_dots']),
            'autoplay' => ('yes' === $settings['tp_campaign_autoplay']),
            'autoplay_speed' => absint($settings['tp_campaign_autoplay_speed']),
            'infinite' => ('yes' === $settings['tp_campaign_infinite']),
            'for_xl_desktop' => absint($settings['tp_campaign_slider_for_xl_desktop']),
            'slidesToShow' => absint($settings['tp_campaign_slider_for_desktop']),
            'for_laptop' => absint($settings['tp_campaign_slider_for_laptop']),
            'for_tablet' => absint($settings['tp_campaign_slider_for_tablet']),
            'for_mobile' => absint($settings['tp_campaign_slider_for_mobile']),
            'for_xs_mobile' => absint($settings['tp_campaign_slider_for_xs_mobile']),
        ];
        $this->add_render_attribute('tp-carousel-campaign-data', 'data-settings', wp_json_encode($carousel_args));

        ?>

        <?php if ( $settings['tp_design_style']  == 'layout-2' ):
            $this->add_render_attribute('title_args', 'class', 'sectionTitle__big tp-el-title');
        ?>
        <div class="featureArea__main cc-slide-wrap">
          <div class="container">
            <div class="row align-items-center">
              <div class="col-lg-6">
                <?php if ( !empty($settings['tp_section_title_show']) ) : ?>
                <!-- Section Heading/Title -->
                <div class="sectionTitle tp-sec-box mb-65">
                    <?php if ( !empty($settings['tp_sub_title']) ) : ?>
                    <span class="sectionTitle__small">
                      <i class="fa-solid fa-heart btn__icon"></i>
                      <?php echo tp_kses( $settings['tp_sub_title'] ); ?>
                    </span>
                    <?php endif; ?>
                    <?php
                        if ( !empty($settings['tp_title' ]) ) :
                            printf( '<%1$s %2$s>%3$s</%1$s>',
                                tag_escape( $settings['tp_title_tag'] ),
                                $this->get_render_attribute_string( 'title_args' ),
                                tp_kses( $settings['tp_title' ] )
                                );
                        endif;
                    ?>
                    <?php if ( !empty($settings['tp_desctiption']) ) : ?>
                    <p><?php echo tp_kses( $settings['tp_desctiption'] ); ?></p>
                    <?php endif; ?>
                </div>
                <!-- Section Heading/Title End -->
                <?php endif; ?>
              </div>
              <div class="col-lg-6">
                <div class="sliderNav sliderNav--style1 mb-65">
                  <span class="sliderNav__btn btn-prev">
                  <svg width="15" height="10" viewBox="0 0 15 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M5.75 9.40625L6.375 8.78125C6.5 8.625 6.5 8.40625 6.34375 8.25L3.84375 5.8125H14.625C14.8125 5.8125 15 5.65625 15 5.4375V4.5625C15 4.375 14.8125 4.1875 14.625 4.1875H3.84375L6.34375 1.78125C6.5 1.625 6.5 1.40625 6.375 1.25L5.75 0.625C5.59375 0.5 5.375 0.5 5.21875 0.625L1.09375 4.75C0.96875 4.90625 0.96875 5.125 1.09375 5.28125L5.21875 9.40625C5.375 9.53125 5.59375 9.53125 5.75 9.40625Z"
                      fill="white" />
                  </svg>
                </span>
                  <span class="sliderNav__btn btn-next">
                  <svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M9.21875 0.625L8.59375 1.25C8.46875 1.40625 8.46875 1.625 8.625 1.78125L11.125 4.1875H0.375C0.15625 4.1875 0 4.375 0 4.5625V5.4375C0 5.65625 0.15625 5.8125 0.375 5.8125H11.125L8.625 8.25C8.46875 8.40625 8.46875 8.625 8.59375 8.78125L9.21875 9.40625C9.375 9.53125 9.59375 9.53125 9.75 9.40625L13.875 5.28125C14 5.125 14 4.90625 13.875 4.75L9.75 0.625C9.59375 0.5 9.375 0.5 9.21875 0.625Z"
                      fill="white" />
                  </svg>
                </span>
                </div>
              </div>
            </div>
            <div class="cc-sliderStyle1 swiper-container">
              <div class="swiper-wrapper">
                <?php
                    while ($query->have_posts()) : $query->the_post();
                    $campaign_id   = get_the_ID();
                    $campaign_info = charitable_get_campaign($campaign_id); // campaign info

                    $campaign_title = $campaign_info->post_title; // title
                    $campaign_content = $campaign_info->post_content; // content
                    $campaign_description = $campaign_info->description; // description
                    $campaign_post_page_link = get_post_permalink($campaign_info->ID); // url
                    $campaign_image_url = get_the_post_thumbnail_url($campaign_info->ID, 'loveicon-chariti-1'); // thumbnail
                    $campaign_currency_helper = charitable_get_currency_helper();
                    $campaign_donated_amount = $campaign_currency_helper->get_monetary_amount($campaign_info->get_donated_amount());
                    $campaign_goal = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal());
                    $campaign_due = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal() - $campaign_info->get_donated_amount());
                    $campaign_percent_unround = $campaign_info->get_percent_donated_raw();
                    $campaign_percent = round($campaign_percent_unround);
                    $campaign_categories = $campaign_info->get('categories', true);

                    $casue_due = (int)$campaign_goal - (int)$campaign_donated_amount;
                ?>
                <div class="swiper-slide">
                  <div class="featureBlock">
                    <div class="featureBlock__wrap">
                      <div class="featureBlock__thumb">
                            <a class="featureBlock__thumb__link" href="<?php the_permalink(); ?>">
                              <?php the_post_thumbnail(); ?>
                            </a>

                            <div class="featureBlock__hashLink">
                              <span class="featureBlock__hashLink__text">#<?php echo esc_html($campaign_categories); ?></span>
                            </div>
                      </div>
                      <div class="featureBlock__content">
                        <h3 class="featureBlock__heading">
                          <a class="featureBlock__heading__link" href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                          </a>
                        </h3>
                        <?php if (!empty($settings['content'])):
                            $content_limit = (!empty($settings['content_limit'])) ? $settings['content_limit'] : '';
                            ?>
                            <p class="featureBlock__text"><?php print wp_trim_words(get_the_excerpt(get_the_ID()), $content_limit, ''); ?></p>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="featureBlock__donation">
                      <div class="featureBlock__donation__progress">
                        <div class="featureBlock__donation__bar">
                          <span class="featureBlock__donation__text skill-bar" data-width="<?php echo $campaign_percent; ?>%"><?php echo $campaign_percent; ?>%</span>
                          <div class="featureBlock__donation__line">
                            <span class="skill-bars">
                            <span class="skill-bars__line skill-bar" data-width="<?php echo $campaign_percent; ?>%"></span>
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="featureBlock__eqn">
                        <div class="featureBlock__eqn__block">
                          <span class="featureBlock__eqn__title"><?php echo esc_html__('our goal','tpcore'); ?></span>
                          <span class="featureBlock__eqn__price"><?php echo $campaign_goal; ?></span>
                        </div>
                        <div class="featureBlock__eqn__block">
                          <span class="featureBlock__eqn__title"><?php echo esc_html__('Raised','tpcore'); ?></span>
                          <span class="featureBlock__eqn__price"><?php echo $campaign_donated_amount; ?></span>
                        </div>
                        <div class="featureBlock__eqn__block">
                          <span class="featureBlock__eqn__title"><?php echo esc_html__('to go','tpcore'); ?></span>
                          <span class="featureBlock__eqn__price"><?php echo $campaign_due; ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endwhile; wp_reset_query(); ?>
              </div>
            </div>
          </div>
        </div>

        <?php elseif ($settings['tp_design_style'] === 'layout-3'):
            $this->add_render_attribute('title_args', 'class', 'sectionTitle__big tp-el-title');
        ?>
        <div class="about position-relative pt-125 pb-130">
          <div class="container">
            <div class="row">
              <div class="col-12">
                <?php if ( !empty($settings['tp_section_title_show']) ) : ?>
                <!-- Section Heading/Title -->
                <div class="sectionTitle tp-sec-box mb-65">
                    <?php if ( !empty($settings['tp_sub_title']) ) : ?>
                    <span class="sectionTitle__small d-block">
                      <i class="fa-solid fa-heart btn__icon"></i>
                      <?php echo tp_kses( $settings['tp_sub_title'] ); ?>
                    </span>
                    <?php endif; ?>
                    <?php
                        if ( !empty($settings['tp_title' ]) ) :
                            printf( '<%1$s %2$s>%3$s</%1$s>',
                                tag_escape( $settings['tp_title_tag'] ),
                                $this->get_render_attribute_string( 'title_args' ),
                                tp_kses( $settings['tp_title' ] )
                                );
                        endif;
                    ?>
                    <?php if ( !empty($settings['tp_desctiption']) ) : ?>
                    <p><?php echo tp_kses( $settings['tp_desctiption'] ); ?></p>
                    <?php endif; ?>
                </div>
                <!-- Section Heading/Title End -->
                <?php endif; ?>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="featureTab featureTab--style2">
                  <?php if( count($filter_list) > 0 ) : ?>
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <?php foreach ( $filter_list as $key => $list ):
                        $active = ($key == 0) ? 'active' : '';
                    ?>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link <?php echo esc_attr($active); ?>" id="all-tab-<?php echo esc_attr( $key ); ?>" data-bs-toggle="tab" data-bs-target="#all-<?php echo esc_attr( $key ); ?>" type="button" role="tab" aria-controls="all-<?php echo esc_attr( $key ); ?>" aria-selected="true"><?php echo esc_html( $list ); ?></button>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                  <?php endif; ?>
                  <div class="tab-content pt-55" id="myTabContent">
                    <?php
                        global $post;
                        foreach ($filter_list as $key => $list):
                        $active_tab = ($key == 0) ? 'active show' : '';
                    ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_tab); ?>" id="all-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="all-tab-<?php echo esc_attr( $key ); ?>">
                      <div class="row gx-3">
                        <?php
                            $post_args = [
                                'post_status' => 'publish',
                                'post_type' => 'campaign',
                                'posts_per_page' => $posts_per_page,
                                'campaign_category' => $list,
                            ];
                            $posts = get_posts($post_args);
                            foreach ($posts as $post):
                            $campaign_id   = get_the_ID();
                            $campaign_info = charitable_get_campaign($campaign_id); // campaign info

                            $campaign_title = $campaign_info->post_title; // title
                            $campaign_content = $campaign_info->post_content; // content
                            $campaign_description = $campaign_info->description; // description
                            $campaign_post_page_link = get_post_permalink($campaign_info->ID); // url
                            $campaign_image_url = get_the_post_thumbnail_url($campaign_info->ID, 'loveicon-chariti-1'); // thumbnail
                            $campaign_currency_helper = charitable_get_currency_helper();
                            $campaign_donated_amount = $campaign_currency_helper->get_monetary_amount($campaign_info->get_donated_amount());
                            $campaign_goal = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal());
                            $campaign_due = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal() - $campaign_info->get_donated_amount());
                            $campaign_percent_unround = $campaign_info->get_percent_donated_raw();
                            $campaign_percent = round($campaign_percent_unround);
                            $campaign_categories = $campaign_info->get('categories', true);

                            $categories = get_the_terms( get_the_id(), 'campaign_category' );

                            $casue_due = (int)$campaign_goal - (int)$campaign_donated_amount;
                        ?>
                        <div class="col-lg-4 col-sm-6 mb-35">
                          <div class="featureBlock">
                            <div class="featureBlock__wrap">
                              <div class="featureBlock__thumb">
                                <a class="featureBlock__thumb__link" href="<?php the_permalink(); ?>">
                                  <?php the_post_thumbnail(); ?>
                                </a>
                                <div class="featureBlock__hashLink">
                                  <span class="featureBlock__hashLink__text">#<?php echo esc_html($campaign_categories); ?></span>
                                </div>
                              </div>
                              <div class="featureBlock__content">
                                <h3 class="featureBlock__heading">
                                  <a class="featureBlock__heading__link" href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                  </a>
                                </h3>
                                <?php if (!empty($settings['content'])):
                                    $content_limit = (!empty($settings['content_limit'])) ? $settings['content_limit'] : '';
                                    ?>
                                    <p class="featureBlock__text"><?php print wp_trim_words(get_the_excerpt(get_the_ID()), $content_limit, ''); ?></p>
                                <?php endif; ?>
                              </div>
                            </div>
                            <div class="featureBlock__donation">
                              <div class="featureBlock__donation__progress">
                                <div class="featureBlock__donation__bar">
                                  <span class="featureBlock__donation__text skill-bar" data-width="<?php echo $campaign_percent; ?>%"><?php echo $campaign_percent; ?>%</span>
                                  <div class="featureBlock__donation__line">
                                    <span class="skill-bars">
                                    <span class="skill-bars__line skill-bar" data-width="<?php echo $campaign_percent; ?>%"></span>
                                    </span>
                                  </div>
                                </div>
                              </div>
                              <div class="featureBlock__eqn">
                                <div class="featureBlock__eqn__block">
                                  <span class="featureBlock__eqn__title"><?php echo esc_html__('our goal','tpcore'); ?></span>
                                  <span class="featureBlock__eqn__price"><?php echo $campaign_goal; ?></span>
                                </div>
                                <div class="featureBlock__eqn__block">
                                  <span class="featureBlock__eqn__title"><?php echo esc_html__('Raised','tpcore'); ?></span>
                                  <span class="featureBlock__eqn__price"><?php echo $campaign_donated_amount; ?></span>
                                </div>
                                <div class="featureBlock__eqn__block">
                                  <span class="featureBlock__eqn__title"><?php echo esc_html__('to go','tpcore'); ?></span>
                                  <span class="featureBlock__eqn__price"><?php echo $campaign_due; ?></span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php endforeach;
                            wp_reset_query();
                        ?>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


        <?php elseif ($settings['tp_design_style'] === 'layout-4'): ?>

        <section class="donation pb-130">
          <div class="container">
            <div class="row">
              <div class="col-lg-11 mx-auto">
                <?php
                    while ($query->have_posts()) : $query->the_post();
                    $campaign_id   = get_the_ID();
                    $campaign_info = charitable_get_campaign($campaign_id); // campaign info

                    $campaign_title = $campaign_info->post_title; // title
                    $campaign_content = $campaign_info->post_content; // content
                    $campaign_description = $campaign_info->description; // description
                    $campaign_post_page_link = get_post_permalink($campaign_info->ID); // url
                    $campaign_image_url = get_the_post_thumbnail_url($campaign_info->ID, 'loveicon-chariti-1'); // thumbnail
                    $campaign_currency_helper = charitable_get_currency_helper();
                    $campaign_donated_amount = $campaign_currency_helper->get_monetary_amount($campaign_info->get_donated_amount());
                    $campaign_goal = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal());
                    $campaign_due = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal() - $campaign_info->get_donated_amount());
                    $campaign_percent_unround = $campaign_info->get_percent_donated_raw();
                    $campaign_percent = round($campaign_percent_unround);
                    $campaign_categories = $campaign_info->get('categories', true);

                    $casue_due = (int)$campaign_goal - (int)$campaign_donated_amount;
                ?>
                <div class="liveDonation">
                  <div class="liveDonation__wrapper">
                    <div class="row align-items-end">
                      <div class="col-lg-8">
                        <div class="liveDonationTitle">
                          <span class="liveDonationTitle__small"><span></span><?php echo esc_html__('LIve Donation'); ?></span>
                          <h2 class="liveDonationTitle__heading"><?php the_title(); ?></h2>
                        </div>
                      </div>
                      <?php if (!empty($settings['tp_btn_text'])) : ?>
                      <div class="col-lg-4">
                        <div class="liveDonation__button">
                          <a class="btn btn--styleOne btn--secondary it-btn" href="<?php the_permalink(); ?>">
                            <span class="btn__text"><?php echo $settings['tp_btn_text']; ?></span>
                            <i class="fa-solid fa-heart btn__icon"></i></a>
                        </div>
                      </div>
                      <?php endif; ?>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="featureBlock__donation">
                          <div class="featureBlock__donation__progress">
                            <div class="featureBlock__donation__bar">
                              <span class="featureBlock__donation__text skill-bar" data-width="<?php echo $campaign_percent; ?>%"
                              style="width: <?php echo $campaign_percent; ?>%;"><?php echo $campaign_percent; ?>%</span>
                              <div class="featureBlock__donation__line">
                                <span class="skill-bars">
                                <span class="skill-bars__line skill-bar" data-width="<?php echo $campaign_percent; ?>%" style="width: <?php echo $campaign_percent; ?>%;"></span>
                                </span>
                              </div>
                            </div>
                          </div>

                          <div class="featureBlock__eqn">
                            <div class="featureBlock__eqn__block">
                              <span class="featureBlock__eqn__title"><?php echo esc_html__('our goal','tpcore'); ?></span>
                              <span class="featureBlock__eqn__price"><?php echo $campaign_goal; ?></span>
                            </div>
                            <div class="featureBlock__eqn__block">
                              <span class="featureBlock__eqn__title"><?php echo esc_html__('Raised','tpcore'); ?></span>
                              <span class="featureBlock__eqn__price"><?php echo $campaign_donated_amount; ?></span>
                            </div>
                            <div class="featureBlock__eqn__block">
                              <span class="featureBlock__eqn__title"><?php echo esc_html__('to go','tpcore'); ?></span>
                              <span class="featureBlock__eqn__price"><?php echo $campaign_due; ?></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endwhile; wp_reset_query(); ?>
              </div>
            </div>
          </div>
        </section>

        <?php else: ?>

        <section class="campaign__area">
            <div class="container">
                 <div class="row">
                   <div class="col-lg-12">
                        <?php if ( !empty($settings['tp_section_title_show']) ) : ?>
                        <!-- Section Heading/Title -->
                        <div class="sectionTitle tp-sec-box mb-65">
                            <?php if ( !empty($settings['tp_sub_title']) ) : ?>
                            <span class="sectionTitle__small">
                              <i class="fa-solid fa-heart btn__icon"></i>
                              <?php echo tp_kses( $settings['tp_sub_title'] ); ?>
                            </span>
                            <?php endif; ?>
                            <?php
                                if ( !empty($settings['tp_title' ]) ) :
                                    printf( '<%1$s %2$s>%3$s</%1$s>',
                                        tag_escape( $settings['tp_title_tag'] ),
                                        $this->get_render_attribute_string( 'title_args' ),
                                        tp_kses( $settings['tp_title' ] )
                                        );
                                endif;
                            ?>
                            <?php if ( !empty($settings['tp_desctiption']) ) : ?>
                            <p><?php echo tp_kses( $settings['tp_desctiption'] ); ?></p>
                            <?php endif; ?>
                        </div>
                        <!-- Section Heading/Title End -->
                        <?php endif; ?>
                    </div>
                 </div>
                 <div class="row gx-3">
                    <?php
                        while ($query->have_posts()) : $query->the_post();
                        $campaign_id   = get_the_ID();
                        $campaign_info = charitable_get_campaign($campaign_id); // campaign info

                        $campaign_title = $campaign_info->post_title; // title
                        $campaign_content = $campaign_info->post_content; // content
                        $campaign_description = $campaign_info->description; // description
                        $campaign_post_page_link = get_post_permalink($campaign_info->ID); // url
                        $campaign_image_url = get_the_post_thumbnail_url($campaign_info->ID, 'loveicon-chariti-1'); // thumbnail
                        $campaign_currency_helper = charitable_get_currency_helper();
                        $campaign_donated_amount = $campaign_currency_helper->get_monetary_amount($campaign_info->get_donated_amount());
                        $campaign_goal = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal());
                        $campaign_due = $campaign_currency_helper->get_monetary_amount($campaign_info->get_goal() - $campaign_info->get_donated_amount());
                        $campaign_percent_unround = $campaign_info->get_percent_donated_raw();
                        $campaign_percent = round($campaign_percent_unround);
                        $campaign_categories = $campaign_info->get('categories', true);

                        $categories = get_the_terms( get_the_id(), 'campaign_category' );

                        $casue_due = (int)$campaign_goal - (int)$campaign_donated_amount;
                    ?>
                    <div class="col-lg-4 col-sm-6 mb-35">
                      <div class="featureBlock">
                        <div class="featureBlock__wrap">
                          <div class="featureBlock__thumb">
                            <a class="featureBlock__thumb__link" href="<?php the_permalink(); ?>">
                              <?php the_post_thumbnail(); ?>
                            </a>
                            <div class="featureBlock__hashLink">
                              <span class="featureBlock__hashLink__text">#<?php echo esc_html($campaign_categories); ?></span>
                            </div>
                          </div>
                          <div class="featureBlock__content">
                            <h3 class="featureBlock__heading">
                              <a class="featureBlock__heading__link" href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                              </a>
                            </h3>
                            <?php if (!empty($settings['content'])):
                                $content_limit = (!empty($settings['content_limit'])) ? $settings['content_limit'] : '';
                                ?>
                                <p class="featureBlock__text"><?php print wp_trim_words(get_the_excerpt(get_the_ID()), $content_limit, ''); ?></p>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="featureBlock__donation">
                          <div class="featureBlock__donation__progress">
                            <div class="featureBlock__donation__bar">
                              <span class="featureBlock__donation__text skill-bar" data-width="<?php echo $campaign_percent; ?>%"><?php echo $campaign_percent; ?>%</span>
                              <div class="featureBlock__donation__line">
                                <span class="skill-bars">
                                <span class="skill-bars__line skill-bar" data-width="<?php echo $campaign_percent; ?>%"></span>
                                </span>
                              </div>
                            </div>
                          </div>
                          <div class="featureBlock__eqn">
                            <div class="featureBlock__eqn__block">
                              <span class="featureBlock__eqn__title"><?php echo esc_html__('our goal','tpcore'); ?></span>
                              <span class="featureBlock__eqn__price"><?php echo $campaign_goal; ?></span>
                            </div>
                            <div class="featureBlock__eqn__block">
                              <span class="featureBlock__eqn__title"><?php echo esc_html__('Raised','tpcore'); ?></span>
                              <span class="featureBlock__eqn__price"><?php echo $campaign_donated_amount; ?></span>
                            </div>
                            <div class="featureBlock__eqn__block">
                              <span class="featureBlock__eqn__title"><?php echo esc_html__('to go','tpcore'); ?></span>
                              <span class="featureBlock__eqn__price"><?php echo $campaign_due; ?></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endwhile; wp_reset_query(); ?>
                </div>
                <?php if($settings['tp_campaign_pagination'] == 'yes' && '-1' != $settings['posts_per_page'] ){ ?>
                        <div class="col-lg-12">
                            <div class="basic-pagination mb-40 pagination justify-content-center">
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
                                    'prev_text'  =>'<i class="fas fa-angle-left"></i>',
                                    'next_text'  =>'<i class="fas fa-angle-right"></i>',
                                    'show_all'   => false,
                                    'end_size'   => 1,
                                    'mid_size'   => 4,
                                ) );
                                ?>
                            </div>
                        </div>
                    <?php } ?>
            </div>
        </section>

    	<?php endif; ?>

       <?php
	}

}

$widgets_manager->register( new TP_Campaign() );