/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/events/woo-add-to-cart-event.js":
/*!************************************************!*\
  !*** ./src/js/events/woo-add-to-cart-event.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   bindAddToCartEvent: () => (/* binding */ bindAddToCartEvent)
/* harmony export */ });
/* harmony import */ var search_insights__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! search-insights */ "./node_modules/search-insights/index-browser.mjs");
/* harmony import */ var _utils_debug__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/debug */ "./src/js/utils/debug.js");
/**
 * WooCommerce Add to Cart Event Handler for Algolia Analytics
 *
 * This module handles tracking of WooCommerce add-to-cart events in Algolia analytics.
 * Captures product data and sends conversion events when products are added to cart.
 *
 * @requires search-insights - Algolia insights library
 */




/**
 * Extracts product data from the WooCommerce add-to-cart button or form
 *
 * @param {HTMLElement} element - The clicked button or form element
 * @param {Object} fragments - Cart fragments from WooCommerce
 * @returns {Object} Normalized product data
 */
function extractProductData(element, fragments) {
  (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Extracting product data from:', {
    element: element,
    elementType: element?.tagName,
    elementClasses: element?.className,
    fragments: fragments
  });

  // Try to get data from button or form first
  const productData = {
    product_id: null,
    quantity: 1,
    price: 0,
    name: '',
    variation_id: null
  };

  // Validate input
  if (!element) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('No element provided to extractProductData');
    return productData;
  }
  try {
    // Get product ID from various possible sources
    if (element instanceof HTMLFormElement) {
      const addToCartInput = element.querySelector('input[name="add-to-cart"]');
      const addToCartButton = element.querySelector('button[name="add-to-cart"]');
      const productIdInput = element.querySelector('[name="product_id"]');
      productData.product_id = addToCartInput?.value || addToCartButton?.value || productIdInput?.value;
      const quantityInput = element.querySelector('input[name="quantity"]');
      productData.quantity = parseInt(quantityInput?.value) || 1;
      const variationInput = element.querySelector('input[name="variation_id"]');
      productData.variation_id = variationInput?.value || null;
    } else {
      // Try jQuery data attributes first
      if (jQuery && jQuery(element).data('product_id')) {
        productData.product_id = jQuery(element).data('product_id');
      } else {
        productData.product_id = element.dataset?.product_id || element.value || element.closest('.product')?.querySelector('[name="product_id"]')?.value;
      }
      productData.quantity = parseInt(element.dataset?.quantity) || 1;
      productData.variation_id = element.dataset?.variation_id || null;
    }

    // Try to get price and name from the page
    const product = element.closest('.product');
    if (product) {
      // Check for simple product price
      const priceElem = product.querySelector('.price .amount, .woocommerce-Price-amount, .price ins .amount, .woocommerce-variation-price .woocommerce-Price-amount.amount');
      if (priceElem) {
        productData.price = parseFloat(priceElem.textContent.replace(/[^0-9.]/g, ''));
      }

      // Check for variation price
      const priceElemVariation = product.querySelector('.woocommerce-variation-price .woocommerce-Price-amount.amount');
      if (priceElemVariation) {
        productData.price = parseFloat(priceElemVariation.textContent.replace(/[^0-9.]/g, ''));
      }
      const nameElem = product.querySelector('.product_title, .woocommerce-loop-product__title, h1.entry-title');
      if (nameElem) {
        productData.name = nameElem.textContent.trim();
      }
    }

    // If we have fragments, try to get additional data
    if (fragments && typeof fragments === 'object') {
      // WooCommerce sometimes includes the data directly in fragments
      if (fragments.cart_item_data) {
        const cartItem = fragments.cart_item_data;
        productData.price = parseFloat(cartItem.price) || productData.price;
        productData.name = cartItem.product_name || productData.name;
        productData.quantity = parseInt(cartItem.quantity) || productData.quantity;
      }

      // Sometimes the product data is in the fragments.products object
      if (fragments.products && productData.product_id) {
        const product = fragments.products[productData.product_id];
        if (product) {
          productData.name = product.title || productData.name;
          productData.price = parseFloat(product.price) || productData.price;
        }
      }
    }
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Extracted Product Data:', productData);

    // Validate product ID
    if (!productData.product_id) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Could not find product ID from element:', element);
    }
    return productData;
  } catch (error) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Error extracting product data:', error);
    return productData;
  }
}

/**
 * Formats the event data for Algolia analytics
 *
 * @param {Object} productData - The extracted product data
 * @returns {Object} Formatted event data for Algolia
 */
function formatEventData(productData) {
  if (!productData.product_id) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Missing product ID in event data');
    return null;
  }
  (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)(wpswapWCData.currency);
  const eventData = {
    eventName: 'Product Added to Cart',
    index: wpswapWCData.algolia_index_name_prefix + 'searchable_posts',
    objectIDs: [productData.product_id.toString() + '-0'],
    objectData: [{
      queryID: window.lastQueryID || '',
      price: productData.price,
      quantity: productData.quantity,
      name: productData.name,
      variation_id: productData.variation_id
    }],
    currency: wpswapWCData.currency || ''
  };
  (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Formatted Event Data:', eventData);
  return eventData;
}

/**
 * Logs an add-to-cart event to Algolia analytics
 *
 * @param {HTMLElement} element - The clicked button or form element
 * @param {Object} fragments - Cart fragments from WooCommerce
 * @returns {Promise} Promise that resolves when the event is logged
 */
