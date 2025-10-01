<?php
/**
 * This partial is used in the Settings_Post_Type_Meta class.
 *
 * @author WebDevStudios <contact@webdevstudios.com>
 * @package WebDevStudios\WPSWAPro
 * @since   1.3.0
 */

?>

<div class="wrap">
	<?php do_action( 'wpswa_pro_post_type_meta_settings_top' ); ?>
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form method="post" action="options.php">
		<?php
		do_action( 'wpswa_pro_post_type_meta_before_settings' );

		settings_fields( $this->option_group );
		do_settings_sections( $this->slug );
		submit_button();
		?>
	</form>
	<?php do_action( 'wpswa_pro_post_type_meta_settings_bottom' ); ?>
</div>
