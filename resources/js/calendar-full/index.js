import { mountAllCalendars } from './core.js';

function onReady(fn){ if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn); else fn(); }

onReady(() => {
  try { mountAllCalendars(); } catch (_) {}
});

export { mountAllCalendars };


