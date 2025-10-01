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
	class="algolia-reindex-network-site-button button-primary"
	data-site="<?php echo esc_attr( \get_current_blog_id() ); ?>"
	data-button-type="index-site"
	data-context="site"
	data-index="searchable_posts"
><?php esc_html_e( 'Reindex Network Site', 'wp-search-with-algolia-pro' ); ?></button>
