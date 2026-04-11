function collectSelectedIdsFromInputs(inputs) {
  return inputs
    .filter((input) => input.checked)
    .map((input) => String(input.dataset?.rowId ?? ''))
    .filter(Boolean);
}

function computeSelectAllState(inputs) {
  const selectableInputs = inputs.filter((input) => !input.disabled);
  const selectedCount = selectableInputs.filter((input) => input.checked).length;

  return {
    checked: selectableInputs.length > 0 && selectedCount === selectableInputs.length,
    indeterminate: selectedCount > 0 && selectedCount < selectableInputs.length,
  };
}

function applySingleSelection(inputs, activeInput) {
  inputs.forEach((input) => {
    input.checked = input === activeInput;
  });
}

function setInputsChecked(inputs, checked) {
  inputs.forEach((input) => {
    if (!input.disabled) {
      input.checked = checked;
    }
  });
}

function buildSelectionDetail(inputs) {
  return {
    selected: collectSelectedIdsFromInputs(inputs),
  };
}

function resolveNextSelectAllState(selectAll) {
  if (!selectAll) {
    return false;
  }

  return selectAll.indeterminate ? true : !selectAll.checked;
}

function syncSelectAllState(selectAll, inputs) {
  if (!selectAll) {
    return { checked: false, indeterminate: false };
  }

  const state = computeSelectAllState(inputs);
  selectAll.checked = state.checked;
  selectAll.indeterminate = state.indeterminate;
  selectAll.setAttribute('aria-checked', state.indeterminate ? 'mixed' : String(state.checked));

  return state;
}

function updateRowState(input) {
  const row = input.closest?.('tr');

  if (!row) {
    return;
  }

  row.classList.toggle('bg-base-200', !!input.checked);
  row.setAttribute('aria-selected', input.checked ? 'true' : 'false');
}

function emitSelection(root, inputs) {
  root.dispatchEvent(
    new CustomEvent('advanced-table:selection', {
      detail: buildSelectionDetail(inputs),
      bubbles: true,
    })
  );
}

function parseClientOptions(table) {
  try {
    return JSON.parse(table.dataset.clientOptions ?? '{}');
  } catch (_) {
    return {};
  }
}

function getUrlStateFromSearch(search, options = {}) {
  const params = new URLSearchParams(search || '');
  const queryBuilder = options.queryBuilder === true;
  const searchParameter = options.searchParameter || 'search';
  const sortParameter = options.sortParameter || 'sort';
  const pageParameter = options.pageParameter || 'page';
  const perPageParameter = options.perPageParameter || 'per_page';

  const searchTerm = queryBuilder
    ? (params.get(`filter[${searchParameter}]`) || '')
    : (params.get(searchParameter) || '');

  const sortToken = params.get(sortParameter) || '';
  const columnFilters = {};

  params.forEach((value, key) => {
    const match = key.match(/^filter\[(.+)\]$/);
    if (match) {
      const filterKey = match[1];
      if (!queryBuilder || filterKey !== searchParameter) {
        columnFilters[filterKey] = value;
      }
    } else if (!queryBuilder && ![searchParameter, sortParameter, pageParameter, perPageParameter].includes(key)) {
      columnFilters[key] = value;
    }
  });

  return {
    searchTerm,
    columnFilters,
    sortBy: sortToken ? sortToken.replace(/^-/, '') : null,
    sortDirection: sortToken.startsWith('-') ? 'desc' : 'asc',
    page: Math.max(1, Number(params.get(pageParameter) || 1)),
    pageSize: Math.max(1, Number(params.get(perPageParameter) || options.pageSize || 10)),
  };
}

