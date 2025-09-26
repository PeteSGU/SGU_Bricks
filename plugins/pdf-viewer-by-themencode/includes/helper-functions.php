<?php

/**
 * @Author: Abdul Awal
 * @Date:   2021-05-11 00:04:08
 * @Last Modified by:   Abdul Awal
 * @Last Modified time: 2021-05-14 10:04:30
 */

if( ! function_exists( 'tnc_mail_to_friend') ){
	add_action("wp_ajax_tnc_mail_to_friend", "tnc_mail_to_friend");
	add_action("wp_ajax_nopriv_tnc_mail_to_friend", "tnc_mail_to_friend");

	function tnc_mail_to_friend() {

	   if ( !wp_verify_nonce( $_POST['nonce'], "tnc_mail_to_friend_nonce")) {
	      exit("Invalid Request");
	   }

		$uname 		= sanitize_text_field($_POST["yourname"]); 
		$fname 		= sanitize_text_field($_POST["friendsname"]);
		$sname 		= $_SERVER['SERVER_NAME'];
		$uemail 	= sanitize_email($_POST["youremailaddress"]); 
		$femail 	= sanitize_email($_POST["friendsemailaddress"]);
		$message 	= nl2br(sanitize_textarea_field($_POST["message"])); 
		$link 		= $share_url;
		$to 		= $femail;
		$subject 	= sanitize_text_field($_POST['email_subject']);
		$headers 	= "MIME-Version: 1.0" . "\r\n";
		$headers 	.= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers 	.= 'From: '.$uname.' <webmaster@'.$sname.'>' . "\r\n";
		$headers 	.= 'Reply-To:' . $uemail . "\r\n";
		$sendmail 	= mail($to,$subject,$message,$headers);

		if($sendmail){
			$result['type'] = "success";
		} else {
			$result['type'] = "error";
		}

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	      $result = json_encode($result);
	      echo $result;
	   }

	   die();
	}

}


if( ! function_exists( 'tnc_pvfw_create_viewer_url_callback' )){
	function tnc_pvfw_create_viewer_url_callback(){
		echo sprintf( esc_html__( 'Please create TNC PDF viewers using %s (PDF Viewer > Add New) before creating a shortcode.', 'pdf-viewer-by-themencode' ) , '<a href="'.admin_url( '/post-new.php?post_type=pdfviewer', $scheme = 'admin' ).'">this link</a>') ;
	}
}


if( ! function_exists( 'tnc_num_to_text' )){
	// convert 0 or 1 to Show or Hide.
	function tnc_num_to_text($value){
		if($value == '1' || $value == "true"){
			return "Show";
		} else {
			return "Hide";
		}
	}
}

if( ! function_exists( 'tnc_pvfw_viewer_generate_file_array' ) ){
	/**
	 * Take requested file url and return array with all the required fields to verify if the user has access.
	 * @param  $get_requested_file the requested file
	 * @return array 
	 */
	function tnc_pvfw_viewer_generate_file_array( $get_requested_file ){

		global $wpdb;

		$posts_table = $wpdb->prefix . 'posts';
		
		$uploadDir      = wp_upload_dir();
		$full_url       = $uploadDir['baseurl'] . $get_requested_file;
		$full_path      = $uploadDir['basedir'] . $get_requested_file;
		$fileInfo       = pathinfo($full_path);
		$isResizedImage   = false;

		$file_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$posts_table} WHERE guid = %s ", $full_url ) );

		if (empty($file_id)) {

			// Convert resized thumb url's to main file url
			$query_url = preg_replace("/(-\d+x\d+)/", "", $full_url);

			$file_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$posts_table} WHERE guid = %s ", $query_url ) );

			if ($file_id) {
			  $isResizedImage = true;
			}
		}

		$file_array = array(
			'id'                => $file_id, 
			'file_url'          => $full_url, 
			'file_path'         => $full_path,
			'is_resized_image'  => $isResizedImage
		);

		return $file_array;
	}
}


/* Filter the single_template with our custom function*/
add_filter('single_template', 'tnc_pvfw_single_pdf_viewer_template');

if( ! function_exists( 'tnc_pvfw_single_pdf_viewer_template' )){
	/**
	 * [tnc_pvfw_single_pdf_viewer_template description]
	 *
	 * @param  [type] $single [description].
	 * @return string template
	 */
	function tnc_pvfw_single_pdf_viewer_template($single) {

	    global $post;

	    if ( $post->post_type == 'pdfviewer' ) {
	    	$viewer_template_file = dirname(__FILE__) .'/../tnc-pdf-viewer-single.php';
	        if ( file_exists( $viewer_template_file ) ) {
	            return $viewer_template_file;
	        }
	    }

	    return $single;

	}
}


