/** @vitest-environment jsdom */

import { describe, expect, it } from 'vitest';
import initOrderedList, { serializeOrderedList } from '../../../resources/js/modules/ordered-list.js';

describe('ordered-list module', () => {
  it('serializes list items in DOM order', () => {
    document.body.innerHTML = `
      <ol data-ordered-list="1">
        <li data-ordered-list-item data-id="first"></li>
        <li data-ordered-list-item data-id="second"></li>
      </ol>
    `;

    const root = document.querySelector('[data-ordered-list="1"]');

    expect(serializeOrderedList(root)).toEqual([
      { id: 'first', index: 0, disabled: false },
      { id: 'second', index: 1, disabled: false },
    ]);
  });

  it('syncs a hidden input when persistence is enabled', () => {
    document.body.innerHTML = `
      <ol data-ordered-list="1" data-sortable="true" data-persist="true">
        <li data-ordered-list-item data-id="first"></li>
        <li data-ordered-list-item data-id="second"></li>
        <input type="hidden" data-ordered-list-input>
      </ol>
    `;

    const root = document.querySelector('[data-ordered-list="1"]');
    const api = initOrderedList(root);

    expect(api.serialize()).toEqual([
      { id: 'first', index: 0, disabled: false },
      { id: 'second', index: 1, disabled: false },
    ]);
    expect(root.querySelector('[data-ordered-list-input]').value).toBe('["first","second"]');
  });

  it('creates a Sortable instance when sorting is enabled', () => {
    document.body.innerHTML = `
      <ol data-ordered-list="1" data-sortable="true">
        <li data-ordered-list-item data-id="first">
          <span data-ordered-list-handle></span>
        </li>
        <li data-ordered-list-item data-id="second">
          <span data-ordered-list-handle></span>
        </li>
      </ol>
    `;

    const root = document.querySelector('[data-ordered-list="1"]');
    const api = initOrderedList(root);

    expect(api.sortable).not.toBeNull();
    expect(root.__daisyOrderedList).toBe(api);
  });

  it('normalizes free slot children into sortable rows with handles', () => {
    document.body.innerHTML = `
      <ol data-ordered-list="1" data-sortable="true" data-handle="true">
        <li id="first">First</li>
        <li>Second</li>
      </ol>
    `;

    const root = document.querySelector('[data-ordered-list="1"]');
    const api = initOrderedList(root);
    const rows = root.querySelectorAll('[data-ordered-list-item]');

    expect(api.sortable).not.toBeNull();
    expect(rows).toHaveLength(2);
    expect(rows[0].getAttribute('data-id')).toBe('first');
    expect(rows[1].getAttribute('data-id')).toBe('ordered-item-1');
    expect(root.querySelectorAll('[data-ordered-list-handle]')).toHaveLength(2);
    expect(serializeOrderedList(root)).toEqual([
      { id: 'first', index: 0, disabled: false },
      { id: 'ordered-item-1', index: 1, disabled: false },
    ]);
  });
});
