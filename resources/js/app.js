import './bootstrap';

async function dynamicImportIf(selector, loader) {
  try {
    if (document.querySelector(selector)) {
      await loader();
    }
  } catch (_) {}
}

function onReady(fn) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fn);
  } else {
    fn();
  }
}

onReady(async () => {
  // Checkbox indéterminée (DaisyUI): état mixed initial puis normalisation au change
  document.querySelectorAll('input[type="checkbox"][data-indeterminate="true"]').forEach((el) => {
    try {
      el.checked = false;
      el.indeterminate = true;
      el.setAttribute('aria-checked', 'mixed');
      el.addEventListener('change', () => {
        el.indeterminate = false;
        el.setAttribute('aria-checked', el.checked ? 'true' : 'false');
      });
    } catch (e) {}
  });

  // Sidebar generic behavior
  document.querySelectorAll('[data-sidebar-root] .sidebar-toggle').forEach((button) => {
    const aside = button.closest('[data-sidebar-root]');
    if (!aside) return;
    const storageKey = aside.dataset.storageKey || 'daisy.sidebar';
    const wideClass = aside.dataset.wideClass;
    const collapsedClass = aside.dataset.collapsedClass || 'w-20';
    const setCollapsed = (collapsed) => {
      aside.dataset.collapsed = collapsed ? '1' : '0';
      if (wideClass) aside.classList.toggle(wideClass, !collapsed);
      if (collapsedClass) aside.classList.toggle(collapsedClass, collapsed);
      aside.querySelectorAll('.sidebar-label').forEach((el) => el.classList.toggle('hidden', collapsed));
      const txt = aside.querySelector('.sidebar-label-toggle');
      if (txt) txt.textContent = collapsed ? 'Expand' : 'Collapse';
      try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch (_) {}
      const icon = button.querySelector('svg');
      if (icon) {
        icon.outerHTML = collapsed
          ? '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 19.5 21 12l-7.5-7.5M3 4.5v15"/></svg>'
          : '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 4.5 3 12l7.5 7.5M21 19.5v-15"/></svg>';
      }
    };
    try {
      const persisted = localStorage.getItem(storageKey);
      if (persisted === '1' || persisted === '0') setCollapsed(persisted === '1');
    } catch (_) {}
    button.addEventListener('click', () => setCollapsed(aside.dataset.collapsed !== '1'));
  });

  // Web component Cally (calendrier)
  await dynamicImportIf('.cally, calendar-date, calendar-range, calendar-month', async () => {
    await import('cally');
  });

  // Radios "uncheckable": permet de décocher un radio déjà coché si data-uncheckable="1"
  // On mémorise l'état AVANT le clic pour distinguer check vs uncheck.
  document.addEventListener('mousedown', (e) => {
    let input = null;
    if (e.target instanceof HTMLInputElement && e.target.type === 'radio') input = e.target;
    else if (e.target instanceof HTMLLabelElement && e.target.control?.type === 'radio') input = e.target.control;
    else {
      const label = e.target.closest('label');
      if (label && label.control?.type === 'radio') input = label.control;
    }
    if (!input) return;
    if (input.dataset.uncheckable !== '1') return;
    input.dataset.wasChecked = input.checked ? '1' : '0';
  }, { capture: true });

  document.addEventListener('click', (e) => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement)) return;
    if (input.type !== 'radio') return;
    if (input.dataset.uncheckable !== '1') return;
    const wasChecked = input.dataset.wasChecked === '1';
    delete input.dataset.wasChecked;
    // Ne décocher que si l'input était déjà coché avant le clic
    if (wasChecked) {
      setTimeout(() => {
        input.checked = false;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }, 0);
    }
  });

  // Support clavier (Space) pour décocher un radio déjà coché
  document.addEventListener('keydown', (e) => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement)) return;
    if (input.type !== 'radio') return;
    if (input.dataset.uncheckable === '1' && (e.key === ' ' || e.key === 'Spacebar') && input.checked) {
      e.preventDefault();
      input.checked = false;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
  });

  // Modules lazy selon présence DOM
  await dynamicImportIf('[data-treeview="1"]', async () => { await import('./treeview'); });
  await dynamicImportIf('.code-editor', async () => { await import('./code-editor'); });
  await dynamicImportIf('[data-scrollspy="1"]', async () => { await import('./scrollspy'); });
  await dynamicImportIf('[data-lightbox="1"]', async () => { await import('./lightbox'); });
  await dynamicImportIf('[data-popconfirm], [data-popconfirm-modal]', async () => { await import('./popconfirm'); });
  await dynamicImportIf('[data-popover]', async () => { await import('./popover'); });
  await dynamicImportIf('[data-stepper="true"]', async () => { await import('./stepper'); });
  await dynamicImportIf('[data-table-select]:not([data-table-select="none"])', async () => { await import('./table'); });
  await dynamicImportIf('[data-colorpicker="1"]', async () => { await import('./color-picker'); });
  await dynamicImportIf('[data-media-gallery="1"]', async () => { await import('./media-gallery'); });
  await dynamicImportIf('[data-fileinput="1"]', async () => { await import('./file-input'); });
  await dynamicImportIf('input[data-inputmask="1"], input[data-obfuscate="1"]', async () => { await import('./input-mask'); });
  await dynamicImportIf('[data-scrollstatus="1"]', async () => { await import('./scroll-status'); });
  await dynamicImportIf('[data-transfer="1"]', async () => { await import('./transfer'); });
});

