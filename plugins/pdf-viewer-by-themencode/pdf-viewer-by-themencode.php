<?php
/**
 * Plugin Name: TNC PDF viewer
 * Plugin URI: https://themencode.com/tncflipbook-preview/
 * Description: The best PDF Reader & FlipBook Plugin for WordPress since 2014, Powers up your WordPress website with a smart and modern PDF Reader & Highly customizable FlipBook.
 * Version: 4.1.1
 * Author: ThemeNcode LLC
 * Author URI: https://themencode.com
 * Text Domain: pdf-viewer-by-themencode
 * Domain Path: /languages
 *
 * @package  pdf-viewer-by-themencode
 */

defined( 'ABSPATH' ) || exit;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define constants.
define( 'PVFW_LITE_PLUGIN_NAME', 'TNC PDF viewer' );
define( 'PVFW_LITE_PLUGIN_DIR', 'pdf-viewer-by-themencode' );
define( 'PVFW_LITE_PLUGIN_VERSION', '4.1.1' );
define( 'PVFW_LITE_WEB_DIR', 'pdf-viewer-by-themencode/web' );
define( 'PVFW_LITE_BUILD_DIR', 'pdf-viewer-by-themencode/build' );
define( 'PVFW_LITE_RESOURCES_DIR', 'pdf-viewer-by-themencode/tnc-resources' );

add_action( 'init', 'pvfw_lite_load_textdomain' );

function pvfw_lite_load_textdomain() {
	load_plugin_textdomain( 'pdf-viewer-by-themencode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function pvfw_lite_include_csf_files() {
	require_once plugin_dir_path( __FILE__ ) . '/includes/pvfw-csf-options.php';
	require_once plugin_dir_path( __FILE__ ) . '/includes/pvfw-csf-custom-field.php';
	require_once plugin_dir_path( __FILE__ ) . '/includes/pvfw-csf-sc.php';
}
add_action( 'init', 'pvfw_lite_include_csf_files', 1 );

// Include files.
require_once plugin_dir_path( __FILE__ ) . 'admin/tnc-pdf-viewer-options.php';
require_once plugin_dir_path( __FILE__ ) . '/admin/TncPvfwCustomMetabox.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/helper-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/cpt.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/csf/pvfwof-framework.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/pvfw-new-shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/scripts.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/generate-shortcode.php';


register_activation_hook( __FILE__, 'pvfw_lite_activation' );
register_deactivation_hook( __FILE__, 'pvfw_lite_deactivation' );

/**
 * Activation function
 */
function pvfw_lite_activation() {
}

/**
 * Deactivation function
 */
function pvfw_lite_deactivation() {
	// Do Nothing Right Now.
}


function tnc_pvfw_pdf_add_links_to_plugin_page ( $actions ) {
   $tnc_pdf_custom_links_array = array(
	  '<a href="edit.php?post_type=pdfviewer&page=pdf-viewer-options">'. esc_html__( 'Settings', 'pdf-viewer-by-themencode' ) . '</a>',
      '<a class="tnc_pdf_get_premium" href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">'. esc_html__( 'Get Premium', 'pdf-viewer-by-themencode' ) .'</a>',
   );
   $actions = array_merge( $actions, $tnc_pdf_custom_links_array );
   return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'tnc_pvfw_pdf_add_links_to_plugin_page' );


function tnc_pvfw_pdf_visit_plugin_site_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
	if ( strpos( $plugin_file_name, basename(__FILE__) ) ) {

	    $tnc_visit_plugin_site = esc_html__('Visit plugin site','pdf-viewer-by-themencode');
		$tnc_visit_plugin_site_url  = 'https://themencode.com/pdf-viewer-for-wordpress/';
		$tnc_documentation   = esc_html__('Documentation','pdf-viewer-by-themencode');
		$tnc_doc_url = 'https://docs.themencode.com/docs/pdf-viewer-by-themencode/';
		$tnc_videos = esc_html__('Videos','pdf-viewer-by-themencode');
		$tnc_video_url = 'https://www.youtube.com/watch?v=w1hDnrfxEwQ';
		$tnc_rate_us =  esc_html__('Rate Us', 'pdf-viewer-by-themncode');

		$tnc_rate_url = 'https://wordpress.org/plugins/pdf-viewer-by-themencode/#reviews';
 		$links_array[] = '<a class="tnc_pdf_visit_plugin_site" target="_blank" href="'. esc_url($tnc_visit_plugin_site_url).'">'.$tnc_visit_plugin_site.'</a>';
		$links_array[] = '<a class="tnc_pdf_visit_plugin_site" target="_blank" href="'. esc_url($tnc_doc_url) .'">'. $tnc_documentation .'</a>';
		$links_array[] = '<a class="tnc_pdf_visit_plugin_site" target="_blank" href="' .esc_url($tnc_video_url) . '"> ' .$tnc_videos.'</a>';
		$links_array[] = '<a class="tnc_pdf_visit_plugin_site" target="_blank" href="'. esc_url($tnc_rate_url) . '">'. $tnc_rate_us .'</a>';
	} 
	return $links_array;
}
add_filter( 'plugin_row_meta', 'tnc_pvfw_pdf_visit_plugin_site_links', 10, 4 );


if ( ! function_exists( 'tnc_pdf_iframe_responsive_fix' ) ){
	// Iframe Responsive Fix.
	function tnc_pdf_iframe_responsive_fix() {
		$get_pvfw_global_settings = get_option( 'pvfw_csf_options' );
		$get_iframe_fix    	= $get_pvfw_global_settings['general-iframe-responsive-fix'];
		
		if($get_iframe_fix == '1'){
			echo "<style type='text/css'>
				iframe{
					border: 0px;
				}
			</style>";
		} else {
			echo "<style type='text/css'>
				iframe{
					max-width: 100%;
					border: 0px;
				}
			</style>";
		}
	}

	add_action( 'wp_head', 'tnc_pdf_iframe_responsive_fix' );
}

