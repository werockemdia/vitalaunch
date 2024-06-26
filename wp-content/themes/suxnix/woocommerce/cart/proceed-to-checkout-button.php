<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<style>
    .checkout_fill {
    background: #000000;
    color: #ffffff;
}
.checkout_fill:hover{
    background: #000000;
    color: #ffffff;
}
.checkout_fill::before
{
    background: #000000;
}
</style>
<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout_fill checkout-button btn w-100">
	<?php esc_html_e( 'Proceed to checkout', 'suxnix' ); ?>
</a>
