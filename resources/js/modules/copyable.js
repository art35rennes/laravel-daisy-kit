/**
 * Daisy Kit - Copyable (Greffon)
 *
 * Greffon pour ajouter la fonctionnalité de copie à n'importe quel élément HTML.
 * S'applique automatiquement aux éléments avec la classe "copyable".
 *
 * Usage :
 * <span class="copyable">Texte à copier</span>
 * <code class="copyable" data-copy-value="valeur">Texte affiché</code>
 * <div class="copyable copyable-underline" data-copy-html="true">Contenu HTML</div>
 */

/**
 * Vérifie si l'API Clipboard est disponible et si les permissions sont accordées.
 * @returns {Promise<boolean>}
 */
function canUseClipboardApi() {
    return typeof window !== 'undefined'
        && window.isSecureContext
        && navigator?.clipboard
        && (typeof navigator.clipboard.writeText === 'function' || typeof navigator.clipboard.write === 'function');
}

async function checkClipboardPermission() {
    if (!canUseClipboardApi()) {
        return false;
    }
    
    // Vérifier les permissions si l'API est disponible
    try {
        const permissionStatus = await navigator.permissions.query({ name: 'clipboard-write' });
        return permissionStatus.state !== 'denied';
    } catch {
        // Si l'API de permissions n'est pas disponible, on essaie quand même
        return true;
    }
}

/**
 * Demande la permission d'écrire dans le presse-papier en tentant une copie de test.
 * @returns {Promise<boolean>}
 */
async function requestClipboardPermission() {
    if (!canUseClipboardApi()) {
        return false;
    }
    
    try {
        // Tenter une copie vide pour déclencher la demande de permission
        await navigator.clipboard.writeText('');
        return true;
    } catch (error) {
        console.warn('[Copyable] Permission refusée pour le presse-papier:', error);
        return false;
    }
}

/**
 * Extrait le texte brut d'une chaîne HTML.
 * @param {string} html
 * @returns {string}
 */
function extractTextFromHtml(html) {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
}

/**
 * Extrait le texte brut d'un élément HTML.
 * @param {HTMLElement} element
 * @returns {string}
 */
function extractTextContent(element) {
    if (!element) return '';
    
    // Cloner l'élément pour ne pas modifier l'original
    const clone = element.cloneNode(true);
    
    // Supprimer les éléments ajoutés par le greffon
    clone.querySelectorAll('.copyable-icon, .copyable-feedback, [data-copyable-ignore]').forEach(el => el.remove());
    
    return clone.textContent?.trim() || clone.innerText?.trim() || '';
}

/**
 * Extrait le HTML d'un élément (pour copier du contenu complexe).
 * @param {HTMLElement} element
 * @returns {string}
 */
function extractHtmlContent(element) {
    if (!element) return '';
    
    const clone = element.cloneNode(true);
    
    // Supprimer les éléments ajoutés par le greffon
    clone.querySelectorAll('.copyable-icon, .copyable-feedback, [data-copyable-ignore]').forEach(el => el.remove());
    
    return clone.innerHTML.trim();
}

/**
 * Crée l'icône de copie.
 * @param {string} size - Taille de l'icône (xs, sm, md, lg, xl)
 * @returns {HTMLElement}
 */
