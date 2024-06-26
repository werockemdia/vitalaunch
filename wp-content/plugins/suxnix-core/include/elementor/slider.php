<?php
namespace TPCore\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Repeater;
use \Elementor\Utils;
use \Elementor\Control_Media;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Suxnix Core
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class TP_Main_Slider extends Widget_Base {

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
		return 'tp-slider';
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
		return __( 'Main Slider', 'tpcore' );
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

        $this->add_control(
            'hero_search_switch',
            [
                'label' => __('Hero Search Show/Hide', 'bdevs-element'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'bdevs-element'),
                'label_off' => __('Hide', 'bdevs-element'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tp_design_style' => 'layout-2',
                ],
                'style_transfer' => true,
            ]
        );

        $this->end_controls_section();


		$this->start_controls_section(
            'tp_main_slider',
            [
                'label' => esc_html__('Main Slider', 'tpcore'),
                'description' => esc_html__( 'Control all the style settings from Style tab', 'tpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

	        $repeater->add_control(
	            'tp_slider_image',
	            [
	                'label' => esc_html__('Upload Image', 'tpcore'),
	                'type' => Controls_Manager::MEDIA,
	                'default' => [
	                    'url' => Utils::get_placeholder_image_src(),
	                ]
	            ]
	        );

            $repeater->add_control(
                'tp_slider_sub_title',
                [
                    'label' => esc_html__('Sub Title', 'tpcore'),
                    'description' => tp_get_allowed_html_desc( 'basic' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => '',
                    'placeholder' => esc_html__('Type Before Heading Text', 'tpcore'),
                    'label_block' => true,
                ]
            );
            $repeater->add_control(
                'tp_slider_title',
                [
                    'label' => esc_html__('Title', 'tpcore'),
                    'description' => tp_get_allowed_html_desc( 'intermediate' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('Grow business.', 'tpcore'),
                    'placeholder' => esc_html__('Type Heading Text', 'tpcore'),
                    'label_block' => true,
                ]
            );
            $repeater->add_control(
                'tp_slider_title_tag',
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

            $repeater->add_control(
                'tp_slider_description',
                [
                    'label' => esc_html__('Description', 'tpcore'),
                    'description' => tp_get_allowed_html_desc( 'intermediate' ),
                    'type' => Controls_Manager::TEXTAREA,
                    'default' => esc_html__('There are many variations of passages of Lorem Ipsum available but the majority have suffered alteration.', 'tpcore'),
                    'placeholder' => esc_html__('Type section description here', 'tpcore'),
                ]
            );

			$repeater->add_control(
	            'tp_btn_link_switcher',
	            [
	                'label' => esc_html__( 'Add Button link', 'tpcore' ),
	                'type' => \Elementor\Controls_Manager::SWITCHER,
	                'label_on' => esc_html__( 'Yes', 'tpcore' ),
	                'label_off' => esc_html__( 'No', 'tpcore' ),
	                'return_value' => 'yes',
	                'default' => 'yes',
	                'separator' => 'before',
	            ]
	        );
	        $repeater->add_control(
	            'tp_btn_btn_text',
	            [
	                'label' => esc_html__('Button Text', 'tpcore'),
	                'type' => Controls_Manager::TEXT,
	                'default' => esc_html__('Button Text', 'tpcore'),
	                'title' => esc_html__('Enter button text', 'tpcore'),
	                'label_block' => true,
	                'condition' => [
	                    'tp_btn_link_switcher' => 'yes'
	                ],
	            ]
	        );
	        $repeater->add_control(
	            'tp_btn_link_type',
	            [
	                'label' => esc_html__( 'Button Link Type', 'tpcore' ),
	                'type' => \Elementor\Controls_Manager::SELECT,
	                'options' => [
	                    '1' => 'Custom Link',
	                    '2' => 'Internal Page',
	                ],
	                'default' => '1',
	                'condition' => [
	                    'tp_btn_link_switcher' => 'yes'
	                ]
	            ]
	        );
	        $repeater->add_control(
	            'tp_btn_link',
	            [
	                'label' => esc_html__( 'Button Link link', 'tpcore' ),
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
	                    'tp_btn_link_type' => '1',
	                    'tp_btn_link_switcher' => 'yes',
	                ]
	            ]
	        );
	        $repeater->add_control(
	            'tp_btn_page_link',
	            [
	                'label' => esc_html__( 'Select Button Link Page', 'tpcore' ),
	                'type' => \Elementor\Controls_Manager::SELECT2,
	                'label_block' => true,
	                'options' => tp_get_all_pages(),
	                'condition' => [
	                    'tp_btn_link_type' => '2',
	                    'tp_btn_link_switcher' => 'yes',
	                ]
	            ]
	        );


        $this->add_control(
            'slider_list',
            [
                'label' => esc_html__('Slider List', 'tpcore'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'tp_slider_title' => esc_html__('Grow business.', 'tpcore')
                    ],
                    [
                        'tp_slider_title' => esc_html__('Development.', 'tpcore')
                    ],
                    [
                        'tp_slider_title' => esc_html__('Marketing.', 'tpcore')
                    ],
                ],
                'title_field' => '{{{ tp_slider_title }}}',
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'exclude' => ['custom'],
                // 'default' => 'tp-portfolio-thumb',
            ]
        );
        $this->end_controls_section();


        // Style
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

		<?php if ( $settings['tp_design_style']  == 'layout-2' ): ?>
         <section class="slider__area">
            <div class="slider__active swiper-container">
               <div class="swiper-wrapper">
           		<?php foreach ($settings['slider_list'] as $item) :
        			$this->add_render_attribute('title_args', 'class', 'slider__title-3');
					$this->add_render_attribute('title_args', 'data-animation', 'fadeInUp');
					$this->add_render_attribute('title_args', 'data-delay', '.6s');

 					if ( !empty($item['tp_slider_image']['url']) ) {
                        $tp_slider_image_url = !empty($item['tp_slider_image']['id']) ? wp_get_attachment_image_url( $item['tp_slider_image']['id'], $settings['thumbnail_size']) : $item['tp_slider_image']['url'];
                        $tp_slider_image_alt = get_post_meta($item["tp_slider_image"]["id"], "_wp_attachment_image_alt", true);
                    }

					// btn Link
                    if ('2' == $item['tp_btn_link_type']) {
                        $link = get_permalink($item['tp_btn_page_link']);
                        $target = '_self';
                        $rel = 'nofollow';
                    } else {
                        $link = !empty($item['tp_btn_link']['url']) ? $item['tp_btn_link']['url'] : '';
                        $target = !empty($item['tp_btn_link']['is_external']) ? '_blank' : '';
                        $rel = !empty($item['tp_btn_link']['nofollow']) ? 'nofollow' : '';
                    }
                ?>
                <div class="slider__item swiper-slide p-relative slider__height slider__height-3 d-flex align-items-center z-index-1">
                     <div class="slider__bg slider__overlay slider__overlay-3 include-bg" data-background="<?php echo esc_url($tp_slider_image_url); ?>"></div>
                     <div class="container">
                        <div class="row">
                           <div class="col-xxl-6 col-xl-7 col-lg-8 col-md-10 col-sm-10">
                              <div class="slider__content-3 p-relative z-index-1">
                             	<?php if (!empty($item['tp_slider_sub_title'])) : ?>
                                 <span data-animation="fadeInUp" data-delay=".3s">
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.745 0.4425C9.435 -0.1475 10.565 -0.1475 11.265 0.4425L12.845 1.8025C13.145 2.0625 13.705 2.2725 14.105 2.2725H15.805C16.865 2.2725 17.735 3.1425 17.735 4.2025V5.9025C17.735 6.2925 17.945 6.8625 18.205 7.1625L19.565 8.7425C20.155 9.4325 20.155 10.5625 19.565 11.2625L18.205 12.8425C17.945 13.1425 17.735 13.7025 17.735 14.1025V15.8025C17.735 16.8625 16.865 17.7325 15.805 17.7325H14.105C13.715 17.7325 13.145 17.9425 12.845 18.2025L11.265 19.5625C10.575 20.1525 9.445 20.1525 8.745 19.5625L7.165 18.2025C6.865 17.9425 6.305 17.7325 5.905 17.7325H4.175C3.115 17.7325 2.245 16.8625 2.245 15.8025V14.0925C2.245 13.7025 2.035 13.1425 1.785 12.8425L0.435 11.2525C-0.145 10.5625 -0.145 9.4425 0.435 8.7525L1.785 7.1625C2.035 6.8625 2.245 6.3025 2.245 5.9125V4.1925C2.245 3.1325 3.115 2.2625 4.175 2.2625H5.905C6.295 2.2625 6.865 2.0525 7.165 1.7925L8.745 0.4425Z" fill="#FF8D00"/>
                                    <path d="M6.375 9.99251L8.785 12.4125L13.615 7.57251" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo tp_kses( $item['tp_slider_sub_title'] ); ?></span>
                                    <?php endif; ?>

								<?php
                                    if ($item['tp_slider_title_tag']) :
                                        printf('<%1$s %2$s>%3$s</%1$s>',
                                            tag_escape($item['tp_slider_title_tag']),
                                            $this->get_render_attribute_string('title_args'),
                                            tp_kses($item['tp_slider_title'])
                                        );
                                    endif;
                                ?>
                                 <?php if ( !empty($settings['hero_search_switch']) ): ?>
                                 <div class="slider__search mb-20" data-animation="fadeInUp" data-delay=".9s">
                                    <form action="<?php print eduker_header_search_url();?>">
                                       <div class="slider__search-input p-relative">
                                       	  <input type="search" name="s" value="<?php print esc_attr( get_search_query() )?>" placeholder="<?php echo esc_attr__('Course title here...','tpcore'); ?>">
                                          <button type="submit">Search</button>
                                          <div class="slider__search-input-icon">
                                             <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.625 15.75C12.56 15.75 15.75 12.56 15.75 8.625C15.75 4.68997 12.56 1.5 8.625 1.5C4.68997 1.5 1.5 4.68997 1.5 8.625C1.5 12.56 4.68997 15.75 8.625 15.75Z" stroke="#828282" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16.5 16.5L15 15" stroke="#828282" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                             </svg>
                                          </div>
                                       </div>
                                    </form>
                                 </div>
                                 <?php endif; ?>
                                 <?php if (!empty($item['tp_slider_description'])) : ?>
                                 <div class="slider__list" data-animation="fadeInUp" data-delay="1.2s">
                                    <?php echo tp_kses( $item['tp_slider_description'] ); ?>
                                 </div>
                                 <?php endif; ?>

                                 <?php if (!empty($link)) : ?>
                                 <div class="slider__btn" data-animation="fadeInUp" data-delay="1.1s">
                                    <a target="<?php echo esc_attr($target); ?>" rel="<?php echo esc_attr($rel); ?>" href="<?php echo esc_url($link); ?>" class="tp-btn-5 tp-btn-11">
                                    	<?php echo tp_kses($item['tp_btn_btn_text']); ?>
                                    </a>
                                 </div>
                                 <?php endif; ?>
                              </div>
                           </div>
                        </div>
                     </div>
                </div>
                <?php endforeach; ?>
               </div>
               <div class="main-slider-paginations main-slider-paginations-2">
                  <button class="slider-button-next"><i class="fa-regular fa-arrow-right"></i></button>
                  <button class="slider-button-prev"><i class="fa-regular fa-arrow-left"></i></button>
               </div>
            </div>
         </section>

		<?php else:
			$this->add_render_attribute('title_args', 'class', 'sectionTitle__big');
		?>
         <section class="slider__area">
            <div class="slider__active swiper-container">

               <div class="swiper-wrapper">
           		<?php foreach ($settings['slider_list'] as $item) :
        			$this->add_render_attribute('title_args', 'class', 'slider__title');
					$this->add_render_attribute('title_args', 'data-animation', 'fadeInUp');
					$this->add_render_attribute('title_args', 'data-delay', '.6s');

 					if ( !empty($item['tp_slider_image']['url']) ) {
                        $tp_slider_image_url = !empty($item['tp_slider_image']['id']) ? wp_get_attachment_image_url( $item['tp_slider_image']['id'], $settings['thumbnail_size']) : $item['tp_slider_image']['url'];
                        $tp_slider_image_alt = get_post_meta($item["tp_slider_image"]["id"], "_wp_attachment_image_alt", true);
                    }

					// btn Link
                    if ('2' == $item['tp_btn_link_type']) {
                        $link = get_permalink($item['tp_btn_page_link']);
                        $target = '_self';
                        $rel = 'nofollow';
                    } else {
                        $link = !empty($item['tp_btn_link']['url']) ? $item['tp_btn_link']['url'] : '';
                        $target = !empty($item['tp_btn_link']['is_external']) ? '_blank' : '';
                        $rel = !empty($item['tp_btn_link']['nofollow']) ? 'nofollow' : '';
                    }
                ?>
                  <div class="slider__item swiper-slide p-relative slider__height d-flex align-items-center z-index-1">
                     <div class="slider__bg slider__overlay include-bg" data-background="<?php echo esc_url($tp_slider_image_url); ?>"></div>
                     <div class="container">
                        <div class="row">
                           <div class="col-xl-7 col-lg-8 col-md-10 col-sm-10">
                              <div class="slider__content p-relative z-index-1">
                              	<?php if (!empty($item['tp_slider_sub_title'])) : ?>
                                 <span data-animation="fadeInUp" data-delay=".3s"><?php echo tp_kses( $item['tp_slider_sub_title'] ); ?></span>
                             	<?php endif; ?>

								<?php
                                    if ($item['tp_slider_title_tag']) :
                                        printf('<%1$s %2$s>%3$s</%1$s>',
                                            tag_escape($item['tp_slider_title_tag']),
                                            $this->get_render_attribute_string('title_args'),
                                            tp_kses($item['tp_slider_title'])
                                        );
                                    endif;
                                ?>
                                <?php if (!empty($item['tp_slider_description'])) : ?>
                                 <p data-animation="fadeInUp" data-delay=".9s"><?php echo tp_kses( $item['tp_slider_description'] ); ?></p>
                                 <?php endif; ?>

                                 <?php if (!empty($link)) : ?>
                                 <div class="slider__btn" data-animation="fadeInUp" data-delay="1.1s">
                                    <a target="<?php echo esc_attr($target); ?>" rel="<?php echo esc_attr($rel); ?>" href="<?php echo esc_url($link); ?>" class="tp-btn">
                                    	<?php echo tp_kses($item['tp_btn_btn_text']); ?>
                                    </a>
                                 </div>
                                 <?php endif; ?>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php endforeach; ?>
               </div>

               <div class="main-slider-paginations">
                  <button class="slider-button-next"><i class="fa-regular fa-arrow-right"></i></button>
                  <button class="slider-button-prev"><i class="fa-regular fa-arrow-left"></i></button>
               </div>
            </div>
         </section>
         <?php endif; ?>


		<?php
	}
}

$widgets_manager->register( new TP_Main_Slider() );