function logAddToCartEvent(element, fragments) {
  return new Promise((resolve, reject) => {
    if (!window.algolia?.insights_enabled) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Algolia insights tracking is disabled');
      resolve();
      return;
    }
    if (!element) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('No element provided for add to cart tracking');
      resolve();
      return;
    }
    const productData = extractProductData(element, fragments);
    if (!productData.product_id) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('No product ID found for add to cart event');
      resolve();
      return;
    }
    const eventData = formatEventData(productData);
    if (!eventData) {
      resolve();
      return;
    }
    try {
      (0,search_insights__WEBPACK_IMPORTED_MODULE_0__["default"])('addedToCartObjectIDs', eventData);
      storeCartEventData(eventData); // Store event data for potential page refresh
      resolve();
    } catch (error) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Error sending event to Algolia:', error);
      resolve(); // Resolve anyway to not block the user
    }
  });
}

/**
 * Stores cart event data in session storage before page refresh
 *
 * @param {Object} eventData - The event data to store
 */
function storeCartEventData(eventData) {
  try {
    sessionStorage.setItem('algolia_cart_event', JSON.stringify(eventData));
  } catch (error) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Error storing cart event data:', error);
  }
}

/**
 * Processes any stored cart events after page load
 */
function processStoredCartEvents() {
  try {
    const storedEvent = sessionStorage.getItem('algolia_cart_event');
    if (storedEvent) {
      const eventData = JSON.parse(storedEvent);
      (0,search_insights__WEBPACK_IMPORTED_MODULE_0__["default"])('addedToCartObjectIDs', eventData);
      sessionStorage.removeItem('algolia_cart_event');
    }
  } catch (error) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Error processing stored cart events:', error);
  }
}

/**
 * Binds the add-to-cart event handler to WooCommerce buttons and forms
 *
 * @returns {void}
 */
function bindAddToCartEvent() {
  // Process any stored events from previous page load
  processStoredCartEvents();

  // Flag to track if added_to_cart event fired
  let addedToCartFired = false;

  // Primary method: Listen for WooCommerce's added_to_cart event
  jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
    addedToCartFired = true;
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Added to cart event triggered:', {
      event: event,
      fragments: fragments,
      cart_hash: cart_hash,
      $button: $button,
      buttonElement: $button ? $button.get(0) : null
    });

    // Convert jQuery object to DOM element if needed
    const button = $button ? $button.get(0) : document.querySelector('.adding_to_cart');
    if (!button) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('No button found for add to cart event');
    }
    logAddToCartEvent(button || this, fragments);
  });

  // Early binding: Catch the click and check if added_to_cart fires
  jQuery(document).on('click', '.ajax_add_to_cart', function (e) {
    const $button = jQuery(this);
    const startTime = Date.now();
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Button clicked:', {
      button: this,
      classes: this.className,
      isAjax: $button.hasClass('ajax_add_to_cart') || $button.hasClass('single_add_to_cart_button'),
      productId: $button.data('product_id') || $button.val()
    });

    // Reset the flag
    addedToCartFired = false;

    // Wait a short time to see if added_to_cart fires
    setTimeout(() => {
      if (!addedToCartFired) {
        (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('added_to_cart event did not fire, using fallback');

        // Convert jQuery object to DOM element if needed
        const button = $button ? $button.get(0) : document.querySelector('.adding_to_cart');
        if (!button) {
          (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('No button found for add to cart event');
        }
        logAddToCartEvent(button || this, null);
      }
    }, 5000); // Wait 5s for the added_to_cart event
  });

  // Backup method: Listen for cart fragment changes
  jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function (event) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Cart fragments updated:', event.type);

    // Get the last clicked add to cart button
    const lastClickedButton = document.querySelector('.ajax_add_to_cart.loading, .single_add_to_cart_button.loading');
    if (lastClickedButton) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Found loading button:', lastClickedButton);
      logAddToCartEvent(lastClickedButton, null);
    }
  });

  // Handle non-AJAX add to cart buttons
  document.body.addEventListener('click', function (event) {
    const button = event.target.closest('.add_to_cart_button:not(.ajax_add_to_cart), .single_add_to_cart_button:not(.ajax_add_to_cart)');
    if (!button) return;

    // Skip if button is inside a form (will be handled by form submit)
    if (button.closest('form.cart')) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Button is inside form, skipping click handler');
      return;
    }

    // For direct add to cart buttons (like in product lists)
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Direct add to cart button clicked');
    event.preventDefault();

    // Store the event data before the redirect
    const productData = extractProductData(button, null);
    const eventData = formatEventData(productData);
    if (eventData) {
      storeCartEventData(eventData);
    }

    // Trigger the original click event
    const href = button.getAttribute('href');
    if (href) {
      window.location.href = href;
    } else {
      button.click();
    }
  });

  // Handle form submissions
  document.body.addEventListener('submit', function (event) {
    const form = event.target.closest('form.cart');
    if (!form) return;
    const button = form.querySelector('.single_add_to_cart_button');

    // Skip if it's an AJAX submission
    if (button?.classList.contains('ajax_add_to_cart')) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('AJAX form submission, skipping');
      return;
    }
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Processing form submission');

    // Store the event data before the redirect
    const productData = extractProductData(form, null);
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugFormSubmission)(form, productData);
    const eventData = formatEventData(productData);
    if (eventData) {
      storeCartEventData(eventData);
    }

    // Submit the form
    form.submit();
  });
}

