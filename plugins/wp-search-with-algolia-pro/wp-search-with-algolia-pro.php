<?php
/**
 * Plugin Name: WP Search with Algolia Pro
 * Plugin URI: https://pluginize.com
 * Description: Extra and enhanced functionality for WP Search with Algolia plugin
 * Version: 1.6.0
 * Author: Pluginize
 * Author URI: https://pluginize.com
 * License: GPLv2
 * Text Domain: wp-search-with-algolia-pro
 * Requires Plugins: wp-search-with-algolia
 *
 * @package WebDevStudios\WPSWAPro
 */

namespace WebDevStudios\WPSWAPro;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'WPSWA_PRO_PLUGIN_NAME', 'WP Search with Algolia Pro' );
define( 'WPSWA_PRO_PLUGIN_SLUG', 'wp-search-with-algolia-pro' );
define( 'WPSWA_PRO_VERSION', '1.6.0' );
define( 'WPSWA_PRO_STORE_URL', 'https://pluginize.com' );
define( 'WPSWA_PRO_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPSWA_PRO_URL', plugin_dir_url( __FILE__ ) );
define( 'WPSWA_PRO_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPSWA_PRO_FILE', __FILE__ );
define( 'WPSWA_PRO_DIR', __DIR__ );

/**
 * Require autoloader.
 *
 * @since 1.0.0
 */
require_once WPSWA_PRO_PATH . 'vendor/autoload.php';
require_once WPSWA_PRO_PATH . 'vendor_prefixed/autoload.php';

/**
 * Create the plugin instance and run hooks.
 *
 * @since 1.0.0
 */
$wpswa_pro = WPSWAPro_Factory::create();
$wpswa_pro->do_hooks();

/**
 * Run uninstall process.
 *
 * @since 1.0.0
 */
function uninstall() {
	$uninstall = new Uninstall();
	$uninstall->run();
}
register_uninstall_hook( __FILE__, __NAMESPACE__ . '\uninstall' );

/**
 * If WP Search with Algolia is not available,
 * render an admin notice, and deactivate,
 * so the site will continue to function.
 *
 * @since 1.0.0
 */
add_action(
	'plugins_loaded',
	function() {

		if ( ! defined( 'ALGOLIA_VERSION' ) ) {

			add_action(
				'all_admin_notices',
				function() {
					$error_text = __(
						'WP Search with Algolia Pro requires WP Search with Algolia to be active. Please install and activate that plugin.',
						'wp-search-with-algolia-pro'
					);
					echo '<div class="notice notice-error"><p>' . esc_html( $error_text ) . '</p></div>';
				}
			);

			add_action(
				'admin_init',
				function () {
					deactivate_plugins(
						plugin_basename( WPSWA_PRO_FILE )
					);
				}
			);

			// Don't show the activation message since the plugin could not be activated.
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
);
