<?php
/**
 * Uninstall Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro;

/**
 * Class Uninstall
 *
 * @since 1.0.0
 */
class Uninstall {

	/**
	 * Method to execute the uninstall process.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->delete_options();
	}

	/**
	 * Iterate over available options and remove from database.
	 *
	 * @since 1.0.0
	 */
	private function delete_options() {
		foreach ( $this->get_option_names() as $option_name ) {
			delete_option( $option_name );
		}
	}

	/**
	 * Return an array of all current option keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_option_names(): array {
		return array_unique(
			(array) apply_filters(
				'wpswa_pro_option_keys',
				[]
			)
		);
	}
}
