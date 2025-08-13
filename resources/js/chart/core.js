/*
 * Daisy Kit - Intégration Chart.js avec le thème DaisyUI
 *
 * API publique : window.DaisyChart
 * - init(root)         : Initialise un conteneur de graphique unique (attend <div data-chart> avec un <canvas> et un <script data-config>)
 * - initAll()          : Initialise tous les graphiques dans le document
 * - create(canvas, cfg): Crée un graphique Chart.js sur un <canvas> avec le thème DaisyUI automatique
 * - updateTheme()      : Ré-applique le thème DaisyUI à tous les graphiques enregistrés (utile quand theme-controller change)
 */

import { resolveColors, buildPalette, resolveSingleColor, applyAlpha, getBaseContentColor, getBase300Color, isDarkTheme } from './theme';

// Bibliothèque Chart.js importée de manière paresseuse
let ChartLib = null;

// Registre pour associer chaque canvas à son instance de graphique
const registry = new WeakMap();

// Valeurs par défaut globales pour les performances (peuvent être remplacées via DaisyChart.setGlobalPerformanceDefaults)
const globalPerf = {
  mode: 'auto',                 // auto | balanced | fast | static - Mode de performance
  pixelRatioCap: 1.75,          // Limite le DPR pour réduire les coûts de taille du canvas
  resizeDelay: 120,             // Debounce du redimensionnement pour réduire les rafales de redessins
  maxTicks: 9,                  // Nombre maximum de ticks par axe
  interactionMode: 'nearest',   // Mode d'interaction du pointeur
  decimationThreshold: 1000,    // Active la décimation si le total de points dépasse ce seuil
  decimationSamples: 300,       // Nombre cible d'échantillons LTTB
};

/**
 * Assure l'importation de Chart.js de manière paresseuse
 * @returns {Promise} La bibliothèque Chart.js
 */
async function ensureChart() {
  if (ChartLib) return ChartLib;
  // Vite va diviser ce chunk
  const mod = await import('chart.js/auto');
  ChartLib = mod.default || mod;
  return ChartLib;
}

/**
 * Normalise et applique les couleurs aux datasets selon le thème DaisyUI
 * @param {Array} datasets - Datasets du graphique
 * @param {Array} palette - Palette de couleurs
 * @param {Element} contextEl - Élément de contexte pour résoudre les couleurs
 * @param {string} chartType - Type de graphique
 * @returns {Array} Datasets normalisés avec les couleurs appliquées
 */
function normalizeDatasets(datasets, palette, contextEl, chartType) {
  const out = [];
  const paletteColors = palette && palette.length ? palette : buildPalette(null, contextEl);
  datasets = Array.isArray(datasets) ? datasets : [];
  
  for (let i = 0; i < datasets.length; i++) {
    const ds = { ...datasets[i] };
    
    // Mappage de l'alias 'area' -> 'line' au niveau du dataset si utilisé
    if (ds.type === 'area') ds.type = 'line';
    
    // Résolution de la couleur de base
    const baseColorToken = ds.color || paletteColors[i % paletteColors.length] || resolveSingleColor('primary', contextEl);
    const baseColor = resolveSingleColor(baseColorToken, contextEl) || baseColorToken;
    
    // Dérivation des couleurs de fond/bordure selon le type de graphique
    const fillAlpha = ds.fillAlpha != null ? Number(ds.fillAlpha) : 0.2;
    const borderAlpha = ds.borderAlpha != null ? Number(ds.borderAlpha) : 1;
    const effectiveType = ds.type || chartType;
    
    // Configuration de la couleur de fond
    if (!ds.backgroundColor) {
      if (['doughnut','pie','polarArea'].includes(effectiveType)) {
        // Pour les graphiques circulaires, une couleur par segment
        const dataLen = Array.isArray(ds.data) ? ds.data.length : 0;
        const colors = [];
        for (let j = 0; j < dataLen; j++) {
          const c = paletteColors[j % paletteColors.length] || baseColor;
          colors.push(applyAlpha(resolveSingleColor(c, contextEl) || c, 0.75));
        }
        ds.backgroundColor = colors;
      } else {
        // Zone remplie : le radar nécessite plus de transparence que les lignes
        const isDark = isDarkTheme(contextEl);
        const defaultAlpha = effectiveType === 'radar' ? (isDark ? 0.12 : 0.08) : (isDark ? 0.25 : 0.18);
        let alpha = ds.fillAlpha != null ? Number(ds.fillAlpha) : defaultAlpha;
        
        // Exiger au moins 40% de transparence (opacité <= 0.6) sauf pour bar/doughnut/pie/polarArea
        if (!['bar','doughnut','pie','polarArea'].includes(effectiveType)) {
          alpha = Math.min(alpha, 0.6);
        }
        ds.backgroundColor = applyAlpha(baseColor, alpha);
      }
    }
    
    // Configuration de la couleur de bordure
    if (!ds.borderColor) {
      // Augmente le contraste sur les thèmes sombres
      const isDark = isDarkTheme(contextEl);
      const alpha = ds.borderAlpha != null ? Number(ds.borderAlpha) : (isDark ? 0.95 : 0.85);
      ds.borderColor = applyAlpha(baseColor, alpha);
    }
    
    // Les graphiques en barres ont un meilleur rendu avec des bordures plus épaisses
    if (Array.isArray(ds.data) && (effectiveType === 'bar' || !ds.type)) {
      ds.borderWidth = ds.borderWidth != null ? ds.borderWidth : 1;
    }
    
    // Pour l'alias area, assurer le remplissage de la zone
    if (chartType === 'area' && ds.fill == null) {
      ds.fill = true;
    }
    
    out.push(ds);
  }
  return out;
}