function updateClientUrl(state, options = {}) {
  if (typeof window === 'undefined' || !window.history?.replaceState) {
    return;
  }

  const url = new URL(window.location.href);
  const params = url.searchParams;
  const queryBuilder = options.queryBuilder === true;
  const searchParameter = options.searchParameter || 'search';
  const sortParameter = options.sortParameter || 'sort';
  const pageParameter = options.pageParameter || 'page';
  const perPageParameter = options.perPageParameter || 'per_page';

  if (queryBuilder) {
    params.delete(`filter[${searchParameter}]`);
  } else {
    params.delete(searchParameter);
  }

  if (state.searchTerm) {
    params.set(queryBuilder ? `filter[${searchParameter}]` : searchParameter, state.searchTerm);
  }

  params.delete(sortParameter);
  if (state.sortBy) {
    params.set(sortParameter, state.sortDirection === 'desc' ? `-${state.sortBy}` : state.sortBy);
  }

  params.delete(pageParameter);
  if (state.page > 1) {
    params.set(pageParameter, String(state.page));
  }

  params.delete(perPageParameter);
  if (state.pageSize) {
    params.set(perPageParameter, String(state.pageSize));
  }

  Object.keys(state.columnFilters || {}).forEach((key) => {
    const filterParam = queryBuilder ? `filter[${key}]` : key;
    params.delete(filterParam);

    const value = state.columnFilters[key];
    if (value) {
      params.set(filterParam, value);
    }
  });

  window.history.replaceState({}, '', `${url.pathname}${params.toString() ? `?${params.toString()}` : ''}${url.hash}`);
}

function updateSelectedSummary(root, selectedCount) {
  const summary = root.querySelector('[data-selected-summary]');
  if (!summary) return;

  if (selectedCount > 0) {
    summary.classList.remove('hidden');
    const suffix = summary.textContent?.trim().split(' ').slice(1).join(' ') || 'selected';
    summary.textContent = `${selectedCount} ${suffix}`;
  } else {
    summary.classList.add('hidden');
  }
}

function createRowRecord(row) {
  const cells = Array.from(row.querySelectorAll('[data-column-key], [data-filter-key], [data-sort-key]'));
  const cellMap = new Map();
  const searchableText = [];

  cells.forEach((cell) => {
    const key = cell.dataset.columnKey || cell.dataset.filterKey || cell.dataset.sortKey;
    if (!key) return;

    const value = (cell.dataset.value || cell.textContent || '').trim();
    searchableText.push(value.toLowerCase());

    cellMap.set(key, {
      value,
      sortKey: cell.dataset.sortKey || key,
      filterKey: cell.dataset.filterKey || key,
    });
  });

  return {
    element: row,
    cells: cellMap,
    searchableText: searchableText.join(' '),
  };
}

