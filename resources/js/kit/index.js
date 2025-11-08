/**
 * DaisyKit Core - Bootstrap et router pour les modules JS
 */

// Utilitaires partagés
export * from './utils/dom.js';
export * from './utils/events.js';
export * from './utils/aria.js';

// Scanner et initialiser les modules
const initialized = new WeakSet();

/**
 * Initialise un élément avec son module JS
 */
async function initElement(element) {
    if (initialized.has(element)) {
        return;
    }

    const moduleName = element.dataset.module;
    if (!moduleName) {
        return;
    }

    try {
        // Import dynamique du module avec fallback
        let module;
        try {
            module = await import(`../modules/${moduleName}.js`);
        } catch (e1) {
            try {
                module = await import(`../${moduleName}.js`);
            } catch (e2) {
                // Support des modules index.js dans des dossiers (ex: leaflet/index.js)
                module = await import(`../${moduleName}/index.js`);
            }
        }
        
        // Extraire les options depuis les data-attributes
        const options = extractOptions(element);
        
        // Initialiser le module
        if (module.default && typeof module.default === 'function') {
            module.default(element, options);
        } else if (typeof module.init === 'function') {
            module.init(element, options);
        }
        
        initialized.add(element);
    } catch (error) {
        console.warn(`[DaisyKit] Failed to load module "${moduleName}":`, error);
    }
}

/**
 * Extrait les options depuis les data-attributes d'un élément
 */
function extractOptions(element) {
    const options = {};
    
    for (const [key, value] of Object.entries(element.dataset)) {
        // Ignorer 'module' qui est réservé
        if (key === 'module') {
            continue;
        }
        
        // Convertir kebab-case en camelCase
        const camelKey = key.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());
        
        // Parser les valeurs JSON si possible
        try {
            options[camelKey] = JSON.parse(value);
        } catch {
            // Sinon, utiliser la valeur telle quelle
            options[camelKey] = value === 'true' ? true : value === 'false' ? false : value;
        }
    }
    
    return options;
}

/**
 * Initialise tous les éléments avec data-module
 */
export function init() {
    const elements = document.querySelectorAll('[data-module]');
    elements.forEach(initElement);
}

/**
 * Réinitialise après des mutations DOM (pour le lazy-loading)
 */
export function reinit(container = document) {
    const elements = container.querySelectorAll('[data-module]:not([data-initialized])');
    elements.forEach(initElement);
}

// Auto-initialisation au chargement
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// Observer les mutations DOM pour le lazy-loading
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    reinit(node);
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

// Exposer globalement pour usage manuel
if (typeof window !== 'undefined') {
    window.DaisyKit = {
        init,
        reinit,
    };
}

