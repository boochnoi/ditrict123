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
define('DB_NAME', 'mynz_wpsite');

/** MySQL database username */
define('DB_USER', 'mynz_dev');

/** MySQL database password */
define('DB_PASSWORD', 'gV3fwIFoEtkndJ2,G5dn');

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
define('AUTH_KEY',         'T`H(Nc|8e#L[wfJTqx*,$/B%jI=zijMP%o1@46Akb+Z@FJrDTl/$tI<fn<?#>J R');
define('SECURE_AUTH_KEY',  'mr<(KUSRCm?Z<4&/o|W[oL}Jb*-iEK?;-SxZ>;8qI4/?HvS:qaB>t<qk1Db3w.gE');
define('LOGGED_IN_KEY',    '=n.{PH3+WL(>+-[3mCy%*eY1?QI`Eb6u5ie^@;qrB(rb3)ynvRW9J*= AxhE$3eo');
define('NONCE_KEY',        '*-^>i/w0u[|Y,;9QYhc6{HDmZO=hGIJ}}q$%U(!H`2Y*}1H$fWyK4/bauD3}c<-]');
define('AUTH_SALT',        '[Q+0GPMZej)f9FQa1TfC)|j+V^pF7.<wp>[x4;(|:9Mn:nJdAi,%6a]3|M7Vw49/');
define('SECURE_AUTH_SALT', '~U/n}fgrMF.O-If/y$LdX3OB%~hOw2^TI+eXDN:0vkm/8WYyXYGL *Urq%`u5aWR');
define('LOGGED_IN_SALT',   '{tMO1)q:v]~88%1A[`Oe:]c#F0/%V+~{1kaGaW)G(o~QZC+aZT22BC.%>,yVQKbt');
define('NONCE_SALT',       'P^7<9tX)Ps[9R8;a4d&]9_aQW1oIRw=b+(9nM<;-U?vR,r|i#PEC0a{N8,o.;jE+');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'ds_';

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