/***/ }),

/***/ "./src/js/events/woo-checkout-event.js":
/*!*********************************************!*\
  !*** ./src/js/events/woo-checkout-event.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   bindCheckoutEvent: () => (/* binding */ bindCheckoutEvent)
/* harmony export */ });
/* harmony import */ var search_insights__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! search-insights */ "./node_modules/search-insights/index-browser.mjs");
/* harmony import */ var _utils_debug__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/debug */ "./src/js/utils/debug.js");
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
      variation_id: product.variation_id || null
    }));
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Mapped product data:', cartData.items);
  }
  // Fallback to DOM elements if no products data, this will run because if condition always going to try, this is just a backup method
  else {
    // Try different selectors for cart items
    const cartItems = jQuery('.woocommerce-checkout-review-order-table, .wc-block-components-order-summary-item, .cart_item, #order_review .cart_item, .woocommerce-cart-form .cart_item');
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Cart items:', cartItems);
    if (cartItems.length) {
      cartData.items = cartItems.map(function () {
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
  (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Extracted Cart Data:', cartData);
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
  (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Formatted Event Data:', eventData);
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
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Sending event to Algolia:', eventData);
    (0,search_insights__WEBPACK_IMPORTED_MODULE_0__["default"])('convertedObjectIDsAfterSearch', eventData);
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
function bindCheckoutEvent($) {
  let hasLoggedCheckout = false;
  const logCheckoutWithDebounce = () => {
    if (hasLoggedCheckout) {
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Checkout already logged, skipping duplicate event');
      return;
    }
    hasLoggedCheckout = true;
    logCheckoutEvent();
  };

  // Listen for WooCommerce's checkout initialization
  $(document.body).on('init_checkout', function () {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Checkout initialized');
    setTimeout(logCheckoutWithDebounce, 500); // Give WC time to update DOM
  });

  // Track when users reach the checkout page directly
  if (document.body.classList.contains('woocommerce-checkout')) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Direct checkout page load');
    // Wait for page to be fully loaded
    $(window).on('load', function () {
      setTimeout(logCheckoutWithDebounce, 3000);
    });
  }

  // Also track on checkout form updates
  $(document.body).on('updated_checkout', function () {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Checkout updated');
    setTimeout(logCheckoutWithDebounce, 3000);
  });
}

/***/ }),

/***/ "./src/js/events/woo-remove-from-cart-event.js":
/*!*****************************************************!*\
  !*** ./src/js/events/woo-remove-from-cart-event.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   bindRemoveFromCartEvent: () => (/* binding */ bindRemoveFromCartEvent)
/* harmony export */ });
/* harmony import */ var search_insights__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! search-insights */ "./node_modules/search-insights/index-browser.mjs");
/* harmony import */ var _utils_debug__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/debug */ "./src/js/utils/debug.js");
/**
 * WooCommerce Remove from Cart Event Handler for Algolia Analytics
 *
 * This module handles tracking when products are removed from the cart in WooCommerce.
 *
 * @module woo-remove-from-cart-event
 */




/**
 * Track a cart removal event in Algolia Analytics
 *
 * @param {HTMLElement} element - The clicked remove button element
 * @returns {void}
 */
function trackCartRemoval(element) {
  // Try to get data from various WooCommerce sources
  const cartData = {
    currency: '',
    items: [],
    subtotal: 0,
    total: 0
  };
  try {
    if (wpswapWCProducts) {
      cartData.items = Object.entries(wpswapWCProducts.items).map(([key, product]) => ({
        product_id: product.id || product.product_id || 'none'
      }));
      (0,_utils_debug__WEBPACK_IMPORTED_MODULE_1__.debugLog)('Mapped product data:', cartData.items);
    }
    const eventData = {
      eventName: 'Product Removed from Cart',
      eventType: 'click',
      objectIDs: cartData.items.map(item => item.product_id.toString() + '-0'),
      index: wpswapWCData.algolia_index_name_prefix + 'searchable_posts',
      userToken: window.algolia?.userToken,
      currency: wpswapWCData.currency
    };
    console.debug('[Algolia] Sending cart removal event:', eventData);
    (0,search_insights__WEBPACK_IMPORTED_MODULE_0__["default"])('sendEvents', [eventData]);
  } catch (error) {
    console.error('[Algolia] Failed to track cart removal:', error);
  }
}

/**
 * Initialize cart removal tracking
 *
 * @returns {void}
 */
function initializeTracking() {
  try {
    console.debug('[Algolia] Setting up remove from cart click listener');
    const selectors = ['.wc-block-cart-item__remove-link', 'a.remove', '.remove_from_cart_button'];
    document.body.addEventListener('click', event => {
      const removeButton = event.target.closest(selectors.join(', '));
      if (!removeButton) return;
      console.debug('[Algolia] Remove button clicked:', {
        element: removeButton,
        classes: removeButton.className
      });
      trackCartRemoval(removeButton);
    });
    console.debug('[Algolia] Cart removal tracking initialized');
  } catch (error) {
    console.error('[Algolia] Failed to initialize cart removal tracking:', error);
  }
}

/**
 * Bind remove from cart event tracking
 *
 * Sets up event listeners for product removal from cart in WooCommerce.
 * Ensures tracking is initialized only after DOM is ready.
 *
 * @returns {void}
 */
