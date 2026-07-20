async function downloadFile(button) {
  const url = button.dataset.url;
  const filename = button.dataset.filename || 'file';

  if (!url) {
    return;
  }

  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        Accept: '*/*',
      },
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const blob = await response.blob();
    const blobUrl = window.URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = blobUrl;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(blobUrl);
  } catch (error) {
    console.error('Error downloading file:', error);
    window.open(url, '_blank', 'noopener,noreferrer');
  }
}

function shouldLogPreviewErrors(element) {
  return element.closest('[data-module="file-preview"]')?.dataset.logPreviewErrors === 'true';
}

function markPreviewError(element, error, type) {
  element.dataset.filePreviewError = 'true';

  element.dispatchEvent(new CustomEvent('daisy:file-preview:error', {
    bubbles: true,
    detail: {
      error,
      type,
      url: element.dataset.url || null,
    },
  }));

  if (shouldLogPreviewErrors(element)) {
    console.error(`Error loading ${type} preview:`, error);
  }
}

function normalizeDocxZoom(value) {
  const zoom = Number.parseInt(value, 10);

  return Number.isFinite(zoom) ? Math.min(100, Math.max(10, zoom)) : 100;
}

function calculateDocxFitZoom(availableWidth, documentWidth) {
  if (!Number.isFinite(availableWidth)
    || !Number.isFinite(documentWidth)
    || availableWidth <= 0
    || documentWidth <= 0) {
    return 100;
  }

  return normalizeDocxZoom(Math.floor((availableWidth / documentWidth) * 100));
}

function getNumericStyle(element, property) {
  if (!(element instanceof HTMLElement) || typeof getComputedStyle !== 'function') {
    return 0;
  }

  return Number.parseFloat(getComputedStyle(element)[property]) || 0;
}

function measureDocxFitZoom(element) {
  const wrapper = element.querySelector('.daisy-docx-preview-wrapper');
  const pages = [...element.querySelectorAll('.daisy-docx-preview')];

  if (!(wrapper instanceof HTMLElement) || pages.length === 0) {
    return normalizeDocxZoom(element.dataset.docxZoom);
  }

  const viewportWidth = element.clientWidth
    - getNumericStyle(element, 'paddingLeft')
    - getNumericStyle(element, 'paddingRight');
  const widestPage = Math.max(...pages.map((page) => page.getBoundingClientRect().width));
  const documentWidth = widestPage
    + getNumericStyle(wrapper, 'paddingLeft')
    + getNumericStyle(wrapper, 'paddingRight');

  return calculateDocxFitZoom(viewportWidth, documentWidth);
}

function updateDocxZoomControls(element, zoom, view) {
  const root = element.closest('[data-module="file-preview"]');

  root?.querySelectorAll('[data-file-preview-docx-zoom]').forEach((button) => {
    const value = button.dataset.filePreviewDocxZoom;
    const pressed = view === 'fit-width' ? value === 'fit-width' : value === String(zoom);

    button.setAttribute('aria-pressed', pressed ? 'true' : 'false');
  });

  const status = root?.querySelector('[data-file-preview-docx-zoom-status]');

  if (status instanceof HTMLElement) {
    status.textContent = `${zoom} %`;
  }
}

function setDocxZoom(element, value) {
  const view = value === 'fit-width' ? 'fit-width' : 'page';

  [...element.classList]
    .filter((className) => /^daisy-docx-zoom-\d+$/.test(className))
    .forEach((className) => element.classList.remove(className));

  const zoom = view === 'fit-width' ? measureDocxFitZoom(element) : normalizeDocxZoom(value);

  element.classList.add(`daisy-docx-zoom-${zoom}`);
  element.dataset.docxView = view;
  element.dataset.docxZoom = view === 'page' ? String(zoom) : element.dataset.docxZoom || '100';
  element.dataset.docxCurrentZoom = String(zoom);
  updateDocxZoomControls(element, zoom, view);

  return zoom;
}

function initializeDocxZoom(element) {
  if (element.dataset.filePreviewDocxZoomInitialized === 'true') {
    return;
  }

  element.dataset.filePreviewDocxZoomInitialized = 'true';

  const resize = () => {
    if (element.dataset.docxView === 'fit-width') {
      setDocxZoom(element, 'fit-width');
    }
  };

  if (typeof ResizeObserver === 'function') {
    const observer = new ResizeObserver(resize);

    observer.observe(element);
    element.__daisyDocxResizeObserver = observer;
  } else {
    window.addEventListener('resize', resize);
    element.__daisyDocxResizeListener = resize;
  }
}

