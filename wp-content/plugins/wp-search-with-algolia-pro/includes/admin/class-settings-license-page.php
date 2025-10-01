<?php
/**
 * License_Page class.
 *
 * @package WebDevStudios\WPSWAPro
 */

namespace WebDevStudios\WPSWAPro\Admin;

use WebDevStudios\WPSWAPro\Utils;

/**
 * Add and render our license page.
 */
class Settings_License_Page {

	/**
	 * @var string Plugin's name.
	 */
	private string $plugin_name;

	/**
	 * @var string Plugin's slug.
	 */
	private string $plugin_slug;

	/**
	 * @var string License page slug.
	 */
	private string $page_slug;

	/**
	 * @var string License activation status field name.
	 */
	private string $status_slug;

	/**
	 * @var string License setting.
	 */
	private string $license_setting;

	/**
	 * @var string License setting slug.
	 */
	private string $license_key_slug;

	/**
	 * @var string Activate license field name.
	 */
	private string $activate_key;

	/**
	 * @var string Deactivate license field name.
	 */
	private string $deactivate_key;

	/**
	 * @var string Nonce string.
	 */
	private string $nonce;

	/**
	 * @var false|mixed|null Plugin license.
	 */
	private string $license;

	/**
	 * @var false|mixed|null Activation status.
	 */
	private string $status;

	/**
	 * @var string Capability level
	 */
	private string $capability = 'manage_options';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name Plugin name
	 * @param string $plugin_slug Plugin slug
	 */
	public function __construct( $plugin_name, $plugin_slug ) {
		$this->plugin_name      = $plugin_name;
		$this->plugin_slug      = $plugin_slug;

		$this->page_slug        = $this->plugin_slug . '-license_page';
		$this->status_slug      = $this->plugin_slug . '_license_status';
		$this->license_setting  = $this->plugin_slug . '_license';
		$this->license_key_slug = $this->plugin_slug . '_license_key';
		$this->activate_key     = $this->plugin_slug . '_activate';
		$this->deactivate_key   = $this->plugin_slug . '_deactivate';
		$this->nonce            = $this->plugin_slug . '_license_nonce';
		$this->license          = $this->ms_get_option( $this->license_key_slug );
		$this->status           = $this->ms_get_option( $this->status_slug );
	}

