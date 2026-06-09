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
}
