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
// API REST User 
$user_info = get_userdata();
$userloginname = $user_info->user_login;
$password = $user_info->user_pass;
$nicename = $user_info->user_nicename;
$email = $user_info->user_email;
$displayname = $user_info->display_name;
$email = $user_info->user_email;
// echo "Hi, your login name: {$userloginname}, your email: {$email}, your author url: example.com/author/{$password} ";
// echo current_time('mysql', 1);
$mydb = new wpdb('root', '', 'main-member', 'localhost');
$mydb->insert( 'wp_users', array('user_login'=> $userloginname, 'user_pass'=> $password, 'user_nicename'=> $nicename, 'user_email'=> $email, 'user_registered'=> current_time('mysql', 1) , 'display_name'=> $displayname ));

$user_info = get_userdata();
	$userloginname = $user_info->user_login;
	$password = $user_info->user_pass;
	$nicename = $user_info->user_nicename;
	$email = $user_info->user_email;
	$displayname = $user_info->display_name;
	$email = $user_info->user_email;
	// echo "Hi, your login name: {$userloginname}, your email: {$email}, your author url: example.com/author/{$password} ";
	// echo current_time('mysql', 1);
	$mydb = new wpdb('root', '', 'main-member', 'localhost');
	$mydb->insert( 'wp_users', array('user_login'=> $userloginname, 'user_pass'=> $password, 'user_nicename'=> $nicename, 'user_email'=> $email, 'user_registered'=> current_time('mysql', 1) , 'display_name'=> $displayname ));