function bindRemoveFromCartEvent() {
  if (!window.algolia) {
    console.warn('[Algolia] Analytics not available - window.algolia is undefined');
    return;
  }
  if (!search_insights__WEBPACK_IMPORTED_MODULE_0__["default"]) {
    console.warn('[Algolia] Analytics not available - search-insights is undefined');
    return;
  }
  console.debug('[Algolia] Initializing remove from cart event tracking');
  if (document.readyState === 'loading') {
    console.debug('[Algolia] Document loading - waiting for DOMContentLoaded');
    document.addEventListener('DOMContentLoaded', initializeTracking);
  } else {
    console.debug('[Algolia] Document ready - initializing immediately');
    initializeTracking();
  }
}

/***/ }),

/***/ "./src/js/utils/debug.js":
/*!*******************************!*\
  !*** ./src/js/utils/debug.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   debugFormSubmission: () => (/* binding */ debugFormSubmission),
/* harmony export */   debugLog: () => (/* binding */ debugLog)
/* harmony export */ });
/**
 * Debug utility functions for Algolia integration
 */

/**
 * Log debug messages with Algolia prefix
 * @param {string} message - The message to log
 * @param {any} [data] - Optional data to log
 */
const debugLog = (message, data = null) => {
  // Only log if debug mode is enabled
  if (!window.algolia?.debug) {
    return;
  }
  const prefix = '[Algolia]';
  if (data) {
    console.log(`${prefix} ${message}`, data);
  } else {
    console.log(`${prefix} ${message}`);
  }
};

/**
 * Log form submission debug data
 * @param {HTMLFormElement} form - The form being submitted
 * @param {Object} productData - Product data being tracked
 */
const debugFormSubmission = (form, productData) => {
  // Only log if debug mode is enabled
  if (!window.algolia?.debug) {
    return;
  }
  debugLog('Form submission details:', {
    form: form,
    formAction: form.action,
    formMethod: form.method,
    formElements: form.elements,
    productData: productData,
    button: form.querySelector('.single_add_to_cart_button'),
    addToCartInput: form.querySelector('[name="add-to-cart"]'),
    isAjax: form.querySelector('.single_add_to_cart_button')?.classList.contains('ajax_add_to_cart')
  });
};

/***/ }),

/***/ "./src/js/wds-algolia-search-pro.js":
/*!******************************************!*\
  !*** ./src/js/wds-algolia-search-pro.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var search_insights__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! search-insights */ "./node_modules/search-insights/index-browser.mjs");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_events__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./woocommerce-events */ "./src/js/woocommerce-events.js");
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





// Enable debug mode
algolia.debug = false;

// Make jQuery available globally for legacy support
window.$ = window.jQuery = (jquery__WEBPACK_IMPORTED_MODULE_1___default());

// Define Algolia configuration object with fallback to window.algolia values
window.algolia = algolia || {
  application_id: algolia.application_id,
  search_api_key: algolia.search_api_key,
  products_index: wpswapWCData.algolia_index_name_prefix + '_searchable_posts',
  insights_enabled: algolia.insights_enabled
};

/**
 * Initialize Algolia analytics with configuration
 * This must be done before any events can be tracked
 */
(0,search_insights__WEBPACK_IMPORTED_MODULE_0__["default"])("init", {
  appId: window.algolia.application_id,
  apiKey: window.algolia.search_api_key
});

// Initialize WooCommerce event tracking
(0,_woocommerce_events__WEBPACK_IMPORTED_MODULE_2__.initWooCommerceEvents)();

/***/ }),

/***/ "./src/js/woocommerce-events.js":
/*!**************************************!*\
  !*** ./src/js/woocommerce-events.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initWooCommerceEvents: () => (/* binding */ initWooCommerceEvents)
/* harmony export */ });
/* harmony import */ var _utils_debug__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/debug */ "./src/js/utils/debug.js");
/* harmony import */ var _events_woo_add_to_cart_event__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./events/woo-add-to-cart-event */ "./src/js/events/woo-add-to-cart-event.js");
/* harmony import */ var _events_woo_remove_from_cart_event__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./events/woo-remove-from-cart-event */ "./src/js/events/woo-remove-from-cart-event.js");
/* harmony import */ var _events_woo_checkout_event__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./events/woo-checkout-event */ "./src/js/events/woo-checkout-event.js");
/**
 * WooCommerce Event Handlers for Algolia Analytics
 * 
 * This module serves as the central coordinator for all WooCommerce event tracking
 * in Algolia analytics. It initializes and manages various event handlers for
 * different WooCommerce interactions.
 * 
 * Current Supported Events:
 * - Add to Cart: Tracks when products are added to the shopping cart
 * - Remove from Cart: Tracks when products are removed from the cart
 * - Begin Checkout: Tracks when users start the checkout process
 * 
 * Future Events (TODO):
 * - Update Cart: When product quantities are changed
 * - Purchase Complete: When an order is successfully placed
 * 
 * @module woocommerce-events
 * @requires ./events/woo-add-to-cart-event - Add to cart event handler
 * @requires ./events/woo-remove-from-cart-event - Remove from cart event handler
 * @requires ./events/woo-checkout-event - Checkout event handler
 */






