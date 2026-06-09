function focusInitialTarget(dialog, selector) {
  if (!dialog.open) {
    return;
  }

  const fallbackSelector = '[autofocus], [data-autofocus], button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
  const target = selector ? dialog.querySelector(selector) : null;
  const focusTarget = target || dialog.querySelector(fallbackSelector);

  if (typeof focusTarget?.focus === 'function') {
    focusTarget.focus();
  }
}

export default function init(dialog, options = {}) {
  if (!(dialog instanceof HTMLDialogElement)) {
    return;
  }

  if (options.teleport && dialog.parentElement?.tagName !== 'BODY' && !dialog.dataset.teleported) {
    document.body.appendChild(dialog);
    dialog.dataset.teleported = '1';
  }

  dialog.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => dialog.close());
  });

  const focus = () => focusInitialTarget(dialog, options.initialFocus || null);

  if (dialog.open) {
    window.requestAnimationFrame(focus);
  }

  if (!dialog.dataset.focusListener) {
    dialog.addEventListener('toggle', focus);
    dialog.dataset.focusListener = '1';
  }
}
