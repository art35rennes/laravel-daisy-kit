/**
 * DaisyKit Core - Bootstrap et router pour les modules JS
 */

// Utilitaires partagés
export * from './utils/dom.js';
export * from './utils/events.js';
export * from './utils/aria.js';

// Scanner et initialiser les modules
const initialized = new WeakSet();

// Précharger tous les modules disponibles pour que Vite puisse les résoudre
const modulesGlob = import.meta.glob('../modules/*.js', { eager: false });
const formsModulesGlob = import.meta.glob('../modules/forms/*.js', { eager: false });
const rootModulesGlob = import.meta.glob('../*.js', { eager: false });
const folderModulesGlob = import.meta.glob('../*/index.js', { eager: false });

// Créer un mapping nom du module -> fonction d'import
function createModuleMap(globMap, extractName) {
    const map = new Map();
    for (const [path, importFn] of Object.entries(globMap)) {
        const name = extractName(path);
        if (name) {
            map.set(name, importFn);
        }
    }
    return map;
}

const modulesMap = createModuleMap(modulesGlob, (path) => {
    const match = path.match(/\/modules\/([^/]+)\.js$/);
    return match ? match[1] : null;
});

const rootModulesMap = createModuleMap(rootModulesGlob, (path) => {
    const match = path.match(/\/([^/]+)\.js$/);
    return match ? match[1] : null;
});

const folderModulesMap = createModuleMap(folderModulesGlob, (path) => {
    const match = path.match(/\/([^/]+)\/index\.js$/);
    return match ? match[1] : null;
});

const formsModulesMap = createModuleMap(formsModulesGlob, (path) => {
    const match = path.match(/\/forms\/([^/]+)\.js$/);
    return match ? match[1] : null;
});

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
        let importFn = null;
        
        // Essayer d'abord dans modules/
        if (modulesMap.has(moduleName)) {
            importFn = modulesMap.get(moduleName);
        } else if (formsModulesMap.has(moduleName)) {
            // Essayer dans modules/forms/
            importFn = formsModulesMap.get(moduleName);
        } else if (rootModulesMap.has(moduleName)) {
            // Essayer à la racine
            importFn = rootModulesMap.get(moduleName);
        } else if (folderModulesMap.has(moduleName)) {
            // Essayer dans un dossier (ex: leaflet/index.js)
            importFn = folderModulesMap.get(moduleName);
        }
        
        if (importFn) {
            module = await importFn();
        } else {
            // Fallback : import dynamique classique (peut ne pas fonctionner avec Vite)
            try {
                module = await import(`../modules/${moduleName}.js`);
            } catch (e1) {
                try {
                    module = await import(`../modules/forms/${moduleName}.js`);
                } catch (e2) {
                    try {
                        module = await import(`../${moduleName}.js`);
                    } catch (e3) {
                        module = await import(`../${moduleName}/index.js`);
                    }
                }
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

