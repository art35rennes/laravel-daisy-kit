import {
  ALLOWED_CELL_RENDERERS,
  escapeHtml,
  isPlainObject,
} from './utils.js';

const DEFAULT_ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];
const BLOCKED_SCHEMES = ['javascript', 'data', 'vbscript'];
const ACTION_VARIANTS = {
  neutral: 'btn-neutral',
  primary: 'btn-primary',
  secondary: 'btn-secondary',
  accent: 'btn-accent',
  info: 'btn-info',
  success: 'btn-success',
  warning: 'btn-warning',
  error: 'btn-error',
  ghost: 'btn-ghost',
};

function normalizeAllowedSchemes(schemes = []) {
  if (!Array.isArray(schemes)) {
    return [];
  }

  return [...new Set(
    schemes
      .map((scheme) => String(scheme ?? '').trim().toLowerCase().replace(/:$/, ''))
      .filter((scheme) => /^[a-z][a-z0-9+.-]*$/.test(scheme))
      .filter((scheme) => !BLOCKED_SCHEMES.includes(scheme))
  )];
}

function getAllowedSchemes(tablePolicy = {}, cellPolicy = {}) {
  return [...new Set([
    ...DEFAULT_ALLOWED_SCHEMES,
    ...normalizeAllowedSchemes(tablePolicy.allowedSchemes),
    ...normalizeAllowedSchemes(cellPolicy.allowedSchemes),
  ])];
}

function normalizeCellDefinition(column = {}) {
  const rawCell = isPlainObject(column.cell) ? column.cell : {};
  let renderer = typeof rawCell.renderer === 'string' ? rawCell.renderer : null;

  if (typeof column.view === 'string' && column.view !== '') {
    renderer = 'blade';
  }

  if (column.html === true || renderer === 'html') {
    throw new Error('The Daisy table html renderer was removed. Use renderer: trusted-html explicitly.');
  }

  if ((column.type === 'link' || column.type === 'resource-link') && renderer === null) {
    renderer = 'link';
  }

  if (column.type === 'actions' && renderer === null) {
    renderer = 'actions';
  }

  renderer = ALLOWED_CELL_RENDERERS.includes(renderer) ? renderer : 'text';

  return {
    renderer,
    view: typeof rawCell.view === 'string' && rawCell.view !== ''
      ? rawCell.view
      : (typeof column.view === 'string' && column.view !== '' ? column.view : null),
    allowedSchemes: normalizeAllowedSchemes(rawCell.allowedSchemes),
  };
}

function normalizeActions(value) {
  const actions = Array.isArray(value) ? value : (isPlainObject(value) ? [value] : []);

  return actions
    .filter((action) => isPlainObject(action) && typeof action.action === 'string' && action.action.trim() !== '')
    .map((action) => ({
      action: action.action.trim(),
      label: typeof action.label === 'string' ? action.label : action.action.trim(),
      variant: Object.hasOwn(ACTION_VARIANTS, action.variant) ? action.variant : 'ghost',
      disabled: action.disabled === true,
      ariaLabel: typeof action.ariaLabel === 'string' ? action.ariaLabel : '',
    }));
}

function renderActionsCell(value, rowId, columnId) {
  return normalizeActions(value).map((action) => {
    const disabled = action.disabled ? ' disabled aria-disabled="true"' : '';
    const ariaLabel = action.ariaLabel !== '' ? ` aria-label="${escapeHtml(action.ariaLabel)}"` : '';

    return `<button type="button" class="btn btn-xs ${ACTION_VARIANTS[action.variant]}" data-table-row-action="${escapeHtml(action.action)}" data-table-row-id="${escapeHtml(rowId ?? '')}" data-table-column-id="${escapeHtml(columnId ?? '')}"${ariaLabel}${disabled}>${escapeHtml(action.label)}</button>`;
  }).join('');
}

function isSafeHref(href, tablePolicy = {}, cellPolicy = {}) {
  const trimmed = String(href ?? '').trim();

  if (trimmed === '') {
    return false;
  }

  if (/[\u0000-\u001F\u007F]/.test(trimmed)) {
    return false;
  }

  if (trimmed.startsWith('/') || trimmed.startsWith('#') || trimmed.startsWith('?')) {
    return true;
  }

  try {
    const protocol = new URL(trimmed, 'http://localhost').protocol.replace(/:$/, '').toLowerCase();

    if (BLOCKED_SCHEMES.includes(protocol)) {
      return false;
    }

    return getAllowedSchemes(tablePolicy, cellPolicy).includes(protocol);
  } catch (_) {
    return false;
  }
}

function renderLinkCell(value, tablePolicy = {}, cellPolicy = {}) {
  const link = isPlainObject(value)
    ? value
    : { href: value, label: value };
  const href = String(link.href ?? '').trim();
  const label = String(link.label ?? link.href ?? '').trim();
  const target = link.target === '_blank' ? '_blank' : null;
  const rel = target === '_blank' ? ' rel="noopener noreferrer"' : '';
  const targetAttribute = target ? ` target="${escapeHtml(target)}"` : '';
  const externalIcon = target === '_blank' ? ' <span aria-hidden="true">&nearr;</span>' : '';

  if (href === '' || !isSafeHref(href, tablePolicy, cellPolicy)) {
    return escapeHtml(label);
  }

  return `<a href="${escapeHtml(href)}"${targetAttribute}${rel} class="link link-hover">${escapeHtml(label)}${externalIcon}</a>`;
}

function getRowDetailContent(rowData = {}) {
  if (rowData._detailHtml != null) {
    return String(rowData._detailHtml);
  }

  if (rowData.detailHtml != null) {
    return String(rowData.detailHtml);
  }

  if (rowData._detail != null) {
    return escapeHtml(rowData._detail);
  }

  if (rowData.detail != null) {
    return escapeHtml(rowData.detail);
  }

  return '';
}

function getDaisyColumnFromCell(cell) {
  return cell?.column?.columnDef?.meta?.daisyColumn ?? null;
}

function getDaisyColumnFromHeader(header) {
  return header?.column?.columnDef?.meta?.daisyColumn ?? null;
}

function renderCellContent(cell, tablePolicy = {}) {
  const column = getDaisyColumnFromCell(cell);
  const renderer = column?.cell?.renderer || 'text';
  const value = cell?.getContext?.().renderValue?.() ?? cell?.getValue?.() ?? '';

  if (renderer === 'link') {
    return renderLinkCell(value, tablePolicy, column?.cell ?? {});
  }

  if (renderer === 'actions') {
    return renderActionsCell(value, cell?.row?.id, cell?.column?.id ?? column?.key);
  }

  // These renderers cross the explicit trusted HTML boundary configured by the host.
  if (renderer === 'trusted-html' || renderer === 'blade') {
    return String(value ?? '');
  }

  return escapeHtml(value ?? '');
}

export {
  ACTION_VARIANTS,
  BLOCKED_SCHEMES,
  DEFAULT_ALLOWED_SCHEMES,
  getAllowedSchemes,
  getDaisyColumnFromCell,
  getDaisyColumnFromHeader,
  getRowDetailContent,
  isSafeHref,
  normalizeAllowedSchemes,
  normalizeActions,
  normalizeCellDefinition,
  renderCellContent,
  renderActionsCell,
  renderLinkCell,
};
