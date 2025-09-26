<?php

if (!defined('ABSPATH')) {
	return;
}

$composer_autoload = ABSPATH . '/vendor/autoload.php';

if (file_exists($composer_autoload)) {
	require_once $composer_autoload;
}

use Timber\Timber;
use Framework\Site as FrameworkSite;

define('IS_LOCAL', defined('FW_IS_LOCAL') && FW_IS_LOCAL);
define('HOME_ROOT', network_site_url());
define('MULTISITE_HOME_ROOT', get_home_url());
define(
	'STATIC_DIRNAME',
	IS_LOCAL ? '/frontend/dist/' : '/frontend/static-html/'
);
define('WWW_ROOT', trailingslashit(get_site_url()));

define('THEME_ROOT', trailingslashit(get_stylesheet_directory()));
define('THEME_ROOT_STATIC', THEME_ROOT . STATIC_DIRNAME);
define('THEME_ROOT_URI', get_stylesheet_directory_uri());
define('THEME_ROOT_URI_STATIC', THEME_ROOT_URI . STATIC_DIRNAME);

define('PARENT_ROOT', trailingslashit(get_theme_root() . '/' . 'primary'));
define('PARENT_ROOT_URI', trailingslashit(get_template_directory_uri()));
define('PARENT_ROOT_STATIC', PARENT_ROOT . STATIC_DIRNAME);
define('PARENT_ROOT_URI_STATIC', PARENT_ROOT_URI . STATIC_DIRNAME);

define('CURRENT_SITE', get_current_blog_id());
// define(
	// 'ICONS_MODIFIED_TIME',
	// filemtime(PARENT_ROOT_STATIC . 'images/icons.svg') ?: '1.0.0'
// );

define('FW_IMAGE_SIZES', include_once 'inc/image-sizes.php' ?: []);
define('FW_CONFIG', include_once 'inc/config.php' ?: []);
define(
	'FW_DEBUG',
	defined('FW_SHOW_DEBUG_MESSAGES') && FW_SHOW_DEBUG_MESSAGES && WP_DEBUG && function_exists('dump')
);
/*
Timber::init();

Timber::$locations = [
	get_stylesheet_directory(), // child theme
];
Timber::$dirname = ['templates'];*/

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/routes.php';
/*require_once __DIR__ . '/inc/ajax.php';
require_once __DIR__ . '/inc/twig.php';
require_once __DIR__ . '/inc/shortcodes.php';
require_once __DIR__ . '/inc/bricks.php';

new FrameworkSite();
 */
add_filter('body_class', function ($classes) {
	array_push($classes, 'preload', 'fs-grid');

/*	if (is_singular(['news'])) {
		$classes[] = 'theme-turquoise';
	}

	if (is_singular(['tribe_events'])) {
		$classes[] = 'theme-light-gray';
	}
*/
	return $classes;
});

add_filter('language_attributes', function ($output, $doctype) {
	if ($doctype !== 'html') {
		return $output;
	}

	$output .= ' class="no-js"';

	return $output;
}, 10, 2);


 

 /* SGU MODIFICATIONS */
 
 // Add background image to login page
 function sgu_custom_bg_image() {
 $bgImageUrl = '/wp-content/uploads/surf-bkgnd-1.jpg';
 ?>
 <style type="text/css">
   body{
	 background-image:url('<?php echo $bgImageUrl; ?>') !important;
	 background-size:cover !important;
	 background-position:center center !important;
   }
 </style>
 <?php }
 add_action( 'login_enqueue_scripts', 'sgu_custom_bg_image' );
 
 
 //Change login page logo
 function swap_login_logo() { 
 ?> 
 <style type="text/css"> 
 	body.login div#login h1 a {
     background-image: url(/wp-content/uploads/sgu-school-crest.png);
 	margin-top: 100px; 
 }   
 

 </style>
  <?php } add_action( 'login_enqueue_scripts', 'swap_login_logo' );


 // Add menu shortcode functionality
 function print_menu_shortcode($atts=[], $content = null) {
	 $shortcode_atts = shortcode_atts([ 'name' => '', 'class' => '' ], $atts);
	 $name   = $shortcode_atts['name'];
	 $class  = $shortcode_atts['class'];
	 return wp_nav_menu( array( 'menu' => $name, 'menu_class' => $class, 'echo' => false ) );
 }
 
 function word_count($string, $limit) {
  
 $words = explode(' ', $string);
  
 return implode(' ', array_slice($words, 0, $limit));
  
 }

 


/**
  * Fixes canonical URL for custom paginated archives using a '_page' query parameter.
  * This ensures that pages like /?_page=2 have a self-referencing canonical URL.
  * Includes high priority and debugging output for administrators.
  */
 function custom_fix_pagination_canonical_url( $canonical_url, $post ) {
 
     // --- Start Debugging Output (only visible to admins in page source) ---
     if ( current_user_can('manage_options') ) {
         echo "\n";
         echo "\n";
     }
     // --- End Debugging Output ---
 
     // Check if we are on the correct post type archive.
     // Replace 'faculty' with your actual custom post type slug if different.
     if ( is_post_type_archive('faculty') && isset( $_GET['_page'] ) ) {
         
         // Sanitize the page number to ensure it's a positive integer.
         $page_number = absint( $_GET['_page'] );
         
         // Only modify the canonical if we are on a paginated page (page 2 or higher).
         if ( $page_number > 1 ) {
             // Add the '_page' query parameter and its value to the base canonical URL.
             $canonical_url = add_query_arg( '_page', $page_number, $canonical_url );
         }
     }
 
     // --- Start Debugging Output (only visible to admins in page source) ---
     if ( current_user_can('manage_options') ) {
         echo "\n";
     }
     // --- End Debugging Output ---
     
     return $canonical_url;
 }
 add_filter( 'get_canonical_url', 'custom_fix_pagination_canonical_url', 99, 2 );
 


