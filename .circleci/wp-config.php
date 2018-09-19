<?php
require __DIR__ . '/vendor/autoload.php';

$root_dir = __DIR__;
$dot_evn_flag = false;
if ( file_exists( __DIR__ . '/.env' ) ) {
	$env_dir = __DIR__;
	$dot_evn_flag = true;
} elseif ( file_exists( dirname( __DIR__ ) . '/.env' ) ) {
	$env_dir = dirname( __DIR__ );
	$dot_evn_flag = true;
} elseif ( file_exists( dirname( dirname( __DIR__ ) ) . '/.env' ) ) {
	$env_dir = dirname( dirname( __DIR__ ) );
	$dot_evn_flag = true;
}
if(true == $dot_evn_flag){
	$dotenv = new Dotenv\Dotenv( $env_dir );
	$dotenv->load();
}

$_ENV['DISABLE_WP_CRON'] = (bool) $_ENV['DISABLE_WP_CRON'];
$_ENV['MULTISITE'] = (bool) $_ENV['MULTISITE'];
$_ENV['SUBDOMAIN_INSTALL'] = (bool) $_ENV['SUBDOMAIN_INSTALL'];

define( 'WP_ENV', $_ENV[ 'WP_ENV' ] ?: 'production' );


if ( ! $_ENV[ 'WP_HOME' ] ) {
	$_ENV['SERVER_NAME'] = $_ENV[ 'WP_HOME' ];
} else {
	$_ENV['SERVER_NAME'] = 'localhost';
}

$protocol = isset( $_ENV['HTTPS'] ) ? 'https://' : 'http://';
define( 'WP_HOME', $_ENV[ 'WP_HOME' ] ? $_ENV[ 'WP_HOME' ] : $protocol . $_ENV['SERVER_NAME'] );
define( 'WP_SITEURL', $_ENV[ 'WP_SITEURL' ] ? $_ENV[ 'WP_SITEURL' ] : $protocol . $_ENV['SERVER_NAME'] . '/');

/**
 * Custom Content Directory
 */
define( 'CONTENT_DIR', '/wp-content' );
define( 'WP_CONTENT_DIR', $root_dir . CONTENT_DIR );
define( 'WP_CONTENT_URL', WP_HOME . CONTENT_DIR );
/**
 * DB settings
 */
define( 'DB_NAME', $_ENV[ 'DB_NAME' ] );
define( 'DB_USER', $_ENV[ 'DB_USER' ] );
define( 'DB_PASSWORD', $_ENV[ 'DB_PASSWORD' ] );
define( 'DB_HOST', $_ENV[ 'DB_HOST' ] ?: 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
$table_prefix = $_ENV[ 'DB_PREFIX' ] ?: 'wp_';
/**
 * Authentication Unique Keys and Salts
 */
define( 'AUTH_KEY', $_ENV[ 'AUTH_KEY' ] );
define( 'SECURE_AUTH_KEY', $_ENV[ 'SECURE_AUTH_KEY' ] );
define( 'LOGGED_IN_KEY', $_ENV[ 'LOGGED_IN_KEY' ] );
define( 'NONCE_KEY', $_ENV[ 'NONCE_KEY' ] );
define( 'AUTH_SALT', $_ENV[ 'AUTH_SALT' ] );
define( 'SECURE_AUTH_SALT', $_ENV[ 'SECURE_AUTH_SALT' ] );
define( 'LOGGED_IN_SALT', $_ENV[ 'LOGGED_IN_SALT' ] );
define( 'NONCE_SALT', $_ENV[ 'NONCE_SALT' ] );
/**
 * Custom Settings
 */
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'DISABLE_WP_CRON', $_ENV[ 'DISABLE_WP_CRON' ] ?: false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );
define( 'WP_CACHE_KEY_SALT', $_ENV['WP_CACHE_KEY_SALT'] );
define( 'WP_DEBUG_DISPLAY', false );
if ( isset ($_ENV[ 'WP_ENV' ]) && $_ENV[ 'WP_ENV' ] != 'production' ) {
	// Activated for staging only.
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'SAVEQUERIES', true );
} else {
	// Disable on live.
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_LOG', false );
	@ini_set( 'display_errors', 0 );
}
/* Redis cache config */
if ( empty( $redis_server ) ) {
	# Attempt to automatically load Pantheon's Redis config from the env.
	if ( null !== $_ENV['CACHE_HOST'] ) {
		$redis_server = array(
			'host' => $_ENV[ 'CACHE_HOST' ],
			'port' => $_ENV[ 'CACHE_PORT' ],
			'auth' => $_ENV[ 'CACHE_PASSWORD' ],
		);
	} else {
		$redis_server = array(
			'host' => '127.0.0.1',
			'port' => 6379,
		);
	}
}
if(isset($_ENV['MULTISITE']) && $_ENV['MULTISITE']==true)
{
	define( 'WP_ALLOW_MULTISITE', true );
	define('MULTISITE', true);
	define('SUBDOMAIN_INSTALL', $_ENV['SUBDOMAIN_INSTALL']);
	define('DOMAIN_CURRENT_SITE', $_ENV['DOMAIN_CURRENT_SITE']);
	define('PATH_CURRENT_SITE', '/');
	define('SITE_ID_CURRENT_SITE', 1);
	define('BLOG_ID_CURRENT_SITE', 1);
}

/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . '/wp-settings.php';
