<?php
/*
Plugin Name: Pagely Cache Purge
Plugin URI: 
Description: Purges Pagely Cache Nodes Servers, supports Extra purge destinations using constants
Author: Pagely
Author URI: http://pagely.com
Version: 1.0
*/

require_once __DIR__.'/../pagely-util/pagely-root-dir.php';

// disable the hooks in old Pagely Management we are taking over the 
// event based purging
if (!defined('PAGELY_DISABLE_VARNISH_HOOKS'))
    define('PAGELY_DISABLE_VARNISH_HOOKS', true);

// CACHE PURGE
if( ! defined('F_PAGELY_CACHE_PURGE') )
   define( 'F_PAGELY_CACHE_PURGE', TRUE); 

if ( ! defined('PAGELY_CACHE_PURGE_IMAGES') )
    define('PAGELY_CACHE_PURGE_IMAGES', false);

// clear archive page parents when a new post was added or updated
if( ! defined('PAGELY_CLEAR_ARCHIVE_PAGE') )
   define( 'PAGELY_CLEAR_ARCHIVE_PAGE', true);

class PagelyCachePurge
{
    public $log = true;
    public $debug = false;
    public $debugMsgs = array();
    public $noActionsAfterPurge = false;

    public $baseUrls = array();
    public $servers = array();
    protected $deferred = false;
    protected $purgeErrors = array();
    protected $shutdownHook = false;
    public static $instance = null;

    public static function init()
    {
        if (empty(self::$instance))
            self::$instance = new PagelyCachePurge();

    }

    public function __construct()
    {
        if (!defined('WP_CLI')) {
            if (!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['post'])) {
                $this->deferred = true;
            }
        }

        if (defined('PAGELY_CACHE_PURGE_URL_OVERRIDE'))
        {
            $this->baseUrls = [PAGELY_CACHE_PURGE_URL_OVERRIDE];
        }
        else
        {
            $this->baseUrls = array(home_url());
            if (is_multisite()) {
                $this->baseUrls[] = network_home_url();
            }
        }

        if (defined('WP_HOME'))
        {
            $this->baseUrls[] = WP_HOME;
        }

        global $PAGELY_CACHE_PURGE_BASE_URLS;

        if (!empty($PAGELY_CACHE_PURGE_BASE_URLS)
            && is_array($PAGELY_CACHE_PURGE_BASE_URLS)
        ) {
            foreach ($PAGELY_CACHE_PURGE_BASE_URLS as $extraBaseUrl) {
                $this->baseUrls[] = $extraBaseUrl;
            }
        }

        if (defined('VARNISH_SERVERS'))
        {
            $tmp = explode(',', VARNISH_SERVERS);
            foreach($tmp as $server)
            {
                $parts = explode(':', $server);
                $this->servers[$parts[0]] = $parts[1] ?? 80;
            }
        }

        // Add filter for baseUrls modifications
        $this->baseUrls = apply_filters('pagely_cache_purge_base_urls', $this->baseUrls);

