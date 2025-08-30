/**
 * Daisy Kit - Onboarding (Tour)
 *
 * Met en avant des éléments de l'UI avec un masque (spotlight) et
 * un panneau d'aide de type popover, plus navigation et sortie optionnelle.
 */

function clamp(value, min, max) {
  return Math.max(min, Math.min(max, value));
}

function readConfig(root) {
  try {
    const script = root.querySelector('script[data-onboarding-config]');
    if (!script) return null;
    const data = JSON.parse(script.textContent || '{}');
    return data && typeof data === 'object' ? data : null;
  } catch (_) { return null; }
}

function createOverlay() {
  const overlay = document.createElement('div');
  overlay.className = 'fixed inset-0 z-[70] pointer-events-none';
  overlay.style.transition = 'opacity 200ms ease';
  overlay.style.opacity = '0';
  return overlay;
}

function createDimLayer(maskMode) {
  const layer = document.createElement('div');
  layer.className = 'absolute inset-0';
  if (maskMode === 'dim' || maskMode === 'dim-blur') {
    layer.style.backgroundColor = 'hsla(var(--bc), 0.6)';
    layer.classList.add('backdrop-blur-0');
    if (maskMode === 'dim-blur') layer.classList.add('backdrop-blur-sm');
  }
  return layer;
}

function createSpotlightLayer() {
  const layer = document.createElement('div');
  layer.className = 'absolute inset-0 pointer-events-none';
  return layer;
}

function createPopover() {
  const panel = document.createElement('div');
  panel.className = 'absolute z-[75]';
  const content = document.createElement('div');
  // Styles du popover du kit
  content.className = 'relative rounded-box bg-base-100 shadow border border-base-200 p-4 w-80 max-w-[92vw]';
  panel.appendChild(content);

  const header = document.createElement('div');
  header.className = 'mb-2 font-medium text-base-content/90';
  content.appendChild(header);

  const body = document.createElement('div');
  body.className = 'text-sm leading-relaxed';
  content.appendChild(body);

  const footer = document.createElement('div');
  footer.className = 'mt-3 pt-3 border-t border-base-200 flex items-center justify-between';
  content.appendChild(footer);

  const left = document.createElement('div');
  left.className = 'flex gap-2';
  footer.appendChild(left);

  const right = document.createElement('div');
  right.className = 'flex gap-2 items-center';
  footer.appendChild(right);

  const btnPrev = document.createElement('button');
  btnPrev.type = 'button';
  btnPrev.className = 'btn btn-ghost btn-sm';
  left.appendChild(btnPrev);

  const btnNext = document.createElement('button');
  btnNext.type = 'button';
  btnNext.className = 'btn btn-primary btn-sm';
  right.appendChild(btnNext);

  const btnFinish = document.createElement('button');
  btnFinish.type = 'button';
  btnFinish.className = 'btn btn-success btn-sm hidden';
  right.appendChild(btnFinish);

  const btnSkip = document.createElement('button');
  btnSkip.type = 'button';
  btnSkip.className = 'btn btn-ghost btn-xs opacity-70';
  right.appendChild(btnSkip);

  return { panel, content, header, body, footer, btnPrev, btnNext, btnFinish, btnSkip };
}

function getElementRect(el) {
  const rect = el.getBoundingClientRect();
  return { x: rect.left + window.scrollX, y: rect.top + window.scrollY, width: rect.width, height: rect.height };
}

