<?php
/**
 * Utils Class file
 *
 * @since   1.3.0
 * @author  WebDevStudios <contact@webdevstudios.com>
 */

namespace WebDevStudios\WPSWAPro;

/**
 * Class Utils
 *
 * @since 1.3.0
 */
class Utils {

	/**
	 * Algolia Plugin instance from WP Search with Algolia.
	 *
	 * @var \Algolia_Plugin
	 */
	public static $wpswa;

	/**
	 * Constructor
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
	}

	/**
	 * Getter for our wpswa property.
	 *
	 * @since 1.3.0
	 *
	 * @return \Algolia_Plugin $wpswa
	 */
	public static function get_wpswa( \Algolia_Plugin $wpswa ) {
		if ( null === self::$wpswa ) {
			self::set_wpswa( $wpswa );
		}
		return self::$wpswa;
	}

	/**
	 * Setter for our wpswa property.
	 *
	 * @since 1.3.0
	 *
	 * @param \Algolia_Plugin $wpswa
	 */
	private static function set_wpswa( \Algolia_Plugin $wpswa ) {
		self::$wpswa = $wpswa;
	}

	/**
	 * Get sync'd index IDs.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_synced_indices_ids() {
		return self::$wpswa->get_settings()->get_synced_indices_ids();
	}

	/**
	 * Get index name prefix.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_index_name_prefix() {
		return self::$wpswa->get_settings()->get_index_name_prefix();
	}

	/**
	 * Get Algolia client.
	 *
	 * @since 1.3.0
	 *
	 * @return \Algolia\AlgoliaSearch\SearchClient|\AlgoliaSearch\Client|null
	 */
	public static function get_client() {
		return self::$wpswa->get_api()->get_client();
	}

	/**
	 * Whether or not a given index name is in autocomplete config.
	 *
	 * @since 1.3.0
	 *
	 * @param string $index_name Index name to check for.
	 * @return bool
	 */
	public function is_index_in_autocomplete_config( $index_name ) {
		return in_array( $index_name, self::get_synced_indices_ids() );
	}

