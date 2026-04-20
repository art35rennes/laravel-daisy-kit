import { GridStack } from 'gridstack';

function parseConfig(root) {
  const script = root.querySelector('[data-editable-grid-config]');

  if (!script) {
    return {};
  }

  try {
    return JSON.parse(script.textContent || '{}');
  } catch (_) {
    return {};
  }
}

function serializeNode(node) {
  const el = node?.el;
  const meta = el?.dataset.meta;
  let parsedMeta = null;

  if (meta) {
    try {
      parsedMeta = JSON.parse(meta);
    } catch (_) {
      parsedMeta = meta;
    }
  }

  return {
    id: el?.getAttribute('gs-id') || el?.dataset.gsId || null,
    type: el?.dataset.type || null,
    x: Number.isFinite(node?.x) ? node.x : 0,
    y: Number.isFinite(node?.y) ? node.y : 0,
    w: Number.isFinite(node?.w) ? node.w : 1,
    h: Number.isFinite(node?.h) ? node.h : 1,
    meta: parsedMeta,
  };
}

function serializeItems(grid) {
  return (grid.engine?.nodes || [])
    .slice()
    .sort((a, b) => {
      if (a.y === b.y) return a.x - b.x;
      return a.y - b.y;
    })
    .map(serializeNode);
}

function serializeGrid(grid) {
  return {
    float: Boolean(grid?.opts?.float),
    columns: Number.isFinite(grid?.opts?.column) ? grid.opts.column : 12,
    items: serializeItems(grid),
  };
}

function dispatch(root, name, detail) {
  root.dispatchEvent(new CustomEvent(name, {
    bubbles: true,
    detail,
  }));
}

export default function initEditableGrid(root) {
  if (!(root instanceof HTMLElement)) {
    return null;
  }

  if (root.__daisyEditableGrid) {
    return root.__daisyEditableGrid;
  }

  const config = parseConfig(root);
  const isEditable = Boolean(config.editable) && !Boolean(config.static);

  root.dataset.editableGridEnabled = isEditable ? '1' : '0';

  const grid = GridStack.init({
    column: Number.isFinite(config.columns) ? config.columns : 12,
    cellHeight: Number.isFinite(config.cellHeight) ? config.cellHeight : 80,
    margin: Number.isFinite(config.gap) ? config.gap : 16,
    minRow: Number.isFinite(config.minRow) ? config.minRow : 0,
    staticGrid: !isEditable,
    disableDrag: !isEditable,
    disableResize: !isEditable,
    acceptWidgets: config.acceptWidgets ?? false,
    columnOpts: config.responsive || undefined,
    layout: config.layout || 'list',
    float: Boolean(config.float),
    animate: true,
  }, root);

  const api = {
    grid,
    serialize: () => serializeGrid(grid),
    serializeItems: () => serializeItems(grid),
    setStatic(nextStatic = true) {
      grid.setStatic(!!nextStatic);
      root.dataset.editableGridEnabled = nextStatic ? '0' : '1';
      dispatch(root, 'gridstack:change', {
        layout: serializeGrid(grid),
        items: serializeItems(grid),
        changed: [],
      });
    },
  };

  const emitChange = (changed = []) => {
    const layout = serializeGrid(grid);
    root.dataset.layout = JSON.stringify(layout);

    dispatch(root, 'gridstack:change', {
      layout,
      items: layout.items,
      changed: Array.isArray(changed) ? changed.map(serializeNode) : [],
    });

    dispatch(root, 'gridstack:layout-changed', {
      layout,
      items: layout.items,
    });

    dispatch(root, 'gridstack:serialized', {
      layout,
      items: layout.items,
    });
  };

  grid.on('change', (_event, items) => {
    emitChange(items);
  });

  grid.on('added', (_event, items) => {
    dispatch(root, 'gridstack:item-added', {
      layout: serializeGrid(grid),
      items: serializeItems(grid),
      added: Array.isArray(items) ? items.map(serializeNode) : [],
    });
  });

  grid.on('removed', (_event, items) => {
    dispatch(root, 'gridstack:item-removed', {
      layout: serializeGrid(grid),
      items: serializeItems(grid),
      removed: Array.isArray(items) ? items.map(serializeNode) : [],
    });
  });

  grid.on('dragstop', (_event, element) => {
    const node = element?.gridstackNode;
    dispatch(root, 'gridstack:item-moved', {
      item: node ? serializeNode(node) : null,
      layout: serializeGrid(grid),
    });
  });

  grid.on('resizestop', (_event, element) => {
    const node = element?.gridstackNode;
    dispatch(root, 'gridstack:item-resized', {
      item: node ? serializeNode(node) : null,
      layout: serializeGrid(grid),
    });
  });

  root.addEventListener('click', (event) => {
    const item = event.target instanceof Element ? event.target.closest('.grid-stack-item') : null;

    if (!item || !root.contains(item)) {
      return;
    }

    dispatch(root, 'gridstack:item-selected', {
      item: {
        id: item.getAttribute('gs-id') || item.dataset.gsId || null,
        type: item.dataset.type || null,
      },
    });
  });

  root.__daisyEditableGrid = api;
  root.dataset.layout = JSON.stringify(api.serialize());

  return api;
}

export { initEditableGrid };
