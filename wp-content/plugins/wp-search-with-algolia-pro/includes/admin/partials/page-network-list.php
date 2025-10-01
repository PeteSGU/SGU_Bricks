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

<div class="wpswap-sites-status">
	<h2><?php esc_html_e( 'Current sites status', 'wp-search-with-algolia-pro' ); ?></h2>
	<table class="wp-list-table widefat plugins striped">
		<thead>
		<tr>
			<th><?php esc_html_e( 'ID', 'wp-search-with-algolia-pro' ); ?></th>
			<th><?php esc_html_e( 'Name', 'wp-search-with-algolia-pro' ); ?></th>
			<th><?php esc_html_e( 'Path', 'wp-search-with-algolia-pro' ); ?></th>
			<th><?php esc_html_e( 'Index Status', 'wp-search-with-algolia-pro' ); ?></th>
			<th><?php esc_html_e( 'Search engine visibility', 'wp-search-with-algolia-pro' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
			$sites                = Utils::get_network_and_visibilities();
			$wpswa_pro            = WPSWAPro_Factory::create();
			$network_batch_status = [];

			if ( ! empty( $wpswa_pro->network_index_manager ) ) {
				$network_batch_status = $wpswa_pro->network_index_manager->get_network_batch_status(
					$sites,
					$wpswa_pro->network_index_manager->get_network_batch_id()
				);
			}

			foreach ( $sites as $site ) {
				?>
				<tr data-site-id="<?php echo esc_attr( $site['id'] ); ?>">
					<td class="site-id"><?php echo esc_html( $site['id'] ); ?></td>
					<td class="site-name"><?php echo esc_html( $site['name'] ); ?></td>
					<td class="site-path">
					<?php
						printf(
							'<a href="%s">%s</a>',
							get_site_url(
								$site['id'],
								esc_url( '/wp-admin/admin.php?page=algolia-account-settings' )
							),
							esc_html( $site['site'] )
						);
					?>
					</td>
					<td class="site-index-status">
					<?php
					if ( $wpswa_pro->network_index_manager ) {
						echo esc_html(
							$wpswa_pro->network_index_manager->get_site_index_status_for_display(
								$wpswa_pro->network_index_manager->get_site_index_status(
									$site['id'],
									$network_batch_status
								)
							)
						);
					} else {
						esc_html_e( 'Unknown', 'wp-search-with-algolia-pro' );
					}
					?>
					</td>
					<td class="site-visibility"><?php echo esc_html( $site['visibility'] ); ?></td>
				</tr>
				<?php
			}
		?>
		</tbody>
		<tfoot>
		<tr>
			<td><?php esc_html_e( 'ID', 'wp-search-with-algolia-pro' ); ?></td>
			<td><?php esc_html_e( 'Name', 'wp-search-with-algolia-pro' ); ?></td>
			<td><?php esc_html_e( 'Path', 'wp-search-with-algolia-pro' ); ?></td>
			<th><?php esc_html_e( 'Index Status', 'wp-search-with-algolia-pro' ); ?></th>
			<td><?php esc_html_e( 'Search engine visibility', 'wp-search-with-algolia-pro' ); ?></td>
		</tr>
		</tfoot>
	</table>
</div>