/* 
* Add a place for display message for buying premium version and leave rating in the admin area.
*/

if ( ! function_exists( 'themencode_pvfw_leave_rating_in_admin_area' ) ) {
	function themencode_pvfw_leave_rating_in_admin_area() {
		global $pagenow, $post_type;
		$admin_pages = [ 'edit.php', 'post.php', 'post-new.php' ];

		if( in_array( $pagenow, $admin_pages ) && $post_type == 'pdfviewer' ) {
			?>
				<div class="notice notice-info is-dismissible" style="margin-bottom: 5px;">
					<p>
						<?php _e( 'Check out the #TNcFlipbook at <a style="text-decoration: none; font-weight: bold;" target="_blank" href="https://1.envato.market/1YD7g">CodeCanyon</a> || Enjoying TNC PDF viewer? It\'s time to <a style="text-decoration: none; font-weight: bold;" target="_blank" href="https://wordpress.org/plugins/pdf-viewer-by-themencode/">Let others know</a> by leaving a review.', 'pdf-viewer-by-themencode' ); ?>
					</p>					
				</div>
			<?php
		}
	}
	add_action( 'admin_notices', 'themencode_pvfw_leave_rating_in_admin_area', 1 );
}


/* 
* Add a place for display advertise in admin panel
*/
if ( ! function_exists( 'tnc_pvfw_advertisement_update' ) ) {
    function tnc_pvfw_advertisement_update() {
        global $pagenow;
        $admin_pages = [ 'edit.php', 'post-new.php' ];

        // Check if $_GET['post_type'] is set
        $post_type_pdfviewer = isset($_GET['post_type']) ? $_GET['post_type'] : '';

        if( in_array( $pagenow, $admin_pages ) && ( !empty($post_type_pdfviewer) && $post_type_pdfviewer == 'pdfviewer' ) ) {

            $advertisement = get_transient( 'themencode-pvfw-lite-promo' );
            if( empty( $advertisement ) ) {
                // Handle wp_remote_get errors
                $get_advertisement = wp_remote_get( 'https://updates.themencode.com/pvfw/promo.php', array(
                    'sslverify' => false
                ) );

                // Check if the response is a WP_Error
                if( is_wp_error( $get_advertisement ) ) {
                    error_log( $get_advertisement->get_error_message() );
                    return;
                }

                if( isset($get_advertisement['body']) && !empty($get_advertisement['body']) ) {
                    set_transient( 'themencode-pvfw-lite-promo', $get_advertisement['body'], 86400 );
                    $advertisement = $get_advertisement['body'];
                } else {
                    error_log( 'Invalid response body from the advertisement server.' );
                    return;
                }
            }

            echo wp_kses_post( $advertisement );
        }
    }
    add_action( 'admin_notices', 'tnc_pvfw_advertisement_update' );
}



// Active Pro addon 
function tnc_pvfw_lt_is_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
}

