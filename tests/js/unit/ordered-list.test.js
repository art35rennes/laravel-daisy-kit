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
});
