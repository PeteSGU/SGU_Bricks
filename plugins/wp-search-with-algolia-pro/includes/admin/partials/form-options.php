<?php
/**
 * This partial is used in the Settings_Admin_Page_Network_Settings class.
 *
 * @author  WebDevStudios <contact@webdevstudios.com>
 * @since   1.3.0
 * @package WebDevStudios\WPSWAPro
 */

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form method="post" action="edit.php?action=wpswa_pro_network_account_settings">
		<?php
		settings_fields( $this->option_group );
		do_settings_sections( $this->slug );

		wp_nonce_field( 'wpswa_pro_account_settings_nonce', 'wpswa_pro_account_settings_nonce' );
		submit_button();
		?>
	</form>
</div>
