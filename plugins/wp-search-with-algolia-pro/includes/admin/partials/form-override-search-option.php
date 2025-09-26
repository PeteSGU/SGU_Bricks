<?php
/**
 * This partial is used in the Settings_Admin_Page_Network_Native_Search class.
 *
 * @author  WebDevStudios <contact@webdevstudios.com>
 * @since   1.3.0
 * @package WebDevStudios\WPSWAPro
 */

$network_wide_disabled = \WebDevStudios\WPSWAPro\Utils::network_wide_indexing_enabled();
?>

<div class="input-radio">
	<label>
		<input type="radio" value="native"
			name="algolia_network_override_native_search" <?php checked( $value, 'native' ); ?>>
		<?php esc_html_e( 'Do not use Algolia', 'wp-search-with-algolia-pro' ); ?>
	</label>
	<div class="radio-info">
		<?php
		echo wp_kses(
			__(
				'Do not use Algolia for searching at all. This option disables the plugin completely',
				'wp-search-with-algolia-pro'
			),
			[
				'br' => [],
			]
		);
		?>
	</div>

	<label>
		<input <?php disabled( $network_wide_disabled ); ?> type="radio" value="backend"
			name="algolia_network_override_native_search" <?php checked( $value, 'backend' ); ?>>
		<?php if ( $network_wide_disabled ) : ?>
			<s>
				<?php esc_html_e( 'Use Algolia with the native WordPress search template', 'wp-search-with-algolia-pro' ); ?>
			</s>
		<?php else : ?>
				<?php esc_html_e( 'Use Algolia with the native WordPress search template', 'wp-search-with-algolia-pro' ); ?>
		<?php endif; ?>
	</label>
	<div class="radio-info">
		<?php
		echo ( $network_wide_disabled ) ?
			esc_html__(
				'Due to limitations with cross network queries, backend only search is not available for network wide indexing',
				'wp-search-with-algolia-pro'
			) :
			wp_kses(
				__(
					'Search results will be powered by Algolia and will use the standard WordPress search template for displaying the results.<br/>This option has the advantage to play nicely with any theme but does not support filtering and displaying instant search results.',
					'wp-search-with-algolia-pro'
				),
				[
					'br' => [],
					'b'  => [],
				]
			);
		?>
	</div>

	<label>
		<input type="radio" value="instantsearch"
			name="algolia_network_override_native_search" <?php checked( $value, 'instantsearch' ); ?>>
		<?php esc_html_e( 'Use Algolia with Instantsearch.js', 'wp-search-with-algolia-pro' ); ?>
	</label>
	<div class="radio-info">
		<?php
		echo wp_kses(
			__(
				'This will replace the WordPress search page with an instant search experience powered by Algolia.<br/>By default you will be able to filter by post type, categories, tags and authors.',
				'wp-search-with-algolia-pro'
			),
			[
				'br' => [],
			]
		);
		?>
	</div>
</div>
