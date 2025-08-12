import './bootstrap';

// Cally: import via npm (déclaré comme web component ESM)
try {
  import('cally');
} catch (_) {}

// TreeView
import './treeview';

// Code Editor
import './code-editor';

// ScrollSpy
import './scrollspy';

// Lightbox
import './lightbox';

// Activer l'état indéterminé pour les checkboxes marquées
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('input[type="checkbox"][data-indeterminate="true"]').forEach((el) => {
    try { el.indeterminate = true; } catch (e) {}
  });
  // Gestion générique de sidebar (éviter tout conflit entre plusieurs sidebars)
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
      // Switch icon
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
});

// Popconfirm
import './popconfirm';

// Popover
import './popover';

// Stepper
import './stepper';

// Table
import './table';

// Color Picker
import './color-picker';

// Media Gallery
import './media-gallery';

// File Input
import './file-input';

// Input Mask
import './input-mask';

// Scroll Status
import './scroll-status';

// Transfer
import './transfer';


