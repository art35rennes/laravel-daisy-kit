import {
  ALLOWED_CELL_RENDERERS,
  escapeHtml,
  isPlainObject,
} from './utils.js';

const DEFAULT_ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];
const BLOCKED_SCHEMES = ['javascript', 'data', 'vbscript'];

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

  if (column.html === true && renderer === null) {
    renderer = 'html';
  }

  if ((column.type === 'link' || column.type === 'resource-link') && renderer === null) {
    renderer = 'link';
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
  const renderer = column?.cell?.renderer || (column?.html ? 'html' : 'text');
  const value = cell?.getContext?.().renderValue?.() ?? cell?.getValue?.() ?? '';

  if (renderer === 'link') {
    return renderLinkCell(value, tablePolicy, column?.cell ?? {});
  }

  if (renderer === 'html' || renderer === 'blade' || renderer === 'actions') {
    return String(value ?? '');
  }

  return escapeHtml(value ?? '');
}

export {
  BLOCKED_SCHEMES,
  DEFAULT_ALLOWED_SCHEMES,
  getAllowedSchemes,
  getDaisyColumnFromCell,
  getDaisyColumnFromHeader,
  getRowDetailContent,
  isSafeHref,
  normalizeAllowedSchemes,
  normalizeCellDefinition,
  renderCellContent,
  renderLinkCell,
};
