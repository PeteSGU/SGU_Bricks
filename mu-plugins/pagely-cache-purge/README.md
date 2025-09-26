Public API
---

### Hooks for purging cache for everything/specific objects, used via `apply_filters`:

- `pagely_purge_all`
- `pagely_purge_comment`
- `pagely_purge_post`
- `pagely_purge_common` (purge `/`, `/(.*)(/?)feed(.*)`, and the contents of the `$PAGELY_CACHE_PURGE_ALWAYS` global)
- `pagely_cache_purge_base_urls`
- `pagely_cache_purge_base_urls_late` (`PAGELY_LATE_BASE_URL_FILTER` constant needs to be defined for this to apply. This filter is the same as `pagely_cache_purge_base_urls` except that it's applied in the `purgePath` function.)

### Hooks for changing cache purge behavior, used via `add_filter`:

- `pagely_cache_purge_path`
  - To disable purging `return null;` from the filter (useful during batch imports, etc)
 
The `pagely-cache-purge.php:purgePath()` function is the main hook which checks & stops all subsequent PURGE requests.

### Other hooks

- `pagely_cache_purge_after($target)`: This is run after a cache purge request is made.
  - args:
    - `$target`: The endpoint the PURGE request was sent to (e.g. `http://10.11.12.13/foobar/`)
---

## Disable all caching from the site by mu-plugin hook

Example code:
- For website to put in their automated import function.
```php
<?php
// disable cache purge
add_filter('pagely_cache_purge_path', function($url) { return null; });
```
---

## Purge all cache function example
- If you need to purge all cache
```
if ( class_exists( 'PagelyCachePurge' ) ) {
        $purger = new PagelyCachePurge();
        $purger->purgeAll();
}
```
---

## Stop the homepage from PURGING, when only comments are made
```
# Create mu-script
$ sudo -u www-data nano wp-content/mu-plugins/remove-homepage-purge-when-comments-posted.php
```

Content:
```php
<?php
// disable cache purge on comments from happening on homepage
add_filter('pagely_cache_purge_path', function($url) {
        if ($url == "/") {
            // Only stop homepage from purging
            return null;
        } else {
            return $url;
        }
});
```



---

## Add additional pages to be purged on any purge

Use case when there's /latest/ or /topic/* additonal pages which show latest posts.

```
wp-content/mu-plugins/custom-global-purge-pages.php
```
```php
<?php
/*
Plugin Name: Pagely Cache Purge global pages
Description: Custom list to add pages in addition to the ones already queued up to be purged.
Author: Pagely
Author URI: http://pagely.com
Version: 1.0
*/

$PAGELY_CACHE_PURGE_ALWAYS = [
    '/latest/',
    '/topics/(.*)',
];
```
Setting the `$PAGELY_CACHE_PURGE_ALWAYS` array should make use of what already exists in the management script by including the global variable, to purge more pages for every purge request.

Test it out to confirm working correctly.

## Change the base common purges
Common normally purges `/` and `/(.*)(/?)feed(.*)` on every purge.  With this you can overwrite or adjust, akin to $PAGELY_CACHE_PURGE_ALWAYS except
you are in control of the base purges rather then just adding to them.

```php
<?php
/*
Plugin Name: Pagely Cache Purge common purges
Description: Adjust common purge pages.
Author: Pagely
Author URI: http://pagely.com
Version: 1.0
*/

// Disable cache purge of feed urls
add_filter('pagely_cache_purge_paths', function($purge_paths) {
  $purge_paths = ['/'];
  return ($purge_paths);
});
```

---

## Change default Purge Base URLs

For cases where a parent multisite network site and children mu-sites are under different domains. This disables it, so the main site cache won't be automatically PURGE'd for every update to child mu-sites. 

- (Note: The default plugin behavior is if `WP_HOME` constant is defined in `wp-config.php`, then that will always get a cache purge. This disables that too.)

```
wp-content/mu-plugins/custom-exclude-purge-urls.php
```

```php
<?php
/*
Plugin Name: Pagely Cache Purge Customizations
Description: Disabled cache from being automatically purged, when on child mu-sites. + dedupes the PURGE Url Hosts.
Author: Pagely
Author URI: http://pagely.com
Version: 1.0
*/

add_filter('pagely_cache_purge_base_urls', function($basePurgeUrls) {

   // Get main sites home url
   if (defined('WP_HOME')) {
      $mainSiteHome = WP_HOME;
   }

   $resultbasePurgeUrls = [];
   $hostIndex = [];

   foreach ($basePurgeUrls as $basePurgeUrl) {

      // Grab only the hosts to build index
      $host = parse_url($basePurgeUrl, PHP_URL_HOST);

      // Check to make sure we're not the main site home, or that the URL Host already exists
      if ( ($basePurgeUrl !== $mainSiteHome) && (!array_key_exists($host, $hostIndex)) ) {
         // Add host to tracked index
         $hostIndex[$host] = "";

         // Add to array to purge
         $resultbasePurgeUrls[] = $basePurgeUrl;
      }

   }
   // New URLs to be PURGE'd
   return $resultbasePurgeUrls;

});
```
