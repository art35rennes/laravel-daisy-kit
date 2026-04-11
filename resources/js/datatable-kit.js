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

  scope.classList.add('space-y-4');

  scope.querySelectorAll('.dt-search input').forEach((input) => {
    input.classList.add('input', 'input-sm', 'w-full');
  });

  scope.querySelectorAll('.dt-length select').forEach((select) => {
    select.classList.add('select', 'select-sm');
  });

  scope.querySelectorAll('.dt-paging').forEach((paging) => {
    paging.classList.add('join', 'join-horizontal');
  });

  scope.querySelectorAll('.dt-paging-button').forEach((button) => {
    button.classList.add('btn', 'btn-sm', 'join-item');
    button.classList.toggle('btn-active', button.classList.contains('current') || button.getAttribute('aria-current') === 'page');
    button.classList.toggle('btn-disabled', button.classList.contains('disabled') || button.getAttribute('aria-disabled') === 'true');
    button.classList.toggle('pointer-events-none', button.classList.contains('disabled') || button.getAttribute('aria-disabled') === 'true');
  });

  scope.querySelectorAll('.dt-paging .ellipsis').forEach((ellipsis) => {
    ellipsis.classList.add('join-item', 'inline-flex', 'items-center', 'px-3', 'text-sm', 'text-base-content/70');
  });

  scope.querySelectorAll('.dt-info, .dt-length label, .dt-search label').forEach((node) => {
    node.classList.add('text-sm', 'text-base-content/70');
  });

  scope.querySelectorAll('ul.dtr-details').forEach((details) => {
    details.classList.add('rounded-box', 'border', 'border-base-content/5', 'bg-base-100');
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
