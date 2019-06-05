<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
// API Payment_Complete
// API REST User 

add_action( 'wp_ajax_my_action', 'my_action_callback' );
wp_register_script( 'ajax-js3',get_template_directory_uri() . '/assets/js/jquery.onepage-scroll.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'ajax-js3' );
wp_register_script( 'ajax-js2','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'ajax-js2' );
function display_price_in_variation_option_name( $term ) {
    $product = wc_get_product();
	$id = $product->get_id();
	if ( $product->is_type( 'variable' ) ) {
		$product_variations = $product->get_available_variations();
	}

	foreach($product_variations as $variation){
		foreach($variation['attributes'] as $key => $slug){
			if("attribute_" == mb_substr( $key, 0, 10 )){
				$taxonomy = mb_substr( $key, 10 ) ;
				$attribute = get_term_by('slug', $slug, $taxonomy);
				if($attribute->name == $term){
					$price_d = " " . wp_kses( wc_price($variation['display_price']), array()) . "";
				}
			}
		}
	}
	
    return $price_d ;

}
add_filter( 'woocommerce_variation_option_name_price', 'display_price_in_variation_option_name' );
add_action('add_to_cart_redirect', 'resolve_dupes_add_to_cart_redirect');

function resolve_dupes_add_to_cart_redirect($url = false) {

     // If another plugin beats us to the punch, let them have their way with the URL
     if(!empty($url)) { return $url; }

     // Redirect back to the original page, without the 'add-to-cart' parameter.
     // We add the `get_bloginfo` part so it saves a redirect on https:// sites.
     return add_query_arg(array(), remove_query_arg('add-to-cart'));

}

remove_action( 'storefront_content_top', 'storefront_shop_messages' , 15);
remove_action( 'woocommerce_before_single_product', 'wc_print_notices' , 10);

add_action( 'storefront_before_header',  'wc_print_notices' );


// wp_register_script( 'ajax-js', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '', true );
// wp_localize_script( 'ajax-js', 'Slug_API_Settings', array(
// 	'root' => esc_url_raw( rest_url() ),
// 	'nonce' => wp_create_nonce( 'wp_rest' ),
// 	'current_user_id' => (int) get_current_user_id()
// ));

// wp_localize_script( 'ajax-js', 'ajax_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
// wp_enqueue_script( 'ajax-js' );
function my_action_callback() {

    // $whatever = $_POST['user_id'];
	// $user_info = get_userdata($whatever);
	// $userloginname = $user_info->user_login;
	// $password = $user_info->user_pass;
	// $nicename = $user_info->user_nicename;
	// $email = $user_info->user_email;
	// $displayname = $user_info->display_name;
	// $email = $user_info->user_email;
	// $all_meta_for_user_address1 = get_user_meta($_POST['user_id'], 'billing_address_1', fales);
	// $all_meta_for_user_address2 = get_user_meta($_POST['user_id'], 'billing_address_2', fales);
	// header('Content-type: application/json');
 //  	echo json_encode($all_meta_for_user_address1 . $all_meta_for_user_address2);
	// $mydb = new wpdb('root', '', 'main-member', 'localhost');
	// $mydb->insert( 'wp_users', array('user_login'=> $userloginname, 'user_pass'=> $password, 'user_nicename'=> $nicename, 'user_email'=> $email, 'user_address'=> $all_meta_for_user_address1 . $all_meta_for_user_address2, 'user_registered'=> current_time('mysql', 1) , 'display_name'=> $displayname ));
    wp_die(); // this is required to terminate immediately and return a proper response
}

if ( ! isset( $content_width ) ) {
	$content_width = 1200; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';

	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
		require 'inc/nux/class-storefront-nux-starter-content.php';
	}
}

// function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
//   $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
//   return $connection;
// }

// $connection = getConnectionWithAccessToken("abcdefg", "hijklmnop");
// $content = $connection->

// get("statuses/home_timeline");


/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
add_action( 'after_setup_theme', 'bbloomer_remove_zoom_lightbox_theme_support', 99 );
 
function bbloomer_remove_zoom_lightbox_theme_support() { 
	remove_theme_support( 'wc-product-gallery-zoom' );
}

add_filter('woocommerce_sale_flash', 'woocommerce_custom_sale_text', 10, 3);
function woocommerce_custom_sale_text($text, $post, $_product)
{
    return '<span class="onsale">DISCOUNT</span>';
}

// Output the Custom field in Product pages
add_action( 'woocommerce_review_order_before_payments', 'bbloomer_checkout_radio_choice' );
add_action( 'woocommerce_review_order_before_payment', 'bbloomer_checkout_radio_choice' );
 
function bbloomer_checkout_radio_choice() {
    
   $chosen = WC()->session->get('radio_chosen');
   $chosen = empty( $chosen ) ? WC()->checkout->get_value('radio_choice') : $chosen;
   $chosen = empty( $chosen ) ? 'no_option' : $chosen;
       
   $args = array(
   'type' => 'radio',
   'class' => array( 'form-row-wide' ),
   'options' => array(
      'no_option' => 'None',
      'option_1' => 'HBD Card',
      'option_2' => 'Congradtulation Card',
      'option_3' => 'Text Card',
   ),
   'default' => $chosen
   );
    
   echo '<div id="checkout-radio">';
   echo '<h3>Customize Your Order!</h3>';
   woocommerce_form_field( 'radio_choice', $args, $chosen );
   echo '</div>';
    
}
 
// Part 2 
// Add Fee and Calculate Total
// Based on session's "radio_chosen"
 
#2 Calculate New Total
add_filter( 'woocommerce_get_price_html', 'bbloomer_price_free_zero_empty', 100, 2 );
  
function bbloomer_price_free_zero_empty( $price, $product ){
 
if ( '' === $product->get_price() || 0 == $product->get_price() ) {
    $price = '<span class="woocommerce-Price-amount amount">FREE</span>';
} 
 
return $price;
}
add_action( 'woocommerce_cart_calculate_fees', 'bbloomer_checkout_radio_choice_fee', 20, 1 );
 
function bbloomer_checkout_radio_choice_fee( $cart ) {
  
  if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
   
  $radio = WC()->session->get( 'radio_chosen' );
    
  if ( "option_1" == $radio ) {
   $fee = 0;
   $name = 'HBD Card';
  } elseif ( "option_2" == $radio ) {
   $fee = 0;
   $name = 'Congradtulation Card';
  }
  elseif ( "option_3" == $radio ) {
   $fee = 10;
   $name = 'Text Card';
  }
   
  $cart->add_fee( __($name, 'woocommerce'), $fee );
  
}
 
// Part 3 
// Refresh Checkout if Radio Changes
// Uses jQuery
 
add_action( 'wp_footer', 'bbloomer_checkout_radio_choice_refresh' );
 
function bbloomer_checkout_radio_choice_refresh() {
if ( ! is_checkout() ) return;
    ?>
    <script type="text/javascript">
    jQuery( function($){
        $('form.checkout').on('change', 'input[name=radio_choice]', function(e){
            e.preventDefault();
            var p = $(this).val();
            $.ajax({
                type: 'POST',
                url: wc_checkout_params.ajax_url,
                data: {
                    'action': 'woo_get_ajax_data',
                    'radio': p,
                },
                success: function (result) {
                    $('body').trigger('update_checkout');
                }
            });
        });
    });
    </script>
    <?php
}
 
// Part 4 
// Add Radio Choice to Session
// Uses Ajax
add_action( 'wp_ajax_woo_get_ajax_data', 'bbloomer_checkout_radio_choice_set_session' );
add_action( 'wp_ajax_nopriv_woo_get_ajax_data', 'bbloomer_checkout_radio_choice_set_session' );
 
function bbloomer_checkout_radio_choice_set_session() {
    if ( isset($_POST['radio']) ){
        $radio = sanitize_key( $_POST['radio'] );
        WC()->session->set('radio_chosen', $radio );
        echo json_encode( $radio );
    }
    die();
}

add_shortcode('list_taxonomy_archive', 'wckc_list_taxonomy_archive');
function wckc_list_taxonomy_archive($atts){
    $a = shortcode_atts( array(
        'cpt' => 'post',
        'tax' => 'category',
    ), $atts );
 
    $output = '';
 
    $terms = get_terms( array('taxonomy' => $a['tax'], 'hide_empty' => false) );

 
    if( $terms ){
        $output .= '<div class="list_tax_archive">';
        foreach ($terms as $term) {
            if ( is_array($term) && isset($term['invalid_taxonomy']) )
                return;
 
            $args = array (
                'post_type'         => $a['cpt'],
                $a['tax']           => $term->slug,
                'posts_per_page'    => '-1',
            );
 
            // The Query
            $posts = get_posts($args);
 
            if( empty($posts)){
                return;
            }
            $output .= "<h4><a href=http://localhost/kmkm-wordpress/category/".$term->slug.">".$term->name."</a></h4>";
            $output .= '<ul class="term_archive">';
            foreach($posts as $post){
                $output .= '<li><a href="'.get_permalink( $post  ).'">'.get_the_title( $post ).'</a></li>';
            }
            $output .= '</ul>';
 
        }
        $output .= '</div>';
    }
    return $output;
 
}
  
    if (class_exists('MultiPostThumbnails')) {
    new MultiPostThumbnails(
        array(
            // Replace [YOUR THEME TEXT DOMAIN] below with the text domain of your theme (found in the theme's `style.css`).
            'label' => __( 'Secondary Image', '[YOUR THEME TEXT DOMAIN]'),
            'id' => 'secondary-image',
            'post_type' => 'page'
        )
    );

}
remove_filter( 'the_excerpt', 'wpautop' );