function applyClientTableState(root, table, state) {
  const body = table.querySelector('tbody');
  if (!body) return;

  const records = Array.from(body.querySelectorAll('tr[data-table-row]')).map(createRowRecord);
  const globalTerm = (state.searchTerm || '').trim().toLowerCase();

  let filtered = records.filter((record) => {
    if (globalTerm && !record.searchableText.includes(globalTerm)) {
      return false;
    }

    for (const [key, term] of Object.entries(state.columnFilters)) {
      const value = (record.cells.get(key)?.value || '').toLowerCase();
      const normalizedTerm = String(term || '').trim().toLowerCase();
      if (normalizedTerm && !value.includes(normalizedTerm)) {
        return false;
      }
    }

    return true;
  });

  if (state.sortBy) {
    filtered = [...filtered].sort((left, right) => {
      const leftValue = (left.cells.get(state.sortBy)?.value || '').toLowerCase();
      const rightValue = (right.cells.get(state.sortBy)?.value || '').toLowerCase();
      const comparison = leftValue.localeCompare(rightValue, undefined, { numeric: true, sensitivity: 'base' });
      return state.sortDirection === 'desc' ? comparison * -1 : comparison;
    });
  }

  const pageSize = Math.max(1, Number(state.pageSize) || 10);
  const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
  state.page = Math.min(Math.max(1, state.page), pageCount);

  const start = (state.page - 1) * pageSize;
  const end = start + pageSize;
  const visible = new Set(filtered.slice(start, end).map((record) => record.element));

  records.forEach((record) => {
    record.element.style.display = visible.has(record.element) ? '' : 'none';
  });

  const pageInfo = root.querySelector('[data-table-page-info]');
  if (pageInfo) {
    if (filtered.length === 0) {
      pageInfo.textContent = '0 result';
    } else {
      pageInfo.textContent = `Showing ${start + 1} to ${Math.min(end, filtered.length)} of ${filtered.length} results`;
    }
  }

  const pageIndicator = root.querySelector('[data-table-page-indicator]');
  if (pageIndicator) {
    pageIndicator.textContent = `${state.page} / ${pageCount}`;
  }

  const prevButton = root.querySelector('[data-table-page-prev]');
  const nextButton = root.querySelector('[data-table-page-next]');
  if (prevButton) prevButton.disabled = state.page <= 1;
  if (nextButton) nextButton.disabled = state.page >= pageCount;

  const visibleSelectionInputs = Array.from(root.querySelectorAll('[data-row-select]')).filter((input) => {
    const row = input.closest('tr');
    return row && row.style.display !== 'none';
  });

  root.__visibleSelectionInputs = visibleSelectionInputs;
  syncSelectAllState(root.querySelector('[data-select-all]'), visibleSelectionInputs);
  updateSelectedSummary(root, collectSelectedIdsFromInputs(Array.from(root.querySelectorAll('[data-row-select]'))).length);
}

function initClientTable(root, table) {
  const options = parseClientOptions(table);
  const urlState = getUrlStateFromSearch(typeof window !== 'undefined' ? window.location.search : '', options);
  const state = {
    searchTerm: urlState.searchTerm || '',
    columnFilters: { ...(urlState.columnFilters || {}) },
    sortBy: urlState.sortBy || options.sortBy || null,
    sortDirection: urlState.sortBy ? urlState.sortDirection : (options.sortDirection || 'asc'),
    page: urlState.page || 1,
    pageSize: Number(urlState.pageSize || options.pageSize) || 10,
  };

  const searchInput = root.querySelector('[data-table-search]');
  if (searchInput) {
    searchInput.value = state.searchTerm;
  }

  root.querySelectorAll('[data-column-filter]').forEach((control) => {
    const key = control.dataset.columnFilter;
    if (key && state.columnFilters[key] != null) {
      control.value = state.columnFilters[key];
    }
  });

  root.querySelector('[data-table-search]')?.addEventListener('input', (event) => {
    state.searchTerm = event.target.value || '';
    state.page = 1;
    applyClientTableState(root, table, state);
    updateClientUrl(state, options);
  });

  root.querySelectorAll('[data-column-filter]').forEach((control) => {
    const key = control.dataset.columnFilter;
    const eventName = control.tagName === 'SELECT' ? 'change' : 'input';

    control.addEventListener(eventName, (event) => {
      state.columnFilters[key] = event.target.value || '';
      state.page = 1;
      applyClientTableState(root, table, state);
      updateClientUrl(state, options);
    });
  });

  root.querySelectorAll('[data-client-sort-key]').forEach((button) => {
    button.addEventListener('click', () => {
      const key = button.dataset.clientSortKey;
      if (!key) return;

      if (state.sortBy === key) {
        state.sortDirection = state.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        state.sortBy = key;
        state.sortDirection = 'asc';
      }

      state.page = 1;
      applyClientTableState(root, table, state);
      updateClientUrl(state, options);
    });
  });

  root.querySelector('[data-table-page-prev]')?.addEventListener('click', () => {
    state.page -= 1;
    applyClientTableState(root, table, state);
    updateClientUrl(state, options);
  });

  root.querySelector('[data-table-page-next]')?.addEventListener('click', () => {
    state.page += 1;
    applyClientTableState(root, table, state);
    updateClientUrl(state, options);
  });

  root.querySelector('[data-table-page-size-select]')?.addEventListener('change', (event) => {
    state.pageSize = Number(event.target.value) || state.pageSize;
    state.page = 1;
    applyClientTableState(root, table, state);
    updateClientUrl(state, options);
  });

  applyClientTableState(root, table, state);
  updateClientUrl(state, options);
}

