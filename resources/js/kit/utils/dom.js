/**
 * Utilitaires DOM
 */

/**
 * Trouve un élément parent correspondant au sélecteur
 */
export function closest(element, selector) {
    return element.closest(selector);
}

/**
 * Trouve tous les éléments correspondant au sélecteur dans le conteneur
 */
export function findAll(container, selector) {
    return Array.from(container.querySelectorAll(selector));
}

/**
 * Trouve un élément correspondant au sélecteur dans le conteneur
 */
export function findOne(container, selector) {
    return container.querySelector(selector);
}

/**
 * Vérifie si un élément est visible
 */
export function isVisible(element) {
    if (!element) {
        return false;
    }
    
    const style = window.getComputedStyle(element);
    return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
}

/**
 * Déclenche un événement personnalisé
 */
export function dispatch(element, eventName, detail = {}) {
    const event = new CustomEvent(eventName, {
        detail,
        bubbles: true,
        cancelable: true,
    });
    element.dispatchEvent(event);
    return event;
}

