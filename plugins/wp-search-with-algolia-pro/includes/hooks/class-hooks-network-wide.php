<?php
/**
 * Hooks_Network_Wide Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.3.0
 */

namespace WebDevStudios\WPSWAPro\Hooks;

use WebDevStudios\WPSWAPro\Utils;

/**
 * Class Hooks_Network_Wide
 *
 * @since 1.3.0
 */
class Hooks_Network_Wide {

	/**
	 * Saved options for SEO settings.
	 * @var array
	 */
	private array $available_options;

	/**
	 * Meta keys for each registered searchable post type per-site.
	 *
	 * @var array
	 */
	private array $data;

	/**
	 * Constructor.
	 *
	 * @param array $available_options Array of options.
	 */
	public function __construct( array $available_options = [] ) {
		$this->available_options = $available_options;
		$this->data              = Utils::get_post_type_meta_options();
		$this->data['defaults']  = get_site_option( 'wpswa_pro_default_meta_fields' );
	}

	/**
	 * Execute our hooks for network-wide based details.
	 *
	 * @since 1.3.0
	 */
	public function do_hooks() {
		// Customizes the post object ID at time of index.
		add_filter( 'algolia_get_post_object_id', [ $this, 'adjust_objectID' ], 10, 3 );
		// Overrides the Free plugin's settings page output when netowrk wide enabled.
		add_action( 'wpswa_pro_override_settings_output', [ $this, 'override_free_settings_output' ] );
		// Add a given site's meta fields settings to the searchable attributes.
		add_filter( 'algolia_searchable_post_shared_attributes', [ $this, 'index_meta' ], 10, 2 );

		add_filter( 'pre_option_algolia_autocomplete_config', [ $this, 'network_wide_autocomplete_config' ], 10, 3 );

		add_filter( 'algolia_should_index_searchable_post', [ $this, 'should_index_searchable_post' ], 9, 2 );
	}

	/**
	 * Override object ID values to allow for adding site ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $value        Original objectID value.
	 * @param int    $post_id      Object ID
	 * @param int    $record_index Split record index.
	 *
	 * @return mixed|string
	 */
	public function adjust_objectID( $value, $post_id, $record_index ) {
		if ( ! Utils::network_wide_indexing_enabled() ) {
			return $value;
		}

		return get_current_blog_id() . '-' . $post_id . '-' . $record_index;
	}

	/**
	 * Filter to determine if we should index this post.
	 *
	 * @since 1.3.0
	 *
	 * @param bool     $should_index Flag indicating whether post should be indexed or not.
	 * @param \WP_Post $post         Post object.
	 *
	 * @return mixed|string
	 */
	public function should_index_searchable_post( $should_index, $post ) {
		if ( ! Utils::network_wide_indexing_enabled() ) {
			return $should_index;
		}

		// Logic elsewhere has already determined we shouldn't.
		if ( false === $should_index ) {
			return $should_index;
		}

		return Utils::should_index_network_site( get_current_blog_id() );
	}

	/**
	 * Overrides the settings screen output in WP Search with Algolia Free plugin.
	 *
	 * @since 1.3.0
	 */
	public function override_free_settings_output() {
		if ( ! Utils::wpswa_pro_is_network_activated() ) {
			return;
		}
		?>
		<div class="wpswap-settings wrap">
			<p><?php
			printf(
				// @translators: placeholders hold `<a>` markup.
				esc_html__( 'Algolia search settings are managed in the %snetwork admin%s when network activated.', 'wp-search-with-algolia-pro' ),
				sprintf(
					'<a href="%s">',
					esc_url( network_admin_url( 'admin.php?page=wpswa_pro_network_account_settings' ) )
				),
				'</a>'
			);
			?></p>

			<?php $this->index_network_site_button(); ?>
		</div>
			<?php
	}

	/**
	 * Get the template to display the index network site button.
	 *
	 * @author WebDevStudios <contact@webdevstudios.com>
	 * @since  1.3.0
	 */
	public function index_network_site_button() {
		require WPSWA_PRO_PATH . 'includes/admin/partials/button-reindex-network-site.php';
	}

	/**
	 * Index a given site's post type meta key settings.
	 *
	 * This method will fetch the meta keys saved for a given post type,
	 * for the current site being used during the indexing process.
	 *
	 * @since 1.3.0
	 * @param array    $shared_attributes Array of attributes that should be indexed.
	 * @param \WP_Post $post              Current post being indexed.
	 *
	 * @return array
	 */
	public function index_meta( array $shared_attributes, \WP_Post $post ) {
		if ( ! Utils::network_wide_indexing_enabled() ) {
			return $shared_attributes;
		}

		$current_keys = '';
		if ( empty( $this->data[ $post->post_type ] ) ) {
			// Nothing saved at the current site level. Fall back to network defaults
			$current_keys = Utils::get_filtered_default_network_meta_keys();
			if ( empty( $current_keys ) ) {
				$current_keys = $this->data['defaults'];
			}
		} elseif ( array_key_exists( $post->post_type, $this->data ) ) {
			// We have current site settings, let's fetch the current post type.
			$current_keys = $this->data[ $post->post_type ];
		}

		// If we just get nothing, return early.
		if ( empty( $current_keys ) || ! is_string( $current_keys ) ) {
			return $shared_attributes;
		}

		$current_keys = array_filter( explode( ',', $current_keys ) );

		if ( ! empty( $current_keys ) ) {
			foreach ( $current_keys as $meta_field ) {
				$meta_field = trim( $meta_field );
				// Default to assumed no underscore prefix meta key.
				$meta_field_property = $meta_field;
				// Trim off '_' underscore prefix for the property name, out of courtesy
				if ( '_' === mb_substr( $meta_field, 0, 1 ) ) {
					$meta_field_property = mb_substr( $meta_field, 1 );
				}
				$field = get_post_meta( $post->ID, $meta_field, true );
				if ( ! empty( $field ) ) {
					$shared_attributes[ $meta_field_property ] = $field;
				}
			}
		}

		return $shared_attributes;
	}

	public function network_wide_autocomplete_config( $orig, $key, $default ) {
		if ( ! Utils::network_wide_indexing_enabled() ) {
			return $orig;
		}

		$network_config   = [];
		$network_config[] = apply_filters(
			'wpswa_pro_network_wide_autocomplete_config',
			[
				'admin_name'      => 'All posts',
				'index_id'        => 'searchable_posts', // E.g. 'searchable_posts'.
				'index_name'      => Utils::get_network_index_name_prefix() . 'searchable_posts', // E.g. "prefix_$index_id".
				'label'           => 'All posts',
				'max_suggestions' => 5,
				'position'        => 1,
				'tmpl_suggestion' => 'autocomplete-post-suggestion'
			]
		);

		return $network_config;
	}
}
