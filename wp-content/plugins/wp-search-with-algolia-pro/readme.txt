=== WP Search with Algolia Pro ===
Contributors: WebDevStudios, williamsba1, tw2113, mrasharirfan
Tags: Search, Algolia, Autocomplete, instant-search, woocommerce
Requires at least: 6.5.0
Tested up to: 6.8.2
Requires PHP: 7.4
Stable tag: 1.6.0
License: GNU General Public License v2.0, MIT License

The developers behind WP Search with Algolia now bring you the only premium Algolia search WordPress plugin built for enterprise-grade websites.

== Description ==

An enterprise-grade website requires an enterprise-grade search platform. That’s where Algolia comes in.

But, an enterprise-grade WordPress website using the power of Algolia search requires a plugin. That’s where WP Search with Algolia Pro comes in.

= Features =

WordPress website agency, WebDevStudios, the developers behind WP Search with Algolia, have now launched WP Search with Algolia Pro. This premium Algolia search plugin is specifically designed for enterprise-grade WordPress websites, including eCommerce.

Think about it. You have a professionally-operated eCommerce website. The search experience you provide your customers is expected to match. Your website’s search should be:

* Dynamic
* Fast
* Intuitive

Meet these goals by relying on the power of Algolia. Isn’t it time you upgraded the quality of your website search experience to suit your enterprise-level website? Integrate WP Search with Algolia Pro today.

#### WooCommerce Support

Use the power of Algolia search for easy indexing of your eCommerce products.

* SKU and product short descriptions
* Total sales and total ratings included for popularity

#### Control Search Settings
* Enable or disable Algolia search indexing on selected pieces of content, such as blog posts, pages, etc.
* Set Algolia’s indexing to match with existing search engine “noindex” settings

#### Additional Support
* Yoast SEO
* All in One SEO

= Links =
* [WebDevStudios](https://webdevstudios.com)
* [Algolia](https://algolia.com)

== Frequently Asked Questions ==

= What are the minimum requirements? =

* Requires WordPress 5.0+
* PHP version 7.4 or greater (PHP 7.4 is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* cURL PHP extension
* mbstring PHP extension
* OpenSSL greater than 1.0.1
* Some payment gateways require fsockopen support (for IPN access)

Visit the [WP Search with Algolia server requirements documentation](https://github.com/WebDevStudios/wp-search-with-algolia/wiki/WP-Search-with-Algolia-plugin-Installation) for a detailed list of server requirements.

= Where can I find WP Search with Algolia documentation and user guides? =

- For help setting up and configuring WP Search with Algolia please refer to the [user guide](https://github.com/WebDevStudios/wp-search-with-algolia/wiki/WP-Search-with-Algolia-plugin-Installation).
- For extending or theming the Autocomplete dropdown, see the [Autocomplete Customization guide](https://github.com/WebDevStudios/wp-search-with-algolia/wiki/Customize-the-Autocomplete-dropdown).
- For extending or theming the Instant Search results page, see the [Search Page Customization guide](https://github.com/WebDevStudios/wp-search-with-algolia/wiki/Customize-your-search-page).

= About Algolia =

Algolia offers its Search as a Service provider on a incremental payment program, including a free Community Plan which includes 10,000 records & 50,000 operations per month. Beyond that, [plans](https://www.algolia.com/pricing/) start at $29/month.

= About WebDevStudios =

WebDevStudios provides end-to-end WordPress opportunities from strategy and planning to website design and development, as well as full data migration, extensive API integrations, scalability, performance and long-term guidance and maintenance. We have service options and solutions for start-ups, small to mid-size businesses, enterprise organizations and marketing agencies.

== Changelog ==

= 1.6.0 =
* Added: Initial support for WooCommerce Event to Algolia event sending. Requires WP Search with Algolia 2.10.2 or higher.
* Added: WooCommerce image size data to the indexed content.
* Added: Filter to prevent disabling typo tolerance for SKU properties.
* Added: Filter to allow for customizing network indexing AJAX settings, most specifically for custom timeout values.
* Updated: Network settings page match version seen in WP Search with Algolia.

= 1.5.2 =
* Fixed: Updated license page code to fix issues around license deactivation and improved multisite support.

= 1.5.1 =
* Fixed: Fatal errors in network admin settings if "network-wide indexing" isn't enabled.

= 1.5.0 =
* Added: Network-wide indexing, local/development installs will be allowed to index site content for testing purposes, even if "Search engine visibility" is toggled on. Hosting providers dev urls include: WPEngine, Pantheon, Pagely, WP Stagecoach, WPVIP.
* Added: `wpswa_pro_should_index_network_site` filter to allow for disabling of index-ability for a given site in network. Defaults to true.
* Added: Indexed status for sites in the Network-wide index list.

= 1.4.1 =

* Fixed: Logic bug with indexing for network-wide indexing. More than what should have been was getting indexed.

= 1.4.0 =
* Added: Confirmed compatibility with and require WordPress 6.5.
* Added: Support for "noindex" settings with The SEO Framework.
* Added: "Indexing complete" messaging for network wide indexing.
* Added: Network batch date to status table.
* Added: Filters to remove default attributes from Free plugin.
* Added: Ability to de-index sold out products.
* Added: Ability to not index "shop only" and "hidden" products.
* Added: Ability to index product weight and dimensions.
* Updated: Improved styling of the Network Admin status table.

= 1.3.4 =
* Fixed: Small breaking change. Adjusted how product "rating" properties are stored, and now store average rating as integer.

= 1.3.3 =
* Fixed: Issues around unchecking general "No index" metabox not tied to an SEO plugin.
* Added: Admin notice upon successful save for Meta Fields with Network Wide Indexing.

= 1.3.2 =
* Updated: Fixed and improved logic around noindex determination for both Yoast SEO and SEOPress. This should help with accuracy.

= 1.3.1 =
* Fixed: Mismatched option indexes for Rank Math and SEOPress "noindex" checks.

= 1.3.0 =
* Added: SEOPress "noindex" support.
* Added: Ability to push content from all sites in a multisite network into one searchable index
* Added: Meta fields UI for searchable content types when network wide enabled.

= 1.2.1 =
* Fixed: Prevent fatal error if WooCommerce not available.

= 1.2.0 =
* Added: Rank Math SEO "noindex" support.
* Added: Filter to disable our out-of-box "noindex" metabox.
* Added: Filter to limit Autocomplete and Instantsearch to just Woocommerce's `product` post type.
* Added: Filter to disable automatic index settings push on settings page save.
* Added: Index cumulative averge rating alongside total ratings, if enabled.

= 1.1.0 =
* Added: Initial support for fetching and including prices for variable products, and total variations.
* Fixed: Don't add total sales to custom ranking if total sales aren't set to be included.

= 1.0.0 =
* Initial release.
