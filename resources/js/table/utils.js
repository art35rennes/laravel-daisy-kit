const DEFAULT_PAGE_SIZE_OPTIONS = [10, 25, 50];
const DEFAULT_MODE = 'client';
const DEFAULT_METHOD = 'GET';
const DEFAULT_SERVER_ADAPTER = 'default';
const DEFAULT_PERSIST_STATE = false;
const DEFAULT_GLOBAL_FILTER_KEY = 'global';
const DEFAULT_SEARCH_DEBOUNCE_MS = 500;
const DEFAULT_FILTER_DEBOUNCE_MS = 500;
const DEFAULT_MIN_SEARCH_CHARS = 3;
const DEFAULT_SELECTION_STATE = {
  selectedIds: [],
  excludedIds: [],
  allFilteredSelected: false,
  selectionScope: 'page',
  filterSignature: '',
};
const ALLOWED_FILTER_TYPES = ['text', 'select', 'boolean', 'date', 'date-range'];
const ALLOWED_ALIGNMENTS = ['left', 'center', 'right'];
const ALLOWED_VERTICAL_ALIGNMENTS = ['top', 'middle', 'bottom'];
const ALLOWED_TRUNCATE_VALUES = ['line', 2, 3];
const ALLOWED_CELL_RENDERERS = ['text', 'trusted-html', 'blade', 'link', 'actions'];

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

function parseJsonParam(value, fallback) {
  if (!value) {
    return fallback;
  }

  try {
    return JSON.parse(value);
  } catch (_) {
    return fallback;
  }
}

export {
  ALLOWED_ALIGNMENTS,
  ALLOWED_CELL_RENDERERS,
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
};
