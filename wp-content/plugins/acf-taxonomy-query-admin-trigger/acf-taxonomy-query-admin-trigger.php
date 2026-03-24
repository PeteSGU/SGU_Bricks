<?php
/**
 * Plugin Name: SOM Faculty Cleanup Tool
 * Description: Cleans up 'faculty-admin' posts. Deletes those in the 'som' branch unless they have an external department tag.
 * Version: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// --- 1. ADMIN MENU ---
add_action( 'admin_menu', function() {
    add_menu_page(
        'SOM Cleanup', 
        'SOM Cleanup', 
        'manage_options', 
        'som-cleanup', 
        'som_render_admin_page', 
        'dashicons-groups'
    );
});

function som_render_admin_page() {
    ?>
    <div class="wrap">
        <h2>School of Medicine (SOM) Faculty Cleanup</h2>
        <p>Targeting Post Type: <code>faculty-admin_staged</code> | Taxonomy: <code>faculty-department</code></p>

        <div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; margin-bottom: 20px; border-radius: 4px;">
            <h3>Step 1: Preview Selection</h3>
            <p>Show faculty who will be <strong>SAVED</strong> because they have a department tag <em>outside</em> of the SOM hierarchy.</p>
            <button id="run-query" class="button button-primary">Preview Faculty to Keep</button>
        </div>

        <div style="background: #fff; border: 1px solid #f5c2c7; padding: 20px; border-left: 4px solid #dc3545; border-radius: 4px;">
            <h3>Step 2: Permanent Deletion</h3>
            <p><strong>WARNING:</strong> This will permanently delete faculty in the SOM branch who have NO other department tags.</p>
            <button id="run-delete" class="button button-danger">Execute Permanent Deletion</button>
        </div>

        <h3>Execution Log:</h3>
        <div id="results-area" style="background: #f0f0f1; padding: 15px; border: 1px solid #ccd0d4; min-height: 150px; border-radius: 4px; overflow-y: auto; max-height: 600px;">
            <p>Ready...</p>
        </div>
    </div>
    <?php
}

// --- 2. ENQUEUE ---
add_action( 'admin_enqueue_scripts', function($hook) {
    if ( $hook !== 'toplevel_page_som-cleanup' ) return;
    
    wp_enqueue_script('som-cleanup-js', plugin_dir_url(__FILE__) . 'admin-query.js', array('jquery'), '6.0', true);

    wp_localize_script('som-cleanup-js', 'atqat_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('som_secure_nonce')
    ));
});

// --- 3. CONFIG HELPER ---
function som_get_cleanup_params() {
    $taxonomy = 'faculty-department';
    $root_slug = 'som'; // School of Medicine
    $post_type = 'faculty-admin';
    
    $root_term = get_term_by('slug', $root_slug, $taxonomy);
    if (!$root_term) return false;

    // Get SOM + all sub-departments
    $children = get_term_children($root_term->term_id, $taxonomy);
    $exclude_ids = array_merge(array($root_term->term_id), $children);
    
    return array(
        'taxonomy'  => $taxonomy,
        'post_type' => $post_type,
        'root_id'   => $root_term->term_id,
        'exclude_string' => implode(',', array_map('intval', array_unique($exclude_ids)))
    );
}

// --- 4. AJAX: PREVIEW ---
add_action('wp_ajax_som_preview', function() {
    global $wpdb;
    check_ajax_referer('som_secure_nonce', 'security');

    $params = som_get_cleanup_params();
    if (!$params) wp_send_json_error('Root term "som" not found.');

    $sql_filter = function($where) use ($wpdb, $params) {
        $subquery = $wpdb->prepare(
            "SELECT tr.object_id FROM {$wpdb->term_relationships} tr 
             INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id 
             WHERE tt.taxonomy = %s 
             GROUP BY tr.object_id 
             HAVING COUNT(CASE WHEN tt.term_id NOT IN ({$params['exclude_string']}) THEN 1 END) >= 1", 
            $params['taxonomy']
        );
        return $where . " AND {$wpdb->posts}.ID IN ({$subquery})";
    };

    add_filter('posts_where', $sql_filter);
    $query = new WP_Query(array(
        'post_type'      => $params['post_type'],
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'tax_query'      => array(
            array(
                'taxonomy'         => $params['taxonomy'],
                'field'            => 'term_id',
                'terms'            => array($params['root_id']),
                'include_children' => true
            )
        )
    ));
    remove_filter('posts_where', $sql_filter);

    if ($query->have_posts()) {
        $output = "<h4>" . $query->found_posts . " Faculty Members SAVED (Multi-Department):</h4><ul>";
        while ($query->have_posts()) {
            $query->the_post();
            $depts = wp_get_post_terms(get_the_ID(), $params['taxonomy'], array('fields' => 'names'));
            $output .= "<li><strong>" . get_the_title() . "</strong> (ID: " . get_the_ID() . ")<br><small>Departments: " . implode(', ', $depts) . "</small></li>";
        }
        $output .= "</ul>";
        wp_reset_postdata();
        wp_send_json_success($output);
    } else {
        wp_send_json_success("No faculty found in SOM who also belong to other departments.");
    }
});

// --- UPDATED AJAX: BATCH DELETE ---
add_action('wp_ajax_som_delete', function() {
    global $wpdb;
    check_ajax_referer('som_secure_nonce', 'security');

    $params = som_get_cleanup_params();
    if (!$params) wp_send_json_error('Config not found.');

    // 1. Identify IDs to Keep
    $sql_filter = function($where) use ($wpdb, $params) {
        $subquery = $wpdb->prepare("SELECT tr.object_id FROM {$wpdb->term_relationships} tr INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = %s GROUP BY tr.object_id HAVING COUNT(CASE WHEN tt.term_id NOT IN ({$params['exclude_string']}) THEN 1 END) >= 1", $params['taxonomy']);
        return $where . " AND {$wpdb->posts}.ID IN ({$subquery})";
    };
    
    add_filter('posts_where', $sql_filter);
    $keep_ids = (new WP_Query(array('post_type' => $params['post_type'], 'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids', 'tax_query' => array(array('taxonomy' => $params['taxonomy'], 'field' => 'term_id', 'terms' => $params['root_id'])))))->posts;
    remove_filter('posts_where', $sql_filter);

    // 2. Identify ALL IDs in SOM
    $all_ids = (new WP_Query(array('post_type' => $params['post_type'], 'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids', 'tax_query' => array(array('taxonomy' => $params['taxonomy'], 'field' => 'term_id', 'terms' => $params['root_id'])))))->posts;

    // 3. Calculate full list to delete
    $to_delete_all = array_diff($all_ids, $keep_ids);
    $total_to_delete = count($to_delete_all);

    // 4. Slice the array to handle only a BATCH (e.g., 50)
    $batch_size = 50; 
    $current_batch = array_slice($to_delete_all, 0, $batch_size);
    
    $deleted_this_round = 0;
    foreach ($current_batch as $id) {
        if (wp_delete_post($id, true)) {
            $deleted_this_round++;
        }
    }

    // 5. Send back info to JS to decide if we need to call again
    wp_send_json_success(array(
        'deleted'   => $deleted_this_round,
        'remaining' => $total_to_delete - $deleted_this_round,
        'done'      => ($total_to_delete <= $deleted_this_round)
    ));
});