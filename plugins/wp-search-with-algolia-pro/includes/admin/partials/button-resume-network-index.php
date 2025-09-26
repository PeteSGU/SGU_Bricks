<?php
/**
 * This partial is used in the Settings_Network class.
 *
 * @author  WebDevStudios <contact@webdevstudios.com>
 * @since   1.3.0
 * @package WebDevStudios\WPSWAPro
 */

namespace WebDevStudios\WPSWAPro;

$wpswa_pro = WPSWAPro_Factory::create();

$network_batch_status = $wpswa_pro->network_index_manager->get_network_batch_status(
	Utils::get_network_and_visibilities(),
	$wpswa_pro->network_index_manager->get_network_batch_id()
);
?>
<button
	type="button"
	class="algolia-reindex-all-sites-button button button-primary"
	data-resume="true"
	data-site="<?php echo esc_attr( $network_batch_status['next_site_to_index'] ); ?>"
	data-button-type="resume-index"
	data-index="searchable_posts"
><?php esc_html_e( 'Resume Indexing Network', 'wp-search-with-algolia-pro' ); ?></button>
