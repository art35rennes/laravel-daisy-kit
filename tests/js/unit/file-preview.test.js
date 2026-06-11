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

    await loadTextPreview(element);

    expect(element.textContent).toBe('Unavailable');
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
});
