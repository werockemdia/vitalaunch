<?php
namespace TPCore\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Suxnix Core
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Hello_Test extends Widget_Base {

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
		return 'hello-test';
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
		return __( 'Hello Test', 'tpcore' );
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
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content hello', 'tpcore' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'tpcore' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'show_elements',
			[
				'label' => esc_html__( 'Show Elements', 'plugin-name' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'title'  => esc_html__( 'Title', 'plugin-name' ),
					'description' => esc_html__( 'Description', 'plugin-name' ),
					'button' => esc_html__( 'Button', 'plugin-name' ),
				],
				'default' => [ 'title', 'description' ],
			]
		);


		// $service_cats = get_terms('project-categories', array('order' => 'DESC'));
		// $cat_array = array( '' => 'Select One' );
		// foreach($service_cats as $cat) {
		//     $cat_array[$cat->slug] = $cat->name;
		// }

		/**
		 * Get Post Categories
		 */
		// function tp_get_categories($taxonomy)
		// {
		//     $terms = get_terms(array(
		//         'taxonomy' => $taxonomy,
		//         'hide_empty' => true,
		//     ));
		//     $options = array();
		//     if (!empty($terms) && !is_wp_error($terms)) {
		//         foreach ($terms as $term) {
		//             $options[$term->slug] = $term->name;
		//         }
		//     }
		//     return $options;
		// }


        // $this->add_control(
        //     'category',
        //     [
        //         'label' => esc_html__('Include Categories', 'tpcore'),
        //         'description' => esc_html__('Select a category to include or leave blank for all.', 'tpcore'),
        //         'type' => Controls_Manager::SELECT2,
        //         'multiple' => true,
        //         'options' => tp_get_categories('project-categories'),
        //         'label_block' => true,
        //     ]
        // );

		$this->end_controls_section();

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

		echo '<div class="title">';
		echo $settings['title'];
		echo '</div>';
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<div class="title">
			{{{ settings.title }}}
		</div>
		<?php
	}
}

$widgets_manager->register( new Hello_Test() );