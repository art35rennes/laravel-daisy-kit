/**
 * Daisy Kit - Form Inline
 *
 * Module pour gérer les filtres inline avec suppression de tokens actifs.
 * Supporte plusieurs instances de formulaires inline sur la même page via un ID unique.
 */

export default function init(element, options) {
    const form = element;
    
    // Générer un ID unique pour cette instance si non fourni
    const instanceId = form.id || form.dataset.inlineInstanceId || `form-inline-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    if (!form.id) {
        form.id = instanceId;
    }
    if (!form.dataset.inlineInstanceId) {
        form.dataset.inlineInstanceId = instanceId;
    }
    
    const action = form.action;
    
    /**
     * Supprime un filtre et redirige sans le paramètre.
     */
    function clearFilter(param) {
        if (!param) {
            return;
        }

        const url = new URL(action, window.location.origin);
        url.searchParams.delete(param);
        
        // Rediriger vers la nouvelle URL
        window.location.href = url.toString();
    }

    // Écouter les clics sur les boutons de suppression de filtre
    const clearButtons = form.querySelectorAll('[data-filter-clear]');
    clearButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const param = button.dataset.filterParam;
            clearFilter(param);
        });
    });

    // Écouter les events filter-clear depuis l'extérieur
    form.addEventListener('filter-clear', (e) => {
        const param = e.detail?.param;
        if (param) {
            clearFilter(param);
        }
    });
}

