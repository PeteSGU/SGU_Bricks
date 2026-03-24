<?php
/**
 * Plugin Name: Faculty & Researcher Importer
 * Description: Migrates Faculty and Researchers to Managed SGU Employees. Batch processes records.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 1. ADMIN MENU SETUP
 */
add_action( 'admin_menu', function() {
    add_management_page( 
        'Staff Import Tools',      
        'Staff Import Tools',      
        'manage_options',          
        'staff-import-tools',      
        'render_staff_import_page' 
    );
});

/**
 * 2. ADMIN UI RENDERING & BATCH CONTROLLER
 */
function render_staff_import_page() {
    
    // --- BATCH PROCESSING & PREVIEW HANDLER ---
    if ( isset( $_GET['import_action'] ) ) {
        $action = sanitize_text_field( $_GET['import_action'] );

        // Handle Preview
        if ( $action === 'global_preview' ) {
            echo '<div class="wrap">';
            render_global_faculty_preview();
            echo '</div>';
            return;
        }

        // Handle Batching
        if ( isset( $_GET['batch'] ) ) {
            $batch = intval( $_GET['batch'] );
            echo '<div class="wrap"><h1>Processing Batch ' . $batch . '...</h1>';
            
            if ( $action === 'faculty' ) {
                process_faculty_batch( $batch );
            } elseif ( $action === 'researcher' ) {
                process_researcher_batch( $batch );
            } elseif ( $action === 'global_faculty' ) {
                process_global_faculty_batch( $batch );
            }
            
            echo '</div>';
            return; 
        }
    }

    // --- MAIN UI ---
    ?>
    <div class="wrap">
        <h1>Staff Migration Tools</h1>
        
        <div style="margin-bottom: 20px; padding: 20px; border: 1px solid #ccc; background: #fff;">
            <h2>1. Faculty to Employee Import (SOM Only)</h2>
            <p>Migrates <strong>all</strong> SOM Faculty Admin posts found in the SOM department.</p>
            <p><em>Processes in batches of 15 to prevent timeouts.</em></p>
            <form method="post">
                <?php wp_nonce_field('run_faculty_import_nonce', 'faculty_import_nonce'); ?>
                <input type="submit" name="init_faculty_import" class="button button-primary" value="Run Faculty Import">
            </form>
        </div>

        <div style="margin-bottom: 20px; padding: 20px; border: 1px solid #ccc; background: #fff;">
            <h2>2. Researcher Sync</h2>
            <p>Syncs <strong>all</strong> researchers and updates by email match.</p>
            <p><em>Processes in batches of 15 to prevent timeouts.</em></p>
            <form method="post">
                <?php wp_nonce_field('run_researcher_sync_nonce', 'researcher_sync_nonce'); ?>
                <input type="submit" name="init_researcher_sync" class="button button-secondary" value="Run Researcher Sync">
            </form>
        </div>

        <div style="margin-bottom: 20px; padding: 20px; border: 1px solid #ccc; background: #fff;">
            <h2>3. Global Faculty Import (By Email)</h2>
            <p>Looks at <strong>every</strong> faculty-admin post regardless of department. If their email does not exist in the employee directory, they will be imported.</p>
            <p><em>Processes in batches of 15 to prevent timeouts.</em></p>
            
            <form method="post" style="display:inline-block; margin-right: 15px;">
                <?php wp_nonce_field('preview_global_faculty_nonce', 'global_faculty_preview_nonce'); ?>
                <input type="submit" name="preview_global_faculty" class="button button-secondary" value="Preview Import (No changes made)">
            </form>

            <form method="post" style="display:inline-block;">
                <?php wp_nonce_field('run_global_faculty_nonce', 'global_faculty_import_nonce'); ?>
                <input type="submit" name="init_global_faculty_import" class="button button-primary" value="Run Global Faculty Import">
            </form>
        </div>

        <?php 
        // --- FORM SUBMISSION HANDLERS (INITIALIZERS) ---
        
        if ( isset( $_POST['init_faculty_import'] ) && check_admin_referer('run_faculty_import_nonce', 'faculty_import_nonce') ) {
            $url = admin_url( 'tools.php?page=staff-import-tools&import_action=faculty&batch=1' );
            echo "<script>window.location.href='$url';</script>";
            exit;
        } 
        
        if ( isset( $_POST['init_researcher_sync'] ) && check_admin_referer('run_researcher_sync_nonce', 'researcher_sync_nonce') ) {
            $url = admin_url( 'tools.php?page=staff-import-tools&import_action=researcher&batch=1' );
            echo "<script>window.location.href='$url';</script>";
            exit;
        } 

        if ( isset( $_POST['preview_global_faculty'] ) && check_admin_referer('preview_global_faculty_nonce', 'global_faculty_preview_nonce') ) {
            $url = admin_url( 'tools.php?page=staff-import-tools&import_action=global_preview' );
            echo "<script>window.location.href='$url';</script>";
            exit;
        }

        if ( isset( $_POST['init_global_faculty_import'] ) && check_admin_referer('run_global_faculty_nonce', 'global_faculty_import_nonce') ) {
            $url = admin_url( 'tools.php?page=staff-import-tools&import_action=global_faculty&batch=1' );
            echo "<script>window.location.href='$url';</script>";
            exit;
        }
        ?>
    </div>
    <?php
}

/**
 * PREVIEW: GLOBAL FACULTY
 * Shows a table of what will be imported without doing the import.
 */
