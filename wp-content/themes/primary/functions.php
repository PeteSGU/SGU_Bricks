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
define(
	'ICONS_MODIFIED_TIME',
	filemtime(PARENT_ROOT_STATIC . 'images/icons.svg') ?: '1.0.0'
);

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
 
 
 /** Add VWO editor if page string condition is true */
  add_action( 'wp_enqueue_scripts', function () {
  
     if ( is_page( 'mdlp2' ) ) {

  
          wp_enqueue_script(
              'vwo_script',
              get_stylesheet_directory_uri() . '/js/vwo-editor.js',
              [],
              null,
              false // false = load in <head>
          );
      }
  
  });
 
 
 
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
  * Fixes canonical URL for custom paginated archives for Rank Math SEO.
  * This ensures that pages like /?_page=2 have a self-referencing canonical URL.
  */
 function sgu_fix_rank_math_pagination_canonical( $canonical_url ) {
 
     // Check if we are on the 'faculty' post type archive and the '_page' parameter exists.
     if ( is_post_type_archive('faculty') && isset( $_GET['_page'] ) ) {
         
         // Sanitize the page number to ensure it's a positive integer.
         $page_number = absint( $_GET['_page'] );
         
         // Only modify the canonical if we are on page 2 or higher.
         if ( $page_number > 1 ) {
             
             // Rebuild the canonical URL with the correct page number.
             // First, get the URL without any existing query parameters from a previous filter.
             $base_canonical_url = strtok( $canonical_url, '?' );
 
             // Add our '_page' query parameter and its value.
             $canonical_url = add_query_arg( '_page', $page_number, $base_canonical_url );
         }
     }
     
     return $canonical_url;
 }
 // FOR RANK MATH: Use Rank Math's specific filter with high priority.
 add_filter( 'rank_math/frontend/canonical', 'sgu_fix_rank_math_pagination_canonical', 99 );
 
 
 //Redirect users to the info sessions page if the 404 is an event CPT 
 add_action( 'template_redirect', 'event404_redirect' );
   function event404_redirect(){
       //check for 404
       if( is_404()){
           global $wp_query;
           //check that wp has figured out post_type from the request
           //and it's the type you're looking for
           if( isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'recruitment-event' ){
           
           wp_redirect( home_url( '/information-sessions/' ) );
           exit();
       }
    }
 }
 
/**
 * Add bricks repeater check for self serve bio blocks
  **/
  function bricks_has_repeater_rows( $repeater ) {
      return function_exists('have_rows') && have_rows( $repeater );
  }
 
 add_filter('acf/update_value/type=repeater', 'my_acf_empty_repeater_cleanup', 10, 3);
 function my_acf_empty_repeater_cleanup($value, $post_id, $field) {
     if (is_array($value)) {
         foreach ($value as $i => $row) {
             // Check if all subfields in the row are empty
             if (!array_filter($row)) {
                 unset($value[$i]);
             }
         }
     }
     return $value;
 }
 
 function check_my_repeater_is_empty($field_name) {
     $rows = get_field($field_name);
     // Returns true only if rows exist AND the first row isn't just an empty array
     return (is_array($rows) && !empty($rows) && count($rows) > 0);
 }
 
 // Add this so Bricks is allowed to run the function
 add_filter( 'bricks/code/echo_function_names', function() {
     return ['check_my_repeater_is_empty'];
 });


 

 // REORDER RESEARCHER TAXONOMIES ON SELF SERVE LISTINGS/PROFILES
 add_filter( 'bricks/terms/query_result', function( $terms, $query_obj ) {
 
     // Only target your specific taxonomy
     if ( empty( $query_obj->object_type ) || $query_obj->object_type !== 'term' ) {
         return $terms;
     }
 
     if ( $query_obj->taxonomy !== 'your_taxonomy' ) {
         return $terms;
     }
 
     // Map parent slug => child slug that must appear last
     $force_last = [
         'school-of-arts-sciences'        => 'researcher-school-of-arts-sciences',
         'graduate-studies-faculty'       => 'researcher-graduate-studies-faculty',
         'som'                            => 'researcher',
         'school-of-veterinary-medicine'  => 'researcher-school-of-veterinary-medicine',
     ];
 
     // If this query is pulling children of a parent
     if ( ! empty( $query_obj->parent ) ) {
 
         $parent = get_term( $query_obj->parent );
 
         if ( ! $parent || ! isset( $force_last[ $parent->slug ] ) ) {
             return $terms;
         }
 
         $researcher_slug = $force_last[ $parent->slug ];
         $researcher_term = null;
 
         foreach ( $terms as $key => $term ) {
             if ( $term->slug === $researcher_slug ) {
                 $researcher_term = $term;
                 unset( $terms[$key] );
                 break;
             }
         }
 
         if ( $researcher_term ) {
             $terms[] = $researcher_term;
         }
 
         return array_values( $terms );
     }
 
     return $terms;
 
 }, 10, 2 );
 
 
 
 
 

