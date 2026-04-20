import Sortable from 'sortablejs';

export function serializeOrderedList(root) {
  if (!(root instanceof HTMLElement)) {
    return [];
  }

  return Array.from(root.querySelectorAll('[data-ordered-list-item]')).map((item, index) => ({
    id: item.getAttribute('data-id') || String(index),
    index,
    disabled: item.getAttribute('data-disabled') === 'true',
  }));
}

function syncOrderedList(root) {
  const order = serializeOrderedList(root);
  root.dataset.order = JSON.stringify(order);

  const input = root.id
    ? document.querySelector(`[data-ordered-list-input-for="${root.id}"]`)
    : root.querySelector('[data-ordered-list-input]');
  if (input instanceof HTMLInputElement) {
    input.value = JSON.stringify(order.map((entry) => entry.id));
  }

  root.dispatchEvent(new CustomEvent('ordered-list:change', {
    bubbles: true,
    detail: {
      items: order,
    },
  }));

  return order;
}

export default function initOrderedList(root) {
  if (!(root instanceof HTMLElement)) {
    return null;
  }

  if (root.__daisyOrderedList) {
    return root.__daisyOrderedList;
  }

  const sortableEnabled = root.dataset.sortable === 'true' && root.dataset.disabled !== 'true';
  let sortable = null;

  if (sortableEnabled) {
    sortable = Sortable.create(root, {
      animation: 150,
      handle: '[data-ordered-list-handle]',
      draggable: '[data-ordered-list-item]:not([data-disabled="true"])',
      ghostClass: 'daisy-sortable-ghost',
      chosenClass: 'daisy-sortable-chosen',
      dragClass: 'daisy-sortable-drag',
      onEnd: () => {
        syncOrderedList(root);
      },
    });
  }

  const api = {
    sortable,
    serialize: () => syncOrderedList(root),
  };

  root.__daisyOrderedList = api;
  syncOrderedList(root);

  return api;
}

export { initOrderedList };
