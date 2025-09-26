<?php
/**
 * This partial is used in the Settings_WooCommerce class.
 *
 * @author  WebDevStudios <contact@webdevstudios.com>
 * @since   1.0.0
 * @package WebDevStudios\WPSWAPro
 */

?>

<div class="wrap">
	<?php do_action( 'wpswa_pro_woocommerce_settings_top' ); ?>
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( ! \WebDevStudios\WPSWAPro\Utils::network_wide_indexing_enabled() ) : ?>
	<button type="button" class="algolia-reindex-button button button-primary" data-index="searchable_posts">
		<?php esc_html_e( 'Re-index records', 'wp-search-with-algolia-pro' ); ?>
	</button>
	<button type="button" class="algolia-push-settings-button button" data-index="searchable_posts">
		<?php esc_html_e( 'Push Settings', 'wp-search-with-algolia-pro' ); ?>
	</button>
	<?php endif; ?>
	<form method="post" action="options.php">
		<?php
		do_action( 'wpswa_pro_woocommerce_before_settings' );

		echo '<p>' . esc_html__( 'Settings will automatically be pushed to your Algolia Dashboard on save.', 'wp-search-with-algolia-pro' ) . '</p>';

		settings_fields( $this->option_group );
		do_settings_sections( $this->slug );
		submit_button();
		?>
	</form>
	<?php do_action( 'wpswa_pro_woocommerce_settings_bottom' ); ?>
</div>
