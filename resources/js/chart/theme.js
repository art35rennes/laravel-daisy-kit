/**
 * Helpers de thème DaisyUI pour Chart.js
 * - Résolution des tokens de couleur DaisyUI (ex: 'primary', 'secondary', 'base-content') vers RGB calculé
 * - Utilitaires pour appliquer l'alpha à une couleur rgb/hex/css
 */

/**
 * Vérifie si une valeur ressemble à une couleur CSS
 * @param {*} value - Valeur à tester
 * @returns {boolean} true si la valeur ressemble à une couleur CSS
 */
function isCssColorLike(value) {
  if (!value || typeof value !== 'string') return false;
  const v = value.trim();
  return v.startsWith('#') || v.startsWith('rgb') || v.startsWith('hsl') || v.startsWith('oklch') || v.startsWith('lab') || v.startsWith('lch') || v.startsWith('color(') || v.startsWith('var(');
}

/**
 * Convertit une chaîne rgb() en rgba() avec l'alpha spécifié
 * @param {string} rgbString - Chaîne RGB à convertir
 * @param {number} alpha - Valeur alpha (0-1)
 * @returns {string} Chaîne RGBA ou la chaîne originale si conversion impossible
 */
function toRgbaString(rgbString, alpha) {
  try {
    const m = rgbString.match(/rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i);
    if (!m) return rgbString;
    const r = parseInt(m[1], 10), g = parseInt(m[2], 10), b = parseInt(m[3], 10);
    const a = Math.max(0, Math.min(1, Number(alpha)));
    return `rgba(${r}, ${g}, ${b}, ${a})`;
  } catch (_) {
    return rgbString;
  }
}

/**
 * Crée un élément sonde invisible pour tester les styles CSS
 * @param {Element} root - Élément racine où attacher la sonde
 * @returns {HTMLElement} Élément sonde créé
 */
function createProbe(root) {
  const probe = document.createElement('span');
  probe.textContent = '\u200b';
  probe.style.position = 'absolute';
  probe.style.left = '-99999px';
  probe.style.top = '-99999px';
  probe.style.pointerEvents = 'none';
  probe.style.opacity = '0';
  (root || document.body).appendChild(probe);
  return probe;
}

/**
 * Résout un token de couleur DaisyUI ou une couleur CSS en valeur calculée
 * @param {string} token - Token DaisyUI (ex: 'primary') ou couleur CSS
 * @param {Element} contextEl - Élément de contexte pour la résolution
 * @param {string} role - Rôle de la couleur ('text', 'bg', 'border')
 * @returns {string|null} Couleur résolue ou null si échec
 */
function resolveColorToken(token, contextEl, role = 'text') {
  if (!token) return null;
  
  // Valeur de couleur CSS directe
  if (isCssColorLike(token)) {
    // Si c'est une variable CSS, la résoudre
    if (token.startsWith('var(')) {
      const probe = createProbe(contextEl || document.body);
      probe.style.color = token;
      const color = getComputedStyle(probe).color;
      probe.remove();
      return color || null;
    }
    return token;
  }

  // Approche par classe utilitaire DaisyUI selon le rôle
  let className = `text-${token}`;
  let readProp = 'color';
  if (role === 'bg') { className = `bg-${token}`; readProp = 'backgroundColor'; }
  else if (role === 'border') { className = `border border-${token}`; readProp = 'borderTopColor'; }
  
  const probe = createProbe(contextEl || document.body);
  probe.className = className;
  const comp = getComputedStyle(probe);
  const color = comp[readProp] || comp.color;
  probe.remove();
  return color || null;
}

/**
 * Résout une liste de tokens/couleurs en couleurs calculées
 * @param {Array|string} tokensOrColors - Liste de tokens ou couleurs à résoudre
 * @param {Element} contextEl - Élément de contexte pour la résolution
 * @param {string} role - Rôle des couleurs ('text', 'bg', 'border')
 * @returns {Array} Tableau des couleurs résolues (filtrées des valeurs nulles)
 */
export function resolveColors(tokensOrColors, contextEl, role = 'text') {
  const list = Array.isArray(tokensOrColors) ? tokensOrColors : (tokensOrColors ? [tokensOrColors] : []);
  return list.map((t) => resolveColorToken(String(t), contextEl, role)).filter(Boolean);
}

/**
 * Résout un seul token/couleur en couleur calculée
 * @param {string} tokenOrColor - Token DaisyUI ou couleur CSS
 * @param {Element} contextEl - Élément de contexte pour la résolution
 * @param {string} role - Rôle de la couleur ('text', 'bg', 'border')
 * @returns {string|null} Couleur résolue ou null si échec
 */
export function resolveSingleColor(tokenOrColor, contextEl, role = 'text') {
  return resolveColorToken(tokenOrColor, contextEl, role);
}

