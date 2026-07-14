function resolveTableRoot(idOrRoot) {
  if (typeof HTMLElement !== 'undefined' && idOrRoot instanceof HTMLElement) {
    return idOrRoot.matches('[data-daisy-table="1"]')
      ? idOrRoot
      : idOrRoot.querySelector('[data-daisy-table="1"]');
  }

  if (typeof document === 'undefined') {
    return null;
  }

  if (typeof idOrRoot === 'string' && idOrRoot !== '') {
    const escaped = typeof CSS !== 'undefined' && typeof CSS.escape === 'function'
      ? CSS.escape(idOrRoot)
      : idOrRoot.replaceAll('"', '\\"');

    return document.getElementById(idOrRoot)
      ?? document.querySelector(`[data-daisy-table-id="${escaped}"]`);
  }

  return null;
}

export { resolveTableRoot };
