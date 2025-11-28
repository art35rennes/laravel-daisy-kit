/**
 * Daisy Kit - Form Wizard
 *
 * Module pour gérer la navigation dans un formulaire multi-étapes avec persistance.
 * Supporte plusieurs instances de wizards sur la même page via un ID unique.
 */

export default function init(element, options) {
    const form = element;
    
    // Générer un ID unique pour cette instance si non fourni
    const instanceId = form.id || form.dataset.wizardInstanceId || `wizard-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    if (!form.id) {
        form.id = instanceId;
    }
    if (!form.dataset.wizardInstanceId) {
        form.dataset.wizardInstanceId = instanceId;
    }
    
    const wizardKey = form.dataset.wizardKey || 'wizard';
    const isLinear = form.dataset.linear === 'true';
    const currentStep = parseInt(form.dataset.currentStep, 10) || 1;
    
    // Clé de stockage unique combinant wizardKey et instanceId
    const storageKey = `wizard_${wizardKey}_${instanceId}`;
    
    const stepInput = form.querySelector('input[name="_wizard_step"]');
    const prevButton = form.querySelector('[data-wizard-prev]');
    const nextButton = form.querySelector('[data-wizard-next]');
    
    /**
     * Sauvegarde les données du formulaire dans le sessionStorage.
     */
    function saveFormData() {
        const formData = new FormData(form);
        const data = {};
        
        for (const [key, value] of formData.entries()) {
            // Ignorer les champs système
            if (key.startsWith('_')) {
                continue;
            }
            data[key] = value;
        }
        
        sessionStorage.setItem(storageKey, JSON.stringify(data));
    }
    
    /**
     * Restaure les données du formulaire depuis le sessionStorage.
     */
    function restoreFormData() {
        const stored = sessionStorage.getItem(storageKey);
        
        if (!stored) {
            return;
        }
        
        try {
            const data = JSON.parse(stored);
            
            for (const [key, value] of Object.entries(data)) {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = input.value === value;
                    } else {
                        input.value = value;
                    }
                }
            }
        } catch (error) {
            console.warn(`[Form Wizard ${instanceId}] Failed to restore form data:`, error);
        }
    }
    
    /**
     * Navigue vers une étape spécifique.
     */
    function goToStep(step) {
        if (step < 1) {
            return;
        }
        
        const totalSteps = form.querySelectorAll('[data-step-content]').length;
        if (step > totalSteps) {
            return;
        }
        
        // Sauvegarder les données avant de changer d'étape
        saveFormData();
        
        // Mettre à jour l'input caché
        if (stepInput) {
            stepInput.value = step;
        }
        
        // Mettre à jour l'attribut data-current-step
        form.dataset.currentStep = step;
        
        // Déclencher un event pour que le stepper se mette à jour
        const event = new CustomEvent('wizard:step-change', {
            detail: { step, instanceId },
        });
        form.dispatchEvent(event);
    }
    
    /**
     * Va à l'étape précédente.
     */
    function goToPrevStep() {
        const current = parseInt(form.dataset.currentStep, 10) || 1;
        goToStep(current - 1);
    }
    
    /**
     * Va à l'étape suivante.
     */
    function goToNextStep() {
        const current = parseInt(form.dataset.currentStep, 10) || 1;
        goToStep(current + 1);
    }
    
    // Écouter les clics sur les boutons de navigation
    if (prevButton) {
        prevButton.addEventListener('click', (e) => {
            e.preventDefault();
            goToPrevStep();
        });
    }
    
    if (nextButton) {
        nextButton.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Valider le formulaire avant de passer à l'étape suivante
            if (form.checkValidity()) {
                saveFormData();
                goToNextStep();
            } else {
                form.reportValidity();
            }
        });
    }
    
    // Écouter les events filter-clear pour synchroniser avec d'autres composants
    form.addEventListener('filter-clear', () => {
        saveFormData();
    });
    
    // Restaurer les données au chargement
    restoreFormData();
    
    // Sauvegarder automatiquement lors des changements
    form.addEventListener('input', () => {
        saveFormData();
    });
    
    form.addEventListener('change', () => {
        saveFormData();
    });
}

