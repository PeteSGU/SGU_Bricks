<?php
/**
 * Settings_Network Class file
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro\Admin;

use WebDevStudios\WPSWAPro\WPSWAPro_Factory;
use WebDevStudios\WPSWAPro\Utils;

/**
 * Class Settings_Network
 * @since 1.0.0
 */
class Settings_Network {

	/**
	 * Settings slug.
	 * @var string
	 */
	private string $slug = 'wpswa_pro_network';

	/**
	 * Option group slug.
	 * @var string
	 */
	private string $option_group = 'wpswa_pro_network';

	/**
	 * Network settings section slug.
	 * @var string
	 */
	private string $network_section = 'network';

	/**
	 * Minimum capability needed to interact with our options.
	 * @var string
	 */
	private string $capability = 'manage_options';

	/**
	 * Constructor
	 * @since 1.0.0
	 */
	public function __construct() {
		add_option( 'wpswa_pro_network_wide_indexing' );
	}

	/**
	 * Execute our hooks for network based settings.
	 *
	 * @since 1.3.0
	 */
	public function do_hooks() {
		add_action( 'network_admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_init', [ $this, 'add_settings' ] );

		add_filter( 'wpswa_pro_option_keys', [ $this, 'registered_options' ] );

		add_action( 'wpswa_pro_network_before_settings', [ $this, 'network_status_list' ], 10 );
		add_action( 'wpswa_pro_network_before_settings', [ $this, 'network_index_controls' ], 15 );
		add_action( 'network_admin_edit_wpswa_pro_network_settings', [ $this, 'save_network_settings' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'localize_scripts' ] );

		if ( class_exists( '\Algolia_Plugin_Factory' ) ) {
			$wpswa_free = Utils::get_wpswa( \Algolia_Plugin_Factory::create() );
			new Settings_Admin_Page_Network_Settings();
			if ( Utils::get_network_api_is_reachable() ) {
				// Leaving in case we decide to add an network admin for autocomplete in a future release.
				//$autocomplete_config = new \Algolia_Autocomplete_Config( $wpswa_free );
				//new Settings_Admin_Page_Network_Autocomplete( $wpswa_free->get_settings(), $autocomplete_config );
				new Settings_Admin_Page_Network_Native_Search( $wpswa_free );
			}
		}
	}

	public function registered_options( array $options = [] ): array {
		foreach (
			[
				'wpswa_pro_network_wide_indexing',
				'wpswa_pro_default_meta_fields',
			] as $key
		) {
			$options[] = $key;
		}

		return $options;
	}

	public function add_page() {
		add_menu_page(
			'WP Search with Algolia',
			esc_html__( 'Algolia Search', 'wp-search-with-algolia-pro' ),
			'manage_options',
			$this->slug,
			[ $this, 'display_page' ],
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MDAgNTAwLjM0Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzAwM2RmZjt9PC9zdHlsZT48L2RlZnM+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMjUwLDBDMTEzLjM4LDAsMiwxMTAuMTYsLjAzLDI0Ni4zMmMtMiwxMzguMjksMTEwLjE5LDI1Mi44NywyNDguNDksMjUzLjY3LDQyLjcxLC4yNSw4My44NS0xMC4yLDEyMC4zOC0zMC4wNSwzLjU2LTEuOTMsNC4xMS02LjgzLDEuMDgtOS41MmwtMjMuMzktMjAuNzRjLTQuNzUtNC4yMi0xMS41Mi01LjQxLTE3LjM3LTIuOTItMjUuNSwxMC44NS01My4yMSwxNi4zOS04MS43NiwxNi4wNC0xMTEuNzUtMS4zNy0yMDIuMDQtOTQuMzUtMjAwLjI2LTIwNi4xLDEuNzYtMTEwLjMzLDkyLjA2LTE5OS41NSwyMDIuOC0xOTkuNTVoMjAyLjgzVjQwNy42OGwtMTE1LjA4LTEwMi4yNWMtMy43Mi0zLjMxLTkuNDMtMi42Ni0xMi40MywxLjMxLTE4LjQ3LDI0LjQ2LTQ4LjU2LDM5LjY3LTgxLjk4LDM3LjM2LTQ2LjM2LTMuMi04My45Mi00MC41Mi04Ny40LTg2Ljg2LTQuMTUtNTUuMjgsMzkuNjUtMTAxLjU4LDk0LjA3LTEwMS41OCw0OS4yMSwwLDg5Ljc0LDM3Ljg4LDkzLjk3LDg2LjAxLC4zOCw0LjI4LDIuMzEsOC4yOCw1LjUzLDExLjEzbDI5Ljk3LDI2LjU3YzMuNCwzLjAxLDguOCwxLjE3LDkuNjMtMy4zLDIuMTYtMTEuNTUsMi45Mi0yMy42LDIuMDctMzUuOTUtNC44My03MC4zOS02MS44NC0xMjcuMDEtMTMyLjI2LTEzMS4zNS04MC43My00Ljk4LTE0OC4yMyw1OC4xOC0xNTAuMzcsMTM3LjM1LTIuMDksNzcuMTUsNjEuMTIsMTQzLjY2LDEzOC4yOCwxNDUuMzYsMzIuMjEsLjcxLDYyLjA3LTkuNDIsODYuMi0yNi45N2wxNTAuMzYsMTMzLjI5YzYuNDUsNS43MSwxNi42MiwxLjE0LDE2LjYyLTcuNDhWOS40OUM1MDAsNC4yNSw0OTUuNzUsMCw0OTAuNTEsMEgyNTBaIi8+PC9zdmc+'
		);
	}

