import { describe, expect, it } from 'vitest';
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
  createFilterSignature,
  getColumnClasses,
  getColumnWrapperClasses,
  getPersistedStateKey,
  getSelectionFeedbackNote,
  isTextSearchReady,
  mergeState,
  normalizeColumns,
  normalizeConfig,
  normalizeInitialState,
  normalizeSelectionState,
  normalizeServerResponse,
  normalizeSpatieResponse,
  parseConfig,
  parseStateFromLocalStorage,
  parseStateFromUrl,
  resolveSearchInputValue,
  resetSelectionState,
  serializeRequestPayload,
  serializeStateToParams,
  toggleRowSelection,
  toggleVisibleRowsSelection,
  toggleSorting,
} from '../../../resources/js/table-kit.js';

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

    expect(columns).toEqual([
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
        filter: {
          id: 'name',
          label: 'Name',
          type: 'text',
          filterKey: 'users.name',
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
      selection: {
        selectedIds: [],
        excludedIds: [],
        allFilteredSelected: false,
        selectionScope: 'page',
        filterSignature: '',
      },
    });
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
});
