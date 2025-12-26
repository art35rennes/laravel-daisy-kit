/**
 * Daisy Kit - Sidebar
 *
 * Ce module gère la sidebar (collapse, resize, etc.).
 * Le filtrage est maintenant géré par le module menu-filter générique.
 *
 * Structure HTML requise :
 * <aside data-module="sidebar">
 *   <ul data-sidebar-menu>
 *     ...
 *   </ul>
 * </aside>
 *
 * Note: Si searchable est activé, le composant sidebar utilise maintenant
 * le module menu-filter au lieu de cette logique spécifique.
 */

/**
 * Initialise le module sidebar
 * 
 * Note: Le filtrage est maintenant géré par le module menu-filter.
 * Ce module se concentre uniquement sur la gestion du collapse/resize.
 */
function initSidebar(root, options = {}) {
    // Le filtrage est maintenant géré par menu-filter
    // Ce module peut être étendu pour d'autres fonctionnalités de sidebar
    // (collapse, resize, etc.)
    
    // Pour compatibilité, on ne fait rien si l'ancien système est utilisé
    // mais on préfère utiliser menu-filter directement
    const searchInput = root.querySelector('[data-sidebar-search]');
    if (searchInput) {
        console.warn('[DaisyKit] sidebar.js: Le filtrage est maintenant géré par menu-filter. Utilisez data-module="menu-filter" sur le conteneur du filtre.');
    }
}

// Export par défaut pour le système data-module
export default initSidebar;

// Export nommé pour compatibilité
export { initSidebar };

