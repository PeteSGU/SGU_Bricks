<?php
/**
 * Plugin class factory.
 *
 * @package WebDevStudios\WPSWAPro
 */

namespace WebDevStudios\WPSWAPro;

/**
 * WPSWA_Pro_Factory class
 *
 * Responsible for creating a shared instance of the main WPSWA_Pro object.
 *
 * @since 1.0.0
 */
class WPSWAPro_Factory {

	/**
	 * Create and return a shared instance of the WPSWA_Pro.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @return WPSWA_Pro The shared plugin instance.
	 */
	public static function create(): WPSWA_Pro {

		/**
		 * The static instance to share, else null.
		 *
		 * @since 1.0.0
		 *
		 * @var null|WPSWA_Pro $plugin
		 */
		static $plugin = null;

		if ( null !== $plugin ) {
			return $plugin;
		}

		$plugin = new WPSWA_Pro();

		return $plugin;
	}
}