function render_global_faculty_preview() {
    global $wpdb;
    
    echo '<h1>Preview: Global Faculty Import</h1>';
    echo '<p><a href="' . admin_url('tools.php?page=staff-import-tools') . '" class="button">&laquo; Back to Tools</a></p>';

    // 1. Fetch all existing employee emails using direct SQL for performance
    $existing_emails_raw = $wpdb->get_col("
        SELECT meta_value 
        FROM {$wpdb->postmeta} pm
        JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_type = 'managed-sgu-employee' 
        AND pm.meta_key = 'managed_sgu_employee_contact_email'
        AND p.post_status = 'publish'
    ");
    
    // Clean and lowercase all existing emails to ensure strict matching
    $existing_emails = array_filter(array_map('strtolower', array_map('trim', $existing_emails_raw)));

    // 2. Fetch all faculty admin posts
    $faculty_posts = get_posts([
        'post_type'      => 'faculty-admin',
        'posts_per_page' => -1, // Get all for preview table
        'post_status'    => 'publish'
    ]);

    $to_import = [];

    foreach ( $faculty_posts as $f_post ) {
        $email = strtolower(trim(get_post_meta( $f_post->ID, 'faculty_staff_email', true )));
        
        // Skip if email is empty (can't cross-reference)
        if ( empty( $email ) ) continue;

        // If email is NOT in the employee database, stage for preview
        if ( ! in_array( $email, $existing_emails ) ) {
            $to_import[] = [
                'first'  => get_post_meta( $f_post->ID, 'faculty_staff_first_name', true ),
                'middle' => get_post_meta( $f_post->ID, 'faculty_staff_middle_name', true ),
                'last'   => get_post_meta( $f_post->ID, 'faculty_staff_last_name', true ),
                'email'  => $email
            ];
        }
    }

    if ( empty( $to_import ) ) {
        echo '<div class="notice notice-success"><p>No new faculty to import. All faculty-admin emails already exist in the employee directory.</p></div>';
    } else {
        echo '<p><strong>Found ' . count( $to_import ) . ' records ready to be imported:</strong></p>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Email</th>
              </tr></thead>';
        echo '<tbody>';
        foreach ( $to_import as $row ) {
            echo '<tr>';
            echo '<td>' . esc_html( $row['first'] ) . '</td>';
            echo '<td>' . esc_html( $row['middle'] ) . '</td>';
            echo '<td>' . esc_html( $row['last'] ) . '</td>';
            echo '<td>' . esc_html( $row['email'] ) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
}

/**
 * GLOBAL FACULTY BATCH PROCESSOR
 * Imports ALL faculty as long as their email doesn't exist in Employees.
 */
function process_global_faculty_batch( $batch_page ) {
    $posts_per_page = 15;

    $args = [
        'post_type'      => 'faculty-admin',
        'posts_per_page' => $posts_per_page,
        'paged'          => $batch_page, 
        'orderby'        => 'ID',
        'order'          => 'ASC'
        // Note: No tax_query here. We want ALL faculty.
    ];

    $faculty_posts = get_posts( $args );
    
    // If empty, we are done
    if ( empty( $faculty_posts ) ) {
        echo "<div class='notice notice-success'><p><strong>Global Faculty Import Complete!</strong> All batches finished.</p></div>";
        echo "<p><a href='" . admin_url('tools.php?page=staff-import-tools') . "' class='button'>Return to Tools</a></p>";
        return;
    }

    $count = 0;
    $skipped = 0;

    foreach ( $faculty_posts as $f_post ) {
        
        $email = trim(get_post_meta( $f_post->ID, 'faculty_staff_email', true ));
        
        // If they have no email, skip them entirely
        if ( empty( $email ) ) {
            $skipped++;
            continue;
        }

        // Duplicate Check based on EXACT EMAIL MATCH
        $already_imported = get_posts([
            'post_type'      => 'managed-sgu-employee',
            'meta_key'       => 'managed_sgu_employee_contact_email',
            'meta_value'     => $email,
            'fields'         => 'ids',
            'posts_per_page' => 1
        ]);

        if ( empty( $already_imported ) ) {
            
            // Collect Source Meta
            $first_name = get_post_meta( $f_post->ID, 'faculty_staff_first_name', true );
            $middle_name = get_post_meta( $f_post->ID, 'faculty_staff_middle_name', true );
            $last_name  = get_post_meta( $f_post->ID, 'faculty_staff_last_name', true );
            $credentials = get_post_meta( $f_post->ID, 'faculty_staff_credentials', true );
            $phone_office = get_post_meta( $f_post->ID, 'faculty_staff_phone_office', true );
            $phone_office_ext = get_post_meta( $f_post->ID, 'faculty_staff_phone_office_ext', true );
            $bio = get_post_meta( $f_post->ID, 'faculty_staff_bio', true );
            $banner_id = get_post_meta( $f_post->ID, 'faculty_banner_id', true );
            $featured_image_id = get_post_thumbnail_id( $f_post->ID );

            $dept_terms = wp_get_object_terms( $f_post->ID, 'faculty-department', ['fields' => 'slugs'] );
            $position_terms = wp_get_object_terms( $f_post->ID, 'position-type', ['fields' => 'ids'] );

            $full_name = $first_name . ( !empty($middle_name) ? ' ' . $middle_name : '' ) . ' ' . $last_name;
            $post_title = $full_name . ' (' . $email . ')';

            $new_employee_id = wp_insert_post([
                'post_title'   => $post_title,
                'post_content' => $f_post->post_content,
                'post_status'  => 'publish',
                'post_type'    => 'managed-sgu-employee',
                'post_name'    => $full_name,
            ]);

            if ( $new_employee_id && ! is_wp_error( $new_employee_id ) ) {
                // Map Core Meta Fields
                update_post_meta( $new_employee_id, '_original_faculty_id', $f_post->ID );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_first_name', $first_name );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_middle_name', $middle_name );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_last_name', $last_name );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_credentials', $credentials );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_contact_email', $email );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_phone_number', $phone_office );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_phone_extension', $phone_office_ext );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_full_bio', $bio );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_banner_id', $banner_id );

                // Category & Hospital Repeater Mapping
                if ( in_array( 'clinical-faculty', $dept_terms ) ) {
                    wp_set_object_terms( $new_employee_id, 'clinical-faculty', 'employee-category' );

                    $raw_position = get_post_meta( $f_post->ID, 'faculty_staff_position', true );
                    $faculty_position_text = trim( str_replace( ["\xe2\x80\x98", "\xe2\x80\x99", "’", "‘"], "'", $raw_position ) );

                    if ( ! empty( $faculty_position_text ) ) {
                        $titles_to_lookup = [];
                        
                        // Full Hospital Mapping Logic
                        switch ( $faculty_position_text ) {
                            case 'Alameda Health System Highland Campus': $titles_to_lookup = ['Alameda Health System - Highland Hospital']; break;
                            case 'Atlantic Health System -Morristown Medical Center & Atlantic Health System- Overlook Medical Center': $titles_to_lookup = ['Atlantic Health System - Morristown Medical Center', 'Atlantic Health System - Overlook Medical Center']; break;
                            case 'Berrycroft Community Health Centre (DME) and Stoke Mandeville Hospital (DME & CD)': $titles_to_lookup = ['Berrycroft Community Health Centre', 'Stoke Mandeville Hospital']; break;
                            case 'CHA Hollywood Presbyterian Medical Center (CD) and Mission Community Hospital (CD)': $titles_to_lookup = ['CHA Hollywood Presbyterian Medical Center', 'Mission Community Hospital']; break;
                            case 'CHA Hollywood Presbyterian Medical Center (CD), Los Angeles Downtown Medical Center (CD), Mission Community Hospital (CD) and PIH Health Good Samaritan Hospital (DME)': $titles_to_lookup = ['CHA Hollywood Presbyterian Medical Center', 'Los Angeles Downtown Medical Center', 'Mission Community Hospital', 'PIH Health Good Samaritan Hospital']; break;
                            case 'CHA Hollywood Presbyterian Medical Center (DME & CD), Los Angeles Downtown Medical Center (DME & CD) and Mission Community Hospital (DME & CD)': $titles_to_lookup = ['CHA Hollywood Presbyterian Medical Center', 'Los Angeles Downtown Medical Center', 'Mission Community Hospital']; break;
                            case 'Doctors Hospital Modesto': $titles_to_lookup = ['Doctors Medical Center of Modesto']; break;
                            case 'Lawrence House Group / Welbourne Health Centre (DME) and North Middlesex University Hospital (DME)': $titles_to_lookup = ['Lawrence House Group', 'Welbourne Health Centre', 'North Middlesex University Hospital']; break;
                            case 'Lawrence House Group/Welbourne Health Centre': $titles_to_lookup = ['Lawrence House Group', 'Welbourne Health Centre']; break;
                            case 'Long Island Community Hospital': $titles_to_lookup = ['NYU Langone Hospital - Suffolk']; break;
                            case 'Los Angeles Downtown Medical Center (LADMC)': $titles_to_lookup = ['Los Angeles Downtown Medical Center']; break;
                            case 'Mercy Health St. Vincent': $titles_to_lookup = ['Mercy Health - St. Vincent Medical Center']; break;
                            case 'Mercy St. Vincent Medical Center': $titles_to_lookup = ['Mercy Health - St. Vincent Medical Center']; break;
                            case 'Mission Community Hospital (CD) and PIH Health Whittier Hospital  (CD)': $titles_to_lookup = ['Mission Community Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'Montefiore New Rochelle': $titles_to_lookup = ['Montefiore New Rochelle Hospital']; break;
                            case "Nationwide Children's Hospital": $titles_to_lookup = ['Nationwide Children’s Hospital']; break;
                            case "Nationwide Children's Hospital (DME) and Mercy St. Vincent Medical Center (CD)": $titles_to_lookup = ['Nationwide Children’s Hospital', 'Mercy Health - St. Vincent Medical Center']; break;
                            case 'Norfolk & Norwich University Hospital (DME) and The Market Surgery (DME)': $titles_to_lookup = ['Norfolk &amp; Norwich University Hospital', 'The Market Surgery']; break;
                            case 'North Hampshire Hospital (CD) and Royal Hampshire County Hospital (CD)': $titles_to_lookup = ['North Hampshire Hospital', 'Royal Hampshire County Hospital']; break;
                            case 'Northwest Hospital Center': $titles_to_lookup = ['Northwest Hospital']; break;
                            case 'NYC Health & Hospitals/ Metropolitan': $titles_to_lookup = ['NYC Health + Hospitals | Metropolitan']; break;
                            case 'NYC Health + Hospitals | Elmhurst & Queens': $titles_to_lookup = ['NYC Health + Hospitals | Elmhurst', 'NYC Health + Hospitals | Queens']; break;
                            case 'NYC Health + Hospitals/ Elmhurst & Queens': $titles_to_lookup = ['NYC Health + Hospitals | Elmhurst', 'NYC Health + Hospitals | Queens']; break;
                            case 'NYC Health+Hospitals/Elmhurst': $titles_to_lookup = ['NYC Health + Hospitals | Elmhurst']; break;
                            case 'PIH Health Downey Hospital (CD) and PIH Health Whittier Hospital (CD)': $titles_to_lookup = ['PIH Health Downey Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'PIH Health Downey Hospital (DME) and PIH Health Whittier Hospital (DME)': $titles_to_lookup = ['PIH Health Downey Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'PIH Health Good Samaritan Hospital (CD) and  PIH Health Whittier Hospital (CD)': $titles_to_lookup = ['PIH Health Good Samaritan Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'Poole  General Hospital / The Adam Practice': $titles_to_lookup = ['Poole General Hospital', 'The Adam Practice']; break;
                            case 'Poole General Hospital (DME & CD) and The Adam Practice (DME)': $titles_to_lookup = ['Poole General Hospital', 'The Adam Practice']; break;
                            case 'Rutgers/Jersey City Medical Center': $titles_to_lookup = ['Rutgers Health/Jersey City Medical Center']; break;
                            case 'Sheepcot Medical Center': $titles_to_lookup = ['Sheepcot Medical Centre']; break;
                            case 'Sheepcot Medical Center (DME) and Watford General Hospital (DME & CD)': $titles_to_lookup = ['Sheepcot Medical Centre', 'Watford General Hospital']; break;
                            case "St. Ann's Hospital - London": $titles_to_lookup = ['St. Ann’s Hospital – London']; break;
                            case "St. Ann's Hospital - Poole": $titles_to_lookup = ['St. Ann’s Hospital – Poole']; break;
                            case "St. George's General Hospital": $titles_to_lookup = ['St. George’s General Hospital']; break;
                            case "St. Joseph's Hospital Health Center": $titles_to_lookup = ['St. Joseph’s Hospital Health Center']; break;
                            case "St. Joseph's University Medical Center": $titles_to_lookup = ['St. Joseph’s University Medical Center']; break;
                            case "St. Mary's Hospital - Connecticut": $titles_to_lookup = ['St. Mary’s Hospital - Connecticut']; break;
                            case "St. Mary's General Hospital/Saint Clare's Hospital": $titles_to_lookup = ['St. Mary’s Hospital - New Jersey', 'Saint Clare’s Denville Hospital']; break;
                            case "St. Michael's Medical Center": $titles_to_lookup = ['St. Michael’s Medical Center']; break;
                            default: $titles_to_lookup = [$faculty_position_text]; break;
                        }

                        $h_row_index = 0;
                        foreach ( $titles_to_lookup as $lookup_title ) {
                            $hospital_post = get_page_by_title( $lookup_title, OBJECT, 'affiliated-hospital' );
                            if ( $hospital_post ) {
                                update_post_meta( $new_employee_id, 'managed_sgu_employee_hospitals_' . $h_row_index . '_managed_sgu_employee_hospital', $hospital_post->ID );
                                update_post_meta( $new_employee_id, '_managed_sgu_employee_hospitals_' . $h_row_index . '_managed_sgu_employee_hospital', 'field_6996a44a74a6c' );
                                $h_row_index++;
                            }
                        }
                        if ( $h_row_index > 0 ) {
                            update_post_meta( $new_employee_id, 'managed_sgu_employee_hospitals', $h_row_index );
                            update_post_meta( $new_employee_id, '_managed_sgu_employee_hospitals', 'field_6996a43f74a6b' );
                        }
                    }
                } else {
                    wp_set_object_terms( $new_employee_id, 'faculty', 'employee-category' );
                }

                // Department Taxonomy
                wp_set_object_terms( $new_employee_id, $dept_terms, 'faculty-department' );

                // Headshot
                if ( $featured_image_id ) {
                    update_post_meta( $new_employee_id, 'managed_sgu_employee_headshot_image', $featured_image_id );
                    set_post_thumbnail( $new_employee_id, $featured_image_id );
                }

                // Position Repeater mapping
                if ( ! empty( $position_terms ) && ! is_wp_error( $position_terms ) ) {
                    $row_count = 0;
                    foreach ( $position_terms as $term_id ) {
                        update_post_meta( $new_employee_id, 'managed_sgu_employee_postions_' . $row_count . '_managed_sgu_employee_postion', $term_id );
                        update_post_meta( $new_employee_id, '_managed_sgu_employee_postions_' . $row_count . '_managed_sgu_employee_postion', 'field_6996a2006b97a' );
                        $row_count++;
                    }
                    update_post_meta( $new_employee_id, 'managed_sgu_employee_postions', $row_count );
                    update_post_meta( $new_employee_id, '_managed_sgu_employee_postions', 'field_6996a1ea6b979' );
                }
                $count++;
            }
        } else {
            $skipped++;
        }
    }
    
    // Auto-redirect to next batch
    $next_batch = $batch_page + 1;
    echo "<p>Batch $batch_page processed ($count created, $skipped skipped).</p>";
    echo "<p><strong>Moving to batch $next_batch...</strong></p>";
    $next_url = admin_url( "tools.php?page=staff-import-tools&import_action=global_faculty&batch=$next_batch" );
    echo "<script>setTimeout(function(){ window.location.href = '$next_url'; }, 1000);</script>";
}

/**
 * RESEARCHER BATCH PROCESSOR
 */
function process_researcher_batch( $batch_page ) {
    $posts_per_page = 5;
    
    $researchers = get_posts([
        'post_type'      => 'researcher',
        'posts_per_page' => $posts_per_page,
        'paged'          => $batch_page, 
        'orderby'        => 'ID',
        'order'          => 'ASC'
    ]);

    if ( empty( $researchers ) ) {
        echo "<div class='notice notice-success'><p><strong>Researcher Sync Complete!</strong> All batches finished.</p></div>";
        echo "<p><a href='" . admin_url('tools.php?page=staff-import-tools') . "' class='button'>Return to Tools</a></p>";
        return;
    }

    $updated_count = 0;
    $created_count = 0;

    foreach ( $researchers as $r_post ) {
        $r_email     = get_post_meta( $r_post->ID, 'researcher_email', true );
        $r_last_name = get_post_meta( $r_post->ID, 'researcher_last_name', true );
        $r_phone     = get_post_meta( $r_post->ID, 'researcher_phone', true );
        $r_about     = get_post_meta( $r_post->ID, 'researcher_about', true );
        $r_position_terms = wp_get_object_terms( $r_post->ID, 'researcher-type', ['fields' => 'slugs'] );
        
        if ( empty( $r_email ) ) continue;

        $existing_employee = get_posts([
            'post_type'      => 'managed-sgu-employee',
            'meta_key'       => 'managed_sgu_employee_contact_email',
            'meta_value'     => $r_email,
            'posts_per_page' => 1,
            'fields'         => 'ids'
        ]);

        $employee_id = !empty( $existing_employee ) ? $existing_employee[0] : 0;
        $is_new_post = ( $employee_id === 0 );

        $first_name  = '';
        $credentials = '';
        if ( ! empty( $r_last_name ) ) {
            $parts = explode( $r_last_name, $r_post->post_title );
            if ( count( $parts ) >= 2 ) {
                $first_name  = trim( $parts[0] );
                $credentials = trim( end( $parts ), " ,/;" ); 
            }
        }

        $post_data = [
            'post_content' => $r_post->post_content,
            'post_status'  => 'publish',
            'post_type'    => 'managed-sgu-employee',
        ];

        if ( $is_new_post ) {
            $full_name = trim( $first_name . ' ' . $r_last_name );
            $constructed_title = $full_name . ' (' . $r_email . ')';
            $post_data['post_title'] = !empty( trim($first_name . $r_last_name) ) ? $constructed_title : $r_post->post_title;
            $post_data['post_name'] = $full_name;
            $employee_id = wp_insert_post( $post_data );
            $created_count++;
        } else {
            $post_data['ID'] = $employee_id;
            wp_update_post( $post_data );
            $updated_count++;
        }

        if ( $employee_id && !is_wp_error( $employee_id ) ) {
            wp_set_object_terms( $employee_id, 'researcher', 'employee-category', true );

            $clean_bio = wp_strip_all_tags( $r_about );
            update_post_meta( $employee_id, 'managed_sgu_employee_full_bio', $clean_bio );

            // 1. POSITION
            if ( ! empty( $r_position_terms ) && ! is_wp_error( $r_position_terms ) ) {
                $existing_pos_count = get_post_meta( $employee_id, 'managed_sgu_employee_postions', true );
                $existing_pos_count = $existing_pos_count ? intval( $existing_pos_count ) : 0;
                
                $existing_pos_values = [];
                for ( $i = 0; $i < $existing_pos_count; $i++ ) {
                    $val = get_post_meta( $employee_id, 'managed_sgu_employee_postions_' . $i . '_managed_sgu_employee_postion', true );
                    if ( $val ) $existing_pos_values[] = $val;
                }

                $pos_row_count = $existing_pos_count; 

                foreach ( $r_position_terms as $r_term ) {
                    $term = get_term_by( 'slug', $r_term, 'position-type' );
                    if ( $term && !in_array( $term->term_id, $existing_pos_values ) ) {
                        update_post_meta( $employee_id, 'managed_sgu_employee_postions_' . $pos_row_count . '_managed_sgu_employee_postion', $term->term_id );
                        update_post_meta( $employee_id, '_managed_sgu_employee_postions_' . $pos_row_count . '_managed_sgu_employee_postion', 'field_6996a2006b97a' );
                        $pos_row_count++;
                    }
                }
                update_post_meta( $employee_id, 'managed_sgu_employee_postions', $pos_row_count );
                update_post_meta( $employee_id, '_managed_sgu_employee_postions', 'field_6996a1ea6b979' );
            }

            // 2. AFFILIATIONS
            $affiliations_count_source = get_post_meta( $r_post->ID, 'researcher_affiliations', true );
            if ( $affiliations_count_source ) {
                $existing_aff_count = get_post_meta( $employee_id, 'managed_sgu_employee_affiliations', true );
                $existing_aff_count = $existing_aff_count ? intval( $existing_aff_count ) : 0;

                $existing_aff_values = [];
                for ( $i = 0; $i < $existing_aff_count; $i++ ) {
                    $val = get_post_meta( $employee_id, 'managed_sgu_employee_affiliations_' . $i . '_managed_sgu_employee_affiliation', true );
                    if ( $val ) $existing_aff_values[] = $val;
                }

                $aff_row_count = $existing_aff_count;
                for ( $i = 0; $i < $affiliations_count_source; $i++ ) {
                    $val = get_post_meta( $r_post->ID, 'researcher_affiliations_' . $i . '_researcher_affiliation', true );
                    if ( !empty( $val ) && !in_array( $val, $existing_aff_values ) ) {
                        update_post_meta( $employee_id, 'managed_sgu_employee_affiliations_' . $aff_row_count . '_managed_sgu_employee_affiliation', $val );
                        update_post_meta( $employee_id, '_managed_sgu_employee_affiliations_' . $aff_row_count . '_managed_sgu_employee_affiliation', 'field_6996a42174a6a' );
                        $aff_row_count++;
                    }
                }
                update_post_meta( $employee_id, 'managed_sgu_employee_affiliations', $aff_row_count );
                update_post_meta( $employee_id, '_managed_sgu_employee_affiliations', 'field_6996a40e74a69' );
            }

            // 3. INTERESTS
            $interests_count_source = get_post_meta( $r_post->ID, 'researcher_areas_of_interest', true );
            if ( $interests_count_source ) {
                $existing_int_count = get_post_meta( $employee_id, 'managed_sgu_employee_research_&_interests', true );
                $existing_int_count = $existing_int_count ? intval( $existing_int_count ) : 0;

                $existing_int_values = [];
                for ( $i = 0; $i < $existing_int_count; $i++ ) {
                    $val = get_post_meta( $employee_id, 'managed_sgu_employee_research_&_interests_' . $i . '_managed_sgu_employee_research_&_interest', true );
                    if ( $val ) $existing_int_values[] = $val;
                }

                $int_row_count = $existing_int_count;
                for ( $i = 0; $i < $interests_count_source; $i++ ) {
                    $interest_val = get_post_meta( $r_post->ID, 'researcher_areas_of_interest_' . $i . '_interest', true );
                    if ( !empty( $interest_val ) && !in_array( $interest_val, $existing_int_values ) ) {
                        update_post_meta( $employee_id, 'managed_sgu_employee_research_&_interests_' . $int_row_count . '_managed_sgu_employee_research_&_interest', $interest_val );
                        update_post_meta( $employee_id, '_managed_sgu_employee_research_&_interests_' . $int_row_count . '_managed_sgu_employee_research_&_interest', 'field_6996a3b16b98b' );
                        $int_row_count++;
                    }
                }
                update_post_meta( $employee_id, 'managed_sgu_employee_research_&_interests', $int_row_count );
                update_post_meta( $employee_id, '_managed_sgu_employee_research_&_interests', 'field_6996a39d6b98a' );
            }

            // 4. JOURNALS
            $pub_count_source = get_post_meta( $r_post->ID, 'researcher_selected_publications', true );
            if ( $pub_count_source ) {
                $existing_jour_count = get_post_meta( $employee_id, 'managed_sgu_employee_journal_articles', true );
                $existing_jour_count = $existing_jour_count ? intval( $existing_jour_count ) : 0;

                $existing_jour_values = [];
                for ( $i = 0; $i < $existing_jour_count; $i++ ) {
                    $val = get_post_meta( $employee_id, 'managed_sgu_employee_journal_articles_' . $i . '_managed_sgu_employee_journal_article', true );
                    if ( $val ) $existing_jour_values[] = $val;
                }

                $jour_row_count = $existing_jour_count;
                for ( $i = 0; $i < $pub_count_source; $i++ ) {
                    $pub_val = get_post_meta( $r_post->ID, 'researcher_selected_publications_' . $i . '_publication', true );
                    if ( !empty( $pub_val ) && !in_array( $pub_val, $existing_jour_values ) ) {
                        update_post_meta( $employee_id, 'managed_sgu_employee_journal_articles_' . $jour_row_count . '_managed_sgu_employee_journal_article', $pub_val );
                        update_post_meta( $employee_id, '_managed_sgu_employee_journal_articles_' . $jour_row_count . '_managed_sgu_employee_journal_article', 'field_6996a6fbb50a9' );
                        $jour_row_count++;
                    }
                }
                update_post_meta( $employee_id, 'managed_sgu_employee_journal_articles', $jour_row_count );
                update_post_meta( $employee_id, '_managed_sgu_employee_journal_articles', 'field_6996a6ecb50a8' );
            }

            // 5. PROJECTS
            $project_count_source = get_post_meta( $r_post->ID, 'researcher_projects', true );
            if ( $project_count_source ) {
                $existing_proj_count = get_post_meta( $employee_id, 'managed_sgu_employee_selected_projects', true );
                $existing_proj_count = $existing_proj_count ? intval( $existing_proj_count ) : 0;

                $existing_proj_values = [];
                for ( $i = 0; $i < $existing_proj_count; $i++ ) {
                    $val = get_post_meta( $employee_id, 'managed_sgu_employee_selected_projects_' . $i . '_managed_sgu_employee_selected_project', true );
                    if ( $val ) $existing_proj_values[] = $val;
                }

                $proj_row_count = $existing_proj_count;
                for ( $i = 0; $i < $project_count_source; $i++ ) {
                    $project_val = get_post_meta( $r_post->ID, 'researcher_projects_' . $i . '_project', true );
                    if ( !empty( $project_val ) && !in_array( $project_val, $existing_proj_values ) ) {
                        update_post_meta( $employee_id, 'managed_sgu_employee_selected_projects_' . $proj_row_count . '_managed_sgu_employee_selected_project', $project_val );
                        update_post_meta( $employee_id, '_managed_sgu_employee_selected_projects_' . $proj_row_count . '_managed_sgu_employee_selected_project', 'field_6996a78cb50b1' );
                        $proj_row_count++;
                    }
                }
                update_post_meta( $employee_id, 'managed_sgu_employee_selected_projects', $proj_row_count );
                update_post_meta( $employee_id, '_managed_sgu_employee_selected_projects', 'field_6996a776b50b0' );
            }

            $featured_image_id = get_post_thumbnail_id( $r_post->ID );
            if ( $featured_image_id ) {
                set_post_thumbnail( $employee_id, $featured_image_id );
                update_post_meta( $employee_id, 'managed_sgu_employee_headshot_image', $featured_image_id );
            }

            if ( $is_new_post ) {
                $phone_number = $r_phone;
                $phone_ext    = '';
                if ( ! empty( $r_phone ) ) {
                    $delimiters = ['ext.', 'ext', 'x'];
                    foreach ( $delimiters as $delim ) {
                        $phone_parts = explode( $delim, $r_phone );
                        if ( count( $phone_parts ) >= 2 ) {
                            $phone_number = trim( $phone_parts[0] );
                            $phone_ext    = trim( $phone_parts[1] );
                            break; 
                        }
                    }
                }
                update_post_meta( $employee_id, 'managed_sgu_employee_contact_email', $r_email );
                update_post_meta( $employee_id, 'managed_sgu_employee_last_name', $r_last_name );
                update_post_meta( $employee_id, 'managed_sgu_employee_first_name', $first_name );
                update_post_meta( $employee_id, 'managed_sgu_employee_credentials', $credentials );
                update_post_meta( $employee_id, 'managed_sgu_employee_phone_number', $phone_number );
                update_post_meta( $employee_id, 'managed_sgu_employee_phone_extension', $phone_ext );
            }
        }
    }

    $next_batch = $batch_page + 1;
    echo "<p>Batch $batch_page processed ($updated_count updated, $created_count created).</p>";
    echo "<p><strong>Moving to batch $next_batch...</strong></p>";
    $next_url = admin_url( "tools.php?page=staff-import-tools&import_action=researcher&batch=$next_batch" );
    echo "<script>setTimeout(function(){ window.location.href = '$next_url'; }, 1000);</script>";
}

/**
 * FACULTY BATCH PROCESSOR (Original SOM Only)
 */
function process_faculty_batch( $batch_page ) {
    $posts_per_page = 15;

    $args = [
        'post_type'      => 'faculty-admin',
        'posts_per_page' => $posts_per_page,
        'paged'          => $batch_page, 
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => 'faculty-department',
                'field'    => 'slug',
                'terms'    => 'som',
            ],
        ],
    ];

    $faculty_posts = get_posts( $args );
    
    if ( empty( $faculty_posts ) ) {
        echo "<div class='notice notice-success'><p><strong>Faculty Import Complete!</strong> All batches finished.</p></div>";
        echo "<p><a href='" . admin_url('tools.php?page=staff-import-tools') . "' class='button'>Return to Tools</a></p>";
        return;
    }

    $count = 0;
    $skipped = 0;

    foreach ( $faculty_posts as $f_post ) {
        
        $already_imported = get_posts([
            'post_type'  => 'managed-sgu-employee',
            'meta_key'   => '_original_faculty_id',
            'meta_value' => $f_post->ID,
            'fields'     => 'ids',
            'posts_per_page' => 1
        ]);

        if ( empty( $already_imported ) ) {
            
            $first_name = get_post_meta( $f_post->ID, 'faculty_staff_first_name', true );
            $middle_name = get_post_meta( $f_post->ID, 'faculty_staff_middle_name', true );
            $last_name  = get_post_meta( $f_post->ID, 'faculty_staff_last_name', true );
            $credentials = get_post_meta( $f_post->ID, 'faculty_staff_credentials', true );
            $email = get_post_meta( $f_post->ID, 'faculty_staff_email', true );
            $phone_office = get_post_meta( $f_post->ID, 'faculty_staff_phone_office', true );
            $phone_office_ext = get_post_meta( $f_post->ID, 'faculty_staff_phone_office_ext', true );
            $bio = get_post_meta( $f_post->ID, 'faculty_staff_bio', true );
            $banner_id = get_post_meta( $f_post->ID, 'faculty_banner_id', true );
            $featured_image_id = get_post_thumbnail_id( $f_post->ID );

            $dept_terms = wp_get_object_terms( $f_post->ID, 'faculty-department', ['fields' => 'slugs'] );
            $position_terms = wp_get_object_terms( $f_post->ID, 'position-type', ['fields' => 'ids'] );

            $full_name = $first_name . ( !empty($middle_name) ? ' ' . $middle_name : '' ) . ' ' . $last_name;
            $post_title = $full_name . ' (' . $email . ')';

            $new_employee_id = wp_insert_post([
                'post_title'   => $post_title,
                'post_content' => $f_post->post_content,
                'post_status'  => 'publish',
                'post_type'    => 'managed-sgu-employee',
                'post_name'    => $full_name,
            ]);

            if ( $new_employee_id && ! is_wp_error( $new_employee_id ) ) {
                update_post_meta( $new_employee_id, '_original_faculty_id', $f_post->ID );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_first_name', $first_name );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_middle_name', $middle_name );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_last_name', $last_name );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_credentials', $credentials );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_contact_email', $email );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_phone_number', $phone_office );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_phone_extension', $phone_office_ext );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_full_bio', $bio );
                update_post_meta( $new_employee_id, 'managed_sgu_employee_banner_id', $banner_id );

                if ( in_array( 'clinical-faculty', $dept_terms ) ) {
                    wp_set_object_terms( $new_employee_id, 'clinical-faculty', 'employee-category' );

                    $raw_position = get_post_meta( $f_post->ID, 'faculty_staff_position', true );
                    $faculty_position_text = trim( str_replace( ["\xe2\x80\x98", "\xe2\x80\x99", "’", "‘"], "'", $raw_position ) );

                    if ( ! empty( $faculty_position_text ) ) {
                        $titles_to_lookup = [];
                        switch ( $faculty_position_text ) {
                            case 'Alameda Health System Highland Campus': $titles_to_lookup = ['Alameda Health System - Highland Hospital']; break;
                            case 'Atlantic Health System -Morristown Medical Center & Atlantic Health System- Overlook Medical Center': $titles_to_lookup = ['Atlantic Health System - Morristown Medical Center', 'Atlantic Health System - Overlook Medical Center']; break;
                            case 'Berrycroft Community Health Centre (DME) and Stoke Mandeville Hospital (DME & CD)': $titles_to_lookup = ['Berrycroft Community Health Centre', 'Stoke Mandeville Hospital']; break;
                            case 'CHA Hollywood Presbyterian Medical Center (CD) and Mission Community Hospital (CD)': $titles_to_lookup = ['CHA Hollywood Presbyterian Medical Center', 'Mission Community Hospital']; break;
                            case 'CHA Hollywood Presbyterian Medical Center (CD), Los Angeles Downtown Medical Center (CD), Mission Community Hospital (CD) and PIH Health Good Samaritan Hospital (DME)': $titles_to_lookup = ['CHA Hollywood Presbyterian Medical Center', 'Los Angeles Downtown Medical Center', 'Mission Community Hospital', 'PIH Health Good Samaritan Hospital']; break;
                            case 'CHA Hollywood Presbyterian Medical Center (DME & CD), Los Angeles Downtown Medical Center (DME & CD) and Mission Community Hospital (DME & CD)': $titles_to_lookup = ['CHA Hollywood Presbyterian Medical Center', 'Los Angeles Downtown Medical Center', 'Mission Community Hospital']; break;
                            case 'Doctors Hospital Modesto': $titles_to_lookup = ['Doctors Medical Center of Modesto']; break;
                            case 'Lawrence House Group / Welbourne Health Centre (DME) and North Middlesex University Hospital (DME)': $titles_to_lookup = ['Lawrence House Group', 'Welbourne Health Centre', 'North Middlesex University Hospital']; break;
                            case 'Lawrence House Group/Welbourne Health Centre': $titles_to_lookup = ['Lawrence House Group', 'Welbourne Health Centre']; break;
                            case 'Long Island Community Hospital': $titles_to_lookup = ['NYU Langone Hospital - Suffolk']; break;
                            case 'Los Angeles Downtown Medical Center (LADMC)': $titles_to_lookup = ['Los Angeles Downtown Medical Center']; break;
                            case 'Mercy Health St. Vincent': $titles_to_lookup = ['Mercy Health - St. Vincent Medical Center']; break;
                            case 'Mercy St. Vincent Medical Center': $titles_to_lookup = ['Mercy Health - St. Vincent Medical Center']; break;
                            case 'Mission Community Hospital (CD) and PIH Health Whittier Hospital  (CD)': $titles_to_lookup = ['Mission Community Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'Montefiore New Rochelle': $titles_to_lookup = ['Montefiore New Rochelle Hospital']; break;
                            case "Nationwide Children's Hospital": $titles_to_lookup = ['Nationwide Children’s Hospital']; break;
                            case "Nationwide Children's Hospital (DME) and Mercy St. Vincent Medical Center (CD)": $titles_to_lookup = ['Nationwide Children’s Hospital', 'Mercy Health - St. Vincent Medical Center']; break;
                            case 'Norfolk & Norwich University Hospital (DME) and The Market Surgery (DME)': $titles_to_lookup = ['Norfolk &amp; Norwich University Hospital', 'The Market Surgery']; break;
                            case 'North Hampshire Hospital (CD) and Royal Hampshire County Hospital (CD)': $titles_to_lookup = ['North Hampshire Hospital', 'Royal Hampshire County Hospital']; break;
                            case 'Northwest Hospital Center': $titles_to_lookup = ['Northwest Hospital']; break;
                            case 'NYC Health & Hospitals/ Metropolitan': $titles_to_lookup = ['NYC Health + Hospitals | Metropolitan']; break;
                            case 'NYC Health + Hospitals | Elmhurst & Queens': $titles_to_lookup = ['NYC Health + Hospitals | Elmhurst', 'NYC Health + Hospitals | Queens']; break;
                            case 'NYC Health + Hospitals/ Elmhurst & Queens': $titles_to_lookup = ['NYC Health + Hospitals | Elmhurst', 'NYC Health + Hospitals | Queens']; break;
                            case 'NYC Health+Hospitals/Elmhurst': $titles_to_lookup = ['NYC Health + Hospitals | Elmhurst']; break;
                            case 'PIH Health Downey Hospital (CD) and PIH Health Whittier Hospital (CD)': $titles_to_lookup = ['PIH Health Downey Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'PIH Health Downey Hospital (DME) and PIH Health Whittier Hospital (DME)': $titles_to_lookup = ['PIH Health Downey Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'PIH Health Good Samaritan Hospital (CD) and  PIH Health Whittier Hospital (CD)': $titles_to_lookup = ['PIH Health Good Samaritan Hospital', 'PIH Health Whittier Hospital']; break;
                            case 'Poole  General Hospital / The Adam Practice': $titles_to_lookup = ['Poole General Hospital', 'The Adam Practice']; break;
                            case 'Poole General Hospital (DME & CD) and The Adam Practice (DME)': $titles_to_lookup = ['Poole General Hospital', 'The Adam Practice']; break;
                            case 'Rutgers/Jersey City Medical Center': $titles_to_lookup = ['Rutgers Health/Jersey City Medical Center']; break;
                            case 'Sheepcot Medical Center': $titles_to_lookup = ['Sheepcot Medical Centre']; break;
                            case 'Sheepcot Medical Center (DME) and Watford General Hospital (DME & CD)': $titles_to_lookup = ['Sheepcot Medical Centre', 'Watford General Hospital']; break;
                            case "St. Ann's Hospital - London": $titles_to_lookup = ['St. Ann’s Hospital – London']; break;
                            case "St. Ann's Hospital - Poole": $titles_to_lookup = ['St. Ann’s Hospital – Poole']; break;
                            case "St. George's General Hospital": $titles_to_lookup = ['St. George’s General Hospital']; break;
                            case "St. Joseph's Hospital Health Center": $titles_to_lookup = ['St. Joseph’s Hospital Health Center']; break;
                            case "St. Joseph's University Medical Center": $titles_to_lookup = ['St. Joseph’s University Medical Center']; break;
                            case "St. Mary's Hospital - Connecticut": $titles_to_lookup = ['St. Mary’s Hospital - Connecticut']; break;
                            case "St. Mary's General Hospital/Saint Clare's Hospital": $titles_to_lookup = ['St. Mary’s Hospital - New Jersey', 'Saint Clare’s Denville Hospital']; break;
                            case "St. Michael's Medical Center": $titles_to_lookup = ['St. Michael’s Medical Center']; break;
                            default: $titles_to_lookup = [$faculty_position_text]; break;
                        }

                        $h_row_index = 0;
                        foreach ( $titles_to_lookup as $lookup_title ) {
                            $hospital_post = get_page_by_title( $lookup_title, OBJECT, 'affiliated-hospital' );
                            if ( $hospital_post ) {
                                update_post_meta( $new_employee_id, 'managed_sgu_employee_hospitals_' . $h_row_index . '_managed_sgu_employee_hospital', $hospital_post->ID );
                                update_post_meta( $new_employee_id, '_managed_sgu_employee_hospitals_' . $h_row_index . '_managed_sgu_employee_hospital', 'field_6996a44a74a6c' );
                                $h_row_index++;
                            }
                        }
                        if ( $h_row_index > 0 ) {
                            update_post_meta( $new_employee_id, 'managed_sgu_employee_hospitals', $h_row_index );
                            update_post_meta( $new_employee_id, '_managed_sgu_employee_hospitals', 'field_6996a43f74a6b' );
                        }
                    }
                } else {
                    wp_set_object_terms( $new_employee_id, 'faculty', 'employee-category' );
                }

                wp_set_object_terms( $new_employee_id, $dept_terms, 'faculty-department' );

                if ( $featured_image_id ) {
                    update_post_meta( $new_employee_id, 'managed_sgu_employee_headshot_image', $featured_image_id );
                    set_post_thumbnail( $new_employee_id, $featured_image_id );
                }

                if ( ! empty( $position_terms ) && ! is_wp_error( $position_terms ) ) {
                    $row_count = 0;
                    foreach ( $position_terms as $term_id ) {
                        update_post_meta( $new_employee_id, 'managed_sgu_employee_postions_' . $row_count . '_managed_sgu_employee_postion', $term_id );
                        update_post_meta( $new_employee_id, '_managed_sgu_employee_postions_' . $row_count . '_managed_sgu_employee_postion', 'field_6996a2006b97a' );
                        $row_count++;
                    }
                    update_post_meta( $new_employee_id, 'managed_sgu_employee_postions', $row_count );
                    update_post_meta( $new_employee_id, '_managed_sgu_employee_postions', 'field_6996a1ea6b979' );
                }
                $count++;
            }
        } else {
            $skipped++;
        }
    }
    
    $next_batch = $batch_page + 1;
    echo "<p>Batch $batch_page processed ($count created, $skipped skipped).</p>";
    echo "<p><strong>Moving to batch $next_batch...</strong></p>";
    $next_url = admin_url( "tools.php?page=staff-import-tools&import_action=faculty&batch=$next_batch" );
    echo "<script>setTimeout(function(){ window.location.href = '$next_url'; }, 1000);</script>";
}