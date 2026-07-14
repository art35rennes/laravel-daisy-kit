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
import { normalizeConfig } from '../../../resources/js/table/runtime.js';

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
