<?php
/**
 * Network_Index_Manager class file.
 *
 * @author  WebDevStudios <contact@webdevstudios.com>
 * @since   1.3.0
 *
 * @package WebDevStudios\WPSWA
 */

namespace WebDevStudios\WPSWAPro;

use WebDevStudios\WPSWAPro\Utils;
use WP_Error;
use RuntimeException;
use Exception;

/**
 * Class Network_Index_Manager
 *
 * @author  WebDevStudios <contact@webdevstudios.com>
 * @since   1.3.0
 */
final class Network_Index_Manager {

	/**
	 * The Algolia Plugin instance.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @var Algolia_Plugin
	 */
	private $wpswa_free;

	/**
	 * Network batch option name.
	 *
	 * This option is saved at the network level to indicate the
	 * current network index batch.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @var string
	 */
	private $network_batch_option_name = 'wpswa_pro_network_batch';

	/**
	 * Network batch complete option name.
	 *
	 * This option is saved at the site level to indicate the
	 * most recent network batch that the site was indexed under.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @var string
	 */
	public $network_batch_complete_option_name = 'wpswa_pro_network_batch_complete';

	/**
	 * REST route namespace.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @var string
	 */
	public $route_namespace = 'wpswap';

	/**
	 * REST route for building the network-wide index.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @var string
	 */
	public $index_network_route = '/index-network';

	/**
	 * Searchable Posts Index name.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @var string
	 */
	private $searchable_posts_index_name = 'searchable_posts';

	/**
	 * Constructor.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 */
	public function __construct() {
		add_site_option( $this->network_batch_option_name, '' );

		$this->wpswa_free = Utils::get_wpswa( \Algolia_Plugin_Factory::create() );
	}

