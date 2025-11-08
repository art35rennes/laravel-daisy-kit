/**
 * Utilitaires d'événements
 */

/**
 * Délégation d'événements
 */
export function delegate(container, selector, eventType, handler) {
    container.addEventListener(eventType, (event) => {
        const target = event.target.closest(selector);
        if (target) {
            handler.call(target, event);
        }
    });
}

/**
 * Debounce une fonction
 */
export function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle une fonction
 */
export function throttle(func, limit) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => {
                inThrottle = false;
            }, limit);
        }
    };
}