/**
 * Applique les options par défaut selon le thème DaisyUI
 * @param {string} type - Type de graphique
 * @param {Object} options - Options personnalisées
 * @param {Element} contextEl - Élément de contexte pour résoudre les couleurs
 * @returns {Object} Options avec le thème appliqué
 */
function applyDefaultOptions(type, options, contextEl) {
  const textColor = getBaseContentColor(contextEl);
  const dark = isDarkTheme(contextEl);
  const gridBase = getBase300Color(contextEl);
  const gridColor = applyAlpha(gridBase, dark ? 0.25 : 0.35);
  const axisColor = applyAlpha(gridBase, dark ? 0.45 : 0.55);
  
  const base = {
    plugins: {
      legend: { labels: { color: textColor } },
      tooltip: (ctx) => {
        return {
          backgroundColor: applyAlpha(resolveSingleColor('base-200', contextEl) || '#222', dark ? 0.98 : 0.97),
          titleColor: textColor,
          bodyColor: textColor,
          borderColor: axisColor,
          borderWidth: 1,
        };
      },
    },
    scales: {},
  };
  
  // Personnalisation des échelles par famille de graphiques
  if (type === 'radar' || type === 'polarArea') {
    // Configuration pour les graphiques polaires
    base.scales.r = {
      angleLines: { color: axisColor, lineWidth: 1, display: true },
      grid: { color: gridColor, lineWidth: 1, circular: true, display: true },
      pointLabels: { color: textColor },
      ticks: { color: textColor, showLabelBackdrop: false, backdropColor: 'transparent', display: true },
      beginAtZero: true,
    };
  } else if (['doughnut','pie'].includes(type)) {
    // Pas d'axes pour les graphiques circulaires
    delete base.scales;
  } else {
    // Configuration des axes X et Y pour les graphiques cartésiens
    base.scales.x = {
      ticks: { color: textColor, autoSkip: true, maxRotation: 0 },
      grid: { color: gridColor, borderColor: axisColor, drawBorder: true, display: true, drawTicks: true, lineWidth: 1 },
      border: { color: axisColor },
    };
    base.scales.y = {
      ticks: { color: textColor },
      grid: { color: gridColor, borderColor: axisColor, drawBorder: true, display: true, drawTicks: true, lineWidth: 1 },
      border: { color: axisColor },
    };
  }
  
  return { ...base, ...(options || {}) };
}

/**
 * Construit les options de performance selon la configuration
 * @param {string} type - Type de graphique
 * @param {Object} cfg - Configuration du graphique
 * @param {Object} data - Données du graphique
 * @returns {Object} Options de performance
 */