function initSimpleClientTable(root) {
  if (!root || root.__simpleTableInit) return;

  const table = root.querySelector('table');
  const body = table?.querySelector('tbody');
  if (!table || !body) return;

  root.__simpleTableInit = true;
  const scope = root.closest('[data-simple-table-root]') || root.parentElement;

  const rows = Array.from(body.querySelectorAll('tr'));
  const state = {
    page: 1,
    pageSize: Number(root.dataset.tablePageSize || 10),
  };
  const pageParameter = root.dataset.tablePageParameter || 'page';
  const perPageParameter = root.dataset.tablePerPageParameter || 'per_page';
  if (typeof window !== 'undefined') {
    const params = new URLSearchParams(window.location.search);
    state.page = Math.max(1, Number(params.get(pageParameter) || 1));
    state.pageSize = Math.max(1, Number(params.get(perPageParameter) || state.pageSize));
  }

  const updateUrl = () => {
    if (typeof window === 'undefined' || !window.history?.replaceState) {
      return;
    }

    const url = new URL(window.location.href);
    url.searchParams.delete(pageParameter);
    url.searchParams.delete(perPageParameter);
    if (state.page > 1) {
      url.searchParams.set(pageParameter, String(state.page));
    }
    if (state.pageSize) {
      url.searchParams.set(perPageParameter, String(state.pageSize));
    }
    window.history.replaceState({}, '', `${url.pathname}${url.searchParams.toString() ? `?${url.searchParams.toString()}` : ''}${url.hash}`);
  };

  const apply = () => {
    const pageCount = Math.max(1, Math.ceil(rows.length / state.pageSize));
    state.page = Math.min(Math.max(1, state.page), pageCount);
    const start = (state.page - 1) * state.pageSize;
    const end = start + state.pageSize;

    rows.forEach((row, index) => {
      row.style.display = index >= start && index < end ? '' : 'none';
    });

    const info = scope?.querySelector('[data-table-page-info]');
    if (info) {
      info.textContent = rows.length === 0 ? '0 result' : `Showing ${start + 1} to ${Math.min(end, rows.length)} of ${rows.length} results`;
    }

    const indicator = scope?.querySelector('[data-table-page-indicator]');
    if (indicator) indicator.textContent = `${state.page} / ${pageCount}`;

    const prevButton = scope?.querySelector('[data-table-page-prev]');
    const nextButton = scope?.querySelector('[data-table-page-next]');
    if (prevButton) prevButton.disabled = state.page <= 1;
    if (nextButton) nextButton.disabled = state.page >= pageCount;

    const pageSizeSelect = scope?.querySelector('[data-table-page-size-select]');
    if (pageSizeSelect) {
      pageSizeSelect.value = String(state.pageSize);
    }
  };

  scope?.querySelector('[data-table-page-prev]')?.addEventListener('click', () => {
    state.page -= 1;
    apply();
    updateUrl();
  });
  scope?.querySelector('[data-table-page-next]')?.addEventListener('click', () => {
    state.page += 1;
    apply();
    updateUrl();
  });
  scope?.querySelector('[data-table-page-size-select]')?.addEventListener('change', (event) => {
    state.pageSize = Number(event.target.value) || state.pageSize;
    state.page = 1;
    apply();
    updateUrl();
  });

  apply();
  updateUrl();
}

