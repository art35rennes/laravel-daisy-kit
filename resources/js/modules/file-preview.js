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

async function loadTextPreview(element) {
  if (element.dataset.filePreviewLoaded === 'true') {
    return;
  }

  element.dataset.filePreviewLoaded = 'true';
  const url = element.dataset.url;
  const maxBytes = Number.parseInt(element.dataset.maxBytes || '65536', 10);
  const errorLabel = element.dataset.errorLabel || 'Preview unavailable';

  if (!url) {
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
    console.error('Error loading text preview:', error);
    element.textContent = errorLabel;
  }
}

async function loadDocxPreview(element) {
  if (element.dataset.filePreviewLoaded === 'true') {
    return;
  }

  element.dataset.filePreviewLoaded = 'true';
  const url = element.dataset.url;
  const errorLabel = element.dataset.errorLabel || 'Preview unavailable';

  if (!url) {
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
    });
  } catch (error) {
    console.error('Error loading DOCX preview:', error);
    element.replaceChildren();
    const message = document.createElement('div');
    message.className = 'flex min-h-48 items-center justify-center text-sm text-base-content/70';
    message.textContent = errorLabel;
    element.append(message);
  }
}

export default function init(root) {
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
    void loadDocxPreview(element);
  });
}

export { downloadFile, loadDocxPreview, loadTextPreview };
