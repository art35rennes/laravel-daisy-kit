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


