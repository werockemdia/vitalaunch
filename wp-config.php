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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', "vitalaunch_main" );

/** Database username */
define( 'DB_USER', "vitalaunch_main" );

/** Database password */
define( 'DB_PASSWORD', "vitalaunch_main" );

/** Database hostname */
define( 'DB_HOST', "localhost" );

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
define( 'AUTH_KEY',          'iAre>|5iOB]y!&8Ix,ZVv:iLiiQX]#MQ6[vatj~W!GjK82E_AhpKaGGkV&U:8n;#' );
define( 'SECURE_AUTH_KEY',   'MBF-k_[S,O)107AX.&[XAS0-m0%46S!bP@y_k$ZR;d_Ng7$x_LvBW.1K2F)<Q~!h' );
define( 'LOGGED_IN_KEY',     'jC}x#4ZV,>Hf],Owlz1V}$(;1ty6G|5$B?m3q(5>je`UFJ9@=LRfI8*U*:42( BH' );
define( 'NONCE_KEY',         'uK[TS%W]p(rLf?gg<wl>B_Z@O @O`bl< *L!{?#@`@0!;<sHy|,(qCt{s>h,fQVf' );
define( 'AUTH_SALT',         'gg4pW.dfb[`Lk@QM-(#`$rjxoB#;O UF7bClLWD[yoTGjG 7xI&QKr%<1iT#hU.4' );
define( 'SECURE_AUTH_SALT',  '<j6XV=#2#v/`kW=/dfeP J*ab& OL<H0IA(5ZV_C}HF2QRg&qz8NRlGk8BKdX3Y*' );
define( 'LOGGED_IN_SALT',    'guAN@ p/qC.2$]YWV[&MT%Qw9AED%mW;n~HTAj(a@aRjueOZ)cqL_:*O?:R219(Y' );
define( 'NONCE_SALT',        'aMVAb^hkeG&B2z#g!!,7 )w|ZJGq&JrieIk.pVk$BjZNZ/P|1:YA/^su6fg7:r(o' );
define( 'WP_CACHE_KEY_SALT', 'Y[f!*S@5+6y6IqU!G.g4k?y.iJn4w4i}W;(3N@Z0-VVz&1f(2piI{FzKMs?r0xv5' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

define( 'SUNRISE', 'on' );
/* Add any custom values between this line and the "stop editing" line. */
/* Multisite */
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
define( 'DOMAIN_CURRENT_SITE', 'vitalaunch.io' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

define( 'WP_DEFAULT_THEME', 'twentytwentytwo' );

define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'DUPLICATOR_AUTH_KEY', ',XQ>3[Mm8tO@q~mIL&3kkfrBRab[T9B,E6:F9BdC30E<5$ 5VE[ jVWMFrMbRYr{' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(__FILE__) . '/' );
}
define('WP_MEMORY_LIMIT', '1024M');
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
