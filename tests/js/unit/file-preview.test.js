/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';
import initFilePreview, { loadDocxPreview, loadTextPreview } from '../../../resources/js/modules/file-preview.js';

async function tick(times = 1) {
  for (let i = 0; i < times; i += 1) {
    await new Promise((resolve) => setTimeout(resolve, 0));
  }
}

vi.mock('docx-preview', () => ({
  renderAsync: vi.fn(async (_blob, bodyContainer) => {
    const rendered = document.createElement('p');
    rendered.textContent = 'Rendered DOCX';
    bodyContainer.append(rendered);
  }),
}));

describe('file-preview module', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
    vi.restoreAllMocks();
  });

  it('loads a text preview with a byte limit', async () => {
    const element = document.createElement('pre');
    element.dataset.url = '/files/readme.txt';
    element.dataset.maxBytes = '4';
    element.dataset.errorLabel = 'Unavailable';
    global.fetch = vi.fn(async () => ({
      ok: true,
      text: async () => 'abcdef',
    }));

    await loadTextPreview(element);

    expect(fetch).toHaveBeenCalledWith('/files/readme.txt', expect.objectContaining({
      headers: expect.objectContaining({ Range: 'bytes=0-3' }),
    }));
    expect(element.textContent).toBe('abcd…');
  });

  it('shows text preview errors', async () => {
    const element = document.createElement('pre');
    element.dataset.url = '/files/readme.txt';
    element.dataset.errorLabel = 'Unavailable';
    global.fetch = vi.fn(async () => ({ ok: false }));
    const listener = vi.fn();
    element.addEventListener('daisy:file-preview:error', listener);
    const consoleError = vi.spyOn(console, 'error').mockImplementation(() => {});

    await loadTextPreview(element);

    expect(element.textContent).toBe('Unavailable');
    expect(element.dataset.filePreviewError).toBe('true');
    expect(listener).toHaveBeenCalledWith(expect.objectContaining({
      detail: expect.objectContaining({
        type: 'text',
        url: '/files/readme.txt',
      }),
    }));
    expect(consoleError).not.toHaveBeenCalled();
  });

  it('renders docx previews through docx-preview', async () => {
    const element = document.createElement('div');
    element.dataset.url = '/files/document.docx';
    element.dataset.errorLabel = 'Unavailable';
    global.fetch = vi.fn(async () => ({
      ok: true,
      blob: async () => new Blob(['docx']),
    }));

    await loadDocxPreview(element);

    expect(element.textContent).toContain('Rendered DOCX');
  });

  it('shows docx fallback and exposes errors without noisy console output', async () => {
    document.body.innerHTML = `
      <div data-module="file-preview">
        <div data-file-preview-docx data-url="/files/broken.docx" data-error-label="Cannot preview">Loading</div>
      </div>
    `;
    const element = document.querySelector('[data-file-preview-docx]');
    const listener = vi.fn();
    document.querySelector('[data-module="file-preview"]').addEventListener('daisy:file-preview:error', listener);
    const consoleError = vi.spyOn(console, 'error').mockImplementation(() => {});
    global.fetch = vi.fn(async () => ({ ok: false }));

    await loadDocxPreview(element);

    expect(element.textContent).toContain('Cannot preview');
    expect(element.dataset.filePreviewError).toBe('true');
    expect(listener).toHaveBeenCalledWith(expect.objectContaining({
      detail: expect.objectContaining({
        type: 'docx',
        url: '/files/broken.docx',
      }),
    }));
    expect(consoleError).not.toHaveBeenCalled();
  });

  it('logs preview errors only when explicitly enabled', async () => {
    document.body.innerHTML = `
      <div data-module="file-preview" data-log-preview-errors="true">
        <pre data-file-preview-text data-url="/files/readme.txt" data-error-label="Unavailable">Loading</pre>
      </div>
    `;
    const element = document.querySelector('[data-file-preview-text]');
    const consoleError = vi.spyOn(console, 'error').mockImplementation(() => {});
    global.fetch = vi.fn(async () => ({ ok: false }));

    await loadTextPreview(element);

    expect(consoleError).toHaveBeenCalledWith('Error loading text preview:', expect.any(Error));
  });

  it('initializes text and docx containers from a root element', async () => {
    document.body.innerHTML = `
      <div data-module="file-preview">
        <pre data-file-preview-text data-url="/files/readme.txt" data-max-bytes="10">Loading</pre>
        <div data-file-preview-docx data-url="/files/document.docx">Loading</div>
      </div>
    `;
    global.fetch = vi.fn(async (url) => {
      if (url.endsWith('.docx')) {
        return { ok: true, blob: async () => new Blob(['docx']) };
      }

      return { ok: true, text: async () => 'hello' };
    });

    initFilePreview(document.querySelector('[data-module="file-preview"]'));
    await tick(3);

    expect(document.querySelector('[data-file-preview-text]').textContent).toBe('hello');
    expect(document.querySelector('[data-file-preview-docx]').textContent).toContain('Rendered DOCX');
  });

  it('keeps a skeleton visible until browser media is loaded', () => {
    document.body.innerHTML = `
      <div data-module="file-preview">
        <div data-file-preview-loadable-container>
          <div class="skeleton" data-file-preview-skeleton></div>
          <video class="opacity-0" data-file-preview-loadable></video>
        </div>
      </div>
    `;

    const root = document.querySelector('[data-module="file-preview"]');
    const media = root.querySelector('[data-file-preview-loadable]');

    initFilePreview(root);
    expect(root.querySelector('[data-file-preview-skeleton]')).not.toBeNull();

    media.dispatchEvent(new Event('loadeddata'));

    expect(root.querySelector('[data-file-preview-skeleton]')).toBeNull();
    expect(media.classList.contains('opacity-0')).toBe(false);
  });
});
