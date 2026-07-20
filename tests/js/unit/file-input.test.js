/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it } from 'vitest';
import { initFileInput } from '../../../resources/js/file-input.js';

function makeFile(name, type = 'application/pdf') {
  return new File(['content'], name, { type });
}

describe('file-input module', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
  });

  it('forces single-file mode from the wrapper dataset', () => {
    document.body.innerHTML = `
      <div data-fileinput="1" data-preview="true" data-multiple="false">
        <input type="file" multiple>
        <div data-previews></div>
      </div>
    `;

    const root = document.querySelector('[data-fileinput="1"]');
    initFileInput(root);

    expect(root.querySelector('input').multiple).toBe(false);
  });

  it('renders only one preview in single-file mode', () => {
    document.body.innerHTML = `
      <div data-fileinput="1" data-preview="true" data-multiple="false">
        <input type="file">
        <div data-previews></div>
      </div>
    `;

    const root = document.querySelector('[data-fileinput="1"]');
    const input = root.querySelector('input');
    Object.defineProperty(input, 'files', {
      configurable: true,
      value: [makeFile('first.pdf'), makeFile('second.pdf')],
    });

    initFileInput(root);
    input.dispatchEvent(new Event('change', { bubbles: true }));

    expect(root.querySelectorAll('[data-previews] > div')).toHaveLength(1);
  });

  it('applies the configured classes to each preview', () => {
    document.body.innerHTML = `
      <div data-fileinput="1" data-preview="true" data-multiple="false" data-preview-item-class="min-h-96 shadow-md">
        <input type="file">
        <div data-previews></div>
      </div>
    `;

    const root = document.querySelector('[data-fileinput="1"]');
    const input = root.querySelector('input');
    Object.defineProperty(input, 'files', {
      configurable: true,
      value: [makeFile('document.pdf')],
    });

    initFileInput(root);
    input.dispatchEvent(new Event('change', { bubbles: true }));

    const preview = root.querySelector('[data-previews] > div');
    expect(preview.classList.contains('min-h-96')).toBe(true);
    expect(preview.classList.contains('shadow-md')).toBe(true);
    expect(preview.classList.contains('aspect-video')).toBe(false);
  });
});
