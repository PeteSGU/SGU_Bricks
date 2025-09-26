<?php
/**
 * This partial is used in the Settings_Network class.
 *
 * @author  WebDevStudios <contact@webdevstudios.com>
 * @since   1.3.0
 * @package WebDevStudios\WPSWAPro
 */

namespace WebDevStudios\WPSWAPro;

?>

<button
		type="button"
		class="algolia-reindex-all-sites-button button button-primary"
		data-resume="false"
		data-button-type="recreate-index"
		data-index="searchable_posts"
	><?php esc_html_e( 'Re-Index Network', 'wp-search-with-algolia-pro' ); ?></button>
