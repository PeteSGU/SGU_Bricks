<?php
/**
 * This partial is used in the Settings_Network class.
 *
 * @author WebDevStudios <contact@webdevstudios.com>
 * @since   1.3.0
 * @package WebDevStudios\WPSWAPro
 */

?>

<div class="wrap">
	<?php do_action( 'wpswa_pro_network_settings_top' ); ?>
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended No processing here. ?>
		<div id="message" class="updated notice is-dismissible"><p><?php esc_html_e( 'Options saved.', 'wp-search-with-algolia-pro' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="edit.php?action=wpswa_pro_network_settings">
		<?php
		do_action( 'wpswa_pro_network_before_settings' );

		settings_fields( $this->option_group );
		do_settings_sections( $this->slug );
		submit_button();
		?>
	</form>
	<?php do_action( 'wpswa_pro_network_settings_bottom' ); ?>
</div>
