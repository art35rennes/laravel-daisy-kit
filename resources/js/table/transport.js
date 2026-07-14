import { isPlainObject } from './utils.js';

const ALLOWED_HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
const ALLOWED_MUTATION_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];
const ALLOWED_CREDENTIALS = ['omit', 'same-origin', 'include'];

function normalizeHttpMethod(method, fallback = 'GET') {
  const normalized = typeof method === 'string' && method !== '' ? method.toUpperCase() : fallback;

  if (!ALLOWED_HTTP_METHODS.includes(normalized)) {
    throw new Error(`Daisy Table HTTP method ${normalized} is not supported.`);
  }

  return normalized;
}

function normalizeCredentials(credentials) {
  if (credentials === undefined) {
    return undefined;
  }

  if (!ALLOWED_CREDENTIALS.includes(credentials)) {
    throw new Error('Daisy Table credentials must be omit, same-origin, or include.');
  }

  return credentials;
}

function normalizeMutationMethod(method, fallback = 'PATCH') {
  const normalized = normalizeHttpMethod(method, fallback);

  if (!ALLOWED_MUTATION_METHODS.includes(normalized)) {
    throw new Error(`Daisy Table mutation method ${normalized} is not supported.`);
  }

  return normalized;
}

function interpolateRowId(url, rowId) {
  return String(url).replaceAll('{rowId}', encodeURIComponent(String(rowId)));
}

function isSameOrigin(url, location = window.location) {
  return new URL(url, location.href).origin === location.origin;
}

async function mutationRequest(endpoint, method, payload, options = {}) {
  const url = new URL(interpolateRowId(endpoint.url, options.rowId ?? ''), window.location.href);
  const headers = new Headers({
    Accept: 'application/json',
    'Content-Type': 'application/json',
    ...(endpoint.headers || {}),
  });
  const csrfToken = typeof document !== 'undefined'
    ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    : null;

  if (csrfToken && isSameOrigin(url) && !headers.has('X-CSRF-TOKEN')) {
    headers.set('X-CSRF-TOKEN', csrfToken);
  }

  return fetch(url.toString(), {
    method: normalizeMutationMethod(method),
    headers,
    credentials: normalizeCredentials(endpoint.credentials),
    body: JSON.stringify(payload),
    signal: options.signal,
  });
}

function requireMutationRow(body, rowKey) {
  if (!isPlainObject(body?.row)) {
    throw new Error('Daisy Table mutation responses must contain a row object.');
  }

  const rowId = body.row[rowKey];

  if (rowId === null || rowId === undefined || String(rowId).trim() === '') {
    throw new Error(`Daisy Table mutation responses must contain a non-empty ${rowKey}.`);
  }

  return body.row;
}

export {
  ALLOWED_CREDENTIALS,
  ALLOWED_HTTP_METHODS,
  ALLOWED_MUTATION_METHODS,
  interpolateRowId,
  isSameOrigin,
  mutationRequest,
  normalizeCredentials,
  normalizeHttpMethod,
  normalizeMutationMethod,
  requireMutationRow,
};
