<?php
/*
Plugin Name: Pagely-Utils
Plugin URI: https://pagely.com
Description: Utility code that runs without a UI
Author: Joshua Eichorn
Author URI: https://pagely.com
Version: 0.1
*/


// We set a error_log path in php-fpm so if you want to see your debug log
// in wp-content, it has to be a symlink
$log = ABSPATH.'/wp-content/debug.log';
if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG == true)
{
    // If the file exists and is not a symlink, rename it
    if (file_exists($log) && !is_link($log)) {
        rename($log, "$log.1");
    }

    // Hacky fix for deep webroots, find the /mnt/log dir up to 5 dirs up
    $mntLogPathRelativeToAbs = "../../mnt/log/";
    for ($i = 1; $i <= 5; $i++) {
        $testMntLogPath = sprintf("%s/%s/mnt/log", ABSPATH, str_repeat("../", $i));
        if (is_dir($testMntLogPath)) {
            $mntLogPathRelativeToAbs = str_repeat("../", $i) . "mnt/log/";
            break;
        }
    }

    $currentLinkDest = false;
    // The file can exist as a link but be broken (in which case file_exists returns false)
    if (is_link($log)) {
        $currentLinkDest = readlink($log);
    }

    // Try and find the right app ID, whether the log file exists yet or not
    // This should match dirs inside docker, the host system, and in deep webroots
    $targetFile = null;
    if (preg_match("@/.*?dom([0-9]+)($|/.*)$@", ABSPATH, $domDirMatches)) {
        $targetFile = sprintf("../%s%s-php.error.log", $mntLogPathRelativeToAbs, $domDirMatches[1]);
    }

    // As a fallback, look for any log file (this is the old logic)
    if ($targetFile === null) {
        $tmp = glob(ABSPATH . "/" . $mntLogPathRelativeToAbs . "*-php.error.log");
        if (isset($tmp[0])) {
            $file = basename($tmp[0]);
            $targetFile = sprintf("../%s%s-php.error.log", $mntLogPathRelativeToAbs, $file);
        }
    }

    if ($targetFile !== null) {
        // If the current link target is not false and not equal to the expected file, then we need to unlink it
        if ($currentLinkDest !== false && $currentLinkDest != $targetFile) {
            unlink($log);
        }

        if (!is_link($log)) {
            // Create the symlink
            symlink($targetFile, $log);
        }
    }
}
else if (is_link($log))
{
    unlink($log);
}

// add a header to reset login rate limiting on successful login
function pagely_add_ratelimit_reset_header()
{
    header('X-Pagely-Ratelimit-Reset: login');
}
add_action('wp_login', 'pagely_add_ratelimit_reset_header');

// Default settings
// there is a good chance we aren't in a chroot if VARNISH_SERVERS isn't defined, so lets try
// to be clever in that case
if (!defined('VARNISH_SERVERS') && file_exists('/srv/pagely/conf/pool_configs.php'))
	include '/srv/pagely/conf/pool_configs.php';

if( ! defined('DISABLE_WP_CRON') ) {
    // Disable WP Cron unless WooCommerce Plugin installer is "health checking" the site
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/wccom-site/v1/installer') === false && ($_GET['rest_route'] ?? '') != '/wccom-site/v1/installer') {
        define('DISABLE_WP_CRON', true);
    }
}

if( ! defined('AUTOSAVE_INTERVAL') )
	define( 'AUTOSAVE_INTERVAL', 300 ); // Seconds

if( ! defined('WP_CRON_LOCK_TIMEOUT') )
	define( 'WP_CRON_LOCK_TIMEOUT', 120 );

if( ! defined('AUTOMATIC_UPDATER_DISABLED') )
   define( 'AUTOMATIC_UPDATER_DISABLED', true);

if (! defined('WP_AUTO_UPDATE_CORE') )
   define('WP_AUTO_UPDATE_CORE', false);

if( ! defined('VARNISH_SERVERS') )
   define( 'VARNISH_SERVERS', '127.0.0.1');

if( ! defined('PMEMCACHED_SERVERS') )
   define( 'PMEMCACHED_SERVERS', '127.0.0.1:11211');


// backwards compat for p3 and p10
if ( isset($_SERVER['HTTP_X_PAGELY_SSL']) && 'on' == strtolower( $_SERVER['HTTP_X_PAGELY_SSL'] ) ) {
	$_SERVER['HTTPS'] = 'on';
}
