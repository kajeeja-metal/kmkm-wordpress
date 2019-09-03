<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'db_kmkmonline');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');



/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'q~KpAG*^ErRaa{)kJ&.q1?kj+JXi*^vCqxHpeEJ2F~-I0ScnD)onu??>$j_@%{#5');
define('SECURE_AUTH_KEY',  '>u{gCHISS++KH3Whfp=JE7I=%ut;U>j0yY]PWY#9Wx4,XLB^5_q<w+&j ^vu>)_<');
define('LOGGED_IN_KEY',    'IX2[H&18>;nrenfEFs%wRW=.eV@4z|HwGPNOj3N)nbkvJ>1#p(kur}1Bu/_W*2^L');
define('NONCE_KEY',        'DoSki|Dx>@F1B-JesT*s{qT/r;l:|%?FC>NJ$q_A87[{lJz^tLT4HCKyx.]{r@ ^');
define('AUTH_SALT',        'Cjh?ZJgsWWLh)0kJAC_jk:0^F5:l+@Bk?O:xF*YWjNDUMOyz6/JbO:cne~y~c$<B');
define('SECURE_AUTH_SALT', 'mP.a08CNMi)U;l&Tz$u@p.Q>0R*%<yrn;qC,6J/HBEKz3([wShW2#QyJI1KKVLpb');
define('LOGGED_IN_SALT',   'E 2;|PVyWO9?RrnZ</DqjCA33>Zk;)^m2?gWx:]d8gAyrE0&qG?>W+6Z#>3AMU[P');
define('NONCE_SALT',       '$c<kz1AuB&5CUenC=q2 Fk^ihmFB~5pcq1(KW13MaCqR]q]P4{geU>AEAQ)$USL4');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
