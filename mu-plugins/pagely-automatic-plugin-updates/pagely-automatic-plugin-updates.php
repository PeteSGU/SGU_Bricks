<?php
/*
 * Plugin Name: Pagely Automatic Plugin Updates
 * Plugin URI: https://pagely.com
 * Description: Allows automatic updates of plugins via WP Core rather than maid in certain circumstances
 * Version: 0.1
 * Author: Pagely
 * Author URI: https://pagely.com
 * License: GPL
 */

if (getenv("PAGELY_ALLOW_PLUGIN_AUTOMATIC_UPDATES") === "1") {
    $autoUpdatePriority = defined("PAGELY_AUTOMATIC_PLUGIN_UPDATE_FILTER_PRIORITY") ? PAGELY_AUTOMATIC_PLUGIN_UPDATE_FILTER_PRIORITY : PHP_INT_MIN;
    add_filter( 'plugins_auto_update_enabled', '__return_true', $autoUpdatePriority );
    if (!defined('PAGELY_AUTO_PLUGIN_UPDATE_TOGGLE') || PAGELY_AUTO_PLUGIN_UPDATE_TOGGLE !== true) {
        add_filter('auto_update_plugin', '__return_true', $autoUpdatePriority);
    }
}
