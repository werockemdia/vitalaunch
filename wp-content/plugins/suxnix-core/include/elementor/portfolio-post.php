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
class TP_Portfolio_Post extends Widget_Base {

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
		return 'portfolio';
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
		return __( 'Portfolio Post', 'tpcore' );
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



		$this->start_controls_section(
            'tp_portfolio_query',
            [
                'label' => esc_html__('Portfolio Query', 'tpcore'),
            ]
        );

        $post_type = 'portfolio';
        $taxonomy = 'portfolio-cat';

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
            'tp_portfolio_btn_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Button Text', 'tpcore'),
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
            ]
        );


        $this->end_controls_section();


        // layout Panel
        $this->start_controls_section(
            'tp_portfolio',
            [
                'label' => esc_html__('Portfolio - Layout', 'tpcore'),
            ]
        );
        $this->add_control(
            'tp_design_style',
            [
                'label' => esc_html__('Select Layout', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'layout-1' => esc_html__('Default', 'tpcore'),
                    'layout-2' => esc_html__('Carousel', 'tpcore'),
                    'layout-3' => esc_html__('Carousel Full Width', 'tpcore'),
                ],
                'default' => 'layout-1',
            ]
        );
        $this->add_control(
            'tp_portfolio_height',
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
            'tp_portfolio_dots',
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
            'tp_portfolio_arrow',
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
            'tp_portfolio_infinite',
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
            'tp_portfolio_autoplay',
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
            'tp_portfolio_autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => '2500',
                'title' => esc_html__('Enter autoplay speed', 'tpcore'),
                'label_block' => true,
                'condition' => array(
                    'tp_portfolio_autoplay' => 'yes',
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_portfolio_filter',
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
                'name' => 'portfolio_thumb_size', // // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'exclude' => ['custom'],
                // 'default' => 'tp-portfolio-thumb',
            ]
        );
        $this->add_control(
            'tp_portfolio_pagination',
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
            'tp_portfolio_view_more_item_button',
            [
                'label' => esc_html__( 'View More Item Button', 'tpcore' ),
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
            'tp_portfolio_view_more_item_button_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => 'View More Project',
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
                'condition' => [
                    'tp_portfolio_view_more_item_button' => 'yes'
                ],
            ]
        );
        $this->add_control(
            'tp_portfolio_view_more_item_button_link_type',
            [
                'label' => esc_html__('Button Link Type', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => 'Custom Link',
                    '2' => 'Internal Page',
                ],
                'default' => '1',
                'label_block' => true,
                'condition' => [
                    'tp_portfolio_view_more_item_button' => 'yes'
                ],
            ]
        );
        $this->add_control(
            'tp_portfolio_view_more_item_button_link',
            [
                'label' => esc_html__('Button link', 'tpcore'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__('https://your-link.com', 'tpcore'),
                'show_external' => false,
                'default' => [
                    'url' => '#',
                    'is_external' => false,
                    'nofollow' => false,
                ],
                'condition' => [
                    'tp_portfolio_view_more_item_button_link_type' => '1',
                    'tp_portfolio_view_more_item_button' => 'yes'
                ],
                'label_block' => true,
            ]
        );
        $this->add_control(
            'tp_portfolio_view_more_item_button_page_link',
            [
                'label' => esc_html__('Select Button Page', 'tpcore'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'options' => tp_get_all_pages(),
                'condition' => [
                    'tp_portfolio_view_more_item_button_link_type' => '2',
                    'tp_portfolio_view_more_item_button' => 'yes'
                ]
            ]
        );
        $this->end_controls_section();

        // tp_portfolio_columns_section
        $this->start_controls_section(
            'tp_portfolio_columns_section',
            [
                'label' => esc_html__('Portfolio - Columns', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_portfolio__for_desktop',
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
            'tp_portfolio__for_laptop',
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
            'tp_portfolio__for_tablet',
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

        // tp_portfolio_slider_columns_section
		$this->start_controls_section(
            'tp_portfolio_slider_columns_section',
            [
                'label' => esc_html__('Portfolio - Columns for Carousel', 'tpcore'),
            ]
        );

        $this->add_control(
            'tp_portfolio_slider_for_xl_desktop',
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
            'tp_portfolio_slider_for_desktop',
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
            'tp_portfolio_slider_for_laptop',
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
            'tp_portfolio_slider_for_tablet',
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
            'tp_portfolio_slider_for_mobile',
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
            'tp_portfolio_slider_for_xs_mobile',
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
            'post_type' => 'portfolio',
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
                    'taxonomy'	=> 'portfolio-cat',
                    'field'	 	=> 'slug',
                    'terms'		=> $exclude_category_list_value,
                    'operator'	=> 'NOT IN'
                )
            );

            // Include the correct cats in tax_query
            if ( !empty($settings['category'])) {
                $args['tax_query']['relation'] = 'AND';
                $args['tax_query'][] = array(
                    'taxonomy'	=> 'portfolio-cat',
                    'field'		=> 'slug',
                    'terms'		=> $category_list_value,
                    'operator'	=> 'IN'
                );
            }

        } else {
            // Include the cats from $cat_slugs in tax_query
            if (!empty($settings['category'])) {
                $args['tax_query'][] = [
                    'taxonomy' => 'portfolio-cat',
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
            'arrows' => ('yes' === $settings['tp_portfolio_arrow']),
            'dots' => ('yes' === $settings['tp_portfolio_dots']),
            'autoplay' => ('yes' === $settings['tp_portfolio_autoplay']),
            'autoplay_speed' => absint($settings['tp_portfolio_autoplay_speed']),
            'infinite' => ('yes' === $settings['tp_portfolio_infinite']),
            'for_xl_desktop' => absint($settings['tp_portfolio_slider_for_xl_desktop']),
            'slidesToShow' => absint($settings['tp_portfolio_slider_for_desktop']),
            'for_laptop' => absint($settings['tp_portfolio_slider_for_laptop']),
            'for_tablet' => absint($settings['tp_portfolio_slider_for_tablet']),
            'for_mobile' => absint($settings['tp_portfolio_slider_for_mobile']),
            'for_xs_mobile' => absint($settings['tp_portfolio_slider_for_xs_mobile']),
        ];
        $this->add_render_attribute('tp-carousel-portfolio-data', 'data-settings', wp_json_encode($carousel_args));

        $i = 1;



        ?>

        <?php if ( $settings['tp_design_style']  == 'layout-2' ): ?>
        <div class="tp-prjects-area pt-120 pb-120 style-2">
            <div class="container">
                <?php if ($query->have_posts()) : ?>
				<div class="rows">
					<div class="tp-demo-active swiper-container" <?php echo $this->get_render_attribute_string('tp-carousel-portfolio-data'); ?>>
						<div class="swiper-wrapper">
							<?php while ($query->have_posts()) :
		                    $query->the_post();
		                    global $post;
		                    $terms = get_the_terms($post->ID, 'portfolio-cat');
							$item_classes = '';
		                    $item_cat_names = '';
		                    $item_cats = get_the_terms( $post->ID, 'portfolio-cat' );
		                    if( !empty($item_cats) ):
		                        $count = count($item_cats) - 1;
		                        foreach($item_cats as $key => $item_cat) {
		                            $item_classes .= $item_cat->slug . ' ';
		                            $item_cat_names .= ( $count > $key ) ? $item_cat->name  . ', ' : $item_cat->name;
		                        }
		                    endif;
		                    ?>
		                    <div class="gallery-items swiper-slide">
		                        <div class="tp-project z-index mb-30">
		                            <div class="tp-project-img">
		                                <img src="<?php the_post_thumbnail_url('large'); ?>" class="img-fluid" alt="img not found">
		                            </div>
		                            <div class="tp-project-text">
		                                <div class="tp-project-text-content">
		                                    <span class="tp-project-subtitle"><?php echo esc_html($item_cat_names); ?></span>
		                                    <h4 class="tp-project-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?></a></h4>
		                                </div>
		                                <div class="tp-project-text-icon">
		                                    <a href="<?php the_post_thumbnail_url('large'); ?>"><i class="fal fa-plus"></i></a>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		                	<?php endwhile; wp_reset_query(); ?>
	                	</div>
                	</div>
                </div>

            	<?php endif; ?>

                <!-- If we need pagination -->
                <?php if (!empty($settings['tp_portfolio_dots'])) : ?>
                <div class="swiper-pagination"></div>
                <?php endif; ?>

                <!-- If we need navigation buttons -->
                <?php if (!empty($settings['tp_portfolio_arrow'])) : ?>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <?php endif; ?>


                <div class="row d-none">
                    <div class="col-12">
                        <div class="tp-projects-btn d-flex justify-content-center">
                            <a href="#" class="theme-btn"><i class="flaticon-enter"></i> Explore More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif ($settings['tp_design_style'] === 'layout-3'): ?>
        <section class="portfolio__area">
            <div class="container">
                <?php if (!empty($settings['tp_portfolio_filter'])) : ?>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="mb-60">
                            <div class="tp-prjects-tab-menu masonary-menu d-flex justify-content-center">
                                <?php foreach ( $filter_list as $list ): ?>
                                    <?php if ( $i === 1 ): $i++; ?>
                                    <button class="active" data-filter="*"><?php echo esc_html__( 'See All','tpcore' ); ?></button>
                                    <button data-filter=".<?php echo esc_attr( $list ); ?>"><?php echo esc_html( $list ); ?></button>
                                    <?php else: ?>
                                    <button data-filter=".<?php echo esc_attr( $list ); ?>"><?php echo esc_html( $list ); ?></button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row filter-active">
                    <?php while ($query->have_posts()) :
                        $query->the_post();
                        global $post;
                        $terms = get_the_terms($post->ID, 'portfolio-cat');
                        $item_classes = '';
                        $item_cat_names = '';
                        $item_cats = get_the_terms( $post->ID, 'portfolio-cat' );
                        if( !empty($item_cats) ):
                            $count = count($item_cats) - 1;
                            foreach($item_cats as $key => $item_cat) {
                                $item_classes .= $item_cat->slug . ' ';
                                $item_cat_names .= ( $count > $key ) ? $item_cat->name  . ', ' : $item_cat->name;
                            }
                        endif;
                    ?>
                    <div class="col-lg-4 col-md-6 grid-item <?php echo $item_classes; ?>">
                        <div class="tp-project z-index mb-30">
                            <?php if ( has_post_thumbnail() ): ?>
                            <div class="tp-project-img">
                                <?php the_post_thumbnail($settings['portfolio_thumb_size_size']); ?>
                            </div>
                            <?php endif; ?>
                            <div class="tp-project-text">
                                <div class="tp-project-text-content">
                                    <span class="tp-project-subtitle"><?php echo esc_html($item_cat_names); ?></span>
                                    <h4 class="tp-project-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?></a></h4>
                                </div>
                                <div class="tp-project-text-icon">
                                    <a class="popup-image" href="<?php echo get_the_post_thumbnail_url(); ?>"><i class="fal fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile;
                    wp_reset_query(); ?>
                </div>
            </div>
        </section>


        <?php else: ?>

        <section class="portfolio_slide">
            <div class="container">
                <?php if ( !empty($settings['tp_section_title_show']) ) : ?>
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8">
                        <div class="section__title text-center">
                            <?php if ( !empty($settings['tp_sub_title']) ) : ?>
                            <span class="sub-title tp-el-subtitle"><?php echo tp_kses( $settings['tp_sub_title'] ); ?></span>
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
                    </div>
                </div>
                <?php endif; ?>
                <?php if( count($filter_list) > 0 ) { ?>
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-12">
                        <ul class="nav nav-tabs portfolio_slide__nav" id="portfolioTab" role="tablist">
                            <?php foreach ( $filter_list as $key => $list ):
                                $active = ($key == 0) ? 'active' : '';
                            ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo esc_attr($active); ?>" id="all-tab-<?php echo esc_attr( $key ); ?>" data-bs-toggle="tab" data-bs-target="#all-<?php echo esc_attr( $key ); ?>" type="button" role="tab" aria-controls="all-<?php echo esc_attr( $key ); ?>" aria-selected="true">
                                    <?php echo esc_html( $list ); ?>
                                </button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php }?>
            </div>
            <?php if( count($filter_list) > 0 ) { ?>
            <div class="tab-content" id="portfolioTabContent">
                <?php
                    global $post;
                    foreach ($filter_list as $key => $list):
                    $active_tab = ($key == 0) ? 'active show' : '';
                ?>
                <div class="tab-pane  <?php echo esc_attr($active_tab); ?>" id="all-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="all-tab-<?php echo esc_attr( $key ); ?>">
                    <div class="container">
                        <div class="row gx-0 justify-content-center">
                            <div class="col">
                                <div class="portfolio_slide__active portfolio__active">
                                    <?php
                                    $post_args = [
                                        'post_status' => 'publish',
                                        'post_type' => 'portfolio',
                                        'posts_per_page' => $posts_per_page,
                                        'portfolio-cat' => $list,
                                    ];
                                    $posts = get_posts($post_args);
                                    foreach ($posts as $post):
                                    ?>
                                    <div class="portfolio_slide__item">
                                        <div class="portfolio_slide__thumb">
                                            <?php the_post_thumbnail($post->ID, $settings['thumbnail_size']); ?>
                                        </div>
                                        <div class="portfolio_slide__overlay__content">
                                            <span><?php echo esc_html($list); ?></span>
                                            <h4 class="title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?></a></h4>
                                            <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="link"><?php echo tp_kses( $settings['tp_portfolio_btn_text'] ); ?></a>
                                        </div>
                                    </div>
                                    <?php endforeach;
                                        wp_reset_query();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php } else { ?>
            <div class="container">
                <div class="row gx-0 justify-content-center">
                    <div class="col">
                        <div class="portfolio_slide__active portfolio__active">
                            <?php
                            global $post;
                            $post_args = [
                                'post_status' => 'publish',
                                'post_type' => 'portfolio',
                                'posts_per_page' => $posts_per_page,
                            ];
                            $posts = get_posts($post_args);
                            foreach ($posts as $post):
                            ?>
                            <div class="portfolio_slide__item">
                                <div class="portfolio_slide__thumb">
                                    <?php the_post_thumbnail($post->ID, $settings['thumbnail_size']); ?>
                                </div>
                                <div class="portfolio_slide__overlay__content">
                                    <span>SSS</span>
                                    <h4 class="title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?></a></h4>
                                    <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="link"><?php echo tp_kses( $settings['tp_portfolio_btn_text'] ); ?></a>
                                </div>
                            </div>
                            <?php endforeach;
                                wp_reset_query();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
        </section>

    	<?php endif; ?>

       <?php
	}

}

$widgets_manager->register( new TP_Portfolio_Post() );