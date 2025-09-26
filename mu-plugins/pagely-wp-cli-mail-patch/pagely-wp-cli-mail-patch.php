<?php

/*
Plugin Name: Pagely Email Patch
Plugin URI: https://pagely.com
Description: Override email From: if not set elsewhere
Author: Pagely
Author URI: https://pagely.com
Version: 0.1
*/

add_filter( 'wp_mail_from', function($email) {
    if ($email == 'wordpress@') {
        return get_option( 'admin_email' );
    } return $email;
}, 99);
