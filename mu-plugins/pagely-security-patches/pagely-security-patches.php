<?php
/*
Plugin Name: Pagely Security HotFixes
Plugin URI: https://pagely.com
Description: Maintains WP Core functionality hotfixes for security issues.
Version: 0.3
Author: Pagely
Author URI: https://pagely.com
License: GPL
*/

if (!defined('SKIP_ALL_HOTFIX')) {

  /******
  Hotfix to santize thumbnail metadata
  https://blog.ripstech.com/2018/wordpress-file-delete-to-code-execution/
  ******/
  if (!defined('SKIP_UNLINK_HOTFIX')) {
    add_filter( 'wp_update_attachment_metadata', 'pagely_unlink_hotfix' );

    function pagely_unlink_hotfix( $data ) {
      if( isset($data['thumb']) ) {
          $data['thumb'] = basename($data['thumb']);
      }
      return $data;
    }
  }
}


// Remove Support user administrator role after 8 hours
function pagely_remove_stale_support_admin() {

    if(defined('PAGELY_SUPPORT_ADMIN') && PAGELY_SUPPORT_ADMIN === true)
        return;

    $email      = 'support@pagely.com';
    $time_limit = time() - (HOUR_IN_SECONDS * 8);
    $ps_user    = get_user_by('email', $email);

    if ($ps_user && user_can($ps_user, 'manage_options')) {

    	$session_tokens = get_user_meta($ps_user->ID, 'session_tokens', 1);

        if (!is_array($session_tokens))
            return;

    	if (!empty( $session_tokens)) {
    	        $session_logins = array_map(
            	        function ($session_id) {
                    	        return $session_id['login'];
    	                }, $session_tokens);
    	}

        if ( (isset($session_logins)) && (max($session_logins) < $time_limit) ) {
                error_log("Deactivating a stale Pagely admin");
                WP_Session_Tokens::get_instance($ps_user->ID)->destroy_all();
                $ps_user->remove_role('administrator');
        }
    }
}
add_action('admin_init', 'pagely_remove_stale_support_admin');
