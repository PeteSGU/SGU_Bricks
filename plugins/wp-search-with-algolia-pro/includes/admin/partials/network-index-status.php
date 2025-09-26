<?php
/**
 * Template to display the network index status table.
 *
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

<div class="wpswap-network-index-status">
	<div class="wrap">
		<h3><?php esc_html_e( 'Network Index Status', 'wp-search-with-algolia-pro' ); ?></h3>
		<table class="wpswap-network-index-status-table wp-list-table widefat plugins striped">
			<tbody>
				<tr>
					<td class="total-indexable-sites-label">
						<?php esc_html_e( 'Total Indexable Sites', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="total-indexable-sites">
						<?php echo esc_html( $network_batch_status['indexable_site_count'] ); ?>
					</td>
				</tr>
				<tr>
					<td class="sites-indexed-label">
						<?php esc_html_e( 'Sites Indexed', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="sites-indexed">
					<?php
						echo esc_html(
							$wpswa_pro->network_index_manager->get_site_index_status_count(
								'complete',
								$network_batch_status
							)
						);
					?>
					</td>
				</tr>
				<tr>
					<td class="sites-not-yet-indexed-label">
						<?php esc_html_e( 'Sites Not Yet Indexed', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="sites-not-yet-indexed">
						<?php
							echo esc_html(
								$wpswa_pro->network_index_manager->get_site_index_status_count( 'incomplete', $network_batch_status )
							);
						?>
					</td>
				</tr>
				<tr>
					<td class="sites-excluded-label">
						<?php esc_html_e( 'Sites Excluded from Index', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="sites-excluded">
						<?php
							echo esc_html(
								$wpswa_pro->network_index_manager->get_site_index_status_count( 'excluded', $network_batch_status )
							);
						?>
					</td>
				</tr>
				<!-- <tr>
					<td class="next-site-to-index-label">
						<?php esc_html_e( 'Next Site ID to Index', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="next-site-to-index">
						<?php echo esc_html( $network_batch_status['next_site_to_index'] ); ?>
					</td>
				</tr> -->
				<tr>
					<td class="site-being-indexed-label">
						<?php esc_html_e( 'Site ID Currently Being Indexed', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="site-being-indexed"></td>
				</tr>
				<tr>
					<td class="batch-id-label">
						<?php esc_html_e( 'Network Batch ID', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="batch-id">
						<?php echo esc_html( $network_batch_status['batch_id'] ); ?>
					</td>
				</tr>
				<tr>
					<td class="batch-status-label">
						<?php esc_html_e( 'Network Batch Status', 'wp-search-with-algolia-pro' ); ?>
					</td>
					<td class="batch-status">
						<?php
							$string_id = $wpswa_pro->network_index_manager->get_string_id_from_batch_status(
								$network_batch_status['batch_status']
							);

						echo esc_html( $wpswa_pro->network_index_manager->get_text_to_display( $string_id ) );
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
