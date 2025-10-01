<?php
/**
 * Settings_WooCommerce Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro\Admin;

/**
 * Class Settings_WooCommerce
 *
 * @since 1.0.0
 */
class Settings_WooCommerce {

	/**
	 * Settings slug.
	 *
	 * @var string
	 */
	private string $slug = 'wpswa_pro_woocommerce';

	/**
	 * Relevance section slug.
	 *
	 * @var string
	 */
	private string $relevance_section = 'relevance';

	/**
	 * Search display section slug.
	 *
	 * @var string
	 */
	private string $display_section = 'display';

	/**
	 * Option group slug.
	 *
	 * @var string
	 */
	private string $option_group = 'wpswa_pro_woocommerce';

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
		// Adds our options to the database if they don't exist, ready to be used later.
		add_option( 'wpswa_pro_include_sku', 'yes' );
		add_option( 'wpswa_pro_include_price', 'yes' );
		add_option( 'wpswa_pro_include_sale_price', 'yes' );
		add_option( 'wpswa_pro_include_short_description' );
		add_option( 'wpswa_pro_include_total_sales' );
		add_option( 'wpswa_pro_include_ratings' );
		add_option( 'wpswa_pro_include_dimensions_weight' );
		add_option( 'wpswa_pro_show_default_hit_template', 'yes' );
		add_option( 'wpswa_pro_only_products' );
		add_option( 'wpswa_pro_product_catalog_visibility' );
		add_option( 'wpswa_pro_noindex_sold_out' );
	}

	/**
	 * Execute our hooks for WooCommerce based settings.
	 *
	 * @since 1.0.0
	 */
	public function do_hooks() {
		add_action( 'admin_menu', [ $this, 'add_page' ], 11 );
		add_action( 'admin_init', [ $this, 'add_settings' ] );

		add_filter( 'wpswa_pro_option_keys', [ $this, 'registered_options' ] );

		add_action( 'admin_init', function () {
			remove_submenu_page( 'algolia', 'algolia-account-woocommerce' );
		}, 11 );

		add_action( 'wpswa_pro_woocommerce_before_settings', [ $this, 'push_settings' ] );

		add_action( 'admin_notices', [ $this, 'admin_notices_woocommerce' ] );
	}

	/**
	 * Add our classes' available options to the array to delete upon deactivation.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Array of options to register for this setting type.
	 *
	 * @return array
	 */
	public function registered_options( array $options ): array {
		foreach (
			[
				'wpswa_pro_include_sku',
				'wpswa_pro_include_short_description',
				'wpswa_pro_include_total_sales',
				'wpswa_pro_include_ratings',
				'wpswa_pro_include_price',
				'wpswa_pro_include_sale_price',
				'wpswa_pro_include_dimensions_weight',
				'wpswa_pro_show_default_hit_template',
				'wpswa_pro_only_products',
				'wpswa_pro_product_catalog_visibility',
				'wpswa_pro_noindex_sold_out',
			] as $key
		) {
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
			esc_html__( 'WP Search with Algolia and WooCommerce', 'wp-search-with-algolia-pro' ),
			esc_html__( 'WooCommerce', 'wp-search-with-algolia-pro' ),
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
		$this->add_relevance_section();
		$this->add_display_section();
	}

	/**
	 * Register our relevance section and related settings fields.
	 *
	 * @since 1.0.0
	 */
	private function add_relevance_section() {
		add_settings_section(
			$this->relevance_section,
			esc_html__( 'Algolia search relevance', 'wp-search-with-algolia-pro' ),
			[ $this, 'relevance_content_callback' ],
			$this->slug
		);

		/*
		 * SKU
		 */
		add_settings_field(
			'wpswa_pro_include_sku',
			esc_html__( 'Product SKU', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_include_sku',
				'helptext'    => esc_html__( 'Useful to help look up products directly.', 'wp-search-with-algolia-pro' ),
				'extra_label' => esc_html__( 'Include SKU value in the index', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_include_sku',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Price
		 */
		add_settings_field(
			'wpswa_pro_include_price',
			esc_html__( 'Price', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_include_price',
				'extra_label' => esc_html__( 'Include price in index', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_include_price',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Sale Price
		 */
		add_settings_field(
			'wpswa_pro_include_sale_price',
			esc_html__( 'Sale price', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_include_sale_price',
				'extra_label' => esc_html__( 'Include sale price in index', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_include_sale_price',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Short Description
		 */
		add_settings_field(
			'wpswa_pro_include_short_description',
			esc_html__( 'Short description', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_include_short_description',
				'extra_label' => esc_html__( 'Include the short description in the index', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_include_short_description',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Total Sales
		 */
		add_settings_field(
			'wpswa_pro_include_total_sales',
			esc_html__( 'Total sales', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_include_total_sales',
				'helptext'    => esc_html__( 'Useful for popularity relevance. This will only be included for ranking and not template display.', 'wp-search-with-algolia-pro' ),
				'extra_label' => esc_html__( 'Include total sales in index', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_include_total_sales',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		$review_ratings_disabled = true;
		if ( function_exists( 'wc_review_ratings_enabled' ) ) {
			$review_ratings_disabled = ! wc_review_ratings_enabled();
		}

		/*
		 * Ratings
		 */
		add_settings_field(
			'wpswa_pro_include_ratings',
			esc_html__( 'Product rating', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_include_ratings',
				'extra_label' => esc_html__( 'Include product cumulative and average rating in index', 'wp-search-with-algolia-pro' ),
				'disabled'    => $review_ratings_disabled,
				'helptext'    => $review_ratings_disabled ? esc_html__( 'Enable ratings with reviews to use this feature.', 'wp-search-with-algolia-pro' ) : ''
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_include_ratings',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Dimensions/Weight
		 */
		add_settings_field(
			'wpswa_pro_include_dimensions_weight',
			esc_html__( 'Dimensions and weight', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_include_dimensions_weight',
				'extra_label' => esc_html__( 'Include product dimensions and weight', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_include_dimensions_weight',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Only index `product` post type.
		 */
		add_settings_field(
			'wpswa_pro_only_products',
			esc_html__( 'Products only indexing', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_only_products',
				'extra_label' => esc_html__( 'Makes only "product" post type searchable with Autocomplete and Instantsearch.', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_only_products',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Only index `product` post type.
		 */
		add_settings_field(
			'wpswa_pro_noindex_sold_out',
			esc_html__( 'Do not index sold out products', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_noindex_sold_out',
				'extra_label' => esc_html__( 'Prevents indexing of products that are presently sold out.', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_noindex_sold_out',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Catalog visibility.
		 */
		add_settings_field(
			'wpswa_pro_product_catalog_visibility',
			esc_html__( 'Catalog visibility indexing', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->relevance_section,
			[
				'label_for'   => 'wpswa_pro_product_catalog_visibility',
				'extra_label' => esc_html__( 'Prevents indexing a product if visibility is "Shop Only" or "Hidden".', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_product_catalog_visibility',
			[
				'sanitize_callback' => 'sanitize_text_field',
			]
		);
	}

	/**
	 * Register our search display section and related settings fields.
	 *
	 * @since 1.0.0
	 */
	private function add_display_section() {

		add_settings_section(
			$this->display_section,
			esc_html__( 'Algolia results display', 'wp-search-with-algolia-pro' ),
			[ $this, 'display_content_callback' ],
			$this->slug
		);

		/*
		 * Show default hit template.
		 */
		add_settings_field(
			'wpswa_pro_show_default_hit_template',
			esc_html__( 'Display product data', 'wp-search-with-algolia-pro' ),
			[ $this, 'checkbox' ],
			$this->slug,
			$this->display_section,
			[
				'label_for'   => 'wpswa_pro_show_default_hit_template',
				'extra_label' => esc_html__( 'Adds SKU, price, and if available, sale price to template output for Autocomplete and Instantsearch', 'wp-search-with-algolia-pro' ),
			]
		);

		register_setting(
			$this->option_group,
			'wpswa_pro_show_default_hit_template',
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
		require_once WPSWA_PRO_PATH . 'includes/admin/partials/page-woocommerce.php';
	}

	/**
	 * Callback to render our checkbox.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of extra arguments for checkbox callback.
	 */
	public function checkbox( array $args ): void {
		$value       = get_option( $args['label_for'], '' );
		$label       = esc_attr( $args['label_for'] );
		$helptext    = ! empty( $args['helptext'] ) ? esc_html( $args['helptext'] ) : '';
		$extra_label = ! empty( $args['extra_label'] ) ? esc_html( $args['extra_label'] ) : '';
		$disabled    = ! empty( $args['disabled'] ) ?? false;
		?>
		<input type="checkbox" id="<?php echo $label ?>" name="<?php echo $label ?>" value="yes" <?php checked( $value, 'yes' ); ?> <?php disabled( $disabled, true ); ?>/> <label for="<?php echo $label ?>"><?php echo $extra_label; ?></label>
		<p class="description"><?php echo $helptext; ?></p>
		<?php
	}

	/**
	 * Relevance callback to render content between the heading and the options themselves.
	 *
	 * @since 1.0.0
	 */
	public function relevance_content_callback() {
		echo '<p>' . esc_html__( 'These details aid in relevance and ranking.', 'wp-search-with-algolia-pro' ) . '</p>';
	}

	/**
	 * Search display callback to render content between the heading and the options themselves.
	 *
	 * @since 1.0.0
	 */
	public function display_content_callback() {
		echo '<p>' . esc_html__( 'These details aid in content indexed for template and results display.', 'wp-search-with-algolia-pro' ) . '</p>';
	}

	/**
	 * Return an array of current values for all of our WooCommerce settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_woocommerce_settings(): array {
		$all_settings = [];
		foreach (
			[
				'wpswa_pro_include_sku',
				'wpswa_pro_include_short_description',
				'wpswa_pro_include_total_sales',
				'wpswa_pro_include_ratings',
				'wpswa_pro_include_price',
				'wpswa_pro_include_sale_price',
				'wpswa_pro_include_dimensions_weight',
				'wpswa_pro_show_default_hit_template',
				'wpswa_pro_only_products',
				'wpswa_pro_noindex_sold_out',
				'wpswa_pro_product_catalog_visibility',
			]
			as $option
		) {
			$all_settings[ $option ] = get_option( $option, '' );
		}

		return $all_settings;
	}

	/**
	 * Push settings automatically on successful settings save.
	 *
	 * @return true[]|void
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function push_settings() {

		/**
		 * Filters whether or not to automatically push settings on save.
		 *
		 * Some people may want to not have Algolia index settings pushed automatically.
		 *
		 * @since 1.2.0
		 *
		 * @param bool $value Whether or not to disable automatic pushes. Default false.
		 * @return bool
		 */
		if ( true === apply_filters( 'wpswa_pro_disable_automatic_settings_push', false ) ) {
			return;
		}

		if ( 'wpswa_pro_woocommerce' !== $_REQUEST['page'] ) {
			return;
		}

		if (
			empty( $_REQUEST['settings-updated'] ) ||
			'true' !== $_REQUEST['settings-updated']
		) {
			return;
		}

		$plugin   = \Algolia_Plugin_Factory::create();
		$index_id = 'searchable_posts';

		try {
			if ( empty( $index_id ) ) {
				throw new \RuntimeException( 'index_id should be provided.' );
			}

			$index = $plugin->get_index( $index_id );
			if ( null === $index ) {
				throw new \RuntimeException( sprintf( 'Index named %s does not exist.', $index_id ) );
			}

			$index->push_settings();

			$response = [
				'success' => true,
			];

			return $response;

		} catch ( \Exception $exception ) {
			echo esc_html( $exception->getMessage() );
			throw $exception;
		}
	}

	/**
	 * Add messaging of successful saving and settings push.
	 *
	 * @since 1.0.0
	 */
	public function admin_notices_woocommerce() {
		if ( empty( $_REQUEST['page'] ) || 'wpswa_pro_woocommerce' !== $_REQUEST['page'] ) {
			return;
		}

		if (
			empty( $_REQUEST['settings-updated'] ) ||
			'true' !== $_REQUEST['settings-updated']
		) {
			return;
		}
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php esc_html_e( 'Settings saved and pushed up to Algolia.', 'wp-search-with-algolia-pro' ); ?>
			</p>
		</div>
		<?php
	}
}
