/**
 * WebDevStudios Algolia Search Integration
 *
 * This module provides the core Algolia search integration for WordPress,
 * handling initialization, configuration, and WooCommerce event tracking.
 *
 * Global Configuration:
 * window.algolia = {
 *   application_id: string,    // Algolia application ID
 *   search_api_key: string,    // Algolia search API key
 *   products_index: string,    // Index name for products
 *   insights_enabled: boolean  // Whether to track analytics
 * }
 *
 * @module wds-algolia-search
 * @requires search-insights - Algolia insights library
 * @requires jquery - For DOM manipulation
 * @requires ./woocommerce-events - WooCommerce event handling
 */

import aa from 'search-insights';
import jQuery from "jquery";
import { initWooCommerceEvents } from './woocommerce-events';

// Enable debug mode
algolia.debug = false;

// Make jQuery available globally for legacy support
window.$ = window.jQuery = jQuery;

// Define Algolia configuration object with fallback to window.algolia values
window.algolia = algolia || {
  application_id: algolia.application_id,
  search_api_key: algolia.search_api_key,
  products_index: wpswapWCData.algolia_index_name_prefix + '_searchable_posts',
  insights_enabled: algolia.insights_enabled,
};

/**
 * Initialize Algolia analytics with configuration
 * This must be done before any events can be tracked
 */
aa("init", {
  appId: window.algolia.application_id,
  apiKey: window.algolia.search_api_key,
});

// Initialize WooCommerce event tracking
initWooCommerceEvents();
