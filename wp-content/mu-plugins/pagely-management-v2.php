<?php
/*
Plugin Name: Pagely Management V2
Plugin URI: https://pagely.com/
Description: Pagely Management Plugins
Version: 2.0.66
Author: Pagely
Author URI: https://pagely.com
*/
require_once __DIR__.'/pagely-util/pagely-util.php';
require_once __DIR__.'/pagely-assets/pagely-assets.php';
require_once __DIR__.'/pagely-cdn/pagely-cdn.php';
require_once __DIR__.'/pagely-cache-purge/pagely-cache-purge.php';
require_once __DIR__.'/pagely-app-stats/pagely-app-stats.php';
require_once __DIR__.'/pagely-cache-control/pagely-cache-control.php';
require_once __DIR__.'/pagely-status/pagely-status.php';
require_once __DIR__.'/pagely-wp-cli-mail-patch/pagely-wp-cli-mail-patch.php';
require_once __DIR__.'/pagely-cli/pagely-cli.php';
require_once __DIR__.'/pagely-security-patches/pagely-security-patches.php';
require_once __DIR__.'/pagely-plugin-upgrade-hooks/pagely-plugin-upgrade-hooks.php';
require_once __DIR__.'/pagely-site-health/pagely-site-health.php';
require_once __DIR__.'/pagely-automatic-plugin-updates/pagely-automatic-plugin-updates.php';
require_once __DIR__.'/pagely-security-hooks/pagely-security-hooks.php';
