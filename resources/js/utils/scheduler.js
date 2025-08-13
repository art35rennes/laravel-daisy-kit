// Petites fonctions utilitaires de planification pour éviter de bloquer le thread principal pendant le chargement de la page

/**
 * Exécute une fonction quand le navigateur est inactif
 * @param {Function} fn - Fonction à exécuter
 * @param {number} timeout - Timeout de fallback en ms (défaut: 300)
 * @returns {number} ID de callback ou timeout
 */
export function onIdle(fn, timeout = 300) {
  try {
    if ('requestIdleCallback' in window) {
      // @ts-ignore
      return window.requestIdleCallback(fn, { timeout });
    }
  } catch (_) {}
  return window.setTimeout(fn, Math.min(16, timeout));
}

/**
 * Exécute une fonction à la prochaine frame d'animation
 * @param {Function} fn - Fonction à exécuter
 * @returns {number} ID de requestAnimationFrame
 */
export function onNextFrame(fn) {
  return window.requestAnimationFrame(() => fn());
}

/**
 * Importe un module de façon différée si au moins un élément correspondant existe dans le DOM
 * @param {string} selector - Sélecteur CSS à vérifier
 * @param {Function} importer - Fonction d'import à exécuter
 * @param {number} timeout - Timeout pour l'idle callback (défaut: 300)
 */
export function importWhenIdle(selector, importer, timeout = 300) {
  try {
    if (!document.querySelector(selector)) return;
  } catch (_) { return; }
  onIdle(() => { try { importer(); } catch (_) {} }, timeout);
}

/**
 * Initialise des éléments quand ils deviennent visibles dans le viewport
 * Traite les éléments par petits lots pour éviter les blocages
 * @param {string|Array} targets - Sélecteur CSS ou tableau d'éléments
 * @param {Function} init - Fonction d'initialisation pour chaque élément
 * @param {Object} options - Options de configuration
 * @param {string} options.rootMargin - Marge pour l'IntersectionObserver (défaut: '200px 0px')
 * @param {number} options.threshold - Seuil de visibilité (défaut: 0.01)
 * @param {number} options.maxPerFrame - Nombre max d'éléments à traiter par frame (défaut: 2)
 * @param {number} options.budgetMs - Budget temps par frame en ms (défaut: 8)
 */
export function initWhenVisible(targets, init, options = {}) {
  const elements = Array.isArray(targets) ? targets : Array.from(document.querySelectorAll(targets));
  if (!elements.length) return;
  const settings = {
    rootMargin: options.rootMargin || '200px 0px',
    threshold: options.threshold == null ? 0.01 : options.threshold,
    maxPerFrame: options.maxPerFrame == null ? 2 : Math.max(1, Number(options.maxPerFrame)),
    budgetMs: options.budgetMs == null ? 8 : Math.max(1, Number(options.budgetMs)),
  };
  const queue = [];
  let scheduled = false;
  function processQueue() {
    scheduled = false;
    const start = performance.now();
    let processed = 0;
    while (queue.length) {
      const el = queue.shift();
      try { init(el); } catch (_) {}
      processed++;
      // Respecter un budget de frame pour éviter les janks
      if (processed >= settings.maxPerFrame || (performance.now() - start) >= settings.budgetMs) {
        onNextFrame(() => { processQueue(); });
        return;
      }
    }
  }
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const el = entry.target;
        queue.push(el);
        obs.unobserve(el);
        if (!scheduled) { scheduled = true; onNextFrame(() => { processQueue(); }); }
      }
    });
  }, { rootMargin: settings.rootMargin, threshold: settings.threshold });
  elements.forEach((el) => obs.observe(el));
}

/**
 * Wrapper pour postTask (Chrome) avec fallback setTimeout
 * Permet de planifier des tâches avec différentes priorités
 * @param {Function} fn - Fonction à exécuter
 * @param {Object} opts - Options de planification
 * @param {string} opts.priority - Priorité de la tâche ('background', 'user-blocking', etc.)
 * @param {number} opts.delay - Délai avant exécution en ms
 * @param {AbortSignal} opts.signal - Signal d'annulation
 * @returns {Promise|number} TaskController ou ID de setTimeout
 */
export function postTask(fn, opts = {}) {
  try {
    // @ts-ignore
    if (window.scheduler && typeof window.scheduler.postTask === 'function') {
      // @ts-ignore
      return window.scheduler.postTask(fn, { priority: opts.priority || 'background', delay: opts.delay || 0, signal: opts.signal });
    }
  } catch (_) {}
  const delay = (opts && typeof opts.delay === 'number') ? opts.delay : 0;
  return window.setTimeout(fn, delay);
}

/**
 * Crée un limiteur de concurrence pour séquencer des tâches lourdes
 * Evite de surcharger le thread principal en limitant le nombre de tâches simultanées
 * @param {number} maxConcurrent - Nombre maximum de tâches simultanées (défaut: 1)
 * @returns {Function} Fonction pour ajouter une tâche à la queue
 */
export function createLimiter(maxConcurrent = 1) {
  const queue = [];
  let active = 0;
  async function runNext() {
    if (active >= maxConcurrent) return;
    const job = queue.shift();
    if (!job) return;
    active++;
    try {
      await job();
    } catch (_) {}
    finally {
      active--;
      if (queue.length) {
        // Laisser respirer le main thread
        onNextFrame(runNext);
      }
    }
  }
  return function enqueue(job) {
    queue.push(job);
    runNext();
  };
}

/**
 * Déclenche un import dynamique lorsque au moins un élément correspondant entre proche du viewport
 * Utile pour charger des modules seulement quand ils sont nécessaires
 * @param {string} selector - Sélecteur CSS des éléments à surveiller
 * @param {Function} importer - Fonction d'import à exécuter
 * @param {Object} options - Options de configuration
 * @param {string} options.rootMargin - Marge pour l'IntersectionObserver (défaut: '400px 0px')
 * @param {number} options.threshold - Seuil de visibilité (défaut: 0.01)
 */
export function importWhenNearViewport(selector, importer, options = {}) {
  let elements = [];
  try { elements = Array.from(document.querySelectorAll(selector)); } catch (_) { return; }
  if (!elements.length) return;
  const rootMargin = options.rootMargin || '400px 0px';
  const threshold = options.threshold == null ? 0.01 : options.threshold;
  const once = { done: false };
  const obs = new IntersectionObserver((entries) => {
    if (once.done) return;
    for (const entry of entries) {
      if (entry.isIntersecting) {
        once.done = true;
        obs.disconnect();
        // Utiliser postTask pour arrière-plan
        postTask(() => { try { importer(); } catch (_) {} }, { priority: 'background' });
        break;
      }
    }
  }, { rootMargin, threshold });
  elements.forEach((el) => obs.observe(el));
}
