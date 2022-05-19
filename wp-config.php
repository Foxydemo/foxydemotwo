<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define( 'WP_MEMORY_LIMIT', '128M' );
define( 'DB_NAME', 'powerklg_foxyshopone' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '&6b}*  Gk0r^P5XN(F6<hv(&mvB+02UH2giG08OiH;+*wVKar !<EFlRi13 .QP ' );
define( 'SECURE_AUTH_KEY',  '1)lfKIE+<~H_0eVr%?}<sioqH%Yx$2uv/@rJfT>8Wyledy7jWi75=s?f=jii]a@L' );
define( 'LOGGED_IN_KEY',    'X,5RqruGN:>7Q)s^&-q>{]Y+B@q)qI3W0PZ#6yhni&8AKd_7{Rvrpe<@J3%v)&V0' );
define( 'NONCE_KEY',        '%8|aIrh-KHj^r$o:cgG[kLyilB8d<kv7/.f^H^oST~|`+tO-rHj(xZ%<I|,@U3<K' );
define( 'AUTH_SALT',        '4pTj~lAOAlEsTh1l7U]16(8e%xc)fVhR$~w5=8jlk%?TVZ^1AUaqCR7Pzb?:H+$1' );
define( 'SECURE_AUTH_SALT', '%n/<W`1ud`Y UPs6|Ns!fX<Q4(MZ)8B+uUp[8BVd& j*@.- { gGgN)$.B=QHH`~' );
define( 'LOGGED_IN_SALT',   'R;FTTVzCi= V_Sa$Wj6`w@SI,3*aiE0?h]#KYGpT?b@2:S1s/rO:@& ~|KKPmoXy' );
define( 'NONCE_SALT',       'l9m#wWsvvGih =dE%zNUrNP@$lAT/YC (MeXUg5ILMeyqXjV,4o*M44C,:zuw|~F' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_fdemo_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