function createIcon(size = 'sm') {
    const iconSizeMap = {
        xs: { className: 'w-3 h-3', dimension: '0.75rem' },
        sm: { className: 'w-4 h-4', dimension: '1rem' },
        md: { className: 'w-5 h-5', dimension: '1.25rem' },
        lg: { className: 'w-6 h-6', dimension: '1.5rem' },
        xl: { className: 'w-8 h-8', dimension: '2rem' },
    };
    
    const iconSize = iconSizeMap[size] || iconSizeMap.sm;
    
    const icon = document.createElement('span');
    const baseClasses = `copyable-icon inline-flex items-center justify-center ${iconSize.className} text-base-content/60`;
    icon.className = baseClasses;
    icon.setAttribute('aria-hidden', 'true');
    icon.setAttribute('data-copyable-ignore', 'true');
    icon.dataset.iconSize = size;
    icon.dataset.iconDimension = iconSize.dimension;
    
    const iconSvg = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" class="${iconSize.className}">
            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
        </svg>
    `;
    icon.innerHTML = iconSvg;
    
    return icon;
}

/**
 * Crée l'élément de feedback.
 * @param {string} message - Message par défaut
 * @returns {HTMLElement}
 */
function createFeedback(message = 'Copié!') {
    const feedback = document.createElement('span');
    feedback.className = 'copyable-feedback hidden absolute -top-8 left-1/2 -translate-x-1/2 bg-base-content text-base-100 px-2 py-1 rounded text-xs whitespace-nowrap z-50';
    feedback.setAttribute('data-copyable-ignore', 'true');
    feedback.textContent = message;
    return feedback;
}

/**
 * Copie du texte dans le presse-papier.
 * @param {string} text
 * @returns {Promise<boolean>}
 */
async function copyToClipboard(text) {
    if (navigator.clipboard?.writeText) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (error) {
            console.error('[Copyable] Erreur lors de la copie:', error);
        }
    }
    
    return fallbackCopyText(text);
}

/**
 * Copie du HTML dans le presse-papier (utilise l'API ClipboardItem).
 * @param {string} html
 * @returns {Promise<boolean>}
 */
async function copyHtmlToClipboard(html) {
    if (typeof ClipboardItem !== 'undefined' && navigator.clipboard?.write) {
        try {
            const clipboardItem = new ClipboardItem({
                'text/html': new Blob([html], { type: 'text/html' }),
                'text/plain': new Blob([extractTextFromHtml(html)], { type: 'text/plain' }),
            });

            await navigator.clipboard.write([clipboardItem]);
            return true;
        } catch (error) {
            console.warn('[Copyable] Impossible de copier le HTML, fallback sur texte brut:', error);
        }
    }

    return await copyToClipboard(extractTextFromHtml(html));
}

/**
 * Fallback pour copier du texte en utilisant document.execCommand.
 * @param {string} text
 * @returns {boolean}
 */
function fallbackCopyText(text) {
    try {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'absolute';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        const successful = document.execCommand('copy');
        document.body.removeChild(textarea);
        return successful;
    } catch (error) {
        console.error('[Copyable] Fallback copy failed:', error);
        return false;
    }
}

/**
 * Affiche un message de feedback.
 * @param {HTMLElement} element
 * @param {string} message
 * @param {boolean} isError
 */
function showFeedback(element, message, isError = false) {
    const feedback = element.querySelector('.copyable-feedback');
    if (!feedback) return;
    
    // Mettre à jour le message si fourni
    if (message) {
        feedback.textContent = message;
    }
    
    // Ajouter une classe d'erreur si nécessaire
    if (isError) {
        feedback.classList.add('bg-error', 'text-error-content');
        feedback.classList.remove('bg-base-content', 'text-base-100');
    } else {
        feedback.classList.remove('bg-error', 'text-error-content');
        feedback.classList.add('bg-base-content', 'text-base-100');
    }
    
    // Afficher le feedback
    feedback.classList.remove('hidden');
    feedback.classList.add('show');
    
    // Masquer après l'animation
    setTimeout(() => {
        feedback.classList.remove('show');
        setTimeout(() => {
            feedback.classList.add('hidden');
        }, 2000);
    }, 2000);
}

/**
 * Initialise le greffon copyable sur un élément.
 * @param {HTMLElement} element - Élément avec la classe "copyable"
 */
function initCopyableElement(element) {
    // Éviter la double initialisation
    if (element.dataset.copyableInitialized === 'true') {
        return;
    }
    
    // Marquer comme initialisé
    element.dataset.copyableInitialized = 'true';
    
    // Rendre l'élément interactif
    element.setAttribute('role', 'button');
    element.setAttribute('tabindex', '0');
    if (!element.getAttribute('aria-label')) {
        element.setAttribute('aria-label', 'Copier dans le presse-papier');
    }
    
    // Ajouter la ligne pointillée si la classe copyable-underline est présente
    if (element.classList.contains('copyable-underline')) {
        element.classList.add('border-b', 'border-dashed', 'border-base-content/30', 'hover:border-base-content/60', 'transition-colors');
    }
    
    // Récupérer les options depuis les attributs data-*
    const copyValue = element.dataset.copyValue;
    const copyHtml = element.dataset.copyHtml === 'true';
    const iconSize = element.dataset.iconSize || 'sm';
    const iconPosition = element.dataset.iconPosition || 'right';
    const successMessage = element.dataset.successMessage;
    const errorMessage = element.dataset.errorMessage;
    
    // Ajouter les classes de base si nécessaire
    if (!element.classList.contains('inline-flex')) {
        element.classList.add('inline-flex');
    }
    if (!element.classList.contains('items-center')) {
        element.classList.add('items-center');
    }
    if (!element.classList.contains('flex-wrap')) {
        element.classList.add('flex-wrap');
    }
    if (!element.classList.contains('flex-wrap')) {
        element.classList.add('flex-wrap');
    }
    
    // Créer et ajouter l'icône
    const icon = createIcon(iconSize);
    if (!element.style.getPropertyValue('--copyable-icon-size')) {
        element.style.setProperty('--copyable-icon-size', icon.dataset.iconDimension || '1rem');
    }
    
    // Positionner l'icône selon iconPosition
    if (iconPosition === 'left') {
        element.classList.add('relative', 'copyable-has-icon-left');
        element.insertBefore(icon, element.firstChild);
        icon.classList.add('copyable-icon-left', 'absolute', 'top-1/2', '-translate-y-1/2', 'left-0');
    } else if (iconPosition === 'inline') {
        // Pour inline, l'icône reste dans le flux normal
        element.appendChild(icon);
        icon.classList.add('ml-1', 'copyable-inline-icon');
    } else {
        element.classList.add('relative', 'copyable-has-icon-right');
        element.appendChild(icon);
        icon.classList.add('copyable-icon-right', 'absolute', 'top-1/2', '-translate-y-1/2', 'right-0');
    }
    
    // Créer et ajouter le feedback
    const feedback = createFeedback(successMessage || 'Copié!');
    element.appendChild(feedback);
    
    // Ajouter les styles CSS si pas déjà présents
    if (!document.getElementById('copyable-styles')) {
        const style = document.createElement('style');
        style.id = 'copyable-styles';
        style.textContent = `
            .copyable-has-icon-right,
            .copyable-has-icon-left {
                transition: padding 150ms ease;
            }
            .copyable-has-icon-right .copyable-icon,
            .copyable-has-icon-left .copyable-icon {
                opacity: 0;
                pointer-events: none;
                transition: opacity 150ms ease;
            }
            .copyable:focus-visible {
                outline: 2px solid hsl(var(--p));
                outline-offset: 2px;
            }
            .copyable-feedback.show {
                display: block;
                animation: copyableFadeInOut 2s ease-in-out;
            }
            .copyable-icon-left {
                left: 0;
            }
            .copyable-icon-right {
                right: 0;
            }
            .copyable-has-icon-right:is(:hover, :focus-visible) {
                padding-right: calc(var(--copyable-icon-size, 1rem) + 0.3rem);
            }
            .copyable-has-icon-left:is(:hover, :focus-visible) {
                padding-left: calc(var(--copyable-icon-size, 1rem) + 0.3rem);
            }
            .copyable-has-icon-right:is(:hover, :focus-visible) .copyable-icon,
            .copyable-has-icon-left:is(:hover, :focus-visible) .copyable-icon {
                opacity: 1;
            }
            .copyable-inline-icon {
                opacity: 0;
                pointer-events: none;
                transition: opacity 150ms ease;
            }
            .copyable:hover .copyable-inline-icon,
            .copyable:focus-visible .copyable-inline-icon {
                opacity: 1;
            }
            @keyframes copyableFadeInOut {
                0%, 100% { opacity: 0; transform: translate(-50%, -4px); }
                10%, 90% { opacity: 1; transform: translate(-50%, 0); }
            }
        `;
        document.head.appendChild(style);
    }
    
    /**
     * Gère le clic ou l'appui sur Entrée/Espace pour copier.
     */
    const handleCopy = async (e) => {
        // Si on clique sur l'icône ou un élément ignoré, on copie quand même
        if (e.target.closest('[data-copyable-ignore]')) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Vérifier les permissions uniquement si l'API clipboard est disponible
        const clipboardApiAvailable = canUseClipboardApi();
        if (clipboardApiAvailable) {
            const hasPermission = await checkClipboardPermission();
            if (!hasPermission) {
                const granted = await requestClipboardPermission();
                if (!granted) {
                    showFeedback(element, errorMessage || 'Permission refusée pour accéder au presse-papier', true);
                    return;
                }
            }
        }
        
        let textToCopy = copyValue;
        
        // Si aucune valeur explicite, extraire du contenu
        if (!textToCopy) {
            if (copyHtml) {
                textToCopy = extractHtmlContent(element);
            } else {
                textToCopy = extractTextContent(element);
            }
        }
        
        if (!textToCopy) {
            showFeedback(element, errorMessage || 'Aucun contenu à copier', true);
            return;
        }
        
        // Copier selon le type
        let success = false;
        if (copyHtml && !copyValue) {
            // Copier le HTML si demandé et si on n'a pas de valeur explicite
            success = await copyHtmlToClipboard(textToCopy);
        } else {
            // Copier le texte brut
            success = await copyToClipboard(textToCopy);
        }
        
        if (success) {
            showFeedback(element, successMessage || 'Copié!');
        } else {
            showFeedback(element, errorMessage || 'Erreur lors de la copie', true);
        }
    };
    
    // Gérer le clic
    element.addEventListener('click', handleCopy);
    
    // Gérer le clavier (Entrée et Espace)
    element.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            handleCopy(e);
        }
    });
}

/**
 * Initialise tous les éléments avec la classe "copyable".
 * @param {HTMLElement|Document} container - Conteneur à scanner (par défaut: document)
 */
function initAllCopyables(container = document) {
    const elements = container.querySelectorAll('.copyable:not([data-copyable-initialized="true"])');
    elements.forEach(initCopyableElement);
}

/**
 * Initialise le greffon copyable.
 * Peut être appelé via data-module="copyable" ou automatiquement pour tous les .copyable
 */
export default function initCopyable(root, options = {}) {
    // Si root a la classe copyable, l'initialiser directement
    if (root.classList.contains('copyable')) {
        initCopyableElement(root);
        return;
    }
    
    // Sinon, scanner tous les .copyable dans root
    initAllCopyables(root);
}

/**
 * Demande l'autorisation du presse-papier au chargement si nécessaire.
 */
async function requestClipboardPermissionOnLoad() {
    // Vérifier s'il y a des éléments copyable sur la page
    const hasCopyableElements = document.querySelectorAll('.copyable').length > 0;
    
    if (!hasCopyableElements) {
        return;
    }
    
    const clipboardAvailable = canUseClipboardApi();
    if (!clipboardAvailable) {
        console.warn('[Copyable] Clipboard API indisponible (page non sécurisée ?). Passage en fallback.');
        return;
    }
    
    // Vérifier les permissions actuelles
    try {
        const permissionStatus = await navigator.permissions.query({ name: 'clipboard-write' });
        
        if (permissionStatus.state === 'granted' || permissionStatus.state === 'prompt') {
            if (permissionStatus.state === 'prompt') {
                try {
                    await navigator.clipboard.writeText('');
                } catch {
                    // Ignorer les erreurs silencieusement
                }
            }
            return;
        }
        
        if (permissionStatus.state === 'denied') {
            return;
        }
    } catch {
        if (canUseClipboardApi()) {
            try {
                await navigator.clipboard.writeText('');
            } catch {
                // Ignorer les erreurs silencieusement
            }
        }
    }
}

// Auto-initialisation au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', async () => {
        await requestClipboardPermissionOnLoad();
        initAllCopyables();
    });
} else {
    requestClipboardPermissionOnLoad().then(() => {
        initAllCopyables();
    });
}

// Observer les mutations DOM pour initialiser les nouveaux éléments
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    // Si le node lui-même a la classe copyable
                    if (node.classList && node.classList.contains('copyable')) {
                        initCopyableElement(node);
                    }
                    // Scanner les enfants
                    initAllCopyables(node);
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

// Exposer la fonction pour initialisation manuelle
if (typeof window !== 'undefined') {
    window.DaisyKit = window.DaisyKit || {};
    window.DaisyKit.initCopyable = initCopyableElement;
    window.DaisyKit.initAllCopyables = initAllCopyables;
}
