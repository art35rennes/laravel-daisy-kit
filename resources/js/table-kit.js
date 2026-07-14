import {
  createTable,
  getCoreRowModel,
  getExpandedRowModel,
  getFacetedRowModel,
  getFacetedUniqueValues,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  sortingFns,
} from '@tanstack/table-core';
import { rankItem } from '@tanstack/match-sorter-utils';
import {
  ALLOWED_ALIGNMENTS,
  ALLOWED_FILTER_TYPES,
  ALLOWED_TRUNCATE_VALUES,
  ALLOWED_VERTICAL_ALIGNMENTS,
  DEFAULT_FILTER_DEBOUNCE_MS,
  DEFAULT_GLOBAL_FILTER_KEY,
  DEFAULT_METHOD,
  DEFAULT_MIN_SEARCH_CHARS,
  DEFAULT_MODE,
  DEFAULT_PAGE_SIZE_OPTIONS,
  DEFAULT_PERSIST_STATE,
  DEFAULT_SEARCH_DEBOUNCE_MS,
  DEFAULT_SELECTION_STATE,
  DEFAULT_SERVER_ADAPTER,
  cloneState,
  escapeHtml,
  isPlainObject,
  parseJsonParam,
} from './table/utils.js';
import {
  getDaisyColumnFromCell,
  getDaisyColumnFromHeader,
  getRowDetailContent,
  isSafeHref,
  normalizeCellDefinition,
  renderCellContent,
  renderLinkCell,
} from './table/renderers.js';
import {
  normalizeSelectionState,
  resetSelectionState,
  rowSelectionFromSelection,
  uniqueStringArray,
} from './table/selection.js';

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
    filterKeyFrom: typeof filter.filterKeyFrom === 'string' && filter.filterKeyFrom !== '' ? filter.filterKeyFrom : null,
    filterKeyTo: typeof filter.filterKeyTo === 'string' && filter.filterKeyTo !== '' ? filter.filterKeyTo : null,
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
      const type = ['actions', 'link', 'resource-link'].includes(column.type) ? column.type : null;
      const cell = normalizeCellDefinition(column);
      const rawEditor = isPlainObject(column.editor) ? column.editor : {};
      const editorType = ['text', 'textarea', 'number', 'select', 'boolean', 'date', 'blade'].includes(rawEditor.type)
        ? rawEditor.type
        : 'text';
      const width = typeof column.width === 'string' ? column.width : (type === 'actions' ? 'fit' : null);
      const align = ALLOWED_ALIGNMENTS.includes(column.align) ? column.align : (type === 'actions' ? 'center' : null);
      const density = ['compact', 'normal'].includes(column.density) ? column.density : (type === 'actions' ? 'compact' : null);

      return {
        key,
        type,
        label: typeof column.label === 'string' && column.label !== '' ? column.label : key,
        sortable: column.sortable === true,
        filterable: column.filterable === true,
        visible: column.visible !== false,
        sortKey,
        filterKey,
        width,
        minWidth: typeof column.minWidth === 'string' ? column.minWidth : null,
        maxWidth: typeof column.maxWidth === 'string' ? column.maxWidth : null,
        align,
        verticalAlign: ALLOWED_VERTICAL_ALIGNMENTS.includes(column.verticalAlign) ? column.verticalAlign : null,
        padding: ['none', 'compact', 'normal'].includes(column.padding) ? column.padding : null,
        density,
        nowrap: column.nowrap === true || type === 'actions',
        truncate: ALLOWED_TRUNCATE_VALUES.includes(column.truncate) ? column.truncate : false,
        cellWrapperClass: typeof column.cellWrapperClass === 'string' ? column.cellWrapperClass : '',
        headerWrapperClass: typeof column.headerWrapperClass === 'string' ? column.headerWrapperClass : '',
        cellClass: typeof column.cellClass === 'string' ? column.cellClass : '',
        headerClass: typeof column.headerClass === 'string' ? column.headerClass : '',
        help: typeof column.help === 'string' && column.help.trim() !== '' ? column.help.trim() : '',
        html: ['html', 'blade', 'actions'].includes(cell.renderer),
        cell,
        editor: {
          type: editorType,
          required: rawEditor.required === true,
          options: Array.isArray(rawEditor.options)
            ? rawEditor.options.filter((option) => isPlainObject(option) && Object.hasOwn(option, 'value'))
            : [],
          view: typeof rawEditor.view === 'string' && rawEditor.view !== '' ? rawEditor.view : null,
          template: typeof rawEditor.template === 'string' ? rawEditor.template : null,
        },
        enableResizing: column.enableResizing !== false,
        size: Number.isInteger(Number.parseInt(column.size, 10)) ? Number.parseInt(column.size, 10) : undefined,
        minSize: Number.isInteger(Number.parseInt(column.minSize, 10)) ? Number.parseInt(column.minSize, 10) : undefined,
        maxSize: Number.isInteger(Number.parseInt(column.maxSize, 10)) ? Number.parseInt(column.maxSize, 10) : undefined,
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

function normalizeIntegerOption(value, fallback, minimum = 0) {
  const parsed = Number.parseInt(value, 10);

  if (!Number.isInteger(parsed)) {
    return fallback;
  }

  return Math.max(minimum, parsed);
}

function isTextSearchReady(value, minChars) {
  const term = String(value ?? '').trim();

  return term === '' || term.length >= minChars;
}

function resolveSearchInputValue(value, activeValue = '', minChars = DEFAULT_MIN_SEARCH_CHARS) {
  if (isTextSearchReady(value, minChars)) {
    return String(value ?? '');
  }

  return String(activeValue ?? '') === '' ? null : '';
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
      const rawEntryValue = isPlainObject(entry.value) && ALLOWED_FILTER_TYPES.includes(entry.value.type)
        ? entry.value.value
        : entry.value;
      const rawEntryType = isPlainObject(entry.value) && ALLOWED_FILTER_TYPES.includes(entry.value.type)
        ? entry.value.type
        : entry.type;
      let value = String(rawEntryValue ?? '');

      if (definition.type === 'boolean') {
        value = rawEntryValue === true || rawEntryValue === 'true' || rawEntryValue === 1 || rawEntryValue === '1';
      }

      if (definition.type === 'date-range') {
        const rawValue = isPlainObject(rawEntryValue) ? rawEntryValue : {};

        value = {
          from: String(rawValue.from ?? '').trim(),
          to: String(rawValue.to ?? '').trim(),
        };
      }

      return {
        id: entry.id,
        type: ALLOWED_FILTER_TYPES.includes(rawEntryType) ? rawEntryType : definition.type,
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

function normalizeColumnOrder(columnOrder = [], columns = []) {
  const keys = columns.map((column) => column.key);

  if (!Array.isArray(columnOrder)) {
    return keys;
  }

  const ordered = columnOrder.filter((key) => keys.includes(key));

  return [...ordered, ...keys.filter((key) => !ordered.includes(key))];
}

function normalizeColumnPinning(columnPinning = {}, columns = []) {
  const keys = columns.map((column) => column.key);
  const normalizeSide = (values = []) => Array.isArray(values)
    ? values.filter((key, index, all) => keys.includes(key) && all.indexOf(key) === index)
    : [];

  return {
    left: normalizeSide(columnPinning.left),
    right: normalizeSide(columnPinning.right),
  };
}

function normalizeColumnSizing(columnSizing = {}, columns = []) {
  if (!isPlainObject(columnSizing)) {
    return {};
  }

  const keys = columns.map((column) => column.key);

  return Object.fromEntries(
    Object.entries(columnSizing)
      .map(([key, value]) => [key, Number.parseInt(value, 10)])
      .filter(([key, value]) => keys.includes(key) && Number.isInteger(value) && value > 0)
  );
}

function normalizeExpanded(expanded = {}) {
  if (expanded === true) {
    return true;
  }

  if (!isPlainObject(expanded)) {
    return {};
  }

  return Object.fromEntries(
    Object.entries(expanded).filter(([, value]) => value === true)
  );
}

function normalizeRowSelection(rowSelection = {}) {
  if (!isPlainObject(rowSelection)) {
    return {};
  }

  return Object.fromEntries(
    Object.entries(rowSelection).filter(([key, value]) => String(key) !== '' && value === true)
  );
}

function normalizeSelectionConfig(raw = {}) {
  const selectionMode = typeof raw.selection === 'string' ? raw.selection : raw.selection?.mode;
  const mode = ['multiple', 'single'].includes(selectionMode) ? selectionMode : 'none';
  const rowKey = typeof raw.rowKey === 'string' && raw.rowKey !== ''
    ? raw.rowKey
    : (typeof raw.selection?.rowKey === 'string' && raw.selection.rowKey !== '' ? raw.selection.rowKey : null);

  return {
    enabled: mode !== 'none' && rowKey !== null,
    mode,
    rowKey: mode !== 'none' ? rowKey : null,
    selectFiltered: mode === 'multiple' && raw.selection?.selectFiltered !== false,
    readOnly: raw.selection?.readOnly === true,
  };
}

function normalizeEditPolicy(policy = {}) {
  if (!isPlainObject(policy)) {
    return {
      required: [],
    };
  }

  return {
    ...policy,
    required: Array.isArray(policy.required)
      ? policy.required.filter((key, index, all) => typeof key === 'string' && key !== '' && all.indexOf(key) === index)
      : (policy.required === true ? true : []),
  };
}

function normalizeEditableConfig(raw = {}, columns = []) {
  const editable = raw.editable === true || raw.editable?.enabled === true;
  const update = isPlainObject(raw.editable?.update) ? raw.editable.update : {};
  const create = isPlainObject(raw.editable?.create) ? raw.editable.create : {};
  const endpoint = normalizeEndpoint(update.endpoint ?? raw.editEndpoint ?? raw.editable?.endpoint ?? null);
  const mode = ['cell', 'row'].includes(raw.editMode ?? raw.editable?.mode)
    ? raw.editMode ?? raw.editable.mode
    : 'cell';
  const rawMethod = update.method ?? raw.editMethod ?? raw.editable?.method;
  const method = typeof rawMethod === 'string' && rawMethod !== ''
    ? String(rawMethod).toUpperCase()
    : 'PATCH';
  const editableColumnKeys = Array.isArray(raw.editableColumns ?? raw.editable?.columns)
    ? raw.editableColumns ?? raw.editable.columns
    : [];
  const columnKeys = columns.map((column) => column.key);
  const defaultEditableKeys = columns
    .filter((column) => !column.html && !['actions', 'link', 'resource-link'].includes(column.type))
    .map((column) => column.key);
  const requestedEditableKeys = editableColumnKeys.length > 0 ? editableColumnKeys : defaultEditableKeys;

  return {
    enabled: editable,
    endpoint,
    method,
    mode,
    columns: requestedEditableKeys.filter((key, index, all) => (
      typeof key === 'string'
      && columnKeys.includes(key)
      && all.indexOf(key) === index
    )),
    policy: normalizeEditPolicy(raw.editPolicy ?? raw.editable?.policy),
    update: {
      strategy: update.strategy === 'local' ? 'local' : 'remote',
      endpoint,
      method,
    },
    create: {
      enabled: create.enabled === true,
      strategy: create.strategy === 'local' ? 'local' : 'remote',
      endpoint: normalizeEndpoint(create.endpoint),
      method: typeof create.method === 'string' && create.method !== '' ? create.method.toUpperCase() : 'POST',
      defaults: isPlainObject(create.defaults) ? create.defaults : {},
      position: 'top',
    },
  };
}

function createFilterSignature(state = {}) {
  return JSON.stringify({
    sorting: Array.isArray(state.sorting) ? state.sorting : [],
    globalFilter: typeof state.globalFilter === 'string' ? state.globalFilter : '',
    columnFilters: Array.isArray(state.columnFilters) ? state.columnFilters : [],
  });
}

function getRowData(row) {
  return row?.original ?? row;
}

function getStableRowKey(config) {
  return config.selection?.rowKey
    || config.rowKey
    || config.editable?.rowKey
    || null;
}

function getRowSelectionId(config, row) {
  if (getRowData(row)?.__daisyTableDraft === true) {
    return null;
  }

  const rowKey = getStableRowKey(config);

  if (typeof rowKey !== 'string' || rowKey === '') {
    return null;
  }

  const value = getRowData(row)?.[rowKey];

  if (value === null || value === undefined || String(value) === '') {
    return null;
  }

  return String(value);
}

function isRowSelected(state, config, row) {
  const rowId = getRowSelectionId(config, row);

  if (rowId === null) {
    return false;
  }

  if (state.selection?.allFilteredSelected === true) {
    return !state.selection.excludedIds.includes(rowId);
  }

  return state.selection?.selectedIds.includes(rowId) === true;
}

function toggleRowSelection(state, config, row, forceSelected = null) {
  const rowId = typeof row === 'string' ? row : getRowSelectionId(config, row);

  if (rowId === null) {
    return state.selection;
  }

  const selection = normalizeSelectionState(state.selection);
  const selected = selection.allFilteredSelected
    ? !selection.excludedIds.includes(rowId)
    : selection.selectedIds.includes(rowId);
  const nextSelected = forceSelected === null ? !selected : forceSelected === true;

  if (selection.allFilteredSelected) {
    selection.excludedIds = nextSelected
      ? selection.excludedIds.filter((id) => id !== rowId)
      : uniqueStringArray([...selection.excludedIds, rowId]);
  } else {
    selection.selectedIds = nextSelected
      ? uniqueStringArray([...selection.selectedIds, rowId])
      : selection.selectedIds.filter((id) => id !== rowId);
  }

  state.selection = selection;

  return selection;
}

function toggleVisibleRowsSelection(state, config, rows = [], forceSelected = null) {
  const visibleIds = rows
    .map((row) => getRowSelectionId(config, row))
    .filter((rowId) => rowId !== null);

  if (visibleIds.length === 0) {
    return state.selection;
  }

  const selection = normalizeSelectionState(state.selection);
  const allVisibleSelected = visibleIds.every((rowId) => {
    if (selection.allFilteredSelected) {
      return !selection.excludedIds.includes(rowId);
    }

    return selection.selectedIds.includes(rowId);
  });
  const nextSelected = forceSelected === null ? !allVisibleSelected : forceSelected === true;

  if (selection.allFilteredSelected) {
    selection.excludedIds = nextSelected
      ? selection.excludedIds.filter((rowId) => !visibleIds.includes(rowId))
      : uniqueStringArray([...selection.excludedIds, ...visibleIds]);
  } else if (nextSelected) {
    selection.selectedIds = uniqueStringArray([...selection.selectedIds, ...visibleIds]);
  } else {
    selection.selectedIds = selection.selectedIds.filter((rowId) => !visibleIds.includes(rowId));
  }

  state.selection = selection;

  return selection;
}

function selectAllFilteredRows(state) {
  state.selection = {
    ...DEFAULT_SELECTION_STATE,
    selectedIds: [],
    excludedIds: [],
    allFilteredSelected: true,
    selectionScope: 'filtered',
    filterSignature: createFilterSignature(state),
  };
  state.rowSelection = {};

  return state.selection;
}

function buildSelectionActionPayload(config, state) {
  const selection = normalizeSelectionState(state.selection);

  if (selection.allFilteredSelected) {
    return {
      mode: 'filtered',
      filters: normalizeColumnFilters(state.columnFilters, config.filters),
      sorting: normalizeSorting(state.sorting, config.columns),
      globalFilter: typeof state.globalFilter === 'string' ? state.globalFilter : '',
      excludedIds: selection.excludedIds,
    };
  }

  return {
    mode: 'ids',
    ids: selection.selectedIds,
  };
}

function buildSelectionDetail(context, visibleRows = []) {
  const selection = normalizeSelectionState(context.state.selection);
  const summary = getSelectionSummary(context, visibleRows);

  return {
    selectedIds: selection.selectedIds,
    excludedIds: selection.excludedIds,
    allFilteredSelected: selection.allFilteredSelected,
    selectionScope: selection.selectionScope,
    selectedCount: summary.selectedCount,
    visibleSelectedCount: summary.visibleSelectedCount,
    offPageCount: summary.offPageCount,
    excludedCount: summary.excludedCount,
    filterSignature: selection.filterSignature,
    tableState: {
      sorting: normalizeSorting(context.state.sorting, context.config.columns),
      pagination: { ...context.state.pagination },
      globalFilter: context.state.globalFilter,
      columnFilters: normalizeColumnFilters(context.state.columnFilters, context.config.filters),
    },
    actionPayload: buildSelectionActionPayload(context.config, context.state),
  };
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
    columnOrder: normalizeColumnOrder(initialState.columnOrder, columns),
    columnPinning: normalizeColumnPinning(initialState.columnPinning, columns),
    columnSizing: normalizeColumnSizing(initialState.columnSizing, columns),
    columnSizingInfo: isPlainObject(initialState.columnSizingInfo) ? initialState.columnSizingInfo : {
      startOffset: null,
      startSize: null,
      deltaOffset: null,
      deltaPercentage: null,
      isResizingColumn: false,
      columnSizingStart: [],
    },
    expanded: normalizeExpanded(initialState.expanded),
    rowSelection: normalizeRowSelection(initialState.rowSelection),
    selection: normalizeSelectionState(initialState.selection),
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
  const selection = normalizeSelectionConfig(raw);
  const rowKey = typeof raw.rowKey === 'string' && raw.rowKey !== '' ? raw.rowKey : selection.rowKey;
  const searchMode = raw.searchMode === 'includes' ? 'includes' : 'fuzzy';
  const subRowsKey = typeof raw.subRowsKey === 'string' && raw.subRowsKey !== '' ? raw.subRowsKey : null;
  const editable = normalizeEditableConfig(raw, columns);
  const config = {
    mode,
    method: typeof raw.method === 'string' && raw.method !== '' ? raw.method.toUpperCase() : DEFAULT_METHOD,
    serverAdapter,
    persistState,
    stateKey: typeof raw.stateKey === 'string' && raw.stateKey !== '' ? raw.stateKey : null,
    globalFilterKey: typeof raw.globalFilterKey === 'string' && raw.globalFilterKey !== '' ? raw.globalFilterKey : DEFAULT_GLOBAL_FILTER_KEY,
    rowKey,
    searchMode,
    subRowsKey,
    columns,
    filters,
    rows: Array.isArray(raw.rows) ? raw.rows : [],
    endpoint,
    search: raw.search !== false,
    externalFilters: raw.externalFilters === true,
    linkPolicy: isPlainObject(raw.linkPolicy) ? {
      allowedSchemes: Array.isArray(raw.linkPolicy.allowedSchemes) ? raw.linkPolicy.allowedSchemes : [],
    } : { allowedSchemes: [] },
    livewireMode: ['ignore', 'morph', 'none'].includes(raw.livewireMode) ? raw.livewireMode : 'none',
    searchDebounceMs: normalizeIntegerOption(raw.searchDebounceMs ?? raw.searchDebounce ?? raw.debounce, DEFAULT_SEARCH_DEBOUNCE_MS),
    filterDebounceMs: normalizeIntegerOption(raw.filterDebounceMs ?? raw.filterDebounce ?? raw.debounce, DEFAULT_FILTER_DEBOUNCE_MS),
    minSearchChars: normalizeIntegerOption(raw.minSearchChars ?? raw.minChars, DEFAULT_MIN_SEARCH_CHARS),
    columnVisibility: raw.columnVisibility === true,
    columnResizing: raw.columnResizing === true,
    rowDetail: isPlainObject(raw.rowDetail) ? {
      mode: ['none', 'inline', 'modal'].includes(raw.rowDetail.mode) ? raw.rowDetail.mode : 'none',
      view: typeof raw.rowDetail.view === 'string' && raw.rowDetail.view !== '' ? raw.rowDetail.view : null,
    } : { mode: 'none', view: null },
    selection,
    editable: {
      ...editable,
      rowKey,
    },
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

  if ((selection.enabled || config.rowDetail.mode !== 'none' || editable.enabled || subRowsKey) && !rowKey) {
    throw new Error('The table component requires a non-empty rowKey prop for interactive row features.');
  }

  if (editable.enabled && editable.update.strategy === 'remote' && !editable.update.endpoint) {
    throw new Error('The table component requires an editEndpoint when editable is enabled.');
  }

  if (editable.create.enabled && editable.create.strategy === 'remote' && !editable.create.endpoint) {
    throw new Error('The table component requires a create endpoint when remote row creation is enabled.');
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
    columnOrder: normalizeColumnOrder(state.columnOrder, config.columns),
    columnPinning: normalizeColumnPinning(state.columnPinning, config.columns),
    columnSizing: normalizeColumnSizing(state.columnSizing, config.columns),
    expanded: normalizeExpanded(state.expanded),
    rowSelection: normalizeRowSelection(state.rowSelection),
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
  params.set('columnOrder', JSON.stringify(payload.columnOrder ?? []));
  params.set('columnPinning', JSON.stringify(payload.columnPinning ?? {}));
  params.set('columnSizing', JSON.stringify(payload.columnSizing ?? {}));
  params.set('expanded', JSON.stringify(payload.expanded ?? {}));
  params.set('rowSelection', JSON.stringify(payload.rowSelection ?? {}));

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

    if (filter.type === 'date-range') {
      const value = isPlainObject(filter.value) ? filter.value : {};
      const from = String(value.from ?? '').trim();
      const to = String(value.to ?? '').trim();

      if (from !== '') {
        params.set(`filter[${definition?.filterKeyFrom || `${filterKey}_from`}]`, from);
      }

      if (to !== '') {
        params.set(`filter[${definition?.filterKeyTo || `${filterKey}_to`}]`, to);
      }

      return;
    }

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
  const baseUrl = typeof window !== 'undefined' && typeof window.location?.href === 'string'
    ? window.location.href
    : 'http://localhost';
  const endpoint = new URL(config.endpoint.url, baseUrl);
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

  if (filter.type === 'date-range') {
    const rawValue = String(value ?? '');
    const from = String(filter.value?.from ?? '');
    const to = String(filter.value?.to ?? '');

    if (from !== '' && rawValue < from) {
      return false;
    }

    if (to !== '' && rawValue > to) {
      return false;
    }

    return true;
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

function functionalUpdate(updater, previous) {
  return typeof updater === 'function' ? updater(previous) : updater;
}

function setControlledState(context, key, updater, normalizer = (value) => value) {
  context.state[key] = normalizer(functionalUpdate(updater, context.state[key]));
}

function fuzzyFilter(row, columnId, value, addMeta) {
  if (row.original?.__daisyTableDraft === true) {
    return true;
  }

  const itemRank = rankItem(row.getValue(columnId), value);

  addMeta({ itemRank });

  return itemRank.passed;
}

fuzzyFilter.autoRemove = (value) => String(value ?? '') === '';

function daisyColumnFilter(row, columnId, filterValue) {
  if (row.original?.__daisyTableDraft === true) {
    return true;
  }

  const filter = isPlainObject(filterValue) && ALLOWED_FILTER_TYPES.includes(filterValue.type)
    ? filterValue
    : { type: 'text', value: filterValue };

  return applyFilterValue(row.getValue(columnId), filter);
}

daisyColumnFilter.autoRemove = (filterValue) => {
  if (!isPlainObject(filterValue)) {
    return String(filterValue ?? '') === '';
  }

  if (filterValue.type === 'boolean') {
    return filterValue.value !== true;
  }

  if (filterValue.type === 'date-range') {
    return String(filterValue.value?.from ?? '') === '' && String(filterValue.value?.to ?? '') === '';
  }

  return String(filterValue.value ?? '') === '';
};

function createColumnDefs(columns) {
  return columns.map((column) => ({
    id: column.key,
    accessorFn: (row) => row?.[column.key],
    enableSorting: column.sortable === true,
    enableColumnFilter: column.filterable === true,
    enableGlobalFilter: column.filterable === true || columns.every((candidate) => candidate.filterable !== true),
    filterFn: daisyColumnFilter,
    sortingFn: (firstRow, secondRow, columnId) => {
      if (firstRow.original?.__daisyTableDraft === true) {
        return -1;
      }

      if (secondRow.original?.__daisyTableDraft === true) {
        return 1;
      }

      return sortingFns.alphanumeric(firstRow, secondRow, columnId);
    },
    enableResizing: column.enableResizing !== false,
    size: column.size,
    minSize: column.minSize,
    maxSize: column.maxSize,
    cell: (context) => context.getValue(),
    meta: {
      daisyColumn: column,
    },
  }));
}

function getTableRows(context) {
  const rows = context.config.mode === 'client' ? context.config.rows : context.rows;

  return context.creating?.row ? [context.creating.row, ...rows] : rows;
}

function createTableModel(context, rows, rowCount, pageCount) {
  const { config, state } = context;

  return createTable({
    data: rows,
    columns: createColumnDefs(config.columns),
    state: {
      sorting: state.sorting,
      pagination: state.pagination,
      columnVisibility: state.columnVisibility,
      columnOrder: state.columnOrder,
      columnPinning: state.columnPinning,
      columnSizing: state.columnSizing,
      columnSizingInfo: state.columnSizingInfo,
      expanded: state.expanded,
      rowSelection: state.rowSelection,
      globalFilter: state.globalFilter,
      columnFilters: state.columnFilters,
    },
    getRowId: (row, index) => {
      const rowKey = getStableRowKey(config);
      const value = row?.__daisyTableDraftId ?? (typeof rowKey === 'string' && rowKey !== '' ? row?.[rowKey] : null);

      return value === null || value === undefined || String(value) === '' ? String(index) : String(value);
    },
    getSubRows: config.subRowsKey
      ? (row) => {
        const subRows = row?.[config.subRowsKey];

        return Array.isArray(subRows) ? subRows : undefined;
      }
      : undefined,
    manualPagination: config.mode === 'server',
    manualSorting: config.mode === 'server',
    manualFiltering: config.mode === 'server',
    manualExpanding: config.mode === 'server' && !config.subRowsKey,
    globalFilterFn: config.searchMode === 'includes' ? 'includesString' : 'fuzzy',
    filterFns: {
      fuzzy: fuzzyFilter,
      daisy: daisyColumnFilter,
    },
    enableRowSelection: (row) => config.selection.enabled === true && row.original?.__daisyTableDraft !== true,
    enableMultiRowSelection: config.selection.mode === 'multiple',
    enableSubRowSelection: false,
    enableColumnResizing: config.columnResizing === true,
    columnResizeMode: 'onChange',
    onSortingChange: (updater) => setControlledState(context, 'sorting', updater, (value) => normalizeSorting(value, config.columns)),
    onPaginationChange: (updater) => setControlledState(context, 'pagination', updater, (value) => ({
      pageIndex: Math.max(0, Number.parseInt(value?.pageIndex, 10) || 0),
      pageSize: config.pageSizeOptions.includes(Number.parseInt(value?.pageSize, 10))
        ? Number.parseInt(value.pageSize, 10)
        : state.pagination.pageSize,
    })),
    onColumnVisibilityChange: (updater) => setControlledState(context, 'columnVisibility', updater, (value) => normalizeColumnVisibility(value, config.columns)),
    onColumnOrderChange: (updater) => setControlledState(context, 'columnOrder', updater, (value) => normalizeColumnOrder(value, config.columns)),
    onColumnPinningChange: (updater) => setControlledState(context, 'columnPinning', updater, (value) => normalizeColumnPinning(value, config.columns)),
    onColumnSizingChange: (updater) => setControlledState(context, 'columnSizing', updater, (value) => normalizeColumnSizing(value, config.columns)),
    onColumnSizingInfoChange: (updater) => setControlledState(context, 'columnSizingInfo', updater),
    onExpandedChange: (updater) => setControlledState(context, 'expanded', updater, normalizeExpanded),
    onRowSelectionChange: (updater) => {
      setControlledState(context, 'rowSelection', updater, normalizeRowSelection);
      syncSelectionFromRowSelection(context);
    },
    onGlobalFilterChange: (updater) => setControlledState(context, 'globalFilter', updater, (value) => String(value ?? '')),
    onColumnFiltersChange: (updater) => setControlledState(context, 'columnFilters', updater, (value) => normalizeColumnFilters(value, config.filters)),
    rowCount,
    pageCount,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getExpandedRowModel: getExpandedRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
  });
}

function getVisibleColumns(config, state) {
  const visibleColumns = config.columns.filter((column) => state.columnVisibility[column.key] !== false);
  const order = normalizeColumnOrder(state.columnOrder, config.columns);

  return [...visibleColumns].sort((first, second) => order.indexOf(first.key) - order.indexOf(second.key));
}

function getDisplayValue(row, column) {
  const value = row?.[column.key];

  if (value == null) {
    return '';
  }

  return String(value);
}

function getVisibleTanStackColumns(context) {
  if (!context.table) {
    return getVisibleColumns(context.config, context.state);
  }

  return context.table.getVisibleLeafColumns()
    .map((column) => column.columnDef?.meta?.daisyColumn)
    .filter(Boolean);
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

function tableWidthClass(value) {
  const raw = String(value || '').trim();

  if (raw === 'fit') {
    return 'daisy-table-width-fit';
  }

  return tableDimensionClass(raw, 'daisy-table-width');
}

function tableMinWidthClass(value) {
  const raw = String(value || '').trim();

  if (raw === 'max-content') {
    return 'daisy-table-min-width-max';
  }

  if (raw === 'full') {
    return 'min-w-full';
  }

  return tableDimensionClass(raw, 'daisy-table-min-width');
}

function tableMaxWidthClass(value) {
  return tableDimensionClass(value, 'daisy-table-max-width');
}

function tableDimensionClass(value, prefix) {
  const raw = String(value || '').trim();
  let match = raw.match(/^(\d+(?:\.\d+)?)px$/);

  if (match) {
    const token = Math.round(Number(match[1]));

    return token >= 1 && token <= 1200 ? `${prefix}-px-${token}` : '';
  }

  match = raw.match(/^(\d+(?:\.\d+)?)%$/);

  if (match) {
    const token = Math.round(Number(match[1]));

    return token >= 1 && token <= 100 ? `${prefix}-percent-${token}` : '';
  }

  match = raw.match(/^(\d+(?:\.\d+)?)rem$/);

  if (match) {
    const token = Math.round(Number(match[1]) * 4);

    return token >= 1 && token <= 512 ? `${prefix}-rem-${token}` : '';
  }

  return '';
}

function getColumnClasses(column, target) {
  return [
    target === 'header' ? column.headerClass : column.cellClass,
    tableWidthClass(column.width),
    tableMinWidthClass(column.minWidth),
    tableMaxWidthClass(column.maxWidth),
    column.align === 'center' ? 'text-center' : '',
    column.align === 'right' ? 'text-right' : '',
    column.align === 'left' ? 'text-left' : '',
    column.verticalAlign === 'top' ? 'align-top' : '',
    column.verticalAlign === 'middle' ? 'align-middle' : '',
    column.verticalAlign === 'bottom' ? 'align-bottom' : '',
    column.padding === 'none' ? 'p-0' : '',
    column.padding === 'compact' ? 'px-2 py-1' : '',
    column.density === 'compact' ? 'daisy-table-cell-compact' : '',
    column.nowrap ? 'whitespace-nowrap' : '',
    column.type === 'actions' ? 'daisy-table-actions-cell' : '',
  ].filter(Boolean).join(' ');
}

function getColumnWrapperClasses(column, target) {
  return [
    target === 'header' ? 'daisy-table-header-content' : 'daisy-table-cell-content',
    target === 'header' ? column.headerWrapperClass : column.cellWrapperClass,
    target === 'cell' && column.type === 'actions' ? 'daisy-table-actions-content' : '',
    target === 'cell' && column.truncate === 'line' ? 'truncate' : '',
    target === 'cell' && (column.truncate === 2 || column.truncate === 3) ? `line-clamp-${column.truncate}` : '',
  ].filter(Boolean).join(' ');
}

function renderColgroup(context) {
  const colgroup = context.root.querySelector('[data-table-colgroup]');

  if (!(colgroup instanceof HTMLElement)) {
    return;
  }

  const selectionCol = context.config.selection.enabled ? '<col class="daisy-table-selection-col">' : '';
  const expandCol = context.config.subRowsKey ? '<col class="daisy-table-expand-col">' : '';
  const detailCol = context.config.rowDetail.mode !== 'none' ? '<col class="daisy-table-detail-col">' : '';
  const columns = context.table
    ? context.table.getVisibleLeafColumns()
    : getVisibleTanStackColumns(context).map((column) => ({ columnDef: { meta: { daisyColumn: column } } }));
  const columnMarkup = columns.map((tanStackColumn) => {
    const column = tanStackColumn.columnDef?.meta?.daisyColumn;

    if (!column) {
      return '<col>';
    }

    const classes = [
      tableWidthClass(column.width),
      tableMinWidthClass(column.minWidth),
      tableMaxWidthClass(column.maxWidth),
    ].filter(Boolean).join(' ');
    const width = context.config.columnResizing && typeof tanStackColumn.getSize === 'function'
      ? ` width="${escapeHtml(tanStackColumn.getSize())}"`
      : '';

    return classes ? `<col class="${escapeHtml(classes)}"${width}>` : `<col${width}>`;
  }).join('');

  colgroup.innerHTML = `${selectionCol}${expandCol}${detailCol}${columnMarkup}`;
}

function renderHeaderHelp(help) {
  if (!help) {
    return '';
  }

  const escapedHelp = escapeHtml(help);

  return ` <span class="tooltip tooltip-top inline-flex align-middle" data-tip="${escapedHelp}" aria-label="${escapedHelp}"><svg class="bi bi-info-circle daisy-table-header-help" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 .936-.252 1.107-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/></svg></span>`;
}

function renderHeader(context) {
  const headRow = context.root.querySelector('[data-table-head-row]');

  if (!(headRow instanceof HTMLElement)) {
    return;
  }

  headRow.innerHTML = '';

  if (context.config.selection.enabled) {
    const th = document.createElement('th');

    th.className = 'daisy-table-selection-cell';
    th.innerHTML = context.config.selection.mode === 'multiple'
      ? `
          <input
            type="checkbox"
            class="checkbox checkbox-sm"
            data-table-select-page
            aria-label="${escapeHtml(context.config.labels.selectAllRows || 'Select all rows on this page')}"
            ${context.config.selection.readOnly ? 'disabled' : ''}
          >
        `
      : '<span class="sr-only">Sélection</span>';
    headRow.append(th);
  }

  if (context.config.subRowsKey) {
    const th = document.createElement('th');

    th.className = 'daisy-table-expand-cell';
    th.innerHTML = '<span class="sr-only">Développer la ligne</span>';
    headRow.append(th);
  }

  if (context.config.rowDetail.mode !== 'none') {
    const th = document.createElement('th');

    th.className = 'daisy-table-detail-cell';
    th.innerHTML = '<span class="sr-only">Details</span>';
    headRow.append(th);
  }

  const headers = context.table
    ? context.table.getFlatHeaders().filter((header) => header.column.getIsVisible())
    : getVisibleColumns(context.config, context.state).map((column) => ({ column: { columnDef: { meta: { daisyColumn: column } } } }));

  headers.forEach((header) => {
    const column = getDaisyColumnFromHeader(header);

    if (!column) {
      return;
    }

    const th = document.createElement('th');
    const thClasses = getColumnClasses(column, 'header');
    const wrapperClass = escapeHtml(getColumnWrapperClasses(column, 'header'));

    if (thClasses) {
      th.className = thClasses;
    }

    if (context.config.columnResizing && typeof header.column?.getSize === 'function') {
      const columnSize = String(header.column.getSize());

      th.setAttribute('width', columnSize);
      th.dataset.tableColumnSize = columnSize;
    }

    if (column.sortable) {
      const button = document.createElement('button');
      const direction = typeof header.column?.getIsSorted === 'function'
        ? header.column.getIsSorted()
        : getSortDirection(context.state, column.key);

      button.type = 'button';
      button.className = 'daisy-table-head-button';
      button.dataset.tableSort = column.key;
      button.setAttribute('aria-sort', direction === 'asc' ? 'ascending' : direction === 'desc' ? 'descending' : 'none');
      button.innerHTML = `<span class="${wrapperClass}">${escapeHtml(column.label)}${renderHeaderHelp(column.help)} <span class="daisy-table-sort-indicator" aria-hidden="true">${direction === 'asc' ? '&uarr;' : direction === 'desc' ? '&darr;' : '&harr;'}</span></span>`;
      th.append(button);
    } else {
      th.innerHTML = `<span class="${wrapperClass}">${escapeHtml(column.label)}${renderHeaderHelp(column.help)}</span>`;
    }

    if (context.config.columnResizing && typeof header.getResizeHandler === 'function' && header.column?.getCanResize?.()) {
      const resizeHandle = document.createElement('button');

      resizeHandle.type = 'button';
      resizeHandle.className = 'daisy-table-resize-handle';
      resizeHandle.dataset.tableResize = header.id;
      resizeHandle.setAttribute('aria-label', `Resize ${column.label}`);
      resizeHandle.setAttribute('title', `Resize ${column.label}`);
      th.append(resizeHandle);
    }

    headRow.append(th);
  });
}

function renderBody(context, rows) {
  const tbody = context.root.querySelector('[data-table-body]');

  if (!(tbody instanceof HTMLElement)) {
    return;
  }

  const visibleColumns = getVisibleTanStackColumns(context);
  const colspan = Math.max(
    1,
    visibleColumns.length
      + (context.config.selection.enabled ? 1 : 0)
      + (context.config.subRowsKey ? 1 : 0)
      + (context.config.rowDetail.mode !== 'none' ? 1 : 0)
  );

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
    const rowId = getRowSelectionId(context.config, row);
    const selectionInputType = context.config.selection.mode === 'single' ? 'radio' : 'checkbox';
    const selectionInputClass = context.config.selection.mode === 'single' ? 'radio radio-sm' : 'checkbox checkbox-sm';
    const selectionCell = context.config.selection.enabled
      ? `<td class="daisy-table-selection-cell">
          <input
            type="${selectionInputType}"
            class="${selectionInputClass}"
            data-table-row-select="${escapeHtml(rowId || '')}"
            aria-label="${escapeHtml(context.config.labels.selectRow || 'Select row')}"
            ${rowId === null || context.config.selection.readOnly ? 'disabled' : ''}
            ${isRowSelected(context.state, context.config, row) ? 'checked' : ''}
          >
        </td>`
      : '';
    const rowData = row.original ?? row;
    const expandCell = context.config.subRowsKey
      ? `<td class="daisy-table-expand-cell">${row.getCanExpand?.()
        ? `<button type="button" class="btn btn-sm btn-square btn-ghost daisy-table-expand-button" data-table-row-expand="${escapeHtml(row.id ?? getRowSelectionId(context.config, row) ?? '')}" aria-expanded="${row.getIsExpanded?.() ? 'true' : 'false'}" aria-label="${row.getIsExpanded?.() ? 'Réduire la ligne' : 'Développer la ligne'}" title="${row.getIsExpanded?.() ? 'Réduire la ligne' : 'Développer la ligne'}"><svg class="bi bi-chevron-right daisy-table-expand-chevron${row.getIsExpanded?.() ? ' is-expanded' : ''}" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6.646 3.646a.5.5 0 0 1 .708 0l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L10.293 8 6.646 4.354a.5.5 0 0 1 0-.708"/></svg></button>`
        : ''}</td>`
      : '';
    const detailCell = context.config.rowDetail.mode !== 'none'
      ? `<td class="daisy-table-detail-cell">
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            data-table-row-detail="${escapeHtml(row.id ?? getRowSelectionId(context.config, row) ?? '')}"
            aria-expanded="${row.getIsExpanded?.() ? 'true' : 'false'}"
          >...</button>
        </td>`
      : '';
    const cells = (typeof row.getVisibleCells === 'function' ? row.getVisibleCells() : visibleColumns.map((column) => ({
      getContext: () => ({ renderValue: () => getDisplayValue(row.original ?? row, column) }),
      getValue: () => getDisplayValue(row.original ?? row, column),
      column: { columnDef: { meta: { daisyColumn: column } } },
    }))).map((cell) => {
      const column = getDaisyColumnFromCell(cell);

      if (!column) {
        return '';
      }

      const classes = getColumnClasses(column, 'cell');
      const className = classes ? ` class="${escapeHtml(classes)}"` : '';
      const cellWidth = context.config.columnResizing && typeof cell.column?.getSize === 'function'
        ? ` width="${escapeHtml(cell.column.getSize())}" data-table-column-size="${escapeHtml(cell.column.getSize())}"`
        : '';
      const wrapperClass = escapeHtml(getColumnWrapperClasses(column, 'cell'));
      const content = isCellEditing(context, row, column)
        ? renderEditCellContent(context, row, column)
        : renderCellContent(cell, context.config.linkPolicy);
      const editable = context.config.editable.enabled && context.config.editable.columns.includes(column.key);
      const editAttrs = editable
        ? ` data-table-edit-cell data-table-row-id="${escapeHtml(row.id)}" data-table-column-id="${escapeHtml(column.key)}"`
        : '';

      return `<td${className}${cellWidth}${editAttrs}><span class="${wrapperClass}">${content}</span></td>`;
    }).join('');

    const detailRow = context.config.rowDetail.mode === 'inline' && row.getIsExpanded?.()
      ? `<tr class="daisy-table-detail-row"><td colspan="${colspan}">${getRowDetailContent(rowData)}</td></tr>`
      : '';
    const editActionsRow = renderEditRowActions(context, row, colspan);

    return `<tr>${selectionCell}${expandCell}${detailCell}${cells}</tr>${editActionsRow}${detailRow}`;
  }).join('');
}

function openDetailModal(context, rowId) {
  const row = context.visibleRows.find((candidate) => String(candidate.id) === String(rowId));
  const rowData = row?.original ?? null;

  if (!rowData) {
    return;
  }

  let dialog = context.root.querySelector('[data-table-detail-modal]');

  if (!(dialog instanceof HTMLDialogElement)) {
    dialog = document.createElement('dialog');
    dialog.className = 'modal';
    dialog.dataset.tableDetailModal = '1';
    dialog.innerHTML = `
      <div class="modal-box max-w-3xl">
        <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" data-table-detail-close aria-label="Close modal">&times;</button>
        <div data-table-detail-content></div>
      </div>
      <form method="dialog" class="modal-backdrop"><button>close</button></form>
    `;
    context.root.append(dialog);
  }

  const content = dialog.querySelector('[data-table-detail-content]');

  if (content instanceof HTMLElement) {
    content.innerHTML = getRowDetailContent(rowData);
  }

  if (typeof dialog.showModal === 'function') {
    dialog.showModal();
  } else {
    dialog.setAttribute('open', 'open');
  }
}

function getVisibleRowById(context, rowId) {
  return context.visibleRows.find((candidate) => String(candidate.id) === String(rowId)) ?? null;
}

function isCellEditing(context, row, column) {
  if (!context.editing || context.editing.saving) {
    return false;
  }

  if (String(context.editing.rowId) !== String(row.id)) {
    return false;
  }

  return context.editing.mode === 'row' || context.editing.columnId === column.key;
}

function renderEditCellContent(context, row, column) {
  const value = context.editing?.draft?.[column.key] ?? '';
  const error = context.editing?.errors?.[column.key] ?? '';
  const saving = context.editing?.saving === true;
  const editor = column.editor || { type: 'text', options: [] };
  const actions = context.editing?.mode === 'row'
    ? ''
    : `
      <div class="daisy-table-edit-actions">
        <button type="button" class="btn btn-xs btn-primary" data-table-edit-save ${saving ? 'disabled' : ''}>Save</button>
        <button type="button" class="btn btn-xs btn-ghost" data-table-edit-cancel ${saving ? 'disabled' : ''}>Cancel</button>
      </div>
    `;

  const attributes = `data-table-edit-input data-table-row-id="${escapeHtml(row.id)}" data-table-column-id="${escapeHtml(column.key)}" aria-invalid="${error ? 'true' : 'false'}" ${saving ? 'disabled' : ''}`;
  let input = '';

  if (editor.type === 'blade' && editor.template) {
    input = `<div class="daisy-table-custom-editor" data-table-editor-column-id="${escapeHtml(column.key)}" data-table-editor-row-id="${escapeHtml(row.id)}">${editor.template}</div>`;
  } else if (editor.type === 'textarea') {
    input = `<textarea class="textarea textarea-bordered textarea-xs w-full" ${attributes}>${escapeHtml(value)}</textarea>`;
  } else if (editor.type === 'select') {
    const options = editor.options.map((option) => {
      const optionValue = String(option.value ?? '');
      const selected = String(value) === optionValue ? ' selected' : '';

      return `<option value="${escapeHtml(optionValue)}"${selected}>${escapeHtml(option.label ?? optionValue)}</option>`;
    }).join('');
    input = `<select class="select select-bordered select-xs w-full" ${attributes}>${options}</select>`;
  } else if (editor.type === 'boolean') {
    input = `<input type="checkbox" class="toggle toggle-sm" ${attributes} ${value === true || value === 'true' || value === 1 || value === '1' ? 'checked' : ''}>`;
  } else {
    const type = editor.type === 'number' || editor.type === 'date' ? editor.type : 'text';
    input = `<input type="${type}" class="input input-bordered input-xs w-full" ${attributes} value="${escapeHtml(value)}">`;
  }

  return `<div class="daisy-table-edit-cell">${input}${actions}${error ? `<p class="daisy-table-edit-error text-xs text-error">${escapeHtml(error)}</p>` : ''}</div>`;
}

function renderEditRowActions(context, row, colspan) {
  if (!context.editing || context.editing.mode !== 'row' || String(context.editing.rowId) !== String(row.id)) {
    return '';
  }

  const saving = context.editing.saving === true;
  const dirtyCount = Object.values(context.editing.dirty).filter((value) => value === true).length;
  const hasErrors = Object.keys(context.editing.errors || {}).length > 0;
  const status = hasErrors
    ? '<span class="text-error">Please fix the highlighted fields.</span>'
    : `<span class="text-base-content/60">${dirtyCount} changed field${dirtyCount === 1 ? '' : 's'}</span>`;

  return `
    <tr class="daisy-table-edit-row-actions">
      <td colspan="${colspan}">
        <div class="daisy-table-edit-row-bar">
          ${status}
          <span class="daisy-table-edit-actions">
            <button type="button" class="btn btn-xs btn-primary" data-table-edit-save ${saving ? 'disabled' : ''}>${context.editing.type === 'create' ? 'Add row' : 'Save row'}</button>
            <button type="button" class="btn btn-xs btn-ghost" data-table-edit-cancel ${saving ? 'disabled' : ''}>Cancel</button>
          </span>
        </div>
      </td>
    </tr>
  `;
}

function getRequiredEditableColumns(context) {
  const required = context.config.editable.policy?.required;

  if (required === true) {
    return context.config.editable.columns;
  }

  if (!Array.isArray(required)) {
    return [];
  }

  return required.filter((key) => context.config.editable.columns.includes(key));
}

function validateEdit(context) {
  if (!context.editing) {
    return {};
  }

  const editedKeys = context.editing.mode === 'row'
    ? Object.keys(context.editing.draft)
    : [context.editing.columnId];
  const requiredKeys = [...new Set([
    ...getRequiredEditableColumns(context),
    ...context.config.columns.filter((column) => column.editor?.required === true).map((column) => column.key),
  ])]
    .filter((key) => editedKeys.includes(key));

  return Object.fromEntries(
    requiredKeys
      .filter((key) => String(context.editing.draft?.[key] ?? '').trim() === '')
      .map((key) => [key, 'This value is required.'])
  );
}

function startCellEdit(context, rowId, columnId) {
  if (context.loading || !context.config.editable.enabled || !context.config.editable.columns.includes(columnId)) {
    return null;
  }

  const row = getVisibleRowById(context, rowId);
  const rowData = row?.original ?? null;

  if (!row || !rowData) {
    return null;
  }

  const editableColumns = context.config.editable.mode === 'row'
    ? context.config.editable.columns
    : [columnId];

  context.editing = {
    type: 'update',
    mode: context.config.editable.mode,
    rowId: String(row.id),
    columnId,
    original: { ...rowData },
    draft: Object.fromEntries(editableColumns.map((key) => [key, String(rowData?.[key] ?? '')])),
    dirty: {},
    errors: {},
    saving: false,
  };

  dispatchTableEditEvent(context, 'daisy:table-edit-started', {
    rowId: String(row.id),
    column: columnId,
    row: rowData,
    mode: context.editing.mode,
  });

  renderTable(context);

  return context.editing;
}

function cancelEdit(context) {
  if (context.editing?.type === 'create') {
    cancelCreate(context);
    return;
  }

  context.editing = null;
  renderTable(context);
}

function getEditorColumnId(input) {
  return input.dataset.tableColumnId
    || input.dataset.tableEditorColumnId
    || input.closest('[data-table-editor-column-id]')?.dataset.tableEditorColumnId
    || null;
}

function readEditorValue(context, input, columnId) {
  const editor = getColumnByKey(context.config.columns, columnId)?.editor;

  if (editor?.type === 'boolean' && input instanceof HTMLInputElement) {
    return input.checked;
  }

  if (editor?.type === 'number') {
    const value = String(input.value ?? '').trim();

    return value === '' ? null : Number(value);
  }

  return String(input.value ?? '');
}

function updateEditDraft(context, input) {
  if (!context.editing) {
    return;
  }

  const columnId = getEditorColumnId(input);

  if (!context.config.editable.columns.includes(columnId)) {
    return;
  }

  context.editing.draft[columnId] = readEditorValue(context, input, columnId);
  context.editing.dirty[columnId] = JSON.stringify(context.editing.draft[columnId]) !== JSON.stringify(context.editing.original?.[columnId] ?? '');

  if (context.editing.errors?.[columnId]) {
    delete context.editing.errors[columnId];
  }
}

function replaceRowInRows(rows = [], rowKey, rowId, nextRow) {
  return rows.map((row) => {
    if (String(row?.[rowKey] ?? '') === String(rowId)) {
      return {
        ...row,
        ...nextRow,
      };
    }

    return row;
  });
}

function applyEditedRow(context, rowId, nextRow) {
  const rowKey = getStableRowKey(context.config);

  if (!rowKey) {
    return;
  }

  if (context.config.mode === 'client') {
    context.config.rows = replaceRowInRows(context.config.rows, rowKey, rowId, nextRow);
    return;
  }

  context.rows = replaceRowInRows(context.rows, rowKey, rowId, nextRow);
}

function createDraftId() {
  return `daisy-draft-${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

function startCreate(context) {
  if (context.loading || !context.config.editable.create.enabled || context.creating) {
    return null;
  }

  const rowKey = getStableRowKey(context.config);
  const draftId = createDraftId();
  const values = {
    ...context.config.editable.create.defaults,
  };
  const draft = {
    ...values,
    __daisyTableDraft: true,
    __daisyTableDraftId: draftId,
  };

  context.creating = { row: draft };
  context.editing = {
    type: 'create',
    mode: 'row',
    rowId: draftId,
    columnId: context.config.editable.columns[0] || null,
    original: {},
    draft: Object.fromEntries(context.config.editable.columns.map((key) => [key, values[key] ?? ''])),
    dirty: {},
    errors: {},
    saving: false,
  };
  context.state.pagination.pageIndex = 0;

  dispatchTableEditEvent(context, 'daisy:table-create-started', {
    draft: { ...context.editing.draft },
    rowKey,
  });
  renderTable(context);
  focusEditor(context);

  return context.editing;
}

function cancelCreate(context) {
  if (!context.creating) {
    return false;
  }

  context.creating = null;
  context.editing = null;
  dispatchTableEditEvent(context, 'daisy:table-create-cancelled');
  renderTable(context);

  return true;
}

function applyCreatedRow(context, row) {
  if (context.config.mode === 'client') {
    context.config.rows = [row, ...context.config.rows];
    return;
  }

  context.rows = [row, ...context.rows];
}

function mutationRequest(endpoint, method, payload) {
  const url = new URL(endpoint.url, window.location.href);
  const headers = new Headers({
    Accept: 'application/json',
    'Content-Type': 'application/json',
    ...(endpoint.headers || {}),
  });
  const csrfToken = typeof document !== 'undefined'
    ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    : null;

  if (csrfToken && !headers.has('X-CSRF-TOKEN')) {
    headers.set('X-CSRF-TOKEN', csrfToken);
  }

  return fetch(url.toString(), {
    method,
    headers,
    credentials: endpoint.credentials,
    body: JSON.stringify(payload),
  });
}

function normalizeMutationErrors(error, fallbackColumn) {
  if (isPlainObject(error.errors)) {
    return Object.fromEntries(Object.entries(error.errors).map(([key, value]) => [key, Array.isArray(value) ? value.join(' ') : String(value)]));
  }

  return { [fallbackColumn]: error.message || 'Unable to save this value.' };
}

async function commitCreate(context) {
  if (!context.editing || context.editing.saving || context.editing.type !== 'create') {
    return null;
  }

  const validationErrors = validateEdit(context);

  if (Object.keys(validationErrors).length > 0) {
    context.editing.errors = validationErrors;
    dispatchTableEditEvent(context, 'daisy:table-create-failed', { values: { ...context.editing.draft }, errors: validationErrors });
    renderTable(context);
    return null;
  }

  const values = { ...context.editing.draft };
  const create = context.config.editable.create;
  context.editing.saving = true;
  renderTable(context);

  try {
    let body = { row: values };

    if (create.strategy === 'remote') {
      const response = await mutationRequest(create.endpoint, create.method, { values });
      body = await response.json().catch(() => ({}));

      if (!response.ok) {
        throw Object.assign(new Error(body.message || `HTTP ${response.status}`), { errors: body.errors });
      }
    }

    const row = isPlainObject(body.row) ? body.row : (isPlainObject(body.data) ? body.data : values);
    applyCreatedRow(context, row);
    context.creating = null;
    context.editing = null;
    dispatchTableEditEvent(context, 'daisy:table-create-committed', { values, row, response: body });

    if (context.config.mode === 'server' && create.strategy === 'remote') {
      await refreshTable(context);
    } else {
      renderTable(context);
    }

    return body;
  } catch (error) {
    if (context.editing) {
      context.editing.saving = false;
      context.editing.errors = normalizeMutationErrors(error, context.config.editable.columns[0] || '_row');
    }

    dispatchTableEditEvent(context, 'daisy:table-create-failed', { values, errors: context.editing?.errors || {}, error });
    renderTable(context);
    return null;
  }
}

async function commitEdit(context) {
  if (!context.editing || context.editing.saving) {
    return null;
  }

  if (context.editing.type === 'create') {
    return commitCreate(context);
  }

  const validationErrors = validateEdit(context);

  if (Object.keys(validationErrors).length > 0) {
    context.editing.errors = validationErrors;
    dispatchTableEditEvent(context, 'daisy:table-edit-failed', {
      rowId: context.editing.rowId,
      column: context.editing.columnId,
      row: getVisibleRowById(context, context.editing.rowId)?.original ?? context.editing.original,
      dirty: { ...context.editing.dirty },
      errors: validationErrors,
      error: new Error('Validation failed.'),
    });
    renderTable(context);

    return null;
  }

  const dirty = Object.fromEntries(
    Object.entries(context.editing.dirty).filter(([, value]) => value === true)
  );

  if (Object.keys(dirty).length === 0) {
    cancelEdit(context);
    return null;
  }

  const rowId = context.editing.rowId;
  const column = context.editing.columnId;
  const row = getVisibleRowById(context, rowId)?.original ?? context.editing.original;
  const payload = {
    rowId,
    column,
    value: context.editing.draft[column],
    row,
    dirty: Object.fromEntries(Object.keys(dirty).map((key) => [key, context.editing.draft[key]])),
  };
  context.editing.saving = true;
  renderTable(context);

  try {
    let body = { row: payload.dirty };

    if (context.config.editable.update.strategy === 'remote') {
      const response = await mutationRequest(context.config.editable.update.endpoint, context.config.editable.update.method, payload);
      body = await response.json().catch(() => ({}));

      if (!response.ok) {
        const errors = isPlainObject(body.errors) ? body.errors : {};

        throw Object.assign(new Error(body.message || `HTTP ${response.status}`), { errors });
      }
    }

    const nextRow = isPlainObject(body.row)
      ? body.row
      : (isPlainObject(body.data) ? body.data : payload.dirty);

    applyEditedRow(context, rowId, nextRow);
    dispatchTableEditEvent(context, 'daisy:table-edit-committed', {
      ...payload,
      response: body,
      row: {
        ...row,
        ...nextRow,
      },
    });
    context.editing = null;

    if (context.config.mode === 'server' && context.config.editable.update.strategy === 'remote') {
      await refreshTable(context);
    } else {
      renderTable(context);
    }

    return body;
  } catch (error) {
    if (context.editing) {
      context.editing.saving = false;
      context.editing.errors = normalizeMutationErrors(error, column);
    }

    dispatchTableEditEvent(context, 'daisy:table-edit-failed', {
      ...payload,
      errors: context.editing?.errors || {},
      error,
    });
    renderTable(context);

    return null;
  }
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

function getSelectionSummary(context, visibleRows = []) {
  const selection = normalizeSelectionState(context.state.selection);
  const visibleSelectedCount = visibleRows.filter((row) => isRowSelected(context.state, context.config, row)).length;
  const selectedCount = selection.allFilteredSelected
    ? Math.max(0, context.rowCount - selection.excludedIds.length)
    : selection.selectedIds.length;

  return {
    selectedCount,
    visibleSelectedCount,
    offPageCount: Math.max(0, selectedCount - visibleSelectedCount),
    excludedCount: selection.excludedIds.length,
    allFilteredSelected: selection.allFilteredSelected,
    selectionScope: selection.selectionScope,
    filterSignature: selection.filterSignature,
  };
}

function formatLabel(template, count) {
  return String(template || ':count selected').replace(':count', String(count));
}

function getSelectionFeedbackNote(summary, labels = {}) {
  if (summary.allFilteredSelected && summary.excludedCount === 0) {
    return labels.allFilteredRowsSelected || '';
  }

  if (summary.offPageCount > 0) {
    return formatLabel(labels.selectedOffPageCount, summary.offPageCount);
  }

  return '';
}

function setSelectionDataset(root, summary) {
  root.dataset.tableSelectionSelectedCount = String(summary.selectedCount);
  root.dataset.tableSelectionVisibleSelectedCount = String(summary.visibleSelectedCount);
  root.dataset.tableSelectionOffPageCount = String(summary.offPageCount);
  root.dataset.tableSelectionExcludedCount = String(summary.excludedCount);
  root.dataset.tableSelectionAllFiltered = summary.allFilteredSelected ? '1' : '0';
  root.dataset.tableSelectionHasSelection = summary.selectedCount > 0 ? '1' : '0';
}

function setBulkActionsDisabled(container, disabled) {
  if (!(container instanceof HTMLElement)) {
    return;
  }

  container.querySelectorAll('[data-table-bulk-action]').forEach((action) => {
    if (action instanceof HTMLButtonElement || action instanceof HTMLInputElement || action instanceof HTMLSelectElement) {
      action.disabled = disabled;
    }

    action.setAttribute('aria-disabled', disabled ? 'true' : 'false');
  });
}

function updateSelectionFeedback(context, visibleRows = []) {
  const bar = context.root.querySelector('[data-table-selection-feedback]');
  const summaryElement = context.root.querySelector('[data-table-selection-summary]');
  const noteElement = context.root.querySelector('[data-table-selection-note]');
  const bulkActions = context.root.querySelector('[data-table-bulk-actions]');
  const selectFilteredButton = context.root.querySelector('[data-table-select-filtered]');

  if (!context.config.selection.enabled || !(bar instanceof HTMLElement)) {
    return;
  }

  const summary = getSelectionSummary(context, visibleRows);
  summary.visibleRowsCount = visibleRows.length;
  setSelectionDataset(context.root, summary);

  if (summaryElement instanceof HTMLElement) {
    const usesPageScopedPrimary = summary.offPageCount > 0 && !(summary.allFilteredSelected && summary.excludedCount === 0);
    const primaryCount = usesPageScopedPrimary ? summary.visibleSelectedCount : summary.selectedCount;
    const primaryLabel = usesPageScopedPrimary
      ? context.config.labels.selectedOnPageCount
      : context.config.labels.selectedCount;

    summaryElement.textContent = formatLabel(primaryLabel, primaryCount);
  }

  if (noteElement instanceof HTMLElement) {
    if (context.selectionNotice) {
      noteElement.textContent = context.selectionNotice;
    } else {
      noteElement.textContent = getSelectionFeedbackNote(summary, context.config.labels);
    }
  }

  setBulkActionsDisabled(bulkActions, summary.selectedCount === 0);

  if (selectFilteredButton instanceof HTMLButtonElement) {
    selectFilteredButton.disabled = context.loading
      || context.rowCount === 0
      || visibleRows.length === 0
      || (summary.allFilteredSelected && summary.excludedCount === 0);
  }

  const clearSelectionButton = context.root.querySelector('[data-table-clear-selection]');

  if (clearSelectionButton instanceof HTMLButtonElement) {
    clearSelectionButton.disabled = summary.selectedCount === 0;
  }
}

function updateSelectionControls(context, visibleRows = []) {
  if (!context.config.selection.enabled) {
    return;
  }

  const selectPageInput = context.root.querySelector('[data-table-select-page]');
  const visibleSelectableRows = visibleRows.filter((row) => getRowSelectionId(context.config, row) !== null);
  const visibleSelectedCount = visibleSelectableRows.filter((row) => isRowSelected(context.state, context.config, row)).length;

  if (selectPageInput instanceof HTMLInputElement) {
    selectPageInput.checked = visibleSelectableRows.length > 0 && visibleSelectedCount === visibleSelectableRows.length;
    selectPageInput.indeterminate = visibleSelectedCount > 0 && visibleSelectedCount < visibleSelectableRows.length;
    selectPageInput.disabled = visibleSelectableRows.length === 0 || context.loading || context.config.selection.readOnly;
  }

  context.root.querySelectorAll('[data-table-row-select]').forEach((input) => {
    if (!(input instanceof HTMLInputElement)) {
      return;
    }

    const rowId = input.dataset.tableRowSelect || null;

    input.disabled = rowId === null || context.loading || context.config.selection.readOnly;
    input.checked = rowId !== null && (
      context.state.selection.allFilteredSelected
        ? !context.state.selection.excludedIds.includes(rowId)
        : context.state.selection.selectedIds.includes(rowId)
    );
  });

  updateSelectionFeedback(context, visibleRows);
}

function dispatchTableSelectionChanged(context, visibleRows = []) {
  if (!context.config.selection.enabled) {
    return;
  }

  context.root.dispatchEvent(new CustomEvent('daisy:table-selection-changed', {
    bubbles: true,
    detail: buildSelectionDetail(context, visibleRows),
  }));
}

function dispatchTableRendered(context) {
  context.root.dispatchEvent(new CustomEvent('daisy:table-rendered', {
    bubbles: true,
    detail: {
      rows: context.visibleRows,
      rowCount: context.rowCount,
      pageCount: context.pageCount,
      state: cloneState(context.state),
      meta: context.meta || {},
      table: context.table,
    },
  }));
}

function dispatchTableDataChanged(context, operation, rowIds = []) {
  context.root.dispatchEvent(new CustomEvent('daisy:table-data-changed', {
    bubbles: true,
    detail: {
      operation,
      rowIds,
      rows: context.visibleRows,
      rowCount: context.rowCount,
      pageCount: context.pageCount,
      state: cloneState(context.state),
      table: context.table,
    },
  }));
}

function dispatchTableEditEvent(context, name, detail = {}) {
  context.root.dispatchEvent(new CustomEvent(name, {
    bubbles: true,
    detail,
  }));
}

function clearSelection(context, notice = '') {
  context.state.selection = resetSelectionState(context.state.selection);
  context.state.rowSelection = {};
  context.selectionNotice = notice;
}

function syncSelectionFromRowSelection(context) {
  if (!context.config.selection.enabled || context.state.selection?.allFilteredSelected === true) {
    return;
  }

  context.state.selection = {
    ...normalizeSelectionState(context.state.selection),
    selectedIds: Object.keys(normalizeRowSelection(context.state.rowSelection)),
    excludedIds: [],
    allFilteredSelected: false,
    selectionScope: 'page',
    filterSignature: '',
  };
}

function syncRowSelectionState(context) {
  if (!context.config.selection.enabled) {
    context.state.rowSelection = {};
    return;
  }

  context.state.rowSelection = rowSelectionFromSelection(context.state.selection);
}

function requireClientDataContext(context) {
  if (context?.config.mode !== 'client' || !getStableRowKey(context.config)) {
    throw new Error('The Daisy Table data API only supports client mode with a non-empty rowKey.');
  }
}

function requireRowCollection(rows, method) {
  if (!Array.isArray(rows) || !rows.every((row) => isPlainObject(row))) {
    throw new TypeError(`DaisyTable.${method} expects an array of row objects.`);
  }
}

function collectClientRowIds(context, rows = context.config.rows) {
  const rowIds = new Set();
  const subRowsKey = context.config.subRowsKey;

  const collect = (items) => {
    items.forEach((row) => {
      const rowId = getRowSelectionId(context.config, row);

      if (rowId !== null) {
        rowIds.add(rowId);
      }

      const subRows = subRowsKey ? row?.[subRowsKey] : null;

      if (Array.isArray(subRows)) {
        collect(subRows);
      }
    });
  };

  collect(rows);

  return rowIds;
}

function reconcileClientDataState(context) {
  const rowIds = collectClientRowIds(context);
  const selection = normalizeSelectionState(context.state.selection);

  context.state.selection = {
    ...selection,
    selectedIds: selection.selectedIds.filter((rowId) => rowIds.has(rowId)),
    excludedIds: selection.excludedIds.filter((rowId) => rowIds.has(rowId)),
  };

  if (context.state.expanded !== true) {
    context.state.expanded = Object.fromEntries(
      Object.entries(normalizeExpanded(context.state.expanded))
        .filter(([rowId]) => rowIds.has(rowId))
    );
  }

  if (context.editing?.type === 'update' && !rowIds.has(String(context.editing.rowId))) {
    context.editing = null;
  }

  syncRowSelectionState(context);
}

function commitClientDataMutation(context, operation, rowIds = []) {
  context.loading = false;
  context.error = '';
  reconcileClientDataState(context);
  renderTable(context);
  persistState(context);
  dispatchTableDataChanged(context, operation, rowIds);

  return context;
}

function setClientRows(context, rows) {
  requireClientDataContext(context);
  requireRowCollection(rows, 'setRows');

  context.config.rows = [...rows];

  return commitClientDataMutation(context, 'setRows', [...collectClientRowIds(context)]);
}

function upsertClientRows(context, rows) {
  requireClientDataContext(context);
  requireRowCollection(rows, 'upsertRows');

  const rowKey = getStableRowKey(context.config);
  const rowsById = new Map();

  rows.forEach((row) => {
    const rowId = getRowSelectionId(context.config, row);

    if (rowId === null) {
      throw new TypeError(`DaisyTable.upsertRows requires every row to include a non-empty ${rowKey}.`);
    }

    rowsById.set(rowId, row);
  });

  const existingRowIds = new Set();
  const nextRows = context.config.rows.map((row) => {
    const rowId = getRowSelectionId(context.config, row);

    if (rowId === null || !rowsById.has(rowId)) {
      return row;
    }

    existingRowIds.add(rowId);

    return rowsById.get(rowId);
  });

  rowsById.forEach((row, rowId) => {
    if (!existingRowIds.has(rowId)) {
      nextRows.push(row);
    }
  });

  context.config.rows = nextRows;

  return commitClientDataMutation(context, 'upsertRows', [...rowsById.keys()]);
}

function removeClientRows(context, rowIds) {
  requireClientDataContext(context);

  if (!Array.isArray(rowIds)) {
    throw new TypeError('DaisyTable.removeRows expects an array of row identifiers.');
  }

  const normalizedRowIds = uniqueStringArray(rowIds);
  const removedRows = new Set(normalizedRowIds);

  context.config.rows = context.config.rows.filter((row) => {
    const rowId = getRowSelectionId(context.config, row);

    return rowId === null || !removedRows.has(rowId);
  });

  return commitClientDataMutation(context, 'removeRows', normalizedRowIds);
}

function setClientLoading(context, loading) {
  requireClientDataContext(context);

  context.loading = loading === true;
  context.error = '';
  renderTable(context);

  return context;
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

    if (input instanceof HTMLInputElement && type === 'date-range') {
      const bound = input.dataset.tableFilterBound;
      const value = isPlainObject(currentFilter.value) ? currentFilter.value : {};

      input.value = String(value[bound] ?? '');
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
      if (filter.type === 'date-range') {
        const from = readSpatieFilterParam(params, filter.filterKeyFrom || `${filter.filterKey}_from`);
        const to = readSpatieFilterParam(params, filter.filterKeyTo || `${filter.filterKey}_to`);

        if (from == null && to == null) {
          return null;
        }

        return {
          id: filter.id,
          type: filter.type,
          value: {
            from: from || '',
            to: to || '',
          },
        };
      }

      const value = readSpatieFilterParam(params, filter.filterKey);

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
  const columnOrder = params.get('columnOrder');
  const columnPinning = params.get('columnPinning');
  const columnSizing = params.get('columnSizing');
  const expanded = params.get('expanded');
  const rowSelection = params.get('rowSelection');

  return {
    sorting: parseJsonParam(sorting, []),
    pagination: {
      pageIndex: Number.parseInt(params.get('pageIndex'), 10) || 0,
      pageSize: Number.parseInt(params.get('pageSize'), 10) || undefined,
    },
    globalFilter: params.get('globalFilter') || '',
    columnFilters: parseJsonParam(columnFilters, []),
    columnVisibility: parseJsonParam(columnVisibility, {}),
    columnOrder: parseJsonParam(columnOrder, []),
    columnPinning: parseJsonParam(columnPinning, {}),
    columnSizing: parseJsonParam(columnSizing, {}),
    expanded: parseJsonParam(expanded, {}),
    rowSelection: parseJsonParam(rowSelection, {}),
  };
}

function readSpatieFilterParam(params, filterKey) {
  const directValues = params.getAll(`filter[${filterKey}]`);
  const arrayValues = params.getAll(`filter[${filterKey}][]`);
  const values = [...directValues, ...arrayValues]
    .map((value) => String(value ?? '').trim())
    .filter((value) => value !== '');

  if (values.length === 0) {
    return null;
  }

  return values.join(',');
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

  if (Array.isArray(overrideState.columnOrder)) {
    nextState.columnOrder = normalizeColumnOrder(overrideState.columnOrder, config.columns);
  }

  if (isPlainObject(overrideState.columnPinning)) {
    nextState.columnPinning = normalizeColumnPinning(overrideState.columnPinning, config.columns);
  }

  if (isPlainObject(overrideState.columnSizing)) {
    nextState.columnSizing = normalizeColumnSizing(overrideState.columnSizing, config.columns);
  }

  if (overrideState.expanded === true || isPlainObject(overrideState.expanded)) {
    nextState.expanded = normalizeExpanded(overrideState.expanded);
  }

  if (isPlainObject(overrideState.rowSelection)) {
    nextState.rowSelection = normalizeRowSelection(overrideState.rowSelection);
  }

  if (config.selection.enabled && isPlainObject(overrideState.selection)) {
    nextState.selection = normalizeSelectionState(overrideState.selection);
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

  if (type === 'date-range') {
    const root = input.closest('[data-daisy-table="1"]');
    const fromInput = root?.querySelector(`[data-table-filter="${filterId}"][data-table-filter-bound="from"]`);
    const toInput = root?.querySelector(`[data-table-filter="${filterId}"][data-table-filter-bound="to"]`);

    return {
      id: filterId,
      type,
      value: {
        from: fromInput instanceof HTMLInputElement ? String(fromInput.value ?? '') : '',
        to: toInput instanceof HTMLInputElement ? String(toInput.value ?? '') : '',
      },
    };
  }

  return {
    id: filterId,
    type,
    value: String(input.value ?? ''),
  };
}

function resolveFilterInputState(context, input) {
  const filterId = input.dataset.tableFilter;
  const type = input.dataset.tableFilterType || 'text';

  if (type !== 'text') {
    return createFilterState(filterId, type, input);
  }

  const activeFilter = context.state.columnFilters.find((filter) => filter.id === filterId);
  const value = resolveSearchInputValue(input.value, activeFilter?.value ?? '', context.config.minSearchChars);

  if (value === null) {
    return null;
  }

  return {
    id: filterId,
    type,
    value,
  };
}

function renderTable(context) {
  const sourceRows = context.config.mode === 'client' ? context.config.rows : context.rows;
  const tableRows = getTableRows(context);
  const rowCount = context.config.mode === 'client' ? sourceRows.length : context.rowCount;
  let pageCount = context.config.mode === 'client'
    ? Math.max(1, Math.ceil(Math.max(1, rowCount) / context.state.pagination.pageSize))
    : Math.max(1, context.pageCount);

  context.rowCount = rowCount;
  context.pageCount = pageCount;
  syncRowSelectionState(context);

  if (context.state.pagination.pageIndex > pageCount - 1) {
    context.state.pagination.pageIndex = Math.max(0, pageCount - 1);
  }

  context.table = createTableModel(context, tableRows, rowCount, pageCount);

  if (context.config.mode === 'client') {
    const filteredRowCount = context.table.getFilteredRowModel().rows
      .filter((row) => row.original?.__daisyTableDraft !== true)
      .length;

    pageCount = Math.max(1, Math.ceil(Math.max(1, filteredRowCount) / context.state.pagination.pageSize));
    context.rowCount = filteredRowCount;
    context.pageCount = pageCount;

    if (context.state.pagination.pageIndex > pageCount - 1) {
      context.state.pagination.pageIndex = Math.max(0, pageCount - 1);
      context.table = createTableModel(context, tableRows, filteredRowCount, pageCount);
    }
  }

  const rowModel = context.table.getRowModel().rows;

  context.visibleRows = rowModel;
  renderColgroup(context);
  renderHeader(context);
  renderColumnVisibility(context);
  syncControls(context);
  renderBody(context, rowModel);
  hydrateCustomEditors(context);
  renderFooter(context, rowModel.length);
  updateSelectionControls(context, rowModel);
  dispatchTableRendered(context);
}

function hydrateCustomEditors(context) {
  context.root.querySelectorAll('[data-table-editor-input]').forEach((input) => {
    if (!(input instanceof HTMLInputElement || input instanceof HTMLSelectElement || input instanceof HTMLTextAreaElement)) {
      return;
    }

    const columnId = getEditorColumnId(input);
    const value = context.editing?.draft?.[columnId];

    if (input instanceof HTMLInputElement && input.type === 'checkbox') {
      input.checked = value === true || value === 'true' || value === 1 || value === '1';
    } else if (value !== undefined) {
      input.value = String(value ?? '');
    }

    input.disabled = context.editing?.saving === true;
  });

  if (context.editing?.type === 'create') {
    context.root.dispatchEvent(new CustomEvent('daisy:table-editor-mounted', {
      bubbles: true,
      detail: { editing: context.editing, table: context.table },
    }));
  }
}

function focusEditor(context) {
  setTimeout(() => {
    const input = context.root.querySelector('[data-table-edit-input], [data-table-editor-input]');

    if (input instanceof HTMLElement) {
      input.focus();
    }
  });
}

async function fetchServerRows(context) {
  const request = buildServerRequest(context.config, context.state);
  const abortController = new AbortController();

  if (context.abortController) {
    context.abortController.abort();
  }

  context.abortController = abortController;
  request.requestInit.signal = abortController.signal;

  try {
    const response = await fetch(request.url, request.requestInit);

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    return request.responseNormalizer(await response.json());
  } finally {
    if (context.abortController === abortController) {
      context.abortController = null;
    }
  }
}

async function refreshTable(context) {
  const refreshId = ++context.refreshId;

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

    if (refreshId !== context.refreshId) {
      return;
    }

    context.rows = response.rows;
    context.rowCount = response.rowCount;
    context.pageCount = response.pageCount;
    context.meta = response.meta || {};
    context.state.pagination.pageIndex = response.state.pageIndex;
    context.state.pagination.pageSize = response.state.pageSize;
    context.loading = false;
    persistState(context);
    renderTable(context);
  } catch (error) {
    if (error?.name === 'AbortError' || refreshId !== context.refreshId) {
      return;
    }

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
  const eventController = typeof AbortController !== 'undefined' ? new AbortController() : null;
  const listenerOptions = eventController ? { signal: eventController.signal } : undefined;
  let searchTimeout;
  let filterTimeout;

  context.eventController = eventController;

  context.root.addEventListener('click', (event) => {
    const sortButton = event.target instanceof Element ? event.target.closest('[data-table-sort]') : null;
    const selectFilteredButton = event.target instanceof Element ? event.target.closest('[data-table-select-filtered]') : null;
    const clearSelectionButton = event.target instanceof Element ? event.target.closest('[data-table-clear-selection]') : null;
    const bulkActionButton = event.target instanceof Element ? event.target.closest('[data-table-bulk-action]') : null;
    const detailButton = event.target instanceof Element ? event.target.closest('[data-table-row-detail]') : null;
    const expandButton = event.target instanceof Element ? event.target.closest('[data-table-row-expand]') : null;
    const detailCloseButton = event.target instanceof Element ? event.target.closest('[data-table-detail-close]') : null;
    const editCell = event.target instanceof Element ? event.target.closest('[data-table-edit-cell]') : null;
    const editSaveButton = event.target instanceof Element ? event.target.closest('[data-table-edit-save]') : null;
    const editCancelButton = event.target instanceof Element ? event.target.closest('[data-table-edit-cancel]') : null;
    const createButton = event.target instanceof Element ? event.target.closest('[data-table-create]') : null;

    if (createButton instanceof HTMLElement) {
      startCreate(context);
      return;
    }

    if (editSaveButton instanceof HTMLElement) {
      void commitEdit(context);
      return;
    }

    if (editCancelButton instanceof HTMLElement) {
      cancelEdit(context);
      return;
    }

    if (editCell instanceof HTMLElement && !(event.target instanceof HTMLInputElement)) {
      startCellEdit(context, editCell.dataset.tableRowId, editCell.dataset.tableColumnId);
      return;
    }

    if (detailCloseButton instanceof HTMLElement) {
      const dialog = detailCloseButton.closest('dialog');

      if (dialog instanceof HTMLDialogElement) {
        dialog.close();
      }

      return;
    }

    if (detailButton instanceof HTMLElement) {
      const rowId = detailButton.dataset.tableRowDetail;
      const row = getVisibleRowById(context, rowId);

      if (!row) {
        return;
      }

      row.toggleExpanded?.();

      if (context.config.rowDetail.mode === 'modal') {
        openDetailModal(context, rowId);
      }

      persistState(context);
      renderTable(context);
      return;
    }

    if (expandButton instanceof HTMLElement) {
      const rowId = expandButton.dataset.tableRowExpand;
      const row = getVisibleRowById(context, rowId);

      if (!row) {
        return;
      }

      row.toggleExpanded?.();
      persistState(context);
      renderTable(context);
      return;
    }

    if (selectFilteredButton instanceof HTMLElement) {
      if (!context.config.selection.selectFiltered) {
        return;
      }

      if (selectFilteredButton instanceof HTMLButtonElement && selectFilteredButton.disabled) {
        return;
      }

      context.selectionNotice = '';
      selectAllFilteredRows(context.state);
      persistState(context);
      updateSelectionControls(context, context.visibleRows);
      dispatchTableSelectionChanged(context, context.visibleRows);
      return;
    }

    if (clearSelectionButton instanceof HTMLElement) {
      if (clearSelectionButton instanceof HTMLButtonElement && clearSelectionButton.disabled) {
        return;
      }

      clearSelection(context);
      persistState(context);
      updateSelectionControls(context, context.visibleRows);
      dispatchTableSelectionChanged(context, context.visibleRows);
      return;
    }

    if (bulkActionButton instanceof HTMLElement) {
      if (
        bulkActionButton.getAttribute('aria-disabled') === 'true'
        || (bulkActionButton instanceof HTMLButtonElement && bulkActionButton.disabled)
      ) {
        return;
      }

      context.root.dispatchEvent(new CustomEvent('daisy:table-bulk-action', {
        bubbles: true,
        detail: {
          action: bulkActionButton.dataset.tableBulkAction,
          payload: buildSelectionActionPayload(context.config, context.state),
        },
      }));
      return;
    }

    if (!(sortButton instanceof HTMLElement)) {
      return;
    }

    const column = context.table?.getColumn(sortButton.dataset.tableSort);

    if (column?.getCanSort?.()) {
      column.toggleSorting();
    } else {
      context.state.sorting = toggleSorting(context.state, sortButton.dataset.tableSort);
    }

    context.table?.setPageIndex?.(0);
    void refreshTable(context);
  }, listenerOptions);

  context.root.addEventListener('change', (event) => {
    const target = event.target;

    if ((target instanceof HTMLInputElement || target instanceof HTMLSelectElement || target instanceof HTMLTextAreaElement)
      && target.matches('[data-table-edit-input], [data-table-editor-input]')) {
      updateEditDraft(context, target);
      return;
    }

    if (target instanceof HTMLInputElement && target.matches('[data-table-select-page]')) {
      context.selectionNotice = '';
      if (context.state.selection.allFilteredSelected) {
        toggleVisibleRowsSelection(context.state, context.config, context.visibleRows, target.checked);
      } else {
        context.table?.toggleAllPageRowsSelected?.(target.checked);
      }
      persistState(context);
      updateSelectionControls(context, context.visibleRows);
      dispatchTableSelectionChanged(context, context.visibleRows);
      return;
    }

    if (target instanceof HTMLInputElement && target.matches('[data-table-row-select]')) {
      if (context.config.selection.readOnly) {
        return;
      }

      context.selectionNotice = '';
      if (context.state.selection.allFilteredSelected) {
        toggleRowSelection(context.state, context.config, target.dataset.tableRowSelect || null, target.checked);
      } else {
        context.table?.getRow(target.dataset.tableRowSelect, true)?.toggleSelected?.(target.checked);
      }
      persistState(context);
      updateSelectionControls(context, context.visibleRows);
      dispatchTableSelectionChanged(context, context.visibleRows);
      return;
    }

    if (target instanceof HTMLInputElement && target.matches('[data-table-column-toggle]')) {
      context.table?.getColumn(target.dataset.tableColumnToggle)?.toggleVisibility?.(target.checked);
      context.table?.setPageIndex?.(0);
      void refreshTable(context);
      return;
    }

    if ((target instanceof HTMLInputElement || target instanceof HTMLSelectElement) && target.matches('[data-table-filter]')) {
      if (target.dataset.tableFilterType === 'text') {
        return;
      }

      const nextFilter = createFilterState(target.dataset.tableFilter, target.dataset.tableFilterType || 'text', target);

      context.table?.getColumn(nextFilter.id)?.setFilterValue(nextFilter);
      context.table?.setPageIndex?.(0);
      clearSelection(context, context.config.labels.selectionResetAfterFilter || '');
      void refreshTable(context);
    }
  }, listenerOptions);

  context.root.addEventListener('input', (event) => {
    const target = event.target;

    if ((target instanceof HTMLInputElement || target instanceof HTMLSelectElement || target instanceof HTMLTextAreaElement)
      && target.matches('[data-table-edit-input], [data-table-editor-input]')) {
      updateEditDraft(context, target);
      return;
    }

    if (!((target instanceof HTMLInputElement || target instanceof HTMLSelectElement) && target.matches('[data-table-filter]'))) {
      return;
    }

    if ((target.dataset.tableFilterType || 'text') !== 'text') {
      return;
    }

    const nextFilter = resolveFilterInputState(context, target);

    clearTimeout(filterTimeout);

    if (nextFilter === null) {
      return;
    }

    context.table?.getColumn(nextFilter.id)?.setFilterValue(nextFilter);
    context.table?.setPageIndex?.(0);
    clearSelection(context, context.config.labels.selectionResetAfterFilter || '');
    filterTimeout = setTimeout(() => {
      void refreshTable(context);
    }, context.config.filterDebounceMs);
  }, listenerOptions);

  context.root.addEventListener('keydown', (event) => {
    const target = event.target;

    if (!((target instanceof HTMLInputElement || target instanceof HTMLSelectElement || target instanceof HTMLTextAreaElement)
      && target.matches('[data-table-edit-input], [data-table-editor-input]'))) {
      return;
    }

    if (event.key === 'Enter') {
      event.preventDefault();
      updateEditDraft(context, target);
      void commitEdit(context);
      return;
    }

    if (event.key === 'Escape') {
      event.preventDefault();
      cancelEdit(context);
    }
  }, listenerOptions);

  context.root.addEventListener('mousedown', (event) => {
    const resizeHandle = event.target instanceof Element ? event.target.closest('[data-table-resize]') : null;

    if (!(resizeHandle instanceof HTMLElement)) {
      return;
    }

    const header = context.table?.getFlatHeaders?.().find((candidate) => candidate.id === resizeHandle.dataset.tableResize);

    header?.getResizeHandler?.()(event);
  }, listenerOptions);

  context.root.addEventListener('touchstart', (event) => {
    const resizeHandle = event.target instanceof Element ? event.target.closest('[data-table-resize]') : null;

    if (!(resizeHandle instanceof HTMLElement)) {
      return;
    }

    const header = context.table?.getFlatHeaders?.().find((candidate) => candidate.id === resizeHandle.dataset.tableResize);

    header?.getResizeHandler?.()(event);
  }, listenerOptions);

  if (searchInput instanceof HTMLInputElement) {
    searchInput.addEventListener('input', () => {
      const nextValue = resolveSearchInputValue(searchInput.value, context.state.globalFilter, context.config.minSearchChars);

      clearTimeout(searchTimeout);

      if (nextValue === null || nextValue === context.state.globalFilter) {
        return;
      }

      searchTimeout = setTimeout(() => {
        context.table?.setGlobalFilter?.(nextValue);
        context.table?.setPageIndex?.(0);
        clearSelection(context, context.config.labels.selectionResetAfterFilter || '');
        void refreshTable(context);
      }, context.config.searchDebounceMs);
    }, listenerOptions);
  }

  if (pageSize instanceof HTMLSelectElement) {
    pageSize.addEventListener('change', () => {
      context.table?.setPageSize?.(Number.parseInt(pageSize.value, 10) || context.state.pagination.pageSize);
      context.table?.setPageIndex?.(0);
      void refreshTable(context);
    }, listenerOptions);
  }

  if (previousButton instanceof HTMLButtonElement) {
    previousButton.addEventListener('click', () => {
      context.table?.previousPage?.();
      void refreshTable(context);
    }, listenerOptions);
  }

  if (nextButton instanceof HTMLButtonElement) {
    nextButton.addEventListener('click', () => {
      context.table?.nextPage?.();
      void refreshTable(context);
    }, listenerOptions);
  }
}

function normalizeExternalFilters(filters, definitions = []) {
  if (Array.isArray(filters)) {
    return normalizeColumnFilters(filters, definitions);
  }

  if (!isPlainObject(filters)) {
    return null;
  }

  return normalizeColumnFilters(
    Object.entries(filters).map(([id, value]) => ({ id, value })),
    definitions
  );
}

function applyExternalRefreshState(context, options = {}) {
  if (!isPlainObject(options)) {
    return;
  }

  const nextFilters = normalizeExternalFilters(options.filters, context.config.filters);

  if (nextFilters !== null) {
    context.state.columnFilters = nextFilters;
    clearSelection(context, context.config.labels.selectionResetAfterFilter || '');
  }

  if (Array.isArray(options.sorting)) {
    context.state.sorting = normalizeSorting(options.sorting, context.config.columns);
  }

  if (Array.isArray(options.columnOrder)) {
    context.state.columnOrder = normalizeColumnOrder(options.columnOrder, context.config.columns);
  }

  if (isPlainObject(options.columnPinning)) {
    context.state.columnPinning = normalizeColumnPinning(options.columnPinning, context.config.columns);
  }

  if (isPlainObject(options.columnSizing)) {
    context.state.columnSizing = normalizeColumnSizing(options.columnSizing, context.config.columns);
  }

  if (options.expanded === true || isPlainObject(options.expanded)) {
    context.state.expanded = normalizeExpanded(options.expanded);
  }

  if (isPlainObject(options.pagination)) {
    context.state.pagination = {
      pageIndex: Math.max(0, Number.parseInt(options.pagination.pageIndex ?? context.state.pagination.pageIndex, 10) || 0),
      pageSize: context.config.pageSizeOptions.includes(Number.parseInt(options.pagination.pageSize, 10))
        ? Number.parseInt(options.pagination.pageSize, 10)
        : context.state.pagination.pageSize,
    };
  }
}

function destroyTable(root) {
  const container = root?.matches?.('[data-daisy-table="1"]')
    ? root
    : root?.querySelector?.('[data-daisy-table="1"]');

  if (!(container instanceof HTMLElement) || !container.__daisyTableContext) {
    return false;
  }

  const context = container.__daisyTableContext;

  if (context.abortController) {
    context.abortController.abort();
  }

  if (context.eventController) {
    context.eventController.abort();
  }

  delete container.__daisyTableInit;
  delete container.__daisyTableContext;

  return true;
}

async function initTable(root) {
  const container = root?.matches?.('[data-daisy-table="1"]')
    ? root
    : root?.querySelector?.('[data-daisy-table="1"]');

  if (!(container instanceof HTMLElement)) {
    return null;
  }

  if (container.__daisyTableInit && container.__daisyTableContext) {
    return container.__daisyTableContext;
  }

  const config = normalizeConfig(parseConfig(container));
  const context = {
    root: container,
    config,
    state: cloneState(config.initialState),
    rows: config.mode === 'client' ? config.rows : [],
    rowCount: config.mode === 'client' ? config.rows.length : 0,
    pageCount: 1,
    meta: {},
    loading: config.mode === 'server',
    error: '',
    table: null,
    visibleRows: [],
    abortController: null,
    eventController: null,
    refreshId: 0,
    selectionNotice: '',
    creating: null,
    editing: null,
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

function reinitMorphTables(scope = null) {
  if (typeof document === 'undefined') {
    return;
  }

  const root = typeof Element !== 'undefined' && scope instanceof Element ? scope : document;
  const tables = root.matches?.('[data-daisy-table="1"]')
    ? [root]
    : Array.from(root.querySelectorAll?.('[data-daisy-table="1"]') ?? []);

  tables.forEach((table) => {
    const raw = parseConfig(table);

    if (raw.livewireMode === 'morph') {
      destroyTable(table);
    }

    void initTable(table);
  });
}

function resolveTableRoot(idOrRoot) {
  if (typeof HTMLElement !== 'undefined' && idOrRoot instanceof HTMLElement) {
    return idOrRoot.matches('[data-daisy-table="1"]')
      ? idOrRoot
      : idOrRoot.closest('[data-daisy-table="1"]')
        ?? idOrRoot.querySelector('[data-daisy-table="1"]');
  }

  if (typeof document === 'undefined') {
    return null;
  }

  if (typeof idOrRoot === 'string' && idOrRoot !== '') {
    const escaped = typeof CSS !== 'undefined' && typeof CSS.escape === 'function'
      ? CSS.escape(idOrRoot)
      : idOrRoot.replaceAll('"', '\\"');

    return document.getElementById(idOrRoot)
      ?? document.querySelector(`[data-daisy-table-id="${escaped}"]`);
  }

  return null;
}

function tableApi(idOrRoot) {
  const root = resolveTableRoot(idOrRoot);

  return {
    async refresh(options = {}) {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      applyExternalRefreshState(context, options);
      await refreshTable(context);

      return context;
    },
    async reinit() {
      destroyTable(root);

      return initTable(root);
    },
    async setLoading(loading = true) {
      const context = await initTable(root);

      return setClientLoading(context, loading);
    },
    async setRows(rows = []) {
      const context = await initTable(root);

      return setClientRows(context, rows);
    },
    async upsertRows(rows = []) {
      const context = await initTable(root);

      return upsertClientRows(context, rows);
    },
    async removeRows(rowIds = []) {
      const context = await initTable(root);

      return removeClientRows(context, rowIds);
    },
    selection() {
      const context = root?.__daisyTableContext ?? null;

      return context ? buildSelectionDetail(context, context.visibleRows) : null;
    },
    selectionPayload() {
      const context = root?.__daisyTableContext ?? null;

      return context ? buildSelectionActionPayload(context.config, context.state) : null;
    },
    selectAllFiltered() {
      const context = root?.__daisyTableContext ?? null;

      if (!context || !context.config.selection.enabled || !context.config.selection.selectFiltered) {
        return null;
      }

      context.selectionNotice = '';
      selectAllFilteredRows(context.state);
      syncRowSelectionState(context);
      persistState(context);
      updateSelectionControls(context, context.visibleRows);
      dispatchTableSelectionChanged(context, context.visibleRows);

      return buildSelectionDetail(context, context.visibleRows);
    },
    clearSelection() {
      const context = root?.__daisyTableContext ?? null;

      if (!context || !context.config.selection.enabled) {
        return null;
      }

      clearSelection(context);
      persistState(context);
      updateSelectionControls(context, context.visibleRows);
      dispatchTableSelectionChanged(context, context.visibleRows);

      return buildSelectionDetail(context, context.visibleRows);
    },
    async setSelection(selectedIds = []) {
      const context = await initTable(root);

      if (!context || !context.config.selection.enabled) {
        return null;
      }

      const ids = uniqueStringArray(Array.isArray(selectedIds) ? selectedIds : [selectedIds]);
      const normalizedIds = context.config.selection.mode === 'single' ? ids.slice(-1) : ids;

      context.selectionNotice = '';
      context.state.selection = {
        selectedIds: normalizedIds,
        excludedIds: [],
        allFilteredSelected: false,
        selectionScope: 'page',
        filterSignature: '',
      };
      syncRowSelectionState(context);
      persistState(context);
      renderTable(context);
      dispatchTableSelectionChanged(context, context.visibleRows);

      return buildSelectionDetail(context, context.visibleRows);
    },
    async setSelectionReadOnly(readOnly = false) {
      const context = await initTable(root);

      if (!context || !context.config.selection.enabled) {
        return null;
      }

      context.config.selection.readOnly = readOnly === true;
      context.root.dataset.tableSelectionReadonly = context.config.selection.readOnly ? 'true' : 'false';
      context.root.setAttribute('aria-disabled', context.config.selection.readOnly ? 'true' : 'false');
      renderTable(context);

      return buildSelectionDetail(context, context.visibleRows);
    },
    async setColumnOrder(columnOrder = []) {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      context.state.columnOrder = normalizeColumnOrder(columnOrder, context.config.columns);
      persistState(context);
      renderTable(context);

      return context.state.columnOrder;
    },
    async setColumnPinning(columnPinning = {}) {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      context.state.columnPinning = normalizeColumnPinning(columnPinning, context.config.columns);
      persistState(context);
      renderTable(context);

      return context.state.columnPinning;
    },
    async setColumnSizing(columnSizing = {}) {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      context.state.columnSizing = normalizeColumnSizing(columnSizing, context.config.columns);
      persistState(context);
      renderTable(context);

      return context.state.columnSizing;
    },
    async setExpanded(expanded = {}) {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      context.state.expanded = normalizeExpanded(expanded);
      persistState(context);
      renderTable(context);

      return context.state.expanded;
    },
    async editCell(rowId, columnId) {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      return startCellEdit(context, rowId, columnId);
    },
    async commitEdit() {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      return commitEdit(context);
    },
    async startCreate() {
      const context = await initTable(root);

      if (!context) {
        return null;
      }

      return startCreate(context);
    },
    async saveCreate() {
      const context = await initTable(root);

      if (!context || context.editing?.type !== 'create') {
        return null;
      }

      return commitCreate(context);
    },
    cancelCreate() {
      const context = root?.__daisyTableContext ?? null;

      return context ? cancelCreate(context) : false;
    },
    cancelEdit() {
      const context = root?.__daisyTableContext ?? null;

      if (!context) {
        return null;
      }

      cancelEdit(context);

      return true;
    },
    destroy() {
      return destroyTable(root);
    },
    get context() {
      return root?.__daisyTableContext ?? null;
    },
  };
}

if (typeof window !== 'undefined') {
  window.DaisyTable = {
    init: initTable,
    initAll: initAllTables,
    destroy: destroyTable,
    table: tableApi,
  };

  window.DaisyKit = window.DaisyKit || {};
  window.DaisyKit.table = tableApi;
}

if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllTables);
  } else {
    initAllTables();
  }

  document.addEventListener('livewire:navigated', (event) => reinitMorphTables(event.target));
  document.addEventListener('livewire:morph.updated', (event) => reinitMorphTables(event.target));
}

export {
  DEFAULT_FILTER_DEBOUNCE_MS,
  DEFAULT_MIN_SEARCH_CHARS,
  DEFAULT_PAGE_SIZE_OPTIONS,
  DEFAULT_SEARCH_DEBOUNCE_MS,
  applyClientFilters,
  applyExternalRefreshState,
  buildRequestPayload,
  buildSelectionActionPayload,
  buildSelectionDetail,
  buildServerRequest,
  buildSpatieRequestParams,
  createColumnDefs,
  createFilterSignature,
  getPersistedStateKey,
  getColumnClasses,
  getColumnWrapperClasses,
  getSortDirection,
  getSelectionFeedbackNote,
  initAllTables,
  initTable,
  isTextSearchReady,
  mergeState,
  normalizeColumns,
  normalizeConfig,
  normalizeInitialState,
  normalizeColumnOrder,
  normalizeColumnPinning,
  normalizeColumnSizing,
  normalizeExpanded,
  normalizeRowSelection,
  normalizeSelectionState,
  normalizeServerResponse,
  normalizeSpatieResponse,
  parseConfig,
  parseJsonParam,
  parseStateFromLocalStorage,
  parseStateFromUrl,
  isSafeHref,
  renderLinkCell,
  getRowDetailContent,
  resolveSearchInputValue,
  resetSelectionState,
  serializeRequestPayload,
  serializeStateToParams,
  tableApi,
  toggleRowSelection,
  toggleVisibleRowsSelection,
  toggleSorting,
};

export default initTable;
