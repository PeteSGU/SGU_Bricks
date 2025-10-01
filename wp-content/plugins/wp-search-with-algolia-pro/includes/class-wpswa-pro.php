<?php
/**
 * WPSWA_Pro Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro;

use WebDevStudios\WPSWAPro\Admin\Settings_License_Page;
use WebDevStudios\WPSWAPro\Admin\Settings_Network;
use WebDevStudios\WPSWAPro\Admin\Settings_Post_Type_Meta;
use WebDevStudios\WPSWAPro\Admin\Settings_SEO;
use WebDevStudios\WPSWAPro\Admin\Settings_WooCommerce;
use WebDevStudios\WPSWAPro\Hooks\Hooks_SEO;
use WebDevStudios\WPSWAPro\Hooks\Hooks_WooCommerce;
use WebDevStudios\WPSWAPro\Hooks\Hooks_Network_Wide;

/**
 * Class WPSWA_Pro
 *
 * @since 1.0.0
 */
final class WPSWA_Pro {

	/**
	 * WooCommerce Settings instance.
	 *
	 * @var Settings_WooCommerce
	 */
	private $settings_woocommerce;

	/**
	 * WooCommerce Hooks instance.
	 *
	 * @var Hooks_WooCommerce
	 */
	private $hooks_woocommerce;

	/**
	 * SEO Settings instance.
	 *
	 * @var Settings_SEO
	 */
	private $settings_seo;

	/**
	 * SEO Hooks instance.
	 *
	 * @var Hooks_SEO
	 */
	private $hooks_seo;

	/**
	 * Network settings instance.
	 *
	 * @var Settings_Network
	 */
	public $settings_network;

	/**
	 * Network Index Manager instance.
	 *
	 * @var Network_Index_Manager
	 */
	public $network_index_manager;

	/**
	 * Network hooks.
	 *
	 * @var Hooks_Network_Wide
	 */
	private $network_hooks;

	/**
	 * Post type meta instance.
	 *
	 * @var Settings_Post_Type_Meta
	 */
	private $post_type_meta;

	/**
	 * Executes our hooks to wire everything up.
	 *
	 * @since 1.0.0
	 */
	public function do_hooks() {
		// Maybe load plugin.php because it's not loaded until the admin_init hook.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Bail if the WP Search with Algolia (free) plugin is not active.
		if ( function_exists( '\\is_plugin_active' ) && ! \is_plugin_active( 'wp-search-with-algolia/algolia.php' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'updater' ] );
		add_action( 'init', [ $this, 'load_classes' ] );

		add_action( 'admin_init', [ $this, 'remove_pro_submenu' ], 11 );

		do_action( 'wpswa_pro_loaded' );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_insights_scripts_for_woocommerce' ] );
	}

	/**
	 * Enqueue Algolia insight events script.
	 *
	 * @since 1.6.0
	 */
	public function enqueue_insights_scripts_for_woocommerce() {
		// Check if insights are enabled and WooCommerce is active
		if ( 'yes' !== get_option( 'algolia_insights_enabled' ) ) {
			return;
		}

		// Maybe load plugin.php because it's not loaded until the admin_init hook.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( ! function_exists( 'is_plugin_active' ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}

		add_action( 'algolia_autocomplete_scripts', [ $this, 'add_the_algolia_insight_events_scripts' ] );
	}

	/**
	 * Enqueue Algolia insight events script.
	 *
	 * @since 1.6.0
	 */
	public function add_the_algolia_insight_events_scripts() {
		// Get WooCommerce cart items data.
		$products = $this->get_wc_cart_items();

		// Get user data if logged in
		$user_data = [];

		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$user_data = array(
				'user_id' => $current_user->ID,
				'user_email' => $current_user->user_email,
				'user_hash' => md5(strtolower(trim($current_user->user_email))),
			);
		}

		// Get WooCommerce currency data
		$currency_data = array(
			'currency' => get_woocommerce_currency(),
			'currency_symbol' => get_woocommerce_currency_symbol(),
			'currency_pos' => get_option('woocommerce_currency_pos'),
			'price_decimal_sep' => wc_get_price_decimal_separator(),
			'price_thousand_sep' => wc_get_price_thousand_separator(),
			'price_decimals' => wc_get_price_decimals(),
		);
		// Add Algolia index name prefix to the data
		$extra_data = array(
			'algolia_index_name_prefix' => get_option( 'algolia_index_name_prefix' ),
		);

		wp_enqueue_script(
			'wds-algolia-search',
			WPSWA_PRO_URL . '/build/index.js',
			array(
				'jquery',
				'algolia-instantsearch',
				'algolia-autocomplete'
			),
			WPSWA_PRO_VERSION,
			true
		);