        if (count($this->baseUrls) > 0 && count($this->servers) > 0)
        {
            $this->registerHooks();
        }
    }

    public function registerHooks()
    {
        // editing nav creates a huge # of edit events so just puge all at the end
        if (($_SERVER['SCRIPT_NAME'] ?? '') == '/wp-admin/nav-menus.php')
        {
            if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST')
            {
                add_action("shutdown", array($this, "purgeAll"));
                return;
            }
        }

        // When posts/pages are published, edited or deleted
        add_action('save_post', array($this, 'purgePost'), 99, 3);

        // When attachments are added/updated
        if (PAGELY_CACHE_PURGE_IMAGES)
        {
            add_action('edit_attachment', array($this, 'purgeAttachment'), 99);
            add_action('add_attachment', array($this, 'purgeAttachment'), 99);
        }

        // When comments are made, edited or deleted
        add_action('comment_post', array($this, 'purgeComment'),99);
        add_action('edit_comment', array($this, 'purgeComment'),99);
        add_action('trashed_comment', array($this, 'purgeComment'),99);
        add_action('untrashed_comment', array($this, 'purgeComment'),99);
        add_action('deleted_comment', array($this, 'purgeComment'),99);
        add_action('wp_set_comment_status', array($this, 'purgeComment'),99);

        // When posts or pages are trashed or deleted
        add_action('deleted_post', array($this, 'purgePost'), 99);
        add_action('deleted_post', array($this, 'purgeCommon'), 99);
        add_action('wp_trash_post', array($this, 'purgePost'), 99);
        add_action('wp_trash_post', array($this, 'purgeCommon'), 99);
        
        // purge actions
        add_filter( 'pagely_purge_all', array($this, 'purgeAll'),10 );
        add_filter( 'pagely_purge_common', array($this, 'purgeCommon'),10 );
        add_filter( 'pagely_purge_comment', array($this, 'purgeComment'),10,1 );
        add_filter( 'pagely_purge_post', array($this, 'purgePost'),10,1 );
        add_filter( 'pagely_purge_errors', array($this, 'purgeErrors'),10);

    }

    public function purgeCommon($path = null)
    {
        global $PAGELY_CACHE_PURGE_ALWAYS;

        if (!is_null($path) && !empty(apply_filters('pagely_cache_purge_path', $path))) {
            $purgePaths = ['/','/(.*)(/?)feed(.*)'];
            $purgePaths = apply_filters('pagely_cache_purge_paths', $purgePaths);
            foreach($purgePaths as $path)
            {
                $this->purgePath($path);
            }
        }

        if (!empty($PAGELY_CACHE_PURGE_ALWAYS))
        {
            foreach($PAGELY_CACHE_PURGE_ALWAYS as $url)
            {
                $this->purgePath($url);
            }
        }
    }

    public function purgeAll()
    {
        $this->purgePath("/(.*)");
    }

    public function purgeComment($comment_id)
    {
        $comment = get_comment($comment_id);

        if (!empty($comment) && ($comment->comment_approved == 1 || $comment->comment_approved == 'trash'))
        {
            // Purge post
            $this->purgePost($comment->comment_post_ID);
        }
    }

    public function purgePost($post_id, $after = null, $before = null)
    {
        // if we are updating a post and its not published don't purge
        // this keeps us from purging autosaves
        if (!empty($after) && $after->post_status != 'publish') {
            return true;
        }

        $perm_url = get_permalink($post_id);
        $post_type = $after->post_type ?? null;

        // Add ability to intercept and disable custom plugins post-types from doing purge requests via a mu-plugin
        $shouldPurge = apply_filters('pagely_cache_purge_should_purge_post', true, $after, $post_id);
        if (!$shouldPurge) {
            return true;
        }

        // If the post is a single post, clear the archive page(s)
        if (PAGELY_CLEAR_ARCHIVE_PAGE)
        {
            if ($post_type !== null) {
                $post_type_object = get_post_type_object($post_type);
                if (!empty($post_type_object) && $post_type_object->has_archive){
                    $archive_path = get_post_type_archive_link($post_type);
                    if ($archive_path) {
                        $this->purgePath(preg_replace('@https?://[^/]+@', '', $archive_path));
                    }
                }
            }
        }

        if ($perm_url !== false) {
            $this->purgePath(preg_replace('@https?://[^/]+@', '', $perm_url));
            $this->purgeCommon(preg_replace('@https?://[^/]+@', '', $perm_url));
        }
        if (function_exists('get_json_url'))
        {
            $this->purgePath(preg_replace('@https?://[^/]+@', '', get_json_url()."/posts/$post_id"));
        }

        return true;
    }

    public function purgeAttachment($post_id)
    {
        $purge_url = wp_get_attachment_url($post_id);
        if ($purge_url === false) {
            return;
        }
        if (preg_match('@(.+)\.[^.]+$@', $purge_url, $match))
        {
            $base = $match[1];
            $purge_url = $base . "(.+)";
        }
        $this->purgePath(preg_replace('@https?://[^/]+@', '', $purge_url));
    }

    public function purgePath($path)
    {
        $path = apply_filters('pagely_cache_purge_path', $path);
        if (empty($path)) {
            return;
        }

        if (defined('PAGELY_LATE_BASE_URL_FILTER') && PAGELY_LATE_BASE_URL_FILTER === true) {
            // Filter for late baseUrls modifications
            $this->baseUrls = apply_filters('pagely_cache_purge_base_urls_late', $this->baseUrls);
        }

        foreach($this->baseUrls as $url)
        {
            $host = parse_url($url, PHP_URL_HOST);
            $servers = apply_filters('pagely_cache_purge_servers', $this->servers, $path);
            foreach($servers as $serverHost => $serverPort)
            {
                $headers = ['Host' => $host];
                switch($serverPort)
                {
                    case 443:
                        $scheme = 'https';
                        $api = new PagelyApi();
                        $headers['Authorization'] = $api->generateV2AuthorizationHash();
                        break;
                    default:
                        $scheme = 'http';
                }
                $target = "$scheme://".str_replace($url, $host, $serverHost).$path;

                $target = apply_filters('pagely_cache_purge_target', $target);
                if (empty($target)) {
                    continue;
                }

                if ($this->deferred)
                {
                    $this->deferredPurges[$target] = $headers;
                    if (empty($this->shutdownHook))
                    {
                        add_action("shutdown", array($this, "purgeDeferred"));
                        $this->shutdownHook = true;
                    }
                }
                else
                {
                    $this->purgeTarget($target, $headers);
                }
            }
        }
        $this->response();
    }

    public function purgeDeferred()
    {
        if (count($this->deferredPurges) > 20)
        {
             $this->purgeAll();
             return;
        }

        foreach($this->deferredPurges as $target => $headers)
        {
            $this->purgeTarget($target, $headers);
        }
    }

    protected function purgeTarget($target, $headers)
    {
        $http = _wp_http_get_object();
        $conf = [
            'method' => 'PURGE',
            'timeout' => 3,
            'httpversion' => '1.1',
            'headers' => $headers];

        $response = $http->request($target, $conf);

        if (is_wp_error($response))
        {
            $this->purgeErrors[] = [$target, $response->get_error_message($response->get_error_code())];
            $code = $response->get_error_code();
        }
        else
        {
            $code = $response['response']['code'];
        }

        if ($this->log)
        {
            $logFile = pagely_root_dir() . '/mnt/log/cache-purge.log';
            file_put_contents($logFile, date('Y-m-d H:i:s')." - $code $target ".json_encode($headers)."\n", FILE_APPEND);
        }

        if ($this->debug)
            $this->debugMsgs[] = "Purged: $target";

        if ($this->noActionsAfterPurge == false)
            do_action('pagely_cache_purge_after', $target);
    }

    public function purgeErrors()
    {
        // relay the response.
        $str = "Error purging assets, please contact support. <ul>";

        foreach($this->purgeErrors as $error)
        {
            $str .= "<li>$error[0] - $error[1]</li>";
        }
        $this->purgeErrors = array();

        $str .= "</ul>";

        $alert = new Pagely_Alert();
        $alert->setAlert($str, false);

    }

    public function purgeSuccess()
    {
        // relay the response.
        $alert = Pagely_Alert::instance();
        $alert->setAlert('Cache Purge: Success',true,'cache_purge_success');

    }



    public function debugPrint()
    {
        $str = "<h3>Pagely Cache Purge Debug</h3><ul>";
        foreach($this->debugMsgs as $msg)
        {
            $str .= "<li>$msg</li>";
        }
        $str .= "</ul>";
        $this->debugMsgs = array();

        $alert = new Pagely_Alert();
        $alert->setAlert($str);

    }

    public function response()
    {
          if ($this->debug) {
                $this->debugPrint();
          } else {
            if (!empty($this->purgeErrors)) {
                 $this->purgeErrors();
            } else {
                 $this->purgeSuccess();
            }
        }
    }
}

PagelyCachePurge::init();
