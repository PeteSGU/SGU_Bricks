<?php
/**
 * Settings_SEO Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro\Admin;

/**
 * Class Settings_SEO
 *
 * @since 1.0.0
 */
class Settings_SEO {

	/**
	 * Settings slug.
	 *
	 * @var string
	 */
	private string $slug = 'wpswa_pro_seo';

	/**
	 * Option group slug.
	 *
	 * @var string
	 */
	private string $option_group = 'wpswa_pro_seo';

	/**
	 * SEO section slug.
	 *
	 * @var string
	 */
	private string $seo_section = 'seo';

	/**
	 * Minimum capability needed to interact with our options.
	 *
	 * @var string
	 */
	private string $capability = 'manage_options';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_option( 'wpswa_pro_yoast_noindex' );
		add_option( 'wpswa_pro_aioseo_noindex' );
		add_option( 'wpswa_pro_rankmath_noindex' );
		add_option( 'wpswa_pro_seopress_noindex' );
		add_option( 'wpswa_pro_seoframework_noindex' );
	}

	/**
	 * Execute our hooks for SEO based settings.
	 *
	 * @since 1.0.0
	 */
	public function do_hooks() {
		add_action( 'admin_menu', [ $this, 'add_page' ], 11 );
		add_action( 'admin_init', [ $this, 'add_settings' ] );

		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save_meta_box' ] );

		add_filter( 'wpswa_pro_option_keys', [ $this, 'registered_options' ] );

		add_action( 'admin_init', function () {
			remove_submenu_page( 'algolia', 'algolia-account-seo' );
		}, 11 );
	}

	/**
	 * Add our classes' available options to the array to delete upon deactivation.
	 *
	 * @since 1.0.0
	 * @param array $options Array of options to register for this setting type.
	 *
	 * @return array
	 */
	public function registered_options( array $options = [] ): array {
		foreach (
			[
				'wpswa_pro_yoast_noindex',
				'wpswa_pro_aioseo_noindex',
				'wpswa_pro_rankmath_noindex',
				'wpswa_pro_seopress_noindex',
				'wpswa_pro_seoframework_noindex',
			] as $key ) {
			$options[] = $key;
		}
		return $options;
	}

	/**
	 * Add our submenu.
	 *
	 * @since 1.0.0
	 */
	public function add_page() {
		add_submenu_page(
			'algolia',
			esc_html__( 'WP Search with Algolia and Search Engine Optimization', 'wp-search-with-algolia-pro' ),
			esc_html__( 'SEO', 'wp-search-with-algolia-pro' ),
			$this->capability,
			$this->slug,
			[ $this, 'display_page' ]
		);
	}

	/**
	 * Execute our settings sections.
	 *
	 * @since 1.0.0
	 */
	public function add_settings() {
		$this->add_seo_section();
	}

	/**
	 * Register our SEO section and related settings fields.
	 *
	 * @since 1.0.0
	 */
	private function add_seo_section() {
		add_settings_section(
			$this->seo_section,
			esc_html__( 'Noindex settings', 'wp-search-with-algolia-pro' ),
			[ $this, 'seo_content_third_party_callback' ],
			$this->slug
		);

		/*
		 * Yoast SEO
		 */
		add_settings_field(
			'wpswa_pro_yoast_noindex',
			esc_html__( 'Yoast SEO', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->seo_section,
			[
				'label_for' => 'wpswa_pro_yoast_noindex',
				'disabled' => ! is_plugin_active( 'wordpress-seo/wp-seo.php' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_yoast_noindex',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * All In One SEO
		 */
		add_settings_field(
			'wpswa_pro_aioseo_noindex',
			esc_html__( 'All in One SEO', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->seo_section,
			[
				'label_for' => 'wpswa_pro_aioseo_noindex',
				'disabled'  => ! is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_aioseo_noindex',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Rank Math SEO
		 */
		add_settings_field(
			'wpswa_pro_rankmath_noindex',
			esc_html__( 'Rank Math SEO', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->seo_section,
			[
				'label_for' => 'wpswa_pro_rankmath_noindex',
				'disabled'  => ! is_plugin_active( 'seo-by-rank-math/rank-math.php' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_rankmath_noindex',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * SEOPress
		 */
		add_settings_field(
			'wpswa_pro_seopress_noindex',
			esc_html__( 'SEOPress', 'wp-search-with-algolia-premium' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->seo_section,
			[
				'label_for' => 'wpswa_pro_seopress_noindex',
				'disabled' => ! is_plugin_active( 'wp-seopress/seopress.php' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_seopress_noindex',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * The SEO Framework
		 */
		add_settings_field(
			'wpswa_pro_seoframework_noindex',
			esc_html__( 'The SEO Framework', 'wp-search-with-algolia-premium' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->seo_section,
			[
				'label_for' => 'wpswa_pro_seoframework_noindex',
				'disabled'  => ! is_plugin_active( 'autodescription/autodescription.php' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_seoframework_noindex',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);
	}

	/**
	 * Load an external PHP file to render our final settings page result.
	 *
	 * @since 1.0.0
	 */
	public function display_page() {
		require_once WPSWA_PRO_PATH . 'includes/admin/partials/page-seo.php';
	}

	/**
	 * Callback to render our checkbox.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of extra arguments for checkbox callback.
	 */
	public function checkbox( array $args ) {
		$value       = get_option( $args['label_for'], '' );
		$disabled    = esc_attr( $args['disabled'] );
		$label       = esc_attr( $args['label_for'] );
		$helptext    = ! empty( $args['helptext'] ) ? esc_html( $args['helptext'] ) : '';
		$extra_label = ! empty( $args['extra_label'] ) ? esc_html( $args['extra_label'] ) : '';
		?>
		<input type="checkbox" id="<?php echo $label; ?>" name="<?php echo $label; ?>" value="yes" <?php checked( $value, 'yes' ); ?> <?php disabled( $disabled, true ); ?>/> <label for="<?php echo $label; ?>"><?php echo $extra_label; ?></label>
		<p class="description"><?php echo $helptext; ?></p>
		<?php
	}

	/**
	 * SEO callback to render content between the heading and the options themselves.
	 *
	 * @since 1.0.0
	 */
	public function seo_content_third_party_callback() {
		?>
		<p><?php esc_html_e( 'Respect a post or content type noindex status for various SEO plugins', 'wp-search-with-algolia-pro' ); ?></p>
		<?php
	}

	/**
	 * Register a metabox for search-enabled post types.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type slug.
	 *
	 * @return string|null
	 */
	public function add_meta_box( string $post_type ): ?string {

		$searchable_post_types = get_post_types( [ 'exclude_from_search' => false ] );

		/**
		 * Filters whether or not to display our metabox.
		 *
		 * @since 1.2.0
		 *
		 * @param bool   $value Whether or not to disable metabox display. Default false.
		 * @param string $post_type Current post type being rendered for.
		 * @param array  $searchable_post_types Array of current searchable post types.
		 * @return bool
		 */
		if ( true === apply_filters( 'wpswa_pro_disable_metabox', false, $post_type, $searchable_post_types ) ) {
			return null;
		}

		if ( in_array( $post_type, $searchable_post_types, true ) ) {
			add_meta_box(
				'wpswa_pro_include_product_in_index',
				esc_html__(
					'WP Search with Algolia Pro',
					'wp-search-with-algolia-pro'
				),
				[ $this, 'render_meta_box_content' ],
				$post_type,
				'side',
			);
		}
		return null;
	}

	/**
	 * Pricess and save metabox settings on post save.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return int|null
	 */
	public function save_meta_box( int $post_id ): ?int {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST['wpswa_pro_should_not_index_nonce'] ) ) {
			return $post_id;
		}

		$nonce = sanitize_text_field( $_POST['wpswa_pro_should_not_index_nonce'] ); // phpcs:ignore

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wpswa_pro_should_not_index' ) ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		if ( ! isset( $_POST['wpswa_pro_should_not_index'] ) ) {
			update_post_meta( $post_id, 'wpswa_pro_should_not_index', '' );
			return $post_id;
		}

		// Sanitize the user input.
		$should_index = sanitize_text_field( $_POST['wpswa_pro_should_not_index'] );

		// Update the meta field.
		update_post_meta( $post_id, 'wpswa_pro_should_not_index', $should_index );

		return null;
	}

	/**
	 * Render the contents of the metabox for the post.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @return string|null
	 */
	public function render_meta_box_content( \WP_Post $post ): ?string {
		wp_nonce_field( 'wpswa_pro_should_not_index', 'wpswa_pro_should_not_index_nonce' );

		$value = get_post_meta( $post->ID, 'wpswa_pro_should_not_index', true );

		?>
		<label for="wpswa_pro_should_not_index">
			<?php esc_html_e( 'Exclude from Algolia indexes?', 'wp-search-with-algolia-pro' ); ?>
		</label>
		<input type="checkbox" id="wpswa_pro_should_not_index" name="wpswa_pro_should_not_index" value="yes" <?php checked( $value, 'yes' ); ?>/>
		<?php

		return null;
	}

	/**
	 * Return an array of current values for all of our WooCommerce settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_seo_settings(): array {
		$all_settings = [];
		foreach (
			[
				'wpswa_pro_yoast_noindex',
				'wpswa_pro_aioseo_noindex',
				'wpswa_pro_rankmath_noindex',
				'wpswa_pro_seopress_noindex',
				'wpswa_pro_seoframework_noindex',
			]
			as $option
		) {
			$all_settings[ $option ] = get_option( $option, '' );
		}

		return $all_settings;
	}
}
