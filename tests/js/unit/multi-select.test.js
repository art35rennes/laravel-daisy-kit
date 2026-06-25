/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';

import initMultiSelect from '../../../resources/js/modules/multi-select.js';

function createRoot(extraData = '', options = '') {
  document.body.innerHTML = `
    <div class="dropdown w-full" data-module="multi-select" data-submit-name="tags[]" ${extraData}>
      <div class="input w-full" data-role="shell">
        <div data-role="selected">
          <input type="text" data-role="input" />
        </div>
      </div>
      <select data-role="native" multiple hidden>
        ${options}
      </select>
      <div data-role="hidden-inputs"></div>
      <ul data-role="list" class="hidden"></ul>
      <p data-role="message" class="hidden"></p>
    </div>
  `;

  return document.querySelector('[data-module="multi-select"]');
}

describe('multi-select module', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
    vi.restoreAllMocks();
  });

  it('filters local options and syncs hidden inputs', async () => {
    const root = createRoot('data-debounce="0"', `
      <option value="laravel">Laravel</option>
      <option value="livewire">Livewire</option>
      <option value="alpine">Alpine.js</option>
    `);

    initMultiSelect(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.value = 'la';
    input.dispatchEvent(new Event('input', { bubbles: true }));
    await new Promise((resolve) => setTimeout(resolve, 0));

    const option = root.querySelector('button[role="option"]');
    expect(option?.textContent).toContain('Laravel');

    option.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
    option.dispatchEvent(new MouseEvent('click', { bubbles: true }));

    expect(root.querySelectorAll('[data-multi-select-item]')).toHaveLength(1);
    expect(root.querySelector('[data-multi-select-hidden]').value).toBe('laravel');
    expect(root.querySelector('select').selectedOptions[0].value).toBe('laravel');
  });

  it('keeps the list open after selecting while the input stays focused', async () => {
    const root = createRoot('data-debounce="0"', `
      <option value="laravel">Laravel</option>
      <option value="livewire">Livewire</option>
      <option value="alpine">Alpine.js</option>
    `);

    initMultiSelect(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.focus();
    await new Promise((resolve) => setTimeout(resolve, 0));

    root.querySelector('button[role="option"]').dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
    root.querySelector('button[role="option"]').dispatchEvent(new MouseEvent('click', { bubbles: true }));
    await new Promise((resolve) => setTimeout(resolve, 0));

    expect(document.activeElement).toBe(input);
    expect(root.classList.contains('dropdown-open')).toBe(true);
    expect(root.querySelector('[data-role="list"]').classList.contains('hidden')).toBe(false);
    expect(root.querySelector('[data-role="list"]').textContent).not.toContain('Laravel');
    expect(root.querySelector('[data-role="list"]').textContent).toContain('Livewire');
  });

  it('trims labels read from native option text', async () => {
    const root = createRoot('data-debounce="0"', `
      <option value="belgium">
        Belgium
      </option>
    `);

    initMultiSelect(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.focus();
    await new Promise((resolve) => setTimeout(resolve, 0));

    root.querySelector('button[role="option"]').dispatchEvent(new MouseEvent('click', { bubbles: true }));

    expect(root.querySelector('[data-multi-select-item]').dataset.label).toBe('Belgium');
  });

  it('reopens suggestions when clicking a focused shell', async () => {
    const root = createRoot('data-debounce="0"', `
      <option value="laravel">Laravel</option>
      <option value="livewire">Livewire</option>
    `);

    initMultiSelect(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.focus();
    await new Promise((resolve) => setTimeout(resolve, 0));

    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
    expect(root.querySelector('[data-role="list"]').classList.contains('hidden')).toBe(true);

    root.querySelector('[data-role="shell"]').dispatchEvent(new MouseEvent('click', { bubbles: true }));
    await new Promise((resolve) => setTimeout(resolve, 0));

    expect(root.querySelector('[data-role="list"]').classList.contains('hidden')).toBe(false);
  });

  it('loads remote options and keeps selecting multiple values', async () => {
    const root = createRoot('data-endpoint="/api/tags" data-param="search" data-min-chars="1" data-debounce="0"');

    global.fetch = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => [
        { value: 'laravel', label: 'Laravel' },
        { value: 'livewire', label: 'Livewire' },
      ],
    });

    initMultiSelect(root, { debounce: 0, endpoint: '/api/tags', minChars: 1 });

    const input = root.querySelector('[data-role="input"]');
    input.value = 'la';
    input.dispatchEvent(new Event('input', { bubbles: true }));
    await new Promise((resolve) => setTimeout(resolve, 0));

    root.querySelector('button[role="option"]').dispatchEvent(new MouseEvent('click', { bubbles: true }));
    await new Promise((resolve) => setTimeout(resolve, 0));

    expect(global.fetch).toHaveBeenCalled();
    expect(root.querySelectorAll('[data-multi-select-item]')).toHaveLength(1);
    expect(root.querySelector('[data-multi-select-item]').textContent).toContain('Laravel');
    expect(root.querySelector('select option[value="laravel"]')?.selected).toBe(true);
  });

  it('removes the latest selected value with Backspace', () => {
    const root = createRoot('', `
      <option value="laravel" selected>Laravel</option>
    `);
    root.querySelector('[data-role="selected"]').insertAdjacentHTML('afterbegin', `
      <span data-multi-select-item data-value="laravel" data-label="Laravel">Laravel</span>
    `);

    initMultiSelect(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.value = '';
    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Backspace', bubbles: true }));

    expect(root.querySelectorAll('[data-multi-select-item]')).toHaveLength(0);
    expect(root.querySelectorAll('[data-multi-select-hidden]')).toHaveLength(0);
  });

  it('does not open suggestions when removing a value by clicking its remove button', async () => {
    const root = createRoot('', `
      <option value="laravel" selected>Laravel</option>
      <option value="livewire">Livewire</option>
    `);
    root.querySelector('[data-role="selected"]').insertAdjacentHTML('afterbegin', `
      <span data-multi-select-item data-value="laravel" data-label="Laravel">Laravel</span>
    `);

    initMultiSelect(root, { debounce: 0 });

    root.querySelector('[data-multi-select-remove]').dispatchEvent(new MouseEvent('click', { bubbles: true }));
    await new Promise((resolve) => setTimeout(resolve, 0));

    expect(root.querySelectorAll('[data-multi-select-item]')).toHaveLength(0);
    expect(root.querySelector('[data-role="list"]').classList.contains('hidden')).toBe(true);
    expect(root.classList.contains('dropdown-open')).toBe(false);
  });

  it('keeps readonly values display-only', async () => {
    const root = createRoot('data-readonly="true"', `
      <option value="admin" selected>Admin</option>
      <option value="editor">Editor</option>
    `);
    root.querySelector('[data-role="selected"]').insertAdjacentHTML('afterbegin', `
      <span data-multi-select-item data-value="admin" data-label="Admin">Admin</span>
    `);

    initMultiSelect(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.focus();
    await new Promise((resolve) => setTimeout(resolve, 0));
    root.querySelector('[data-role="shell"]').dispatchEvent(new MouseEvent('click', { bubbles: true }));
    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Backspace', bubbles: true }));

    expect(root.querySelectorAll('[data-multi-select-item]')).toHaveLength(1);
    expect(root.querySelector('[data-multi-select-remove]')).toBeNull();
    expect(root.querySelector('[data-role="list"]').classList.contains('hidden')).toBe(true);
    expect(root.classList.contains('dropdown-open')).toBe(false);
  });
});