	/**
	 * Check whether or not network wide indexing is enabled.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function network_wide_indexing_enabled() {
		if ( ! is_plugin_active_for_network( 'wp-search-with-algolia-pro/wp-search-with-algolia-pro.php' ) ) {
			return false;
		}

		$network_options = get_site_option( 'wpswa_pro_network_wide_indexing' );

		return 'yes' === $network_options;
	}

	/**
	 * Construct an array of all the sites and their public visibility to robots.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public static function get_network_and_visibilities() {
		$sites   = get_sites( [ 'deleted' => false ] );
		$results = [];
		foreach( $sites as $site ) {
			$site_result         = [];
			$site_result['id']   = $site->blog_id;
			$site_result['site'] = $site->path;
			$site_result['is_main_site'] = is_main_site( $site->blog_id );
			switch_to_blog( $site->blog_id );
			$site_result['name'] = get_blog_details( [ 'blog_id' => $site->blog_id ] )->blogname;
			$site_result['visibility'] =
				( '1' === get_option( 'blog_public' ) ) ?
					esc_html__( 'Public', 'wp-search-with-algolia-pro' ) :
					esc_html__( 'Private', 'wp-search-with-algolia-pro' );
			$site_result['visibility'] =
				self::is_development_domain() ?
					esc_html__( 'Dev environment', 'wp-search-with-algolia-pro' ) :
					$site_result['visibility'];
			$site_result['should_index'] =
				self::should_index_network_site( $site->blog_id );
			$site_result['last_completed_network_batch'] = (int) get_option( 'wpswa_pro_network_batch_complete' );
			restore_current_blog();
			$results[] = $site_result;
		}
		return $results;
	}

	/**
	 * Return a comma-separated string of default network meta keys.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_filtered_default_network_meta_keys() {
		return implode(
			',',
			array_filter(
				apply_filters(
					'wpswa_pro_default_network_meta_keys',
					[]
				)
			)
		);
	}

	/**
	 * Return an array of saved meta keys for each searchable post type in a site.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public static function get_post_type_meta_options() {
		$options = [];
		$post_types = get_post_types( [ 'exclude_from_search' => false ], 'objects' );

		foreach( $post_types as $post_type ) {
			$data = get_option( "wpswa_pro_{$post_type->name}_meta" );
			if ( ! empty( $data ) ) {
				$options[ $post_type->name ] = $data;
			}
		}

		return $options;
	}

	/**
	 * Return whether or not WP Search with Algolia Pro is network activated.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function wpswa_pro_is_network_activated() {
		return is_plugin_active_for_network( WPSWA_PRO_BASENAME );
	}

	/**
	 * Return the current network application ID.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_network_application_id() {
		if ( ! self::is_network_application_id_in_config() ) {

			return (string) get_site_option( 'algolia_network_application_id', '' );
		}

		self::assert_constant_is_non_empty_string( ALGOLIA_NETWORK_APPLICATION_ID, 'ALGOLIA_NETWORK_APPLICATION_ID' );

		return ALGOLIA_NETWORK_APPLICATION_ID;
	}

	/**
	 * Check to see if the network application ID is defined in wp-config.php.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function is_network_application_id_in_config() {
		return defined( 'ALGOLIA_NETWORK_APPLICATION_ID' );
	}

	/**
	 * Return the current network search API key.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_network_search_api_key() {
		if ( ! self::is_network_search_api_key_in_config() ) {

			return (string) get_site_option( 'algolia_network_search_api_key', '' );
		}

		self::assert_constant_is_non_empty_string( ALGOLIA_NETWORK_SEARCH_API_KEY, 'ALGOLIA_NETWORK_SEARCH_API_KEY' );

		return ALGOLIA_NETWORK_SEARCH_API_KEY;
	}

	/**
	 * Check to see if the network search API key is defined in wp-config.php.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function is_network_search_api_key_in_config() {
		return defined( 'ALGOLIA_NETWORK_SEARCH_API_KEY' );
	}

	/**
	 * Return the current network api key.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_network_api_key() {
		if ( ! self::is_network_api_key_in_config() ) {

			return (string) get_site_option( 'algolia_network_api_key', '' );
		}

		self::assert_constant_is_non_empty_string( ALGOLIA_NETWORK_API_KEY, 'ALGOLIA_NETWORK_API_KEY' );

		return ALGOLIA_NETWORK_API_KEY;
	}

	/**
	 * Check to see if the network API key is defined in wp-config.php.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function is_network_api_key_in_config() {
		return defined( 'ALGOLIA_NETWORK_API_KEY' );
	}

	/**
	 * Return the current network index name prefix.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_network_index_name_prefix() {
		if ( ! self::is_network_index_name_prefix_in_config() ) {

			return (string) get_site_option( 'algolia_network_index_name_prefix', '' );
		}

		self::assert_constant_is_non_empty_string( ALGOLIA_NETWORK_INDEX_NAME_PREFIX, 'ALGOLIA_NETWORK_INDEX_NAME_PREFIX' );

		return ALGOLIA_NETWORK_INDEX_NAME_PREFIX;
	}

	/**
	 * Check to see if the network index name prefix is defined in wp-config.php.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function is_network_index_name_prefix_in_config() {
		return defined( 'ALGOLIA_NETWORK_INDEX_NAME_PREFIX' );
	}

	/**
	 * Makes sure that constants are non empty strings.
	 * This makes sure that we fail early if the environment configuration is wrong.
	 *
	 * @param mixed  $value         The constant value to check.
	 * @param string $constant_name The constant name to check.
	 *
	 * @throws \RuntimeException If the constant is not a string or is empty.
	 * @since   1.3.0
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 */
	protected static function assert_constant_is_non_empty_string( $value, $constant_name ) {
		if ( ! is_string( $value ) ) {
			throw new \RuntimeException( sprintf( 'Constant %s in wp-config.php should be a string, %s given.', $constant_name, gettype( $value ) ) );
		}

		if ( 0 === mb_strlen( $value ) ) {
			throw new \RuntimeException( sprintf( 'Constant %s in wp-config.php cannot be empty.', $constant_name ) );
		}
	}

	/**
	 * Determine if powered by is enabled.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 *
	 * @since   1.3.0
	 *
	 * @return bool
	 */
	public static function is_network_powered_by_enabled() {
		$enabled = get_site_option( 'algolia_network_powered_by_enabled', 'yes' );

		return 'yes' === $enabled;
	}

	/**
	 * Enable the powered by option setting.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 *
	 * @since   1.3.0
	 */
	public static function enable_network_powered_by() {
		update_option( 'algolia_network_powered_by_enabled', 'yes' );
	}

	/**
	 * Disable the powered by option setting.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 *
	 * @since   1.3.0
	 */
	public static function disable_network_powered_by() {
		update_option( 'algolia_network_powered_by_enabled', 'no' );
	}

	/**
	 * Updates the option indicating whether or not the API is presently reachable.
	 *
	 * @since 1.3.0
	 *
	 * @param bool $flag Whether or not the API is reachable.
	 */
	public static function set_network_api_is_reachable( $flag ) {
		$value = (bool) true === $flag ? 'yes' : 'no';
		update_site_option( 'algolia_network_api_is_reachable', $value );
	}

