import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { JSDOM } from 'jsdom';
import { describe, expect, it, vi } from 'vitest';
import {
  DEFAULT_FILTER_DEBOUNCE_MS,
  DEFAULT_MIN_SEARCH_CHARS,
  DEFAULT_PAGE_SIZE_OPTIONS,
  DEFAULT_SEARCH_DEBOUNCE_MS,
  applyClientFilters,
  applyExternalRefreshState,
  buildRequestPayload,
  buildSelectionDetail,
  buildSelectionActionPayload,
  buildServerRequest,
  buildSpatieRequestParams,
  createColumnDefs,
  createFilterSignature,
  getColumnClasses,
  getColumnWrapperClasses,
  getPersistedStateKey,
  getSelectionFeedbackNote,
  isSafeHref,
  isTextSearchReady,
  initTable,
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
  renderLinkCell,
  getRowDetailContent,
  resolveSearchInputValue,
  resetSelectionState,
  serializeRequestPayload,
  serializeStateToParams,
  toggleRowSelection,
  toggleVisibleRowsSelection,
  toggleSorting,
} from '../../../resources/js/table-kit.js';

const DEFAULT_COLUMN_SIZING_INFO = {
  startOffset: null,
  startSize: null,
  deltaOffset: null,
  deltaPercentage: null,
  isResizingColumn: false,
  columnSizingStart: [],
};

const tableConfigFixture = JSON.parse(readFileSync(
  fileURLToPath(new URL('../../Fixtures/table-config/tanstack-first.json', import.meta.url)),
  'utf8'
));

function installDom(html = '<div></div>') {
  const dom = new JSDOM(html, { url: 'https://example.test/users' });
  const previous = {
    window: global.window,
    document: global.document,
    Element: global.Element,
    HTMLElement: global.HTMLElement,
    HTMLInputElement: global.HTMLInputElement,
    HTMLSelectElement: global.HTMLSelectElement,
    HTMLButtonElement: global.HTMLButtonElement,
    HTMLDialogElement: global.HTMLDialogElement,
    CustomEvent: global.CustomEvent,
    AbortController: global.AbortController,
  };

  global.window = dom.window;
  global.document = dom.window.document;
  global.Element = dom.window.Element;
  global.HTMLElement = dom.window.HTMLElement;
  global.HTMLInputElement = dom.window.HTMLInputElement;
  global.HTMLSelectElement = dom.window.HTMLSelectElement;
  global.HTMLButtonElement = dom.window.HTMLButtonElement;
  global.HTMLDialogElement = dom.window.HTMLDialogElement;
  global.CustomEvent = dom.window.CustomEvent;
  global.AbortController = dom.window.AbortController;

  return {
    dom,
    restore() {
      Object.entries(previous).forEach(([key, value]) => {
        if (value === undefined) {
          delete global[key];
        } else {
          global[key] = value;
        }
      });
    },
  };
}

function createTableRoot(config) {
  document.body.innerHTML = `
    <div data-daisy-table="1">
      <input type="search" data-table-search>
      <div data-table-column-menu></div>
      <table>
        <colgroup data-table-colgroup></colgroup>
        <thead><tr data-table-head-row></tr></thead>
        <tbody data-table-body></tbody>
      </table>
      <span data-table-info></span>
      <span data-table-page-indicator></span>
    </div>
  `;

  const root = document.querySelector('[data-daisy-table="1"]');

  root.dataset.tableConfig = JSON.stringify(config);

  return root;
}

function nextTick() {
  return new Promise((resolve) => {
    setTimeout(resolve, 0);
  });
}

