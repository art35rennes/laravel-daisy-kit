/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';
import { renderAsync } from 'docx-preview';
import initFilePreview, {
  calculateDocxFitZoom,
  loadDocxPreview,
  loadTextPreview,
  setDocxZoom,
} from '../../../resources/js/modules/file-preview.js';

async function tick(times = 1) {
  for (let i = 0; i < times; i += 1) {
    await new Promise((resolve) => setTimeout(resolve, 0));
  }
}

vi.mock('docx-preview', () => ({
  renderAsync: vi.fn(async (_blob, bodyContainer) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'daisy-docx-preview-wrapper';
    const page = document.createElement('section');
    page.className = 'daisy-docx-preview';
    const rendered = document.createElement('p');
    rendered.textContent = 'Rendered DOCX';
    page.append(rendered);
    wrapper.append(page);
    bodyContainer.append(wrapper);
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
    expect(renderAsync).toHaveBeenCalledWith(expect.any(Blob), element, element, expect.objectContaining({
      renderAltChunks: false,
    }));
  });

  it('calculates a fit width zoom without scaling above one hundred percent', () => {
    expect(calculateDocxFitZoom(600, 800)).toBe(75);
    expect(calculateDocxFitZoom(1000, 800)).toBe(100);
    expect(calculateDocxFitZoom(0, 800)).toBe(100);
  });

  it('applies fit width and manual docx zoom through CSP safe classes', () => {
    document.body.innerHTML = `
      <div data-module="file-preview">
        <div data-file-preview-docx-controls>
          <button data-file-preview-docx-zoom="fit-width" aria-pressed="false">Fit</button>
          <button data-file-preview-docx-zoom="75" aria-pressed="false">75%</button>
          <span data-file-preview-docx-zoom-status></span>
        </div>
        <div data-file-preview-docx data-docx-view="fit-width" data-docx-zoom="100">
          <div class="daisy-docx-preview-wrapper"><section class="daisy-docx-preview">Document</section></div>
        </div>
      </div>
    `;
    const element = document.querySelector('[data-file-preview-docx]');
    const page = element.querySelector('.daisy-docx-preview');

    Object.defineProperty(element, 'clientWidth', { configurable: true, value: 600 });
    page.getBoundingClientRect = () => ({ width: 800, height: 1000 });

    setDocxZoom(element, 'fit-width');

    expect(element.classList.contains('daisy-docx-zoom-75')).toBe(true);
    expect(element.dataset.docxCurrentZoom).toBe('75');
    const fitButton = element.closest('[data-module="file-preview"]')
      .querySelector('[data-file-preview-docx-zoom="fit-width"]');

    expect(fitButton.getAttribute('aria-pressed')).toBe('true');
    expect(element.hasAttribute('style')).toBe(false);

    setDocxZoom(element, '100');

    expect(element.classList.contains('daisy-docx-zoom-100')).toBe(true);
    expect(element.dataset.docxView).toBe('page');
  });

  it('recalculates fit width docx zoom when its viewport is resized', async () => {
    let resizeCallback;
    global.ResizeObserver = vi.fn(function (callback) {
      resizeCallback = callback;
      this.observe = vi.fn();
    });
    document.body.innerHTML = `
      <div data-module="file-preview">
        <div data-file-preview-docx-controls>
          <button data-file-preview-docx-zoom="fit-width" aria-pressed="false">Fit</button>
          <button data-file-preview-docx-zoom="75" aria-pressed="false">75%</button>
        </div>
        <div data-file-preview-docx data-url="/files/document.docx" data-docx-view="fit-width" data-docx-zoom="100"></div>
      </div>
    `;
    const element = document.querySelector('[data-file-preview-docx]');
    let availableWidth = 600;

    Object.defineProperty(element, 'clientWidth', { configurable: true, get: () => availableWidth });
    global.fetch = vi.fn(async () => ({ ok: true, blob: async () => new Blob(['docx']) }));

    initFilePreview(document.querySelector('[data-module="file-preview"]'));
    await tick(3);
    const page = element.querySelector('.daisy-docx-preview');
    page.getBoundingClientRect = () => ({ width: 800, height: 1000 });
    resizeCallback();

    expect(element.dataset.docxCurrentZoom).toBe('75');

    document.querySelector('[data-file-preview-docx-zoom="75"]').click();

    expect(element.dataset.docxView).toBe('page');
    expect(element.dataset.docxCurrentZoom).toBe('75');

    document.querySelector('[data-file-preview-docx-zoom="fit-width"]').click();

    availableWidth = 400;
    resizeCallback();

    expect(element.dataset.docxCurrentZoom).toBe('50');
    delete global.ResizeObserver;
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
