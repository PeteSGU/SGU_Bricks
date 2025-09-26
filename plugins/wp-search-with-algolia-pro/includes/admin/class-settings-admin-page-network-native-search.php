<?php

namespace WebDevStudios\WPSWAPro\Admin;

use WebDevStudios\WPSWAPro\Utils;
class Settings_Admin_Page_Network_Native_Search {

	/**
	 * Admin page slug.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $slug = 'wpswa_pro_network_search_settings';

	/**
	 * Admin page capabilities.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Admin page section.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $section = 'algolia_section_network_native_search';

	/**
	 * Admin page option group.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $option_group = 'algolia_network_native_search';

	/**
	 * The Algolia_Plugin instance.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @var Algolia_Plugin
	 */
	private $plugin;

	/**
	 * Algolia_Admin_Page_Native_Search constructor.
	 *
	 * @param \Algolia_Plugin $plugin The Algolia_Plugin instance.
	 */
	public function __construct( \Algolia_Plugin $plugin ) {
		$this->plugin = $plugin;

		add_action( 'network_admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_init', [ $this, 'add_settings' ] );
		add_action( 'admin_notices', [ $this, 'display_errors' ] );

		add_action( 'network_admin_edit_wpswa_pro_network_search_settings', [
			$this,
			'save_network_search_settings'
		] );
	}

	/**
	 * Add submenu page.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 */
	public function add_page() {
		add_submenu_page(
			'wpswa_pro_network',
			esc_html__( 'Search Page', 'wp-search-with-algolia-pro' ),
			esc_html__( 'Search Page', 'wp-search-with-algolia-pro' ),
			$this->capability,
			$this->slug,
			[ $this, 'display_page' ]
		);
	}

	/**
	 * Add settings.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 */
	public function add_settings() {
		add_settings_section(
			$this->section,
			null,
			[ $this, 'print_section_settings' ],
			$this->slug
		);

		add_settings_field(
			'algolia_network_override_native_search',
			esc_html__( 'Search results', 'wp-search-with-algolia-pro' ),
			[ $this, 'override_native_search_callback' ],
			$this->slug,
			$this->section
		);

		register_setting( $this->option_group, 'algolia_network_override_native_search', [ $this, 'sanitize_override_native_search' ] );

		add_settings_field(
			'algolia_network_autocomplete_enabled',
			esc_html__( 'Enable Autocomplete?', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->section,
			[
				'label_for' => 'algolia_network_autocomplete_enabled',
				'helptext'  => esc_html__( 'Check to enable Autocomplete on searches.', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting( $this->option_group, 'algolia_network_autocomplete_enabled', [ $this, 'sanitize_text_field' ] );
	}

	/**
	 * Override native search callback.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 */
	public function override_native_search_callback() {
		$value = $this->get_network_search_setting( 'algolia_network_override_native_search' );

		require_once dirname( __FILE__ ) . '/partials/form-override-search-option.php';
	}

	/**
	 * Sanitize override native search.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return array|string
	 */
	public function sanitize_override_native_search( $value ) {

		if ( 'backend' === $value ) {
			add_settings_error(
				$this->option_group,
				'native_search_enabled',
				esc_html__( 'WordPress search is now based on Algolia!', 'wp-search-with-algolia-pro' ),
				'updated'
			);
		} elseif ( 'instantsearch' === $value ) {
			add_settings_error(
				$this->option_group,
				'native_search_enabled',
				esc_html__( 'WordPress search is now based on Algolia instantsearch.js!', 'wp-search-with-algolia-pro' ),
				'updated'
			);
		} else {
			$value = 'native';
			add_settings_error(
				$this->option_group,
				'native_search_disabled',
				esc_html__( 'You chose to keep the WordPress native search instead of Algolia. If you are using the autocomplete feature of the plugin we highly recommend you turn Algolia search on instead of the WordPress native search.', 'wp-search-with-algolia-pro' ),
				'updated'
			);
		}

		return $value;
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
		$label       = esc_attr( $args['label_for'] );
		$helptext    = ! empty( $args['helptext'] ) ? esc_html( $args['helptext'] ) : '';
		$extra_label = ! empty( $args['extra_label'] ) ? esc_html( $args['extra_label'] ) : '';
		?>
		<input type="checkbox" id="<?php echo $label; ?>" name="<?php echo $label; ?>" value="yes" <?php checked( $value, 'yes' ); ?> <?php disabled( $disabled, true ); ?>/>
		<label for="<?php echo $label; ?>"><?php echo $extra_label; ?></label>
		<p class="description"><?php echo $helptext; ?></p>
		<?php
	}

	/**
	 * Display the page.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 */
	public function display_page() {
		require_once dirname( __FILE__ ) . '/partials/page-search.php';
	}

	/**
	 * Display the errors.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function display_errors() {
		settings_errors( $this->option_group );

		if ( defined( 'ALGOLIA_HIDE_HELP_NOTICES' ) && ALGOLIA_HIDE_HELP_NOTICES ) {
			return;
		}

		if ( ! Utils::should_override_search_in_backend() ) {
			return;
		}

		$maybe_get_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );

		$searchable_posts_index = $this->plugin->get_index( 'searchable_posts' );
		if ( false === $searchable_posts_index->is_enabled() && ( ! empty( $maybe_get_page ) ) && $maybe_get_page === $this->slug ) {
			// translators: placeholder contains the link to the indexing page.
			$message = sprintf( __( 'Searchable posts index needs to be checked on the <a href="%s">Algolia: Indexing page</a> for the search results to be powered by Algolia.', 'wp-search-with-algolia-pro' ), esc_url( admin_url( 'admin.php?page=algolia-indexing' ) ) );
			echo '<div class="error notice">
					  <p>' . wp_kses_post( $message ) . '</p>
				  </div>';
		}
	}

	/**
	 * Prints the section text.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.0.0
	 */
	public function print_section_settings() {
		echo '<p>' . esc_html__( 'By enabling this plugin to override the native WordPress search, your search results will be powered by Algolia\'s typo-tolerant & relevant search algorithms.', 'wp-search-with-algolia-pro' ) . '</p>';
	}

	public function save_network_search_settings() {

		wp_verify_nonce( sanitize_key( $_POST['wpswa_pro_search_settings_nonce'] ), 'wpswa_pro_search_settings_nonce' );

		$settings_keys = $this->get_network_search_settings_keys();

		foreach ( $settings_keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ] );
				update_site_option( $key, $value );
			} else {
				update_site_option( $key, '' );
			}
		}

		wp_redirect( add_query_arg( [
			'page'    => 'wpswa_pro_network_search_settings',
			'updated' => 'true'
		], network_admin_url( 'admin.php' ) ) );
		exit;
	}

	public function get_network_search_settings_keys() {
		return [
			'algolia_network_override_native_search',
			'algolia_network_autocomplete_enabled',
		];
	}

	public function get_network_search_setting( $key = '' ) {
		if ( empty( $key ) ) {
			return $key;
		}

		return get_site_option( $key, '' );
	}
}
