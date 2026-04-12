let dataTableCtorPromise;

const DEFAULT_LAYOUT = {
  topStart: 'pageLength',
  topEnd: 'search',
  bottomStart: 'info',
  bottomEnd: 'paging',
};

function isPlainObject(value) {
  return Object.prototype.toString.call(value) === '[object Object]';
}

function deepMerge(base, overrides) {
  const output = Array.isArray(base) ? [...base] : { ...base };

  if (!isPlainObject(overrides) && !Array.isArray(overrides)) {
    return output;
  }

  Object.entries(overrides).forEach(([key, value]) => {
    if (Array.isArray(value)) {
      output[key] = [...value];
      return;
    }

    if (isPlainObject(value) && isPlainObject(output[key])) {
      output[key] = deepMerge(output[key], value);
      return;
    }

    output[key] = value;
  });

  return output;
}

function parseOptions(source) {
  const raw = typeof source === 'string'
    ? source
    : source?.dataset?.options;

  if (!raw) {
    return {};
  }

  try {
    const parsed = JSON.parse(raw);
    return isPlainObject(parsed) ? parsed : {};
  } catch (_) {
    return {};
  }
}

async function ensureDataTables() {
  if (!dataTableCtorPromise) {
    dataTableCtorPromise = Promise.all([
      import('jquery'),
      import('datatables.net-dt'),
      import('datatables.net-responsive-dt'),
    ]).then(([jqueryModule, datatableModule]) => {
      const jQuery = jqueryModule.default;
      const DataTable = datatableModule.default;

      if (typeof window !== 'undefined') {
        window.jQuery = window.jQuery || jQuery;
        window.$ = window.$ || jQuery;
        window.DataTable = window.DataTable || DataTable;
      }

      return DataTable;
    });
  }

  return dataTableCtorPromise;
}

function buildDataTableOptions(raw = {}) {
  const defaults = {
    layout: DEFAULT_LAYOUT,
    paging: true,
    pageLength: 10,
    lengthChange: true,
    searching: true,
    ordering: true,
    processing: raw.serverSide === true,
    scrollX: false,
    responsive: false,
    language: {},
  };

  const merged = deepMerge(defaults, raw);

  merged.layout = deepMerge(DEFAULT_LAYOUT, raw.layout || {});
  merged.language = deepMerge({}, raw.language || {});

  if (merged.serverSide === true && merged.processing == null) {
    merged.processing = true;
  }

  return merged;
}

function applyDaisyUiTheme(instance, root) {
  const container = instance?.table?.().container?.();
  const scope = container instanceof HTMLElement
    ? container
    : root.querySelector('.dt-container');

  if (!(scope instanceof HTMLElement)) {
    return;
  }

  scope.classList.add('space-y-4', 'text-sm');

  scope.querySelectorAll('.dt-layout-row:not(.dt-layout-table)').forEach((row) => {
    row.classList.add('flex', 'flex-col', 'gap-3', 'md:flex-row', 'md:items-center', 'md:justify-between');
  });

  scope.querySelectorAll('.dt-layout-row:not(.dt-layout-table) .dt-layout-cell').forEach((cell) => {
    cell.classList.add('min-w-0', 'flex', 'flex-wrap', 'items-center', 'gap-3');

    if (cell.classList.contains('dt-layout-start')) {
      cell.classList.add('justify-start');
    }

    if (cell.classList.contains('dt-layout-end')) {
      cell.classList.add('justify-start', 'md:justify-end');
    }

    if (cell.classList.contains('dt-layout-full')) {
      cell.classList.add('w-full');
    }
  });

  scope.querySelectorAll('.dt-search, .dt-length').forEach((wrapper) => {
    wrapper.classList.add('w-full', 'md:w-auto');
  });

  scope.querySelectorAll('.dt-search input').forEach((input) => {
    input.classList.add('input', 'input-sm', 'w-full', 'md:w-72');
  });

  scope.querySelectorAll('.dt-length select').forEach((select) => {
    select.classList.add('select', 'select-sm', 'w-full', 'md:w-auto');
  });

  scope.querySelectorAll('.dt-paging').forEach((paging) => {
    paging.classList.add('join', 'join-horizontal', 'max-w-full', 'overflow-x-auto');
  });

  scope.querySelectorAll('.dt-paging-button').forEach((button) => {
    const isCurrent = button.classList.contains('current') || button.getAttribute('aria-current') === 'page';
    const isDisabled = button.classList.contains('disabled') || button.getAttribute('aria-disabled') === 'true';

    button.classList.add('btn', 'btn-sm', 'join-item');
    button.classList.toggle('btn-active', isCurrent);
    button.classList.toggle('btn-disabled', isDisabled);
    button.classList.toggle('pointer-events-none', isDisabled);
  });

  scope.querySelectorAll('.dt-paging .ellipsis').forEach((ellipsis) => {
    ellipsis.classList.add('join-item', 'inline-flex', 'items-center', 'px-3', 'text-sm', 'text-base-content/70');
  });

  scope.querySelectorAll('.dt-info, .dt-length label, .dt-search label').forEach((node) => {
    node.classList.add('text-sm', 'text-base-content/70');
  });

  scope.querySelectorAll('.dt-search label, .dt-length label').forEach((label) => {
    label.classList.add('flex', 'w-full', 'items-center', 'gap-2', 'md:w-auto', 'md:flex-nowrap');
  });

  scope.querySelectorAll('ul.dtr-details').forEach((details) => {
    details.classList.add('list-none', 'rounded-box', 'border', 'border-base-content/5', 'bg-base-100');
  });
}

async function initDataTable(root) {
  const container = root?.matches?.('[data-daisy-datatable="1"]')
    ? root
    : root?.querySelector?.('[data-daisy-datatable="1"]');

  if (!(container instanceof HTMLElement) || container.__daisyDatatableInit) {
    return null;
  }

  const table = container.querySelector('table');
  if (!(table instanceof HTMLTableElement)) {
    return null;
  }

  const DataTable = await ensureDataTables();
  const options = buildDataTableOptions(parseOptions(container));
  const instance = new DataTable(table, options);

  container.__daisyDatatableInit = true;
  container.__daisyDatatableInstance = instance;

  applyDaisyUiTheme(instance, container);

  if (typeof instance.on === 'function') {
    instance.on('draw', () => applyDaisyUiTheme(instance, container));
    instance.on('responsive-display', () => applyDaisyUiTheme(instance, container));
  }

  return instance;
}

async function initAllDataTables() {
  if (typeof document === 'undefined') {
    return;
  }

  await Promise.all(
    Array.from(document.querySelectorAll('[data-daisy-datatable="1"]')).map((root) => initDataTable(root))
  );
}

if (typeof window !== 'undefined') {
  window.DaisyDataTable = {
    init: initDataTable,
    initAll: initAllDataTables,
  };
}

if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllDataTables);
  } else {
    initAllDataTables();
  }
}

export {
  DEFAULT_LAYOUT,
  buildDataTableOptions,
  deepMerge,
  ensureDataTables,
  initAllDataTables,
  initDataTable,
  parseOptions,
};

export default initDataTable;
