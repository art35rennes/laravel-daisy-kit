/** @vitest-environment jsdom */

import { describe, expect, it } from 'vitest';
import { moveTransferItem, reorderTransferItems } from '../../../resources/js/transfer.js';

describe('transfer dnd helpers', () => {
  it('reorders items from a DOM id sequence', () => {
    const items = [
      { id: 'a', label: 'A', disabled: false, checked: false },
      { id: 'b', label: 'B', disabled: false, checked: false },
      { id: 'c', label: 'C', disabled: false, checked: false },
    ];

    expect(reorderTransferItems(items, ['c', 'a', 'b']).map((item) => item.id)).toEqual(['c', 'a', 'b']);
  });

  it('moves an item to a target index', () => {
    const items = [
      { id: 'a', label: 'A', disabled: false, checked: false },
      { id: 'b', label: 'B', disabled: false, checked: false },
      { id: 'c', label: 'C', disabled: false, checked: false },
    ];

    expect(moveTransferItem(items, 'a', 2).map((item) => item.id)).toEqual(['b', 'c', 'a']);
  });
});
