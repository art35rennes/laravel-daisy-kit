import { mountAllCalendars, mount } from './core.js';

function onReady(fn){ if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn); else fn(); }

onReady(() => {
  try { mountAllCalendars(); } catch (_) {}
});

// Export pour le système data-module (kit/index.js)
export default mount;
export { mount, mountAllCalendars };