function initAdvancedTable(root) {
  if (!root || root.__advancedTableInit) {
    return;
  }

  const table = root.matches?.('[data-advanced-table="1"]')
    ? root
    : root.querySelector?.('[data-advanced-table="1"]');

  if (!table) {
    return;
  }

  root.__advancedTableInit = true;

  const mode = table.dataset.selection ?? 'none';
  const tableMode = table.dataset.tableMode ?? 'server';
  const getRowInputs = () => Array.from(table.querySelectorAll('[data-row-select]'));
  const getVisibleRowInputs = () => root.__visibleSelectionInputs || getRowInputs().filter((input) => {
    const row = input.closest('tr');
    return row && row.style.display !== 'none';
  });
  const getSelectAllInput = () => table.querySelector('[data-select-all]');
  const syncRows = () => {
    getRowInputs().forEach(updateRowState);
  };
  const syncSelectAll = () => {
    syncSelectAllState(getSelectAllInput(), tableMode === 'client' ? getVisibleRowInputs() : getRowInputs());
    updateSelectedSummary(root, collectSelectedIdsFromInputs(getRowInputs()).length);
  };

  if (tableMode === 'client') {
    initClientTable(root, table);
  }

  syncRows();
  syncSelectAll();

  table.addEventListener('click', (event) => {
    const selectAll = getSelectAllInput();
    if (!selectAll) {
      return;
    }

    const target = event.target;
    if (!(target instanceof Element)) {
      return;
    }

    const clickedLabel = target.closest('label');
    const clickedSelectAll = target === selectAll || clickedLabel?.contains(selectAll);

    if (!clickedSelectAll) {
      return;
    }

    event.preventDefault();

    const rowInputs = getRowInputs();
    const scopeInputs = tableMode === 'client' ? getVisibleRowInputs() : rowInputs;
    const nextChecked = resolveNextSelectAllState(selectAll);

    selectAll.indeterminate = false;
    selectAll.checked = nextChecked;

    if (mode === 'multiple') {
      setInputsChecked(scopeInputs, nextChecked);
    }

    syncRows();
    syncSelectAll();
    emitSelection(root, rowInputs);
  });

  table.addEventListener('change', (event) => {
    const input = event.target;

    if (!input || typeof input.matches !== 'function') {
      return;
    }

    const rowInputs = getRowInputs();
    const scopeInputs = tableMode === 'client' ? getVisibleRowInputs() : rowInputs;

    if (input.matches('[data-select-all]')) {
      if (mode === 'multiple') {
        setInputsChecked(scopeInputs, input.checked);
      }

      syncRows();
      syncSelectAll();
      emitSelection(root, rowInputs);
      return;
    }

    if (!input.matches('[data-row-select]')) {
      return;
    }

    if (mode === 'single') {
      applySingleSelection(rowInputs, input);
    }

    syncRows();
    syncSelectAll();
    emitSelection(root, rowInputs);
  });
}

function initAllAdvancedTables() {
  if (typeof document === 'undefined') {
    return;
  }

  document.querySelectorAll('[data-advanced-table="1"]').forEach((table) => {
    initAdvancedTable(table);
  });

  document.querySelectorAll('[data-simple-table="1"][data-table-pagination-mode="client"]').forEach((tableRoot) => {
    initSimpleClientTable(tableRoot);
  });
}

if (typeof window !== 'undefined') {
  window.DaisyAdvancedTable = {
    init: initAdvancedTable,
    initAll: initAllAdvancedTables,
  };
}

if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllAdvancedTables);
  } else {
    initAllAdvancedTables();
  }
}

export default initAdvancedTable;
export {
  applySingleSelection,
  applyClientTableState,
  buildSelectionDetail,
  collectSelectedIdsFromInputs,
  computeSelectAllState,
  initAdvancedTable,
  initAllAdvancedTables,
  getUrlStateFromSearch,
  parseClientOptions,
  resolveNextSelectAllState,
  setInputsChecked,
  syncSelectAllState,
  updateClientUrl,
};
