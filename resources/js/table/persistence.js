import { isPlainObject } from './utils.js';

const MAX_PERSISTED_STATE_BYTES = 4096;
const DEFAULT_PERSISTED_STATE_FIELDS = [
  'sorting',
  'globalFilter',
  'columnFilters',
  'pagination',
  'columnVisibility',
  'columnOrder',
  'columnPinning',
  'columnSizing',
];
const OPTIONAL_PERSISTED_STATE_FIELDS = ['expanded', 'rowSelection'];
const ALLOWED_PERSISTED_STATE_FIELDS = [
  ...DEFAULT_PERSISTED_STATE_FIELDS,
  ...OPTIONAL_PERSISTED_STATE_FIELDS,
];

function normalizePersistedStateFields(fields) {
  if (!Array.isArray(fields)) {
    return [...DEFAULT_PERSISTED_STATE_FIELDS];
  }

  return [...new Set(fields.filter((field) => ALLOWED_PERSISTED_STATE_FIELDS.includes(field)))];
}

function getPersistenceNamespace(config, root = null) {
  const key = config.stateKey || root?.id;

  return typeof key === 'string' && key.trim() !== '' ? key.trim() : null;
}

function getPersistenceParameter(config, root = null) {
  const namespace = getPersistenceNamespace(config, root);

  return namespace ? `daisy-table[${namespace}]` : null;
}

function selectPersistedState(state, fields) {
  return Object.fromEntries(
    normalizePersistedStateFields(fields)
      .filter((field) => Object.hasOwn(state, field))
      .map((field) => [field, state[field]])
  );
}

function serializePersistedState(state, fields, maxBytes = MAX_PERSISTED_STATE_BYTES) {
  const serialized = JSON.stringify(selectPersistedState(state, fields));

  if (new TextEncoder().encode(serialized).byteLength > maxBytes) {
    throw new Error(`Daisy Table persisted state exceeds the ${maxBytes} byte limit.`);
  }

  return serialized;
}

function parsePersistedState(raw, maxBytes = MAX_PERSISTED_STATE_BYTES) {
  if (typeof raw !== 'string' || raw === '' || new TextEncoder().encode(raw).byteLength > maxBytes) {
    return {};
  }

  try {
    const parsed = JSON.parse(raw);

    return isPlainObject(parsed) ? parsed : {};
  } catch (_) {
    return {};
  }
}

function readStateFromUrl(config, root = null, location = window.location) {
  const parameter = getPersistenceParameter(config, root);

  if (!parameter) {
    return {};
  }

  return parsePersistedState(new URLSearchParams(location.search).get(parameter));
}

function writeStateToUrl(config, root, state, history = window.history, location = window.location) {
  const parameter = getPersistenceParameter(config, root);

  if (!parameter) {
    return false;
  }

  const url = new URL(location.href);
  const serialized = serializePersistedState(state, config.persistStateFields);

  url.searchParams.set(parameter, serialized);
  history.replaceState({}, '', url);

  return true;
}

export {
  ALLOWED_PERSISTED_STATE_FIELDS,
  DEFAULT_PERSISTED_STATE_FIELDS,
  MAX_PERSISTED_STATE_BYTES,
  getPersistenceNamespace,
  getPersistenceParameter,
  normalizePersistedStateFields,
  parsePersistedState,
  readStateFromUrl,
  selectPersistedState,
  serializePersistedState,
  writeStateToUrl,
};
