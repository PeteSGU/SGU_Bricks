/**
 * WooCommerce Checkout Event Handler for Algolia Analytics
 *
 * This module handles tracking of WooCommerce checkout initialization events
 * in Algolia analytics. It captures when users start the checkout process
 * and logs relevant cart and product information.
 *
 * Required Global Configuration:
 * window.algolia = {
 *   products_index: string,     // Algolia index name for products
 *   insights_enabled: boolean   // Analytics tracking toggle
 * }
 *
 * @module woo-checkout-event
 * @requires search-insights - Algolia insights library
 * @requires jquery - For DOM manipulation and event handling
 */

import aa from 'search-insights';
import { debugLog } from '../utils/debug';

/**
 * Extracts checkout data from WooCommerce's global state and DOM
 *
 * Gathers relevant checkout information from:
 * 1 Checkout form data
 * 2. Cart item details in the DOM
 *
 * @returns {Object} Normalized checkout data containing:
 *   - currency {string} - Currency code
 *   - items {Array} - Cart items
 */
function extractCheckoutData() {
  // Try to get data from various WooCommerce sources
  const cartData = {
    currency: '',
    items: [],
    subtotal: 0,
    total: 0
  };

  // Try to get currency from different possible sources
  cartData.currency = wpswapWCData.currency;
  console.log(wpswapWCProducts);

  // Map through products object if available
  if (wpswapWCProducts) {
    cartData.items = Object.entries(wpswapWCProducts.items).map(([key, product]) => ({
      product_id: product.id || product.product_id || 'none',
      quantity: parseInt(product.quantity || 1),
      price: parseFloat(product.price || 1),
      name: product.name || product.title || '',
      sku: product.sku || '',
      categories: product.categories || [],
      variation_id: product.variation_id || null,
    }));
    debugLog('Mapped product data:', cartData.items);
  }
  // Fallback to DOM elements if no products data, this will run because if condition always going to try, this is just a backup method
  else {
    // Try different selectors for cart items
    const cartItems = jQuery('.woocommerce-checkout-review-order-table, .wc-block-components-order-summary-item, .cart_item, #order_review .cart_item, .woocommerce-cart-form .cart_item');
    debugLog('Cart items:', cartItems);
    if (cartItems.length) {
      cartData.items = cartItems.map(function() {
        const item = jQuery(this);
        const priceText = item.find('.product-total .amount, .product-price .amount, .wc-block-formatted-money-amount, .woocommerce-Price-amount.amount').first().text();
        const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));

        return {
          product_id: item.find('[data-product_id]').data('product_id') || item.data('product_id') || 'none',
          quantity: parseInt(item.find('.quantity input').val() || item.find('.product-quantity').text() || item.find('.wc-block-components-order-summary-item__quantity > .screen-reader-text').text() || 1),
          price: price || 0,
          name: item.find('.product-name, .wc-block-components-order-summary-item__name, .wc-block-components-product-name').text().trim()
        };
      }).get();
    }

  }

  debugLog('Extracted Cart Data:', cartData);
  return cartData;
}

/**
 * Formats checkout data for Algolia analytics
 *
 * Transforms the extracted checkout data into Algolia's
 * expected event format for conversion tracking.
 *
 * @param {Object} checkoutData - The extracted checkout data
 * @returns {Object} Formatted event data for Algolia
 */
function formatEventData(checkoutData) {
  const eventData = {
    eventName: 'Checkout Started',
    eventType: 'view',
    index: wpswapWCData.algolia_index_name_prefix + 'searchable_posts',
    objectIDs: checkoutData.items.map(item => item.product_id.toString() + '-0'),
    objectData: checkoutData.items.map(item => ({
      queryID: window.lastQueryID || '',
      price: item.price,
      quantity: item.quantity,
      name: item.name
    })),
    currency: wpswapWCData.currency,
    value: wpswapWCProducts.total || 0
  };

  debugLog('Formatted Event Data:', eventData);
  return eventData;
}

/**
 * Logs a checkout initialization event to Algolia analytics
 *
 * Main event logging function that:
 * 1. Checks if insights tracking is enabled
 * 2. Extracts checkout data
 * 3. Formats the data for Algolia
 * 4. Sends the conversion event
 *
 * @returns {void}
 */
function logCheckoutEvent() {
  if (!window.algolia?.insights_enabled) {
    return;
  }

  const checkoutData = extractCheckoutData();
  const eventData = formatEventData(checkoutData);

  if (eventData.objectIDs.length > 0) {
    debugLog('Sending event to Algolia:', eventData);
    aa('convertedObjectIDsAfterSearch', eventData);
  } else {
    console.warn('No cart items found to track');
  }
}

/**
 * Binds the checkout event handler to WooCommerce's checkout initialization
 *
 * Sets up event listeners for multiple checkout triggers to ensure
 * we catch the checkout event regardless of how it's initiated.
 *
 * @param {jQuery} $ - jQuery instance
 * @returns {void}
 */
export function bindCheckoutEvent($) {
  let hasLoggedCheckout = false;

  const logCheckoutWithDebounce = () => {
    if (hasLoggedCheckout) {
      debugLog('Checkout already logged, skipping duplicate event');
      return;
    }
    hasLoggedCheckout = true;
    logCheckoutEvent();
  };

  // Listen for WooCommerce's checkout initialization
  $(document.body).on('init_checkout', function() {
    debugLog('Checkout initialized');
    setTimeout(logCheckoutWithDebounce, 500); // Give WC time to update DOM
  });

  // Track when users reach the checkout page directly
  if (document.body.classList.contains('woocommerce-checkout')) {
    debugLog('Direct checkout page load');
    // Wait for page to be fully loaded
    $(window).on('load', function() {
      setTimeout(logCheckoutWithDebounce, 3000);
    });
  }

  // Also track on checkout form updates
  $(document.body).on('updated_checkout', function() {
    debugLog('Checkout updated');
    setTimeout(logCheckoutWithDebounce, 3000);
  });
}
