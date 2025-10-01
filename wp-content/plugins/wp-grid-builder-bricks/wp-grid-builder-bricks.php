<?php
/**
 * WP Grid Builder Bricks Add-on
 *
 * @package   WP Grid Builder - Bricks
 * @author    Loïc Blascos
 * @link      https://www.wpgridbuilder.com
 * @copyright 2019-2025 Loïc Blascos
 *
 * @wordpress-plugin
 * Plugin Name:  WP Grid Builder - Bricks
 * Plugin URI:   https://www.wpgridbuilder.com
 * Description:  Integrate WP Grid Builder with Bricks plugin.
 * Version:      1.3.5
 * Author:       Loïc Blascos
 * Author URI:   https://www.wpgridbuilder.com
 * License:      GPL-3.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:  wpgb-bricks
 * Domain Path:  /languages
 */

namespace WP_Grid_Builder_Bricks;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGB_BRICKS_VERSION', '1.3.5' );
define( 'WPGB_BRICKS_FILE', __FILE__ );
define( 'WPGB_BRICKS_BASE', plugin_basename( WPGB_BRICKS_FILE ) );
define( 'WPGB_BRICKS_PATH', plugin_dir_path( WPGB_BRICKS_FILE ) );
define( 'WPGB_BRICKS_URL', plugin_dir_url( WPGB_BRICKS_FILE ) );

require_once WPGB_BRICKS_PATH . 'includes/class-autoload.php';

/**
 * Load plugin text domain.
 *
 * @since 1.0.0
 */
function textdomain() {

	load_plugin_textdomain(
		'wpgb-bricks',
		false,
		basename( dirname( WPGB_BRICKS_FILE ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\textdomain' );

/**
 * Plugin compatibility notice.
 *
 * @since 1.0.0
 */
function plugin_notice() {

	$notice = __( '<strong>Gridbuilder ᵂᴾ - Bricks</strong> add-on requires at least <code>Gridbuilder ᵂᴾ v1.6.8</code>. Please update Gridbuilder ᵂᴾ to use Bricks add-on.', 'wpgb-bricks' );

	echo '<div class="error">' . wp_kses_post( wpautop( $notice ) ) . '</div>';

}

/**
 * Bricks compatibility notice.
 *
 * @since 1.0.0
 */
function bricks_notice() {

	$notice = __( '<strong>Gridbuilder ᵂᴾ - Bricks</strong> add-on requires at least <code>Bricks v1.5.5</code>. Please update and activate Bricks.', 'wpgb-bricks' );

	echo '<div class="error">' . wp_kses_post( wpautop( $notice ) ) . '</div>';

}

/**
 * Initialize plugin
 *
 * @since 1.0.0
 */
function loaded() {

	if ( version_compare( WPGB_VERSION, '1.6.8', '<' ) ) {

		add_action( 'admin_notices', __NAMESPACE__ . '\plugin_notice' );
		return;

	}

	new Includes\Plugin();

}
add_action( 'wp_grid_builder/loaded', __NAMESPACE__ . '\loaded' );
