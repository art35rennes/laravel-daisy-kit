/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';

import { init } from '../../../resources/js/scrollspy.js';

function rect(top, bottom) {
  return {
    top,
    bottom,
    height: bottom - top,
    left: 0,
    right: 100,
    width: 100,
  };
}

function setRect(element, bounds) {
  element.getBoundingClientRect = vi.fn(() => bounds);
}

describe('scrollspy module', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
    vi.restoreAllMocks();

    global.IntersectionObserver = class {
      observe() {}
      disconnect() {}
    };

    setRect(document.documentElement, rect(0, 600));
  });

  it('keeps the section crossing the activation line active instead of the last fully visible short section', () => {
    document.body.innerHTML = `
      <nav data-scrollspy="1" data-offset="0" data-active-class="active">
        <ul>
          <li><a href="#section-a">A</a></li>
          <li><a href="#section-b">B</a></li>
          <li><a href="#section-c">C</a></li>
        </ul>
      </nav>
      <section id="section-a"></section>
      <section id="section-b"></section>
      <section id="section-c"></section>
    `;

    setRect(document.getElementById('section-a'), rect(-200, 300));
    setRect(document.getElementById('section-b'), rect(320, 420));
    setRect(document.getElementById('section-c'), rect(440, 540));

    const nav = document.querySelector('[data-scrollspy="1"]');

    init(nav);

    expect(nav.querySelector('a[href="#section-a"]').classList.contains('active')).toBe(true);
    expect(nav.querySelector('a[href="#section-b"]').classList.contains('active')).toBe(false);
    expect(nav.querySelector('a[href="#section-c"]').classList.contains('active')).toBe(false);
  });
});