async function loadTextPreview(element) {
  if (element.dataset.filePreviewLoaded === 'true') {
    return;
  }

  element.dataset.filePreviewLoaded = 'true';
  element.dataset.filePreviewError = 'false';
  const url = element.dataset.url;
  const maxBytes = Number.parseInt(element.dataset.maxBytes || '65536', 10);
  const errorLabel = element.dataset.errorLabel || 'Preview unavailable';

  if (!url) {
    markPreviewError(element, new Error('Missing preview URL'), 'text');
    element.textContent = errorLabel;
    return;
  }

  try {
    const response = await fetch(url, {
      headers: {
        Accept: 'text/plain, text/*, application/json, application/xml, */*',
        Range: `bytes=0-${Math.max(maxBytes - 1, 0)}`,
      },
    });

    if (!response.ok) {
      throw new Error('Unable to load text preview');
    }

    const text = await response.text();
    element.textContent = text.length > maxBytes ? `${text.slice(0, maxBytes)}…` : text;
  } catch (error) {
    markPreviewError(element, error, 'text');
    element.textContent = errorLabel;
  }
}

async function loadDocxPreview(element) {
  if (element.dataset.filePreviewLoaded === 'true') {
    return;
  }

  element.dataset.filePreviewLoaded = 'true';
  element.dataset.filePreviewError = 'false';
  const url = element.dataset.url;
  const errorLabel = element.dataset.errorLabel || 'Preview unavailable';

  if (!url) {
    markPreviewError(element, new Error('Missing preview URL'), 'docx');
    element.textContent = errorLabel;
    return;
  }

  try {
    const [{ renderAsync }, response] = await Promise.all([
      import('docx-preview'),
      fetch(url, {
        headers: {
          Accept: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/octet-stream, */*',
        },
      }),
    ]);

    if (!response.ok) {
      throw new Error('Unable to load DOCX preview');
    }

    const blob = await response.blob();
    element.replaceChildren();

    await renderAsync(blob, element, element, {
      className: 'daisy-docx-preview',
      inWrapper: true,
      ignoreWidth: false,
      ignoreHeight: false,
      breakPages: true,
      renderHeaders: true,
      renderFooters: true,
      renderFootnotes: true,
      renderEndnotes: true,
      renderAltChunks: false,
    });

    setDocxZoom(element, element.dataset.docxView === 'fit-width' ? 'fit-width' : element.dataset.docxZoom);
  } catch (error) {
    markPreviewError(element, error, 'docx');
    element.replaceChildren();
    const message = document.createElement('div');
    message.className = 'flex min-h-48 items-center justify-center text-sm text-base-content/70';
    message.textContent = errorLabel;
    element.append(message);
  }
}

function initializeLoadablePreview(element) {
  const container = element.closest('[data-file-preview-loadable-container]');
  const skeleton = container?.querySelector('[data-file-preview-skeleton]');

  if (!container || element.dataset.filePreviewLoadableInitialized === 'true') {
    return;
  }

  element.dataset.filePreviewLoadableInitialized = 'true';

  const reveal = () => {
    skeleton?.remove();
    element.classList.remove('opacity-0');
  };

  element.addEventListener('load', reveal, { once: true });
  element.addEventListener('loadeddata', reveal, { once: true });

  if (element instanceof HTMLImageElement && element.complete) {
    reveal();
  }
}

export default function init(root) {
  root.querySelectorAll('[data-file-preview-loadable]').forEach(initializeLoadablePreview);

  root.querySelectorAll('[data-file-preview-open-modal]').forEach((button) => {
    button.addEventListener('click', () => {
      const dialog = document.getElementById(button.dataset.filePreviewOpenModal);

      if (typeof dialog?.showModal === 'function') {
        dialog.showModal();
      }
    });
  });

  root.querySelectorAll('[data-file-download]').forEach((button) => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();

      void downloadFile(button);
    });
  });

  root.querySelectorAll('[data-file-preview-text]').forEach((element) => {
    void loadTextPreview(element);
  });

  root.querySelectorAll('[data-file-preview-docx]').forEach((element) => {
    initializeDocxZoom(element);
    void loadDocxPreview(element);
  });

  root.querySelectorAll('[data-file-preview-docx-zoom]').forEach((button) => {
    button.addEventListener('click', () => {
      const element = root.querySelector('[data-file-preview-docx]');

      if (element instanceof HTMLElement) {
        setDocxZoom(element, button.dataset.filePreviewDocxZoom);
      }
    });
  });
}

export {
  calculateDocxFitZoom,
  downloadFile,
  loadDocxPreview,
  loadTextPreview,
  markPreviewError,
  setDocxZoom,
};
