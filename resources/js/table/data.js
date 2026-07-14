import { isPlainObject } from './utils.js';
import { clonePublicState } from './state.js';

function getRowId(row, rowKey) {
  const value = row?.[rowKey];

  return value === null || value === undefined || String(value).trim() === ''
    ? null
    : String(value);
}

function validateRows(rows, rowKey, subRowsKey = null, method = 'setRows') {
  if (!Array.isArray(rows) || !rows.every((row) => isPlainObject(row))) {
    throw new TypeError(`DaisyTable.${method} expects an array of row objects.`);
  }

  const seen = new Set();

  const visit = (items, path = 'rows') => {
    items.forEach((row, index) => {
      const rowId = getRowId(row, rowKey);

      if (rowId === null) {
        throw new TypeError(`DaisyTable.${method} requires ${path}[${index}] to include a non-empty ${rowKey}.`);
      }

      if (seen.has(rowId)) {
        throw new TypeError(`DaisyTable.${method} requires unique ${rowKey} values. Duplicate value: ${rowId}.`);
      }

      seen.add(rowId);

      if (!subRowsKey || row[subRowsKey] === undefined) {
        return;
      }

      if (!Array.isArray(row[subRowsKey]) || !row[subRowsKey].every((child) => isPlainObject(child))) {
        throw new TypeError(`DaisyTable.${method} expects ${path}[${index}].${subRowsKey} to be an array of row objects.`);
      }

      visit(row[subRowsKey], `${path}[${index}].${subRowsKey}`);
    });
  };

  visit(rows);

  return seen;
}

function snapshotContext(context) {
  return {
    rows: clonePublicState(context.rows),
    rowCount: context.rowCount,
    pageCount: context.pageCount,
    loading: context.loading,
    error: context.error,
    state: clonePublicState(context.state),
    meta: clonePublicState(context.meta || {}),
  };
}

function cloneRows(rows) {
  return clonePublicState(rows);
}

export {
  cloneRows,
  getRowId,
  snapshotContext,
  validateRows,
};
