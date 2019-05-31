<?php
/*
Plugin Name: WooCommerce Privage
Plugin URI: https://www.privageapp.com
Description: Privage Member connect with WooCommerce Plugin
Author: Privage App Co.,Ltd.
Author URI: https://www.privageapp.com
Version: 1.0
*/

function coupon_checking() {
  check_ajax_referer( 'apply-coupon', 'security' );

  // Coupon Code
  $code = $_POST['coupon_code'];

  if ( ! empty( $code ) ) {
    WC()->cart->add_discount( wc_format_coupon_code( wp_unslash( $code ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
  } else {
    wc_add_notice( WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_PLEASE_ENTER ), 'error' );
  }

  wc_print_notices();
  wp_die();
}

// Override coupon ajax
add_action('wc_ajax_apply_coupon', 'coupon_checking');