function positionPopover(pop, rect, placement, offset) {
  const panel = pop.panel;
  const x = rect.x, y = rect.y, w = rect.width, h = rect.height;
  const vw = window.scrollX + document.documentElement.clientWidth;
  const vh = window.scrollY + document.documentElement.clientHeight;
  let top = 0, left = 0;

  const prefer = placement === 'auto' ? ['bottom','right','top','left'] : [placement];
  const panelRect = panel.getBoundingClientRect();
  const pw = panelRect.width || 320;
  const ph = panelRect.height || 120;

  function fitsBottom() { return y + h + offset + ph <= vh; }
  function fitsTop() { return y - offset - ph >= window.scrollY; }
  function fitsRight() { return x + w + offset + pw <= vw; }
  function fitsLeft() { return x - offset - pw >= window.scrollX; }

  let place = prefer[0];
  if (placement === 'auto') {
    if (fitsBottom()) place = 'bottom'; else if (fitsRight()) place = 'right'; else if (fitsTop()) place = 'top'; else if (fitsLeft()) place = 'left'; else place = 'bottom';
  }

  // Augmenter l'offset pour éviter que le popover se superpose à l'élément
  const safeOffset = Math.max(offset, 20);
  
  if (place === 'bottom') { top = y + h + safeOffset; left = x + (w/2) - (pw/2); }
  else if (place === 'top') { top = y - ph - safeOffset; left = x + (w/2) - (pw/2); }
  else if (place === 'right') { top = y + (h/2) - (ph/2); left = x + w + safeOffset; }
  else if (place === 'left') { top = y + (h/2) - (ph/2); left = x - pw - safeOffset; }

  const pad = 8;
  left = clamp(left, window.scrollX + pad, vw - pw - pad);
  top = clamp(top, window.scrollY + pad, vh - ph - pad);
  
  // S'assurer que le popover ne se superpose pas à l'élément cible
  if (place === 'bottom' && top < y + h + 10) top = y + h + 10;
  if (place === 'top' && top + ph > y - 10) top = y - ph - 10;
  if (place === 'right' && left < x + w + 10) left = x + w + 10;
  if (place === 'left' && left + pw > x - 10) left = x - pw - 10;
  
  panel.style.top = `${top}px`;
  panel.style.left = `${left}px`;
}

function updateSpotlightMask(spotlightLayer, rect, radius) {
  if (!spotlightLayer) return;
  
  const r = Math.max(0, radius);
  const x = rect.x, y = rect.y, w = rect.width, h = rect.height;
  
  // Utiliser des dimensions plus sûres pour éviter les excroissances
  const docWidth = Math.max(document.documentElement.scrollWidth, document.body.scrollWidth, window.innerWidth);
  const docHeight = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, window.innerHeight);
  
  const path = `path('M0 0 H${docWidth} V${docHeight} H0 Z M${x - r} ${y - r} H${x + w + r} V${y + h + r} H${x - r} Z')`;
  spotlightLayer.style.clipPath = path;
  spotlightLayer.style.WebkitClipPath = path;
  spotlightLayer.style.background = 'rgba(0,0,0,0.4)';
}

function ringHighlight(targetEl, enable, highlightedTargets = null) {
  if (!targetEl) return;
  
  if (enable) {
    // Ajouter le ring de mise en évidence
    targetEl.classList.add('ring', 'ring-primary', 'ring-offset-2', 'ring-offset-base-100');
    
    // Ajouter un z-index élevé pour s'assurer que l'élément est visible au-dessus du voile
    targetEl.style.position = 'relative';
    targetEl.style.zIndex = '80';
    
    if (highlightedTargets) highlightedTargets.add(targetEl);
  } else {
    // Nettoyer le ring
    targetEl.classList.remove('ring', 'ring-primary', 'ring-offset-2', 'ring-offset-base-100');
    
    // Restaurer le style original
    targetEl.style.position = '';
    targetEl.style.zIndex = '';
    
    if (highlightedTargets) highlightedTargets.delete(targetEl);
  }
}

