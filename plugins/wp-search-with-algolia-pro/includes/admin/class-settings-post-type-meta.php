<?php
/**
 * Settings_Post_Type_Meta Class file
 * @package WebDevStudios\WPSWAPro
 * @since   1.3.0
 */

namespace WebDevStudios\WPSWAPro\Admin;

/**
 * Class Settings_Post_Type_Meta
 * @since 1.3.0
 */
class Settings_Post_Type_Meta {

	/**
	 * Settings slug.
	 * @var string
	 */
	private string $slug = 'wpswa_pro_post_type_meta';

	/**
	 * Option group slug.
	 * @var string
	 */
	private string $option_group = 'wpswa_pro_post_type_meta';

	/**
	 * Meta section slug.
	 * @var string
	 */
	private string $post_type_meta_section = 'meta';

	/**
	 * Minimum capability needed to interact with our options.
	 * @var string
	 */
	private string $capability = 'manage_options';

	/**
	 * Constructor
	 * @since 1.3.0
	 */
	public function __construct() {}

	/**
	 * Execute our hooks for Post Meta based settings.
	 * @since 1.3.0
	 */
	public function do_hooks() {
		add_action( 'admin_menu', [ $this, 'add_page' ], 11 );
		add_action( 'admin_init', [ $this, 'add_settings' ] );
		add_action( 'admin_notices', [ $this, 'meta_notices' ] );

		add_filter( 'wpswa_pro_option_keys', [ $this, 'registered_options' ] );
	}

	/**
	 * Add our classes' available options to the array to delete upon deactivation.
	 *
	 * @since 1.3.0
	 *
	 * @param array $options Array of options to register for this setting type.
	 * @return array
	 */
	public function registered_options( array $options = [] ): array {
		$post_types = get_post_types( [ 'exclude_from_search' => false ], 'objects' );

		foreach ( $post_types as $post_type ) {
			$options[] = "wpswa_pro_{$post_type->name}_meta";
		}

		return $options;
	}

	/**
	 * Add our submenu.
	 *
	 * @since 1.3.0
	 */
	public function add_page() {
		add_submenu_page(
			'algolia',
			esc_html__( 'Post Type Meta', 'wp-search-with-algolia-pro' ),
			esc_html__( 'Meta', 'wp-search-with-algolia-pro' ),
			$this->capability,
			$this->slug,
			[ $this, 'display_page' ]
		);
	}

	/**
	 * Display a success admin notice upon save.
	 *
	 * @since 1.3.3
	 */
	public function meta_notices() {
		if ( empty( $_GET ) ) {
			return;
		}

		if ( isset( $_GET['page'] ) && 'wpswa_pro_post_type_meta' === $_GET['page'] && isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Meta fields saved successfully.', 'wp-search-with-algolia-pro' ) . '</p></div>';
		}
	}

	/**
	 * Execute our settings sections.
	 *
	 * @since 1.3.0
	 */
	public function add_settings() {
		$this->add_post_type_meta_section();
	}

	/**
	 * Register our Post Meta section and related settings fields.
	 *
	 * @since 1.3.0
	 */
	private function add_post_type_meta_section() {
		add_settings_section(
			$this->post_type_meta_section,
			esc_html__( 'Meta fields', 'wp-search-with-algolia-pro' ),
			[ $this, 'post_type_meta_callback' ],
			$this->slug
		);

		$post_types = get_post_types( [ 'exclude_from_search' => false ], 'objects' );

		foreach( $post_types as $post_type ) {
			add_settings_field(
				"wpswa_pro_{$post_type->name}_meta",
				sprintf(
					esc_html__( '%s meta fields', 'wp-search-with-algolia-pro' ),
					ucfirst( $post_type->label )
				),
				[ $this, 'text' ],
				$this->slug,
				$this->post_type_meta_section,
				[
					'label_for' => "wpswa_pro_{$post_type->name}_meta",
				]
			);
			register_setting(
				$this->option_group,
				"wpswa_pro_{$post_type->name}_meta",
				[
					'sanitize_callback' => 'sanitize_text_field',
				]
			);
		}
	}

	/**
	 * Load an external PHP file to render our final settings page result.
	 *
	 * @since 1.3.0
	 */
	public function display_page() {
		require_once WPSWA_PRO_PATH . 'includes/admin/partials/page-post-type-meta.php';
	}

	/**
	 * Callback to render our checkbox.
	 *
	 * @param array $args Array of extra arguments for checkbox callback.
	 *
	 * @since 1.3.0
	 */
	public function checkbox( array $args ) {
		$value       = get_option( $args['label_for'], '' );
		$disabled    = esc_attr( $args['disabled'] );
		$label       = esc_attr( $args['label_for'] );
		$helptext    = ! empty( $args['helptext'] ) ? wpautop( esc_html( $args['helptext'] ) ) : '';
		$extra_label = ! empty( $args['extra_label'] ) ? esc_html( $args['extra_label'] ) : '';
		?>
		<input type="checkbox" id="<?php echo $label; ?>" name="<?php echo $label; ?>" value="yes" <?php checked( $value, 'yes' ); ?> <?php disabled( $disabled, true ); ?>/>
		<label for="<?php echo $label; ?>"><?php echo $extra_label; ?></label>
		<?php echo $helptext; ?>
		<?php
	}

	/**
	 * Render a text field.
	 *
	 * @since 1.3.0
	 *
	 * @param array $args Extra arguments.
	 */
	public function text( array $args ) { ?>
		<label for="<?php echo esc_attr( $args['label_for'] ); ?>">
		<input
			class="regular-text"
			type="text"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo esc_attr( get_option( $args['label_for'], '' ) ); ?>"
			/>
		</label>
		<?php
	}

	/**
	 * Meta callback to render content between the heading and the options themselves.
	 *
	 * @since 1.3.0
	 */
	public function post_type_meta_callback() {
		?>
		<p><?php esc_html_e( 'Use the provided text inputs to specify a comma separated list of meta keys to index with each post', 'wp-search-with-algolia-pro' ); ?></p>
		<?php
	}
}