describe('table-kit helpers', () => {
  it('parses config from a dataset payload', () => {
    const config = parseConfig({
      dataset: {
        tableConfig: JSON.stringify({ mode: 'server', search: true }),
      },
    });

    expect(config).toEqual({
      mode: 'server',
      search: true,
    });
  });

  it('tolerates malformed persisted URL JSON state', () => {
    expect(parseJsonParam('{bad', [])).toEqual([]);

    const originalWindow = global.window;

    global.window = {
      location: {
        search: '?sorting=%7Bbad&columnFilters=%5Bbad&columnVisibility=%7Bbad',
      },
    };

    const config = normalizeConfig({
      columns: [{ key: 'name', label: 'Name' }],
    });

    expect(parseStateFromUrl(config)).toMatchObject({
      sorting: [],
      columnFilters: [],
      columnVisibility: {},
    });

    global.window = originalWindow;
  });

  it('escapes unsafe link cells and non trusted row details', () => {
    expect(renderLinkCell({ href: 'javascript:alert(1)', label: '<Open>' })).toBe('&lt;Open&gt;');
    expect(renderLinkCell({ href: '/users/1', label: 'Open', target: '_blank' })).toContain('rel="noopener noreferrer"');
    expect(getRowDetailContent({ detail: '<img src=x onerror=alert(1)>' })).toBe('&lt;img src=x onerror=alert(1)&gt;');
    expect(getRowDetailContent({ detailHtml: '<strong>Trusted</strong>' })).toBe('<strong>Trusted</strong>');
  });

  it('allows deeplink link cells only through explicit policies', () => {
    expect(renderLinkCell({ href: 'myapp://ticket/123', label: 'Open' })).toBe('Open');
    expect(renderLinkCell({ href: 'myapp://ticket/123', label: 'Open' }, {}, { allowedSchemes: ['myapp'] }))
      .toContain('href="myapp://ticket/123"');
    expect(renderLinkCell({ href: 'intent://scan/#Intent;scheme=zxing;end', label: 'Scan' }, { allowedSchemes: ['intent'] }))
      .toContain('href="intent://scan/#Intent;scheme=zxing;end"');
    expect(renderLinkCell({ href: 'intent://scan/#Intent;scheme=zxing;end', label: 'Scan' })).toBe('Scan');
    expect(renderLinkCell({ href: 'javascript:alert(1)', label: 'Bad' }, { allowedSchemes: ['javascript'] })).toBe('Bad');
    expect(isSafeHref('https://example.test/\nusers', { allowedSchemes: ['https'] })).toBe(false);
  });

  it('normalizes columns and initial state defaults', () => {
    const columns = normalizeColumns([
      { key: 'name', label: 'Name', sortable: true, visible: false, sortKey: 'users.name', filterable: true, filterKey: 'users.name', filter: { type: 'text' } },
      { key: 'email' },
    ]);
    const state = normalizeInitialState({
      pagination: { pageSize: 999 },
      columnVisibility: { name: true },
      columnFilters: [{ id: 'name', value: 'Jane' }],
    }, columns, columns.filter((column) => column.filter), [10, 25]);

    expect(columns).toMatchObject([
      {
        key: 'name',
        type: null,
        label: 'Name',
        sortable: true,
        filterable: true,
        visible: false,
        sortKey: 'users.name',
        filterKey: 'users.name',
        width: null,
        minWidth: null,
        maxWidth: null,
        align: null,
        verticalAlign: null,
        padding: null,
        density: null,
        nowrap: false,
        truncate: false,
        cellWrapperClass: '',
        headerWrapperClass: '',
        cellClass: '',
        headerClass: '',
        html: false,
        cell: {
          renderer: 'text',
          view: null,
          allowedSchemes: [],
        },
        enableResizing: true,
        size: undefined,
        minSize: undefined,
        maxSize: undefined,
        filter: {
          id: 'name',
          label: 'Name',
          type: 'text',
          filterKey: 'users.name',
          filterKeyFrom: null,
          filterKeyTo: null,
          options: [],
        },
      },
      {
        key: 'email',
        type: null,
        label: 'email',
        sortable: false,
        filterable: false,
        visible: true,
        sortKey: 'email',
        filterKey: 'email',
        width: null,
        minWidth: null,
        maxWidth: null,
        align: null,
        verticalAlign: null,
        padding: null,
        density: null,
        nowrap: false,
        truncate: false,
        cellWrapperClass: '',
        headerWrapperClass: '',
        cellClass: '',
        headerClass: '',
        html: false,
        cell: {
          renderer: 'text',
          view: null,
          allowedSchemes: [],
        },
        enableResizing: true,
        size: undefined,
        minSize: undefined,
        maxSize: undefined,
        filter: null,
      },
    ]);

    expect(state).toEqual({
      sorting: [],
      pagination: {
        pageIndex: 0,
        pageSize: 10,
      },
      globalFilter: '',
      columnFilters: [{ id: 'name', type: 'text', value: 'Jane' }],
      columnVisibility: {
        name: false,
        email: true,
      },
      columnOrder: ['name', 'email'],
      columnPinning: {
        left: [],
        right: [],
      },
      columnSizing: {},
      columnSizingInfo: DEFAULT_COLUMN_SIZING_INFO,
      expanded: {},
      rowSelection: {},
      selection: {
        selectedIds: [],
        excludedIds: [],
        allFilteredSelected: false,
        selectionScope: 'page',
        filterSignature: '',
      },
    });
  });

  it('normalizes the shared TanStack-first JSON fixture', () => {
    const config = normalizeConfig(tableConfigFixture);

    expect(config).toMatchObject({
      mode: 'client',
      rowKey: 'id',
      searchMode: 'fuzzy',
      subRowsKey: 'children',
      columnResizing: true,
      editable: {
        enabled: true,
        method: 'PATCH',
        mode: 'row',
        columns: ['name', 'status'],
        policy: {
          required: ['name'],
        },
        rowKey: 'id',
      },
    });
    expect(config.columns[0]).toMatchObject({
      key: 'name',
      size: 180,
      minSize: 120,
      maxSize: 320,
    });
    expect(config.initialState.columnSizing).toEqual({ name: 200 });
  });

  it('normalizes local updates, remote row creation, and typed editors', () => {
    const config = normalizeConfig({
      rowKey: 'id',
      editable: {
        enabled: true,
        mode: 'row',
        update: { strategy: 'local' },
        create: {
          enabled: true,
          strategy: 'remote',
          endpoint: { url: '/projects', method: 'POST' },
          defaults: { status: 'draft' },
          position: 'top',
        },
      },
      columns: [
        { key: 'name', editor: { type: 'text', required: true } },
        { key: 'budget', editor: { type: 'number' } },
        { key: 'active', editor: { type: 'boolean' } },
      ],
    });

    expect(config.editable).toMatchObject({
      update: { strategy: 'local', endpoint: null, method: 'PATCH' },
      create: {
        enabled: true,
        strategy: 'remote',
        endpoint: { url: '/projects' },
        method: 'POST',
        defaults: { status: 'draft' },
        position: 'top',
      },
    });
    expect(config.columns.map((column) => column.editor.type)).toEqual(['text', 'number', 'boolean']);
  });

  it('normalizes ECA table column presentation options', () => {
    const [actions, address] = normalizeColumns([
      { key: '_action', type: 'actions' },
      { key: 'postal_address', width: '260px', minWidth: 'max-content', align: 'center', nowrap: true, truncate: 2 },
    ]);

    expect(actions).toMatchObject({
      type: 'actions',
      width: 'fit',
      align: 'center',
      nowrap: true,
      density: 'compact',
    });
    expect(address).toMatchObject({
      width: '260px',
      minWidth: 'max-content',
      align: 'center',
      nowrap: true,
      truncate: 2,
    });
    expect(getColumnClasses(actions, 'cell')).toContain('daisy-table-width-fit');
    expect(getColumnClasses(actions, 'cell')).toContain('daisy-table-actions-cell');
    expect(getColumnWrapperClasses(actions, 'cell')).toContain('daisy-table-actions-content');
    expect(getColumnClasses(address, 'cell')).toContain('daisy-table-width-px-260');
    expect(getColumnClasses(address, 'cell')).toContain('daisy-table-min-width-max');
    expect(getColumnWrapperClasses(address, 'cell')).toContain('line-clamp-2');
  });

  it('normalizes cell renderers and builds TanStack column defs with Daisy metadata', () => {
    const columns = normalizeColumns([
      { key: 'actions', type: 'actions', view: 'users._actions', size: 96 },
      { key: 'profile', type: 'resource-link' },
      { key: 'status', html: true },
    ]);
    const columnDefs = createColumnDefs(columns);

    expect(columns[0]).toMatchObject({
      type: 'actions',
      html: true,
      cell: {
        renderer: 'blade',
        view: 'users._actions',
        allowedSchemes: [],
      },
      size: 96,
    });
    expect(columns[1].cell.renderer).toBe('link');
    expect(columns[2].cell.renderer).toBe('html');
    expect(columnDefs[0]).toMatchObject({
      id: 'actions',
      size: 96,
      meta: {
        daisyColumn: columns[0],
      },
    });
    expect(typeof columnDefs[0].cell).toBe('function');
  });

  it('normalizes TanStack-adjacent table state', () => {
    const columns = normalizeColumns([
      { key: 'name' },
      { key: 'email' },
      { key: 'actions' },
    ]);

    expect(normalizeColumnOrder(['actions', 'missing'], columns)).toEqual(['actions', 'name', 'email']);
    expect(normalizeColumnPinning({ left: ['name'], right: ['missing', 'actions'] }, columns)).toEqual({
      left: ['name'],
      right: ['actions'],
    });
    expect(normalizeColumnSizing({ name: '220', email: 0, missing: 100 }, columns)).toEqual({ name: 220 });
    expect(normalizeExpanded({ 1: true, 2: false })).toEqual({ 1: true });
    expect(normalizeRowSelection({ 1: true, 2: false })).toEqual({ 1: true });
  });

  it('builds a clean server config and request payload', () => {
    const config = normalizeConfig({
      mode: 'server',
      endpoint: '/users/table',
      method: 'post',
      columns: [{ key: 'name', label: 'Name', sortable: true, filterable: true, filter: { type: 'text' } }],
      initialState: {
        sorting: [{ id: 'name', desc: true }],
        pagination: { pageIndex: 2, pageSize: 25 },
        columnFilters: [{ id: 'name', value: 'doe' }],
      },
      pageSizeOptions: [10, 25, 50],
    });
    const payload = buildRequestPayload(config, config.initialState);

    expect(config.method).toBe('POST');
    expect(config.endpoint).toEqual({ url: '/users/table' });
    expect(payload).toEqual({
      pageIndex: 2,
      pageSize: 25,
      sorting: [{ id: 'name', desc: true }],
      globalFilter: '',
      columnFilters: [{ id: 'name', type: 'text', value: 'doe' }],
      columnVisibility: { name: true },
      columnOrder: ['name'],
      columnPinning: { left: [], right: [] },
      columnSizing: {},
      expanded: {},
      rowSelection: {},
    });
    expect(serializeRequestPayload(payload).toString()).toContain('sorting=%5B%7B%22id%22%3A%22name%22%2C%22desc%22%3Atrue%7D%5D');
  });

  it('applies external refresh filters without discarding pagination or sorting', () => {
    const config = normalizeConfig({
      mode: 'server',
      endpoint: '/users/table',
      columns: [
        { key: 'name', label: 'Name', sortable: true },
        { key: 'status', label: 'Status', filterable: true, filter: { type: 'text' } },
      ],
      initialState: {
        sorting: [{ id: 'name', desc: true }],
        pagination: { pageIndex: 2, pageSize: 25 },
      },
      pageSizeOptions: [10, 25, 50],
    });
    const context = {
      config,
      state: structuredClone(config.initialState),
    };

    applyExternalRefreshState(context, {
      filters: { status: 'active' },
    });

    expect(context.state).toEqual({
      sorting: [{ id: 'name', desc: true }],
      pagination: { pageIndex: 2, pageSize: 25 },
      globalFilter: '',
      columnFilters: [{ id: 'status', type: 'text', value: 'active' }],
      columnVisibility: { name: true, status: true },
      columnOrder: ['name', 'status'],
      columnPinning: { left: [], right: [] },
      columnSizing: {},
      columnSizingInfo: DEFAULT_COLUMN_SIZING_INFO,
      expanded: {},
      rowSelection: {},
      selection: {
        selectedIds: [],
        excludedIds: [],
        allFilteredSelected: false,
        selectionScope: 'page',
        filterSignature: '',
      },
    });
  });

  it('normalizes search pacing defaults and overrides', () => {
    const defaultConfig = normalizeConfig({
      columns: [{ key: 'name', label: 'Name' }],
    });
    const customConfig = normalizeConfig({
      columns: [{ key: 'name', label: 'Name' }],
      searchDebounceMs: 700,
      filterDebounceMs: 650,
      minSearchChars: 4,
    });

    expect(defaultConfig.searchDebounceMs).toBe(DEFAULT_SEARCH_DEBOUNCE_MS);
    expect(defaultConfig.filterDebounceMs).toBe(DEFAULT_FILTER_DEBOUNCE_MS);
    expect(defaultConfig.minSearchChars).toBe(DEFAULT_MIN_SEARCH_CHARS);
    expect(customConfig.searchDebounceMs).toBe(700);
    expect(customConfig.filterDebounceMs).toBe(650);
    expect(customConfig.minSearchChars).toBe(4);
  });

  it('normalizes external filters and livewire table mode flags', () => {
    const config = normalizeConfig({
      columns: [{ key: 'name', label: 'Name' }],
      externalFilters: true,
      livewireMode: 'morph',
    });

    expect(config.externalFilters).toBe(true);
    expect(config.livewireMode).toBe('morph');
  });

  it('normalizes selection config and initial state', () => {
    const config = normalizeConfig({
      selection: 'multiple',
      rowKey: 'uuid',
      columns: [{ key: 'name', label: 'Name' }],
      initialState: {
        selection: {
          selectedIds: [1, '2', null],
          excludedIds: ['3'],
          allFilteredSelected: true,
          selectionScope: 'filtered',
          filterSignature: 'saved',
        },
      },
    });

    expect(config.selection).toMatchObject({
      enabled: true,
      mode: 'multiple',
      rowKey: 'uuid',
    });
    expect(config.initialState.selection).toEqual({
      selectedIds: [],
      excludedIds: ['3'],
      allFilteredSelected: true,
      selectionScope: 'filtered',
      filterSignature: 'saved',
    });
  });

  it('toggles row and visible page selection without losing off-page ids', () => {
    const config = normalizeConfig({
      selection: 'multiple',
      rowKey: 'id',
      columns: [{ key: 'name', label: 'Name' }],
    });
    const state = structuredClone(config.initialState);
    const visibleRows = [{ id: 1 }, { id: 2 }];

    toggleRowSelection(state, config, { id: 1 });
    expect(state.selection.selectedIds).toEqual(['1']);

    toggleVisibleRowsSelection(state, config, visibleRows, true);
    expect(state.selection.selectedIds).toEqual(['1', '2']);

    state.selection.selectedIds.push('99');
    toggleVisibleRowsSelection(state, config, visibleRows, false);
    expect(state.selection.selectedIds).toEqual(['99']);
  });

  it('resets selection state when filters or search change', () => {
    const selection = normalizeSelectionState({
      selectedIds: ['1'],
      excludedIds: ['2'],
      allFilteredSelected: true,
      selectionScope: 'filtered',
      filterSignature: 'abc',
    });

    expect(resetSelectionState(selection)).toEqual({
      selectedIds: [],
      excludedIds: [],
      allFilteredSelected: false,
      selectionScope: 'page',
      filterSignature: '',
    });
  });

  it('builds explicit and filtered bulk action payloads', () => {
    const config = normalizeConfig({
      selection: 'multiple',
      rowKey: 'id',
      columns: [{ key: 'name', label: 'Name', sortable: true, filterable: true, filter: { type: 'text' } }],
    });
    const tableState = {
      ...structuredClone(config.initialState),
      sorting: [{ id: 'name', desc: true }],
      globalFilter: 'jane',
      columnFilters: [{ id: 'name', type: 'text', value: 'ja' }],
      selection: {
        selectedIds: ['1'],
        excludedIds: [],
        allFilteredSelected: false,
        selectionScope: 'page',
        filterSignature: '',
      },
    };

    expect(buildSelectionActionPayload(config, tableState)).toEqual({
      mode: 'ids',
      ids: ['1'],
    });

    tableState.selection = {
      selectedIds: [],
      excludedIds: ['2'],
      allFilteredSelected: true,
      selectionScope: 'filtered',
      filterSignature: createFilterSignature(tableState),
    };

    expect(buildSelectionActionPayload(config, tableState)).toEqual({
      mode: 'filtered',
      filters: [{ id: 'name', type: 'text', value: 'ja' }],
      sorting: [{ id: 'name', desc: true }],
      globalFilter: 'jane',
      excludedIds: ['2'],
    });
  });

  it('uses global filtered feedback only while no rows are excluded', () => {
    const labels = {
      allFilteredRowsSelected: 'All filtered results are selected',
      selectedOffPageCount: ':count selected off page',
    };

    expect(getSelectionFeedbackNote({
      allFilteredSelected: true,
      excludedCount: 1,
      offPageCount: 6,
      visibleSelectedCount: 4,
      visibleRowsCount: 5,
    }, labels)).toBe('6 selected off page');

    expect(getSelectionFeedbackNote({
      allFilteredSelected: true,
      excludedCount: 0,
      offPageCount: 7,
      visibleSelectedCount: 5,
      visibleRowsCount: 5,
    }, labels)).toBe('All filtered results are selected');
  });

  it('builds a complete selection detail for custom selection bars', () => {
    const config = normalizeConfig({
      selection: 'multiple',
      rowKey: 'id',
      columns: [{ key: 'name', label: 'Name', sortable: true, filterable: true, filter: { type: 'text' } }],
    });
    const context = {
      config,
      state: {
        ...structuredClone(config.initialState),
        sorting: [{ id: 'name', desc: false }],
        pagination: { pageIndex: 1, pageSize: 10 },
        globalFilter: 'jane',
        selection: {
          selectedIds: ['1', '2', '9'],
          excludedIds: [],
          allFilteredSelected: false,
          selectionScope: 'page',
          filterSignature: '',
        },
      },
    };

    expect(buildSelectionDetail(context, [{ id: 1, name: 'Jane' }, { id: 2, name: 'Janet' }])).toMatchObject({
      selectedIds: ['1', '2', '9'],
      selectedCount: 3,
      visibleSelectedCount: 2,
      offPageCount: 1,
      actionPayload: {
        mode: 'ids',
        ids: ['1', '2', '9'],
      },
      tableState: {
        sorting: [{ id: 'name', desc: false }],
        pagination: { pageIndex: 1, pageSize: 10 },
        globalFilter: 'jane',
      },
    });
  });

  it('waits for enough characters before applying text search', () => {
    expect(isTextSearchReady('', 3)).toBe(true);
    expect(isTextSearchReady('ab', 3)).toBe(false);
    expect(isTextSearchReady('abc', 3)).toBe(true);
    expect(resolveSearchInputValue('ab', '', 3)).toBeNull();
    expect(resolveSearchInputValue('ab', 'previous', 3)).toBe('');
    expect(resolveSearchInputValue('abcd', '', 3)).toBe('abcd');
  });

  it('filters client rows with global and column filters', () => {
    const rows = [
      { name: 'Jane', status: 'active', is_published: true },
      { name: 'John', status: 'suspended', is_published: false },
    ];
    const columns = normalizeColumns([
      { key: 'name', filterable: true },
      { key: 'status', filterable: true },
      { key: 'is_published', filterable: true },
    ]);

    const filtered = applyClientFilters(rows, columns, {
      globalFilter: 'ja',
      columnFilters: [{ id: 'status', type: 'text', value: 'active' }, { id: 'is_published', type: 'boolean', value: true }],
    });

    expect(filtered).toEqual([{ name: 'Jane', status: 'active', is_published: true }]);
  });

  it('cycles sorting directions', () => {
    const baseState = { sorting: [] };

    expect(toggleSorting(baseState, 'name')).toEqual([{ id: 'name', desc: false }]);
    expect(toggleSorting({ sorting: [{ id: 'name', desc: false }] }, 'name')).toEqual([{ id: 'name', desc: true }]);
    expect(toggleSorting({ sorting: [{ id: 'name', desc: true }] }, 'name')).toEqual([]);
  });

  it('normalizes server responses and preserves pagination metadata', () => {
    const normalized = normalizeServerResponse({
      rows: [{ name: 'Jane' }],
      rowCount: 42,
      pageCount: 5,
      state: { pageIndex: 1, pageSize: 10 },
    }, {
      pagination: { pageIndex: 0, pageSize: DEFAULT_PAGE_SIZE_OPTIONS[0] },
    });

    expect(normalized).toEqual({
      rows: [{ name: 'Jane' }],
      rowCount: 42,
      pageCount: 5,
      state: { pageIndex: 1, pageSize: 10 },
      meta: {},
    });
  });

  it('builds spatie query builder params from sorting, filters and pagination', () => {
    const config = normalizeConfig({
      mode: 'server',
      serverAdapter: 'spatie-query-builder',
      endpoint: '/users',
      columns: [
        { key: 'name', label: 'Name', sortable: true, sortKey: 'users.name', filterable: true, filterKey: 'name', filter: { type: 'text' } },
        { key: 'status', label: 'Status', sortable: true, filterable: true, filterKey: 'status', filter: { type: 'select', options: [{ value: 'active', label: 'Active' }] } },
      ],
      initialState: {
        sorting: [{ id: 'name', desc: true }],
        pagination: { pageIndex: 2, pageSize: 25 },
        globalFilter: 'jane',
        columnFilters: [{ id: 'status', type: 'select', value: 'active' }],
      },
    });

    const params = buildSpatieRequestParams(config, config.initialState);

    expect(params.toString()).toContain('sort=-users.name');
    expect(params.toString()).toContain('page%5Bnumber%5D=3');
    expect(params.toString()).toContain('page%5Bsize%5D=25');
    expect(params.toString()).toContain('filter%5Bglobal%5D=jane');
    expect(params.toString()).toContain('filter%5Bstatus%5D=active');
  });

  it('serializes date and date-range filters for default and spatie adapters', () => {
    const config = normalizeConfig({
      mode: 'server',
      endpoint: '/audits',
      columns: [
        { key: 'created_at', filterable: true, filter: { type: 'date' } },
        { key: 'period', filterable: true, filterKey: 'period', filter: { type: 'date-range', filterKeyFrom: 'started_after', filterKeyTo: 'started_before' } },
      ],
      initialState: {
        columnFilters: [
          { id: 'created_at', type: 'date', value: '2026-06-25' },
          { id: 'period', type: 'date-range', value: { from: '2026-06-01', to: '2026-06-30' } },
        ],
      },
    });
    const spatieConfig = normalizeConfig({
      ...config,
      serverAdapter: 'spatie-query-builder',
    });

    expect(buildRequestPayload(config, config.initialState).columnFilters).toEqual([
      { id: 'created_at', type: 'date', value: '2026-06-25' },
      { id: 'period', type: 'date-range', value: { from: '2026-06-01', to: '2026-06-30' } },
    ]);

    const params = buildSpatieRequestParams(spatieConfig, spatieConfig.initialState);

    expect(params.toString()).toContain('filter%5Bcreated_at%5D=2026-06-25');
    expect(params.toString()).toContain('filter%5Bstarted_after%5D=2026-06-01');
    expect(params.toString()).toContain('filter%5Bstarted_before%5D=2026-06-30');
  });

  it('normalizes a spatie paginator response', () => {
    const normalized = normalizeSpatieResponse({
      data: [{ name: 'Jane' }],
      meta: {
        current_page: 3,
        per_page: 25,
        total: 120,
        last_page: 5,
      },
    });

    expect(normalized).toEqual({
      rows: [{ name: 'Jane' }],
      rowCount: 120,
      pageCount: 5,
      state: { pageIndex: 2, pageSize: 25 },
      meta: {
        current_page: 3,
        per_page: 25,
        total: 120,
        last_page: 5,
      },
    });
  });

  it('serializes state to adapter-native url params', () => {
    const config = normalizeConfig({
      mode: 'server',
      serverAdapter: 'spatie-query-builder',
      endpoint: '/users',
      columns: [{ key: 'name', label: 'Name', sortable: true, filterable: true, filter: { type: 'text' } }],
      initialState: {
        sorting: [{ id: 'name', desc: false }],
        pagination: { pageIndex: 1, pageSize: 25 },
        globalFilter: 'doe',
      },
    });

    expect(serializeStateToParams(config, config.initialState).toString()).toContain('sort=name');
    expect(serializeStateToParams(config, config.initialState).toString()).toContain('filter%5Bglobal%5D=doe');
  });

  it('hydrates state from a spatie-style url and merges it with defaults', () => {
    const originalWindow = global.window;

    global.window = {
      location: {
        search: '?sort=-users.name&filter%5Bglobal%5D=jane&filter%5Bstatus%5D=active&page%5Bnumber%5D=4&page%5Bsize%5D=50',
      },
    };

    const config = normalizeConfig({
      mode: 'server',
      serverAdapter: 'spatie-query-builder',
      endpoint: '/users',
      columns: [
        { key: 'name', label: 'Name', sortable: true, sortKey: 'users.name' },
        { key: 'status', label: 'Status', filterable: true, filter: { type: 'select', options: [{ value: 'active', label: 'Active' }] } },
      ],
      initialState: {
        pagination: { pageIndex: 0, pageSize: 10 },
      },
    });

    const merged = mergeState(config.initialState, parseStateFromUrl(config), config);

    expect(merged).toEqual({
      sorting: [{ id: 'name', desc: true }],
      pagination: { pageIndex: 3, pageSize: 50 },
      globalFilter: 'jane',
      columnFilters: [{ id: 'status', type: 'select', value: 'active' }],
      columnVisibility: { name: true, status: true },
      columnOrder: ['name', 'status'],
      columnPinning: { left: [], right: [] },
      columnSizing: {},
      columnSizingInfo: DEFAULT_COLUMN_SIZING_INFO,
      expanded: {},
      rowSelection: {},
      selection: {
        selectedIds: [],
        excludedIds: [],
        allFilteredSelected: false,
        selectionScope: 'page',
        filterSignature: '',
      },
    });

    global.window = originalWindow;
  });

  it('hydrates spatie filters submitted as bracket arrays from external forms', () => {
    const originalWindow = global.window;

    global.window = {
      location: {
        search: '?filter%5Bstatus%5D%5B%5D=toSpecify&filter%5Bstatus%5D%5B%5D=finished&filter%5Bcompliance%5D%5B%5D=conforme&page%5Bnumber%5D=1&page%5Bsize%5D=20',
      },
    };

    const config = normalizeConfig({
      mode: 'server',
      serverAdapter: 'spatie-query-builder',
      endpoint: '/interventions',
      columns: [
        { key: 'status', label: 'Status', filterable: true, filterKey: 'status', filter: { type: 'text' } },
        { key: 'compliance', label: 'Compliance', filterable: true, filterKey: 'compliance', filter: { type: 'text' } },
      ],
      initialState: {
        pagination: { pageIndex: 0, pageSize: 20 },
      },
    });

    const merged = mergeState(config.initialState, parseStateFromUrl(config), config);

    expect(merged.columnFilters).toEqual([
      { id: 'status', type: 'text', value: 'toSpecify,finished' },
      { id: 'compliance', type: 'text', value: 'conforme' },
    ]);
    expect(serializeStateToParams(config, merged).toString()).toContain('filter%5Bstatus%5D=toSpecify%2Cfinished');
    expect(serializeStateToParams(config, merged).toString()).toContain('filter%5Bcompliance%5D=conforme');

    global.window = originalWindow;
  });

  it('builds a server request using the spatie adapter', () => {
    const config = normalizeConfig({
      mode: 'server',
      serverAdapter: 'spatie-query-builder',
      endpoint: '/users',
      columns: [{ key: 'name', label: 'Name', sortable: true }],
      initialState: {
        sorting: [{ id: 'name', desc: false }],
      },
    });

    const request = buildServerRequest(config, config.initialState);

    expect(request.url).toContain('/users?sort=name');
    expect(typeof request.responseNormalizer).toBe('function');
  });

  it('hydrates persisted state from localStorage when requested', () => {
    const originalWindow = global.window;
    const context = {
      config: {
        persistState: 'local',
        stateKey: 'users-index',
        endpoint: { url: '/users' },
      },
      root: { id: 'users-table' },
    };

    global.window = {
      localStorage: {
        getItem(key) {
          expect(key).toBe(getPersistedStateKey(context));

          return JSON.stringify({
            globalFilter: 'saved',
            pagination: { pageIndex: 1, pageSize: 25 },
          });
        },
      },
    };

    expect(parseStateFromLocalStorage(context)).toEqual({
      globalFilter: 'saved',
      pagination: { pageIndex: 1, pageSize: 25 },
    });

    global.window = originalWindow;
  });

  it('delegates client global search to TanStack fuzzy or includes filtering', async () => {
    const env = installDom();

    try {
      const fuzzyRoot = createTableRoot({
        searchMode: 'fuzzy',
        searchDebounceMs: 0,
        minSearchChars: 1,
        columns: [{ key: 'name', label: 'Name', filterable: true }],
        rows: [
          { id: '1', name: 'Jonathan' },
          { id: '2', name: 'Jane' },
        ],
      });
      const fuzzyContext = await initTable(fuzzyRoot);
      const fuzzyInput = fuzzyRoot.querySelector('[data-table-search]');

      fuzzyInput.value = 'jhn';
      fuzzyInput.dispatchEvent(new window.Event('input', { bubbles: true }));
      await nextTick();
      await nextTick();

      expect(fuzzyContext.rowCount).toBe(1);
      expect(fuzzyRoot.querySelector('[data-table-body]').textContent).toContain('Jonathan');
      expect(fuzzyRoot.querySelector('[data-table-body]').textContent).not.toContain('Jane');

      const includesRoot = createTableRoot({
        searchMode: 'includes',
        searchDebounceMs: 0,
        minSearchChars: 1,
        columns: [{ key: 'name', label: 'Name', filterable: true }],
        rows: [{ id: '1', name: 'Jonathan' }],
      });
      const includesContext = await initTable(includesRoot);
      const includesInput = includesRoot.querySelector('[data-table-search]');

      includesInput.value = 'jhn';
      includesInput.dispatchEvent(new window.Event('input', { bubbles: true }));
      await nextTick();
      await nextTick();

      expect(includesContext.rowCount).toBe(0);
      expect(includesRoot.querySelector('[data-table-body]').textContent).toContain('No results');
    } finally {
      env.restore();
    }
  });

  it('renders TanStack expanded row detail and sub rows from the configured key', async () => {
    const env = installDom();

    try {
      const root = createTableRoot({
        rowKey: 'id',
        subRowsKey: 'children',
        rowDetail: { mode: 'inline' },
        columns: [{ key: 'name', label: 'Name' }],
        rows: [
          {
            id: 'parent',
            name: 'Parent',
            detail: 'Parent details',
            children: [{ id: 'child', name: 'Child' }],
          },
        ],
        initialState: {
          expanded: { parent: true },
        },
      });
      const context = await initTable(root);
      const bodyText = root.querySelector('[data-table-body]').textContent;

      expect(context.visibleRows.map((row) => row.id)).toEqual(['parent', 'child']);
      expect(bodyText).toContain('Parent');
      expect(bodyText).toContain('Child');
      expect(bodyText).toContain('Parent details');
    } finally {
      env.restore();
    }
  });

  it('uses TanStack row selection and column sizing state for interactive controls', async () => {
    const env = installDom();

    try {
      const root = createTableRoot({
        rowKey: 'id',
        selection: 'multiple',
        columnResizing: true,
        columns: [{ key: 'name', label: 'Name', size: 120 }],
        rows: [
          { id: 'a', name: 'Ada' },
          { id: 'b', name: 'Ben' },
        ],
      });
      const context = await initTable(root);
      let selectionDetail = null;
      const firstRowCheckbox = root.querySelector('[data-table-row-select="a"]');

      root.addEventListener('daisy:table-selection-changed', (event) => {
        selectionDetail = event.detail;
      });

      firstRowCheckbox.checked = true;
      firstRowCheckbox.dispatchEvent(new window.Event('change', { bubbles: true }));

      expect(context.state.rowSelection).toEqual({ a: true });
      expect(context.state.selection.selectedIds).toEqual(['a']);
      expect(selectionDetail.selectedIds).toEqual(['a']);
      expect(root.querySelector('[data-table-resize]')).toBeInstanceOf(HTMLElement);
      expect(root.querySelector('th[width="120"]')).toBeInstanceOf(HTMLElement);
      expect(root.querySelector('td[width="120"]')).toBeInstanceOf(HTMLElement);
      expect(root.querySelector('[style*="120px"]')).toBeNull();

      context.table.setColumnSizing({ name: 180 });

      expect(context.state.columnSizing).toEqual({ name: 180 });
    } finally {
      env.restore();
    }
  });

  it('updates column filters through TanStack column.setFilterValue', async () => {
    const env = installDom();

    try {
      const root = createTableRoot({
        columns: [
          { key: 'name', label: 'Name' },
          { key: 'status', label: 'Status', filterable: true, filter: { type: 'text' } },
        ],
        rows: [
          { id: '1', name: 'Jane', status: 'active' },
          { id: '2', name: 'John', status: 'archived' },
        ],
      });
      const context = await initTable(root);

      context.table.getColumn('status').setFilterValue({ id: 'status', type: 'text', value: 'active' });

      expect(context.state.columnFilters).toEqual([{ id: 'status', type: 'text', value: 'active' }]);
    } finally {
      env.restore();
    }
  });

  it('renders grouped row edit controls in row mode', async () => {
    const env = installDom();

    try {
      const root = createTableRoot({
        rowKey: 'id',
        editable: true,
        editEndpoint: '/users/1',
        editMode: 'row',
        editableColumns: ['name', 'status'],
        columns: [
          { key: 'name', label: 'Name' },
          { key: 'status', label: 'Status' },
        ],
        rows: [{ id: '1', name: 'Jane', status: 'draft' }],
      });

      await initTable(root);

      root.querySelector('[data-table-edit-cell][data-table-column-id="name"]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      expect(root.querySelectorAll('[data-table-edit-input]')).toHaveLength(2);
      expect(root.querySelector('.daisy-table-edit-row-actions [data-table-edit-save]')).toBeInstanceOf(HTMLElement);
      expect(root.querySelector('.daisy-table-edit-cell [data-table-edit-save]')).toBeNull();
    } finally {
      env.restore();
    }
  });

  it('commits row mode edits as a grouped dirty payload', async () => {
    const env = installDom();
    const previousFetch = global.fetch;

    try {
      const fetchMock = vi.fn(async () => ({
        ok: true,
        json: async () => ({
          row: { id: '1', name: 'Janet', status: 'active' },
        }),
      }));

      global.fetch = fetchMock;

      const root = createTableRoot({
        rowKey: 'id',
        editable: true,
        editEndpoint: '/users/1',
        editMode: 'row',
        editableColumns: ['name', 'status'],
        columns: [
          { key: 'name', label: 'Name' },
          { key: 'status', label: 'Status' },
        ],
        rows: [{ id: '1', name: 'Jane', status: 'draft' }],
      });
      const context = await initTable(root);

      root.querySelector('[data-table-edit-cell][data-table-column-id="name"]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      const nameInput = root.querySelector('[data-table-edit-input][data-table-column-id="name"]');
      const statusInput = root.querySelector('[data-table-edit-input][data-table-column-id="status"]');

      nameInput.value = 'Janet';
      nameInput.dispatchEvent(new window.Event('input', { bubbles: true }));
      statusInput.value = 'active';
      statusInput.dispatchEvent(new window.Event('input', { bubbles: true }));
      root.querySelector('.daisy-table-edit-row-actions [data-table-edit-save]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      await nextTick();
      await nextTick();

      const [, request] = fetchMock.mock.calls[0];

      expect(JSON.parse(request.body)).toMatchObject({
        rowId: '1',
        column: 'name',
        dirty: {
          name: 'Janet',
          status: 'active',
        },
      });
      expect(context.config.rows[0]).toMatchObject({ name: 'Janet', status: 'active' });
    } finally {
      if (previousFetch === undefined) {
        delete global.fetch;
      } else {
        global.fetch = previousFetch;
      }

      env.restore();
    }
  });

  it('blocks editable commits locally when required policy fields are blank', async () => {
    const env = installDom();
    const previousFetch = global.fetch;

    try {
      const fetchMock = vi.fn();

      global.fetch = fetchMock;

      const root = createTableRoot({
        rowKey: 'id',
        editable: true,
        editEndpoint: '/users/1',
        editableColumns: ['name'],
        editPolicy: { required: ['name'] },
        columns: [{ key: 'name', label: 'Name' }],
        rows: [{ id: '1', name: 'Jane' }],
      });
      let failedDetail = null;

      await initTable(root);
      root.addEventListener('daisy:table-edit-failed', (event) => {
        failedDetail = event.detail;
      });

      root.querySelector('[data-table-edit-cell][data-table-column-id="name"]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      const input = root.querySelector('[data-table-edit-input]');

      input.value = '';
      input.dispatchEvent(new window.Event('input', { bubbles: true }));
      root.querySelector('[data-table-edit-save]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      await nextTick();

      expect(fetchMock).not.toHaveBeenCalled();
      expect(failedDetail.errors).toEqual({ name: 'This value is required.' });
      expect(root.querySelector('.daisy-table-edit-error').textContent).toContain('required');
    } finally {
      if (previousFetch === undefined) {
        delete global.fetch;
      } else {
        global.fetch = previousFetch;
      }

      env.restore();
    }
  });

  it('keeps original row data and exposes server errors when editable commits fail', async () => {
    const env = installDom();
    const previousFetch = global.fetch;

    try {
      const fetchMock = vi.fn(async () => ({
        ok: false,
        status: 422,
        json: async () => ({
          message: 'Invalid data.',
          errors: { name: ['Name is already used.'] },
        }),
      }));

      global.fetch = fetchMock;

      const root = createTableRoot({
        rowKey: 'id',
        editable: true,
        editEndpoint: '/users/1',
        editableColumns: ['name'],
        columns: [{ key: 'name', label: 'Name' }],
        rows: [{ id: '1', name: 'Jane' }],
      });
      const context = await initTable(root);
      let failedDetail = null;

      root.addEventListener('daisy:table-edit-failed', (event) => {
        failedDetail = event.detail;
      });

      root.querySelector('[data-table-edit-cell][data-table-column-id="name"]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      const input = root.querySelector('[data-table-edit-input]');

      input.value = 'Janet';
      input.dispatchEvent(new window.Event('input', { bubbles: true }));
      root.querySelector('[data-table-edit-save]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      await nextTick();
      await nextTick();

      expect(fetchMock).toHaveBeenCalledOnce();
      expect(context.config.rows[0].name).toBe('Jane');
      expect(failedDetail.errors).toEqual({ name: 'Name is already used.' });
      expect(root.querySelector('.daisy-table-edit-error').textContent).toContain('already used');
    } finally {
      if (previousFetch === undefined) {
        delete global.fetch;
      } else {
        global.fetch = previousFetch;
      }

      env.restore();
    }
  });

  it('commits editable cell changes with Daisy payload events and row rollback state', async () => {
    const env = installDom();
    const previousFetch = global.fetch;

    try {
      const fetchMock = vi.fn(async () => ({
        ok: true,
        json: async () => ({
          row: { id: '1', name: 'Janet', status: 'active' },
        }),
      }));

      global.fetch = fetchMock;

      const root = createTableRoot({
        rowKey: 'id',
        editable: true,
        editEndpoint: '/users/1',
        editableColumns: ['name', 'status'],
        columns: [
          { key: 'name', label: 'Name' },
          { key: 'status', label: 'Status' },
        ],
        rows: [{ id: '1', name: 'Jane', status: 'draft' }],
      });
      const context = await initTable(root);
      let committedDetail = null;

      root.addEventListener('daisy:table-edit-committed', (event) => {
        committedDetail = event.detail;
      });

      root.querySelector('[data-table-edit-cell][data-table-column-id="name"]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      const input = root.querySelector('[data-table-edit-input][data-table-column-id="name"]');

      input.value = 'Janet';
      input.dispatchEvent(new window.Event('input', { bubbles: true }));

      root.querySelector('[data-table-edit-save]')
        .dispatchEvent(new window.MouseEvent('click', { bubbles: true }));

      await nextTick();
      await nextTick();

      const [, request] = fetchMock.mock.calls[0];

      expect(fetchMock).toHaveBeenCalledOnce();
      expect(JSON.parse(request.body)).toMatchObject({
        rowId: '1',
        column: 'name',
        value: 'Janet',
        dirty: { name: 'Janet' },
      });
      expect(context.config.rows[0].name).toBe('Janet');
      expect(committedDetail.row.name).toBe('Janet');
    } finally {
      if (previousFetch === undefined) {
        delete global.fetch;
      } else {
        global.fetch = previousFetch;
      }

      env.restore();
    }
  });

  it('creates a local row through the TanStack draft row without affecting selection', async () => {
    const env = installDom();

    try {
      const root = createTableRoot({
        rowKey: 'id',
        selection: 'multiple',
        editable: {
          enabled: true,
          columns: ['name', 'active'],
          update: { strategy: 'local' },
          create: { enabled: true, strategy: 'local', defaults: { active: false } },
        },
        columns: [
          { key: 'name', label: 'Name', editor: { type: 'text', required: true } },
          { key: 'active', label: 'Active', editor: { type: 'boolean' } },
        ],
        rows: [{ id: '1', name: 'Jane', active: true }],
      });
      root.insertAdjacentHTML('afterbegin', '<button type="button" data-table-create>Add</button>');
      const context = await initTable(root);
      let committed = null;

      root.addEventListener('daisy:table-create-committed', (event) => {
        committed = event.detail;
      });
      root.querySelector('[data-table-create]').click();

      expect(context.visibleRows).toHaveLength(2);
      expect(root.querySelector('[data-table-row-select]')?.disabled).toBe(true);

      const nameInput = root.querySelector('[data-table-edit-input][data-table-column-id="name"]');
      nameInput.value = 'New project';
      nameInput.dispatchEvent(new window.Event('input', { bubbles: true }));
      root.querySelector('[data-table-edit-save]').click();

      await nextTick();

      expect(context.config.rows[0]).toMatchObject({ name: 'New project', active: false });
      expect(committed.values).toEqual({ name: 'New project', active: false });
      expect(context.creating).toBeNull();
    } finally {
      env.restore();
    }
  });

  it('posts creation values and keeps the draft on validation errors', async () => {
    const env = installDom();
    const previousFetch = global.fetch;

    try {
      const fetchMock = vi.fn(async () => ({
        ok: false,
        status: 422,
        json: async () => ({ message: 'Invalid project.', errors: { name: ['Name is required.'] } }),
      }));
      global.fetch = fetchMock;

      const root = createTableRoot({
        rowKey: 'id',
        editable: {
          enabled: true,
          columns: ['name'],
          update: { strategy: 'local' },
          create: { enabled: true, strategy: 'remote', endpoint: '/projects', method: 'POST' },
        },
        columns: [{ key: 'name', label: 'Name', editor: { type: 'text' } }],
        rows: [],
      });
      root.insertAdjacentHTML('afterbegin', '<button type="button" data-table-create>Add</button>');
      const context = await initTable(root);

      root.querySelector('[data-table-create]').click();
      const input = root.querySelector('[data-table-edit-input]');
      input.value = 'New project';
      input.dispatchEvent(new window.Event('input', { bubbles: true }));
      root.querySelector('[data-table-edit-save]').click();

      await nextTick();
      await nextTick();

      expect(JSON.parse(fetchMock.mock.calls[0][1].body)).toEqual({ values: { name: 'New project' } });
      expect(context.creating).not.toBeNull();
      expect(root.querySelector('.daisy-table-edit-error').textContent).toContain('Name is required.');
    } finally {
      if (previousFetch === undefined) {
        delete global.fetch;
      } else {
        global.fetch = previousFetch;
      }

      env.restore();
    }
  });

  it('replaces a remote creation draft with the canonical response row', async () => {
    const env = installDom();
    const previousFetch = global.fetch;

    try {
      global.fetch = vi.fn(async () => ({
        ok: true,
        json: async () => ({ row: { id: '2', name: 'New project' } }),
      }));

      const root = createTableRoot({
        rowKey: 'id',
        editable: {
          enabled: true,
          columns: ['name'],
          update: { strategy: 'local' },
          create: { enabled: true, strategy: 'remote', endpoint: '/projects', method: 'POST' },
        },
        columns: [{ key: 'name', label: 'Name' }],
        rows: [{ id: '1', name: 'Jane' }],
      });
      root.insertAdjacentHTML('afterbegin', '<button type="button" data-table-create>Add</button>');
      const context = await initTable(root);

      root.querySelector('[data-table-create]').click();
      const input = root.querySelector('[data-table-edit-input]');
      input.value = 'New project';
      input.dispatchEvent(new window.Event('input', { bubbles: true }));
      root.querySelector('[data-table-edit-save]').click();

      await nextTick();
      await nextTick();

      expect(context.creating).toBeNull();
      expect(context.config.rows[0]).toEqual({ id: '2', name: 'New project' });
      expect(context.visibleRows.map((row) => row.original.__daisyTableDraft)).not.toContain(true);
    } finally {
      if (previousFetch === undefined) {
        delete global.fetch;
      } else {
        global.fetch = previousFetch;
      }

      env.restore();
    }
  });
});