function scrollIntoViewIfNeeded(targetEl) {
  try { targetEl.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' }); } catch (_) {}
}

function setupOnboarding(root) {
  const config = readConfig(root);
  if (!config || !Array.isArray(config.steps) || config.steps.length === 0) return;

  let current = -1;
  let timer = null;
  let activeTarget = null;
  let highlightedTargets = new Set(); // Suivre tous les éléments qui ont été highlightés
  let overlay = null;
  let dim = null;
  let spotlight = null;
  let popover = null;
  let isRunning = false; // Éviter les conflits de démarrage multiple

  function createElements() {
    overlay = createOverlay();
    dim = createDimLayer(config.mask);
    spotlight = createSpotlightLayer();
    popover = createPopover();
    overlay.appendChild(dim);
    overlay.appendChild(spotlight);
    document.body.appendChild(overlay);
    document.body.appendChild(popover.panel);
    
    // Ajouter les event listeners après création du popover
    setupEventListeners();
  }
  
  function setupEventListeners() {
    if (!popover) return;
    popover.btnPrev.addEventListener('click', (e) => { e.preventDefault(); prev(); });
    popover.btnNext.addEventListener('click', (e) => { e.preventDefault(); next(); });
    popover.btnFinish.addEventListener('click', (e) => { e.preventDefault(); finish(); });
    popover.btnSkip.addEventListener('click', (e) => { e.preventDefault(); if (config.allowSkip) skip(); });
  }

  function setOverlayVisible(visible) {
    if (!overlay) return;
    overlay.style.pointerEvents = visible ? 'auto' : 'none';
    overlay.style.opacity = visible ? '1' : '0';
  }

  function clearTimer() { if (timer) { clearTimeout(timer); timer = null; } }
  
  function cleanupTarget() { 
    if (activeTarget) { 
      ringHighlight(activeTarget, false, highlightedTargets); 
      activeTarget = null; 
    } 
  }
  
  function cleanupAllTargets() {
    // Nettoyer tous les éléments qui ont été highlightés
    highlightedTargets.forEach(target => {
      try {
        ringHighlight(target, false, highlightedTargets);
      } catch (e) {
        // Ignorer les erreurs si l'élément n'existe plus dans le DOM
        console.warn('Erreur lors du nettoyage d\'un target:', e);
      }
    });
    highlightedTargets.clear();
    activeTarget = null;
  }
  
  function safeRemoveElement(element) {
    try {
      if (element && element.parentNode) {
        element.remove();
      }
    } catch (e) {
      console.warn('Erreur lors de la suppression d\'un élément:', e);
    }
  }

  function showStep(index) {
    clearTimer();
    cleanupTarget(); // Nettoyer l'ancien target avant de passer au suivant
    if (index < 0 || index >= config.steps.length) return;
    const step = config.steps[index];
    const placement = step.placement || 'auto';
    const stepInteractive = step.interactive != null ? !!step.interactive : !!config.interactive;

    const target = step.target ? document.querySelector(step.target) : null;
    if (!target) return next();

    activeTarget = target;
    const rect = getElementRect(target);
    updateSpotlightMask(spotlight, rect, config.radius);
    if (config.highlight) ringHighlight(target, true, highlightedTargets);
    if (config.highlight) scrollIntoViewIfNeeded(target);

    popover.header.textContent = step.title || '';
    popover.header.classList.toggle('hidden', !step.title);
    popover.body.textContent = step.content || '';
    popover.btnPrev.textContent = config.labels.prev;
    popover.btnNext.textContent = config.labels.next;
    popover.btnFinish.textContent = config.labels.finish;
    popover.btnSkip.textContent = config.labels.skip;
    popover.btnSkip.classList.toggle('hidden', !config.allowSkip);

    overlay.style.pointerEvents = stepInteractive ? 'none' : 'auto';
    dim.style.pointerEvents = 'auto';
    spotlight.style.pointerEvents = 'none';

    positionPopover(popover, rect, placement, config.offset);

    popover.btnPrev.disabled = index === 0;
    popover.btnNext.classList.toggle('hidden', index === config.steps.length - 1);
    popover.btnFinish.classList.toggle('hidden', index !== config.steps.length - 1);

    if (step.auto && step.auto > 0) { timer = setTimeout(() => { next(); }, step.auto); }

    root.dispatchEvent(new CustomEvent('onboarding:step', { detail: { index }, bubbles: true }));
  }

  function start() {
    // Éviter le démarrage multiple simultané
    if (isRunning) return;
    isRunning = true;
    
    // Nettoyer tout d'abord
    cleanupAllTargets();
    
    // Supprimer les éléments existants s'ils existent déjà
    safeRemoveElement(overlay);
    if (popover && popover.panel) {
      safeRemoveElement(popover.panel);
    }
    
    // Créer de nouveaux éléments à chaque démarrage pour permettre un redémarrage propre
    createElements();
    setOverlayVisible(true);
    current = 0;
    root.dispatchEvent(new CustomEvent('onboarding:start', { detail: { index: 0 }, bubbles: true }));
    showStep(current);
    bindWindow();
  }

  function finish() {
    clearTimer();
    cleanupAllTargets(); // Nettoyer tous les targets, pas seulement l'actuel
    setOverlayVisible(false);
    setTimeout(() => {
      safeRemoveElement(overlay);
      if (popover && popover.panel) {
        safeRemoveElement(popover.panel);
      }
      overlay = null;
      popover = null;
      isRunning = false; // Permettre un nouveau démarrage
    }, 200);
    root.dispatchEvent(new CustomEvent('onboarding:finish', { bubbles: true }));
    unbindWindow();
    // Réinitialiser pour permettre un nouveau démarrage
    current = -1;
  }

  function confirmAnd(fn) {
    const c = config.confirm || {};
    if (!c.enabled) return fn();

    const wrapper = document.createElement('span');
    wrapper.setAttribute('data-popconfirm', '');
    wrapper.className = 'relative inline-block';

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'popconfirm-trigger hidden';
    wrapper.appendChild(trigger);

    const panel = document.createElement('div');
    panel.className = 'popconfirm-panel absolute z-[80] top-0 left-0';
    panel.innerHTML = `
      <div class="rounded-box bg-base-100 shadow border border-base-200 p-4 w-64">
        <div class="text-sm mb-3">${c.text || ''}</div>
        <div class="flex justify-end gap-2">
          <button class="btn btn-ghost btn-sm" data-popconfirm-action="cancel">${c.cancel || 'Annuler'}</button>
          <button class="btn btn-primary btn-sm" data-popconfirm-action="confirm">${c.ok || 'OK'}</button>
        </div>
      </div>`;
    wrapper.appendChild(panel);
    document.body.appendChild(wrapper);

    const ref = popover.btnSkip.getBoundingClientRect();
    wrapper.style.position = 'absolute';
    wrapper.style.top = `${window.scrollY + ref.bottom + 6}px`;
    wrapper.style.left = `${window.scrollX + ref.right - 260}px`;

    const afterLoad = () => {
      try { window.DaisyPopconfirm?.initAll?.(); } catch(_) {}
      const cleanup = () => { try { wrapper.remove(); } catch(_) {} };
      const onConfirm = () => { cleanup(); fn(); };
      const onCancel = () => { cleanup(); };
      wrapper.addEventListener('popconfirm:confirm', onConfirm, { once: true });
      wrapper.addEventListener('popconfirm:cancel', onCancel, { once: true });
      trigger.click();
    };

    if (!window.DaisyPopconfirm) {
      try { import('./popconfirm').then(() => { afterLoad(); }); }
      catch(_) { afterLoad(); }
    } else { afterLoad(); }
  }

  function skip() {
    confirmAnd(() => {
      clearTimer();
      cleanupAllTargets(); // Nettoyer tous les targets, pas seulement l'actuel
      setOverlayVisible(false);
      setTimeout(() => {
        safeRemoveElement(overlay);
        if (popover && popover.panel) {
          safeRemoveElement(popover.panel);
        }
        overlay = null;
        popover = null;
        isRunning = false; // Permettre un nouveau démarrage
      }, 200);
      root.dispatchEvent(new CustomEvent('onboarding:skip', { bubbles: true }));
      unbindWindow();
      // Réinitialiser pour permettre un nouveau démarrage
      current = -1;
    });
  }

  function next() {
    const idx = current + 1;
    if (idx >= config.steps.length) return finish();
    current = idx;
    showStep(current);
  }

  function prev() {
    const idx = current - 1;
    current = Math.max(0, idx);
    showStep(current);
  }

  function onResizeScroll() {
    if (current < 0 || current >= config.steps.length) return;
    if (!spotlight || !popover) return; // Vérifier que les éléments existent
    
    const step = config.steps[current];
    const target = step.target ? document.querySelector(step.target) : null;
    if (!target) return;
    const rect = getElementRect(target);
    updateSpotlightMask(spotlight, rect, config.radius);
    positionPopover(popover, rect, step.placement || 'auto', config.offset);
  }

  function bindWindow() {
    window.addEventListener('resize', onResizeScroll);
    window.addEventListener('scroll', onResizeScroll, { passive: true });
    if (config.keyboard) {
      window.addEventListener('keydown', onKeydown, { capture: true });
    }
  }

  function unbindWindow() {
    window.removeEventListener('resize', onResizeScroll);
    window.removeEventListener('scroll', onResizeScroll, { passive: true });
    window.removeEventListener('keydown', onKeydown, { capture: true });
  }

  function onKeydown(e) {
    if (e.key === 'Escape' && config.allowSkip) {
      e.preventDefault();
      skip();
    } else if (e.key === 'ArrowRight') {
      e.preventDefault();
      next();
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault();
      prev();
    }
  }

  // Nettoyer l'ancien onboarding s'il existe déjà
  if (root.__onboarding && typeof root.__onboarding.finish === 'function') {
    try {
      root.__onboarding.finish();
    } catch (e) {
      console.warn('Erreur lors du nettoyage de l\'ancien onboarding:', e);
    }
  }

  if (root.dataset.start === '1') setTimeout(start, 50);

  root.__onboarding = { start, next, prev, finish, skip };
}

function initAllOnboarding() {
  document.querySelectorAll('[data-onboarding="1"]').forEach((el) => {
    // Toujours réinitialiser pour permettre les redémarrages
    setupOnboarding(el);
  });
}

window.DaisyOnboarding = { initAll: initAllOnboarding };

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAllOnboarding);
} else {
  initAllOnboarding();
}