/**
 * Initialize WooCommerce event listeners
 * 
 * Central initialization function that sets up all WooCommerce event handlers.
 * This function should be called once when the application starts.
 * 
 * Current Initialization Steps:
 * 1. Binds add to cart event tracking
 * 2. Binds remove from cart event tracking
 * 3. Binds checkout initialization tracking
 * 
 * As new event handlers are added, they should be initialized here
 * to maintain a centralized event management system.
 * 
 * @returns {void}
 */
function initWooCommerceEvents() {
  (0,_utils_debug__WEBPACK_IMPORTED_MODULE_0__.debugLog)('Initializing WooCommerce event tracking...');

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTracking);
  } else {
    initializeTracking();
  }
}

/**
 * Initialize all tracking handlers
 */
function initializeTracking() {
  if (!window.algolia?.insights_enabled) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_0__.debugLog)('Algolia insights tracking is disabled');
    return;
  }
  try {
    // Initialize add to cart tracking
    (0,_events_woo_add_to_cart_event__WEBPACK_IMPORTED_MODULE_1__.bindAddToCartEvent)();
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_0__.debugLog)('Add to cart tracking initialized');

    // Initialize remove from cart tracking
    (0,_events_woo_remove_from_cart_event__WEBPACK_IMPORTED_MODULE_2__.bindRemoveFromCartEvent)();
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_0__.debugLog)('Remove from cart tracking initialized');

    // Initialize checkout tracking
    (0,_events_woo_checkout_event__WEBPACK_IMPORTED_MODULE_3__.bindCheckoutEvent)(jQuery);
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_0__.debugLog)('Checkout tracking initialized');
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_0__.debugLog)('WooCommerce event tracking initialization complete');
  } catch (error) {
    (0,_utils_debug__WEBPACK_IMPORTED_MODULE_0__.debugLog)('Error initializing WooCommerce tracking:', error);
  }
}

/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

module.exports = window["jQuery"];

/***/ }),

/***/ "./node_modules/search-insights/dist/search-insights-browser.mjs":
/*!***********************************************************************!*\
  !*** ./node_modules/search-insights/dist/search-insights-browser.mjs ***!
  \***********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AlgoliaAnalytics: () => (/* binding */ AlgoliaAnalytics),
