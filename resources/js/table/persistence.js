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

function createStateDelta(value, initialValue) {
  if (Object.is(value, initialValue)) {
    return undefined;
  }

  if (Array.isArray(value) || Array.isArray(initialValue)) {
    return JSON.stringify(value) === JSON.stringify(initialValue) ? undefined : value;
  }

  if (isPlainObject(value) && isPlainObject(initialValue)) {
    const keys = new Set([...Object.keys(initialValue), ...Object.keys(value)]);
    const delta = {};

    keys.forEach((key) => {
      if (!Object.hasOwn(value, key)) {
        delta[key] = null;
        return;
      }

      const nestedDelta = createStateDelta(value[key], initialValue[key]);

      if (nestedDelta !== undefined) {
        delta[key] = nestedDelta;
      }
    });

    return Object.keys(delta).length > 0 ? delta : undefined;
  }

  return value;
}

function withoutEmptyColumnFilters(filters) {
  if (!Array.isArray(filters)) {
    return filters;
  }

  return filters.filter((filter) => {
    if (!isPlainObject(filter)) {
      return false;
    }

    if (isPlainObject(filter.value)) {
      return Object.values(filter.value).some((value) => value !== '' && value != null);
    }

    return filter.value !== '' && filter.value != null;
  });
}

function normalizePersistedValue(field, value) {
  return field === 'columnFilters' ? withoutEmptyColumnFilters(value) : value;
}

function selectPersistedState(state, fields, initialState = {}) {
  return Object.fromEntries(
    normalizePersistedStateFields(fields)
      .filter((field) => Object.hasOwn(state, field))
      .map((field) => [field, createStateDelta(
        normalizePersistedValue(field, state[field]),
        normalizePersistedValue(field, initialState[field])
      )])
      .filter(([, value]) => value !== undefined)
  );
}

function serializePersistedState(state, fields, maxBytes = MAX_PERSISTED_STATE_BYTES, initialState = {}) {
  const serialized = JSON.stringify(selectPersistedState(state, fields, initialState));

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
  const serialized = serializePersistedState(
    state,
    config.persistStateFields,
    MAX_PERSISTED_STATE_BYTES,
    config.initialState
  );

  if (serialized === '{}') {
    url.searchParams.delete(parameter);
  } else {
    url.searchParams.set(parameter, serialized);
  }

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