if ( ! ( tnc_pvfw_lt_is_plugin_active( 'pvfw-vc-addon/pdf-viewer-for-wordpress-visual-composer-addon.php' ) || tnc_pvfw_lt_is_plugin_active( 'divi-pdf-viewer-for-wordpress/divi-pdf-viewer-for-wordpress.php' ) || tnc_pvfw_lt_is_plugin_active( 'display-pdf-viewer-for-wordpress-addon/display-pdf-viewer-for-wordpress-addon.php' ) || tnc_pvfw_lt_is_plugin_active( 'avada-pdf-viewer-for-wordpress/avada-pdf-viewer-for-wordpress.php' ) || tnc_pvfw_lt_is_plugin_active( 'preview-pvfw-addon/preview-pvfw-addon.php' ) || tnc_pvfw_lt_is_plugin_active( 'elementor-pdf-viewer-for-wordpress/elementor-pdf-viewer-for-wordpress-addon.php' )  || tnc_pvfw_lt_is_plugin_active( 'Navigative/navigative.php' ) ) ) {
	// Check the current user's pdfviewer post count
	function tnc_pvfw_get_pdfviewer_post_count() {
		$current_user = wp_get_current_user();
		$args = array(
			'post_type' => 'pdfviewer',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);
		$pdfviewer_posts = new WP_Query($args);

		return $pdfviewer_posts->found_posts;
	}

	add_action( 'transition_post_status', 'tnc_pvfw_restrict_viewer_count', 10, 3 );


	function tnc_pvfw_restrict_viewer_count($new_status, $old_status, $post) {
		if ($new_status === 'publish' && $post->post_type === 'pdfviewer' && tnc_pvfw_get_pdfviewer_post_count() >= 5) {
			
			if( $old_status === 'publish' ){
				return;
			}
			wp_delete_post($post->ID, true);

			$error_msg = esc_html__('TNC FlipBook - PDF viewer for WordPress (Pro Version) is required to publish more than 5 viewers. ', 'pdf-viewer-for-wordpress');
			$error_msg .= sprintf(
				wp_kses(__('Pro version can be purchased from <a href="%s" target="_blank">https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815</a>.', 'pdf-viewer-for-wordpress'),
					array(
						'a' => array(
							'href' => array(),
							'target' => array()
						)
					)
				),
				esc_url('https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815')
			);
			wp_die($error_msg);
		}
	}

	// Display an admin notice on the 'Add New' page for the 'pdfviewer' post type
	function tnc_pvfw_upgrade_notice() {
		global $pagenow, $post_type;

		if ($pagenow == 'post-new.php' && $post_type == 'pdfviewer' && tnc_pvfw_get_pdfviewer_post_count() >= 5) {
			$upgrade_msg = '<strong>You\'ve reached maximum Viewer Creation Limit!</strong> <br />';
			$upgrade_msg .= esc_html__('To add more PDF Viewers, please upgrade to the Pro version, TNC FlipBook - PDF viewer for WordPress. ', 'pdf-viewer-for-wordpress');
			$upgrade_msg .= sprintf(
				wp_kses(__('You can purchase the Pro version from <a href="%s" target="_blank">https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815</a>.', 'pdf-viewer-for-wordpress'),
					array(
						'a' => array(
							'href' => array(),
							'target' => array()
						)
					)
				),
				esc_url('https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815')
			);
			printf('<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $upgrade_msg);
		}
	}
	add_action('admin_notices', 'tnc_pvfw_upgrade_notice');

} 



// Display upgrate betabox in admin panel
function tnc_pvfw_upgrade_metabox() {
	$TncPvfwCustomMetaboxObj = new TncPvfwCustomMetabox();
}
add_action('plugins_loaded', 'tnc_pvfw_upgrade_metabox');

/**
 * Promo Temporary
 */

 function tnc_pvfw_lite_enqueue_admin_notice_script() {
    // Get the URL of the plugin directory
    $plugin_url = plugin_dir_url( dirname(__FILE__) );

    // Append the path to your JS file
    $js_url = $plugin_url . 'tnc-resources/admin-notice.js';

    wp_enqueue_script('tnc-pvfw-lite-admin-notice-script', $js_url, array('jquery'), null, true);
    wp_localize_script('tnc-pvfw-lite-admin-notice-script', 'tncPvfwLiteAdminNotice', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('admin_enqueue_scripts', 'tnc_pvfw_lite_enqueue_admin_notice_script');


// Function to display the admin notice
function tnc_pvfw_lite_display_black_friday_notice() {
    if (get_transient('tnc_pvfw_lite_black_friday_notice_dismissed')) {
        return;
    }

    $currentDate = new DateTime();
    $startDate = new DateTime('2024-11-20');
    $endDate = new DateTime('2024-11-30');

    if ($currentDate >= $startDate && $currentDate <= $endDate) {
        echo '<div class="notice notice-info is-dismissible" id="tnc-pvfw-lite-black-friday-notice">
            <p>🌟 <strong>Cyber Week Special:</strong> Get 40% Off on TNC FlipBook & all addons! Limited Time Offer – Act Fast and Save Big! <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815/" class="button button-primary" style="margin-left: 10px;" target="_blank">Get TNC FlipBook</a> <a target="_blank" href="https://themencode.com/deals/" class="button button-secondary" style="margin-left: 10px;">Addon Deals</a></p>
        </div>';
    }
}
add_action('admin_notices', 'tnc_pvfw_lite_display_black_friday_notice');

// AJAX handler for dismissal
function tnc_pvfw_lite_dismiss_black_friday_notice() {
    set_transient('tnc_pvfw_lite_black_friday_notice_dismissed', true, DAY_IN_SECONDS * 365);
    wp_die();
}
add_action('wp_ajax_tnc_pvfw_lite_dismiss_black_friday_notice', 'tnc_pvfw_lite_dismiss_black_friday_notice');
