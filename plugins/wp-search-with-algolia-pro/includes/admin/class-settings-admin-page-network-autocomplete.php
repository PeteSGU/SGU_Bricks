<?php

namespace WebDevStudios\WPSWAPro\Admin;
class Settings_Admin_Page_Network_Autocomplete {

	/**
	 * Admin page slug.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @var string
	 */
	private $slug = 'wpswa_pro_network_autocomplete_settings';

	/**
	 * Admin page capabilities.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Admin page section.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @var string
	 */
	private $section = 'algolia_section_autocomplete';

	/**
	 * Admin page option group.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @var string
	 */
	private $option_group = 'algolia_autocomplete';

	/**
	 * The Algolia_Settings object.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @var \Algolia_Settings
	 */
	private $settings;

	/**
	 * The Algolia_Autocomplete_Config object.
	 *
	 * @since 1.0.0
	 *
	 * @var \Algolia_Autocomplete_Config
	 */
	private $autocomplete_config;

	/**
	 * Algolia_Admin_Page_Autocomplete constructor.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @param \Algolia_Settings            $settings            The Algolia_Settings object.
	 * @param \Algolia_Autocomplete_Config $autocomplete_config The Algolia_Autocomplete_Config object.
	 */
	public function __construct( \Algolia_Settings $settings, \Algolia_Autocomplete_Config $autocomplete_config ) {
		$this->settings            = $settings;
		$this->autocomplete_config = $autocomplete_config;

		add_action( 'network_admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_init', [ $this, 'add_settings' ] );
		add_action( 'admin_notices', [ $this, 'display_errors' ] );

		add_action( 'network_admin_edit_wpswa_pro_network_autocomplete_settings', [
			$this,
			'save_network_autocomplete_settings'
		] );
	}

	/**
	 * Add menu pages.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function add_page() {
		add_submenu_page(
			'wpswa_pro_network',
			esc_html__( 'Autocomplete', 'wp-search-with-algolia-pro' ),
			esc_html__( 'Autocomplete', 'wp-search-with-algolia-pro' ),
			$this->capability,
			$this->slug,
			[ $this, 'display_page' ]
		);
	}

	/**
	 * Add and register settings.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function add_settings() {
		add_settings_section(
			$this->section,
			null,
			[ $this, 'print_section_settings' ],
			$this->slug
		);

		add_settings_field(
			'algolia_network_autocomplete_enabled',
			esc_html__( 'Enable Autocomplete', 'wp-search-with-algolia-pro' ),
			[ $this, 'autocomplete_enabled_callback' ],
			$this->slug,
			$this->section
		);

		add_settings_field(
			'algolia_network_autocomplete_config',
			esc_html__( 'Autocomplete Config', 'wp-search-with-algolia-pro' ),
			[ $this, 'autocomplete_config_callback' ],
			$this->slug,
			$this->section
		);

		register_setting( $this->option_group, 'algolia_network_autocomplete_enabled', [ $this, 'sanitize_autocomplete_enabled' ] );
		register_setting( $this->option_group, 'algolia_network_autocomplete_config', [ $this, 'sanitize_autocomplete_config' ] );
	}

	/**
	 * Callback to print the autocomplete enabled checkbox.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function autocomplete_enabled_callback() {
		$value    = $this->get_network_autocomplete_setting( 'algolia_network_autocomplete_enabled' );
		$indices  = $this->autocomplete_config->get_form_data();
		$checked  = 'yes' === $value ? 'checked ' : '';
		$disabled = empty( $indices ) ? 'disabled ' : '';
		?>
		<input type='checkbox' name='algolia_network_autocomplete_enabled' value='yes' <?php echo esc_html( $checked . ' ' . $disabled ); ?>/>
		<?php
	}

	/**
	 * Sanitize the Autocomplete enabled setting.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @param string $value The original value.
	 *
	 * @return string
	 */
	public function sanitize_autocomplete_enabled( $value ) {

		add_settings_error(
			$this->option_group,
			'autocomplete_enabled',
			esc_html__( 'Autocomplete configuration has been saved. Make sure to hit the "re-index" buttons of the different indices that are not indexed yet.', 'wp-search-with-algolia-pro' ),
			'updated'
		);

		return 'yes' === $value ? 'yes' : 'no';
	}

	/**
	 * Autocomplete Config Callback.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function autocomplete_config_callback() {
		$indices = $this->autocomplete_config->get_form_data();

		require_once dirname( __FILE__ ) . '/partials/page-autocomplete-config.php';
	}

	/**
	 * Sanitize Autocomplete Config.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @param array $values Array of autocomplete config values.
	 *
	 * @return array|mixed
	 */
	public function sanitize_autocomplete_config( $values ) {
		return $this->autocomplete_config->sanitize_form_data( $values );
	}

	/**
	 * Display the page.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function display_page() {
		require_once dirname( __FILE__ ) . '/partials/page-autocomplete.php';
	}

	/**
	 * Display the errors.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @return void
	 */
	public function display_errors() {
		settings_errors( $this->option_group );

		if ( defined( 'ALGOLIA_HIDE_HELP_NOTICES' ) && ALGOLIA_HIDE_HELP_NOTICES ) {
			return;
		}

		$is_enabled = 'yes' === $this->settings->get_autocomplete_enabled();
		$indices    = $this->autocomplete_config->get_config();

		if ( true === $is_enabled && empty( $indices ) ) {
			// translators: placeholder contains the URL to the autocomplete configuration page.
			$message = sprintf( __( 'Please select one or multiple indices on the <a href="%s">Algolia: Autocomplete configuration page</a>.', 'wp-search-with-algolia-pro' ), esc_url( admin_url( 'admin.php?page=' . $this->slug ) ) );
			echo '<div class="error notice">
					  <p>' . esc_html__( 'You have enabled the Algolia Autocomplete feature but did not choose any index to search in.', 'wp-search-with-algolia-pro' ) . '</p>
					  <p>' . wp_kses_post( $message ) . '</p>
				  </div>';
		}
	}

	/**
	 * Prints the section text.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 */
	public function print_section_settings() {
		echo '<p>' . esc_html__( 'Autocomplete adds a find-as-you-type dropdown to your search field(s).', 'wp-search-with-algolia-pro' ) . '</p>';

		echo '<p>' . esc_html__( 'Enabling Autocomplete adds the functionality to your site\'s frontend search. Indexing and settings pushes can be done regardless of enabled status.', 'wp-search-with-algolia-pro' ) . '</p>';
	}

	public function save_network_autocomplete_settings() {

		wp_verify_nonce( sanitize_key( $_POST['wpswa_pro_autocomplete_settings_nonce'] ), 'wpswa_pro_autocomplete_settings_nonce' );

		$settings_keys = $this->get_network_autocomplete_settings_keys();

		foreach ( $settings_keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ] );
				update_site_option( $key, $value );
			} else {
				update_site_option( $key, '' );
			}
		}

		wp_redirect( add_query_arg( [
			'page'    => 'wpswa_pro_network_autocomplete_settings',
			'updated' => 'true'
		], network_admin_url( 'admin.php' ) ) );
		exit;
	}

	public function get_network_autocomplete_settings_keys() {
		return [
			'algolia_network_autocomplete_enabled',
			'algolia_network_autocomplete_config',
		];
	}

	public function get_network_autocomplete_setting( $key = '' ) {
		if ( empty( $key ) ) {
			return $key;
		}

		return get_site_option( $key, '' );
	}
}
