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

import { debugLog } from './utils/debug';
import { bindAddToCartEvent } from './events/woo-add-to-cart-event';
import { bindRemoveFromCartEvent } from './events/woo-remove-from-cart-event';
import { bindCheckoutEvent } from './events/woo-checkout-event';

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
export function initWooCommerceEvents() {
    debugLog('Initializing WooCommerce event tracking...');

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
        debugLog('Algolia insights tracking is disabled');
        return;
    }

    try {
        // Initialize add to cart tracking
        bindAddToCartEvent();
        debugLog('Add to cart tracking initialized');
        
        // Initialize remove from cart tracking
        bindRemoveFromCartEvent();
        debugLog('Remove from cart tracking initialized');
        
        // Initialize checkout tracking
        bindCheckoutEvent(jQuery);
        debugLog('Checkout tracking initialized');

        debugLog('WooCommerce event tracking initialization complete');
    } catch (error) {
        debugLog('Error initializing WooCommerce tracking:', error);
    }
} 