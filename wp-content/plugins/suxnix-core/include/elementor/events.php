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
class TP_Events extends Widget_Base {

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
		return 'tp-events';
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
		return __( 'Events', 'tpcore' );
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

        // Blog Query
		$this->start_controls_section(
            'tp_post_query',
            [
                'label' => esc_html__('Events Query', 'tpcore'),
            ]
        );

        $post_type = 'tribe_events';
        $taxonomy = 'tribe_events_cat';

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
            'tp_post_content',
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
            'tp_post_content_limit',
            [
                'label' => __('Content Limit', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '14',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'tp_post_content' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();


        // layout Panel
        $this->start_controls_section(
            'tp_post_',
            [
                'label' => esc_html__('Event - Layout', 'tpcore'),
            ]
        );
        $this->add_control(
            'tp_design_style',
            [
                'label' => esc_html__('Select Layout', 'tpcore'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'layout-1' => esc_html__('Default', 'tpcore'),
                    // 'layout-2' => esc_html__('Carousel', 'tpcore'),
                    // 'layout-3' => esc_html__('Carousel Full Width', 'tpcore'),
                ],
                'default' => 'layout-1',
            ]
        );
        $this->add_control(
            'tp_post__height',
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
            'tp_post__dots',
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
            'tp_post__arrow',
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
            'tp_post__infinite',
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
            'tp_post__autoplay',
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
            'tp_post__autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => '2500',
                'title' => esc_html__('Enter autoplay speed', 'tpcore'),
                'label_block' => true,
                'condition' => array(
                    'tp_post__autoplay' => 'yes',
                    'tp_design_style' => 'layout-2',
                ),
            ]
        );
        $this->add_control(
            'tp_post__filter',
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
                // 'default' => 'tp-post-thumb',
            ]
        );
        $this->add_control(
            'tp_post_pagination',
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
            'post_type' => 'tribe_events',
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
                    'taxonomy'	=> 'tribe_events_cat',
                    'field'	 	=> 'slug',
                    'terms'		=> $exclude_category_list_value,
                    'operator'	=> 'NOT IN'
                )
            );

            // Include the correct cats in tax_query
            if ( !empty($settings['category'])) {
                $args['tax_query']['relation'] = 'AND';
                $args['tax_query'][] = array(
                    'taxonomy'	=> 'tribe_events_cat',
                    'field'		=> 'slug',
                    'terms'		=> $category_list_value,
                    'operator'	=> 'IN'
                );
            }

        } else {
            // Include the cats from $cat_slugs in tax_query
            if (!empty($settings['category'])) {
                $args['tax_query'][] = [
                    'taxonomy' => 'tribe_events_cat',
                    'field' => 'slug',
                    'terms' => $category_list_value,
                ];
            }
        }

        $filter_list = $settings['category'];

        // The Query
        $query = new \WP_Query($args);

        $time_format = get_option('time_format', \Tribe__Date_Utils::TIMEFORMAT);
        $time_range_separator = tribe_get_option('timeRangeSeparator', ' - ');
        $start_time           = tribe_get_start_date(null, false, $time_format);
        $end_time             = tribe_get_end_date(null, false, $time_format);
        $time_formatted = null;

        if ($start_time == $end_time) {
            $time_formatted = esc_html($start_time);
        } else {
            $time_formatted = esc_html($start_time . $time_range_separator . $end_time);
        }

        ?>

        <?php if ( $settings['tp_design_style']  == 'layout-2' ): ?>

        <?php else:
            $this->add_render_attribute('title_args', 'class', 'sectionTitle__big tp-el-title');
        ?>

        <section class="events-area">
          <div class="container">
            <div class="row">
                <?php while ($query->have_posts()) :
                $query->the_post();
                global $post;
                $terms = get_the_terms($post->ID, 'tribe_events_cat');

                $date_text = function_exists('get_field') ? get_field('date_text') : '';
                $address_text = function_exists('get_field') ? get_field('address_text') : '';
                $speaker_image = function_exists('get_field') ? get_field('speaker_image') : '';
                $speaker_name = function_exists('get_field') ? get_field('speaker_name') : '';

                $item_classes = '';
                $item_cat_names = '';
                $item_cats = get_the_terms( $post->ID, 'tribe_events_cat' );
                if( !empty($item_cats) ):
                    $count = count($item_cats) - 1;
                    foreach($item_cats as $key => $item_cat) {
                        $item_classes .= $item_cat->slug . ' ';
                        $item_cat_names .= ( $count > $key ) ? $item_cat->name  . ', ' : $item_cat->name;
                    }
                endif;
                ?>
              <div class="col-12 mb-30">
                    <div class="event__item white-bg mb-10 transition-3 p-relative d-lg-flex align-items-center justify-content-between">
                        <div class="event__left d-sm-flex align-items-center">
                           <div class="event__date">
                              <h4><?php  echo tribe_get_start_date( $post->ID, false, 'j' ); ?> </h4>
                              <p><?php echo tribe_get_start_date( $post->ID, false, 'F' ); ?>, <?php  echo tribe_get_start_date( $post->ID, false, 'Y' ); ?></p>
                           </div>
                           <div class="event__content">
                              <div class="event__meta">
                                 <ul>
                                    <li>
                                       <a href="#"><svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M8.49992 9.51253C9.72047 9.51253 10.7099 8.52308 10.7099 7.30253C10.7099 6.08198 9.72047 5.09253 8.49992 5.09253C7.27937 5.09253 6.28992 6.08198 6.28992 7.30253C6.28992 8.52308 7.27937 9.51253 8.49992 9.51253Z" stroke="#5F6160" stroke-width="1.5"/>
                                          <path d="M2.56416 6.01334C3.95958 -0.120822 13.0475 -0.113738 14.4358 6.02043C15.2504 9.61876 13.0121 12.6646 11.05 14.5488C9.62625 15.9229 7.37375 15.9229 5.94291 14.5488C3.98791 12.6646 1.74958 9.61168 2.56416 6.01334Z" stroke="#5F6160" stroke-width="1.5"/>
                                          </svg>
                                          <?php echo tribe_get_address(); ?>, <?php echo tribe_get_city(); ?></a>
                                    </li>
                                 </ul>
                              </div>
                              <h3 class="event__title">
                                 <a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
                              </h3>

                              <?php if (!empty($speaker_name)) : ?>
                              <div class="event__person">
                                 <ul>
                                    <li>
                                       <a href="<?php echo get_the_permalink(); ?>">
                                          <img src="<?php echo esc_html($speaker_image['url']); ?>" alt="img">
                                          <span><?php echo esc_html($speaker_name); ?></span>
                                       </a>
                                    </li>
                                 </ul>
                              </div>
                              <?php endif; ?>

                           </div>
                        </div>
                        <div class="event__right d-sm-flex align-items-center">
                           <div class="event__time">
                              <span>
                                 <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.75 7.50024C13.75 10.9502 10.95 13.7502 7.5 13.7502C4.05 13.7502 1.25 10.9502 1.25 7.50024C1.25 4.05024 4.05 1.25024 7.5 1.25024C10.95 1.25024 13.75 4.05024 13.75 7.50024Z" stroke="#258E46" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.8188 9.48735L7.8813 8.3311C7.5438 8.1311 7.2688 7.64985 7.2688 7.2561V4.6936" stroke="#258E46" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                 </svg>
                                 <?php echo tribe_get_start_date( $post->ID, false, 'H:ia' ); ?>
                              </span>
                           </div>
                           <div class="event__more ml-30">
                              <a href="<?php echo get_the_permalink(); ?>" class="tp-btn-5 tp-btn-7"><?php echo esc_html__('View Events','tpcore'); ?></a>
                           </div>
                        </div>
                    </div>
              </div>
              <?php endwhile; wp_reset_query(); ?>

                <?php if($settings['tp_post_pagination'] == 'yes' && '-1' != $settings['posts_per_page'] ){ ?>
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
          </div>
        </section>

    	<?php endif; ?>

       <?php
	}

}

$widgets_manager->register( new TP_Events() );