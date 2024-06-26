<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else :

$user_id =  get_current_user_id();
$billing_first_name = $_POST['billing_first_name'];
$billing_last_name = $_POST['billing_last_name'];
$billing_company = $_POST['billing_company'];
$billing_country = $_POST['billing_country'];
$billing_address_1 = $_POST['billing_address_1'];
$billing_address_2 = $_POST['billing_address_2'];
$city_value = $_POST['billing_city'];
$billing_postcode = $_POST['billing_postcode'];
$billing_phone = $_POST['billing_phone'];
$billing_email = $_POST['billing_email'];

if(!empty($billing_first_name )){
$data = array(
    'billing_first_name'          => $billing_first_name,
    'billing_last_name'          => $billing_last_name,
    'billing_company'          => $billing_company,
    'billing_country'          => $billing_country,
    'billing_address_1'          => $billing_address_1,
    'billing_address_2'          => $billing_address_2,
    'billing_city'          => $city_value,
    'billing_postcode'      => $billing_postcode,
    'billing_phone'         => $billing_phone,
    'billing_email'         => $billing_email,
);
foreach ($data as $meta_key => $meta_value ) {
    update_user_meta( $user_id, $meta_key, $meta_value );
}
}
?>

	<form method="post">

		<h3><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?></h3><?php // @codingStandardsIgnoreLine ?>

		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

			<div class="woocommerce-address-fields__field-wrapper">
				<?php
				foreach ( $address as $key => $field ) {
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				}
				?>
			</div>

			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

			<p>
				<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</p>
		</div>

	</form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
