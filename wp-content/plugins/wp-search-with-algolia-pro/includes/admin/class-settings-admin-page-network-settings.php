<?php

namespace WebDevStudios\WPSWAPro\Admin;

use WebDevStudios\WPSWAPro\Utils;

class Settings_Admin_Page_Network_Settings {

	/**
	 * Admin page slug.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 * @var string
	 */
	private $slug = 'wpswa_pro_network_account_settings';

	/**
	 * Admin page capabilities.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Admin page section.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 * @var string
	 */
	private $section = 'algolia_network_section_settings';

	/**
	 * Admin page option group.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 * @var string
	 */
	private $option_group = 'algolia_network_settings';

	/**
	 * Algolia_Admin_Page_Settings constructor.
	 *
	 * @param \Algolia_Plugin $plugin The Algolia_Plugin instance.
	 *
	 * @since  1.3.0
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function __construct() {
		add_action( 'network_admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_init', [ $this, 'add_settings' ] );
		add_action( 'admin_notices', [ $this, 'display_errors' ] );

		add_action( 'network_admin_edit_wpswa_pro_network_account_settings', [ $this, 'save_network_account_settings' ] );

		if ( Utils::network_wide_indexing_enabled() ) {
			add_action( 'admin_menu', [ $this, 'remove_submenus' ], 11 );

			foreach (
				[
					'pre_option_algolia_search_api_key',
					'pre_option_algolia_application_id',
					'pre_option_algolia_search_api_key',
					'pre_option_algolia_api_key',
					'pre_option_algolia_index_name_prefix',
					'pre_option_algolia_powered_by_enabled',
					'pre_option_algolia_api_is_reachable',
					'pre_option_algolia_autocomplete_enabled',
					'pre_option_algolia_override_native_search',
				] as $key
			) {
				add_filter( $key, [ $this, 'override_option_with_network_value' ], 10, 3 );
			}
		}
	}

	public function remove_submenus() {
		remove_submenu_page( 'algolia', 'algolia-search-page' ); // search
		remove_submenu_page( 'algolia', 'algolia' ); // autocomplete
	}

	/**
	 * Add admin menu page.
	 * @return string|void The resulting page's hook_suffix.
	 * @since  1.3.0
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function add_page() {
		add_submenu_page(
			'wpswa_pro_network',
			esc_html__( 'WP Search with Algolia Settings', 'wp-search-with-algolia-pro' ),
			esc_html__( 'Settings', 'wp-search-with-algolia-pro' ),
			$this->capability,
			$this->slug,
			[ $this, 'display_page' ]
		);
	}

	/**
	 * Add settings.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function add_settings() {
		add_settings_section(
			$this->section,
			null,
			[ $this, 'print_section_settings' ],
			$this->slug
		);

		add_settings_field(
			'algolia_network_application_id',
			esc_html__( 'Application ID', 'wp-search-with-algolia-pro' ),
			[ $this, 'application_id_callback' ],
			$this->slug,
			$this->section
		);

		add_settings_field(
			'algolia_network_search_api_key',
			esc_html__( 'Search-only API key', 'wp-search-with-algolia-pro' ),
			[ $this, 'search_api_key_callback' ],
			$this->slug,
			$this->section
		);

		add_settings_field(
			'algolia_network_api_key',
			esc_html__( 'Admin API key', 'wp-search-with-algolia-pro' ),
			[ $this, 'api_key_callback' ],
			$this->slug,
			$this->section
		);

		add_settings_field(
			'algolia_network_index_name_prefix',
			esc_html__( 'Index name prefix', 'wp-search-with-algolia-pro' ),
			[ $this, 'index_name_prefix_callback' ],
			$this->slug,
			$this->section
		);

		add_settings_field(
			'algolia_network_powered_by_enabled',
			esc_html__( 'Remove Algolia powered by logo', 'wp-search-with-algolia-pro' ),
			[ $this, 'powered_by_enabled_callback' ],
			$this->slug,
			$this->section
		);

		register_setting( $this->option_group, 'algolia_network_application_id', [ $this, 'sanitize_application_id' ] );
		register_setting( $this->option_group, 'algolia_network_search_api_key', [ $this, 'sanitize_search_api_key' ] );
		register_setting( $this->option_group, 'algolia_network_api_key', [ $this, 'sanitize_api_key' ] );
		register_setting( $this->option_group, 'algolia_network_index_name_prefix', [
			$this,
			'sanitize_index_name_prefix'
		] );
		register_setting( $this->option_group, 'algolia_network_powered_by_enabled', [
			$this,
			'sanitize_powered_by_enabled'
		] );
	}

	/**
	 * Application ID callback.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function application_id_callback() {
		$setting       = Utils::get_network_application_id();
		$disabled_html = Utils::is_network_application_id_in_config() ? ' disabled' : '';
		?>
		<input type="text" name="algolia_network_application_id" class="regular-text" value="<?php echo esc_attr( $setting ); ?>" <?php echo esc_html( $disabled_html ); ?>/>
		<p class="description" id="home-description">
			<?php esc_html_e( 'Your Algolia Application ID.', 'wp-search-with-algolia-pro' ); ?>
		</p>
		<?php
	}

	/**
	 * Search API key callback.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function search_api_key_callback() {
		$setting       = Utils::get_network_search_api_key();
		$disabled_html = Utils::is_network_search_api_key_in_config() ? ' disabled' : '';
		?>
		<input type="text" name="algolia_network_search_api_key" class="regular-text" value="<?php echo esc_attr( $setting ); ?>" <?php echo esc_html( $disabled_html ); ?>/>
		<p class="description" id="home-description">
			<?php esc_html_e( 'Your Algolia Search-only API key (public).', 'wp-search-with-algolia-pro' ); ?>
		</p>
		<?php
	}

	/**
	 * Admin API key callback.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function api_key_callback() {
		$setting       = Utils::get_network_api_key();
		$disabled_html = Utils::is_network_api_key_in_config() ? ' disabled' : '';
		?>
		<input type="password" name="algolia_network_api_key" class="regular-text" value="<?php echo esc_attr( $setting ); ?>" <?php echo esc_html( $disabled_html ); ?>/>
		<p class="description" id="home-description">
			<?php esc_html_e( 'Your Algolia ADMIN API key (kept private).', 'wp-search-with-algolia-pro' ); ?>
		</p>
		<?php
	}

	/**
	 * Index name prefix callback.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function index_name_prefix_callback() {
		$index_name_prefix = Utils::get_network_index_name_prefix();
		$disabled_html     = Utils::is_network_index_name_prefix_in_config() ? ' disabled' : '';
		?>
		<input type="text" name="algolia_network_index_name_prefix" value="<?php echo esc_attr( $index_name_prefix ); ?>" <?php echo esc_html( $disabled_html ); ?>/>
		<p class="description" id="home-description"><?php esc_html_e( 'This prefix will be prepended to your index names.', 'wp-search-with-algolia-pro' ); ?></p>
		<?php
	}

	/**
	 * Powered by enabled callback.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function powered_by_enabled_callback() {
		$powered_by_enabled = Utils::is_network_powered_by_enabled();
		$checked            = '';
		if ( ! $powered_by_enabled ) {
			$checked = ' checked';
		}
		echo "<input type='checkbox' name='algolia_network_powered_by_enabled' value='no' " . esc_html( $checked ) . ' />' .
		     '<p class="description" id="home-description">' . esc_html( __( 'This will remove the Algolia logo from the autocomplete and the search page. Algolia requires that you keep the logo if you are using a free plan.', 'wp-search-with-algolia-pro' ) ) . '</p>';
	}

	/**
	 * Sanitize application ID.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function sanitize_application_id( $value ) {
		if ( Utils::is_network_application_id_in_config() ) {
			$value = Utils::get_network_application_id();
		}
		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			Utils::set_network_api_is_reachable( false );
			add_settings_error(
				$this->option_group,
				'empty',
				esc_html__( 'Application ID should not be empty.', 'wp-search-with-algolia-pro' )
			);

		}

		return $value;
	}

	/**
	 * Sanitize search API key.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function sanitize_search_api_key( $value ) {
		if ( Utils::is_network_search_api_key_in_config() ) {
			$value = Utils::get_network_search_api_key();
		}
		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			Utils::set_network_api_is_reachable( false );
			add_settings_error(
				$this->option_group,
				'empty',
				esc_html__( 'Search-only API key should not be empty.', 'wp-search-with-algolia-pro' )
			);
		}

		return $value;
	}

	/**
	 * Sanitize Admin API key.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function sanitize_api_key( $value ) {
		if ( Utils::is_network_api_key_in_config() ) {
			$value = Utils::get_network_api_key();
		}
		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			add_settings_error(
				$this->option_group,
				'empty',
				esc_html__( 'API key should not be empty', 'wp-search-with-algolia-pro' )
			);
		}

		$errors = get_settings_errors( $this->option_group );

		// @todo Not 100% clear why this is returning here.
		if ( ! empty( $errors ) ) {
			return $value;
		}

		$valid_credentials = true;
		try {
			\Algolia_API::assert_valid_credentials( Utils::get_network_application_id(), $value );
		} catch ( \Exception $exception ) {
			$valid_credentials = false;
			add_settings_error(
				$this->option_group,
				'login_exception',
				$exception->getMessage()
			);
		}

		if ( ! $valid_credentials ) {
			add_settings_error(
				$this->option_group,
				'no_connection',
				esc_html__(
					'We were unable to authenticate you against the Algolia servers with the provided information. Please ensure that you used a valid Application ID and Admin API key.',
					'wp-search-with-algolia-pro'
				)
			);
			Utils::set_network_api_is_reachable( false );
		} else {
			if ( ! \Algolia_API::is_valid_search_api_key( Utils::get_network_application_id(), Utils::get_network_search_api_key() ) ) {
				add_settings_error(
					$this->option_group,
					'wrong_search_API_key',
					esc_html__(
						'It looks like your search API key is wrong. Ensure that the key you entered has only the search capability and nothing else. Also ensure that the key has no limited time validity.',
						'wp-search-with-algolia-pro'
					)
				);
				Utils::set_network_api_is_reachable( false );
			} else {
				add_settings_error(
					$this->option_group,
					'connection_success',
					esc_html__( 'Connection to the Algolia servers was succesful! Configure your Search Page to start using Algolia!', 'wp-search-with-algolia-pro' ),
					'updated'
				);
				Utils::set_network_api_is_reachable( true );
			}
		}

		return $value;
	}

	/**
	 * Determine if the index name prefix is valid.
	 *
	 * @param string $index_name_prefix The index name prefix.
	 *
	 * @return bool
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function is_valid_index_name_prefix( $index_name_prefix ) {
		$to_validate = str_replace( '_', '', $index_name_prefix );

		return ctype_alnum( $to_validate );
	}

	/**
	 * Sanitize the index name prefix.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return bool|mixed|string|void
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function sanitize_index_name_prefix( $value ) {
		if ( Utils::is_network_index_name_prefix_in_config() ) {
			$value = Utils::get_network_index_name_prefix();
		}

		if ( $this->is_valid_index_name_prefix( $value ) ) {
			return $value;
		}

		add_settings_error(
			$this->option_group,
			'wrong_prefix',
			esc_html__( 'Indices prefix can only contain alphanumeric characters and underscores.', 'wp-search-with-algolia-pro' )
		);

		$value = get_site_option( 'algolia_network_index_name_prefix' );

		return $this->is_valid_index_name_prefix( $value ) ? $value : 'wp_network_';
	}

	/**
	 * Sanitize the powered by enabled setting.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function sanitize_powered_by_enabled( $value ) {
		return 'no' === $value ? 'no' : 'yes';
	}

	/**
	 * Display the page.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function display_page() {
		require_once dirname( __FILE__ ) . '/partials/form-options.php';
	}

	/**
	 * Display errors.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function display_errors() {
		settings_errors( $this->option_group );
	}

	/**
	 * Print the settings section.
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function print_section_settings() {
		echo '<p>' .
		     wp_kses(
			     sprintf(
			     // translators: URL to API keys section in Algolia dashboard.
				     __( 'Configure your Algolia account credentials. You can find them in the <a href="%s" target="_blank">API Keys</a> section of your Algolia dashboard.', 'wp-search-with-algolia-pro' ),
				     'https://dashboard.algolia.com/account/api-keys/all'
			     ),
			     [
				     'a' => [
					     'href'   => [],
					     'target' => [],
				     ],
			     ]
		     ) . '</p>';
		// translators: the placeholder contains the URL to Algolia's website.
		echo '<p>' . wp_kses_post( sprintf( __( 'No Algolia account yet? <a href="%s">Follow this link</a> to create one for free in a couple of minutes!', 'wp-search-with-algolia-pro' ), 'https://dashboard.algolia.com/users/sign_up' ) ) . '</p>';

		echo '<p>' . esc_html__( 'Once you provide your Algolia Application ID and API key, this plugin will be able to securely communicate with Algolia servers.', 'wp-search-with-algolia-pro' ) . ' ' . esc_html__( 'We ensure your information is correct by testing them against the Algolia servers upon save.', 'wp-search-with-algolia-pro' ) . '</p>';
		?>
		<a href="https://dashboard.algolia.com/account/api-keys/all" target="_blank"><?php esc_html_e( 'Manage your Algolia API Keys', 'wp-search-with-algolia' ); ?></a>
		<?php
	}

	public function save_network_account_settings() {

		wp_verify_nonce( sanitize_key( $_POST[ 'wpswa_pro_account_settings_nonce' ] ), 'wpswa_pro_account_settings_nonce' );

		$settings_keys = $this->get_network_account_settings_keys();

		foreach( $settings_keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ] );
				update_site_option( $key, $value );
			} else {
				update_site_option( $key, '' );
			}
		}

		wp_redirect( add_query_arg( [
			'page'    => 'wpswa_pro_network_account_settings',
			'updated' => 'true'
		], network_admin_url( 'admin.php' ) ) );
		exit;
	}

	public function get_network_account_settings_keys() {
		return [
			'algolia_network_application_id',
			'algolia_network_search_api_key',
			'algolia_network_api_key',
			'algolia_network_index_name_prefix',
			'algolia_network_powered_by_enabled',
		];
	}

	public function get_network_settings_keys_map() {
		return [
			'algolia_application_id'         => 'algolia_network_application_id',
			'algolia_search_api_key'         => 'algolia_network_search_api_key',
			'algolia_api_key'                => 'algolia_network_api_key',
			'algolia_index_name_prefix'      => 'algolia_network_index_name_prefix',
			'algolia_powered_by_enabled'     => 'algolia_network_powered_by_enabled',
			'algolia_api_is_reachable'       => 'algolia_network_api_is_reachable',
			'algolia_override_native_search' => 'algolia_network_override_native_search',
			'algolia_autocomplete_enabled'   => 'algolia_network_autocomplete_enabled',
		];
	}

	public function get_network_account_setting( $key = '' ) {
		if ( empty( $key ) ) {
			return $key;
		}

		return get_site_option( $key, '' );
	}

	public function override_option_with_network_value( $orig, $key, $default ) {
		$map = $this->get_network_settings_keys_map();
		$network_key = $map[ $key ];
		$override = $this->get_network_account_setting( $network_key );

		return ( ! empty( $override ) ) ? $override : $orig;
	}
}
