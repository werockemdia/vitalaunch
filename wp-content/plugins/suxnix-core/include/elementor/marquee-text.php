<?php
namespace TPCore\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Control_Media;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Suxnix Core
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class TP_Marquee_Text extends Widget_Base {

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
        return 'marquee-text';
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
        return __( 'Marquee Text', 'tpcore' );
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

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'tg_title',
            [
                'label' => esc_html__('Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('We are always ready to protect your data', 'tpcore'),
                'placeholder' => esc_html__('Type Marquee Text', 'tpcore'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'marquee_list',
            [
                'label' => esc_html__('Marquee List', 'tpcore'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'tg_title' => esc_html__('We are always ready to protect your data', 'tpcore'),
                    ],
                    [
                        'tg_title' => esc_html__('We are always ready to protect your data', 'tpcore'),
                    ],
                    [
                        'tg_title' => esc_html__('We are always ready to protect your data', 'tpcore'),
                    ],
                    [
                        'tg_title' => esc_html__('We are always ready to protect your data', 'tpcore'),
                    ]
                ],
                'title_field' => '{{{ tg_title }}}',
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

        <?php if ( $settings['tg_design_style']  == 'layout-2' ): ?>

            <!-- marquee-area -->
            <div class="marquee-area">
                <div class="marquee-wrap">

                    <?php foreach( $settings['marquee_list'] as $item ) : ?>
                        <span><?php echo tp_kses( $item['tg_title'] ); ?></span>
                    <?php endforeach; ?>

                </div>
            </div>
            <!-- marquee-area-end -->


        <?php else : ?>

            <!-- marquee-area -->
            <div class="marquee-area marquee-style-two">
                <div class="marquee-wrap">

                    <?php foreach( $settings['marquee_list'] as $item ) : ?>
                        <span><?php echo tp_kses( $item['tg_title'] ); ?></span>
                    <?php endforeach; ?>

                </div>
            </div>
            <!-- marquee-area-end -->

        <?php endif; ?>

        <?php

    }

}

$widgets_manager->register( new TP_Marquee_Text() );