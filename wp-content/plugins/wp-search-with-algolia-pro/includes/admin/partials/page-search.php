<?php
/**
 * This partial is used in the Settings_Admin_Page_Network_Native_Search class.
 *
 * @author WebDevStudios <contact@webdevstudios.com>
 * @package WebDevStudios\WPSWAPro
 * @since   1.3.0
 */

use WebDevStudios\WPSWAPro\Utils;

?>

<div class="wrap">
	<?php do_action( 'wpswa_pro_network_search_settings_top' ); ?>
	<h1><?php echo esc_html( get_admin_page_title() ); ?>
		<?php if ( ! Utils::network_wide_indexing_enabled() ) : ?>
		<button type="button" class="algolia-reindex-button button button-primary" data-index="searchable_posts">
			<?php esc_html_e( 'Re-index All Content', 'wp-search-with-algolia-pro' ); ?>
		</button>
		<button type="button" class="algolia-push-settings-button button" data-index="searchable_posts">
			<?php esc_html_e( 'Push Settings', 'wp-search-with-algolia-pro' ); ?>
		</button>
		<?php endif; ?>
	</h1>

	<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended No processing here. ?>
		<div id="message" class="updated notice is-dismissible">
			<p><?php esc_html_e( 'Options saved.', 'wp-search-with-algolia-pro' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="edit.php?action=wpswa_pro_network_search_settings">
		<?php
		do_action( 'wpswa_pro_network_search_before_settings' );

		settings_fields( $this->option_group );
		do_settings_sections( $this->slug );
		submit_button();
		wp_nonce_field( 'wpswa_pro_search_settings_nonce', 'wpswa_pro_search_settings_nonce' );
		?>
	</form>
	<?php do_action( 'wpswa_pro_network_search_settings_bottom' ); ?>
</div>
