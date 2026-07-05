import { DEFAULT_SELECTION_STATE } from './utils.js';

function uniqueStringArray(values = []) {
  if (!Array.isArray(values)) {
    return [];
  }

  return [...new Set(
    values
      .filter((value) => value !== null && value !== undefined && String(value) !== '')
      .map((value) => String(value))
  )];
}

function resetSelectionState(selection = {}) {
  return {
    ...DEFAULT_SELECTION_STATE,
    selectedIds: [],
    excludedIds: [],
  };
}

function normalizeSelectionState(selection = {}) {
  const normalized = {
    ...DEFAULT_SELECTION_STATE,
    selectedIds: uniqueStringArray(selection.selectedIds),
    excludedIds: uniqueStringArray(selection.excludedIds),
    allFilteredSelected: selection.allFilteredSelected === true,
    selectionScope: selection.selectionScope === 'filtered' ? 'filtered' : 'page',
    filterSignature: typeof selection.filterSignature === 'string' ? selection.filterSignature : '',
  };

  if (!normalized.allFilteredSelected) {
    normalized.excludedIds = [];
    normalized.selectionScope = 'page';
    normalized.filterSignature = '';
  }

  if (normalized.allFilteredSelected) {
    normalized.selectedIds = [];
    normalized.selectionScope = 'filtered';
  }

  return normalized;
}

function rowSelectionFromSelection(selection = {}) {
  const normalized = normalizeSelectionState(selection);

  if (normalized.allFilteredSelected) {
    return {};
  }

  return Object.fromEntries(normalized.selectedIds.map((id) => [id, true]));
}

export {
  normalizeSelectionState,
  resetSelectionState,
  rowSelectionFromSelection,
  uniqueStringArray,
};
