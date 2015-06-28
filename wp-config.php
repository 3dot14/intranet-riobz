<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'retotrei_intranet');

/** MySQL database username */
define('DB_USER', 'retotrei_intra');

/** MySQL database password */
define('DB_PASSWORD', 'M0nst3r');

/** MySQL hostname */
define('DB_HOST', 'retotreinta.com');

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
define('AUTH_KEY',         'IbQC1_{|76z@O!TwQLRtC3V@p~&dfA%jNK:*xtVj0_Jgs@tuatA($.`wf i|$9JZ');
define('SECURE_AUTH_KEY',  'hW.jj-lYND|tba+uu=h:N]|bqZDLFYI&E!0}Gtd8]s<L|w/Dph[Z4T6f<vfBNz&I');
define('LOGGED_IN_KEY',    'q5^?,/?m!*j59E=v(LxV+}o%`G%Gw1WTiG>)oo--y&&e]`|[xbr{aA9aLt|.)nan');
define('NONCE_KEY',        'I6,,2wXm?R6ZcV(LW1z^q^l$NTLBr`<,+aA[aqe_YSdYOiB$T<]f8}aM_LL-yiEq');
define('AUTH_SALT',        'AKN0|$dGlzjb&pxV2D=|_*D?q5)rev7PPwDRT5|&0o[6s/Cw(xB{iRv0)Ps+yT!7');
define('SECURE_AUTH_SALT', '%i(E(/w.8t;X j-rSV*wy@Z}G~bl~FxZQ8h#]E}q(,`82?-8PwA`p%.ifC6mMvL+');
define('LOGGED_IN_SALT',   '?BkIj9&+]3 xe@F&6cg_CcF`x+O(;(Yqw{{[zlfG#cx5|<E,MQktu1xUy8c|b;B+');
define('NONCE_SALT',       'O,d|TGsJj:dbUZBb-t<5,#M6|A_(i77GAHz@+x@ah?TEDy%iFG|&J8C){94^V6D!');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
