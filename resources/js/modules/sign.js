/**
 * Daisy Kit - Signature Pad
 *
 * Composant de signature responsive utilisant signature_pad
 * Supporte le dessin au doigt/stylet sur mobile et souris sur desktop
 *
 * Data-attributes (sur le root [data-sign="1"]):
 * - data-width: largeur du canvas (défaut: 400)
 * - data-height: hauteur du canvas (défaut: 200)
 * - data-pen-color: couleur de l'encre (défaut: #000000)
 * - data-min-width: épaisseur minimale du trait (défaut: 0.5)
 * - data-max-width: épaisseur maximale du trait (défaut: 2.5)
 * - data-velocity-filter-weight: vitesse de dessin (défaut: 0.7)
 * - data-responsive: ajuste automatiquement la taille (défaut: true)
 * - data-disabled: désactive le composant (défaut: false)
 * - data-show-actions: affiche les boutons d'action (défaut: true)
 * - data-download-format: format de téléchargement (png|jpg|svg, défaut: png)
 * - data-download-filename: nom du fichier (défaut: signature)
 *
 * Événements:
 * - 'sign:change' - déclenché quand la signature change (detail.isEmpty, detail.dataURL)
 * - 'sign:clear' - déclenché quand la signature est effacée
 * - 'sign:end' - déclenché à la fin d'un trait
 *
 * API: window.DaisySign.{ init(root), initAll() }
 */

import SignaturePad from 'signature_pad';

/**
 * Initialise un composant de signature
 * @param {HTMLElement} root - Élément racine du composant
 * @param {object} options - Options depuis les data-attributes
 */
export default function initSign(root, options = {}) {
    if (!root || root.__signInit) {
        return;
    }
    root.__signInit = true;

    const canvas = root.querySelector('[data-sign-canvas]');
    const canvasWrapper = root.querySelector('[data-sign-canvas-wrapper]');
    const input = root.querySelector('[data-sign-input]');
    const clearBtn = root.querySelector('[data-sign-clear]');
    const downloadBtn = root.querySelector('[data-sign-download]');

    if (!canvas) {
        console.warn('[DaisySign] Canvas not found');
        return;
    }

    // Configuration depuis les data-attributes ou options
    const config = {
        width: parseInt(root.dataset.width || options.width || 400),
        height: parseInt(root.dataset.height || options.height || 200),
        penColor: root.dataset.penColor || options.penColor || '#000000',
        minWidth: parseFloat(root.dataset.minWidth || options.minWidth || 0.5),
        maxWidth: parseFloat(root.dataset.maxWidth || options.maxWidth || 2.5),
        velocityFilterWeight: parseFloat(root.dataset.velocityFilterWeight || options.velocityFilterWeight || 0.7),
        responsive: root.dataset.responsive !== 'false' && options.responsive !== false,
        disabled: root.dataset.disabled === 'true' || options.disabled === true,
        showActions: root.dataset.showActions !== 'false' && options.showActions !== false,
        downloadFormat: root.dataset.downloadFormat || options.downloadFormat || 'png',
        downloadFilename: root.dataset.downloadFilename || options.downloadFilename || 'signature',
    };

    // Configuration du canvas
    const setCanvasSize = () => {
        if (config.responsive) {
            // Mode responsive : s'adapte à la largeur du conteneur
            const containerWidth = canvasWrapper.clientWidth;
            const aspectRatio = config.width / config.height;
            
            canvas.width = containerWidth;
            canvas.height = Math.round(containerWidth / aspectRatio);
            canvas.style.width = '100%';
            canvas.style.height = 'auto';
        } else {
            // Mode fixe : utilise les dimensions spécifiées
            canvas.width = config.width;
            canvas.height = config.height;
            canvas.style.width = `${config.width}px`;
            canvas.style.height = `${config.height}px`;
        }
    };

    // Initialisation de la taille
    setCanvasSize();

    // Gestion du redimensionnement responsive
    if (config.responsive) {
        const resizeObserver = new ResizeObserver(() => {
            if (signaturePad) {
                signaturePad.clear();
                setCanvasSize();
            }
        });
        resizeObserver.observe(canvasWrapper);
    }

    // Initialisation de SignaturePad
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'transparent',
        penColor: config.penColor,
        minWidth: config.minWidth,
        maxWidth: config.maxWidth,
        velocityFilterWeight: config.velocityFilterWeight,
        throttle: 16, // ~60fps
    });

    // Désactiver si nécessaire
    if (config.disabled) {
        signaturePad.off();
        if (clearBtn) clearBtn.disabled = true;
        if (downloadBtn) downloadBtn.disabled = true;
    }

    // Mise à jour de l'input hidden et déclenchement d'événements
    const updateInput = () => {
        const isEmpty = signaturePad.isEmpty();
        const dataURL = isEmpty ? '' : signaturePad.toDataURL();
        
        if (input) {
            input.value = dataURL;
        }

        // Déclencher l'événement de changement
        root.dispatchEvent(new CustomEvent('sign:change', {
            detail: { isEmpty, dataURL },
            bubbles: true,
        }));
    };

    // Événements de SignaturePad
    signaturePad.addEventListener('endStroke', () => {
        updateInput();
        root.dispatchEvent(new CustomEvent('sign:end', {
            detail: { isEmpty: signaturePad.isEmpty() },
            bubbles: true,
        }));
    });

    // Bouton effacer
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            signaturePad.clear();
            updateInput();
            root.dispatchEvent(new CustomEvent('sign:clear', {
                bubbles: true,
            }));
        });
    }

    // Bouton télécharger
    if (downloadBtn) {
        downloadBtn.addEventListener('click', () => {
            if (signaturePad.isEmpty()) {
                return;
            }

            const dataURL = signaturePad.toDataURL(`image/${config.downloadFormat}`);
            const link = document.createElement('a');
            link.href = dataURL;
            link.download = `${config.downloadFilename}.${config.downloadFormat}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

    // Exposer l'API sur l'élément root
    root.__signaturePad = signaturePad;
    root.clearSignature = () => {
        signaturePad.clear();
        updateInput();
    };
    root.getSignature = () => {
        return signaturePad.isEmpty() ? '' : signaturePad.toDataURL();
    };
    root.isEmpty = () => signaturePad.isEmpty();
    root.setPenColor = (color) => {
        signaturePad.penColor = color;
    };
    root.setMinWidth = (width) => {
        signaturePad.minWidth = width;
    };
    root.setMaxWidth = (width) => {
        signaturePad.maxWidth = width;
    };
}

/**
 * Initialise tous les composants de signature présents dans le DOM
 */
export function initAllSigns() {
    const elements = document.querySelectorAll('[data-sign="1"]');
    elements.forEach((el) => {
        if (!el.__signInit) {
            initSign(el);
        }
    });
}

// Expose l'API globale DaisySign
if (typeof window !== 'undefined') {
    window.DaisySign = {
        init: initSign,
        initAll: initAllSigns,
    };
}

