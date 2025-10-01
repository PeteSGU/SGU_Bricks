<?php
/**
 * Hooks_SEO Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro\Hooks;

/**
 * Class SEO
 *
 * @since 1.0.0
 */
class Hooks_SEO {

	/**
	 * Saved options for SEO settings.
	 *
	 * @var array
	 */
	private array $available_options;

	/**
	 * Constructor.
	 *
	 * @param array $available_options Array of SEO options.
	 */
	public function __construct( array $available_options = [] ) {
		$this->available_options = $available_options;
	}

	/**
	 * Execute our hooks for SEO based functionality.
	 *
	 * @since 1.0.0
	 */
	public function do_hooks() {
		add_filter( 'algolia_should_index_post', [ $this, 'should_index' ], 10, 2 );
		add_filter( 'algolia_should_index_searchable_post', [ $this, 'should_index' ], 10, 2 );
	}

	/**
	 * Catch-all method that returns the final determined "should index" status.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean  $should_index Whether or not the post should be indexed.
	 * @param \WP_Post $post WP_Post object.
	 *
	 * @return bool
	 */
	public function should_index( bool $should_index, \WP_Post $post ): bool {

		// Each method should return early if the given plugin is not available.
		// They should also return early if it gets to a `false` status for whether or not
		// the post should be indexed, to help speed up the process of finally returning
		// final determination.
		$should_index = $this->wpswa_pro_should_index( $should_index, $post );
		$should_index = $this->yoast_should_index( $should_index, $post );
		$should_index = $this->aioseo_should_index( $should_index, $post );
		$should_index = $this->rankmath_should_index( $should_index, $post );
		$should_index = $this->seopress_should_index( $should_index, $post );
		$should_index = $this->seoframework_should_index( $should_index, $post );

		return $should_index;
	}

	/**
	 * Determine if we should index a post with our own metabox checked status.
	 *
	 * @since 1.0.0
	 *
	 * @param bool     $should_index Whether or not the post should be indexed.
	 * @param \WP_Post $post WP_Post object.
	 *
	 * @return bool
	 */
	public function wpswa_pro_should_index( bool $should_index, \WP_Post $post ): bool {
		if ( false === $should_index ) {
			return $should_index;
		}

		$should_not_index_meta = get_post_meta( $post->ID, 'wpswa_pro_should_not_index', true );

		return ( 'yes' !== $should_not_index_meta );
	}