	public function add_settings() {
		$this->add_network_section();
	}

	private function add_network_section() {
		add_settings_section(
			$this->network_section,
			esc_html__( 'Network settings', 'wp-search-with-algolia-pro' ),
			[ $this, 'network_content_callback' ],
			$this->slug
		);

		add_settings_field(
			'wpswa_pro_network_wide_indexing',
			esc_html__( 'Network-wide indexing', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->network_section,
			[
				'label_for' => 'wpswa_pro_network_wide_indexing',
				'helptext' => esc_html__( 'This setting enables pushing searchable content from all sites in the network to one Algolia index', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_network_wide_indexing',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		add_settings_field(
			'wpswa_pro_default_meta_fields',
			esc_html__( 'Default meta fields', 'wp-search-with-algolia-pro' ),
			[ $this, 'text' ],
			$this->slug,
			$this->network_section,
			[
				'label_for' => 'wpswa_pro_default_meta_fields',
				'helptext' => esc_html__( 'Standard WordPress meta keys, comma separated.', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_default_meta_fields',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);
	}

	/**
	 * Load an external PHP file to render our final settings page result.
	 * @since 1.3.0
	 */
	public function display_page() {
		require_once WPSWA_PRO_PATH . 'includes/admin/partials/page-network.php';
	}

	/**
	 * Callback to render our checkbox.
	 *
	 * @param array $args Array of extra arguments for checkbox callback.
	 *
	 * @since 1.3.0
	 */
	public function checkbox( array $args ) {
		$value       = get_site_option( $args['label_for'], '' );
		$disabled    = '';
		$helptext    = ! empty( $args['helptext'] ) ? esc_html( $args['helptext'] ) : '';
		$extra_label = ! empty( $args['extra_label'] ) ? $args['extra_label'] : '';

		$field = '<input type="checkbox" id="%1$s" name="%1$s" value="yes" %2$s %3$s /><label for="%1$s">%4$s</label><p class="description">%5$s</p>';

		printf(
			$field,
			esc_attr( $args['label_for'] ),
			checked( $value, 'yes', false ),
			disabled( $disabled ),
			esc_html( $extra_label ),
			esc_html( $helptext )
		);
	}

	public function text( array $args ) {
		$value    = get_site_option( $args['label_for'], '' );
		$filtered = Utils::get_filtered_default_network_meta_keys();
		$disabled = '';
		if ( ! empty( $filtered ) ) {
			$value = $filtered;
			$disabled = ' disabled';
		}
		$helptext = ! empty( $args['helptext'] ) ? $args['helptext'] : '';

		$field = '<label for="%1$s"><input class="regular-text" type="text" id="%1$s" name="%1$s" value="%2$s" %3$s /></label><p class="descripton">%4$s</p>';
		printf(
			$field,
			esc_attr( $args['label_for'] ),
			esc_attr( $value ),
			esc_attr( $disabled ),
			esc_html( $helptext )
		);
	}

	/**
	 * Callback to render content between the heading and the options themselves.
	 * @since 1.3.0
	 */
	public function network_content_callback() {}

	public function get_network_settings(): array {
		$all_settings = [];
		foreach (
			[
				'wpswa_pro_network_wide_indexing',
				'wpswa_pro_default_meta_fields',
			]
			as $option
		) {
			$all_settings[ $option ] = get_site_option( $option, '' );
		}

		return $all_settings;
	}

	/**
	 * Get the template to display the network list.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function network_status_list() {
		require WPSWA_PRO_PATH . 'includes/admin/partials/page-network-list.php';
	}

	/**
	 * Get the template to display the network buttons and index status table.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function network_index_controls() {
		require WPSWA_PRO_PATH . 'includes/admin/partials/page-network-index-controls.php';
	}

	/**
	 * Get the button template.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 *
	 * @param string $button_type The type of button to get. create_network_index|recreate_network_index|resume_network_index.
	 */
	public function get_network_index_button_template( string $button_type ) {
		switch ( $button_type ) {
			case 'create_network_index':
				require WPSWA_PRO_PATH . 'includes/admin/partials/button-create-network-index.php';
				break;
			case 'recreate_network_index':
				require WPSWA_PRO_PATH . 'includes/admin/partials/button-recreate-network-index.php';
				break;
			case 'resume_network_index':
				require WPSWA_PRO_PATH . 'includes/admin/partials/button-resume-network-index.php';
				break;
		}
	}

	public function save_network_settings() {
		check_admin_referer( 'wpswa_pro_network-options' );

		$settings = $this->registered_options();

		foreach( $settings as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$setting = sanitize_text_field( $_POST[ $key ] );
				update_site_option( $key, $setting );
			} else {
				update_site_option( $key, '' );
			}
		}

		wp_redirect( add_query_arg( [
			'page'    => 'wpswa_pro_network',
			'updated' => 'true'
		], network_admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 */
	public function enqueue_scripts() {
		if ( Utils::network_wide_indexing_enabled() ) {
			wp_enqueue_script(
				'wpswapro-admin-index-network-button',
				plugin_dir_url( __FILE__ ) . 'js/index-network-button.js',
				[ 'jquery' ],
				WPSWA_PRO_VERSION,
				false
			);

			wp_enqueue_script(
				'wpswapro-admin-index-network-site-button',
				plugin_dir_url( __FILE__ ) . 'js/index-network-site-button.js',
				[ 'jquery' ],
				WPSWA_PRO_VERSION,
				false
			);
		}
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.4.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'wpswap-admin',
			plugin_dir_url( __FILE__ ) . 'css/wpswap-admin.css',
			[],
			WPSWA_PRO_VERSION
		);
	}

	/**
	 * Add data for scripts.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function localize_scripts() {

		if ( Utils::network_wide_indexing_enabled() ) {
			$wpswa_pro = WPSWAPro_Factory::create();

			$network_batch_status = $wpswa_pro->network_index_manager->get_network_batch_status(
				Utils::get_network_and_visibilities(),
				$wpswa_pro->network_index_manager->get_network_batch_id()
			);

			/**
			 * Filters the arguments for network wide ajax index settings.
			 *
			 * @since 1.6.0
			 *
			 * @param array $value Array of values to use with our javascript.
			 */
			$network_wide_button_args = apply_filters( 'wpswa_pro_network_wide_button_args', [
				'strings'       => [
					'network_index_status_complete'          => $wpswa_pro->network_index_manager->get_text_to_display( 'network_index_status_complete' ),
					'network_index_status_incomplete'        => $wpswa_pro->network_index_manager->get_text_to_display( 'network_index_status_incomplete' ),
					'network_index_status_preparing'         => $wpswa_pro->network_index_manager->get_text_to_display( 'network_index_status_preparing' ),
					'network_index_status_indexing'          => $wpswa_pro->network_index_manager->get_text_to_display( 'network_index_status_indexing' ),
					'network_index_status_undefined'         => $wpswa_pro->network_index_manager->get_text_to_display( 'network_index_status_undefined' ),
					'dialog_confirm_overwrite_network_index' => $wpswa_pro->network_index_manager->get_text_to_display( 'dialog_confirm_overwrite_network_index' ),
				],
				'main_site_id'  => get_main_site_id(),
				'main_site_url' => get_home_url( get_main_site_id() ),
				'rest_endpoint' => $wpswa_pro->network_index_manager->get_index_network_endpoint(),
				'rest_nonce'    => wp_create_nonce( 'wp_rest' ),
				'timeout'       => 0
			] );

			wp_localize_script(
				'wpswapro-admin-index-network-button',
				'wpswaProNetworkIndexManager',
				array_merge(
					$network_wide_button_args,
					$network_batch_status
				)
			);

			wp_localize_script(
				'wpswapro-admin-index-network-site-button',
				'wpswaProNetworkIndexManagerSite',
				array_merge(
					[
						'strings' => [
							'dialog_confirm_overwrite_network_site_index' => $wpswa_pro->network_index_manager->get_text_to_display( 'dialog_confirm_overwrite_network_site_index' ),
						],
					],
					$network_batch_status
				)
			);
		}
	}
}
