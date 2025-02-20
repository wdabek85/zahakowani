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
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'U/t/LasV5kWZq8nYY$,Yg(m4`SSWMCHz:!/7A 1o;>pBHdUp32q<6(J$*>|.X#P9' );
define( 'SECURE_AUTH_KEY',   'ahO9wc?E6qZxU1;Vg$Vg$UaIayIHC0Va%gxzHEEBTxkM2/gg!rTn2WF_CFsP#}$3' );
define( 'LOGGED_IN_KEY',     '1-JN&-sIMpP`Hh)?Uu+tW$KgG><E`BNYLVNtvU%d.9eJ4VB3R+.O+Sqh@<NR@-no' );
define( 'NONCE_KEY',         'SHlHNQY`Q5%+D+Ze%Mo-(6b@x=#Gron,KqVeeejYcyGb-?:=g8b~IwZRVmazHIOj' );
define( 'AUTH_SALT',         '8L,uJ&2jt](dc4Y9o<(+b:I7@%:>+BUuS~yT02=mANx<WhcoMqb(VymX(#W]]kKs' );
define( 'SECURE_AUTH_SALT',  'aKaHrKbQ sj;b_jBdg{Jl^*.~50ls58@[&SL9HZFPwl~Gw[[KTh5KV0m[>m}y>Gs' );
define( 'LOGGED_IN_SALT',    '}b<?Ded]/KEjZ8*-yiXra#>|nZ(caq~]C5- EArS#.je]lq-<m>0qGCC{_BfAG8O' );
define( 'NONCE_SALT',        'Sh.[>)9OCiI0P635uRBRy<s<`{stUlv^m]Pkr`3_ZY/DTy9S=RvuB=^ >a#vDw)p' );
define( 'WP_CACHE_KEY_SALT', '^{-*bW{+4gE?Q$){@C2M+ V~zk1R>_OV-8bHt?l%Hg2%MW~:dZBUUc<H<jU{* Cl' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
