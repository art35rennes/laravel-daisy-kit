/**
 * Daisy Kit - Color Picker (HSLA)
 *
 * Sélecteur de couleur léger avec sliders H/S/L/A, palette de swatches et options de format (HSLA/RGBA/HEX).
 *
 * Data-attributes (sur le root [data-colorpicker="1"]):
 * - data-value: valeur initiale (#hex, rgba(), hsla())
 * - data-showInputs, data-showFormatToggle, data-showHue, data-showAlpha, data-showPalette
 * - data-swatches: JSON d'un tableau de lignes de couleurs [["#fff", "#000"], ...]
 * - data-swatches-height: hauteur max en px de la palette scrollable
 * - Sorties facultatives: [data-colorchip], [data-colortext] pour refléter la valeur
 *
 * Evénement: 'colorpicker:change' (detail.value = hsla(...))
 * API: window.DaisyColorPicker.{ init(root), initAll() }
 */

// Limite une valeur n entre min et max
function clamp(n, min, max) { 
  return Math.min(max, Math.max(min, n)); 
}

/**
 * Parse une chaîne de couleur (hex, rgba, hsla) et retourne un objet {h, s, l, a}
 * @param {string} str - La couleur à parser
 * @returns {object} - Objet couleur {h, s, l, a}
 */
function parseColor(str) {
  if (!str) return { h: 270, s: 50, l: 50, a: 1 };
  
  const hex = String(str).trim();
  if (hex.startsWith('#')) {
    // Format hexadécimal simple
    let r, g, b, a = 1;
    if (hex.length === 4) { // #rgb
      r = parseInt(hex[1] + hex[1], 16); 
      g = parseInt(hex[2] + hex[2], 16); 
      b = parseInt(hex[3] + hex[3], 16);
    } else if (hex.length === 7) { // #rrggbb
      r = parseInt(hex.slice(1,3), 16); 
      g = parseInt(hex.slice(3,5), 16); 
      b = parseInt(hex.slice(5,7), 16);
    } else {
      return { h: 270, s: 50, l: 50, a: 1 }; // Fallback pour formats non supportés
    }
    return rgbToHsl(r, g, b, a);
  }
  
  // Fallback pour les chaînes CSS
  try { 
    return cssToHsl(hex); 
  } catch (_) { 
    return { h: 270, s: 50, l: 50, a: 1 }; 
  }
}

/**
 * Convertit un objet HSL(A) en string CSS hsla() (version optimisée)
 * @param {object} param0 - {h, s, l, a}
 * @returns {string}
 */
function hslToCss({ h, s, l, a }) {
  // Utilisation de | 0 pour un arrondi rapide et + pour éviter .toFixed() si possible
  const hVal = (h + 0.5) | 0;
  const sVal = (s + 0.5) | 0;
  const lVal = (l + 0.5) | 0;
  const aVal = a === 1 ? 1 : Math.round(a * 1000) / 1000;
  return `hsla(${hVal}, ${sVal}%, ${lVal}%, ${aVal})`;
}

/**
 * Convertit un objet HSL(A) en objet RGB(A) (version optimisée)
 * @param {object} param0 - {h, s, l, a}
 * @returns {object} - {r, g, b, a}
 */
function hslToRgb({ h, s, l, a }) {
  // h en [0,360], s,l en [0,100] - optimisation des calculs
  const _h = ((h % 360) + 360) % 360; // Normalisation de la teinte
  const _s = s * 0.01; // Division par 100 optimisée
  const _l = l * 0.01;
  
  const c = (1 - Math.abs(2 * _l - 1)) * _s;
  const x = c * (1 - Math.abs(((_h * 0.016666667) % 2) - 1)); // 0.016666667 = 1/60
  const m = _l - c * 0.5;
  
  let r1, g1, b1;
  // Optimisation des conditions avec des seuils précalculés
  if (_h < 60) { r1 = c; g1 = x; b1 = 0; }
  else if (_h < 120) { r1 = x; g1 = c; b1 = 0; }
  else if (_h < 180) { r1 = 0; g1 = c; b1 = x; }
  else if (_h < 240) { r1 = 0; g1 = x; b1 = c; }
  else if (_h < 300) { r1 = x; g1 = 0; b1 = c; }
  else { r1 = c; g1 = 0; b1 = x; }
  
  // Conversion rapide en [0,255] avec arrondi optimisé
  const r = ((r1 + m) * 255 + 0.5) | 0;
  const g = ((g1 + m) * 255 + 0.5) | 0;
  const b = ((b1 + m) * 255 + 0.5) | 0;
  
  return { r, g, b, a };
}

/**
 * Convertit un objet RGBA en string CSS rgba() (version optimisée)
 * @param {object} param0 - {r, g, b, a}
 * @returns {string}
 */