	/**
	 * Run our hooks.
	 *
	 * @since 1.0.0
	 */
	public function do_hooks() {
		$admin_hook = ( Utils::wpswa_pro_is_network_activated() || is_network_admin() ) ? 'network_admin_menu' : 'admin_menu';

		if ( is_network_admin() ) {
			$this->capability = 'manage_network_options';
		}

		add_action( $admin_hook, [ $this, 'add_license_page' ], 20 );
		add_action( 'admin_init', [ $this, 'register_options' ] );
		add_action( 'admin_init', [ $this, 'activate_license' ] );
		add_action( 'admin_init', [ $this, 'deactivate_license' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
		add_action( 'network_admin_notices', [ $this, 'admin_notices' ] );

		//Network admin saving.
		add_action( "network_admin_edit_$this->page_slug", [ $this, 'network_license_page' ] );
	}

	/**
	 * Register our menu page.
	 *
	 * @since 1.0.0
	 */
	public function add_license_page() {
		$parent = 'algolia';
		if ( is_multisite() ) {
			$parent = 'wpswa_pro_network_individual';
			if ( Utils::wpswa_pro_is_network_activated() ) {
				$parent = 'wpswa_pro_network';
			}

			if ( is_network_admin() ) {
				if ( ! Utils::wpswa_pro_is_network_activated() ) {
					add_menu_page(
						'WP Search with Algolia',
						esc_html__( 'Algolia Search', 'wp-search-with-algolia-pro' ),
						$this->capability,
						$parent,
						[ $this, 'network_individual_empty' ],
						'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MDAgNTAwLjM0Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzAwM2RmZjt9PC9zdHlsZT48L2RlZnM+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMjUwLDBDMTEzLjM4LDAsMiwxMTAuMTYsLjAzLDI0Ni4zMmMtMiwxMzguMjksMTEwLjE5LDI1Mi44NywyNDguNDksMjUzLjY3LDQyLjcxLC4yNSw4My44NS0xMC4yLDEyMC4zOC0zMC4wNSwzLjU2LTEuOTMsNC4xMS02LjgzLDEuMDgtOS41MmwtMjMuMzktMjAuNzRjLTQuNzUtNC4yMi0xMS41Mi01LjQxLTE3LjM3LTIuOTItMjUuNSwxMC44NS01My4yMSwxNi4zOS04MS43NiwxNi4wNC0xMTEuNzUtMS4zNy0yMDIuMDQtOTQuMzUtMjAwLjI2LTIwNi4xLDEuNzYtMTEwLjMzLDkyLjA2LTE5OS41NSwyMDIuOC0xOTkuNTVoMjAyLjgzVjQwNy42OGwtMTE1LjA4LTEwMi4yNWMtMy43Mi0zLjMxLTkuNDMtMi42Ni0xMi40MywxLjMxLTE4LjQ3LDI0LjQ2LTQ4LjU2LDM5LjY3LTgxLjk4LDM3LjM2LTQ2LjM2LTMuMi04My45Mi00MC41Mi04Ny40LTg2Ljg2LTQuMTUtNTUuMjgsMzkuNjUtMTAxLjU4LDk0LjA3LTEwMS41OCw0OS4yMSwwLDg5Ljc0LDM3Ljg4LDkzLjk3LDg2LjAxLC4zOCw0LjI4LDIuMzEsOC4yOCw1LjUzLDExLjEzbDI5Ljk3LDI2LjU3YzMuNCwzLjAxLDguOCwxLjE3LDkuNjMtMy4zLDIuMTYtMTEuNTUsMi45Mi0yMy42LDIuMDctMzUuOTUtNC44My03MC4zOS02MS44NC0xMjcuMDEtMTMyLjI2LTEzMS4zNS04MC43My00Ljk4LTE0OC4yMyw1OC4xOC0xNTAuMzcsMTM3LjM1LTIuMDksNzcuMTUsNjEuMTIsMTQzLjY2LDEzOC4yOCwxNDUuMzYsMzIuMjEsLjcxLDYyLjA3LTkuNDIsODYuMi0yNi45N2wxNTAuMzYsMTMzLjI5YzYuNDUsNS43MSwxNi42MiwxLjE0LDE2LjYyLTcuNDhWOS40OUM1MDAsNC4yNSw0OTUuNzUsMCw0OTAuNTEsMEgyNTBaIi8+PC9zdmc+'
					);
				}

				add_submenu_page(
					$parent,
					esc_html__( 'Algolia Pro License', 'wp-search-with-algolia-pro' ),
					esc_html__( 'Algolia Pro License', 'wp-search-with-algolia-pro' ),
					$this->capability,
					$this->page_slug,
					[ $this, 'license_page' ]
				);
			}
		} else {
			add_submenu_page(
				$parent,
				esc_html__( 'Algolia Pro License', 'wp-search-with-algolia-pro' ),
				esc_html__( 'Algolia Pro License' ),
				$this->capability,
				$this->page_slug,
				[ $this, 'license_page' ]
			);
		}
	}

	/**
	 * Provide something basic when nudging to the license page.
	 *
	 * @since 1.5.2
	 */
	public function network_individual_empty() {
		echo '<div class="wrap">';
		echo wpautop( esc_html__( 'Please visit the license page', 'wp-search-with-algolia-pro' ) );
		echo '</div>';
	}

	/**
	 * Register our option.
	 *
	 * @since 1.0.0
	 */
	public function register_options() : void {
		add_settings_section(
			'wpswa_pro_license',
			'',
			[ $this, 'wpswa_pro_license_settings_section' ],
			$this->page_slug
		);

		add_settings_field(
			$this->license_key_slug,
			'<label for="' . esc_attr( $this->license_key_slug ) . '">' . esc_html__( 'License Key', 'wp-search-with-algolia-pro' ) . '</label>',
			[ $this, 'license_key_settings_field' ],
			$this->page_slug,
			'wpswa_pro_license',
		);

		register_setting(
			$this->license_setting,
			$this->license_key_slug,
			[ $this, 'sanitize_license' ]
		);
	}

	/**
	 * Adds content to the settings section.
	 *
	 * @since 1.5.2
	 *
	 * @return void
	 */
	public function wpswa_pro_license_settings_section() {
		printf(
			wpautop( esc_html__( 'Please activate your WP Search with Algolia Pro license below. If you do not have an active license, please visit %s to purchase one.', 'wp-search-with-algolia-pro' ) ),
			'<a href="https://pluginize.com/plugins/wp-search-with-algolia-pro/" target="_blank">https://pluginize.com/plugins/wp-search-with-algolia-pro/</a>'
		);
		echo wpautop( esc_html__( ' An active license is required for ongoing plugin updates and plugin support.', 'wp-search-with-algolia-pro' ) );
	}

	/**
	 * Outputs the license key settings field.
	 *
	 * @since 1.5.2
	 *
	 * @return void
	 */
	public function license_key_settings_field() {
		?>
		<p class="description"><?php esc_html_e( 'Enter your license key.', 'wp-search-with-algolia-pro' ); ?></p>
		<?php
		printf(
			'<input type="text" class="regular-text" id="%s" name="%s" value="%s" />',
			esc_attr( $this->license_key_slug ),
			esc_attr( $this->license_key_slug ),
			esc_attr( $this->license )
		);
		$button = array(
			'name'  => $this->deactivate_key,
			'label' => esc_html__( 'Deactivate License', 'wp-search-with-algolia-pro' ),
		);
		if ( 'valid' !== $this->status ) {
			$button = array(
				'name'  => $this->activate_key,
				'label' => esc_html__( 'Activate License', 'wp-search-with-algolia-pro' ),
			);
		}


		wp_nonce_field( $this->nonce, $this->nonce );
		?>
		<input type="submit" class="button-secondary" name="<?php echo esc_attr( $button['name'] ); ?>" value="<?php echo esc_attr( $button['label'] ); ?>" />
		<?php
		if ( 'valid' === $this->status ) :
			echo wpautop( sprintf( esc_html__( 'Status: %s', 'wp-search-with-algolia-pro' ), $this->status ) );
		endif;
	}

	/**
	 * Render our licenase page output.
	 * @since 0.3.0
	 */
	public function license_page(): void {
		?>
		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php
			$action = ( Utils::wpswa_pro_is_network_activated() || is_network_admin() ) ? 'edit.php?action=' . $this->page_slug : 'options.php';
			?>
			<form method="post" action="<?php echo esc_attr( $action ); ?>">

				<?php
				do_settings_sections( $this->page_slug );
				settings_fields( $this->license_setting );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitize the license key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $new Newly saved license.
	 * @return string
	 */
	public function sanitize_license( string $new ): string {
		$old = $this->license;
		if ( $old && $old != $new ) {
			$this->ms_delete_option( $this->status_slug );
		}
		return sanitize_text_field( $new );
	}

	/**
	 * Activate a license.
	 *
	 * @since 1.0.0
	 */
	public function activate_license() : void {

		if ( empty( $_POST ) || ! isset( $_POST[ $this->activate_key ] ) ) {
			return;
		}

		if ( empty( $_POST[ $this->license_key_slug ] ) ) {
			return;
		}

		// Run a quick security check.
		if ( ! check_admin_referer( $this->nonce, $this->nonce ) ) {
			return;
		}

		$base_url = $this->get_license_page_admin_url();
		$license_key = '';
		if ( ! empty( $_POST[ $this->license_key_slug ] ) ) {
			$license_key = sanitize_text_field( $_POST[ $this->license_key_slug ] );
		}

		$response = $this->activate_deactivate( 'activate_license', $license_key );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = esc_html__( 'An error occurred, please try again.', 'wp-search-with-algolia-pro' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch( $license_data->error ) {

					case 'expired' :
						$message = sprintf(
							esc_html__( 'Your license key expired on %s.', 'wp-search-with-algolia-pro' ),
							date_i18n(
								get_option( 'date_format' ), // okay to leave at per-site, even with MS.
								strtotime(
									$license_data->expires,
									current_time( 'timestamp' ) // phpcs:ignore
								)
							)
						);
						break;

					case 'revoked' :
						$message = esc_html__( 'Your license key has been disabled.', 'wp-search-with-algolia-pro' );
						break;

					case 'missing' :
						$message = esc_html__( 'Invalid license.', 'wp-search-with-algolia-pro' );
						break;

					case 'invalid' :
					case 'site_inactive' :
						$message = esc_html__( 'Your license is not active for this URL.', 'wp-search-with-algolia-pro' );
						break;

					case 'item_name_mismatch' :
						$message = sprintf(
							esc_html__( 'This appears to be an invalid license key for %s.', 'wp-search-with-algolia-pro' ),
							$this->plugin_name
						);
						break;

					case 'no_activations_left':
						$message = esc_html__( 'Your license key has reached its activation limit.', 'wp-search-with-algolia-pro' );
						break;

					default :
						$message = esc_html__( 'An error occurred, please try again.', 'wp-search-with-algolia-pro' );
						break;
				}
			}
		}

		if ( ! empty( $message ) ) {
			$redirect = add_query_arg(
				[
					'sl_activation' => 'false',
					'message' => urlencode( $message ), // phpcs:ignore
				],
				$base_url
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		$this->ms_update_option( $this->status_slug, $license_data->license );
		$this->ms_update_option( $this->license_key_slug, $license_key );

		$url = add_query_arg(
			[
				'sl_activation' => 'true',
				'message' => 'activated',
			],
			$base_url
		);
		wp_safe_redirect( $url );
		exit();
	}

	/**
	 * Deactivate a license.
	 *
	 * @since 1.0.0
	 */
	public function deactivate_license() {

		if ( empty( $_POST ) || ! isset( $_POST[ $this->deactivate_key ] ) ) {
			return;
		}

		// Run a quick security check.
		if ( ! check_admin_referer( $this->nonce, $this->nonce ) ) {
			return;
		}

		$base_url    = $this->get_license_page_admin_url();
		$license_key = '';
		if ( ! empty( $_POST[ $this->license_key_slug ] ) ) {
			$license_key = sanitize_text_field( $_POST[ $this->license_key_slug ] );
		}

		$response = $this->activate_deactivate( 'deactivate_license', $license_key );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = esc_html__( 'An error occurred, please try again.', 'wp-search-with-algolia-pro' );
			}

			$redirect = add_query_arg(
				[
					'sl_activation' => 'false',
					'message'       => urlencode( $message ), // phpcs:ignore
				],
				$base_url
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// Decode the license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( in_array( $license_data->license, [ 'deactivated', 'failed' ], true ) ) {
			$this->ms_delete_option( $this->status_slug );
		}

		$url = add_query_arg(
			[
				'sl_activation' => 'true',
				'message'       => 'deactivated',
			],
			$base_url
		);
		wp_safe_redirect( $url );
		exit();
	}

	/**
	 * Send POST request to Pluginize.com to handle a license status change.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action  Activating or deactivating.
	 * @param string $license The license to activate or deactivate.
	 *
	 * @return array|false|\WP_Error
	 */
	private function activate_deactivate( string $action = 'activate_license', string $license = '' ) {
		if ( empty( $license ) ) {
			$license = $this->ms_get_option( $this->license_key_slug );
		}

		if ( empty( $license ) ) {
			return false;
		}

		// Data to send in our API request.
		$api_params = [
			'edd_action' => $action,
			'license'    => $license,
			'item_name'  => urlencode( $this->plugin_name ), // phpcs:ignore
			'url'        => home_url()
		];
		return wp_remote_post(
			WPSWA_PRO_STORE_URL,
			[
				'timeout' => 15,
				'sslverify' => false,
				'body' => $api_params,
			]
		);
	}

	/**
	 * Custom callback for network admin option saving because Settings API
	 * is not fully integrated into Multisite.
	 * @since 1.5.2
	 */
	public function network_license_page() {
		check_admin_referer( $this->license_setting . '-options' );

		if ( ! empty( $_POST[ $this->license_key_slug ] ) ) {
			$clean_key = sanitize_text_field( $_POST[ $this->license_key_slug ] );
			// Intentionally using direct *_option function since we know we want to
			// save at network level
			update_site_option( $this->license_key_slug, $clean_key );
		} elseif ( 'update' === $_POST['action'] && empty( $_POST[ $this->license_key_slug ] ) ) {
			// Trigger a deactivation, delete status and license key
			$this->activate_deactivate( 'deactivate_license' );
			delete_site_option( $this->status_slug );
			delete_site_option( $this->license_key_slug );
		}

		wp_safe_redirect( $this->get_license_page_admin_url() );
		exit();
	}

	/**
	 * Handle admin notices.
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {
		if ( isset( $_GET['sl_activation'] ) ) {
			if ( 'false' === $_GET['sl_activation'] ) {
				if ( ! empty( $_GET['message'] ) ) {
					$message = urldecode( $_GET['message'] );
					wp_admin_notice(
						esc_html( $message ),
						[
							'id'          => 'message',
							'type'        => 'error',
							'dismissible' => true,
						]
					);
				}
			} else if ( 'true' === $_GET['sl_activation'] ) {
				if ( ! empty( $_GET['message'] ) ) {
					if ( 'activated' === sanitize_text_field( $_GET['message'] ) ) {
						wp_admin_notice(
							esc_html__( 'Successfully activated the license.', 'wp-search-with-algolia-pro' ),
							array(
								'id'                 => 'message',
								'additional_classes' => array( 'updated' ),
								'dismissible'        => true,
							)
						);
					} else if ( 'deactivated' === sanitize_text_field( $_GET['message'] ) ) {
						wp_admin_notice(
							esc_html__( 'Successfully deactivated the license.', 'wp-search-with-algolia-pro' ),
							array(
								'id'                 => 'message',
								'additional_classes' => array( 'updated' ),
								'dismissible'        => true,
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Returns the URL for our settings page.
	 *
	 * May be network admin or single site.
	 *
	 * @since 1.3.0
	 * @return string|null
	 */
	private function get_license_page_admin_url(): string {
		$path = 'admin.php?page=' . $this->page_slug;
		return is_network_admin() ?
			network_admin_url( $path ) :
			admin_url( $path );
	}

	/**
	 * Multisite-aware get_option.
	 *
	 * @param string $option_name Option to fetch.
	 * @param mixed  $default     Default value if one does not exist.
	 *
	 * @return mixed
	 * @since 1.5.2
	 */
	protected function ms_get_option( string $option_name, $default = '' ) {
		return ( Utils::wpswa_pro_is_network_activated() || is_network_admin() ) ?
			get_site_option( $option_name, $default ) :
			get_option( $option_name, $default );
	}

	/**
	 * Multisite-aware delete_option.
	 *
	 * @param string $option_name Option to delete.
	 *
	 * @return bool
	 * @since 1.5.2
	 */
	protected function ms_delete_option( string $option_name ): bool {
		return ( Utils::wpswa_pro_is_network_activated() || is_network_admin() ) ?
			delete_site_option( $option_name ) :
			delete_option( $option_name );
	}

	/**
	 * Multisite-aware update option.
	 *
	 * @param string $option_name  Option to update.
	 * @param mixed  $option_value Option value being updated.
	 *
	 * @return bool
	 * @since 1.5.2
	 */
	protected function ms_update_option( string $option_name, $option_value ): bool {
		return ( Utils::wpswa_pro_is_network_activated() || is_network_admin() ) ?
			update_site_option( $option_name, $option_value ) :
			update_option( $option_name, $option_value );
	}
}