	/**
	 * Gets whether or not our data indicates the API is reachable.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function get_network_api_is_reachable() {
		return 'yes' === get_site_option( 'algolia_network_api_is_reachable', 'no' );
	}

	/**
	 * Determine whether or not we should override search in the backend.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function should_override_search_in_backend() {
		$search_type = get_option( 'algolia_override_native_search', 'native' );

		return in_array( $search_type, [ 'instantsearch', 'backend' ] );
	}

	/**
	 * Helper function for if a site should get network indexed.
	 *
	 * @since 1.5.0
	 *
	 * @param int $site_id Site ID being indexed.
	 * @return bool
	 */
	public static function should_index_network_site( int $site_id ) {
		$should_index = false;

		if ( '1' === get_option( 'blog_public' ) ) {
			$should_index = true;
		}

		if ( self::is_development_domain() ) {
			$should_index = true;
		}

		/**
		 * Filters whether or not a site should be indexed.
		 *
		 * @since 1.5.0
		 *
		 * @param bool $value   Whether or not a site should be indexed
		 * @param int  $site_id Current site ID being evaluated.
		 */
		if ( true === (bool) apply_filters( 'wpswa_pro_should_index_network_site', true, $site_id ) ) {
			$should_index = true;
		}

		return $should_index;
	}

	/**
	 * Return whether or not a development domain has been detected.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public static function is_development_domain() {
		$is_dev = false;

		$wp_env = wp_get_environment_type();
		$wp_dev_envs = [ 'local', 'development', 'staging' ];

		// Rely on these env types first. If we get production, continue testing.
		if ( in_array( $wp_env, $wp_dev_envs ) ) {
			$is_dev = true;
		}

		$current_domain = network_home_url();

		if ( ! $is_dev ) {

			// We will check for `$is_dev` each time because if we find we're on a dev site
			// this will help prevent us from needlessly re-doing strpos() checks when we
			// have already found one.

			// WPEngine
			if ( ! $is_dev && false !== strpos( $current_domain, '.wpengine.com' ) ) {
				$is_dev = true;
			}

			// Pantheon
			if ( ! $is_dev && false !== strpos( $current_domain, '.pantheonsite.io' ) ) {
				$is_dev = true;
			}

			// Pagely
			if ( ! $is_dev && false !== strpos( $current_domain, '.sites.pressdns.com' ) ) {
				$is_dev = true;
			}

			// WP Stagecoach
			if ( ! $is_dev && false !== strpos( $current_domain, '.wpstagecoach.com' ) ) {
				$is_dev = true;
			}

			// WP VIP
			if ( ! $is_dev && false !== strpos( $current_domain, '.go-vip.net' ) ) {
				$is_dev = true;
			}

			// WDSLab. Who dat?
			if ( ! $is_dev && false !== strpos( $current_domain, '.wdslab.com' ) ) {
				$is_dev = true;
			}

			// "Development" Variations. Assume for now that the word won't be a suffix.
			if ( ! $is_dev &&
			     (
					 false !== strpos( $current_domain, 'dev.' ) ||
					 false !== strpos( $current_domain, '.dev.' ) ||
					 false !== strpos( $current_domain, 'development.' ) ||
					 false !== strpos( $current_domain, '.development.' ) ||
					 false !== strpos( $current_domain, 'develop.' ) ||
					 false !== strpos( $current_domain, '.develop.' )
			     )
			) {
				$is_dev = true;
			}

			// "Staging" Variations. Assume for now that the word won't be a suffix.
			if ( ! $is_dev &&
			     (
				     false !== strpos( $current_domain, 'stage.' ) ||
				     false !== strpos( $current_domain, '.stage.' ) ||
				     false !== strpos( $current_domain, 'staging.' ) ||
				     false !== strpos( $current_domain, '.staging.' ) ||
				     false !== strpos( $current_domain, 'stg.' ) ||
				     false !== strpos( $current_domain, '.stg.' ) ||
				     false !== strpos( $current_domain, 'stag.' ) ||
				     false !== strpos( $current_domain, '.stag.' )
			     )
			) {
				$is_dev = true;
			}

			// "Test" Variations + "local". This one will get suffixes.
			if ( ! $is_dev &&
			     (
				     false !== strpos( $current_domain, 'test.' ) ||
				     false !== strpos( $current_domain, '.test.' ) ||
				     false !== strpos( $current_domain, '.test' ) ||
				     false !== strpos( $current_domain, 'testing.' ) ||
				     false !== strpos( $current_domain, '.testing.' ) ||
				     false !== strpos( $current_domain, '.testing' ) ||
				     false !== strpos( $current_domain, 'local.' ) ||
				     false !== strpos( $current_domain, '.local.' ) ||
				     false !== strpos( $current_domain, '.local' )
			     )
			) {
				$is_dev = true;
			}

			// "QA" Variations
			if ( ! $is_dev &&
			     (
				     false !== strpos( $current_domain, 'qa.' ) ||
				     false !== strpos( $current_domain, '.qa.' )
			     )
			) {
				$is_dev = true;
			}
		}

		/**
		 * Filters whether or not the current domain is for a development site.
		 *
		 * @since 1.5.0
		 *
		 * @param bool   $is_dev         Whether or not the domain is for a development site
		 * @param string $current_domain Current domain being checked.
		 */
		return (bool) apply_filters( 'wpswa_pro_is_development_domain', $is_dev, $current_domain );
	}
}
