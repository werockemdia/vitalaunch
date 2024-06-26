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
class TP_Services extends Widget_Base {

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
        return 'services';
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
        return __( 'Services', 'tpcore' );
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
            'tp_design_style',
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

        // Service group
        $this->start_controls_section(
            'tg_services',
            [
                'label' => esc_html__('Services List', 'tpcore'),
                'description' => esc_html__( 'Control all the style settings from Style tab', 'tpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'repeater_condition',
            [
                'label' => __( 'Field condition', 'tpcore' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style_1' => __( 'Style 1', 'tpcore' ),
                    'style_2' => __( 'Style 2', 'tpcore' ),
                ],
                'default' => 'style_1',
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $repeater->add_control(
            'tg_service_icon_type',
            [
                'label' => esc_html__('Select Icon Type', 'tpcore'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'icon',
                'options' => [
                    'image' => esc_html__('Image', 'tpcore'),
                    'icon' => esc_html__('Icon', 'tpcore'),
                ],
            ]
        );

        $repeater->add_control(
            'tg_service_image',
            [
                'label' => esc_html__('Upload Icon Image', 'tpcore'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'tg_service_icon_type' => 'image'
                ]

            ]
        );

        if (tp_is_elementor_version('<', '2.6.0')) {
            $repeater->add_control(
                'tg_service_icon',
                [
                    'show_label' => false,
                    'type' => Controls_Manager::ICON,
                    'label_block' => true,
                    'default' => 'fa fa-star',
                    'condition' => [
                        'tg_service_icon_type' => 'icon'
                    ]
                ]
            );
        } else {
            $repeater->add_control(
                'tg_service_selected_icon',
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
                        'tg_service_icon_type' => 'icon'
                    ]
                ]
            );
        }

        $repeater->add_control(
            'tg_service_title', [
                'label' => esc_html__('Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'basic' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Service Title', 'tpcore'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'tg_service_description',
            [
                'label' => esc_html__('Description', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Nam libero tempore, cum soluta nobis eligendi optio cumque quo minus quod maxime placeat',
                'label_block' => true,
                'condition' => [
                    'repeater_condition' => 'style_2'
                ]
            ]
        );

        $repeater->add_control(
            'tg_services_link_switcher',
            [
                'label' => esc_html__( 'Add Services link', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'tpcore' ),
                'label_off' => esc_html__( 'No', 'tpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'tg_services_btn_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Learn More', 'tpcore'),
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
                'condition' => [
                    'tg_services_link_switcher' => 'yes',
                    'repeater_condition' => 'style_2'
                ],
            ]
        );

        $repeater->add_control(
            'tg_services_link_type',
            [
                'label' => esc_html__( 'Service Link Type', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '1' => 'Custom Link',
                    '2' => 'Internal Page',
                ],
                'default' => '1',
                'condition' => [
                    'tg_services_link_switcher' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'tg_services_link',
            [
                'label' => esc_html__( 'Service Link link', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'tpcore' ),
                'show_external' => true,
                'default' => [
                    'url' => '#',
                    'is_external' => false,
                    'nofollow' => false,
                ],
                'condition' => [
                    'tg_services_link_type' => '1',
                    'tg_services_link_switcher' => 'yes',
                ]
            ]
        );

        $repeater->add_control(
            'tg_services_page_link',
            [
                'label' => esc_html__( 'Select Service Link Page', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'options' => tp_get_all_pages(),
                'condition' => [
                    'tg_services_link_type' => '2',
                    'tg_services_link_switcher' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'tg_service_list',
            [
                'label' => esc_html__('Services - List', 'tpcore'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'tg_service_title' => esc_html__('Spyware Protection', 'tpcore'),
                    ],
                    [
                        'tg_service_title' => esc_html__('Fast Cloud Backup', 'tpcore'),
                    ],
                    [
                        'tg_service_title' => esc_html__('Database Security', 'tpcore'),
                    ],
                    [
                        'tg_service_title' => esc_html__('Transaction Security', 'tpcore'),
                    ]
                ],
                'title_field' => '{{{ tg_service_title }}}',
            ]
        );

        $this->add_responsive_control(
            'tg_service_align',
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

        // tg_btn_button_group
        $this->start_controls_section(
            'tg_btn_button_group',
            [
                'label' => esc_html__('Button', 'tpcore'),
            ]
        );

        $this->add_control(
            'tg_btn_button_show',
            [
                'label' => esc_html__( 'Show Button', 'tpcore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tpcore' ),
                'label_off' => esc_html__( 'Hide', 'tpcore' ),
                'return_value' => 'yes',
                'default' => true,
            ]
        );

        $this->add_control(
            'tg_btn_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Explore all', 'tpcore'),
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
                'condition' => [
                    'tg_btn_button_show' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'tg_btn_link_type',
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
                    'tg_btn_button_show' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'tg_btn_link',
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
                    'is_external' => true,
                    'nofollow' => true,
                    'custom_attributes' => '',
                ],
                'condition' => [
                    'tg_btn_link_type' => '1',
                    'tg_btn_button_show' => 'yes'
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tg_btn_page_link',
            [
                'label' => esc_html__('Select Button Page', 'tpcore'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'options' => tp_get_all_pages(),
                'condition' => [
                    'tg_btn_link_type' => '2',
                    'tg_btn_button_show' => 'yes'
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

        // Link
        if ('2' == $settings['tg_btn_link_type']) {
            $this->add_render_attribute('tg-button-arg', 'href', get_permalink($settings['tg_btn_page_link']));
            $this->add_render_attribute('tg-button-arg', 'target', '_self');
            $this->add_render_attribute('tg-button-arg', 'rel', 'nofollow');
            $this->add_render_attribute('tg-button-arg', 'class', 'btn');
        } else {
            if ( ! empty( $settings['tg_btn_link']['url'] ) ) {
                $this->add_link_attributes( 'tg-button-arg', $settings['tg_btn_link'] );
                $this->add_render_attribute('tg-button-arg', 'class', 'btn');
            }
        }

        ?>

        <?php if ( $settings['tp_design_style']  == 'layout-2' ):
            $this->add_render_attribute('title_args', 'class', 'title');
        ?>

         <!-- services-area -->
        <section class="services-two-area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-10">
                        <div class="services-two-items-wrap">
                            <div class="row g-0 justify-content-center">

                                <?php foreach ($settings['tg_service_list'] as $item) :
                                    // Link
                                    if ('2' == $item['tg_services_link_type']) {
                                        $link = get_permalink($item['tg_services_page_link']);
                                        $target = '_self';
                                        $rel = 'nofollow';
                                    } else {
                                        $link = !empty($item['tg_services_link']['url']) ? $item['tg_services_link']['url'] : '';
                                        $target = !empty($item['tg_services_link']['is_external']) ? '_blank' : '';
                                        $rel = !empty($item['tg_services_link']['nofollow']) ? 'nofollow' : '';
                                    }
                                ?>

                                <div class="col-md-6 col-sm-8">
                                    <div class="services-two-item">
                                        <div class="services-two-icon">

                                            <?php if($item['tg_service_icon_type'] !== 'image') : ?>

                                                <?php if (!empty($item['tg_service_icon']) || !empty($item['tg_service_selected_icon']['value'])) : ?>
                                                    <?php tp_render_icon($item, 'tg_service_icon', 'tg_service_selected_icon'); ?>
                                                <?php endif; ?>

                                            <?php else : ?>

                                                <?php if (!empty($item['tg_service_image']['url'])): ?>
                                                    <img src="<?php echo $item['tg_service_image']['url']; ?>" alt="<?php echo get_post_meta(attachment_url_to_postid($item['tg_service_image']['url']), '_wp_attachment_image_alt', true); ?>">
                                                <?php endif; ?>

                                            <?php endif; ?>

                                        </div>
                                        <div class="services-two-content">
                                            <?php if (!empty($item['tg_service_title'])) : ?>
                                            <h3 class="title">
                                                <?php if ($item['tg_services_link_switcher'] == 'yes') : ?>
                                                <a href="<?php echo esc_url($link); ?>"><?php echo tp_kses($item['tg_service_title']); ?></a>
                                                <?php else : ?>
                                                    <?php echo tp_kses($item['tg_service_title']); ?>
                                                <?php endif; ?>
                                            </h3>
                                            <?php endif; ?>

                                            <?php if (!empty($item['tg_service_description' ])): ?>
                                                <p><?php echo tp_kses($item['tg_service_description']); ?></p>
                                            <?php endif; ?>

                                            <?php if (!empty($link)) : ?>
                                                <a target="<?php echo esc_attr($target); ?>" rel="<?php echo esc_attr($rel); ?>" href="<?php echo esc_url($link); ?>" class="read-more">
                                                <?php echo tp_kses($item['tg_services_btn_text']); ?></a>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>

                                <?php endforeach; ?>

                            </div>
                        </div>

                        <?php if (!empty($settings['tg_btn_text'])) : ?>
                        <div class="services-explore-btn text-center">
                            <a <?php echo $this->get_render_attribute_string( 'tg-button-arg' ); ?>>
                                <span class="text"><?php echo $settings['tg_btn_text']; ?></span>
                                <span class="shape"></span>
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </section>
        <!-- services-area-end -->


        <?php else:
            $this->add_render_attribute('title_args', 'class', 'title');
        ?>

        <!-- services-area -->
        <section class="services-area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="services-items-wrapper">
                            <ul class="list-wrap">

                                <?php foreach ($settings['tg_service_list'] as $item) :
                                    // Link
                                    if ('2' == $item['tg_services_link_type']) {
                                        $link = get_permalink($item['tg_services_page_link']);
                                        $target = '_self';
                                        $rel = 'nofollow';
                                    } else {
                                        $link = !empty($item['tg_services_link']['url']) ? $item['tg_services_link']['url'] : '';
                                        $target = !empty($item['tg_services_link']['is_external']) ? '_blank' : '';
                                        $rel = !empty($item['tg_services_link']['nofollow']) ? 'nofollow' : '';
                                    }
                                ?>
                                <li>
                                    <a href="<?php echo esc_url($link); ?>">
                                        <div class="top-content">

                                            <?php if($item['tg_service_icon_type'] !== 'image') : ?>

                                                <?php if (!empty($item['tg_service_icon']) || !empty($item['tg_service_selected_icon']['value'])) : ?>
                                                    <?php tp_render_icon($item, 'tg_service_icon', 'tg_service_selected_icon'); ?>
                                                <?php endif; ?>

                                            <?php else : ?>

                                                <?php if (!empty($item['tg_service_image']['url'])): ?>
                                                    <img src="<?php echo $item['tg_service_image']['url']; ?>" alt="<?php echo get_post_meta(attachment_url_to_postid($item['tg_service_image']['url']), '_wp_attachment_image_alt', true); ?>">
                                                <?php endif; ?>

                                            <?php endif; ?>

                                            <?php if (!empty($item['tg_service_title' ])): ?>
                                                <span><?php echo tp_kses($item['tg_service_title']); ?></span>
                                            <?php endif; ?>

                                        </div>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </li>
                                <?php endforeach; ?>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- services-area-end -->

        <?php endif; ?>

        <?php
    }
}

$widgets_manager->register( new TP_Services() );