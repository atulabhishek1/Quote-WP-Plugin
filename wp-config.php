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
define( 'AUTH_KEY',          'J*q_Kl>7>#GYMZ7_l4v*`y)o8UE;&=A@=&rLjIwm#5y=$.K[oEBHbLVD0-Xnc5=>' );
define( 'SECURE_AUTH_KEY',   '/heEkg6]OTQ/;/c2qYH]DB_Xgm](xr5{RVPM:~U/w{#FtZgHa%-Hm{~JFz[FeEm*' );
define( 'LOGGED_IN_KEY',     '%8iq.O2QSJ}/P]&M%@oaOA3,SG+Rp|X=:,&/_5-Itx6xO/_:Ou1Jnyt$mW=z#I-P' );
define( 'NONCE_KEY',         'wJj*O@<fECL{g#qJPy2g{Qy|=^A4{((!74 8 VyfoNBBX8;1aDi9@XelHEI#$_je' );
define( 'AUTH_SALT',         'P=j>P^}.T~#x=twR;OlMwy#!Vo0P-CMQ/X`O%S$z+Rsq6J4{WBfsQLd,y_JqUS0m' );
define( 'SECURE_AUTH_SALT',  '*r!2x[&HJDh.`FHJUI oH,k=/aF_-s~i_:4<a^{yB{B]pB9KTkM9$I;6_sV346uE' );
define( 'LOGGED_IN_SALT',    'wnLXnTwn3Qv^%wo3?nRJq**WcU(#g]`-n[E/9Yx(S1[^?Vh@o%1~(Ld9j) ,95n>' );
define( 'NONCE_SALT',        'X^<=~E}+|ymQd6|8jk< C ZnU;z7rt3gV)%d>n11[0I`OK6HQD0*H}{e,!5},:0p' );
define( 'WP_CACHE_KEY_SALT', '2J7Hs12Dnu)5=s4e*h]g?24{.^b2dqrMwINIa)A6e8[F5H[E*a4yk()6rKR *:%#' );


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