function rgbaToCss({ r, g, b, a }) {
  const aVal = a === 1 ? 1 : Math.round(a * 1000) / 1000;
  return `rgba(${r}, ${g}, ${b}, ${aVal})`;
}

/**
 * Convertit des composantes RGB(A) en hexadécimal (#rrggbb ou #rrggbbaa)
 * @param {number} r 
 * @param {number} g 
 * @param {number} b 
 * @param {number} a 
 * @returns {string}
 */
function rgbToHex(r, g, b, a = 1) {
  const to2 = (n) => n.toString(16).padStart(2, '0');
  const R = to2(Math.max(0, Math.min(255, r)));
  const G = to2(Math.max(0, Math.min(255, g)));
  const B = to2(Math.max(0, Math.min(255, b)));
  const A = to2(Math.round(Math.max(0, Math.min(1, a)) * 255));
  // Si alpha < 1, on ajoute la composante alpha
  return a < 1 ? `#${R}${G}${B}${A}` : `#${R}${G}${B}`;
}

/**
 * Convertit un objet HSLA en hexadécimal
 * @param {object} hsla 
 * @returns {string}
 */
function hslToHex(hsla) {
  const { r, g, b, a } = hslToRgb(hsla);
  return rgbToHex(r, g, b, a);
}

/**
 * Parse une string CSS hsla()/rgba() en objet HSL(A)
 * @param {string} css 
 * @returns {object}
 */
function cssToHsl(css) {
  const hsla = css.match(/hsla?\(([^)]+)\)/i);
  if (hsla) {
    // Extraction des valeurs h, s, l, a
    const [h, s, l, a] = hsla[1].split(',').map((v) => v.trim());
    return { h: parseFloat(h), s: parseFloat(s), l: parseFloat(l), a: a ? parseFloat(a) : 1 };
  }
  const rgba = css.match(/rgba?\(([^)]+)\)/i);
  if (rgba) {
    // Extraction des valeurs r, g, b, a puis conversion en HSL
    const [r, g, b, a] = rgba[1].split(',').map((v) => v.trim());
    return rgbToHsl(parseFloat(r), parseFloat(g), parseFloat(b), a ? parseFloat(a) : 1);
  }
  // Fallback couleur par défaut
  return parseColor('#563d7c');
}

/**
 * Convertit des composantes RGB(A) en HSL(A)
 * @param {number} r 
 * @param {number} g 
 * @param {number} b 
 * @param {number} a 
 * @returns {object}
 */
function rgbToHsl(r, g, b, a = 1) {
  r /= 255; g /= 255; b /= 255;
  const max = Math.max(r,g,b), min = Math.min(r,g,b);
  let h, s, l = (max + min) / 2;
  if (max === min) { 
    h = s = 0; // Couleur grise
  }
  else {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    switch (max) {
      case r: h = (g - b) / d + (g < b ? 6 : 0); break;
      case g: h = (b - r) / d + 2; break;
      case b: h = (r - g) / d + 4; break;
    }
    h *= 60;
  }
  return { h, s: s * 100, l: l * 100, a };
}

/**
 * Utilitaire de debouncing pour optimiser les performances
 * @param {Function} func - Fonction à débouncer
 * @param {number} wait - Délai en ms
 * @returns {Function}
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Throttle pour limiter la fréquence d'exécution
 * @param {Function} func - Fonction à throttler
 * @param {number} limit - Délai minimum entre les exécutions
 * @returns {Function}
 */
