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