		// User, currenty and item (cart) details.
		wp_localize_script(
			'wds-algolia-search',
			'wpswapWCData',
			array_merge( $user_data, $currency_data, $extra_data )
		);

		// Products details.
		wp_localize_script(
			'wds-algolia-search',
			'wpswapWCProducts',
			$products
		);
	}

	/**
	 * Load all of our classes.
	 *
	 * @since 1.0.0
	 */
	public function load_classes() {
		// Maybe load plugin.php because it's not loaded until the admin_init hook.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$license_page = new Settings_License_Page(
			WPSWA_PRO_PLUGIN_NAME,
			WPSWA_PRO_PLUGIN_SLUG
		);
		$license_page->do_hooks();

		$this->settings_woocommerce = new Settings_WooCommerce();
		$this->settings_woocommerce->do_hooks();
		$woocommerce_options['options'] = $this->settings_woocommerce->get_woocommerce_settings();

		if ( function_exists( '\\is_plugin_active' ) && \is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$this->hooks_woocommerce = new Hooks_WooCommerce( $woocommerce_options );
			$this->hooks_woocommerce->do_hooks();
		}

		$this->settings_seo = new Settings_SEO();
		$this->settings_seo->do_hooks();
		$seo_options['options'] = $this->settings_seo->get_seo_settings();

		$this->hooks_seo = new Hooks_SEO( $seo_options );
		$this->hooks_seo->do_hooks();

		if (
			is_plugin_active_for_network(
				'wp-search-with-algolia-pro/wp-search-with-algolia-pro.php'
			)
		) {
			$this->settings_network = new Settings_Network();
			$this->settings_network->do_hooks();

			if ( Utils::network_wide_indexing_enabled() ) {
				$this->network_hooks = new Hooks_Network_Wide();
				$this->network_hooks->do_hooks();

				$this->network_index_manager = new Network_Index_Manager();
				$this->network_index_manager->do_hooks();

				$this->post_type_meta = new Settings_Post_Type_Meta();
				$this->post_type_meta->do_hooks();
			}

		}
	}

	/**
	 * Fire up our updater processes.
	 *
	 * @since 1.0.0
	 */
	public function updater() {
		$config          = [
			'license_key' => get_option( WPSWA_PRO_PLUGIN_SLUG . '_license_key', '' ),
			'store_url'   => WPSWA_PRO_STORE_URL,
			'version'     => WPSWA_PRO_VERSION,
			'item_name'   => WPSWA_PRO_PLUGIN_NAME,
		];
		$license_handler = new Updater( $config );
		$license_handler->do_update();
	}

	/**
	 * Remove our "Upgrade to Pro" submenu item.
	 *
	 * @since 1.0.0
	 */
	public function remove_pro_submenu() {
		global $submenu;

		if ( empty( $submenu['algolia'] ) ) {
			return;
		}

		foreach ( $submenu['algolia'] as $index => $item ) {
			if ( false !== strpos( $item[2], 'algolia-pro-upgrade' ) ) {
				unset( $submenu['algolia'][ $index ] );
				break;
			}
		}
	}

	/**
	 * Get WooCommerce cart items data in a structured format.
	 *
	 * @since 1.6.0
	 * @return array Array of cart items with their details. Empty array if cart is not available.
	 */
	private function get_wc_cart_items() {
		// Check if WC() exists and cart is available
		if ( ! function_exists( 'WC' ) || ! ( WC()?->cart instanceof \WC_Cart ) ) {
			return [];
		}

		$cart_items = [];

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			if ( ! $product ) {
				continue;
			}

			$cart_items['items'][$cart_item_key] = [
				'product_id' => $cart_item['product_id'],
				'variation_id' => $cart_item['variation_id'],
				'quantity' => $cart_item['quantity'],
				'price' => wc_get_price_including_tax( $product ),
				'subtotal' => WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ),
				'permalink' => $product->get_permalink( $cart_item ),
				'total' => WC()->cart->total,
				'name' => $product->get_name(),
				'sku' => $product->get_sku(),
				'attributes' => [],
				'meta_data' => wc_get_formatted_cart_item_data( $cart_item ),
			];


			// Get product attributes
			$attributes = $product->get_attributes();
			foreach ( $attributes as $attribute => $value ) {
				$cart_items['items'][$cart_item_key]['attributes'][$attribute] = [
					'value' => $product->get_attribute( $attribute ),
					'variation' => isset( $cart_item['variation'][ 'attribute_' . $attribute ] )
						? $cart_item['variation'][ 'attribute_' . $attribute ]
						: '',
				];
			}
		}

		$cart_items['total'] = WC()->cart->total;

		return $cart_items;
	}
}