function buildPerformanceOptions(type, cfg, data) {
  const daisy = cfg.daisy || {};
  const perfCfg = typeof daisy.performance === 'string' ? { mode: daisy.performance } : (daisy.performance || {});
  const mode = perfCfg.mode || globalPerf.mode || 'auto';

  // Calcul du nombre total de points de données
  const totalPoints = (() => {
    try {
      const datasets = (data && data.datasets) || [];
      return datasets.reduce((sum, ds) => sum + (Array.isArray(ds?.data) ? ds.data.length : 0), 0);
    } catch (_) { return 0; }
  })();

  const perf = {};
  
  // Limitation du ratio de pixels de l'appareil
  const cap = perfCfg.pixelRatioCap ?? globalPerf.pixelRatioCap;
  if (cap && typeof window !== 'undefined') {
    const dpr = Math.min(window.devicePixelRatio || 1, Number(cap));
    perf.devicePixelRatio = dpr;
  }
  
  // Debounce du redimensionnement
  perf.resizeDelay = perfCfg.resizeDelay ?? globalPerf.resizeDelay;

  // Allègement des interactions
  const interactionMode = perfCfg.interactionMode || globalPerf.interactionMode;
  perf.interaction = { mode: interactionMode, intersect: true }; // moins d'éléments testés
  perf.events = ['mousemove','mouseout','click','touchstart','touchmove'];

  // Contrôle de la densité des ticks
  const maxTicks = perfCfg.maxTicks ?? globalPerf.maxTicks;
  if (type === 'radar' || type === 'polarArea') {
    perf.scales = { r: { angleLines: { display: true }, grid: {}, pointLabels: {}, ticks: { maxTicksLimit: maxTicks } } };
  } else if (['doughnut','pie'].includes(type)) {
    perf.scales = {};
  } else {
    perf.scales = {
      x: { ticks: { maxTicksLimit: maxTicks, autoSkip: true, maxRotation: 0 } },
      y: { ticks: { maxTicksLimit: maxTicks, autoSkip: true } },
    };
  }

  // Réglage des éléments
  perf.elements = {};
  if (type === 'line' || type === 'radar') {
    // Affiche de petits points pour correspondre à la documentation tout en gardant des performances acceptables
    perf.elements.point = { radius: 2, hitRadius: 6, hoverRadius: 4 };
    perf.normalized = true;
  }

  // Décimation pour les grandes séries de lignes
  if ((type === 'line' || type === 'bar') && totalPoints >= (perfCfg.decimationThreshold ?? globalPerf.decimationThreshold)) {
    perf.plugins = {
      ...(perf.plugins || {}),
      decimation: { enabled: true, algorithm: 'lttb', samples: perfCfg.decimationSamples ?? globalPerf.decimationSamples },
    };
  }

  // Niveaux d'animation
  if (mode === 'static') {
    // Aucune animation
    perf.animation = false;
    perf.responsiveAnimationDuration = 0;
  } else if (mode === 'fast') {
    // Animation instantanée
    perf.animation = { duration: 0 };
    perf.responsiveAnimationDuration = 0;
  } else if (mode === 'balanced' || mode === 'auto') {
    // Animation équilibrée
    perf.animation = { duration: 200, easing: 'easeOutQuart' };
    perf.responsiveAnimationDuration = 0;
  }

  return perf;
}

/**
 * Fusionne deux objets d'options de manière profonde
 * @param {Object} base - Options de base
 * @param {Object} extra - Options supplémentaires
 * @returns {Object} Options fusionnées
 */
