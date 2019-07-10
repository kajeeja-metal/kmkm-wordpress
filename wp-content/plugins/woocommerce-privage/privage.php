<?php
/*
Plugin Name: WooCommerce Privage
Plugin URI: https://www.privageapp.com
Description: Privage Member connect with WooCommerce Plugin
Author: Privage App Co.,Ltd.
Author URI: https://www.privageapp.com
Version: 1.0
*/

function private_coupon_checking() {
  $api_key = 'f2c659dc5e4aaf84465488d10baa836a9ad9b1c61c56e5386be30ab31a9e0ffa';

  check_ajax_referer( 'apply-coupon', 'security' );

  // Coupon Code
  $code = $_POST['coupon_code'];

  $url = "https://service.privageapp.com/remote/api/check_code/".$code;

  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorize-Key: " . $api_key,
    "Content-Type: application/json"
  ));

  $output = curl_exec($ch);
  $result = json_decode($output);

  curl_close($ch);

  if($result->can_use) {
    WC()->cart->add_discount( wc_format_coupon_code( wp_unslash( $result->link_code ) ) );
  }
  else {
    wc_add_notice( WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_NOT_EXIST ), 'error' );
  }

  wc_print_notices();
  wp_die();
}

// Override coupon ajax
add_action('wc_ajax_apply_coupon', 'private_coupon_checking');

function privage_locate_template( $template, $template_name, $template_path ) {
  $basename = basename( $template );
  if( $basename == 'form-login.php' ) {
    $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/form-login.php';
  }
  else if($basename == 'form-edit-account.php') {
    $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/form-edit-account.php';
  }
  return $template;
}

add_filter( 'woocommerce_locate_template', 'privage_locate_template', 10, 3 );

// Apply script
add_action('wc_ajax_login_callback', 'privage_login' );

function privage_login() {
  // Get access_token
  try {
    $code = $_GET['code'];
    $url = "https://account.privageapp.com/grant?code=".$code;

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

    $output = curl_exec($ch); 
    $result = json_decode($output);

    curl_close($ch); 

    $access_token = $result->access_token;

    // Get profile
    $url = "https://account.privageapp.com/user/profile";

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Authorization: Bearer '.$access_token
    ));

    $output = curl_exec($ch); 
    $user_profile = json_decode($output);

    curl_close($ch);

    $user_id = $user_profile->user_id;
    $name = $user_profile->profile->full_name;
    $email = $user_profile->profile->email;
    $identifier = $user_profile->identifier;

    if (!username_exists( $user_id )) {
      wp_create_user( $user_id, $user_id.$identifier, $email );
    }

    $creds = array();
    $creds['user_login'] = $user_id;
    $creds['user_password'] = $user_id.$identifier;
    $creds['remember'] = true;
    wp_signon($creds, false);

    // Update profile
    $current_user = get_user_by('login', $user_id);

    if($current_user->display_name != $name) {
      $names = explode(" ", $name);

      $result = wp_update_user(array(
        'ID' => $current_user->ID,
        'nickname' => $name,
        'first_name' => sizeof($names) > 0 ? $names[0] : '',
        'last_name' => sizeof($names) > 1 ? $names[1] : '',
        'display_name' => $name
      ));
    }

    update_user_meta( $current_user->ID, 'token', $access_token );
    
  } catch (Exception $e) { 
    echo $e;
  }

  ?>
  <script>
  window.onload = function() {
    window.opener.location.reload();
    window.close();
  }
  </script>
  <?php
  exit();
}

// Get user profile
add_filter( 'privage_profile', 'get_privage_profile', 10, 1);

function get_privage_profile($user_id) {
  $sso_token = get_user_meta( $user_id, 'token', true );
  $privage_token = get_user_meta( $user_id, 'privage_token', true );
  if($privage_token == null) {
    $url = "https://service.privageapp.com/bridge/login";

    $params = array(
      "login_token" => $sso_token
    );

    $data_string = json_encode($params); 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($data_string))                                                                       
    );     
    
    $output = curl_exec($ch); 
    $result = json_decode($output);

    update_user_meta( $user_id, 'privage_token', $result->access_token );
    $privage_token = $result->access_token;

    curl_close($ch); 
  }

  $url = 'https://service.privageapp.com/v2/member/dashboard';

  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                                                                
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
      'Content-Type: application/json',                                                                                
      'access-token: ' . $privage_token,
      'bundle-identifier: com.privage.hmc'                                                                      
  ));
  
  $output = curl_exec($ch); 
  $result = json_decode($output);

  // Save member id
  if($result->results->card != null) {
    update_user_meta( $user_id, 'card_id', $result->results->card->card_id);
  }

  curl_close($ch); 

  return $result->results;
}

// Complete payment
add_action( 'woocommerce_payment_complete', 'privage_payment_complete' );

function privage_payment_complete( $order_id ){
    $order = wc_get_order( $order_id );
    $user = $order->get_user();
    if( $user ){

    }
}