/* harmony export */   LocalStorage: () => (/* binding */ LocalStorage),
/* harmony export */   createInsightsClient: () => (/* binding */ createInsightsClient),
/* harmony export */   "default": () => (/* binding */ entryBrowser),
/* harmony export */   getFunctionalInterface: () => (/* binding */ getFunctionalInterface),
/* harmony export */   getRequesterForBrowser: () => (/* binding */ getRequesterForBrowser),
/* harmony export */   processQueue: () => (/* binding */ processQueue)
/* harmony export */ });
var version="2.17.3";function extractAdditionalParams(e){return e.reduce(function(e,t){var n=e.events,e=e.additionalParams;return"index"in t?{additionalParams:e,events:n.concat([t])}:{events:n,additionalParams:t}},{events:[],additionalParams:void 0})}var supportsCookies=function(){try{return Boolean(navigator.cookieEnabled)}catch(e){return!1}},supportsSendBeacon=function(){try{return Boolean(navigator.sendBeacon)}catch(e){return!1}},supportsXMLHttpRequest=function(){try{return Boolean(XMLHttpRequest)}catch(e){return!1}},supportsNativeFetch=function(){try{return void 0!==fetch}catch(e){return!1}},LocalStorage=function(){};function ensureLocalStorage(){try{var e="__test_localStorage__";return globalThis.localStorage.setItem(e,e),globalThis.localStorage.removeItem(e),globalThis.localStorage}catch(e){}}LocalStorage.get=function(e){var t=null==(t=this.store)?void 0:t.getItem(e);if(!t)return null;try{return JSON.parse(t)}catch(e){return null}},LocalStorage.set=function(t,e){var n;try{null!=(n=this.store)&&n.setItem(t,JSON.stringify(e))}catch(e){console.error("Unable to set "+t+" in localStorage, storage may be full.")}},LocalStorage.remove=function(e){var t;null!=(t=this.store)&&t.removeItem(e)},LocalStorage.store=ensureLocalStorage();var STORE="AlgoliaObjectQueryCache",LIMIT=5e3,FREE=1e3;function getCache(){var e;return null!=(e=LocalStorage.get(STORE))?e:{}}function setCache(e){LocalStorage.set(STORE,limited(e))}function limited(e){return Object.keys(e).length>LIMIT?purgeOldest(e):e}function purgeOldest(e){e=Object.entries(e).sort(function(e,t){e=e[1][1];return t[1][1]-e});return e.slice(0,e.length-FREE-1).reduce(function(e,t){var n=t[0],t=t[1];return Object.assign(Object.assign({},e),((e={})[n]=t,e))},{})}function makeKey(e,t){return e+"_"+t}function storeQueryForObject(e,t,n){var i=getCache();i[makeKey(e,t)]=[n,Date.now()],setCache(i)}function getQueryForObject(e,t){return getCache()[makeKey(e,t)]}function removeQueryForObjects(t,e){var n=getCache();e.forEach(function(e){delete n[makeKey(t,e)]}),setCache(n)}var isUndefined=function(e){return void 0===e},isNumber=function(e){return"number"==typeof e},isFunction=function(e){return"function"==typeof e},isPromise=function(e){return"function"==typeof(null==e?void 0:e.then)};function getFunctionalInterface(i){return function(e){for(var t=[],n=arguments.length-1;0<n--;)t[n]=arguments[n+1];if(e&&isFunction(i[e]))return i[e].apply(i,t);console.warn("The method `"+e+"` doesn't exist.")}}var DEFAULT_ALGOLIA_AGENTS=["insights-js ("+version+")","insights-js-browser-esm ("+version+")"];function addAlgoliaAgent(e){-1===this._ua.indexOf(e)&&this._ua.push(e)}function getVersion(e){return isFunction(e)&&e(this.version),this.version}function __rest(e,t){var n={};for(r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.indexOf(r)<0&&(n[r]=e[r]);if(null!=e&&"function"==typeof Object.getOwnPropertySymbols)for(var i=0,r=Object.getOwnPropertySymbols(e);i<r.length;i++)t.indexOf(r[i])<0&&Object.prototype.propertyIsEnumerable.call(e,r[i])&&(n[r[i]]=e[r[i]]);return n}function addQueryId(e){return e.map(function(i){var r,e;return!isValidEventForQueryIdLookup(i)||(r=[],e=null==(e=i.objectIDs)?void 0:e.map(function(e,t){var n=null==(n=i.objectData)?void 0:n[t];return null!=n&&n.queryID?n:((t=(null!=(t=getQueryForObject(i.index,e))?t:[])[0])&&r.push(e),Object.assign(Object.assign({},n),{queryID:t}))}),0===r.length)?i:Object.assign(Object.assign({},i),{objectData:e,objectIDsWithInferredQueryID:r})})}function isValidEventForQueryIdLookup(e){return!e.queryID&&"conversion"===e.eventType}function makeSendEvents(r){return function(e,t){var i=this;if(this._userHasOptedOut)return Promise.resolve(!1);if(!(!isUndefined(this._apiKey)&&!isUndefined(this._appId)||(null==(n=null==t?void 0:t.headers)?void 0:n["X-Algolia-Application-Id"])&&(null==(n=null==t?void 0:t.headers)?void 0:n["X-Algolia-API-Key"])))throw new Error("Before calling any methods on the analytics, you first need to call the 'init' function with appId and apiKey parameters or provide custom credentials in additional parameters.");!this._userToken&&this._anonymousUserToken&&this.setAnonymousUserToken(!0);var n=(null!=t&&t.inferQueryID?addQueryId(e):e).map(function(e){var t=e.filters,n=__rest(e,["filters"]),e=Object.assign(Object.assign({},n),{userToken:null!=(n=null==e?void 0:e.userToken)?n:i._userToken,authenticatedUserToken:null!=(n=null==e?void 0:e.authenticatedUserToken)?n:i._authenticatedUserToken});return isUndefined(t)||(e.filters=t.map(encodeURIComponent)),e});return 0===n.length?Promise.resolve(!1):(e=sendRequest(r,this._ua,this._endpointOrigin,n,this._appId,this._apiKey,null==t?void 0:t.headers),isPromise(e)?e.then(purgePurchased(n)):e)}}function purgePurchased(t){return function(e){return e&&t.filter(function(e){var t=e.eventType,n=e.eventSubtype,e=e.objectIDs;return"conversion"===t&&"purchase"===n&&(null==e?void 0:e.length)}).forEach(function(e){return removeQueryForObjects(e.index,e.objectIDs)}),e}}function sendRequest(e,t,n,i,r,o,s){var a=(s=void 0===s?{}:s)["X-Algolia-Application-Id"],c=s["X-Algolia-API-Key"],s=__rest(s,["X-Algolia-Application-Id","X-Algolia-API-Key"]),u=Object.assign({"X-Algolia-Application-Id":null!=a?a:r,"X-Algolia-API-Key":null!=c?c:o,"X-Algolia-Agent":encodeURIComponent(t.join("; "))},s);return e(n+"/1/events?"+Object.keys(u).map(function(e){return e+"="+u[e]}).join("&"),{events:i})}function createUUID(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(e){var t=16*Math.random()|0;return("x"===e?t:3&t|8).toString(16)})}var COOKIE_KEY="_ALGOLIA",MONTH=2592e6,setCookie=function(e,t,n){var i=new Date,n=(i.setTime(i.getTime()+n),"expires="+i.toUTCString());document.cookie=e+"="+t+";"+n+";path=/"},getCookie=function(e){for(var t=e+"=",n=document.cookie.split(";"),i=0;i<n.length;i++){for(var r=n[i];" "===r.charAt(0);)r=r.substring(1);if(0===r.indexOf(t))return r.substring(t.length,r.length)}return""};function checkIfAnonymousToken(e){return"number"!=typeof e&&0===e.indexOf("anonymous-")}function saveTokenAsCookie(){var e=getCookie(COOKIE_KEY);!this._userToken||e&&""!==e&&0===e.indexOf("anonymous-")||setCookie(COOKIE_KEY,this._userToken,this._cookieDuration)}function setAnonymousUserToken(e){(e=void 0!==e&&e)?this.setUserToken("anonymous-"+createUUID()):supportsCookies()&&((e=getCookie(COOKIE_KEY))&&""!==e&&0===e.indexOf("anonymous-")?this.setUserToken(e):(e=this.setUserToken("anonymous-"+createUUID()),setCookie(COOKIE_KEY,e,this._cookieDuration)))}function setUserToken(e){return this._userToken=e,isFunction(this._onUserTokenChangeCallback)&&this._onUserTokenChangeCallback(this._userToken),this._userToken}function getUserToken(e,t){return isFunction(t)&&t(null,this._userToken),this._userToken}function onUserTokenChange(e,t){this._onUserTokenChangeCallback=e,t&&t.immediate&&isFunction(this._onUserTokenChangeCallback)&&this._onUserTokenChangeCallback(this._userToken)}function setAuthenticatedUserToken(e){return this._authenticatedUserToken=e,isFunction(this._onAuthenticatedUserTokenChangeCallback)&&this._onAuthenticatedUserTokenChangeCallback(this._authenticatedUserToken),this._authenticatedUserToken}function getAuthenticatedUserToken(e,t){return isFunction(t)&&t(null,this._authenticatedUserToken),this._authenticatedUserToken}function onAuthenticatedUserTokenChange(e,t){this._onAuthenticatedUserTokenChangeCallback=e,t&&t.immediate&&isFunction(this._onAuthenticatedUserTokenChangeCallback)&&this._onAuthenticatedUserTokenChangeCallback(this._authenticatedUserToken)}function addEventType(t,e){return e.map(function(e){return Object.assign({eventType:t},e)})}function addEventTypeAndSubtype(t,n,e){return e.map(function(e){return Object.assign({eventType:t,eventSubtype:n},e)})}function clickedObjectIDsAfterSearch(){for(var i=this,e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),r=n.events,n=n.additionalParams;return r.forEach(function(e){var t=e.index,n=e.queryID;return e.objectIDs.forEach(function(e){return!i._userHasOptedOut&&storeQueryForObject(t,e,n)})}),this.sendEvents(addEventType("click",r),n)}function clickedObjectIDs(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventType("click",i),n)}function clickedFilters(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventType("click",i),n)}function convertedObjectIDsAfterSearch(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventType("conversion",i),n)}function addedToCartObjectIDsAfterSearch(){for(var o=this,e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return i.forEach(function(e){var n=e.index,i=e.queryID,t=e.objectIDs,r=e.objectData;return t.forEach(function(e,t){t=null!=(t=null==(t=null==r?void 0:r[t])?void 0:t.queryID)?t:i;!o._userHasOptedOut&&t&&storeQueryForObject(n,e,t)})}),this.sendEvents(addEventTypeAndSubtype("conversion","addToCart",i),n)}function purchasedObjectIDsAfterSearch(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventTypeAndSubtype("conversion","purchase",i),n)}function convertedObjectIDs(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventType("conversion",i),n)}function addedToCartObjectIDs(){for(var r=this,e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return i.forEach(function(e){var n=e.index,t=e.objectIDs,i=e.objectData;return t.forEach(function(e,t){t=null==(t=null==i?void 0:i[t])?void 0:t.queryID;!r._userHasOptedOut&&t&&storeQueryForObject(n,e,t)})}),this.sendEvents(addEventTypeAndSubtype("conversion","addToCart",i),n)}function purchasedObjectIDs(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventTypeAndSubtype("conversion","purchase",i),n)}function convertedFilters(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventType("conversion",i),n)}var SUPPORTED_REGIONS=["de","us"];function init(e){var t;if(!isUndefined((e=void 0===e?{}:e).region)&&-1===SUPPORTED_REGIONS.indexOf(e.region))throw new Error("optional region is incorrect, please provide either one of: "+SUPPORTED_REGIONS.join(", ")+".");if(!(isUndefined(e.cookieDuration)||isNumber(e.cookieDuration)&&isFinite(e.cookieDuration)&&Math.floor(e.cookieDuration)===e.cookieDuration))throw new Error("optional cookieDuration is incorrect, expected an integer.");setOptions(this,e,{_userHasOptedOut:Boolean(e.userHasOptedOut),_region:e.region,_host:e.host,_anonymousUserToken:null==(t=e.anonymousUserToken)||t,_useCookie:null!=(t=e.useCookie)&&t,_cookieDuration:e.cookieDuration||6*MONTH}),this._endpointOrigin=this._host||(this._region?"https://insights."+this._region+".algolia.io":"https://insights.algolia.io"),this._ua=[].concat(DEFAULT_ALGOLIA_AGENTS),e.authenticatedUserToken&&this.setAuthenticatedUserToken(e.authenticatedUserToken),e.userToken?this.setUserToken(e.userToken):this._userToken||this._userHasOptedOut||!this._useCookie?checkIfTokenNeedsToBeSaved(this)&&this.saveTokenAsCookie():this.setAnonymousUserToken()}function setOptions(e,t,n){var i=t.partial,r=__rest(t,["partial"]);i||Object.assign(e,n),Object.assign(e,Object.keys(r).reduce(function(e,t){return Object.assign(Object.assign({},e),((e={})["_"+t]=r[t],e))},{}))}function checkIfTokenNeedsToBeSaved(e){return void 0!==e._userToken&&checkIfAnonymousToken(e._userToken)&&e._useCookie&&!e._userHasOptedOut}function viewedObjectIDs(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventType("view",i),n)}function viewedFilters(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n=extractAdditionalParams(e),i=n.events,n=n.additionalParams;return this.sendEvents(addEventType("view",i),n)}var AlgoliaAnalytics=function(e){e=e.requestFn;this._endpointOrigin="https://insights.algolia.io",this._anonymousUserToken=!0,this._userHasOptedOut=!1,this._useCookie=!1,this._cookieDuration=6*MONTH,this._ua=[],this.version=version,this.sendEvents=makeSendEvents(e).bind(this),this.init=init.bind(this),this.addAlgoliaAgent=addAlgoliaAgent.bind(this),this.saveTokenAsCookie=saveTokenAsCookie.bind(this),this.setUserToken=setUserToken.bind(this),this.setAnonymousUserToken=setAnonymousUserToken.bind(this),this.getUserToken=getUserToken.bind(this),this.onUserTokenChange=onUserTokenChange.bind(this),this.setAuthenticatedUserToken=setAuthenticatedUserToken.bind(this),this.getAuthenticatedUserToken=getAuthenticatedUserToken.bind(this),this.onAuthenticatedUserTokenChange=onAuthenticatedUserTokenChange.bind(this),this.clickedObjectIDsAfterSearch=clickedObjectIDsAfterSearch.bind(this),this.clickedObjectIDs=clickedObjectIDs.bind(this),this.clickedFilters=clickedFilters.bind(this),this.convertedObjectIDsAfterSearch=convertedObjectIDsAfterSearch.bind(this),this.purchasedObjectIDsAfterSearch=purchasedObjectIDsAfterSearch.bind(this),this.addedToCartObjectIDsAfterSearch=addedToCartObjectIDsAfterSearch.bind(this),this.convertedObjectIDs=convertedObjectIDs.bind(this),this.addedToCartObjectIDs=addedToCartObjectIDs.bind(this),this.purchasedObjectIDs=purchasedObjectIDs.bind(this),this.convertedFilters=convertedFilters.bind(this),this.viewedObjectIDs=viewedObjectIDs.bind(this),this.viewedFilters=viewedFilters.bind(this),this.getVersion=getVersion.bind(this)};function createInsightsClient(e){var t,e=getFunctionalInterface(new AlgoliaAnalytics({requestFn:e}));if("object"==typeof window&&!window.AlgoliaAnalyticsObject){for(;t=createUUID(),void 0!==window[t];);window.AlgoliaAnalyticsObject=t,window[window.AlgoliaAnalyticsObject]=e}return e.version=version,e}function processQueue(e){var n,t=e.AlgoliaAnalyticsObject;t&&(n=getFunctionalInterface(this),(e=e[t]).queue=e.queue||[],(t=e.queue).forEach(function(e){var e=[].slice.call(e),t=e[0],e=e.slice(1);n.apply(void 0,[t].concat(e))}),t.push=function(e){var e=[].slice.call(e),t=e[0],e=e.slice(1);n.apply(void 0,[t].concat(e))})}var requestWithXMLHttpRequest=function(r,o){return new Promise(function(e,t){var n=JSON.stringify(o),i=new XMLHttpRequest;i.addEventListener("readystatechange",function(){4===i.readyState&&200===i.status?e(!0):4===i.readyState&&e(!1)}),i.addEventListener("error",function(){return t()}),i.addEventListener("timeout",function(){return e(!1)}),i.open("POST",r),i.setRequestHeader("Content-Type","application/json"),i.send(n)})},requestWithSendBeacon=function(e,t){var n=JSON.stringify(t),n=navigator.sendBeacon(e,n);return Promise.resolve(!!n||requestWithXMLHttpRequest(e,t))},requestWithNativeFetch=function(e,i){return new Promise(function(t,n){fetch(e,{method:"POST",body:JSON.stringify(i),headers:{"Content-Type":"application/json"}}).then(function(e){t(200===e.status)}).catch(function(e){n(e)})})};function getRequesterForBrowser(){if(supportsSendBeacon())return requestWithSendBeacon;if(supportsXMLHttpRequest())return requestWithXMLHttpRequest;if(supportsNativeFetch())return requestWithNativeFetch;throw new Error("Could not find a supported HTTP request client in this environment.")}var entryBrowser=createInsightsClient(getRequesterForBrowser());


