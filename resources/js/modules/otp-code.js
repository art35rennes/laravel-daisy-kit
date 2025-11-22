/**
 * Daisy Kit - OTP Code Input
 *
 * Gère la saisie d'un code OTP avec navigation automatique entre les champs.
 * Chaque champ accepte un seul caractère (par défaut) ou peut être restreint aux chiffres.
 *
 * API (data-attributes) :
 * - container: [data-module="otp-code"]
 * - data-length: nombre de champs (défaut: 6)
 * - data-numeric-only: restreindre aux chiffres uniquement (défaut: false)
 * - data-auto-submit: soumettre automatiquement quand le code est complet (défaut: false)
 * - data-hidden-input-name: nom du champ caché pour la soumission (défaut: "code")
 */

export default function initOtpCode(container, options = {}) {
    const length = Number.parseInt(options.length ?? container.dataset.length ?? 6, 10) || 6;
    const numericOnly = String(options.numericOnly ?? container.dataset.numericOnly ?? 'false') === 'true';
    const autoSubmit = String(options.autoSubmit ?? container.dataset.autoSubmit ?? 'false') === 'true';
    const hiddenInputName = String(options.hiddenInputName ?? container.dataset.hiddenInputName ?? 'code');
    
    // Trouver tous les inputs OTP
    const inputs = Array.from(container.querySelectorAll('input[data-otp-digit]'));
    
    if (inputs.length !== length) {
        console.warn(`[DaisyKit OTP] Expected ${length} inputs, found ${inputs.length}`);
        return;
    }
    
    // Créer ou trouver le champ caché pour la soumission
    let hiddenInput = container.querySelector(`input[name="${hiddenInputName}"][type="hidden"]`);
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = hiddenInputName;
        container.appendChild(hiddenInput);
    }
    
    // Trouver le formulaire parent
    const form = container.closest('form');
    
    /**
     * Met à jour la valeur du champ caché avec tous les caractères
     */
    function updateHiddenValue() {
        const code = inputs.map(input => input.value || '').join('');
        hiddenInput.value = code;
        
        // Déclencher un événement personnalisé
        container.dispatchEvent(new CustomEvent('otp:change', {
            detail: { code, complete: code.length === length }
        }));
        
        // Soumission automatique si activée et code complet
        if (autoSubmit && code.length === length && form) {
            container.dispatchEvent(new CustomEvent('otp:complete', { detail: { code } }));
            // Optionnel: soumettre automatiquement après un court délai
            setTimeout(() => {
                if (form && code.length === length) {
                    form.requestSubmit();
                }
            }, 300);
        }
    }
    
    /**
     * Focus sur un input spécifique
     */
    function focusInput(index) {
        if (index >= 0 && index < inputs.length) {
            inputs[index].focus();
        }
    }
    
    /**
     * Trouve le premier input vide
     */
    function findFirstEmpty() {
        return inputs.findIndex(input => !input.value);
    }
    
    /**
     * Trouve le dernier input rempli
     */
    function findLastFilled() {
        for (let i = inputs.length - 1; i >= 0; i--) {
            if (inputs[i].value) {
                return i;
            }
        }
        return -1;
    }
    
    // Initialiser chaque input
    inputs.forEach((input, index) => {
        // S'assurer que chaque input accepte un seul caractère
        input.setAttribute('maxlength', '1');
        
        // Configurer selon le mode (numérique ou alphanumérique)
        if (numericOnly) {
            input.setAttribute('inputmode', 'numeric');
            input.setAttribute('pattern', '[0-9]');
        }
        input.setAttribute('autocomplete', index === 0 ? 'one-time-code' : 'off');
        
        // Gérer la saisie
        input.addEventListener('input', (e) => {
            const value = e.target.value;
            
            // Filtrer selon le mode
            let char = '';
            if (numericOnly) {
                // Ne garder que le premier chiffre
                char = value.replace(/[^0-9]/g, '').charAt(0);
            } else {
                // Accepter n'importe quel caractère (premier caractère seulement)
                char = value.charAt(0);
            }
            e.target.value = char;
            
            updateHiddenValue();
            
            // Aller au champ suivant si un caractère a été saisi
            if (char && index < inputs.length - 1) {
                focusInput(index + 1);
            }
        });
        
        // Gérer le collage (paste)
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            
            // Filtrer selon le mode
            let chars = '';
            if (numericOnly) {
                chars = pasted.replace(/[^0-9]/g, '').slice(0, length);
            } else {
                chars = pasted.slice(0, length);
            }
            
            // Remplir les champs avec les caractères collés
            chars.split('').forEach((char, i) => {
                if (index + i < inputs.length) {
                    inputs[index + i].value = char;
                }
            });
            
            updateHiddenValue();
            
            // Focus sur le premier champ vide ou le dernier
            const nextEmpty = findFirstEmpty();
            if (nextEmpty >= 0) {
                focusInput(nextEmpty);
            } else {
                focusInput(inputs.length - 1);
            }
        });
        
        // Gérer les touches de navigation
        input.addEventListener('keydown', (e) => {
            // Backspace: effacer et aller au champ précédent
            if (e.key === 'Backspace') {
                if (!e.target.value && index > 0) {
                    e.preventDefault();
                    inputs[index - 1].value = '';
                    focusInput(index - 1);
                    updateHiddenValue();
                } else if (e.target.value) {
                    // Si le champ a une valeur, l'effacer d'abord
                    e.target.value = '';
                    updateHiddenValue();
                }
            }
            // Flèche gauche: aller au champ précédent
            else if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                focusInput(index - 1);
            }
            // Flèche droite: aller au champ suivant
            else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                e.preventDefault();
                focusInput(index + 1);
            }
            // Supprimer: effacer le champ actuel
            else if (e.key === 'Delete') {
                e.target.value = '';
                updateHiddenValue();
            }
        });
        
        // Gérer le focus: sélectionner le contenu pour faciliter la saisie
        input.addEventListener('focus', (e) => {
            e.target.select();
        });
    });
    
    // Initialiser la valeur cachée
    updateHiddenValue();
    
    // Focus automatique sur le premier champ au chargement
    if (inputs[0] && !inputs.some(input => input.value)) {
        inputs[0].focus();
    }
}