	/**
	 * Determine if we should index a post with WordPress SEO.
	 *
	 * This method will first check at the per-post level setting and then the global setting for the content type.
	 *
	 * @since 1.0.0
	 *
	 * @param bool     $should_index Whether or not the post should be indexed.
	 * @param \WP_Post $post WP_Post object.
	 *
	 * @return bool
	 */
	public function yoast_should_index( bool $should_index, \WP_Post $post ): bool {
		if ( empty( $this->available_options['options']['wpswa_pro_yoast_noindex'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_yoast_noindex'] ) {
			return $should_index;
		}

		if ( ! defined( 'WPSEO_VERSION' ) ) {
			return $should_index;
		}

		if ( false === $should_index ) {
			return $should_index;
		}

		$maybe_should_index = get_post_meta(
			$post->ID,
			'_yoast_wpseo_meta-robots-noindex',
			true
		);

		// 1 is saved to meta when "No, don't show" is chosen.
		if ( ! empty( $maybe_should_index ) && '1' === $maybe_should_index ) {
			return false;
		}

		// 2 is saved to meta when "Yes, show" is chosen.
		if ( ! empty( $maybe_should_index ) && '2' === $maybe_should_index ) {
			return true;
		}

		// We will reach here if `_yoast_wpseo_meta-robots-noindex` meta doesn't exist.
		if ( true === \WPSEO_Options::get( 'noindex-' . $post->post_type, false ) ) {
			return false;
		}

		return $should_index;
	}

	/**
	 * Determine if we should index a post with All In One SEO.
	 *
	 * This method will first check at the per-post level setting and then the global setting for the content type.
	 *
	 * @since 1.0.0
	 *
	 * @param bool     $should_index Whether or not the post should be indexed.
	 * @param \WP_Post $post WP_Post object.
	 *
	 * @return bool
	 */
	public function aioseo_should_index( bool $should_index, \WP_Post $post ): bool {
		if ( empty( $this->available_options['options']['wpswa_pro_aioseo_noindex'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_aioseo_noindex'] ) {
			return $should_index;
		}

		// Plugin not available.
		if ( ! function_exists( '\aioseo' ) ) {
			return $should_index;
		}

		// Already should not index.
		if ( false === $should_index ) {
			return $should_index;
		}

		// Post-level noindex is true.
		if ( true === aioseo()->meta->metaData->getMetaData( $post )->robots_noindex ) {
			return false;
		}

		// Global option is true for post type.
		if ( true === aioseo()->dynamicOptions->searchAppearance->postTypes->{$post->post_type}->advanced->robotsMeta->noindex ) {
			return false;
		}

		return $should_index;
	}

	/**
	 * Determine if we should index a post with Rank Math SEO.
	 * This method will first check at the per-post level setting and then the global setting for the content type.
	 *
	 * @since 1.2.0
	 *
	 * @param bool     $should_index Whether or not it should be indexed at this time.
	 * @param \WP_Post $post         The post to maybe get indexed.
	 *
	 * @return bool
	 */
	public function rankmath_should_index( bool $should_index, \WP_Post $post ): bool {
		if ( empty( $this->available_options['options']['wpswa_pro_rankmath_noindex'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_rankmath_noindex'] ) {
			return $should_index;
		}

		if ( ! function_exists( '\rank_math' ) ) {
			return $should_index;
		}

		if ( false === $should_index ) {
			return $should_index;
		}

		$data = get_post_meta( $post->ID, 'rank_math_robots', true );
		if ( ! empty( $data ) && is_array( $data ) ) {
			return ! in_array( 'noindex', $data, true );
		}

		$settings = rank_math()->settings->get( "titles.pt_{$post->post_type}_robots", [] );

		return ! in_array( 'noindex', $settings, true );
	}

	/**
	 * Determine if we should index a post with SEOPress.
	 * This method will first check at the per-post level setting and then the global setting for the content type.
	 *
	 * @since 1.3.0
	 *
	 * @param bool     $should_index Whether or not the post should be indexed.
	 * @param \WP_Post $post         WP_Post object.
	 *
	 * @return bool
	 */
	public function seopress_should_index( bool $should_index, \WP_Post $post ): bool {
		if ( empty( $this->available_options['options']['wpswa_pro_seopress_noindex'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_seopress_noindex'] ) {
			return $should_index;
		}

		if ( ! defined( 'SEOPRESS_VERSION' ) ) {
			return $should_index;
		}

		if ( false === $should_index ) {
			return $should_index;
		}

		$maybe_should_index = get_post_meta(
			$post->ID,
			'_seopress_robots_index',
			true
		);

		if ( ! empty( $maybe_should_index ) && 'yes' === $maybe_should_index ) {
			return false;
		}

		// Returns null or '1' so casting should convert to appropriate boolean value.
		if ( true === (bool) seopress_get_service( 'TitleOption' )->getSingleCptNoIndex( $post ) ) {
			return false;
		}

		return $should_index;
	}

	/**
	 * Determine if we should index a post with The SEO Framework.
	 *
	 * This method will first check at the per-post level setting and then the global setting for the content type.
	 *
	 * @since 1.4.0
	 *
	 * @param bool     $should_index Whether or not the post should be indexed.
	 * @param \WP_Post $post         WP_Post object.
	 *
	 * @return bool
	 */
	public function seoframework_should_index( bool $should_index, \WP_Post $post ): bool {
		if ( empty( $this->available_options['options']['wpswa_pro_seoframework_noindex'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_seoframework_noindex'] ) {
			return $should_index;
		}

		if ( ! defined( 'THE_SEO_FRAMEWORK_VERSION' ) ) {
			return $should_index;
		}

		if ( false === $should_index ) {
			return $should_index;
		}

		$maybe_should_index = get_post_meta(
			$post->ID,
			'_genesis_noindex',
			true
		);

		// Checking explicitly 1 because "should index" gets stored as `-1` and default gets no meta value.
		if ( 1 === $maybe_should_index ) {
			return false;
		}

		$options = get_option( THE_SEO_FRAMEWORK_SITE_OPTIONS, [] );
		if (
			$options &&
			(
				(
					isset( $options['pta'][ $post->post_type ]['noindex'] ) &&
					true === (bool) $options['pta'][ $post->post_type ]['noindex']
				) ||
				(
					isset( $options['noindex_post_types'][ $post->post_type ] ) &&
					true === (bool) $options['noindex_post_types'][ $post->post_type ]
				)
			)
		) {
			return false;
		}

		return $should_index;
	}
}
