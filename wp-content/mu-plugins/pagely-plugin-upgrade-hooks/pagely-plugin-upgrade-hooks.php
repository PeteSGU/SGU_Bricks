<?php

// Do things after a plugin updates or installs

function pagely_post_plugin_actions($response, $hook_extra, $result) {
    if (is_array($result) && isset($result["destination_name"]) && strpos($result["destination_name"], "wordpress-seo") !== false) {
        if (function_exists("wp_cache_flush")) {
            error_log("[pagely-plugin-upgrade-hooks] Flushing WP cache as wordpress-seo was detected");
            wp_cache_flush();
        }
    }
    $jsonFile = false;
    foreach(['/info.json', ABSPATH.'/../info.json', ABSPATH.'/../../../info.json'] as $path) {
        if (file_exists($path)) {
            $jsonFile = $path;
            break;
        }
    }
    if ($jsonFile === false) {
        error_log("[pagely-plugin-upgrade-hooks] - Can't find info.json!");
        return false;
    }
    $jsonPayload = json_decode(file_get_contents($jsonFile));
    $ch = curl_init("http://127.0.0.1:90/wp-content/mu-plugins/pagely-status/opcache.php");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: " . $jsonPayload->domain));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    // Curl post parameters
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "PAGELY_RESET_OPCACHE=" . $jsonPayload->apiKey);
    $result = curl_exec($ch);
    $response_header = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
	
    error_log("[pagely-plugin-upgrade-hooks] - OPcache flush, Header Response: " . $response_header);
    error_log("[pagely-plugin-upgrade-hooks] - Body: " . $result);
}

add_filter( 'upgrader_post_install', 'pagely_post_plugin_actions', 20, 3 );

