<?php
/*
Plugin Name: Pagely Site Health
Plugin URI: https://pagely.com
Description: Fixes for managed WordPress core warnings in site health
Version: 0.3.0
Author: Pagely
Author URI: https://pagely.com
License: GPL
*/

// Suppress health checks that aren't relevant to Pagely users bc they're things we manage
function pagely_remove_background_update_check( $tests ) {
    unset( $tests['async']['background_updates'] );
    unset( $tests['direct']['sql_server'] );
    return $tests;
}
add_filter( 'site_status_tests', 'pagely_remove_background_update_check' );


// WP 6.1 adds cache header checks, make sure Pagely is supported
add_filter( 'site_status_page_cache_supported_cache_headers', function( $cache_headers ) {
    $cache_headers['x-gateway-cache-status'] = static function ( $header_value ) {
        return false !== strpos( strtolower( $header_value ), 'hit' );
    };
    return $cache_headers;
});


// Suppress cache test for direct host names (where caching in unavailable by design)
function pagely_remove_caching_check( $tests ) {
    unset( $tests['async']['page_cache'] );
    return $tests;
}
if ( strpos( @$_SERVER['HTTP_HOST'], '.sites.pressdns.com' ) !== false ) {
    add_filter( 'site_status_tests', 'pagely_remove_caching_check' );
}


// Direct Pagely customers to our docs for common questions/prompts from core
add_filter( 'site_status_persistent_object_cache_notes', function( $notes ) {
    $notes = __( 'Pagely customers can take advantage of Object Cache Pro. For more information, and instructions on how to enable Object Cache Pro on your site, see the link below.' );
    return $notes;
} );
add_filter( 'site_status_persistent_object_cache_url', function() {
    return 'https://support.pagely.com/hc/en-us/articles/360038613312-How-to-Use-Redis-Object-Caching-in-WordPress';
});

add_filter( 'wp_update_php_url', function( $update_url ) {
    return 'https://support.pagely.com/hc/en-us/articles/115000013092-Changing-PHP-Versions';
});

add_filter( 'wp_update_https_url', function( $update_url ) {
    return 'https://support.pagely.com/hc/en-us/articles/360043846911-Changing-Your-TLS-Version';
});