/**
 * 1. REGISTRY
 */
add_action('graphql_register_types', function () {
    $types = ['ManagedSguEmployeeWhereArgs', 'RootQueryToManagedSguEmployeeConnectionWhereArgs', 'PostObjectsConnectionWhereArgs'];
    foreach ($types as $type) {
        register_graphql_field($type, 'priorityEmail', ['type' => 'String']);
    }
    add_filter('graphql_PostObjectsConnectionOrderbyEnum_values', function ($values) {
        $values['EMPLOYEE_PORTAL_PRIORITY'] = [
            'value' => 'portal_priority_sort',
            'description' => __('Sort by Priority Email, then Status, then Name.', 'textdomain'),
        ];
        return $values;
    });
});

/**
 * 2. THE RESOLVER HIJACK
 * We must force 'graphql_cursor_pagination' to TRUE to kill the array connection.
 */
add_filter('graphql_connection_query_args', function ($query_args, $resolver) {
    if (!$resolver instanceof \WPGraphQL\Data\Connection\PostObjectConnectionResolver) {
        return $query_args;
    }

    $args = $resolver->get_args();
    
    if (isset($args['where']['orderby']['field']) && 'EMPLOYEE_PORTAL_PRIORITY' === $args['where']['orderby']['field']) {
        $query_args['custom_portal_sort'] = true;
        $query_args['priority_email_val'] = !empty($args['where']['priorityEmail']) ? sanitize_email($args['where']['priorityEmail']) : '___NONE___';
        
        // This is the magic line that kills "arrayconnection"
        $query_args['graphql_cursor_pagination'] = true;
        
        // Force a recognized orderby so WPGraphQL doesn't bail to array mode
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = 'managed_sgu_employee_portal_status';
    }

    return $query_args;
}, 30, 2);

/**
 * 3. THE SQL SORT (EMAIL FIRST)
 */
add_filter('posts_orderby', function ($orderby, $query) {
    if ($query->get('custom_portal_sort')) {
        global $wpdb;
        $email = $query->get('priority_email_val');

        $st_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_portal_status' LIMIT 1)";
        $em_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_requested_approver_email' LIMIT 1)";
        $ln_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_last_name' LIMIT 1)";

        // PRIORITY HIERARCHY:
        // 1. Does the Email match? (0 is top, 1 is rest)
        // 2. Status: Pending(1), Draft(2), Live(3)
        // 3. Last Name ASC
        return "CASE WHEN $em_sq = '$email' THEN 0 ELSE 1 END ASC, 
                CASE WHEN $st_sq = 'PendingApproval' THEN 1 WHEN $st_sq = 'Draft' THEN 2 WHEN $st_sq = 'Live' THEN 3 ELSE 4 END ASC, 
                $ln_sq ASC, 
                $wpdb->posts.ID DESC";
    }
    return $orderby;
}, 1000, 2);

/**
 * 4. THE CURSOR OVERRIDE
 */
