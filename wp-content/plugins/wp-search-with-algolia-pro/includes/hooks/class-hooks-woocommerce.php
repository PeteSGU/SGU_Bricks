<?php
/**
 * Hooks_WooCommerce Class file
 *
 * @package WebDevStudios\WPSWAPro
 * @since   1.0.0
 */

namespace WebDevStudios\WPSWAPro\Hooks;

use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;

/**
 * Class WooCommerce
 *
 * @since 1.0.0
 */
class Hooks_WooCommerce {

	/**
	 * Saved options for WooCommerce settings.
	 *
	 * @var array|mixed
	 */
	private $available_options;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param array $available_options Array of WooCommerce options.
	 */
	public function __construct( array $available_options = [] ) {
		$this->available_options = $available_options;
	}

	/**
	 * Execute our hooks for WooCommerce based functionality.
	 *
	 * @since 1.0.0
	 */
	public function do_hooks() {

		/*
		 * Attributes.
		 */
		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'index_sku' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'index_sku' ], 10, 2 );

		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'index_short_description' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'index_short_description' ], 10, 2 );

		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'index_total_sales' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'index_total_sales' ], 10, 2 );

		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'index_ratings' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'index_ratings' ], 10, 2 );

		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'index_prices' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'index_prices' ], 10, 2 );

		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'index_dimensions_weight' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'index_dimensions_weight' ], 10, 2 );

		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'index_woocommerce_image_sizes' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'index_woocommerce_image_sizes' ], 10, 2 );

		/*
		 * Index settings.
		 */
		add_filter( 'algolia_searchable_post_product_shared_attributes', [ $this, 'remove_attributes_on_products' ], 10, 2 );
		add_filter( 'algolia_post_product_shared_attributes', [ $this, 'remove_attributes_on_products' ], 10, 2 );

		add_filter( 'algolia_searchable_posts_index_settings', [ $this, 'set_total_sales_as_unretrievable' ] );
		add_filter( 'algolia_posts_product_index_settings', [ $this, 'set_total_sales_as_unretrievable' ] );

		add_filter( 'algolia_searchable_posts_index_settings', [ $this, 'rank_total_sales' ] );
		add_filter( 'algolia_posts_product_index_settings', [ $this, 'rank_total_sales' ] );

		add_filter( 'algolia_searchable_posts_index_settings', [ $this, 'disable_typo_tolerance_for_sku' ] );
		add_filter( 'algolia_posts_product_index_settings', [ $this, 'disable_typo_tolerance_for_sku' ] );

		add_filter( 'algolia_searchable_posts_index_settings', [ $this, 'set_attributes_as_searchable' ] );
		add_filter( 'algolia_posts_product_index_settings', [ $this, 'set_attributes_as_searchable' ] );

		/*
		 * Update total sales for products upon order.
		 */
		add_action( 'woocommerce_order_status_completed', [ $this, 'update_total_sales_on_completed' ] );

		/*
		 * Template output.
		 */
		add_action( 'algolia_autocomplete_after_hit', [ $this, 'hit_template_content_output' ] );
		add_action( 'algolia_instantsearch_after_hit', [ $this, 'hit_template_content_output' ] );

		/*
		 * Only products indexed.
		 */
		add_filter( 'algolia_searchable_post_types', [ $this, 'only_products_indexed' ] );

		/*
		 * Sold out products.
		 */
		add_filter( 'algolia_should_index_post', [ $this, 'index_sold_out_products' ], 10, 2 );
		add_filter( 'algolia_should_index_searchable_post', [ $this, 'index_sold_out_products' ], 10, 2 );
		add_action( 'woocommerce_order_status_completed', [ $this, 'update_sold_out_indexing_on_completed' ] );

		add_filter( 'algolia_should_index_searchable_post', [ $this, 'exclude_by_product_catalog_visibility' ], 10, 2 );
		add_filter( 'algolia_should_index_post', [ $this, 'exclude_by_product_catalog_visibility' ], 10, 2 );
	}

	/**
	 * Add product SKU to shared attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $shared_attributes Current shared attributes.
	 * @param \WP_Post $post              Product being indexed.
	 *
	 * @return array
	 */
	public function index_sku( array $shared_attributes, \WP_Post $post ): array {
		if ( empty( $this->available_options['options']['wpswa_pro_include_sku'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_include_sku'] ) {
			return $shared_attributes;
		}

		$product = wc_get_product( $post );

		if ( ! $product ) {
			return $shared_attributes;
		}

		$shared_attributes['sku'] = $product->get_sku();

		return $shared_attributes;
	}

	/**
	 * Add product short description to shared attributes.
	 *
	 * @since 1.0.0
	 * @param array    $shared_attributes Current shared attributes.
	 * @param \WP_Post $post              Product being indexed.
	 *
	 * @return array
	 */
	public function index_short_description( array $shared_attributes, \WP_Post $post ): array {
		if ( empty( $this->available_options['options']['wpswa_pro_include_short_description'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_include_short_description'] ) {
			return $shared_attributes;
		}

		$product = wc_get_product( $post );

		if ( ! $product ) {
			return $shared_attributes;
		}

		$shared_attributes['short_description'] = $product->get_short_description();

		return $shared_attributes;
	}

	/**
	 * Add product total sales to shared attributes.
	 *
	 * @since 1.0.0
	 * @param array    $shared_attributes Current shared attributes.
	 * @param \WP_Post $post              Product being indexed.
	 *
	 * @return array
	 */
	public function index_total_sales( array $shared_attributes, \WP_Post $post ): array {
		if ( empty( $this->available_options['options']['wpswa_pro_include_total_sales'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_include_total_sales'] ) {
			return $shared_attributes;
		}
		$product = wc_get_product( $post );

		if ( ! $product ) {
			return $shared_attributes;
		}

		$shared_attributes['total_sales'] = $product->get_total_sales();

		return $shared_attributes;
	}

	/**
	 * Add product rating to shared attributes.
	 *
	 * @since 1.0.0
	 * @param array    $shared_attributes Current shared attributes.
	 * @param \WP_Post $post              Product being indexed.
	 *
	 * @return array
	 */
	public function index_ratings( array $shared_attributes, \WP_Post $post ): array {
		if ( empty( $this->available_options['options']['wpswa_pro_include_ratings'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_include_ratings'] ) {
			return $shared_attributes;
		}
		$product = wc_get_product( $post );

		if ( ! $product || ! wc_review_ratings_enabled() ) {
			return $shared_attributes;
		}

		// for rating widgets, we need to have an integer. Algolia considers `4.3` as `4` thus we are rounding down.
		$shared_attributes['ratings']                   = [];
		$shared_attributes['ratings']['total_ratings']  = $product->get_rating_count();
		$shared_attributes['ratings']['average_rating'] = (int) floor( $product->get_average_rating() );

		return $shared_attributes;
	}

	/**
	 * Add product dimensions and weight to shared attributes.
	 *
	 * @since 1.4.0
	 *
	 * @param array    $shared_attributes Current shared attributes.
	 * @param \WP_Post $post              Product being indexed.
	 *
	 * @return array
	 */
	public function index_dimensions_weight( array $shared_attributes, \WP_Post $post ): array {

		if ( empty( $this->available_options['options']['wpswa_pro_include_dimensions_weight'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_include_dimensions_weight'] ) {
			return $shared_attributes;
		}

		$product = wc_get_product();

		if ( ! $product ) {
			return $shared_attributes;
		}

		$shared_attributes['physical_attributes'] = [
			'weight'     => '',
			'dimensions' => '',
		];

		if ( $product->has_weight() ) {
			$shared_attributes['physical_attributes']['weight'] = wc_format_weight( $product->get_weight() );
		}

		if ( $product->has_dimensions() ) {
			$shared_attributes['physical_attributes']['dimensions'] = wc_format_dimensions( $product->get_dimensions( false ) );
		}

		return $shared_attributes;
	}

	/**
	 * Add product prices to shared attributes.
	 *
	 * @since 1.0.0
	 * @param array    $shared_attributes Current shared attributes.
	 * @param \WP_Post $post              Product being indexed.
	 *
	 * @return array
	 */
	public function index_prices( array $shared_attributes, \WP_Post $post ): array {

		$product = wc_get_product( $post );

		if ( ! $product ) {
			return $shared_attributes;
		}

		$include_currency = false;
		if ( 'yes' === $this->available_options['options']['wpswa_pro_include_price'] ) {
			if ( $product instanceof \WC_Product_Variable ) {
				$price                                        = (float) $product->get_variation_price( 'min', true );
				$regular_price                                = (float) $product->get_variation_regular_price( 'min', true );
				$max_price                                    = (float) $product->get_variation_price( 'max', true );
				$shared_attributes['regular_price']           = $regular_price;
				$shared_attributes['regular_price_formatted'] = wc_price( $regular_price );
				$shared_attributes['max_price']               = $max_price;
				$shared_attributes['max_price_formatted']     = wc_price( $max_price );
			} else {
				$price = (float) $product->get_regular_price();
			}
			$shared_attributes['price']           = $price;
			$shared_attributes['price_formatted'] = wc_price( $price );
			$include_currency                     = true;
		}

		if ( 'yes' === $this->available_options['options']['wpswa_pro_include_sale_price'] ) {
			if ( $product instanceof \WC_Product_Variable ) {
				$sale_price = (float) $product->get_variation_sale_price( 'min', true );
			} else {
				$sale_price = (float) $product->get_sale_price();
			}
			$include_currency                          = true;
			$shared_attributes['sale_price']           = $sale_price;
			$shared_attributes['sale_price_formatted'] = wc_price( $sale_price );
		}

		if ( $include_currency ) {
			$currency_symbol                      = get_woocommerce_currency_symbol();
			$shared_attributes['currency_symbol'] = html_entity_decode( $currency_symbol );
		}

		if ( $product instanceof \WC_Product_Variable ) {
			$shared_attributes['variations_count'] = count( $product->get_available_variations() );
		}

		return $shared_attributes;
	}

	/**
	 * Add the three WooCommerce image sizes to our images array.
	 *
	 * @since 1.6.0
	 * @param array    $shared_attributes Current shared attributes
	 * @param \WP_Post $post              Product being indexed
	 *
	 * @return array
	 */
	public function index_woocommerce_image_sizes( array $shared_attributes, \WP_Post $post ): array {
		// Algolia attribute key => wc size name
		$sizes = [
			'wc_thumbnail'         => 'woocommerce_thumbnail',
			'wc_single'            => 'woocommerce_single',
			'wc_gallery_thumbnail' => 'woocommerce_gallery_thumbnail',
		];

		$product = wc_get_product( $post );

		if ( ! $product ) {
			return $shared_attributes;
		}

		foreach ( $sizes as $key => $size ) {
			$size_data = \wc_get_image_size( $size );
			$atts['url'] = wp_get_attachment_image_url( $product->get_image_id(), $size );
			$atts['width'] = $size_data['width'];
			$atts['height'] = $size_data['height'];
			$shared_attributes['images'][ $key ] = $atts;
		}

		return $shared_attributes;
	}

	/**
	 * Modify default indexed shared attributes on products.
	 *
	 * This method is meant to reduce some perceived non-needed attributes for
	 * Ecommerce based objects.
	 *
	 * @since 1.4.0
	 *
	 * @param array    $shared_attributes Array of shared attributes.
	 * @param \WP_Post $post              Current post being indexed.
	 *
	 * @return array
	 */
	public function remove_attributes_on_products( array $shared_attributes, \WP_Post $post ): array {

		/**
		 * Filters whether or not to retain all default attributes.
		 *
		 * @since 1.4.0
		 *
		 * @param bool $value Whether or not to retain all default attributes. Default false.
		 *
		 * @return bool
		 */
		if ( true === apply_filters( 'wpswa_pro_keep_all_default_attributes_on_product', false ) ) {
			return $shared_attributes;
		}

		/**
		 * Filters the attributes to remove from product objects going into Algolia.
		 *
		 * @since 1.4.0
		 *
		 * @param array    $value             Array of attributes that should be removed from the indexed attributes.
		 * @param array    $shared_attributes Original list of attributes being indexed.
		 * @param \WP_Post $post              WP_Post object for current item being indexed.
		 */
		$remove_properties = (array) apply_filters(
			'wpswa_pro_remove_attributes_on_products',
			[
				'is_sticky',
				'post_date_formatted',
				'post_author',
				'post_modified',
				'post_mime_type',
			],
			$shared_attributes,
			$post
		);
		foreach ( $remove_properties as $key ) {
			unset( $shared_attributes[ $key ] );
		}

		return $shared_attributes;
	}

	/**
	 * Prevent total sales metrics from being returned with frontend search results.
	 *
	 * We only want total sales to be used for relevance and ranking, and that data should not
	 * be publicly accessible.
	 *
	 * @since 1.0.0
	 * @param array $settings Array of settings.
	 *
	 * @return array
	 */
	public function set_total_sales_as_unretrievable( array $settings ): array {
		if ( empty( $this->available_options['options']['wpswa_pro_include_total_sales'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_include_total_sales'] ) {
			return $settings;
		}

		// Protect our sensitive data.
		$protected_attributes = [];

		if ( isset( $settings['unretrievableAttributes'] ) ) {
			// Ensure we merge our values with the existing ones if available.
			$protected_attributes = $settings['unretrievableAttributes'];
		}

		$protected_attributes[]              = 'total_sales';
		$settings['unretrievableAttributes'] = $protected_attributes;

		return $settings;
	}

	/**
	 * Add total sales as part of custom ranking configuration.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Algolia index settings.
	 *
	 * @return array
	 */
	public function rank_total_sales( array $settings ): array {
		if ( empty( $this->available_options['options']['wpswa_pro_include_total_sales'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_include_total_sales'] ) {
			return $settings;
		}

		array_unshift( $settings['customRanking'], 'desc(total_sales)' );
		return $settings;
	}

	/**
	 * Disable typo tolerance for SKUs for exact match purposes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Algolia index settings.
	 *
	 * @return array
	 */
	public function disable_typo_tolerance_for_sku( array $settings ): array {
		/**
		 * Filters whether or not SKU properties should keep typo tolerance.
		 *
		 * @since 1.6.0
		 *
		 * @param bool $value Whether or not to keep typo tolerance enabled.
		 */
		if ( true === apply_filters( 'algolia_keep_sku_typo_tolerance', false ) ) {
			return $settings;
		}

		$settings['disableTypoToleranceOnAttributes'] = [
			'sku',
		];

		return $settings;
	}

	/**
	 * Mark appropriate attributes as searchable.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Algolia index settings.
	 * @return array
	 */
	public function set_attributes_as_searchable( array $settings ): array {
		if ( 'yes' === $this->available_options['options']['wpswa_pro_include_sku'] ) {
			$settings['searchableAttributes'][] = 'unordered(sku)';
		}
		if ( 'yes' === $this->available_options['options']['wpswa_pro_include_short_description'] ) {
			$settings['searchableAttributes'][] = 'unordered(short_description)';
		}
		return $settings;
	}

	/**
	 * Set only `product` post type to be searchable and indexed.
	 *
	 * @since 1.2.0
	 *
	 * @param array $searchable_post_types Array of searchable post types.
	 * @return array
	 */
	public function only_products_indexed( $searchable_post_types ) {
		if ( 'yes' === $this->available_options['options']['wpswa_pro_only_products'] ) {
			return [ 'product' => 'product' ];
		}

		return $searchable_post_types;
	}

	/**
	 * Re-sync product indexes on completed order.
	 *
	 * @since 1.0.0
	 * @param int $order_id Order ID.
	 */
	public function update_total_sales_on_completed( int $order_id ) {
		$order       = wc_get_order( $order_id );
		$order_items = $order->get_items();

		$searchable_post_types = get_post_types(
			[
				'exclude_from_search' => false,
			],
		);
		$indices[]             = new \Algolia_Searchable_Posts_Index( $searchable_post_types );

		$algolia_plugin     = \Algolia_Plugin_Factory::create();
		$synced_indices_ids = $algolia_plugin->get_settings()->get_synced_indices_ids();
		$index_name_prefix  = $algolia_plugin->get_settings()->get_index_name_prefix();
		$client             = $algolia_plugin->get_api()->get_client();

		// Only include Autocomplete index if enabled.
		if ( in_array( 'posts_product', $synced_indices_ids, true ) ) {
			$indices[] = new \Algolia_Posts_Index( 'product' );
		}

		foreach ( $indices as $index ) {
			$index->set_name_prefix( $index_name_prefix );
			$index->set_client( $client );

			if ( in_array( $index->get_id(), $synced_indices_ids, true ) ) {
				$index->set_enabled( true );
			}
		}

		foreach ( $order_items as $item ) {
			$product_id = $item->get_product_id();
			$product    = get_post( $product_id );
			foreach ( $indices as $index ) {
				if ( ! $index->supports( $product ) ) {
					continue;
				}

				try {
					$index->sync( $product );
				} catch ( AlgoliaException $exception ) {
					error_log( $exception->getMessage() ); // phpcs:ignore -- Legacy.
				}
			}
		}
	}

	/**
	 * Check on a given product's catalog visibility to determine if we should index.
	 *
	 * @since 1.4.0
	 *
	 * @param bool     $should_index Whether or not we should index.
	 * @param \WP_Post $post         Product object.
	 *
	 * @return false|mixed
	 */
	public function exclude_by_product_catalog_visibility( $should_index, \WP_Post $post ) {

		if ( empty( $this->available_options['options']['wpswa_pro_product_catalog_visibility'] ) || 'yes' !== $this->available_options['options']['wpswa_pro_product_catalog_visibility'] ) {
			return $should_index;
		}

		if ( false === $should_index ) {
			return false;
		}

		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			return $should_index;
		}
		$product_visibility = $product->get_catalog_visibility();

		if ( in_array( $product_visibility, [ 'catalog', 'hidden' ] ) ) {
			$should_index = false;
		}

		/**
		 * Filters the should index status at the last moment before we return the intent.
		 *
		 * This can allow for programmatically limiting to just catalog or hidden, as opposed to forced for both.
		 *
		 * @since 1.4.0
		 *
		 * @param bool     $should_index Whether or not we should index
		 * @param \WP_Post $post         Current product object.
		 */
		return (bool) apply_filters(
			'wpswa_pro_should_exclude_by_product_catalog_visibility',
			$should_index,
			$post
		);
	}

	/**
	 * Checks if a given product is in stock or not, and de-indexes if not.
	 *
	 * @param bool     $should_index Whether or not the product should be indexed.
	 * @param \WP_Post $post         WP_Post object.
	 *
	 * @return false|mixed
	 * @since 1.4.0
	 */
	public function index_sold_out_products( $should_index, \WP_Post $post ) {
		if (
			empty( $this->available_options['options']['wpswa_pro_noindex_sold_out'] ) ||
			'yes' !== $this->available_options['options']['wpswa_pro_noindex_sold_out']
		) {
			return $should_index;
		}

		if ( false === $should_index ) {
			return $should_index;
		}

		$product = wc_get_product( $post );

		if ( ! $product ) {
			return $should_index;
		}

		if ( ! $product->is_in_stock() ) {
			$should_index = false;
		}

		return $should_index;
	}

	/**
	 * Syncs products from a new WooCommerce order upon completion, if now out of stock.
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @since 1.4.0
	 */
	public function update_sold_out_indexing_on_completed( int $order_id ) {
		if (
			empty( $this->available_options['options']['wpswa_pro_noindex_sold_out'] ) ||
			'yes' !== $this->available_options['options']['wpswa_pro_noindex_sold_out']
		) {
			return;
		}

		$order       = wc_get_order( $order_id );
		$order_items = $order->get_items();

		$searchable_post_types = get_post_types(
			[
				'exclude_from_search' => false,
			],
		);
		$indices[]             = new \Algolia_Searchable_Posts_Index( $searchable_post_types );

		$algolia_plugin     = \Algolia_Plugin_Factory::create();
		$synced_indices_ids = $algolia_plugin->get_settings()->get_synced_indices_ids();
		$index_name_prefix  = $algolia_plugin->get_settings()->get_index_name_prefix();
		$client             = $algolia_plugin->get_api()->get_client();

		// Only include Autocomplete index if enabled.
		if ( in_array( 'posts_product', $synced_indices_ids, true ) ) {
			$indices[] = new \Algolia_Posts_Index( 'product' );
		}

		foreach ( $order_items as $item ) {
			$product_id = $item->get_product_id();
			$product    = wc_get_product( $product_id );
			foreach ( $indices as $index ) {
				$index->set_name_prefix( $index_name_prefix );
				$index->set_client( $client );

				if ( in_array( $index->get_id(), $synced_indices_ids, true ) ) {
					$index->set_enabled( true );
				}

				if ( ! $index->supports( $product ) ) {
					continue;
				}

				// Product is still available, should stay indexed.
				if ( $product->is_in_stock() ) {
					continue;
				}

				try {
					$index->sync( $product );
				} catch ( AlgoliaException $exception ) {
					error_log( $exception->getMessage() ); // phpcs:ignore -- Legacy.
				}
			}
		}
	}

	/**
	 * Display SKU and prices in InstantSearch content output.
	 *
	 * @since 1.0.0
	 */
	public function hit_template_content_output() {
		if ( 'yes' !== $this->available_options['options']['wpswa_pro_show_default_hit_template'] ) {
			return;
		}

		ob_start();
	?>
		<p>
			<# if ( data.sku ) { #>
			<?php esc_html_e( 'SKU:', 'wp-search-with-algolia-pro' ); ?>
			{{ data.sku }}
			<# } #>

			<# if ( data.price || data.sale_price ) { #>
				<?php esc_html_e( 'Price:', 'wp-search-with-algolia-pro' ); ?>
				<# if ( data.sale_price ) { #>
					<# if ( data.price ) { #>
					<s>{{{ data.price_formatted }}}</s>
					<# } #>
					{{{ data.sale_price_formatted }}}
				<# } #>
				<# if ( ! data.sale_price && data.price ) { #>
					{{{ data.price_formatted }}}
					<# if ( data.max_price ) { #>
						- {{{ data.max_price_formatted }}}
					<# } #>
				<# } #>
			<# } #>
		</p>
	<?php
		echo ob_get_clean();
	}
}