	/**
	 * Add hooks.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @return void
	 */
	public function do_hooks() {
		if ( ! Utils::get_network_api_is_reachable() ) {
			return;
		}

		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'wp_ajax_wpswa_pro_index_network_site', [ $this, 'index_network_site' ] );
		add_filter( 'algolia_clear_index_if_existing', [ $this, 'clear_index_if_existing' ], 10, 2 );
		add_filter( 'algolia_searchable_posts_index_settings', [ $this, 'searchable_posts_index_settings' ], 10, 1 );
		add_action( 'init', [ $this, 'remove_indexing_notices' ], 20 );
	}

	/**
	 * Remove the indexing notices created by the free plugin.
	 *
	 * When the network-wide index is enabled, only the searchable_posts index is used
	 * and it is handled differently than the single site searchable_posts index, so we
	 * don't want to show the notice containing the Index button.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @var string
	 */
	public function remove_indexing_notices() {
		if ( ! empty( $this->wpswa_free->admin ) ) {
			remove_action( 'admin_notices', [ $this->wpswa_free->admin, 'display_reindexing_notices' ] );
		}
	}

	/**
	 * Prevent the index from being overwritten as each site is added.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param bool   $clear_if_existing Whether to clear the existing index or not.
	 * @param string $index_id          The index ID without prefix.
	 */
	public function clear_index_if_existing( $clear_if_existing, $index_id ) {
		if ( 'searchable_posts' === $index_id ) {
			return false;
		}

		return $clear_if_existing;
	}

	/**
	 * Modifiy searchable_posts index settings.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param array $settings Alogolia settings for the searchable_posts index.
	 *
	 * @return array $index_id searchable_posts index settings.
	 */
	public function searchable_posts_index_settings( $settings ) {
		/*
			When using a network-wide index, the default value of post_id no-longer
			works as intended because there can be multiple index entries with the same
			post_id across the network-wide index.
		*/
		$settings['attributeForDistinct'] = 'permalink';

		return $settings;
	}

	/**
	 * Register REST routes
	 */
	public function register_rest_routes() {
		// Register the REST route used for generating the network-wide index.
		register_rest_route(
			$this->route_namespace,
			$this->index_network_route,
			[
				'methods'             => [ 'POST' ],
				'callback'            => [ $this, 'index_network' ],
				'permission_callback' => [ $this, 'index_network_permissions_callback' ],
				'args'                => [],
			]
		);
	}

	/**
	 * Check permissions for accessing the REST route used for generating the network-wide index.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return bool True if the user can access this route, false otherwise.
	 */
	public function index_network_permissions_callback( $request ) {
		return is_super_admin();
	}

	/**
	 * Get the REST endpoint used for generating the network-wide index.
	 */
	public function get_index_network_endpoint() {
		return 'wp-json/' . $this->route_namespace . $this->index_network_route;
	}

	/**
	 * Callback for the REST route used for generating the network-wide index.
	 *
	 * The REST API is used to ensure that WP is fully loaded for each site, ensuring that
	 * the theme and plugins are available.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @throws RuntimeException If site ID, index ID, page are not provided, or index name does not exist.
	 */
	public function index_network( $request ) {
		$params            = $request->get_params();
		$site_id_requested = filter_var( $params['site_id'], FILTER_SANITIZE_NUMBER_INT );
		$index_id          = filter_var( $params['index_id'], FILTER_SANITIZE_SPECIAL_CHARS );
		$page              = filter_var( $params['p'], FILTER_SANITIZE_NUMBER_INT );
		$resume            = filter_var( $params['resume'], FILTER_VALIDATE_BOOLEAN );

		try {
			if ( empty( $index_id ) ) {
				throw new RuntimeException( esc_html__( 'Index ID should be provided.', 'wp-search-with-algolia-pro' ) );
			}

			if ( ! ctype_digit( $page ) ) {
				throw new RuntimeException( esc_html__( 'Page should be provided.', 'wp-search-with-algolia-pro' ) );
			}
			$page = (int) $page;

			if ( ! ctype_digit( $site_id_requested ) ) {
				throw new RuntimeException( esc_html__( 'Site ID should be provided.', 'wp-search-with-algolia-pro' ) );
			}
			$site_id_requested = (int) $site_id_requested;

			$site_id_to_index = null;
			$sites            = Utils::get_network_and_visibilities();

			$index = $this->wpswa_free->get_index( $index_id );

			if ( null === $index ) {
				throw new RuntimeException(
					/* translators: Algolia index name */
					sprintf(
						esc_html__( 'Index named %s does not exist.', 'wp-search-with-algolia-pro' ),
						$index_id
					)
				);
			}

			// $site_id_requested will be 0 on first run. Perform one-time tasks.
			if ( 0 === $site_id_requested && false === $resume ) {
				// Clear the existing index when recreating it.
				$index->clear();

				// Create a new network batch ID (timestamp).
				$this->set_network_batch_id();
			}

			$network_batch_status = $this->get_network_batch_status( $sites, $this->get_network_batch_id() );
			$site_id_to_index     = $network_batch_status['next_site_to_index'];

			// Bail now if there is no site to index.
			if ( null === $site_id_to_index ) {
				$response = $this->prepare_response_data(
					$network_batch_status,
					$site_id_to_index,
					1,
					true,
				);
				wp_send_json( $response );
			}

			$network_batch_id = $this->get_network_batch_id();
			$total_pages      = $index->get_re_index_max_num_pages();

			ob_start();
			if ( $page <= $total_pages || 0 === $total_pages ) {
				$index->re_index( $page );
			}
			ob_end_clean();

			$pages_finished = $page >= $total_pages;

			// If indexing is complete for this site, mark it with the current batch id (timestamp).
			if ( true === $pages_finished ) {
				update_option( $this->network_batch_complete_option_name, $network_batch_id );
			}

			$response = $this->prepare_response_data(
				$this->get_network_batch_status(
					Utils::get_network_and_visibilities(),
					$this->get_network_batch_id()
				),
				$site_id_to_index,
				$total_pages,
				$pages_finished,
			);

			wp_send_json( $response );
		} catch ( Exception $exception ) {
			return new WP_Error(
				'rest-error',
				$exception->getMessage(),
				[ 'status' => 400 ]
			);
		}
	}

	/**
	 * Index a single site within the network index.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @throws RuntimeException If site ID, index ID, page are not provided, or index name does not exist.
	 * @throws Exception        If site ID, index ID, or page are not provided, or index name does not exist.
	 */
	public function index_network_site() {
		$site_id  = filter_input( INPUT_POST, 'site_id', FILTER_SANITIZE_SPECIAL_CHARS );
		$index_id = filter_input( INPUT_POST, 'index_id', FILTER_SANITIZE_SPECIAL_CHARS );
		$page     = filter_input( INPUT_POST, 'p', FILTER_SANITIZE_SPECIAL_CHARS );

		try {
			if ( empty( $index_id ) ) {
				throw new RuntimeException( esc_html__( 'Index ID should be provided.', 'wp-search-with-algolia-pro' ) );
			}

			if ( ! ctype_digit( $page ) ) {
				throw new RuntimeException( esc_html__( 'Page should be provided.', 'wp-search-with-algolia-pro' ) );
			}
			$page = (int) $page;

			if ( ! ctype_digit( $site_id ) ) {
				throw new RuntimeException( esc_html__( 'Site ID should be provided.', 'wp-search-with-algolia-pro' ) );
			}
			$site_id = (int) $site_id;

			$index = $this->wpswa_free->get_index( $index_id );

			if ( null === $index ) {
				throw new RuntimeException( sprintf( esc_html__( 'Index named %s does not exist.', 'wp-search-with-algolia-pro' ), $index_id ) );
			}

			$network_batch_id = $this->get_network_batch_id();

			// If no network batch ID exists yet, create it.
			if ( empty( $network_batch_id ) ) {
				$this->set_network_batch_id();
				$network_batch_id = $this->get_network_batch_id();
			}

			$total_pages = $index->get_re_index_max_num_pages();

			/*
				Delete items.
				Note 1: Index items could potentially be orphaned if the index has been created
				and a post is deleted outside of the WP API (e.g. directly via DB).

				Note 2: The existing index is going to be deleted even if the site is private
				which means it's not configured for indexing. This allows for a site's index to
				be deleted after it has been created when switching a site from public to private.
			*/
			$items = $index->get_items( $page, $index->get_re_index_batch_size() );

			foreach ( $items as $item ) {
				$index->delete_item( $item );
			}

			$should_index = Utils::should_index_network_site( $site_id );

			// If the site is indexable, reindex.
			if ( ! empty( $should_index ) ) {
				ob_start();
				if ( $page <= $total_pages || 0 === $total_pages ) {
					$index->re_index( $page );
				}
				ob_end_clean();

				$pages_finished = $page >= $total_pages;
			} else {
				// Skip indexing if the site is not indexable.
				$pages_finished = true;
			}

			// If indexing is complete for this site, mark it with the current batch id (timestamp).
			if ( true === $pages_finished ) {
				update_option( $this->network_batch_complete_option_name, $network_batch_id );
			}

			$response = $this->prepare_response_data(
				$this->get_network_batch_status(
					Utils::get_network_and_visibilities(),
					$network_batch_id
				),
				$site_id,
				$total_pages,
				$pages_finished,
			);

			wp_send_json( $response );
		} catch ( Exception $exception ) {
			echo esc_html( $exception->getMessage() );
			throw $exception;
		}
	}

	/**
	 * Get the network batch status.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param array $sites            Network site data. See Utils::get_network_and_visibilities().
	 * @param ?int  $network_batch_id The current network batch ID.

	 * @return array Network batch status data.
	 */
	public function get_network_batch_status( array $sites, ?int $network_batch_id ) {

		$index = $this->wpswa_free->get_index( $this->searchable_posts_index_name );
		if ( empty( $index ) || ! $index->exists() ) {
			// Create a new network batch ID (timestamp).
			$this->set_network_batch_id();

			$network_batch_id = $this->get_network_batch_id();
		}

		$status = [
			'indexable_site_count' => 0,
			'batch_id'             => $network_batch_id,
			'sites'                => [],
			'next_site_to_index'   => $this->get_next_site_to_index( $sites, $network_batch_id ),
			'batch_status'         => 'undefined', // undefined, complete, incomplete, not_started.
		];

		foreach ( $sites as $site ) {
			if ( true !== $site['should_index'] ) {
				// Site is excluded from indexing.
				$site['index_status'] = 'excluded';

				// Add site to the sites array.
				$status['sites'][] = $site;

				continue;
			}

			$status['indexable_site_count'] = $status['indexable_site_count'] + 1;

			if ( $site['last_completed_network_batch'] === $network_batch_id ) {
				// Site has been indexed under current netork batch ID.
				$site['index_status'] = 'complete';

				// Add site to the sites array.
				$status['sites'][] = $site;
				continue;
			}

			// Site has been not yet been indexed under current netork batch ID.
			$site['index_status'] = 'incomplete';

			// Add site to the sites array.
			$status['sites'][] = $site;
		}

		if ( empty( $network_batch_id ) || empty( $index ) || ! $index->exists() ) {
			$status['batch_status'] = 'not_started';
		} elseif ( $this->get_site_index_status_count( 'complete', $status ) === $status['indexable_site_count'] ) {
			$status['batch_status'] = 'complete';
		} elseif ( $this->get_site_index_status_count( 'complete', $status ) < $status['indexable_site_count'] ) {
			$status['batch_status'] = 'incomplete';
		} else {
			$status['batch_status'] = 'undefined';
		}

		return $status;
	}

	/**
	 * Get the next site ID to index.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param array $sites            Network site data. See Utils::get_network_and_visibilities().
	 * @param ?int  $network_batch_id The current network batch ID.
	 *
	 * @return ?int The ID of the next site to index.
	 */
	private function get_next_site_to_index( array $sites, ?int $network_batch_id ): ?int {

		foreach ( $sites as $site ) {
			// Skip sites that aren't being indexed.
			if ( true !== $site['should_index'] ) {
				continue;
			}

			// Skip sites that have already been processed.
			if ( $site['last_completed_network_batch'] === $network_batch_id ) {
				continue;
			}

			return intval( $site['id'] );
		}

		return null;
	}

	/**
	 * Get the index status for a site within the network-wide index.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.5.0
	 *
	 * @param int $site_id The site ID to get the index status for.

	 * @return string Index status for site.
	 */
	public function get_site_index_status( int $site_id, array $network_batch_status ): string {
		$site_id = intval( $site_id );
		$status = 'undefined';

		foreach ( $network_batch_status['sites'] as $site ) {
			if ( $site_id !== intval( $site['id'] ) ) {
				continue;
			}

			return $site['index_status'];
		}
		return $status;
	}

	/**
	 * Get the index status for display for a site within the network-wide index.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.5.0
	 *
	 * @param string $status The index status to display.

	 * @return string Index status for display for site.
	 */
	public function get_site_index_status_for_display( $status ): ?string {
		return $this->get_text_to_display(
			$this->get_string_id_from_batch_status(
				$status
			)
		);
	}

	/**
	 * Get the site count for sites matching the index status specified.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param string $status      The indexing status to count. Either 'complete', 'inclomplete', or 'excluded'.
	 * @param array  $status_data Network batch status data. See Network_Index_Manager::get_network_batch_status().

	 * @return int Count of sites matching the specified status. Null if an invalid status is requested.
	 */
	public function get_site_index_status_count( string $status, array $status_data ): ?int {
		if ( ! in_array( $status, [ 'complete', 'incomplete', 'excluded' ], true ) ) {
			return null;
		}

		$count = 0;

		foreach ( $status_data['sites'] as $site ) {
			if ( $status === $site['index_status'] ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Get the current network batch id.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @return int The network batch id (timestamp).
	 */
	public function get_network_batch_id() {
		return (int) get_site_option( $this->network_batch_option_name );
	}

	/**
	 * Set the network batch id. The value is set to the current timestamp.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @return bool True if the value was updated, false otherwise.
	 */
	private function set_network_batch_id() {
		return update_site_option( $this->network_batch_option_name, (int) time() );
	}

	/**
	 * Set up data to return in AJAX response.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param array $network_batch_status Network batch status data. See Network_Index_Manager::get_network_batch_status().
	 * @param ?int  $site_id              The site ID being processed.
	 * @param int   $total_pages          The total number of "pages" of data to index. By default, a page contains 100 posts.
	 * @param bool  $pages_finished       Flag to indicate if the current set of pages is done being processed.
	 *
	 * @return array Data to send return in AJAX response.
	 */
	private function prepare_response_data(
		array $network_batch_status,
		?int $site_id,
		int $total_pages,
		bool $pages_finished
	): array {
		return [
			'sites'                   => $network_batch_status['sites'],
			'totalSitesCount'         => $network_batch_status['indexable_site_count'],
			'sitesIndexedCount'       => $this->get_site_index_status_count( 'complete', $network_batch_status ),
			'sitesNotIndexedCount'    => $this->get_site_index_status_count( 'incomplete', $network_batch_status ),
			'batchId'                 => $network_batch_status['batch_id'],
			'batchStatus'             => $network_batch_status['batch_status'],
			'nextSiteId'              => $network_batch_status['next_site_to_index'],
			'siteId'                  => $site_id,
			'totalPagesCount'         => $total_pages,
			'finished'                => $pages_finished,
			'networkIndexCompleteMsg' => esc_html__( 'Network index successfully updated', 'wp-search-with-algolia-pro' ),
			'siteIndexCompleteMsg'    => esc_html__( 'Network site index successfully updated', 'wp-search-with-algolia-pro' ),
		];
	}

	/**
	 * Get batch status string ID.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param string $batch_status The network batch status.
	 *
	 * @return ?string ID for translating the batch status into a translation-friendly string.
	 */
	public function get_string_id_from_batch_status( ?string $batch_status ): ?string {
		switch ( $batch_status ) {
			case 'not_started':
				return 'network_index_status_not_started';
			case 'incomplete':
				return 'network_index_status_incomplete';
			case 'complete':
				return 'network_index_status_complete';
			case 'preparing':
				return 'network_index_status_preparing';
			case 'indexing':
				return 'network_index_status_indexing';
			case 'excluded':
				return 'network_index_status_excluded';
			case 'undefined':
				return 'network_index_status_undefined';
		}

		return null;
	}

	/**
	 * Get translation friendly text.
	 *
	 * @author  WebDevStudios <contact@webdevstudios.com>
	 * @since   1.3.0
	 *
	 * @param string $string_id The ID of the string to get.
	 *
	 * @return ?string Translation-friendly string or null if no strings match the ID specfified.
	 */
	public function get_text_to_display( ?string $string_id ): ?string {
		switch ( $string_id ) {
			// Network Index Status.
			case 'network_index_status_not_started':
				return esc_html__( 'Not started', 'wp-search-with-algolia-pro' );
			case 'network_index_status_incomplete':
				return esc_html__( 'Incomplete', 'wp-search-with-algolia-pro' );
			case 'network_index_status_complete':
				return esc_html__( 'Complete', 'wp-search-with-algolia-pro' );
			case 'network_index_status_preparing':
				return esc_html__( 'Preparing to index...', 'wp-search-with-algolia-pro' );
			case 'network_index_status_indexing':
				return esc_html__( 'Indexing...', 'wp-search-with-algolia-pro' );
			case 'network_index_status_undefined':
				return esc_html__( 'Error', 'wp-search-with-algolia-pro' );
			case 'network_index_status_excluded':
				return esc_html__( 'Excluded', 'wp-search-with-algolia-pro' );

			// Dialog.
			case 'dialog_confirm_overwrite_network_index':
				return esc_html__( 'Clicking OK will clear the entire existing network-wide index before building a new one. Are you sure that you want to continue?', 'wp-search-with-algolia-pro' );
			case 'dialog_confirm_overwrite_network_site_index':
				return esc_html__( 'Clicking OK will clear the existing site index before building a new one. Are you sure that you want to continue?', 'wp-search-with-algolia-pro' );
		}

		return null;
	}
}