/***/ }),

/***/ "./node_modules/search-insights/index-browser.mjs":
/*!********************************************************!*\
  !*** ./node_modules/search-insights/index-browser.mjs ***!
  \********************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AlgoliaAnalytics: () => (/* reexport safe */ _dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__.AlgoliaAnalytics),
/* harmony export */   LocalStorage: () => (/* reexport safe */ _dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__.LocalStorage),
/* harmony export */   createInsightsClient: () => (/* reexport safe */ _dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__.createInsightsClient),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   getFunctionalInterface: () => (/* reexport safe */ _dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__.getFunctionalInterface),
/* harmony export */   getRequesterForBrowser: () => (/* reexport safe */ _dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__.getRequesterForBrowser),
/* harmony export */   processQueue: () => (/* reexport safe */ _dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__.processQueue)
/* harmony export */ });
/* harmony import */ var _dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dist/search-insights-browser.mjs */ "./node_modules/search-insights/dist/search-insights-browser.mjs");


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_dist_search_insights_browser_mjs__WEBPACK_IMPORTED_MODULE_0__["default"]);


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _js_wds_algolia_search_pro__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./js/wds-algolia-search-pro */ "./src/js/wds-algolia-search-pro.js");

})();

/******/ })()
;
//# sourceMappingURL=index.js.map