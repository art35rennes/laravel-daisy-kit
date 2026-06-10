/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it } from 'vitest';
import initThemeController from '../../../resources/js/modules/theme-controller.js';

function setupController(defaultTheme = null) {
  document.body.innerHTML = `
    <div data-module="theme-controller"${defaultTheme ? ` data-default-theme="${defaultTheme}"` : ''}>
      <input type="radio" class="theme-controller" value="light" />
      <input type="radio" class="theme-controller" value="suez" />
      <input type="radio" class="theme-controller" value="dark" />
    </div>
  `;
}

describe('theme-controller module', () => {
  beforeEach(() => {
    document.documentElement.removeAttribute('data-theme');
    document.body.innerHTML = '';
    localStorage.clear();
  });

  it('prefers a saved theme over document and configured defaults', () => {
    setupController('suez');
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('daisy-theme', 'light');

    initThemeController();

    expect(document.documentElement.getAttribute('data-theme')).toBe('light');
    expect(document.querySelector('input[value="light"]').checked).toBe(true);
  });

  it('uses the document theme when there is no saved theme', () => {
    setupController('suez');
    document.documentElement.setAttribute('data-theme', 'dark');

    initThemeController();

    expect(document.documentElement.getAttribute('data-theme')).toBe('dark');
    expect(document.querySelector('input[value="dark"]').checked).toBe(true);
  });

  it('uses the configured default theme when no saved or document theme exists', () => {
    setupController('suez');

    initThemeController();

    expect(document.documentElement.getAttribute('data-theme')).toBe('suez');
    expect(document.querySelector('input[value="suez"]').checked).toBe(true);
  });

  it('falls back to light without any saved, document, or configured default theme', () => {
    setupController();

    initThemeController();

    expect(document.documentElement.getAttribute('data-theme')).toBe('light');
    expect(document.querySelector('input[value="light"]').checked).toBe(true);
  });

  it('does not rely on dynamic javascript evaluation', async () => {
    const source = await import('../../../resources/js/modules/theme-controller.js?raw');

    expect(source.default).not.toMatch(/\beval\s*\(/);
    expect(source.default).not.toMatch(/\bnew\s+Function\b/);
  });
});
