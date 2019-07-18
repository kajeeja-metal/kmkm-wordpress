<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="tabs tabs-style-bar">
	<nav>
		<ul>
			<!-- <li class="tab-current"><a href="#section-bar-1" class="icon icon-home"><span>LOGIN</span></a></li> -->
			<li class=""><a href="#section-bar-2" class="icon icon-box"><span>COUPON</span></a></li>
			<li class=""><a href="#section-bar-3" class="icon icon-display"><span>BILLING & SHIPPING</span></a></li>
			<li class=""><a href="#section-bar-4" class="icon icon-upload"><span>ORDER & PAYMENT</span></a></li>
		</ul>
	</nav>
	<BR><BR>
<div class="content-wrap">

<!-- <section id="section-bar-1" class=" content-current">
<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
</section> -->
<style type="text/css">
	.checkout_coupon{
		display: block !important;
	}
</style>
<section id="section-bar-2" class="">
<?php do_action( 'woocommerce_before_checkout_coupons_form', $checkout ); ?>
</section>
<?php // If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>


<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
<section id="section-bar-3" class="">
		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="col2-set" id="customer_details">
				<div class="col-1">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>

				<div class="col-2">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>
</section>
	<section id="section-bar-4" class="">
		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
		
		<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
		
		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</section>
	<section id="section-bar-5" class=""><p>5</p></section>

</form>
</div><!-- /content -->

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>

