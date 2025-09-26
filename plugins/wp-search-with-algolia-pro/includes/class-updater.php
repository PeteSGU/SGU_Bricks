<?php
/**
 * Updater Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro;

require_once WPSWA_PRO_PATH . 'vendor/autoload.php';
require_once WPSWA_PRO_PATH . 'vendor_prefixed/autoload.php';

/**
 * Class Updater
 *
 * @since 1.0.0
 */
class Updater {

	/**
	 * Domain to check for updates at.
	 *
	 * @var string
	 */
	private $store_url;

	/**
	 * License key to check.
	 *
	 * @var string
	 */
	private $license_key;

	/**
	 * Current installed version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Product name to check.
	 *
	 * @var string
	 */
	private $item_name;

	/**
	 * Constructor
	 *
	 * @param array $config Array of Updater configuration settings.
	 *
	 * @since 1.0.0
	 */
	public function __construct( array $config = [] ) {
		$this->store_url   = $config['store_url'];
		$this->license_key = $config['license_key'];
		$this->version     = $config['version'];
		$this->item_name   = $config['item_name'];
	}

	/**
	 * Run our updater process.
	 *
	 * @since 1.0.0
	 */
	public function do_update() {
		$edd_updater = new \EDD_SL_Plugin_Updater(
			$this->store_url,
			WPSWA_PRO_FILE,
			[
				'version'   => $this->version,
				'license'   => $this->license_key,
				'item_name' => $this->item_name,
				'author'    => 'Pluginize',
			]
		);
	}
}
