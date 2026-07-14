import { isPlainObject } from './utils.js';

function buildUpdatePayload(editing) {
  const dirtyColumns = Object.entries(editing.dirty)
    .filter(([, dirty]) => dirty === true)
    .map(([column]) => column);

  return {
    rowId: editing.rowId,
    column: editing.columnId,
    value: editing.draft[editing.columnId],
    dirty: Object.fromEntries(dirtyColumns.map((column) => [column, editing.draft[column]])),
  };
}

function normalizeMutationErrors(error, fallbackColumn) {
  if (isPlainObject(error.errors)) {
    return Object.fromEntries(
      Object.entries(error.errors)
        .map(([key, value]) => [key, Array.isArray(value) ? value.join(' ') : String(value)])
    );
  }

  return { [fallbackColumn]: error.message || 'Unable to save this value.' };
}

export {
  buildUpdatePayload,
  normalizeMutationErrors,
};
