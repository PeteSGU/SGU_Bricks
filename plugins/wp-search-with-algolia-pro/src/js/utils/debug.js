/**
 * Debug utility functions for Algolia integration
 */

/**
 * Log debug messages with Algolia prefix
 * @param {string} message - The message to log
 * @param {any} [data] - Optional data to log
 */
export const debugLog = (message, data = null) => {
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
export const debugFormSubmission = (form, productData) => {
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