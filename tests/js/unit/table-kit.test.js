import { describe, expect, it } from 'vitest';
import {
  DEFAULT_PAGE_SIZE_OPTIONS,
  applyClientFilters,
  buildRequestPayload,
  buildServerRequest,
  buildSpatieRequestParams,
  getPersistedStateKey,
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
        label: 'Name',
        sortable: true,
        filterable: true,
        visible: false,
        sortKey: 'users.name',
        filterKey: 'users.name',
        width: null,
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
        label: 'email',
        sortable: false,
        filterable: false,
        visible: true,
        sortKey: 'email',
        filterKey: 'email',
        width: null,
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
    });
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
    });

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
