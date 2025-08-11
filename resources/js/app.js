import './bootstrap';

// Cally: import via npm (déclaré comme web component ESM)
try {
  import('cally');
} catch (_) {}

// Activer l'état indéterminé pour les checkboxes marquées
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('input[type="checkbox"][data-indeterminate="true"]').forEach((el) => {
    try { el.indeterminate = true; } catch (e) {}
  });
});
