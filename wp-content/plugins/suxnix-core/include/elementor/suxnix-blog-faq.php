<?php
namespace TPCore\Widgets;

use Elementor\Widget_Base;
use \Elementor\Group_Control_Text_Shadow;
use \Elementor\Repeater;
use \Elementor\Control_Media;
use \Elementor\Utils;
Use \Elementor\Core\Schemes\Typography;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Image_Size;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Suxnix Core
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class TG_Blog_Faq extends Widget_Base {

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
		return 'tg-blog-faq';
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
		return __( 'Blog & FAQ', 'tpcore' );
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

        // Blog
        $this->start_controls_section(
            '_section_blog',
            [
                'label' => __('Blog Title', 'tpcore'),
            ]
        );

        $this->add_control(
            'show_blog_title',
            [
                'label' => __('Show Title', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tpcore'),
                'label_off' => __('Hide', 'tpcore'),
                'return_value' => 'yes',
                'default' => 'yes',
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'blog_sub_title',
            [
                'label' => __('Sub Title Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('.. Suxnix News ..', 'tpcore'),
                'placeholder' => __('Type title text', 'tpcore'),
                'dynamic' => [
                    'active' => true
                ]
            ]
        );

        $this->add_control(
            'blog_title',
            [
                'label' => esc_html__('Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Latest News', 'tpcore'),
                'placeholder' => esc_html__('Type Heading Text', 'tpcore'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'blog_title_tag',
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
            'blog_title_align',
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
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};'
                ]
            ]
        );

        $this->end_controls_section();

        // Blog Query
		$this->start_controls_section(
            'tp_post_query',
            [
                'label' => esc_html__('Blog Query', 'tpcore'),
            ]
        );

        $post_type = 'post';
        $taxonomy = 'category';

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

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'exclude' => ['custom'],
                // 'default' => 'tp-post-thumb',
            ]
        );

        $this->end_controls_section();


        // FAQ
        $this->start_controls_section(
            '_section_faq',
            [
                'label' => __('FAQ Title', 'tpcore'),
            ]
        );

        $this->add_control(
            'show_faq_title',
            [
                'label' => __('Show Title', 'tpcore'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tpcore'),
                'label_off' => __('Hide', 'tpcore'),
                'return_value' => 'yes',
                'default' => 'yes',
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'faq_sub_title',
            [
                'label' => __('Sub Title Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('.. Ask Question ..', 'tpcore'),
                'placeholder' => __('Type title text', 'tpcore'),
                'dynamic' => [
                    'active' => true
                ]
            ]
        );

        $this->add_control(
            'faq_title',
            [
                'label' => esc_html__('Title', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Get Every Answers', 'tpcore'),
                'placeholder' => esc_html__('Type Heading Text', 'tpcore'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'faq_title_tag',
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
            'faq_title_align',
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
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_accordion',
            [
                'label' => esc_html__( 'FAQ Accordion', 'tpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'accordion_title', [
                'label' => esc_html__( 'Accordion Title', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Suxnix ingredients provides a searchable ?' , 'tpcore' ),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'accordion_description',
            [
                'label' => esc_html__('Description', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'There are many variations of passages of lorem ipsum that available but the majority have alteration in some form by injected humour. There are many variations of passages.',
            ]
        );
        $this->add_control(
            'accordions',
            [
                'label' => esc_html__( 'Repeater Accordion', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'accordion_title' => esc_html__( 'Suxnix ingredients provides a searchable ?', 'tpcore' ),
                    ],
                    [
                        'accordion_title' => esc_html__( 'How to edit Suxnix themes ?', 'tpcore' ),
                    ],
                    [
                        'accordion_title' => esc_html__( 'Suxnix app is a powerful application ?', 'tpcore' ),
                    ],
                    [
                        'accordion_title' => esc_html__( 'Latest version thorough Suxnix powerful ?', 'tpcore' ),
                    ],
                ],
                'title_field' => '{{{ accordion_title }}}',
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
                'post_type' => 'post',
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
                        'taxonomy'	=> 'category',
                        'field'	 	=> 'slug',
                        'terms'		=> $exclude_category_list_value,
                        'operator'	=> 'NOT IN'
                    )
                );

                // Include the correct cats in tax_query
                if ( !empty($settings['category'])) {
                    $args['tax_query']['relation'] = 'AND';
                    $args['tax_query'][] = array(
                        'taxonomy'	=> 'category',
                        'field'		=> 'slug',
                        'terms'		=> $category_list_value,
                        'operator'	=> 'IN'
                    );
                }

            } else {
                // Include the cats from $cat_slugs in tax_query
                if (!empty($settings['category'])) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => $category_list_value,
                    ];
                }
            }

            $filter_list = $settings['category'];

            // The Query
            $query = new \WP_Query($args);

            // Title Args
            $this->add_render_attribute('blog_title_args', 'class', 'title');

            $this->add_render_attribute('faq_title_args', 'class', 'title');

		?>

        <script>
            jQuery(document).ready(function($){

                /*=============================================
                    =          Data Background               =
                =============================================*/
                $("[data-background]").each(function () {
                    $(this).css("background-image", "url(" + $(this).attr("data-background") + ")")
                });


                /*=============================================
                    =    		Accordion Active		      =
                =============================================*/
                $(function () {
                    $('.accordion-collapse').on('show.bs.collapse', function () {
                        $(this).parent().addClass('active-item');
                        $(this).parent().prev().addClass('prev-item');
                    });

                    $('.accordion-collapse').on('hide.bs.collapse', function () {
                        $(this).parent().removeClass('active-item');
                        $(this).parent().prev().removeClass('prev-item');
                    });
                });

            });
        </script>

            <!-- blog-post-area -->
            <section id="news" class="blog-post-area">
                <div class="container">
                    <div class="blog-inner-wrapper">
                        <div class="row justify-content-center">
                            <div class="col-lg-6 col-md-10">
                                <div class="blog-posts-wrapper">

                                    <?php if ( !empty($settings['show_blog_title']) ) : ?>
                                        <div class="section-title mb-50">

                                            <?php if ( !empty($settings['blog_sub_title']) ) : ?>
                                            <p class="sub-title"><?php echo tp_kses( $settings['blog_sub_title'] ); ?></p>
                                            <?php endif; ?>

                                            <?php
                                                if ( !empty($settings['blog_title' ]) ) :
                                                    printf( '<%1$s %2$s>%3$s</%1$s>',
                                                        tag_escape( $settings['blog_title_tag'] ),
                                                        $this->get_render_attribute_string( 'blog_title_args' ),
                                                        tp_kses( $settings['blog_title' ] )
                                                    );
                                                endif;
                                            ?>

                                        </div>
                                    <?php endif; ?>

                                    <?php while ($query->have_posts()) :
                                        $query->the_post();
                                        global $post;

                                        $categories = get_the_category($post->ID);
                                    ?>

                                    <div class="blog-post-item">

                                        <?php if (has_post_thumbnail( $post->ID ) ): ?>
                                            <a href="<?php the_permalink(); ?>"><div class="blog-post-thumb" data-background="<?php the_post_thumbnail_url( $post->ID, $settings['thumbnail_size'] );?>"></div></a>
                                        <?php endif; ?>

                                        <div class="blog-post-content">

                                            <div class="content-top">

                                                <div class="tags"><a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>"><?php echo esc_html($categories[0]->name); ?></a></div>

                                                <span class="date"><i class="far fa-clock"></i> <?php the_time( get_option('date_format') ); ?></span>

                                            </div>

                                            <h3 class="title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h3>

                                            <div class="content-bottom">
                                                <ul class="list-wrap">
                                                    <li class="user"><?php echo esc_html__('Post By','tpcore') ?> - <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>"><?php print get_the_author();?></a></li>
                                                    <li class="comments">
                                                        <i class="far fa-envelope"></i>
                                                        <?php
                                                            $num_comments = get_comments(
                                                                array(
                                                                    'post_id' => get_the_ID(),
                                                                    'type' => 'comment',
                                                                    'count' => true // return only the count
                                                                )
                                                            );
                                                            echo $num_comments;
                                                        ?>

                                                    </li>
                                                    <li class="viewers"><i class="far fa-eye"></i>
                                                    <?php
                                                        $post_views_count = get_post_meta( get_the_ID(), 'post_views_count', true );
                                                        // Check if the custom field has a value.
                                                        if ( ! empty( $post_views_count ) ) {
                                                            echo $post_views_count;
                                                        }
                                                        echo ' '.'Views'
                                                    ?>
                                                </li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                    <?php endwhile; wp_reset_query(); ?>

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-10">
                                <div class="faq-wrapper">

                                    <?php if ( !empty($settings['show_faq_title']) ) : ?>
                                        <div class="section-title mb-50">

                                            <?php if ( !empty($settings['faq_sub_title']) ) : ?>
                                            <p class="sub-title"><?php echo tp_kses( $settings['faq_sub_title'] ); ?></p>
                                            <?php endif; ?>

                                            <?php
                                                if ( !empty($settings['faq_title' ]) ) :
                                                    printf( '<%1$s %2$s>%3$s</%1$s>',
                                                        tag_escape( $settings['faq_title_tag'] ),
                                                        $this->get_render_attribute_string( 'faq_title_args' ),
                                                        tp_kses( $settings['faq_title' ] )
                                                    );
                                                endif;
                                            ?>

                                        </div>
                                    <?php endif; ?>

                                    <div class="accordion" id="accordionExample">

                                        <?php foreach ( $settings['accordions'] as $index => $item) :
                                            $collapsed = ($index == '0' ) ? '' : 'collapsed';
                                            $aria_expanded = ($index == '0' ) ? "true" : "false";
                                            $show = ($index == '0' ) ? "show" : "";
                                            $active = ($index == '0' ) ? "active-item" : "";
                                        ?>

                                        <div class="accordion-item <?php echo esc_attr($active); ?>">
                                            <h2 class="accordion-header" id="headingOne-<?php echo esc_attr($index); ?>">
                                                <button class="accordion-button <?php echo esc_attr($collapsed); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-<?php echo esc_attr($index); ?>" aria-expanded="<?php echo esc_attr($aria_expanded); ?>" aria-controls="collapseOne-<?php echo esc_attr($index); ?>">
                                                <span class="count">0<?php echo esc_html($index)+1; ?>.</span> <?php echo esc_html($item['accordion_title']); ?>
                                                </button>
                                            </h2>
                                            <div id="collapseOne-<?php echo esc_attr($index); ?>" class="accordion-collapse collapse <?php echo esc_attr($show); ?>" aria-labelledby="headingOne-<?php echo esc_attr($index); ?>" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <?php echo tp_kses($item['accordion_description']); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="blog-bg-shape one"></div>
                <div class="blog-bg-shape two"></div>
            </section>
            <!-- blog-post-area-end -->


        <?php
	}

}

$widgets_manager->register( new TG_Blog_Faq() );