/*
 * Daisy Kit - ScrollSpy (léger basé sur IntersectionObserver)
 * API: window.DaisyScrollSpy
 * - init(navEl)      : Initialise le scrollspy sur un élément de navigation donné
 * - refresh(navEl)   : Réinitialise et réobserve les sections (utile si le DOM change)
 * - dispose(navEl)   : Nettoie tous les écouteurs et observers liés à la nav
 *
 * Data-attributes (sur le nav):
 * - data-container: sélecteur du conteneur scrollable (optionnel)
 * - data-active-class: classe appliquée au lien actif (défaut 'active')
 * - data-offset: offset px pour le scroll programmatique
 * - data-smooth: 'true' => scroll smooth
 * - data-track: sélecteur des sections à observer (défaut 'section')
 * - data-root-margin, data-threshold: options IntersectionObserver (JSON pour threshold)
 * - data-autogen: 'true' => génère la nav à partir des sections (utilise les h2..h6)
 */

/**
 * Résout le conteneur scrollable à partir de l'attribut data-container.
 * Retourne l'élément correspondant ou null si absent/non trouvé.
 * @param {HTMLElement} nav
 * @returns {HTMLElement|null}
 */
function resolveContainer(nav) {
  const sel = nav.dataset.container;
  if (!sel) return null;
  try { return document.querySelector(sel); } catch (_) { return null; }
}

/**
 * Construit la liste des couples { a, target } pour chaque lien d'ancrage de la nav.
 * @param {HTMLElement} nav
 * @returns {Array<{a: HTMLAnchorElement, target: HTMLElement}>}
 */
function buildItems(nav) {
  const list = nav.querySelectorAll('a[href^="#"]');
  const items = [];
  list.forEach((a) => {
    const id = decodeURIComponent(a.getAttribute('href') || '').slice(1);
    if (!id) return;
    const target = document.getElementById(id);
    if (target) items.push({ a, target });
  });
  return items;
}

/**
 * Applique la classe active sur le lien donné, la retire sur tous les autres.
 * @param {HTMLElement} nav
 * @param {HTMLAnchorElement|null} a
 */
function setActive(nav, a) {
  const activeClass = nav.dataset.activeClass || 'active';
  nav.querySelectorAll('a').forEach((el) => el.classList.remove(activeClass));
  if (a) a.classList.add(activeClass);
}

/**
 * Détermine si le scroll doit être smooth (doux) selon data-smooth.
 * @param {HTMLElement} nav
 * @returns {boolean}
 */
function isSmooth(nav) { return nav.dataset.smooth === 'true'; }

/**
 * Attache un gestionnaire de clic sur la nav pour gérer le scroll programmatique.
 * Prend en compte l'offset, le smooth, et le conteneur scrollable.
 * @param {HTMLElement} nav
 */
function attachClicks(nav) {
  if (nav.__sspClicks) return; // Empêche l'attachement multiple
  nav.__sspClicks = true;
  nav.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a || !nav.contains(a)) return;
    const id = decodeURIComponent(a.getAttribute('href') || '').slice(1);
    const tgt = document.getElementById(id);
    if (!tgt) return;
    e.preventDefault();
    const container = resolveContainer(nav);
    const offset = parseInt(nav.dataset.offset || '0', 10) || 0;
    if (container) {
      // Scroll dans un conteneur custom (overflow)
      const cRect = container.getBoundingClientRect();
      const tRect = tgt.getBoundingClientRect();
      const top = (tRect.top - cRect.top) + container.scrollTop - offset;
      container.scrollTo({ top, behavior: isSmooth(nav) ? 'smooth' : 'auto' });
    } else {
      // Scroll global (window)
      const y = tgt.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top: y, behavior: isSmooth(nav) ? 'smooth' : 'auto' });
    }
  });
}

/**
 * Calcule la section la plus visible dans le viewport/conteneur et active le lien correspondant.
 * Utilisé à chaque notification d'IntersectionObserver ou scroll/resize.
 * @param {HTMLElement} nav
 */
function computeActive(nav) {
  const container = resolveContainer(nav) || undefined;
  const items = nav.__sspItems || [];
  const targets = nav.__sspTargets || [];
  if (!items.length || !targets.length) return;
  // Récupère le rectangle visible du conteneur (ou du document)
  const rootRect = (container || document.documentElement).getBoundingClientRect();
  let best = null; let bestRatio = -1;
  targets.forEach((t) => {
    const r = t.getBoundingClientRect();
    // Calcule la hauteur visible de la section dans le conteneur
    const visibleH = Math.max(0, Math.min(r.bottom, rootRect.bottom) - Math.max(r.top, rootRect.top));
    const ratio = visibleH / Math.max(1, r.height);
    // On choisit la section la plus visible (ratio le plus élevé)
    if (r.bottom > rootRect.top && r.top < rootRect.bottom && ratio >= bestRatio) { best = t; bestRatio = ratio; }
  });
  if (best) {
    const cur = items.find((it) => it.target === best);
    if (cur) setActive(nav, cur.a);
  }
}

