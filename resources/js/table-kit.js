import {
  createTable,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
} from '@tanstack/table-core';

const DEFAULT_PAGE_SIZE_OPTIONS = [10, 25, 50];
const DEFAULT_MODE = 'client';
const DEFAULT_METHOD = 'GET';
const DEFAULT_SERVER_ADAPTER = 'default';
const DEFAULT_PERSIST_STATE = false;
const DEFAULT_GLOBAL_FILTER_KEY = 'global';
const ALLOWED_FILTER_TYPES = ['text', 'select', 'boolean'];

function isPlainObject(value) {
  return Object.prototype.toString.call(value) === '[object Object]';
}

function escapeHtml(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function cloneState(value) {
  return JSON.parse(JSON.stringify(value));
}

function parseConfig(source) {
  const raw = typeof source === 'string'
    ? source
    : source?.dataset?.tableConfig;

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

function normalizeFilterDefinition(filter = {}, fallback = {}) {
  const type = ALLOWED_FILTER_TYPES.includes(filter?.type) ? filter.type : null;

  if (!type) {
    return null;
  }

  return {
    id: typeof fallback.id === 'string' && fallback.id !== '' ? fallback.id : String(filter.id ?? ''),
    label: typeof fallback.label === 'string' && fallback.label !== '' ? fallback.label : String(filter.label ?? ''),
    type,
    filterKey: typeof fallback.filterKey === 'string' && fallback.filterKey !== '' ? fallback.filterKey : String(filter.filterKey ?? fallback.id ?? ''),
    options: Array.isArray(filter.options)
      ? filter.options
        .filter((option) => isPlainObject(option) && option.value != null)
        .map((option) => ({
          value: String(option.value),
          label: typeof option.label === 'string' && option.label !== '' ? option.label : String(option.value),
        }))
      : [],
  };
}

function normalizeColumns(columns = []) {
  if (!Array.isArray(columns)) {
    return [];
  }

  return columns
    .filter((column) => isPlainObject(column))
    .map((column) => {
      const key = typeof column.key === 'string' ? column.key.trim() : '';
      const sortKey = typeof column.sortKey === 'string' && column.sortKey !== '' ? column.sortKey : key;
      const filterKey = typeof column.filterKey === 'string' && column.filterKey !== '' ? column.filterKey : key;

      return {
        key,
        label: typeof column.label === 'string' && column.label !== '' ? column.label : key,
        sortable: column.sortable === true,
        filterable: column.filterable === true,
        visible: column.visible !== false,
        sortKey,
        filterKey,
        width: typeof column.width === 'string' ? column.width : null,
        cellClass: typeof column.cellClass === 'string' ? column.cellClass : '',
        headerClass: typeof column.headerClass === 'string' ? column.headerClass : '',
        html: column.html === true,
        // `key` identifies the column in the component, while backend adapters can
        // target a different query name through `sortKey` / `filterKey`.
        filter: normalizeFilterDefinition(column.filter, {
          id: key,
          label: typeof column.label === 'string' && column.label !== '' ? column.label : key,
          filterKey,
        }),
      };
    })
    .filter((column) => column.key !== '');
}

function normalizeStandaloneFilters(filters = []) {
  if (!Array.isArray(filters)) {
    return [];
  }

  return filters
    .filter((filter) => isPlainObject(filter))
    .map((filter) => normalizeFilterDefinition(filter, {
      id: typeof filter.id === 'string' ? filter.id : String(filter.key ?? ''),
      label: typeof filter.label === 'string' ? filter.label : String(filter.key ?? filter.id ?? ''),
      filterKey: typeof filter.filterKey === 'string' ? filter.filterKey : String(filter.key ?? filter.id ?? ''),
    }))
    .filter((filter) => filter && filter.id !== '');
}

function normalizeFilters(rawFilters = [], columns = []) {
  const merged = [
    ...columns.filter((column) => column.filterable === true && column.filter).map((column) => column.filter),
    ...normalizeStandaloneFilters(rawFilters),
  ];

  return merged.filter((filter, index, all) => all.findIndex((item) => item.id === filter.id) === index);
}

function normalizePageSizeOptions(options = []) {
  const values = Array.isArray(options) ? options : DEFAULT_PAGE_SIZE_OPTIONS;

  return values
    .map((value) => Number.parseInt(value, 10))
    .filter((value, index, all) => Number.isInteger(value) && value > 0 && all.indexOf(value) === index);
}

function normalizeSorting(sorting = [], columns = []) {
  const keys = new Set(columns.map((column) => column.key));

  if (!Array.isArray(sorting)) {
    return [];
  }

  return sorting
    .filter((entry) => isPlainObject(entry) && typeof entry.id === 'string' && keys.has(entry.id))
    .map((entry) => ({
      id: entry.id,
      desc: entry.desc === true,
    }));
}

function normalizeColumnFilters(filters = [], filterDefinitions = []) {
  const definitions = new Map(
    (Array.isArray(filterDefinitions) ? filterDefinitions : [])
      .map((entry) => {
        if (!isPlainObject(entry)) {
          return null;
        }

        if (typeof entry.id === 'string' && ALLOWED_FILTER_TYPES.includes(entry.type)) {
          return [entry.id, entry];
        }

        if (isPlainObject(entry.filter) && typeof entry.key === 'string') {
          const normalized = normalizeFilterDefinition(entry.filter, {
            id: entry.key,
            label: typeof entry.label === 'string' && entry.label !== '' ? entry.label : entry.key,
            filterKey: typeof entry.filterKey === 'string' && entry.filterKey !== '' ? entry.filterKey : entry.key,
          });

          return normalized ? [normalized.id, normalized] : null;
        }

        return null;
      })
      .filter(Boolean)
  );

  if (!Array.isArray(filters)) {
    return [];
  }

  return filters
    .filter((entry) => isPlainObject(entry) && typeof entry.id === 'string' && definitions.has(entry.id))
    .map((entry) => {
      const definition = definitions.get(entry.id);
      const value = definition.type === 'boolean'
        ? entry.value === true || entry.value === 'true' || entry.value === 1 || entry.value === '1'
        : String(entry.value ?? '');

      return {
        id: entry.id,
        type: ALLOWED_FILTER_TYPES.includes(entry.type) ? entry.type : definition.type,
        value,
      };
    });
}

function normalizeColumnVisibility(visibility = {}, columns = []) {
  const fallback = Object.fromEntries(
    columns.map((column) => [column.key, column.visible !== false])
  );

  if (!isPlainObject(visibility)) {
    return fallback;
  }

  return Object.fromEntries(
    columns.map((column) => [column.key, visibility[column.key] !== false && fallback[column.key] !== false])
  );
}

function normalizeInitialState(initialState = {}, columns = [], filterDefinitions = [], pageSizeOptions = DEFAULT_PAGE_SIZE_OPTIONS) {
  const safePageSizes = normalizePageSizeOptions(pageSizeOptions);
  const defaultPageSize = safePageSizes[0] ?? DEFAULT_PAGE_SIZE_OPTIONS[0];
  const pagination = isPlainObject(initialState.pagination) ? initialState.pagination : {};

  return {
    sorting: normalizeSorting(initialState.sorting, columns),
    pagination: {
      pageIndex: Math.max(0, Number.parseInt(pagination.pageIndex, 10) || 0),
      pageSize: safePageSizes.includes(Number.parseInt(pagination.pageSize, 10))
        ? Number.parseInt(pagination.pageSize, 10)
        : defaultPageSize,
    },
    globalFilter: typeof initialState.globalFilter === 'string' ? initialState.globalFilter : '',
    // Client and server flows intentionally share one normalized filter state so the
    // UI can stay identical while the transport layer changes underneath.
    columnFilters: normalizeColumnFilters(initialState.columnFilters, filterDefinitions),
    columnVisibility: normalizeColumnVisibility(initialState.columnVisibility, columns),
  };
}

function normalizeEndpoint(endpoint) {
  if (typeof endpoint === 'string' && endpoint !== '') {
    return { url: endpoint };
  }

  if (isPlainObject(endpoint) && typeof endpoint.url === 'string' && endpoint.url !== '') {
    return {
      url: endpoint.url,
      headers: isPlainObject(endpoint.headers) ? endpoint.headers : {},
      credentials: typeof endpoint.credentials === 'string' ? endpoint.credentials : undefined,
    };
  }

  return null;
}

function normalizeConfig(raw = {}) {
  const columns = normalizeColumns(raw.columns);
  const filters = normalizeFilters(raw.filters, columns);
  const pageSizeOptions = normalizePageSizeOptions(raw.pageSizeOptions);
  const mode = raw.mode === 'server' ? 'server' : DEFAULT_MODE;
  const endpoint = normalizeEndpoint(raw.endpoint);
  const serverAdapter = raw.serverAdapter === 'spatie-query-builder' ? 'spatie-query-builder' : DEFAULT_SERVER_ADAPTER;
  const persistState = raw.persistState === 'url' || raw.persistState === 'local' ? raw.persistState : DEFAULT_PERSIST_STATE;
  const config = {
    mode,
    method: typeof raw.method === 'string' && raw.method !== '' ? raw.method.toUpperCase() : DEFAULT_METHOD,
    serverAdapter,
    persistState,
    stateKey: typeof raw.stateKey === 'string' && raw.stateKey !== '' ? raw.stateKey : null,
    globalFilterKey: typeof raw.globalFilterKey === 'string' && raw.globalFilterKey !== '' ? raw.globalFilterKey : DEFAULT_GLOBAL_FILTER_KEY,
    columns,
    filters,
    rows: Array.isArray(raw.rows) ? raw.rows : [],
    endpoint,
    search: raw.search !== false,
    columnVisibility: raw.columnVisibility === true,
    pageSizeOptions: pageSizeOptions.length > 0 ? pageSizeOptions : DEFAULT_PAGE_SIZE_OPTIONS,
    emptyLabel: typeof raw.emptyLabel === 'string' && raw.emptyLabel !== '' ? raw.emptyLabel : 'No results',
    loadingLabel: typeof raw.loadingLabel === 'string' && raw.loadingLabel !== '' ? raw.loadingLabel : 'Loading...',
    errorLabel: typeof raw.errorLabel === 'string' && raw.errorLabel !== '' ? raw.errorLabel : 'Unable to load data.',
    labels: isPlainObject(raw.labels) ? raw.labels : {},
  };

  config.initialState = normalizeInitialState(raw.initialState, columns, filters, config.pageSizeOptions);

  if (mode === 'server' && !endpoint) {
    throw new Error('The table component requires an endpoint when mode is set to server.');
  }

  if (mode !== 'server' && serverAdapter !== DEFAULT_SERVER_ADAPTER) {
    throw new Error('The table component only allows a serverAdapter when mode is set to server.');
  }

  return config;
}

function getColumnByKey(columns, key) {
  return columns.find((column) => column.key === key) ?? null;
}

function getFilterDefinition(filters, id) {
  return filters.find((filter) => filter.id === id) ?? null;
}

function buildRequestPayload(config, state) {
  return {
    pageIndex: state.pagination.pageIndex,
    pageSize: state.pagination.pageSize,
    sorting: normalizeSorting(state.sorting, config.columns),
    globalFilter: typeof state.globalFilter === 'string' ? state.globalFilter : '',
    columnFilters: normalizeColumnFilters(state.columnFilters, config.filters),
    columnVisibility: normalizeColumnVisibility(state.columnVisibility, config.columns),
  };
}

function serializeRequestPayload(payload) {
  const params = new URLSearchParams();

  params.set('pageIndex', String(payload.pageIndex ?? 0));
  params.set('pageSize', String(payload.pageSize ?? DEFAULT_PAGE_SIZE_OPTIONS[0]));

  if (payload.globalFilter) {
    params.set('globalFilter', payload.globalFilter);
  }

  params.set('sorting', JSON.stringify(payload.sorting ?? []));
  params.set('columnFilters', JSON.stringify(payload.columnFilters ?? []));
  params.set('columnVisibility', JSON.stringify(payload.columnVisibility ?? {}));

  return params;
}

// Spatie Query Builder expects adapter-native query keys. We preserve those names
// in URL persistence so a copied URL can be consumed directly by the host backend.
function buildSpatieRequestParams(config, state) {
  const params = new URLSearchParams();
  const sorting = normalizeSorting(state.sorting, config.columns)
    .map((entry) => {
      const column = getColumnByKey(config.columns, entry.id);
      const sortKey = column?.sortKey || entry.id;

      return entry.desc ? `-${sortKey}` : sortKey;
    });
  const columnFilters = normalizeColumnFilters(state.columnFilters, config.filters);

  if (sorting.length > 0) {
    params.set('sort', sorting.join(','));
  }

  if (typeof state.globalFilter === 'string' && state.globalFilter !== '') {
    params.set(`filter[${config.globalFilterKey}]`, state.globalFilter);
  }

  columnFilters.forEach((filter) => {
    const definition = getFilterDefinition(config.filters, filter.id);
    const filterKey = definition?.filterKey || filter.id;

    if (filter.type === 'boolean') {
      params.set(`filter[${filterKey}]`, filter.value ? 'true' : 'false');
      return;
    }

    if (filter.value !== '') {
      params.set(`filter[${filterKey}]`, String(filter.value));
    }
  });

  params.set('page[number]', String((state.pagination.pageIndex ?? 0) + 1));
  params.set('page[size]', String(state.pagination.pageSize ?? DEFAULT_PAGE_SIZE_OPTIONS[0]));

  return params;
}

function normalizeServerResponse(response, state) {
  const rows = Array.isArray(response?.rows) ? response.rows : [];
  const pageSize = Math.max(1, Number.parseInt(response?.state?.pageSize ?? state.pagination.pageSize, 10) || state.pagination.pageSize);
  const pageIndex = Math.max(0, Number.parseInt(response?.state?.pageIndex ?? state.pagination.pageIndex, 10) || state.pagination.pageIndex);
  const rowCount = Math.max(0, Number.parseInt(response?.rowCount, 10) || rows.length);
  const pageCount = Math.max(1, Number.parseInt(response?.pageCount, 10) || Math.max(1, Math.ceil(rowCount / pageSize)));

  return {
    rows,
    rowCount,
    pageCount,
    state: {
      pageIndex,
      pageSize,
    },
    meta: isPlainObject(response?.meta) ? response.meta : {},
  };
}

// Spatie endpoints typically return Laravel paginator JSON. We normalize the
// paginator metadata back into the table state expected by the runtime.
function normalizeSpatieResponse(response) {
  const meta = isPlainObject(response?.meta) ? response.meta : {};
  const pageSize = Math.max(1, Number.parseInt(meta.per_page, 10) || DEFAULT_PAGE_SIZE_OPTIONS[0]);
  const pageIndex = Math.max(0, (Number.parseInt(meta.current_page, 10) || 1) - 1);
  const rowCount = Math.max(0, Number.parseInt(meta.total, 10) || 0);
  const pageCount = Math.max(1, Number.parseInt(meta.last_page, 10) || Math.ceil(Math.max(1, rowCount) / pageSize));

  return {
    rows: Array.isArray(response?.data) ? response.data : [],
    rowCount,
    pageCount,
    state: {
      pageIndex,
      pageSize,
    },
    meta,
  };
}

function buildServerRequest(config, state) {
  const endpoint = new URL(config.endpoint.url, typeof window !== 'undefined' ? window.location.href : 'http://localhost');
  const headers = new Headers({
    Accept: 'application/json',
    ...(config.endpoint.headers || {}),
  });
  const requestInit = {
    method: config.method,
    headers,
    credentials: config.endpoint.credentials,
  };

  // Server mode can switch transport adapters while keeping the same table state
  // and UI controls. Only the serialized request/response contract changes.
  if (config.serverAdapter === 'spatie-query-builder') {
    endpoint.search = buildSpatieRequestParams(config, state).toString();

    return {
      url: endpoint.toString(),
      requestInit,
      responseNormalizer: normalizeSpatieResponse,
    };
  }

  const payload = buildRequestPayload(config, state);

  if (config.method === 'GET') {
    endpoint.search = serializeRequestPayload(payload).toString();
  } else {
    const csrfToken = typeof document !== 'undefined'
      ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      : null;

    headers.set('Content-Type', 'application/json');

    if (csrfToken && !headers.has('X-CSRF-TOKEN')) {
      headers.set('X-CSRF-TOKEN', csrfToken);
    }

    requestInit.body = JSON.stringify(payload);
  }

  return {
    url: endpoint.toString(),
    requestInit,
    responseNormalizer: (response) => normalizeServerResponse(response, state),
  };
}

function getFilterableColumns(columns) {
  const filterable = columns.filter((column) => column.filterable === true);

  return filterable.length > 0 ? filterable : columns;
}

function applyFilterValue(value, filter) {
  if (filter.type === 'boolean') {
    const normalized = value === true || value === 'true' || value === 1 || value === '1';

    return normalized === filter.value;
  }

  return String(value ?? '').toLowerCase().includes(String(filter.value ?? '').toLowerCase());
}

function applyClientFilters(rows, columns, state) {
  let filteredRows = Array.isArray(rows) ? [...rows] : [];
  const filterableColumns = getFilterableColumns(columns);
  const globalNeedle = String(state.globalFilter ?? '').trim().toLowerCase();

  if (globalNeedle !== '') {
    filteredRows = filteredRows.filter((row) => filterableColumns.some((column) => {
      const value = row?.[column.key];

      return String(value ?? '').toLowerCase().includes(globalNeedle);
    }));
  }

  if (Array.isArray(state.columnFilters) && state.columnFilters.length > 0) {
    filteredRows = filteredRows.filter((row) => state.columnFilters.every((filter) => {
      const value = row?.[filter.id];

      return applyFilterValue(value, filter);
    }));
  }

  return filteredRows;
}

function createColumnDefs(columns) {
  return columns.map((column) => ({
    id: column.key,
    accessorFn: (row) => row?.[column.key],
    enableSorting: column.sortable === true,
  }));
}

function createTableModel(config, state, rows, rowCount, pageCount) {
  return createTable({
    data: rows,
    columns: createColumnDefs(config.columns),
    state: {
      sorting: state.sorting,
      pagination: state.pagination,
      columnVisibility: state.columnVisibility,
    },
    manualPagination: config.mode === 'server',
    manualSorting: config.mode === 'server',
    rowCount,
    pageCount,
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
  });
}

function getVisibleColumns(config, state) {
  return config.columns.filter((column) => state.columnVisibility[column.key] !== false);
}

function getDisplayValue(row, column) {
  const value = row?.[column.key];

  if (value == null) {
    return '';
  }

  return String(value);
}

function getSortDirection(state, columnKey) {
  const entry = state.sorting.find((item) => item.id === columnKey);

  if (!entry) {
    return null;
  }

  return entry.desc === true ? 'desc' : 'asc';
}

function toggleSorting(state, columnKey) {
  const current = getSortDirection(state, columnKey);

  if (current === null) {
    return [{ id: columnKey, desc: false }];
  }

  if (current === 'asc') {
    return [{ id: columnKey, desc: true }];
  }

  return [];
}

function renderHeader(context) {
  const headRow = context.root.querySelector('[data-table-head-row]');

  if (!(headRow instanceof HTMLElement)) {
    return;
  }

  headRow.innerHTML = '';

  getVisibleColumns(context.config, context.state).forEach((column) => {
    const th = document.createElement('th');

    if (column.width) {
      th.style.width = column.width;
    }

    if (column.headerClass) {
      th.className = column.headerClass;
    }

    if (column.sortable) {
      const button = document.createElement('button');
      const direction = getSortDirection(context.state, column.key);

      button.type = 'button';
      button.className = 'daisy-table-head-button';
      button.dataset.tableSort = column.key;
      button.setAttribute('aria-sort', direction === 'asc' ? 'ascending' : direction === 'desc' ? 'descending' : 'none');
      button.innerHTML = `${escapeHtml(column.label)} <span class="daisy-table-sort-indicator" aria-hidden="true">${direction === 'asc' ? '&uarr;' : direction === 'desc' ? '&darr;' : '&harr;'}</span>`;
      th.append(button);
    } else {
      th.textContent = column.label;
    }

    headRow.append(th);
  });
}

function renderBody(context, rows) {
  const tbody = context.root.querySelector('[data-table-body]');

  if (!(tbody instanceof HTMLElement)) {
    return;
  }

  const visibleColumns = getVisibleColumns(context.config, context.state);
  const colspan = Math.max(1, visibleColumns.length);

  if (context.loading) {
    tbody.innerHTML = `<tr class="daisy-table-loading-row"><td colspan="${colspan}">${escapeHtml(context.config.loadingLabel)}</td></tr>`;
    return;
  }

  if (context.error) {
    tbody.innerHTML = `<tr class="daisy-table-error-row"><td colspan="${colspan}">${escapeHtml(context.error)}</td></tr>`;
    return;
  }

  if (!Array.isArray(rows) || rows.length === 0) {
    tbody.innerHTML = `<tr class="daisy-table-empty-row"><td colspan="${colspan}">${escapeHtml(context.config.emptyLabel)}</td></tr>`;
    return;
  }

  tbody.innerHTML = rows.map((row) => {
    const cells = visibleColumns.map((column) => {
      const className = column.cellClass ? ` class="${escapeHtml(column.cellClass)}"` : '';
      const value = getDisplayValue(row.original ?? row, column);
      const content = column.html ? value : escapeHtml(value);

      return `<td${className}>${content}</td>`;
    }).join('');

    return `<tr>${cells}</tr>`;
  }).join('');
}

function renderFooter(context, currentRowsLength) {
  const info = context.root.querySelector('[data-table-info]');
  const indicator = context.root.querySelector('[data-table-page-indicator]');
  const previousButton = context.root.querySelector('[data-table-prev]');
  const nextButton = context.root.querySelector('[data-table-next]');
  const pageIndex = context.state.pagination.pageIndex;
  const pageCount = Math.max(1, context.pageCount);
  const rowCount = Math.max(0, context.rowCount);
  const from = rowCount === 0 ? 0 : pageIndex * context.state.pagination.pageSize + 1;
  const to = rowCount === 0 ? 0 : Math.min(rowCount, from + Math.max(0, currentRowsLength - 1));

  if (info instanceof HTMLElement) {
    const template = context.config.labels.showingResults || 'Showing :from to :to of :total results';

    info.textContent = template
      .replace(':from', String(from))
      .replace(':to', String(to))
      .replace(':total', String(rowCount));
  }

  if (indicator instanceof HTMLElement) {
    const template = context.config.labels.page || 'Page :page of :pages';

    indicator.textContent = template
      .replace(':page', String(pageIndex + 1))
      .replace(':pages', String(pageCount));
  }

  if (previousButton instanceof HTMLButtonElement) {
    previousButton.disabled = pageIndex <= 0 || context.loading;
  }

  if (nextButton instanceof HTMLButtonElement) {
    nextButton.disabled = pageIndex >= pageCount - 1 || context.loading;
  }
}

function renderColumnVisibility(context) {
  const menu = context.root.querySelector('[data-table-column-menu]');

  if (!(menu instanceof HTMLElement)) {
    return;
  }

  menu.innerHTML = '';

  context.config.columns.forEach((column) => {
    const item = document.createElement('label');

    item.className = 'label cursor-pointer gap-3 px-3 py-2';
    item.innerHTML = `
      <span class="label-text">${escapeHtml(column.label)}</span>
      <input
        type="checkbox"
        class="checkbox checkbox-sm"
        data-table-column-toggle="${escapeHtml(column.key)}"
        ${context.state.columnVisibility[column.key] !== false ? 'checked' : ''}
      >
    `;

    menu.append(item);
  });
}

function syncControls(context) {
  const searchInput = context.root.querySelector('[data-table-search]');
  const sizeSelect = context.root.querySelector('[data-table-page-size]');

  if (searchInput instanceof HTMLInputElement) {
    searchInput.value = context.state.globalFilter;
  }

  if (sizeSelect instanceof HTMLSelectElement) {
    sizeSelect.value = String(context.state.pagination.pageSize);
  }

  context.root.querySelectorAll('[data-table-filter]').forEach((input) => {
    if (!(input instanceof HTMLInputElement || input instanceof HTMLSelectElement)) {
      return;
    }

    const currentFilter = context.state.columnFilters.find((filter) => filter.id === input.dataset.tableFilter);
    const type = input.dataset.tableFilterType || 'text';

    if (!currentFilter) {
      if (input instanceof HTMLInputElement && type === 'boolean') {
        input.checked = false;
      } else {
        input.value = '';
      }

      return;
    }

    if (input instanceof HTMLInputElement && type === 'boolean') {
      input.checked = currentFilter.value === true;
      return;
    }

    input.value = String(currentFilter.value ?? '');
  });
}

function getPersistedStateKey(context) {
  if (context.config.stateKey) {
    return `daisy-table:${context.config.stateKey}`;
  }

  return `daisy-table:${context.config.endpoint?.url || context.root.id || 'default'}`;
}

function serializeStateToParams(config, state) {
  if (config.serverAdapter === 'spatie-query-builder') {
    return buildSpatieRequestParams(config, state);
  }

  return serializeRequestPayload(buildRequestPayload(config, state));
}

function parseStateFromUrl(config) {
  if (typeof window === 'undefined') {
    return {};
  }

  const params = new URLSearchParams(window.location.search);

  if (config.serverAdapter === 'spatie-query-builder') {
    const sort = params.get('sort');
    const columnFilters = config.filters.map((filter) => {
      const value = params.get(`filter[${filter.filterKey}]`);

      if (value == null) {
        return null;
      }

      return {
        id: filter.id,
        type: filter.type,
        value: filter.type === 'boolean' ? value === 'true' : value,
      };
    }).filter(Boolean);

    return {
      sorting: sort
        ? sort.split(',').filter(Boolean).map((entry) => ({
          id: config.columns.find((column) => column.sortKey === entry.replace(/^-/, ''))?.key || entry.replace(/^-/, ''),
          desc: entry.startsWith('-'),
        }))
        : [],
      pagination: {
        pageIndex: Math.max(0, (Number.parseInt(params.get('page[number]'), 10) || 1) - 1),
        pageSize: Number.parseInt(params.get('page[size]'), 10) || undefined,
      },
      globalFilter: params.get(`filter[${config.globalFilterKey}]`) || '',
      columnFilters,
    };
  }

  const sorting = params.get('sorting');
  const columnFilters = params.get('columnFilters');
  const columnVisibility = params.get('columnVisibility');

  return {
    sorting: sorting ? JSON.parse(sorting) : [],
    pagination: {
      pageIndex: Number.parseInt(params.get('pageIndex'), 10) || 0,
      pageSize: Number.parseInt(params.get('pageSize'), 10) || undefined,
    },
    globalFilter: params.get('globalFilter') || '',
    columnFilters: columnFilters ? JSON.parse(columnFilters) : [],
    columnVisibility: columnVisibility ? JSON.parse(columnVisibility) : {},
  };
}

function parseStateFromLocalStorage(context) {
  if (typeof window === 'undefined' || context.config.persistState !== 'local') {
    return {};
  }

  try {
    const raw = window.localStorage.getItem(getPersistedStateKey(context));

    return raw ? JSON.parse(raw) : {};
  } catch (_) {
    return {};
  }
}

function persistState(context) {
  if (context.config.persistState === false || typeof window === 'undefined') {
    return;
  }

  if (context.config.persistState === 'url') {
    const params = serializeStateToParams(context.config, context.state);
    const url = new URL(window.location.href);

    url.search = params.toString();
    window.history.replaceState({}, '', url);
    return;
  }

  try {
    window.localStorage.setItem(getPersistedStateKey(context), JSON.stringify(context.state));
  } catch (_) {}
}

function mergeState(baseState, overrideState = {}, config) {
  const nextState = cloneState(baseState);

  if (Array.isArray(overrideState.sorting)) {
    nextState.sorting = normalizeSorting(overrideState.sorting, config.columns);
  }

  if (isPlainObject(overrideState.pagination)) {
    const pageSize = Number.parseInt(overrideState.pagination.pageSize, 10);
    const pageIndex = Number.parseInt(overrideState.pagination.pageIndex, 10);

    if (Number.isInteger(pageIndex) && pageIndex >= 0) {
      nextState.pagination.pageIndex = pageIndex;
    }

    if (config.pageSizeOptions.includes(pageSize)) {
      nextState.pagination.pageSize = pageSize;
    }
  }

  if (typeof overrideState.globalFilter === 'string') {
    nextState.globalFilter = overrideState.globalFilter;
  }

  if (Array.isArray(overrideState.columnFilters)) {
    nextState.columnFilters = normalizeColumnFilters(overrideState.columnFilters, config.filters);
  }

  if (isPlainObject(overrideState.columnVisibility)) {
    nextState.columnVisibility = normalizeColumnVisibility(overrideState.columnVisibility, config.columns);
  }

  return nextState;
}

function createFilterState(filterId, type, input) {
  if (type === 'boolean') {
    return {
      id: filterId,
      type,
      value: input.checked === true,
    };
  }

  return {
    id: filterId,
    type,
    value: String(input.value ?? ''),
  };
}

function updateFilterState(context, nextFilter) {
  const filters = context.state.columnFilters.filter((filter) => filter.id !== nextFilter.id);

  if (nextFilter.type === 'boolean') {
    if (nextFilter.value === true) {
      filters.push(nextFilter);
    }
  } else if (nextFilter.value !== '') {
    filters.push(nextFilter);
  }

  context.state.columnFilters = filters;
}

function renderTable(context) {
  const filteredRows = context.config.mode === 'client'
    ? applyClientFilters(context.config.rows, context.config.columns, context.state)
    : context.rows;
  const rowCount = context.config.mode === 'client' ? filteredRows.length : context.rowCount;
  const pageCount = context.config.mode === 'client'
    ? Math.max(1, Math.ceil(Math.max(1, filteredRows.length) / context.state.pagination.pageSize))
    : Math.max(1, context.pageCount);

  context.rowCount = rowCount;
  context.pageCount = pageCount;

  if (context.state.pagination.pageIndex > pageCount - 1) {
    context.state.pagination.pageIndex = Math.max(0, pageCount - 1);
  }

  context.table = createTableModel(
    context.config,
    context.state,
    filteredRows,
    rowCount,
    pageCount
  );

  const rowModel = context.table.getRowModel().rows;

  renderHeader(context);
  renderColumnVisibility(context);
  syncControls(context);
  renderBody(context, rowModel);
  renderFooter(context, rowModel.length);
}

async function fetchServerRows(context) {
  const request = buildServerRequest(context.config, context.state);
  const response = await fetch(request.url, request.requestInit);

  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`);
  }

  return request.responseNormalizer(await response.json());
}

async function refreshTable(context) {
  context.loading = true;
  context.error = '';
  renderTable(context);

  if (context.config.mode !== 'server') {
    context.loading = false;
    persistState(context);
    renderTable(context);
    return;
  }

  try {
    const response = await fetchServerRows(context);

    context.rows = response.rows;
    context.rowCount = response.rowCount;
    context.pageCount = response.pageCount;
    context.state.pagination.pageIndex = response.state.pageIndex;
    context.state.pagination.pageSize = response.state.pageSize;
    context.loading = false;
    persistState(context);
    renderTable(context);
  } catch (_) {
    context.loading = false;
    context.error = context.config.errorLabel;
    context.rows = [];
    context.rowCount = 0;
    context.pageCount = 1;
    renderTable(context);
  }
}

function attachEvents(context) {
  const searchInput = context.root.querySelector('[data-table-search]');
  const pageSize = context.root.querySelector('[data-table-page-size]');
  const previousButton = context.root.querySelector('[data-table-prev]');
  const nextButton = context.root.querySelector('[data-table-next]');
  let searchTimeout;

  context.root.addEventListener('click', (event) => {
    const button = event.target instanceof Element ? event.target.closest('[data-table-sort]') : null;

    if (!(button instanceof HTMLElement)) {
      return;
    }

    context.state.sorting = toggleSorting(context.state, button.dataset.tableSort);
    context.state.pagination.pageIndex = 0;
    void refreshTable(context);
  });

  context.root.addEventListener('change', (event) => {
    const target = event.target;

    if (target instanceof HTMLInputElement && target.matches('[data-table-column-toggle]')) {
      context.state.columnVisibility[target.dataset.tableColumnToggle] = target.checked;
      context.state.pagination.pageIndex = 0;
      void refreshTable(context);
      return;
    }

    if ((target instanceof HTMLInputElement || target instanceof HTMLSelectElement) && target.matches('[data-table-filter]')) {
      updateFilterState(context, createFilterState(target.dataset.tableFilter, target.dataset.tableFilterType || 'text', target));
      context.state.pagination.pageIndex = 0;
      void refreshTable(context);
    }
  });

  if (searchInput instanceof HTMLInputElement) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        context.state.globalFilter = searchInput.value;
        context.state.pagination.pageIndex = 0;
        void refreshTable(context);
      }, 150);
    });
  }

  if (pageSize instanceof HTMLSelectElement) {
    pageSize.addEventListener('change', () => {
      context.state.pagination.pageSize = Number.parseInt(pageSize.value, 10) || context.state.pagination.pageSize;
      context.state.pagination.pageIndex = 0;
      void refreshTable(context);
    });
  }

  if (previousButton instanceof HTMLButtonElement) {
    previousButton.addEventListener('click', () => {
      context.state.pagination.pageIndex = Math.max(0, context.state.pagination.pageIndex - 1);
      void refreshTable(context);
    });
  }

  if (nextButton instanceof HTMLButtonElement) {
    nextButton.addEventListener('click', () => {
      context.state.pagination.pageIndex += 1;
      void refreshTable(context);
    });
  }
}

async function initTable(root) {
  const container = root?.matches?.('[data-daisy-table="1"]')
    ? root
    : root?.querySelector?.('[data-daisy-table="1"]');

  if (!(container instanceof HTMLElement) || container.__daisyTableInit) {
    return null;
  }

  const config = normalizeConfig(parseConfig(container));
  const context = {
    root: container,
    config,
    state: cloneState(config.initialState),
    rows: config.mode === 'client' ? config.rows : [],
    rowCount: config.mode === 'client' ? config.rows.length : 0,
    pageCount: 1,
    loading: config.mode === 'server',
    error: '',
    table: null,
  };

  context.state = mergeState(
    context.state,
    config.persistState === 'url' ? parseStateFromUrl(config) : parseStateFromLocalStorage(context),
    config
  );

  container.__daisyTableInit = true;
  container.__daisyTableContext = context;

  attachEvents(context);
  await refreshTable(context);

  return context;
}

async function initAllTables() {
  if (typeof document === 'undefined') {
    return;
  }

  await Promise.all(
    Array.from(document.querySelectorAll('[data-daisy-table="1"]')).map((root) => initTable(root))
  );
}

if (typeof window !== 'undefined') {
  window.DaisyTable = {
    init: initTable,
    initAll: initAllTables,
  };
}

if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllTables);
  } else {
    initAllTables();
  }
}

export {
  DEFAULT_PAGE_SIZE_OPTIONS,
  applyClientFilters,
  buildRequestPayload,
  buildServerRequest,
  buildSpatieRequestParams,
  getPersistedStateKey,
  getSortDirection,
  initAllTables,
  initTable,
  mergeState,
  normalizeColumns,
  normalizeConfig,
  normalizeInitialState,
  normalizeServerResponse,
  normalizeSpatieResponse,
  parseConfig,
  parseStateFromLocalStorage,
  parseStateFromUrl,
  serializeRequestPayload,
  serializeStateToParams,
  toggleSorting,
};

export default initTable;