function throttle(func, limit) {
  let inThrottle;
  return function executedFunction(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}

/**
 * Construit le panneau du color picker dans le DOM
 * @param {HTMLElement} root - Racine du color picker
 * @param {object} state - Etat courant {h, s, l, a, __format}
 */
function buildPanel(root, state) {
  const panel = root.querySelector('[data-colorpicker-panel]');
  if (!panel) return;
  panel.innerHTML = ''; // Nettoyage du contenu
  
  // Ajustement responsive simple
  if (!panel.closest('.dropdown-content')) {
    // Mode inline : responsive via CSS avec fallback JS
    const isMobile = window.innerWidth < 640;
    panel.style.width = isMobile ? '100%' : '288px';
    panel.style.maxWidth = '100%';
  }

  // Format de sortie courant (hsla, rgba, hex)
  state.__format = state.__format || 'hsla'; // 'hsla' | 'rgba' | 'hex'

  // === Zone d'aperçu et champ texte ===
  const preview = document.createElement('div');
  preview.className = 'flex items-center gap-2 mb-2';
  // Pastille de couleur
  const chip = document.createElement('span');
  chip.className = 'w-6 h-6 rounded-box border';
  chip.style.background = hslToCss(state);
  // Champ texte pour la valeur couleur
  const text = document.createElement('input');
  text.type = 'text';
  text.className = 'input input-sm input-bordered w-full';
  text.value = hslToCss(state);
  text.disabled = root.dataset.showInputs !== 'true';
  preview.append(chip, text);
  panel.append(preview);

  // === Sélecteur de format (hsla/rgba/hex) ===
  if (root.dataset.showFormatToggle === 'true') {
    const selWrap = document.createElement('div');
    selWrap.className = 'mb-2 flex items-center gap-2';
    const lab = document.createElement('div'); 
    lab.className = 'text-xs opacity-70'; 
    lab.textContent = 'Format';
    const sel = document.createElement('select'); 
    sel.className = 'select select-xs select-bordered';
    ['hsla','rgba','hex'].forEach((fmt) => {
      const opt = document.createElement('option'); 
      opt.value = fmt; 
      opt.textContent = fmt.toUpperCase();
      if (fmt === state.__format) opt.selected = true; 
      sel.append(opt);
    });
    // Changement de format : on met à jour l'affichage
    sel.addEventListener('change', () => { 
      state.__format = sel.value;
      // Invalidation du cache pour forcer le recalcul des formats
      lastState = null;
      fastUpdate();
      debouncedEvent();
    });
    selWrap.append(lab, sel); 
    panel.append(selWrap);
  }

  /**
   * Crée un slider avec label
   * @param {string} labelText - Label du slider
   * @param {number} min - Valeur min
   * @param {number} max - Valeur max
   * @param {number} value - Valeur initiale
   * @param {number|null} step - Pas
   * @param {function} onInput - Callback sur input
   * @param {string} extraClass - Classes CSS additionnelles
   * @returns {HTMLInputElement}
   */
  const makeLabeledRange = (labelText, min, max, value, step, onInput, extraClass = '') => {
    const wrap = document.createElement('label');
    wrap.className = 'block space-y-1';
    const lab = document.createElement('div');
    lab.className = 'text-xs opacity-70 flex items-center justify-between';
    lab.textContent = labelText;
    const rng = document.createElement('input');
    rng.type = 'range'; 
    rng.min = String(min); 
    rng.max = String(max); 
    rng.value = String(value);
    if (step != null) rng.step = String(step);
    rng.className = 'range range-xs ' + extraClass;
    rng.setAttribute('aria-label', labelText);
    wrap.append(lab, rng);
    rng.addEventListener('input', onInput);
    panel.append(wrap);
    return rng;
  };

  // Fonction de mise à jour optimisée pour les sliders (update immédiat + événement débounced)
  const fastUpdate = () => {
    updateDisplay(); // Mise à jour visuelle immédiate
  };
  
  // === Slider Teinte (Hue) ===
  if (root.dataset.showHue === 'true') {
    makeLabeledRange('Teinte (H)', 0, 360, state.h, null, (e) => {
      state.h = clamp(parseFloat(e.target.value), 0, 360); 
      fastUpdate();
      debouncedEvent();
    });
  }

  // === Slider Saturation ===
  makeLabeledRange('Saturation (S)', 0, 100, Math.round(state.s), null, (e) => {
    state.s = clamp(parseFloat(e.target.value), 0, 100); 
    fastUpdate();
    debouncedEvent();
  });

  // === Slider Luminance ===
  makeLabeledRange('Luminance (L)', 0, 100, Math.round(state.l), null, (e) => {
    state.l = clamp(parseFloat(e.target.value), 0, 100); 
    fastUpdate();
    debouncedEvent();
  });

  // === Slider Alpha (transparence) ===
  if (root.dataset.showAlpha === 'true') {
    makeLabeledRange('Transparence (A)', 0, 1, state.a, 0.01, (e) => {
      state.a = clamp(parseFloat(e.target.value), 0, 1); 
      fastUpdate();
      debouncedEvent();
    });
  }

  // === Palette de swatches ===
  if (root.dataset.showPalette === 'true') {
    try {
      const swatches = JSON.parse(root.dataset.swatches || '[]');
      if (Array.isArray(swatches) && swatches.length) {
        const wrap = document.createElement('div');
        wrap.className = 'mt-2 grid gap-2';
        // Limite la hauteur de la palette si précisé
        if (+root.dataset.swatchesHeight > 0) 
          wrap.style.maxHeight = root.dataset.swatchesHeight + 'px';
        wrap.style.overflow = 'auto';
        // Pour chaque ligne de swatches
        swatches.forEach((row) => {
          const rowEl = document.createElement('div');
          rowEl.className = 'flex flex-wrap gap-2';
          (row || []).forEach((col) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'w-5 h-5 rounded border hover:scale-110 transition-transform';
            b.style.background = col;
            b.addEventListener('click', () => { 
              Object.assign(state, parseColor(col)); 
              fastUpdate();
              debouncedEvent();
            });
            rowEl.append(b);
          });
          wrap.append(rowEl);
        });
        panel.append(wrap);
      }
    } catch(_) {
      // Erreur parsing JSON swatches : on ignore
    }
  }

  // Cache des éléments DOM pour éviter les recherches répétitives
  const chipOut = root.querySelector('[data-colorchip]');
  const textOut = root.querySelector('[data-colortext]');
  
  // Cache des conversions pour éviter les recalculs
  let lastState = null;
  let cachedFormats = {};

  /**
   * Met à jour l'affichage du color picker (version optimisée)
   */
  function updateDisplay() {
    const css = hslToCss(state);
    chip.style.background = css;
    
    // Mise à jour des sorties facultatives (utilisation du cache)
    if (chipOut) chipOut.style.background = css;
    
    // Calcul des formats seulement si nécessaire
    const stateKey = `${state.h}-${state.s}-${state.l}-${state.a}-${state.__format}`;
    if (lastState !== stateKey) {
      cachedFormats = {};
      lastState = stateKey;
      
      // Calcul unique des conversions selon le format
      if (state.__format === 'rgba') {
        const { r, g, b, a } = hslToRgb(state);
        cachedFormats.rgba = rgbaToCss({ r, g, b, a });
      } else if (state.__format === 'hex') {
        cachedFormats.hex = hslToHex(state);
      }
      cachedFormats.hsla = css;
    }
    
    // Mise à jour du champ texte selon le format sélectionné
    if (root.dataset.showInputs === 'true') {
      const formatted = cachedFormats[state.__format] || css;
      if (text.value !== formatted) {
        text.value = formatted;
      }
    }
    
    // Mise à jour du texte de sortie
    if (textOut) {
      const formatted = cachedFormats[state.__format] || css;
      if (textOut.textContent !== formatted) {
        textOut.textContent = formatted;
      }
    }
  }

  /**
   * Met à jour et déclenche l'événement de changement (avec debouncing pour l'événement)
   */
  const debouncedEvent = debounce(() => {
    const css = hslToCss(state);
    root.dispatchEvent(new CustomEvent('colorpicker:change', { detail: { value: css }, bubbles: true }));
  }, 50);

  /**
   * Fonction de mise à jour principale avec throttling pour l'affichage
   */
  const update = throttle(() => {
    updateDisplay();
    debouncedEvent();
  }, 16); // ~60fps

  // Quand l'utilisateur modifie le champ texte, on tente de parser la couleur
  text.addEventListener('change', () => { 
    Object.assign(state, parseColor(text.value)); 
    fastUpdate();
    debouncedEvent();
  });
}

