<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

<div class="u-columns col2-set" id="customer_login">

	<div class="u-column1 col-1">

<?php endif; ?>

		<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

    <script>
    function doLogin() {
      var redirect = location.origin + '/?wc-ajax=login_callback';
      var url = 'https://account.privageapp.com/?identifier=com.privage.hmc&redirect_url=' + redirect;
      window.open(url,'popUpWindow','height=700,width=400,resizable=no');
    }
    </script>

		<form class="woocommerce-form woocommerce-form-login login" method="post">

      <p>Click the button to Log In</p>
      <button type="button" onclick="doLogin()" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="Click to Log In">Click to Log In</button>

		</form>


<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
