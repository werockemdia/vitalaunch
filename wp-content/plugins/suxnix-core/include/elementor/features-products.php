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
class TG_features_products extends Widget_Base {

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
		return 'features-products';
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
		return __( 'Features Products', 'tpcore' );
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
            'tg_features_product_section',
            [
                'label' => __( 'Features Products', 'tpcore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

		$repeater->add_control(
            'select_product',
            [
                'label' => esc_html__('Select a product', 'tpcore'),
                'type' => Controls_Manager::SELECT,
				'label_block' => true,
                'options' => $this->get_all_products(),
                'default' => 'none'
            ]
        );

		$repeater->add_control(
            'tg_title',
            [
                'label' => esc_html__('Product Name', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Seriour Mass', 'tpcore'),
                'placeholder' => esc_html__('Type Product Name', 'tpcore'),
                'label_block' => true,
            ]
        );

		$repeater->add_control(
            'tg_product_qty',
            [
                'label' => esc_html__('Product Quantity', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('High-strength, 5000IU', 'tpcore'),
                'placeholder' => esc_html__('Type Product Name', 'tpcore'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'tg_description',
            [
                'label' => esc_html__('Product Description', 'tpcore'),
                'description' => tp_get_allowed_html_desc( 'intermediate'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Vitamin D3 supplements are commonly recommended for people at risk for vitamin D deficiency. Low vitamin D levels cause depression, fatigue, and muscle weakness. Over time, vitamin D deficiency can lead to weak bones, rickets in children, and osteoporosis in adults.', 'tpcore'),
                'placeholder' => esc_html__('Type section description here', 'tpcore'),
            ]
        );

		$repeater->add_control(
            'tg_btn_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Order Now', 'tpcore'),
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'tg_product_image',
            [
                'type' => Controls_Manager::MEDIA,
                'label' => __( 'Product Image', 'tpcore' ),
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'tg_product_shape',
            [
                'type' => Controls_Manager::MEDIA,
                'label' => __( 'Product Shape', 'tpcore' ),
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->add_control(
            'tg_products_lists',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

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

		// _tg_image
		$this->start_controls_section(
            '_tg_image',
            [
                'label' => esc_html__('Shapes', 'tpcore'),
            ]
        );

        $this->add_control(
            'tg_shape01',
            [
                'label' => esc_html__( 'Choose Left Shape', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'tg_shape01_size',
                'default' => 'full',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->add_control(
            'tg_shape02',
            [
                'label' => esc_html__( 'Choose Right Top Shape', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'tg_shape02_size',
                'default' => 'full',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->add_control(
            'tg_shape03',
            [
                'label' => esc_html__( 'Choose Right Bottom Shape', 'tpcore' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'tg_shape03_size',
                'default' => 'full',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->end_controls_section();
	}

	// Get Product Lists
    public function get_all_products(){
        $products = wc_get_products([
            'status' => 'publish',
            'orderby' => 'name',
            'order' => 'DESC',
            'limit' => -1,
        ]);

        $options = ['none' => 'None'];
        foreach(  $products as $product ){
            $options[$product->get_id()] = $product->get_name();
        }

        return $options;
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

			if ( !empty($settings['tg_shape01']['url']) ) {
                $tg_image_shape01 = !empty($settings['tg_shape01']['id']) ? wp_get_attachment_image_url( $settings['tg_shape01']['id'], $settings['tg_shape01_size_size']) : $settings['tg_shape01']['url'];
                $tg_image_alt01 = get_post_meta($settings["tg_shape01"]["id"], "_wp_attachment_image_alt", true);
            }

			if ( !empty($settings['tg_shape02']['url']) ) {
                $tg_image_shape02 = !empty($settings['tg_shape02']['id']) ? wp_get_attachment_image_url( $settings['tg_shape02']['id'], $settings['tg_shape02_size_size']) : $settings['tg_shape02']['url'];
                $tg_image_alt02 = get_post_meta($settings["tg_shape02"]["id"], "_wp_attachment_image_alt", true);
            }

			if ( !empty($settings['tg_shape03']['url']) ) {
                $tg_image_shape03 = !empty($settings['tg_shape03']['id']) ? wp_get_attachment_image_url( $settings['tg_shape03']['id'], $settings['tg_shape03_size_size']) : $settings['tg_shape03']['url'];
                $tg_image_alt03 = get_post_meta($settings["tg_shape03"]["id"], "_wp_attachment_image_alt", true);
            }

			$this->add_render_attribute('title_args', 'class', 'title');
		?>

		<script>
		jQuery(document).ready(function($){

			/*=============================================
				=    	   Paroller Active  	         =
			=============================================*/
			if ($('#paroller').length) {
				$('.paroller').paroller();
			}

		});
	</script>

		<!-- features-product -->
		<section id="paroller" class="features-products">
			<div class="container">

				<?php foreach( $settings['tg_products_lists'] as $item ) :

					$product_details = wc_get_product($item['select_product']);

                    if (empty($product_details)) {
                        return;
                    }

				?>
				<div class="features-products-wrap">
					<div class="row justify-content-center">
						<div class="col-lg-6 col-md-8">
							<div class="features-products-thumb">
								<div class="main-img">
									<img src="<?php echo esc_url( $item['tg_product_image']['url'] ); ?>" alt="<?php echo esc_attr__('img','tpcore') ?>">
								</div>
								<img src="<?php echo esc_url( $item['tg_product_shape']['url'] ); ?>" alt="<?php echo esc_attr__('shape','tpcore') ?>" class="shape-img">
							</div>
						</div>
						<div class="col-lg-6 col-md-10">
							<div class="features-product-content">

								<h2 class="title">
									<a href="<?php echo get_permalink($product_details->get_id()) ?>"><?php echo tp_kses( $item['tg_title'] ); ?></a>
								</h2>

								<?php if( !empty($item['tg_product_qty']) ) : ?>
								<h6 class="features-product-quantity"><?php echo tp_kses( $item['tg_product_qty'] ); ?></h6>
								<?php endif; ?>

								<?php if( !empty($item['tg_description']) ) : ?>
								<p><?php echo tp_kses( $item['tg_description'] ); ?></p>
								<?php endif; ?>

								<div class="features-product-bottom">
									<a href="<?php echo esc_url(wc_get_checkout_url()) . '?add-to-cart=' . $product_details->get_id();?>" class="btn"><?php echo tp_kses( $item['tg_btn_text'] ); ?></a>
									<span class="price"><?php echo tp_kses($product_details->get_price_html()); ?></span>
								</div>

							</div>
						</div>
					</div>
				</div>

				<?php endforeach; ?>

			</div>
			<div class="fp-shapes-wrap">

				<?php if( !empty($tg_image_shape02) ): ?>
				<div class="fp-shape-one">
					<img src="<?php echo esc_url($tg_image_shape02); ?>" alt="shape" class="paroller" data-paroller-factor="0.25" data-paroller-factor-lg="0.20" data-paroller-factor-md="0.25" data-paroller-factor-sm="0.10" data-paroller-type="foreground" data-paroller-direction="vertical">
				</div>
				<?php endif; ?>

				<?php if( !empty($tg_image_shape01) ): ?>
				<div class="fp-shape-two">
					<img src="<?php echo esc_url($tg_image_shape01); ?>" alt="shape" class="paroller" data-paroller-factor="-0.25" data-paroller-factor-lg="0.20" data-paroller-factor-md="0.25" data-paroller-factor-sm="0.10" data-paroller-type="foreground" data-paroller-direction="vertical">
				</div>
				<?php endif; ?>

				<?php if( !empty($tg_image_shape03) ): ?>
				<div class="fp-shape-three">
					<img src="<?php echo esc_url($tg_image_shape03); ?>" alt="shape" class="paroller" data-paroller-factor="0.25" data-paroller-factor-lg="0.20" data-paroller-factor-md="0.25" data-paroller-factor-sm="0.10" data-paroller-type="foreground" data-paroller-direction="vertical">
				</div>
				<?php endif; ?>

			</div>
			<div class="fp-circle one"></div>
			<div class="fp-circle two"></div>
			<div class="fp-circle three"></div>
			<div class="fp-circle four"></div>
			<div class="fp-circle five"></div>
		</section>
		<!-- features-product-end -->


	<?php
	}

}

$widgets_manager->register( new TG_features_products() );