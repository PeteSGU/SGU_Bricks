<?php
/**
 * Plugin Name: SGU Mapbox Embedder
 * Description: Embeds custom Mapbox projects using a shortcode. Easily pull different maps by specifying the folder name.
 * Version: 1.3
 * Author: Your Name
 */

// Exit if accessed directly for security
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sgu_mapbox_render_shortcode( $atts ) {
    // Set up our shortcode variables (attributes) and their defaults
    $atts = shortcode_atts(
        array(
            'folder' => 'main-map', // Default folder name
            'height' => '800px',    // Default height
            'width'  => '100%'      // Default width
        ), 
        $atts, 
        'sgu_map'
    );

    // Get the exact protocol and domain the user is currently visiting
    $protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) ? 'https://' : 'http://';
    $current_host = $_SERVER['HTTP_HOST']; 

    // Manually build the exact string to bypass CDN upload directory filters
    $iframe_path = '/wp-content/uploads/sgu-maps/' . $atts['folder'] . '/index.html';
    $iframe_url  = esc_url( $protocol . $current_host . $iframe_path );

    // Create a safe CSS class based on the folder name
    $custom_class = sanitize_html_class( $atts['folder'] );

    // Build the iframe HTML (Notice the new class attribute and %4$s variable)
    $output = sprintf(
        '<iframe src="%1$s" class="sgu-map-iframe %4$s" width="%2$s" height="%3$s" style="border:none; max-width: 100%%;" title="Interactive Map"></iframe>',
        $iframe_url,
        esc_attr( $atts['width'] ),
        esc_attr( $atts['height'] ),
        $custom_class
    );

    return $output;
}

// Register the shortcode [sgu_map]
add_shortcode( 'sgu_map', 'sgu_mapbox_render_shortcode' );