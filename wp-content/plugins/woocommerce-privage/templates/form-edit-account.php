<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
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

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); 

$token = get_user_meta( $user->ID, 'token', true );

// Get profile
$url = "https://account.privageapp.com/user/profile";

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Bearer '.$token
));

$output = curl_exec($ch); 
$user_profile = json_decode($output);

curl_close($ch);

$name = $user_profile->profile->full_name;
$email = $user_profile->profile->email;
$identifier = $user_profile->identifier;
$names = explode(" ", $name);

$first_name = '';
$last_name = '';
if(sizeof($names) > 0) $first_name = $names[0];
if(sizeof($names) > 1) $last_name = $names[1];

if($name != $user->display_name) {
	$result = wp_update_user(array(
		'ID' => $user->ID,
		'nickname' => $name,
		'first_name' => $first_name,
		'last_name' => $last_name,
		'display_name' => $name
	));
}

$profile = apply_filters('privage_profile', $user->ID);
$card_design = $profile->card->card_type->image;

?>

<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
	<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?></label>
	<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" readonly="readonly" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $first_name ) ?>" />
</p>
<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
	<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?></label>
	<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" readonly="readonly"  name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $last_name ); ?>" />
</p>
<div class="clear"></div>

<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
	<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?></label>
	<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" readonly="readonly"  name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $name ); ?>" />
</p>
<div class="clear"></div>

<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
	<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
	<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" readonly="readonly" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $email ); ?>" />
</p>

<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
	<label for="account_email">Member Card</label>
	<div style="width: 300px; min-height: 150px; background-color: #efefef; border-radius: 8px; overflow: hidden; position: relative;">
		<img style="width: 100%;" src="https://service.privageapp.com<?php echo $card_design; ?>" />
		<div style="font-size: 14px; text-align: center; color: black; padding: 8px; background: rgba(255,255,255,0.8); position: absolute; left: 8px; right: 8px; bottom: 8px;">
			<div><strong>#<?php echo $profile->card->card_id; ?></strong></div>
			<div><?php echo $profile->card->point; ?> Points</div>
		</div>
	</div>
</p>

<div class="clear"></div>

<?php do_action( 'woocommerce_edit_account_form' ); ?>

<p>
	<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
	<br/>
	<script>
	function editProfile() {
		var url = 'https://account.privageapp.com/connect?token=<?php echo $token; ?>&next=edit_profile';
		var child = window.open(url,'popUpWindow','height=700,width=400,resizable=no');
		var timer = setInterval(function() {
			if (child.closed) { 
				clearInterval(timer);
				window.location.reload();
			}
		}, 500);
	}
	</script>
	<button type="button" onclick="editProfile()" class="woocommerce-Button button" name="save_account_details" value="Edit Profile">Edit Profile</button>
	<input type="hidden" name="action" value="save_account_details" />
</p>

<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
