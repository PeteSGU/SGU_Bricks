/**
 * WooCommerce Remove from Cart Event Handler for Algolia Analytics
 *
 * This module handles tracking when products are removed from the cart in WooCommerce.
 *
 * @module woo-remove-from-cart-event
 */

import aa from 'search-insights';
import { debugLog } from '../utils/debug';

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
                product_id: product.id || product.product_id || 'none',
            }));
            debugLog('Mapped product data:', cartData.items);
        }

        const eventData = {
            eventName: 'Product Removed from Cart',
            eventType: 'click',
            objectIDs: cartData.items.map(item => item.product_id.toString() + '-0'),
            index: wpswapWCData.algolia_index_name_prefix + 'searchable_posts',
            userToken: window.algolia?.userToken,
            currency: wpswapWCData.currency,
        };

        console.debug('[Algolia] Sending cart removal event:', eventData);
        aa('sendEvents', [eventData]);
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

        const selectors = [
            '.wc-block-cart-item__remove-link',
            'a.remove',
            '.remove_from_cart_button'
        ];

        document.body.addEventListener('click', (event) => {
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
export function bindRemoveFromCartEvent() {
    if (!window.algolia) {
        console.warn('[Algolia] Analytics not available - window.algolia is undefined');
        return;
    }

    if (!aa) {
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
