/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';
import initTokenInput, {
  isValidTokenValue,
  normalizeTokenValue,
  splitTokenEntries,
} from '../../../resources/js/modules/token-input.js';

function createRoot(extraData = '') {
  document.body.innerHTML = `
    <div class="dropdown w-full" data-module="token-input" data-submit-name="recipients[]" data-preset="email" ${extraData}>
      <div class="input w-full" data-role="shell">
        <div data-role="tokens">
          <input type="text" data-role="input" />
        </div>
      </div>
      <div data-role="hidden-inputs"></div>
      <ul data-role="list" class="hidden"></ul>
      <p data-role="message" class="hidden"></p>
    </div>
  `;

  return document.querySelector('[data-module="token-input"]');
}

describe('token-input helpers', () => {
  it('normalizes and validates email values', () => {
    expect(normalizeTokenValue('  Alice@Example.com ', 'email')).toBe('alice@example.com');
    expect(isValidTokenValue('alice@example.com', 'email')).toBe(true);
    expect(isValidTokenValue('alice-example.com', 'email')).toBe(false);
  });

  it('splits pasted values with multiple separators', () => {
    expect(splitTokenEntries('a@test.com, b@test.com; c@test.com\n d@test.com')).toEqual([
      'a@test.com',
      'b@test.com',
      'c@test.com',
      'd@test.com',
    ]);
  });
});

describe('token-input module', () => {
  beforeEach(() => {
    vi.restoreAllMocks();
  });

  it('creates a token on Enter and syncs hidden inputs', () => {
    const root = createRoot();
    initTokenInput(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.value = 'alice@example.com';
    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));

    expect(root.querySelectorAll('[data-token-item]')).toHaveLength(1);
    expect(root.querySelectorAll('input[type="hidden"]')).toHaveLength(1);
    expect(root.querySelector('input[type="hidden"]').value).toBe('alice@example.com');
    expect(input.value).toBe('');
  });

  it('shows an error for invalid email values and keeps the text', () => {
    const root = createRoot('data-invalid-text="Invalid recipient"');
    initTokenInput(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.value = 'invalid-email';
    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));

    expect(root.querySelectorAll('[data-token-item]')).toHaveLength(0);
    expect(root.querySelector('[data-role="message"]').textContent).toBe('Invalid recipient');
    expect(input.value).toBe('invalid-email');
  });

  it('removes the last token on Backspace when the input is empty', () => {
    const root = createRoot();
    initTokenInput(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    input.value = 'alice@example.com';
    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
    input.value = '';
    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Backspace', bubbles: true }));

    expect(root.querySelectorAll('[data-token-item]')).toHaveLength(0);
    expect(root.querySelectorAll('input[type="hidden"]')).toHaveLength(0);
  });

  it('splits pasted recipients and prevents duplicates', () => {
    const root = createRoot('data-duplicate-text="Duplicate"');
    initTokenInput(root, { debounce: 0 });

    const input = root.querySelector('[data-role="input"]');
    const pasteEvent = new Event('paste', { bubbles: true, cancelable: true });
    Object.defineProperty(pasteEvent, 'clipboardData', {
      value: {
        getData: () => 'alice@example.com, bob@example.com; alice@example.com',
      },
    });
    input.dispatchEvent(pasteEvent);

    expect(root.querySelectorAll('[data-token-item]')).toHaveLength(2);
    expect(root.querySelector('[data-role="message"]').textContent).toBe('Duplicate');
  });

  it('enforces maxItems and keeps suggestions selectable', async () => {
    const root = createRoot(`
      data-preset="text"
      data-max-items="1"
      data-max-items-text="Limit reached"
      data-suggestions='[{"value":"laravel","label":"Laravel"},{"value":"livewire","label":"Livewire"}]'
    `);
    initTokenInput(root, { debounce: 0, preset: 'text' });

    const input = root.querySelector('[data-role="input"]');
    input.dispatchEvent(new Event('focus', { bubbles: true }));

    const firstSuggestion = root.querySelector('button[role="option"]');
    expect(firstSuggestion?.textContent).toContain('Laravel');

    firstSuggestion.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
    firstSuggestion.dispatchEvent(new MouseEvent('click', { bubbles: true }));

    input.value = 'another';
    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));

    expect(root.querySelectorAll('[data-token-item]')).toHaveLength(1);
    expect(root.querySelector('[data-role="message"]').textContent).toBe('Limit reached');
  });

  it('loads remote suggestions and commits the active option with Enter', async () => {
    const root = createRoot(`
      data-preset="text"
      data-endpoint="/api/users"
      data-param="search"
      data-min-chars="1"
    `);

    global.fetch = vi.fn().mockResolvedValue({
      json: async () => [{ value: 'john@example.com', label: 'John Doe' }],
    });

    initTokenInput(root, { debounce: 0, preset: 'text' });

    const input = root.querySelector('[data-role="input"]');
    input.value = 'jo';
    input.dispatchEvent(new Event('input', { bubbles: true }));
    await new Promise((resolve) => setTimeout(resolve, 0));

    expect(global.fetch).toHaveBeenCalledTimes(1);
    expect(root.querySelector('button[role="option"]')?.textContent).toContain('John Doe');

    input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));

    expect(root.querySelectorAll('[data-token-item]')).toHaveLength(1);
    expect(root.querySelector('[data-token-item]').textContent).toContain('John Doe');
  });
});
