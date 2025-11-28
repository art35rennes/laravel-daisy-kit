/**
 * Daisy Kit - Form Tabs
 *
 * Module pour gérer la persistance de l'onglet actif dans un formulaire à onglets.
 * Supporte plusieurs instances de formulaires à onglets sur la même page via un ID unique.
 */

export default function init(element, options) {
    const form = element;
    
    // Générer un ID unique pour cette instance si non fourni
    const instanceId = form.id || form.dataset.tabsInstanceId || `form-tabs-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    if (!form.id) {
        form.id = instanceId;
    }
    if (!form.dataset.tabsInstanceId) {
        form.dataset.tabsInstanceId = instanceId;
    }
    
    const persistField = form.dataset.persistField || '_active_tab';
    const hiddenInput = form.querySelector(`input[name="${persistField}"]`);
    
    if (!hiddenInput) {
        return;
    }

    /**
     * Met à jour le champ caché avec l'ID de l'onglet actif.
     */
    function updateActiveTab() {
        // Trouver l'onglet radio actif
        const activeRadio = form.querySelector('input[type="radio"].tab:checked');
        if (!activeRadio) {
            return;
        }

        const tabId = activeRadio.dataset.tabId;
        if (tabId) {
            hiddenInput.value = tabId;
        }
    }

    /**
     * Affiche le contenu de l'onglet actif et cache les autres.
     * Note: Le composant tabs de daisyUI gère déjà l'affichage via les radios,
     * mais on doit s'assurer que le champ caché est synchronisé.
     */
    function syncTabContent() {
        // Le composant tabs gère déjà l'affichage, on synchronise juste le champ caché
        updateActiveTab();
    }

    // Écouter les changements d'onglets
    const radioInputs = form.querySelectorAll('input[type="radio"].tab');
    radioInputs.forEach(radio => {
        radio.addEventListener('change', () => {
            updateActiveTab();
            syncTabContent();
        });
    });

    // Initialiser au chargement
    updateActiveTab();
    syncTabContent();
}