/**
 * Met en place l'observation des sections via IntersectionObserver.
 * Gère aussi les écouteurs scroll/resize pour une robustesse maximale.
 * @param {HTMLElement} nav
 */
function observe(nav) {
  const track = nav.dataset.track || 'section';
  const container = resolveContainer(nav) || undefined;
  const rootMargin = nav.dataset.rootMargin || '0px 0px -25%';
  const thresholdRaw = nav.dataset.threshold;
  let threshold = [0.1, 0.5, 1];
  try { if (thresholdRaw) threshold = JSON.parse(thresholdRaw); } catch (_) {}
  const opts = { root: container || null, rootMargin, threshold };
  const items = buildItems(nav);
  const targets = items.map((it) => it.target);
  if (!targets.length) return;
  // Création de l'observer
  const io = new IntersectionObserver(() => {
    // À chaque notification, on recalcule la section la plus visible
    computeActive(nav);
  }, opts);
  targets.forEach((t) => io.observe(t));
  nav.__sspIO = io;
  nav.__sspTargets = targets;
  nav.__sspItems = items;
  // Définir l'actif initial + écouteurs scroll/resize pour fallback
  computeActive(nav);
  const scrollEl = container || window;
  const onScroll = () => computeActive(nav);
  scrollEl.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  // Fonction de nettoyage
  nav.__sspOff = () => {
    scrollEl.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onScroll);
  };
}

/**
 * Rafraîchit complètement le scrollspy sur la nav (utile après modification du DOM).
 * @param {HTMLElement} nav
 */
function refresh(nav) {
  dispose(nav);
  observe(nav);
}

/**
 * Nettoie tous les observers et écouteurs liés à la nav.
 * @param {HTMLElement} nav
 */
function dispose(nav) {
  if (nav?.__sspIO) {
    try { nav.__sspIO.disconnect(); } catch (_) {}
  }
  nav.__sspIO = null;
  nav.__sspTargets = null;
  nav.__sspItems = null;
  if (nav.__sspOff) { try { nav.__sspOff(); } catch (_) {} nav.__sspOff = null; }
}

/**
 * Génère automatiquement la structure de navigation (ul/li/a) à partir des sections du conteneur.
 * Utilise le premier titre h2..h6 trouvé dans chaque section comme libellé.
 * @param {HTMLElement} nav
 */
function autogen(nav) {
  if (nav.dataset.autogen !== 'true') return;
  const container = resolveContainer(nav) || document;
  const track = nav.dataset.track || 'section';
  const ul = nav.querySelector('ul'); if (!ul) return;
  ul.innerHTML = '';
  const seen = new Set();
  container.querySelectorAll(track).forEach((sec) => {
    // Cherche le titre de la section (h2..h6)
    const h = sec.querySelector('h2, h3, h4, h5, h6');
    const text = h?.textContent?.trim() || sec.id || 'Section';
    // Génère un id unique si besoin
    let id = sec.id || text.toLowerCase().replace(/[^\w\s-]/g,'').replace(/\s+/g,'-');
    let base = id, i = 2; while (seen.has(id)) { id = base + '-' + (i++); }
    if (!sec.id) sec.id = id;
    seen.add(id);
    // Crée l'élément de navigation
    const li = document.createElement('li');
    const a = document.createElement('a');
    a.href = `#${id}`; a.textContent = text; a.className = 'truncate';
    li.appendChild(a); ul.appendChild(li);
  });
}

/**
 * Initialise le scrollspy sur un élément de navigation donné.
 * @param {HTMLElement} nav
 */
function init(nav) {
  if (!nav || nav.__sspInit) return;
  nav.__sspInit = true;
  autogen(nav);      // Génération auto si demandé
  attachClicks(nav); // Gestion du scroll programmatique
  observe(nav);      // Observation des sections
}

/**
 * Initialise tous les scrollspy présents dans le DOM (data-scrollspy="1").
 */
function initAll() {
  document.querySelectorAll('[data-scrollspy="1"]').forEach(init);
}

// Expose l'API globale
window.DaisyScrollSpy = { init, initAll, refresh, dispose };

// Initialisation automatique (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
