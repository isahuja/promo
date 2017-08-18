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
define('DB_NAME', 'i3882044_wp1');

/** MySQL database username */
define('DB_USER', 'i3882044_wp1');

/** MySQL database password */
define('DB_PASSWORD', 'P*c([jSrrDTzLxu3H7~39^.4');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'NYXP0KjJ4RHStZrnjEU0fYLxxqPEw38wL2lK09VIRsAOhyxDyMLhsLpBSNWCKtZH');
define('SECURE_AUTH_KEY',  'hPqKX11Xm1IrWbIux570BSL0MwoHdJmLA3DfndITk0ljH99fkVTsN81TjIvPCDq0');
define('LOGGED_IN_KEY',    'm2kcJQF8HYc86L459tp1yOYRT6BOESI71lFUkTUK1CRB09rh3i39hsXORtDTQTOF');
define('NONCE_KEY',        'Vo5UPm4xWTfudke2LJqbX5fWjlghjZspQmudHrYYBdxsqzMhDYI3AksZjklLRRfU');
define('AUTH_SALT',        's8nDg8w1hmvZ2kwSeGw9SMissWWagbfryRQW3F4y1cjk60p9efJ57IQa3pmke6dE');
define('SECURE_AUTH_SALT', 'gnDSzqMNbRWM2PVEfIFJphB8GeRlvpxgZFPzjrkpbPOeiu5fB4R2lQRHbA6mVX15');
define('LOGGED_IN_SALT',   'mvVwRsbz0OfluJLBkKGdTVVPePXiXRcICwnjDMvUapMPiu2T76bc1nHbkt7KCBAs');
define('NONCE_SALT',       '3DWapDK0tmYIFi52j6F714JQxgmnXOmYkkA0qWXwqzawwi5P2aetHgGDPSYtP6p6');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


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
