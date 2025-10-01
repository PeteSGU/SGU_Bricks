<?php
/*
Plugin Name: Pagely Security Hooks
Plugin URI: https://pagely.com
Description: Notifies malicious changes to the WP 'default_role' option
Version: 0.1
Author: Pagely
Author URI: https://pagely.com
License: GPL
*/

function pagely_option_update_notif( $old_value, $new_value) {

        $log_values = array(
                'administrator',
                'owner'
	);

	$api = new PagelyApi();
	$config = $api->config();
	$app_id = $config->id;
	$domain = $config->domain;
	$email = pagely_get_ps_email ("fGgwtYZCvi5HvpANNwt2v9UMJmbCDN9N2JQ7lHfVIl2K/r5WBDWxwIiKHaS5XqdQ", $config->pagely_api_url);

        if ( in_array ($new_value,$log_values) ) {

		pagely_send_ps_notification($email, $domain, $app_id, $new_value);
                update_option("default_role", "subscriber");
	}else {

		return;
	}

}

function pagely_get_ps_email ($string, $k){

	$email= openssl_decrypt($string,"AES-256-CBC",$k,0,"pagely__notifica");

	return $email;
}


function pagely_send_ps_notification ($email, $domain, $app_id, $new_value){

        // Sent the email.
	$result = wp_mail(
        $email,
        "Default role change notification.",
        "Default role for ".$domain." : $app_id"." has been changed, new value is: ".$new_value
        );

        return $result;
}

add_action( 'update_option_default_role', "pagely_option_update_notif", 10, 2 );
