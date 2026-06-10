const THEME_KEY = 'daisy-theme';

function controllers() {
  return Array.from(document.querySelectorAll('.theme-controller'));
}

function applyTheme(theme) {
  if (!theme) {
    return;
  }

  document.documentElement.setAttribute('data-theme', theme);

  try {
    localStorage.setItem(THEME_KEY, theme);
  } catch (_) {}

  controllers().forEach((el) => {
    if (el.type === 'radio') {
      el.checked = el.value === theme;
    }
  });
}

function readSavedTheme() {
  try {
    return localStorage.getItem(THEME_KEY);
  } catch (_) {
    return null;
  }
}

function readDefaultTheme() {
  return document.querySelector('[data-module="theme-controller"][data-default-theme]')?.dataset.defaultTheme || null;
}

function initThemeController() {
  const saved = readSavedTheme();
  const current = saved || document.documentElement.getAttribute('data-theme') || readDefaultTheme() || 'light';

  applyTheme(current);
}

if (typeof document !== 'undefined' && !document.documentElement.dataset.daisyThemeControllerBound) {
  document.documentElement.dataset.daisyThemeControllerBound = '1';

  document.addEventListener('change', (event) => {
    const target = event.target;

    if (target?.classList?.contains('theme-controller')) {
      applyTheme(target.value);
    }
  });
}

export default function init() {
  initThemeController();
}
