/**
 * WooCommerce Add to Cart Event Handler for Algolia Analytics
 *
 * This module handles tracking of WooCommerce add-to-cart events in Algolia analytics.
 * Captures product data and sends conversion events when products are added to cart.
 *
 * @requires search-insights - Algolia insights library
 */

import aa from 'search-insights';
import { debugLog, debugFormSubmission } from '../utils/debug';

/**
 * Extracts product data from the WooCommerce add-to-cart button or form
 *
 * @param {HTMLElement} element - The clicked button or form element
 * @param {Object} fragments - Cart fragments from WooCommerce
 * @returns {Object} Normalized product data
 */
function extractProductData(element, fragments) {
    debugLog('Extracting product data from:', {
        element: element,
        elementType: element?.tagName,
        elementClasses: element?.className,
        fragments: fragments
    });

	// Try to get data from button or form first
	const productData = {
		product_id  : null,
		quantity    : 1,
		price       : 0,
		name        : '',
		variation_id: null
	};

    // Validate input
    if (!element) {
        debugLog('No element provided to extractProductData');
        return productData;
    }

    try {
        // Get product ID from various possible sources
        if (element instanceof HTMLFormElement) {
            const addToCartInput = element.querySelector('input[name="add-to-cart"]');
            const addToCartButton = element.querySelector('button[name="add-to-cart"]');
            const productIdInput = element.querySelector('[name="product_id"]');

            productData.product_id = (addToCartInput?.value ||
                                    addToCartButton?.value ||
                                    productIdInput?.value);

            const quantityInput = element.querySelector('input[name="quantity"]');
            productData.quantity = parseInt(quantityInput?.value) || 1;

            const variationInput = element.querySelector('input[name="variation_id"]');
            productData.variation_id = variationInput?.value || null;
        } else {
            // Try jQuery data attributes first
            if (jQuery && jQuery(element).data('product_id')) {
                productData.product_id = jQuery(element).data('product_id');
            } else {
                productData.product_id = element.dataset?.product_id ||
                                    element.value ||
                                    element.closest('.product')?.querySelector('[name="product_id"]')?.value;
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

        debugLog('Extracted Product Data:', productData);

        // Validate product ID
        if (!productData.product_id) {
            debugLog('Could not find product ID from element:', element);
        }

        return productData;

    } catch (error) {
        debugLog('Error extracting product data:', error);
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
        debugLog('Missing product ID in event data');
        return null;
    }
    debugLog(wpswapWCData.currency);
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
        currency: wpswapWCData.currency || '',
    };

    debugLog('Formatted Event Data:', eventData);
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
            debugLog('Algolia insights tracking is disabled');
            resolve();
            return;
        }

        if (!element) {
            debugLog('No element provided for add to cart tracking');
            resolve();
            return;
        }

        const productData = extractProductData(element, fragments);
        if (!productData.product_id) {
            debugLog('No product ID found for add to cart event');
            resolve();
            return;
        }

        const eventData = formatEventData(productData);
        if (!eventData) {
            resolve();
            return;
        }

        try {
            aa('addedToCartObjectIDs', eventData);
            storeCartEventData(eventData); // Store event data for potential page refresh
            resolve();
        } catch (error) {
            debugLog('Error sending event to Algolia:', error);
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
        debugLog('Error storing cart event data:', error);
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
            aa('addedToCartObjectIDs', eventData);
            sessionStorage.removeItem('algolia_cart_event');
        }
    } catch (error) {
        debugLog('Error processing stored cart events:', error);
    }
}

/**
 * Binds the add-to-cart event handler to WooCommerce buttons and forms
 *
 * @returns {void}
 */
export function bindAddToCartEvent() {
    // Process any stored events from previous page load
    processStoredCartEvents();

    // Flag to track if added_to_cart event fired
    let addedToCartFired = false;

    // Primary method: Listen for WooCommerce's added_to_cart event
    jQuery(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
        addedToCartFired = true;
        debugLog('Added to cart event triggered:', {
            event: event,
            fragments: fragments,
            cart_hash: cart_hash,
            $button: $button,
            buttonElement: $button ? $button.get(0) : null
        });

        // Convert jQuery object to DOM element if needed
        const button = $button ? $button.get(0) : document.querySelector('.adding_to_cart');

        if (!button) {
            debugLog('No button found for add to cart event');
        }

        logAddToCartEvent(button || this, fragments);
    });

    // Early binding: Catch the click and check if added_to_cart fires
    jQuery(document).on('click', '.ajax_add_to_cart', function(e) {

        const $button = jQuery(this);
        const startTime = Date.now();

        debugLog('Button clicked:', {
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
                debugLog('added_to_cart event did not fire, using fallback');

                // Convert jQuery object to DOM element if needed
                const button = $button ? $button.get(0) : document.querySelector('.adding_to_cart');
                if (!button) {
                    debugLog('No button found for add to cart event');
                }

                logAddToCartEvent(button || this, null);
            }
        }, 5000); // Wait 5s for the added_to_cart event
    });

    // Backup method: Listen for cart fragment changes
    jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function(event) {
        debugLog('Cart fragments updated:', event.type);

        // Get the last clicked add to cart button
        const lastClickedButton = document.querySelector('.ajax_add_to_cart.loading, .single_add_to_cart_button.loading');
        if (lastClickedButton) {
            debugLog('Found loading button:', lastClickedButton);
            logAddToCartEvent(lastClickedButton, null);
        }
    });

    // Handle non-AJAX add to cart buttons
    document.body.addEventListener('click', function(event) {
        const button = event.target.closest('.add_to_cart_button:not(.ajax_add_to_cart), .single_add_to_cart_button:not(.ajax_add_to_cart)');
        if (!button) return;

        // Skip if button is inside a form (will be handled by form submit)
        if (button.closest('form.cart')) {
            debugLog('Button is inside form, skipping click handler');
            return;
        }

        // For direct add to cart buttons (like in product lists)
        debugLog('Direct add to cart button clicked');
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
    document.body.addEventListener('submit', function(event) {
        const form = event.target.closest('form.cart');
        if (!form) return;

        const button = form.querySelector('.single_add_to_cart_button');

        // Skip if it's an AJAX submission
        if (button?.classList.contains('ajax_add_to_cart')) {
            debugLog('AJAX form submission, skipping');
            return;
        }

        debugLog('Processing form submission');

        // Store the event data before the redirect
        const productData = extractProductData(form, null);
        debugFormSubmission(form, productData);

        const eventData = formatEventData(productData);
        if (eventData) {
            storeCartEventData(eventData);
        }

        // Submit the form
        form.submit();
    });

}
