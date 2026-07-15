import { JSDOM } from 'jsdom';
import { afterEach, describe, expect, it, vi } from 'vitest';
import {
  MAX_PERSISTED_STATE_BYTES,
  readStateFromUrl,
  serializePersistedState,
  writeStateToUrl,
} from '../../../resources/js/table/persistence.js';
import {
  mutationRequest,
  requireMutationRow,
} from '../../../resources/js/table/transport.js';
import { normalizeActions } from '../../../resources/js/table/renderers.js';
import { mergeState, normalizeConfig } from '../../../resources/js/table/runtime.js';

const previousGlobals = {
  window: global.window,
  document: global.document,
  fetch: global.fetch,
};

afterEach(() => {
  Object.entries(previousGlobals).forEach(([key, value]) => {
    if (value === undefined) {
      delete global[key];
    } else {
      global[key] = value;
    }
  });
});

describe('table v2 runtime contracts', () => {
  it('namespaces two tables while preserving host query parameters', () => {
    const dom = new JSDOM('<div></div>', { url: 'https://example.test/users?tenant=acme' });
    const fields = ['sorting', 'expanded', 'rowSelection'];

    global.window = dom.window;
    global.document = dom.window.document;

    writeStateToUrl({ stateKey: 'users', persistStateFields: fields }, null, {
      sorting: [{ id: 'name', desc: false }],
      expanded: { 1: true },
      rowSelection: { 1: true },
    });
    writeStateToUrl({ stateKey: 'assets', persistStateFields: ['sorting'] }, null, {
      sorting: [{ id: 'title', desc: true }],
      rowSelection: { secret: true },
    });

    const params = new URLSearchParams(window.location.search);

    expect(params.get('tenant')).toBe('acme');
    expect(readStateFromUrl({ stateKey: 'users' })).toMatchObject({
      expanded: { 1: true },
      rowSelection: { 1: true },
    });
    expect(readStateFromUrl({ stateKey: 'assets' })).toEqual({ sorting: [{ id: 'title', desc: true }] });
  });

  it('removes URL persistence for an untouched table while preserving host parameters and other tables', () => {
    const initialState = {
      sorting: [],
      globalFilter: '',
      columnFilters: [],
      pagination: { pageIndex: 0, pageSize: 20 },
      columnVisibility: { name: true, status: true },
      columnOrder: ['name', 'status'],
      columnPinning: { left: [], right: [] },
      columnSizing: {},
    };
    const dom = new JSDOM('<div></div>', {
      url: 'https://example.test/users?tenant=acme&daisy-table%5Bassets%5D=%7B%22sorting%22%3A%5B%5D%7D&daisy-table%5Busers%5D=%7B%22globalFilter%22%3A%22stale%22%7D',
    });

    global.window = dom.window;
    global.document = dom.window.document;

    const state = structuredClone(initialState);

    state.columnFilters = [{ id: 'status', type: 'text', value: '' }];
    writeStateToUrl({ stateKey: 'users', initialState }, null, state);

    const params = new URLSearchParams(window.location.search);

    expect(params.get('tenant')).toBe('acme');
    expect(params.has('daisy-table[users]')).toBe(false);
    expect(params.get('daisy-table[assets]')).toBe('{"sorting":[]}');
  });

  it('persists an empty filter array when it clears an initial filter', () => {
    const initialState = {
      columnFilters: [{ id: 'status', type: 'select', value: 'active' }],
    };
    const dom = new JSDOM('<div></div>', { url: 'https://example.test/users' });

    global.window = dom.window;
    global.document = dom.window.document;

    writeStateToUrl({ stateKey: 'users', initialState }, null, { columnFilters: [] });

    expect(readStateFromUrl({ stateKey: 'users' })).toEqual({ columnFilters: [] });
  });

  it('persists only nested differences and restores them over normalized initial state', () => {
    const config = normalizeConfig({
      stateKey: 'users',
      persistState: 'url',
      pageSizeOptions: [20, 50],
      columns: [
        { key: 'name' },
        { key: 'status' },
        { key: 'updated_at' },
      ],
      filters: [{ id: 'status', type: 'select' }],
      initialState: {
        pagination: { pageIndex: 0, pageSize: 20 },
        columnVisibility: { status: false },
        columnPinning: { right: ['updated_at'] },
        columnSizing: { name: 160 },
      },
    });
    const state = structuredClone(config.initialState);
    const dom = new JSDOM('<div></div>', { url: 'https://example.test/users?tenant=acme' });

    state.sorting = [{ id: 'name', desc: false }];
    state.globalFilter = 'alice';
    state.columnFilters = [{ id: 'status', type: 'select', value: 'active' }];
    state.pagination.pageIndex = 2;
    state.columnVisibility.name = false;
    state.columnPinning.left = ['name'];
    state.columnSizing.updated_at = 220;

    global.window = dom.window;
    global.document = dom.window.document;

    writeStateToUrl(config, null, state);

    const persistedState = readStateFromUrl(config);
    const restoredState = mergeState(config.initialState, persistedState, config, true);

    expect(persistedState).toEqual({
      sorting: [{ id: 'name', desc: false }],
      globalFilter: 'alice',
      columnFilters: [{ id: 'status', type: 'select', value: 'active' }],
      pagination: { pageIndex: 2 },
      columnVisibility: { name: false },
      columnPinning: { left: ['name'] },
      columnSizing: { updated_at: 220 },
    });
    expect(restoredState).toEqual(state);
  });

  it('persists removal of an initial column customization', () => {
    const config = normalizeConfig({
      stateKey: 'users',
      persistState: 'url',
      columns: [{ key: 'name' }],
      initialState: { columnSizing: { name: 160 } },
    });
    const state = structuredClone(config.initialState);
    const dom = new JSDOM('<div></div>', { url: 'https://example.test/users' });

    state.columnSizing = {};
    global.window = dom.window;
    global.document = dom.window.document;

    writeStateToUrl(config, null, state);

    const persistedState = readStateFromUrl(config);

    expect(persistedState).toEqual({ columnSizing: { name: null } });
    expect(mergeState(config.initialState, persistedState, config, true).columnSizing).toEqual({});
  });

  it('keeps full-state replacement semantics outside URL delta hydration', () => {
    const config = normalizeConfig({
      columns: [{ key: 'name' }],
      initialState: {
        columnSizing: { name: 160 },
        rowSelection: { initial: true },
      },
    });

    const restoredState = mergeState(config.initialState, {
      columnSizing: {},
      rowSelection: { persisted: true },
    }, config);

    expect(restoredState.columnSizing).toEqual({});
    expect(restoredState.rowSelection).toEqual({ persisted: true });
  });

  it('excludes selection and expansion by default and bounds persisted JSON', () => {
    expect(JSON.parse(serializePersistedState({
      sorting: [],
      expanded: { secret: true },
      rowSelection: { secret: true },
    }))).toEqual({ sorting: [] });

    expect(() => serializePersistedState({
      globalFilter: 'x'.repeat(MAX_PERSISTED_STATE_BYTES + 1),
    })).toThrow('byte limit');
  });

  it('interpolates row ids and sends CSRF only to same-origin endpoints', async () => {
    const dom = new JSDOM('<meta name="csrf-token" content="token">', { url: 'https://example.test/users' });
    const fetchMock = vi.fn(async () => ({ ok: true }));

    global.window = dom.window;
    global.document = dom.window.document;
    global.fetch = fetchMock;

    await mutationRequest({ url: '/users/{rowId}' }, 'PATCH', { rowId: 'a/b' }, { rowId: 'a/b' });
    await mutationRequest({ url: 'https://api.example.net/users/{rowId}' }, 'PATCH', { rowId: 'a/b' }, { rowId: 'a/b' });

    const [sameOriginUrl, sameOriginRequest] = fetchMock.mock.calls[0];
    const [, crossOriginRequest] = fetchMock.mock.calls[1];

    expect(sameOriginUrl).toBe('https://example.test/users/a%2Fb');
    expect(sameOriginRequest.headers.get('X-CSRF-TOKEN')).toBe('token');
    expect(crossOriginRequest.headers.has('X-CSRF-TOKEN')).toBe(false);
  });

  it('rejects malformed mutation responses and unsafe transport options', async () => {
    expect(() => requireMutationRow({}, 'id')).toThrow('contain a row object');
    expect(() => requireMutationRow({ row: { name: 'Missing id' } }, 'id')).toThrow('non-empty id');
    expect(requireMutationRow({ row: { id: '1' } }, 'id')).toEqual({ id: '1' });

    const dom = new JSDOM('', { url: 'https://example.test/users' });

    global.window = dom.window;
    global.document = dom.window.document;
    global.fetch = vi.fn();

    await expect(mutationRequest({ url: '/users', credentials: 'unsafe' }, 'PATCH', {}))
      .rejects.toThrow('credentials');
    await expect(mutationRequest({ url: '/users' }, 'TRACE', {}))
      .rejects.toThrow('not supported');
    await expect(mutationRequest({ url: '/users' }, 'GET', {}))
      .rejects.toThrow('mutation method');
  });

  it('rejects raw HTML and malformed structured row actions', () => {
    expect(() => normalizeActions('<button>Unsafe</button>')).toThrow('descriptor');
    expect(() => normalizeActions([{ label: 'Missing action' }])).toThrow('non-empty action');
  });

  it('resolves the editable mutation method using the public precedence contract', () => {
    const endpointMethod = normalizeConfig({
      columns: [{ key: 'name' }],
      rowKey: 'id',
      editable: true,
      editEndpoint: { url: '/users/{rowId}', method: 'PUT' },
    });
    const explicitMethod = normalizeConfig({
      columns: [{ key: 'name' }],
      rowKey: 'id',
      editable: true,
      editEndpoint: { url: '/users/{rowId}', method: 'PUT' },
      editMethod: 'DELETE',
    });
    const structuredMethod = normalizeConfig({
      columns: [{ key: 'name' }],
      rowKey: 'id',
      editable: {
        enabled: true,
        update: {
          method: 'PATCH',
          endpoint: { url: '/users/{rowId}', method: 'PUT' },
        },
      },
    });

    expect(endpointMethod.editable.update.method).toBe('PUT');
    expect(explicitMethod.editable.update.method).toBe('DELETE');
    expect(structuredMethod.editable.update.method).toBe('PATCH');
  });
});
