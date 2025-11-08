/**
 * Utilitaires ARIA et accessibilité
 */

/**
 * Gère le focus trap dans un conteneur
 */
export function trapFocus(container) {
    const focusableElements = container.querySelectorAll(
        'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
    );
    
    if (focusableElements.length === 0) {
        return null;
    }
    
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    const handleTab = (e) => {
        if (e.key !== 'Tab') {
            return;
        }
        
        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            }
        } else {
            if (document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    };
    
    container.addEventListener('keydown', handleTab);
    firstElement.focus();
    
    return () => {
        container.removeEventListener('keydown', handleTab);
    };
}

/**
 * Vérifie si un clic est en dehors d'un élément
 */
export function isOutsideClick(event, element) {
    return !element.contains(event.target);
}

/**
 * Ferme un élément lors d'un clic extérieur
 */
export function closeOnOutsideClick(element, callback) {
    const handleClick = (event) => {
        if (isOutsideClick(event, element)) {
            callback();
        }
    };
    
    // Utiliser capture pour attraper avant les autres handlers
    document.addEventListener('click', handleClick, true);
    
    return () => {
        document.removeEventListener('click', handleClick, true);
    };
}