/**
 * Applique une valeur alpha à une couleur
 * @param {string} color - Couleur source
 * @param {number} alpha - Valeur alpha (0-1)
 * @returns {string} Couleur avec alpha appliqué
 */
export function applyAlpha(color, alpha) {
  if (!color) return color;
  
  if (color.startsWith('rgba(')) {
    // Remplace l'alpha existant
    try {
      const parts = color.substring(5, color.length - 1).split(',').map((s) => s.trim());
      const r = parts[0], g = parts[1], b = parts[2];
      const a = Math.max(0, Math.min(1, Number(alpha)));
      return `rgba(${r}, ${g}, ${b}, ${a})`;
    } catch (_) { return color; }
  }
  
  if (color.startsWith('rgb(')) {
    return toRgbaString(color, alpha);
  }
  
  // Pour les chaînes non-rgb, essaie de résoudre en rgb via une sonde
  const probe = createProbe(document.body);
  probe.style.color = color;
  const rgb = getComputedStyle(probe).color;
  probe.remove();
  return toRgbaString(rgb, alpha);
}

/**
 * Obtient la couleur base-content du thème DaisyUI
 * @param {Element} contextEl - Élément de contexte
 * @returns {string} Couleur base-content ou fallback
 */
export function getBaseContentColor(contextEl) {
  return resolveSingleColor('base-content', contextEl) || 'rgb(30,30,30)';
}

/**
 * Obtient la couleur base-300 du thème DaisyUI
 * Utilisée pour les bordures subtiles et lignes de grille
 * @param {Element} contextEl - Élément de contexte
 * @returns {string} Couleur base-300 ou fallback
 */
export function getBase300Color(contextEl) {
  // DaisyUI: border-base-300
  const probe = createProbe(contextEl || document.body);
  probe.className = 'border border-base-300';
  const style = getComputedStyle(probe);
  // Dans plusieurs navigateurs, la couleur de bordure est reflétée comme color quand pas de texte
  const borderColor = style.borderTopColor || style.color;
  probe.remove();
  return borderColor || 'rgb(200,200,200)';
}

/**
 * Obtient la couleur base-100 du thème DaisyUI
 * Couleur d'arrière-plan de base/carte
 * @param {Element} contextEl - Élément de contexte
 * @returns {string} Couleur base-100 ou fallback
 */
export function getBase100Color(contextEl) {
  const probe = createProbe(contextEl || document.body);
  probe.className = 'bg-base-100';
  const style = getComputedStyle(probe);
  const bg = style.backgroundColor || 'rgb(255,255,255)';
  probe.remove();
  return bg;
}

/**
 * Parse une chaîne RGB/RGBA en objet avec composantes numériques
 * @param {string} rgbString - Chaîne RGB à parser
 * @returns {Object|null} Objet {r, g, b, a} ou null si parsing impossible
 */
function parseRgb(rgbString) {
  const m = rgbString.match(/rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*([0-9.]+))?\s*\)/i);
  if (!m) return null;
  return { r: Number(m[1]), g: Number(m[2]), b: Number(m[3]), a: m[4] != null ? Number(m[4]) : 1 };
}

/**
 * Convertit une composante sRGB en linéaire pour le calcul de luminance
 * @param {number} c - Composante sRGB (0-255)
 * @returns {number} Composante linéaire
 */
function srgbToLin(c) {
  c = c / 255;
  return c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
}

/**
 * Calcule la luminance relative d'une couleur RGB selon WCAG
 * @param {Object} rgb - Objet RGB avec propriétés r, g, b
 * @returns {number} Luminance relative (0-1)
 */
function relativeLuminance(rgb) {
  if (!rgb) return 1;
  const r = srgbToLin(rgb.r);
  const g = srgbToLin(rgb.g);
  const b = srgbToLin(rgb.b);
  return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

/**
 * Détermine si le thème actuel est sombre basé sur la luminance de base-100
 * @param {Element} contextEl - Élément de contexte
 * @returns {boolean} true si le thème est sombre
 */
export function isDarkTheme(contextEl) {
  const bg = getBase100Color(contextEl);
  const p = parseRgb(bg);
  const L = relativeLuminance(p);
  return L < 0.5; // heuristique
}

/**
 * Construit une palette de couleurs à partir de tokens DaisyUI
 * @param {Array} paletteTokens - Liste des tokens à utiliser
 * @param {Element} contextEl - Élément de contexte pour la résolution
 * @returns {Array} Palette de couleurs résolues
 */
export function buildPalette(paletteTokens, contextEl) {
  const tokens = Array.isArray(paletteTokens) && paletteTokens.length ? paletteTokens : ['primary','secondary','accent','info','success','warning','error'];
  const colors = resolveColors(tokens, contextEl, 'text');
  return colors;
}