function mergeOptions(base, extra) {
  const out = { ...base, ...extra };
  
  // Fusion des objets imbriqués
  if (base.plugins || extra.plugins) out.plugins = { ...(base.plugins || {}), ...(extra.plugins || {}) };
  if (base.interaction || extra.interaction) out.interaction = { ...(base.interaction || {}), ...(extra.interaction || {}) };
  if (base.elements || extra.elements) out.elements = { ...(base.elements || {}), ...(extra.elements || {}) };

  /**
   * Fusionne les objets d'échelle de manière profonde
   * @param {Object} b - Échelle de base
   * @param {Object} e - Échelle supplémentaire
   * @returns {Object} Échelle fusionnée
   */
  function mergeScaleObject(b, e) {
    const s = { ...(b || {}), ...(e || {}) };
    if ((b?.ticks) || (e?.ticks)) s.ticks = { ...(b?.ticks || {}), ...(e?.ticks || {}) };
    if ((b?.grid) || (e?.grid)) s.grid = { ...(b?.grid || {}), ...(e?.grid || {}) };
    if ((b?.angleLines) || (e?.angleLines)) s.angleLines = { ...(b?.angleLines || {}), ...(e?.angleLines || {}) };
    if ((b?.pointLabels) || (e?.pointLabels)) s.pointLabels = { ...(b?.pointLabels || {}), ...(e?.pointLabels || {}) };
    if ((b?.title) || (e?.title)) s.title = { ...(b?.title || {}), ...(e?.title || {}) };
    return s;
  }

  // Fusion des échelles
  if (base.scales || extra.scales) {
    out.scales = { ...(base.scales || {}), ...(extra.scales || {}) };
    const bx = base.scales?.x || {}; const ex = extra.scales?.x || {};
    const by = base.scales?.y || {}; const ey = extra.scales?.y || {};
    const br = base.scales?.r || {}; const er = extra.scales?.r || {};
    if (Object.keys(bx).length || Object.keys(ex).length) out.scales.x = mergeScaleObject(bx, ex);
    if (Object.keys(by).length || Object.keys(ey).length) out.scales.y = mergeScaleObject(by, ey);
    if (Object.keys(br).length || Object.keys(er).length) out.scales.r = mergeScaleObject(br, er);
  }
  return out;
}

/**
 * Lit la configuration JSON depuis un conteneur de graphique
 * @param {Element} root - Élément racine contenant le canvas et la configuration
 * @returns {Object|null} Objet avec canvas et configuration, ou null si non trouvé
 */
function readConfigFromContainer(root) {
  const canvas = root.querySelector('canvas');
  const cfgScript = root.querySelector('script[data-config]');
  if (!canvas || !cfgScript) return null;
  try {
    const cfg = JSON.parse(cfgScript.textContent || '{}');
    return { canvas, cfg };
  } catch (_) {
    return null;
  }
}

/**
 * Crée un graphique Chart.js avec le thème DaisyUI
 * @param {HTMLCanvasElement} canvas - Élément canvas
 * @param {Object} config - Configuration du graphique
 * @param {Element} contextEl - Élément de contexte pour le thème
 * @returns {Promise<Chart>} Instance du graphique
 */
export async function createChart(canvas, config, contextEl) {
  const Chart = await ensureChart();
  const rawType = config.type || 'bar';
  const type = rawType === 'area' ? 'line' : rawType; // Conversion de l'alias area
  const daisy = config.daisy || {};
  
  // Résolution des couleurs
  const colors = daisy.colors ? resolveColors(daisy.colors, contextEl) : null;
  const palette = colors && colors.length ? colors : buildPalette(daisy.palette, contextEl);

  // Normalisation de la configuration
  const normalized = {
    type,
    data: {
      labels: (config.data && config.data.labels) || [],
      datasets: normalizeDatasets((config.data && config.data.datasets) || [], palette, contextEl, rawType),
    },
    options: applyDefaultOptions(type, config.options, contextEl),
  };
  
  // Fusion des options axées sur les performances
  normalized.options = mergeOptions(normalized.options, buildPerformanceOptions(type, config, normalized.data));

  // Création du graphique et enregistrement
  const chart = new Chart(canvas, normalized);
  registry.set(canvas, chart);
  return chart;
}

/**
 * Initialise un graphique depuis un conteneur DOM
 * @param {Element} root - Élément racine contenant [data-chart="1"]
 * @returns {Promise<Chart|null>} Instance du graphique ou null
 */
export async function init(root) {
  const pair = readConfigFromContainer(root);
  if (!pair) return null;
  const { canvas, cfg } = pair;
  return createChart(canvas, cfg, root);
}

/**
 * Initialise tous les graphiques dans le document
 * @returns {Promise<Array<Chart>>} Tableau des instances de graphiques créées
 */
export async function initAll() {
  const roots = Array.from(document.querySelectorAll('[data-chart="1"]'));
  const charts = [];
  for (const root of roots) {
    const chart = await init(root);
    if (chart) charts.push(chart);
  }
  return charts;
}

/**
 * Met à jour le thème de tous les graphiques enregistrés
 * Recalcule les couleurs résolues et les options puis met à jour les graphiques
 */
