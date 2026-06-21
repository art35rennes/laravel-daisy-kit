import Sortable from 'sortablejs';

function createOrderedListHandle(label) {
  const handle = document.createElement('span');

  handle.className = 'btn btn-ghost btn-xs btn-square mt-0.5 cursor-grab select-none daisy-drag-handle';
  handle.setAttribute('data-ordered-list-handle', '');
  handle.setAttribute('aria-hidden', 'true');
  handle.innerHTML = '<span aria-hidden="true">⋮⋮</span>';

  if (label) {
    handle.setAttribute('title', label);
  }

  return handle;
}

function normalizeOrderedListItems(root, sortableEnabled) {
  const useHandle = root.dataset.handle !== 'false';

  Array.from(root.children).forEach((item, index) => {
    if (!(item instanceof HTMLElement) || item.matches('[data-ordered-list-input]')) {
      return;
    }

    if (!item.hasAttribute('data-ordered-list-item')) {
      item.setAttribute('data-ordered-list-item', '');
    }

    if (!item.hasAttribute('data-id')) {
      item.setAttribute('data-id', item.id || `ordered-item-${index}`);
    }

    item.classList.add('list-row', 'daisy-ordered-list-item');

    if (sortableEnabled && useHandle && !item.querySelector('[data-ordered-list-handle]')) {
      item.prepend(createOrderedListHandle(item.textContent?.trim() || ''));
    }
  });
}

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

  normalizeOrderedListItems(root, sortableEnabled);

  if (sortableEnabled) {
    sortable = Sortable.create(root, {
      animation: 150,
      handle: root.dataset.handle === 'false' ? undefined : '[data-ordered-list-handle]',
      draggable: '[data-ordered-list-item]:not([data-disabled="true"])',
      ghostClass: 'daisy-sortable-ghost',
      chosenClass: 'daisy-sortable-chosen',
      dragClass: 'daisy-sortable-drag',
      onStart: (event) => {
        root.classList.add('daisy-sortable-sorting');
        const handle = event.item?.querySelector('[data-ordered-list-handle]');
        if (handle instanceof HTMLElement) {
          if (typeof handle.blur === 'function') {
            handle.blur();
          }
          handle.setAttribute('aria-pressed', 'true');
        }
      },
      onEnd: () => {
        root.classList.remove('daisy-sortable-sorting');
        root.querySelectorAll('[data-ordered-list-handle][aria-pressed="true"]').forEach((handle) => {
          handle.removeAttribute('aria-pressed');
          if (handle instanceof HTMLElement && typeof handle.blur === 'function') {
            handle.blur();
          }
        });
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