/**
 * Initialise un color picker sur un élément racine donné
 * @param {HTMLElement} root 
 */
function initColorPicker(root) {
  if (!root || root.__cpInit) return; // Déjà initialisé
  root.__cpInit = true;
  if (root.dataset.disabled === 'true') return; // Désactivé
  
  // Etat initial à partir de data-value ou couleur par défaut
  const state = parseColor(root.dataset.value || '#563d7c');
  buildPanel(root, state);
  // Récupération des éléments d'interaction
  const trigger = root.querySelector('[data-colorpicker-trigger]');
  const panel = root.querySelector('[data-colorpicker-panel]');
  const dropdown = root.querySelector('.dropdown');
  if (dropdown && trigger && panel) {
    // Ouverture/fermeture du dropdown au clic sur le trigger
    trigger.addEventListener('click', (e) => {
      e.preventDefault(); 
      e.stopPropagation();
      dropdown.classList.toggle('dropdown-open');
    });
    // Fermeture du dropdown si clic en dehors
    document.addEventListener('click', (e) => {
      if (!root.contains(e.target)) dropdown.classList.remove('dropdown-open');
    });
    // Fermeture du dropdown à la touche Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') dropdown.classList.remove('dropdown-open');
    });
  }
}

/**
 * Initialise tous les color pickers présents dans le DOM
 */
function initAllColorPickers() {
  const elements = document.querySelectorAll('[data-colorpicker="1"]');
  elements.forEach(initColorPicker);
}

// Expose l'API globale DaisyColorPicker
window.DaisyColorPicker = { 
  init: initColorPicker, 
  initAll: initAllColorPickers
};

// Export pour le système data-module (kit/index.js)
export default initColorPicker;
export { initColorPicker, initAllColorPickers };

// Initialisation automatique
function autoInit() {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllColorPickers);
  } else {
    // Si le DOM est déjà chargé, on attend un peu pour s'assurer que tous les éléments sont présents
    setTimeout(initAllColorPickers, 0);
  }
}

// Déclenche l'initialisation automatique
autoInit();
