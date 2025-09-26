<?php
/**
 * Template to display Network Index button(s) and status table.
 *
 * @package WebDevStudios\WPSWAPro
 *
 * @author WebDevStudios <contact@webdevstudios.com>
 * @since  1.3.0
 */

namespace WebDevStudios\WPSWAPro;

if ( ! Utils::network_wide_indexing_enabled() ) :
	?>
	<div class="wpswap-network-wide-indexing-disabled">
		<p>
			<?php echo esc_html__( 'Enable the "Network-wide indexing" setting below to allow all sites in the network to be have thier data saved under a single Algolia index.', 'wp-search-with-algolia-pro' ); ?>
		</p>
	</div>
	<?php
	return;
endif;

$wpswa_pro = WPSWAPro_Factory::create();

$network_batch_status = $wpswa_pro->network_index_manager->get_network_batch_status(
	Utils::get_network_and_visibilities(),
	$wpswa_pro->network_index_manager->get_network_batch_id()
);
?>
<div class="wpswap-network-wide-indexing">
	<p>
	<?php
	// Show buttons.
	if ( 'not_started' === $network_batch_status['batch_status'] ) :
		$wpswa_pro->settings_network->get_network_index_button_template( 'create_network_index' );
	elseif ( 'complete' === $network_batch_status['batch_status'] ) :
		$wpswa_pro->settings_network->get_network_index_button_template( 'recreate_network_index' );
	elseif ( 'incomplete' === $network_batch_status['batch_status'] ) :
		$wpswa_pro->settings_network->get_network_index_button_template( 'resume_network_index' );
		$wpswa_pro->settings_network->get_network_index_button_template( 'recreate_network_index' );
	endif;

	?>
	</p>
	<p>
	<?php
	// Show status info.
	require_once WPSWA_PRO_PATH . 'includes/admin/partials/network-index-status.php';
	?>
	</p>
	<div class="push-settings">
		<hr/>
		<p>
			<?php require_once WPSWA_PRO_PATH . 'includes/admin/partials/push-settings-searchable-posts.php'; ?>
			<?php
				esc_html_e( "The Push Settings button will push the network-wide index settings
										to Algolia. These settings are pushed automatically when the index is first built,
										so this button should only be used if you've modified the searchable_posts
										index settings via code.",
										'wp-search-with-algolia-pro'
									);
			?>
		</p>
		<?php
				esc_html_e( 'Note that any index configuration that has been performed in the Algolia
										Dashboard will be overwritten when using the Push Settings button.',
										'wp-search-with-algolia-pro'
									);
		?>
		</p>
		<hr/>
	</div>
</div>
