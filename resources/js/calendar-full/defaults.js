/**
 * Valeurs par défaut et helpers de normalisation des options
 */

export const DEFAULTS = {
  view: 'month',
  views: ['year','month','week','day','list'],
  initialDate: null,
  firstDay: 1,
  hourStart: 6,
  hourEnd: 22,
  height: 'auto',
  detail: 'modal', // 'none' | 'modal'
};

/**
 * Construit les options depuis data-options (JSON) + fallback dataset simples
 * @param {HTMLElement} el
 * @returns {object}
 */
export function readOptions(el){
  let base = {};
  try { base = JSON.parse(el.getAttribute('data-options') || '{}') || {}; } catch(_) {}
  const opts = { ...DEFAULTS, ...base };
  // Normalisations
  if (!Array.isArray(opts.views) || !opts.views.length) opts.views = DEFAULTS.views.slice();
  opts.firstDay = clampInt(opts.firstDay, 0, 6, DEFAULTS.firstDay);
  opts.hourStart = clampInt(opts.hourStart, 0, 23, DEFAULTS.hourStart);
  opts.hourEnd = clampInt(opts.hourEnd, opts.hourStart + 1, 24, DEFAULTS.hourEnd);
  if (typeof opts.height !== 'string') opts.height = String(opts.height);
  if (!['modal','none'].includes(opts.detail)) opts.detail = DEFAULTS.detail;
  return opts;
}

function clampInt(v, min, max, fallback){
  const n = Number(v);
  if (!Number.isFinite(n)) return fallback;
  return Math.min(max, Math.max(min, Math.trunc(n)));
}