add_filter('graphql_wp_query_cursor_orderby', function ($cursor_orderby, $query) {
    if ($query->get('custom_portal_sort')) {
        global $wpdb;
        $email = $query->get('priority_email_val');

        $st_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_portal_status' LIMIT 1)";
        $em_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_requested_approver_email' LIMIT 1)";
        $ln_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_last_name' LIMIT 1)";

        return [
            "CASE WHEN $em_sq = '$email' THEN 0 ELSE 1 END",
            "CASE WHEN $st_sq = 'PendingApproval' THEN 1 WHEN $st_sq = 'Draft' THEN 2 WHEN $st_sq = 'Live' THEN 3 ELSE 4 END",
            $ln_sq,
            "$wpdb->posts.ID"
        ];
    }
    return $cursor_orderby;
}, 1000, 2);

add_filter('graphql_wp_query_cursor_is_valid', '__return_true', 1000);

/**
 * Change the browser tab title for Managed SGU Employees (Rank Math Compatibility)
 * Uses the first, middle (if available), and last name meta fields.
 */
add_filter( 'rank_math/frontend/title', 'custom_rankmath_sgu_employee_tab_title', 10, 1 );

function custom_rankmath_sgu_employee_tab_title( $title ) {
    // Only alter the title on single Managed SGU Employee pages
    if ( is_singular( 'managed-sgu-employee' ) ) {
        global $post;

        // Uncomment the line below to test your debug log
        // error_log( 'Rank Math title filter triggered for Post ID: ' . $post->ID );

        // Fetch the name meta fields you established in the importer
        $first_name  = get_post_meta( $post->ID, 'managed_sgu_employee_first_name', true );
        $middle_name = get_post_meta( $post->ID, 'managed_sgu_employee_middle_name', true );
        $last_name   = get_post_meta( $post->ID, 'managed_sgu_employee_last_name', true );

        // If we have at least a first and last name, construct the new title
        if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
            
            // Check if middle name exists and is not empty
            if ( ! empty( $middle_name ) ) {
                $full_name = trim( $first_name . ' ' . $middle_name . ' ' . $last_name );
            } else {
                $full_name = trim( $first_name . ' ' . $last_name );
            }

            // Return just the employee's name. 
            // (Note: If you want to append the site name like "John Doe - Your Site", 
            // you would do: return $full_name . ' - ' . get_bloginfo('name');)
            return $full_name;
        }
    }

    // Return the default Rank Math title for all other pages
    return $title;
}

/**
 * 1. Register Parameter
 */
add_filter( 'rest_managed-sgu-employee_collection_params', function( $params ) {
    $params['priority_email'] = [
        'description'       => 'Email to float to the top of its status group.',
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_email',
    ];
    return $params;
});

/**
 * 2. MAP Request to Query
 */
add_action( 'pre_get_posts', function( $query ) {
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST && $query->get('post_type') === 'managed-sgu-employee' ) {
        if ( isset( $_GET['priority_email'] ) ) {
            $query->set( 'priority_email', sanitize_email( $_GET['priority_email'] ) );
        }
    }
});

/**
 * 3. SQL INJECTION: Multi-Tier Sort
 */
add_filter( 'posts_orderby', function( $orderby, $query ) {
    $email = $query->get('priority_email');

    if ( ! empty( $email ) ) {
        global $wpdb;

        $st_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_portal_status' LIMIT 1)";
        $em_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_requested_approver_email' LIMIT 1)";
        $ln_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_last_name' LIMIT 1)";
        $fn_sq = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id=$wpdb->posts.ID AND meta_key='managed_sgu_employee_first_name' LIMIT 1)";

        return "CASE 
                    WHEN $st_sq = 'PendingApproval' THEN 0 
                    WHEN $st_sq = 'Draft' THEN 1 
                    WHEN $st_sq = 'Live' THEN 2 
                    ELSE 3 
                END ASC, 
                /* 1. Exact priority match floats to the absolute top of the group */
                CASE WHEN $em_sq = '$email' THEN 0 ELSE 1 END ASC, 
                /* 2. Items WITH an email float above those WITHOUT an email */
                CASE WHEN ($em_sq IS NULL OR $em_sq = '') THEN 1 ELSE 0 END ASC,
                /* 3. Sort existing emails alphabetically */
                $em_sq ASC,
                /* 4. Alphabetical fallback by name */
                $ln_sq ASC, 
                $fn_sq ASC, 
                $wpdb->posts.ID DESC";
    }
    
    return $orderby;
}, 10, 2 );