export function updateTheme() {
  registryForEach((canvas, chart) => {
    try {
      const root = canvas.closest('[data-chart="1"]') || document.body;
      const currentType = chart.config.type;
      const cfgScript = root.querySelector('script[data-config]');
      const cfg = cfgScript ? JSON.parse(cfgScript.textContent || '{}') : {};
      const daisy = cfg.daisy || {};
      
      // Recalcul des couleurs
      const colors = daisy.colors ? resolveColors(daisy.colors, root) : null;
      const palette = colors && colors.length ? colors : buildPalette(daisy.palette, root);
      
      // Mise à jour des couleurs des datasets uniquement ; conservation des données
      const ds = chart.config.data.datasets || [];
      const rawType = cfg.type || currentType;
      const recolored = normalizeDatasets(ds, palette, root, rawType);
      chart.config.data.datasets = recolored.map((d, i) => ({ 
        ...ds[i], 
        backgroundColor: d.backgroundColor, 
        borderColor: d.borderColor 
      }));
      
      // Recalcul des options
      const effectiveType = (rawType === 'area') ? 'line' : rawType;
      const base = applyDefaultOptions(effectiveType, cfg.options, root);
      const perf = buildPerformanceOptions(effectiveType, cfg, chart.config.data);
      chart.config.options = mergeOptions(base, perf);
      
      // Mise à jour sans animation
      chart.update('none');
    } catch (_) {}
  });
}

/**
 * Itère sur tous les graphiques enregistrés
 * @param {Function} fn - Fonction à appeler pour chaque paire (canvas, chart)
 */
function registryForEach(fn) {
  // WeakMap n'a pas de forEach ; on suit les canvas via une requête DOM pour simplifier
  const canvases = Array.from(document.querySelectorAll('[data-chart="1"] canvas'));
  canvases.forEach((canvas) => {
    const chart = registry.get(canvas);
    if (chart) fn(canvas, chart);
  });
}

// Exposition de l'API globale
window.DaisyChart = {
  init,
  initAll,
  create: createChart,
  updateTheme,
  setGlobalPerformanceDefaults: (overrides) => { Object.assign(globalPerf, overrides || {}); },
};

// Initialisation automatique paresseuse lors de l'importation dynamique : initialise les graphiques quand ils deviennent visibles
(function setupLazyChartInit() {
  /**
   * Observe les graphiques et les initialise quand ils entrent dans le viewport
   */
  function observeCharts() {
    const roots = Array.from(document.querySelectorAll('[data-chart="1"]'));
    if (!roots.length) return;
    
    // Contrainte : éviter les rafales -> concurrence 1 via petite file
    const queue = [];
    let active = false;
    
    /**
     * Exécute la prochaine tâche dans la file
     */
    async function runNext() {
      if (active) return;
      const job = queue.shift();
      if (!job) return;
      active = true;
      try { 
        await job(); 
      } catch (_) {}
      finally {
        active = false;
        if (queue.length) requestAnimationFrame(runNext);
      }
    }
    
    // Observer d'intersection pour l'initialisation paresseuse
    const obs = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          obs.unobserve(el);
          queue.push(() => init(el));
          runNext();
        }
      });
    }, { rootMargin: '400px 0px', threshold: 0.05 });
    
    roots.forEach((el) => obs.observe(el));
  }
  
  // Initialisation au chargement du DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', observeCharts);
  } else {
    Promise.resolve().then(observeCharts);
  }
})();

/**
 * Configure la synchronisation automatique avec les changements de thème DaisyUI
 */
function setupThemeSync() {
  try {
    const html = document.documentElement;
    // Observer les changements d'attribut data-theme
    const mo = new MutationObserver((mutations) => {
      for (const m of mutations) {
        if (m.type === 'attributes' && m.attributeName === 'data-theme') {
          updateTheme();
          break;
        }
      }
    });
    mo.observe(html, { attributes: true, attributeFilter: ['data-theme'] });
  } catch (_) {}
  
  // Écoute également les événements de changement sur .theme-controller ou #themeSelect (démo)
  document.addEventListener('change', (e) => {
    const t = e.target;
    if (!t) return;
    if ((t.classList && t.classList.contains('theme-controller')) || t.id === 'themeSelect') {
      updateTheme();
    }
  });
}

// Configuration de la synchronisation des thèmes
setupThemeSync();
