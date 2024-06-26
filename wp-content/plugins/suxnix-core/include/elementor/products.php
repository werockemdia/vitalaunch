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
class TG_products extends Widget_Base {

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
		return 'products';
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
		return __( 'Products', 'tpcore' );
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
            'tg_product_section',
            [
                'label' => __( 'Products Loop', 'tpcore' ),
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
            'tg_product_color',
            [
                'label' => __( 'Product Color', 'tpcore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#A3D86D',
            ]
        );

		$repeater->add_control(
            'tg_btn_text',
            [
                'label' => esc_html__('Button Text', 'tpcore'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Buy Now', 'tpcore'),
                'title' => esc_html__('Enter button text', 'tpcore'),
                'label_block' => true,
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

		?>

		<script>
			jQuery(document).ready(function($){

				/*=============================================
					=           Data Color             =
				=============================================*/
				$("[data-bg-color]").each(function () {
					$(this).css("background-color", $(this).attr("data-bg-color"));
				});

				/*=============================================
					=    		Shop Active		      =
				=============================================*/
				$('.home-shop-active').slick({
					dots: true,
					infinite: true,
					speed: 1000,
					autoplay: true,
					arrows: true,
					slidesToShow: 4,
					prevArrow: '<button type="button" class="slick-prev"><i class="flaticon-left-arrow"></i></button>',
					nextArrow: '<button type="button" class="slick-next"><i class="flaticon-right-arrow"></i></button>',
					slidesToScroll: 1,
					responsive: [
						{
						breakpoint: 1500,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 1,
								infinite: true,
							}
						},
						{
						breakpoint: 1200,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 1,
								infinite: true,
							}
						},
						{
						breakpoint: 992,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1
							}
						},
						{
						breakpoint: 767,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1,
								arrows: true,
							}
						},
						{
						breakpoint: 575,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1,
								arrows: false,
							}
						},
					]
				});

			});
		</script>

		<!-- shop-area -->
		<section class="home-shop-area">
			<div class="container">
				<div class="row home-shop-active">

					<?php foreach( $settings['tg_products_lists'] as $item ) :
						$product_details = wc_get_product($item['select_product']);

                        if (empty($product_details)) {
                            return;
                        }

					?>
					<div class="col-xl-3">
						<div class="home-shop-item">
							<div class="home-shop-thumb">
								<a href="<?php echo get_permalink($product_details->get_id()) ?>">
									<img src="<?php echo esc_url( $item['tg_product_image']['url'] ); ?>" alt="<?php echo esc_attr__('img','tpcore') ?>">

									<?php
										if( $product_details->get_regular_price() != '' && $product_details->get_sale_price() != '' ){
											$percent = round(( ((int)$product_details->get_regular_price() - (int)$product_details->get_sale_price()) / (int)$product_details->get_regular_price() ) * 100);
											echo '<div class="discount">' . esc_html( '-'.$percent .'%', 'woocommerce' ) . '</div>';
										}
									?>

								</a>
								<div class="shop-thumb-shape" data-bg-color="<?php echo esc_attr( $item['tg_product_color'] ) ?>"></div>
							</div>
							<div class="home-shop-content">
								<h4 class="title">
									<a href="<?php echo get_permalink($product_details->get_id()) ?>"><?php echo esc_html($product_details->get_name()); ?></a>
								</h4>

								<span class="home-shop-price price">
									<?php echo tp_kses($product_details->get_price_html()); ?>
								</span>

								<div class="home-shop-rating">
									<?php
										if ($average = $product_details->get_average_rating()) {
											echo '<div class="star-rating" title="'.sprintf(__( 'Rated %s out of 5', 'tpcore' ), $average).'"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'tpcore' ).'</span></div>';
										}else {
											for ($i = 0; $i < 5; $i++) {
												echo '<i class="far fa-star"></i>';
											}
										}
									?>
									<span class="total-rating">(<?php echo $product_details->get_rating_count(); ?>)</span>
								</div>

								<div class="shop-content-bottom">

									<a href="<?php echo esc_url(wc_get_cart_url()) . '?add-to-cart=' . $product_details->get_id();?>" class="cart"><i class="flaticon-shopping-cart-1"></i></a>

									<a href="<?php echo get_permalink($product_details->get_id()) ?>" class="btn btn-two"><?php echo esc_html( $item['tg_btn_text'] ); ?></a>

								</div>
							</div>
						</div>
					</div>

					<?php endforeach; ?>

				</div>
			</div>
		</section>
		<!-- shop-area-end -->


	<?php
	}

}

$widgets_manager->register( new TG_products() );