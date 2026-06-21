import { normalizeBlueprintTheme } from './theme.js';

const themeNames = ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'];

const themeHostRules = themeNames.map((theme) => `
  :host([data-blueprint-theme="${theme}"]) {
    --daisy-blueprint-node-theme: var(--color-${theme});
    --daisy-blueprint-node-theme-content: var(--color-${theme}-content);
  }
`).join('');

const nodeShadowCss = `
  ${themeHostRules}

  :host {
    --socket-size: 18px !important;
    --socket-margin: 0px !important;
    background: var(--color-base-100) !important;
    border-color: color-mix(in oklch, var(--daisy-blueprint-node-theme) 42%, var(--color-base-300)) !important;
    border-radius: 8px !important;
    box-shadow: 0 8px 18px color-mix(in oklch, var(--color-base-content) 8%, transparent) !important;
    box-sizing: border-box !important;
    overflow: visible !important;
  }

  .title {
    align-items: center !important;
    background: var(--daisy-blueprint-node-theme) !important;
    border-bottom: 1px solid color-mix(in oklch, var(--daisy-blueprint-node-theme) 42%, var(--color-base-300)) !important;
    border-radius: 6px 6px 0 0 !important;
    color: var(--daisy-blueprint-node-theme-content) !important;
    display: flex !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    gap: 8px !important;
    justify-content: space-between !important;
    line-height: 1.2 !important;
    min-height: 42px !important;
    padding: 8px 12px !important;
    text-shadow: none !important;
  }

  :host([data-blueprint-display="minimal"]) {
    width: 188px !important;
  }

  :host([data-blueprint-display="minimal"]) .title {
    border-radius: 6px !important;
    min-height: 46px !important;
    padding: 10px 10px 10px 12px !important;
  }

  [data-blueprint-title-label] {
    min-width: 0 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  [data-blueprint-title-icon] {
    align-items: center !important;
    background: color-mix(in oklch, var(--daisy-blueprint-node-theme-content) 18%, transparent) !important;
    border: 1px solid color-mix(in oklch, var(--daisy-blueprint-node-theme-content) 28%, transparent) !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    flex: 0 0 26px !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    height: 26px !important;
    justify-content: center !important;
    line-height: 1 !important;
    width: 26px !important;
  }

  .input,
  .output {
    align-items: center !important;
    background: transparent !important;
    border: 0 !important;
    box-sizing: border-box !important;
    display: flex !important;
    gap: 6px !important;
    height: 24px !important;
    min-height: 24px !important;
    padding: 0 !important;
    position: absolute !important;
    top: calc(50% - 12px) !important;
    width: 22px !important;
    z-index: 3 !important;
  }

  .input {
    left: -11px !important;
    justify-content: flex-start !important;
    text-align: left !important;
  }

  .output {
    right: -11px !important;
    justify-content: flex-end !important;
    text-align: right !important;
  }

  .input::after,
  .output::before {
    background: var(--daisy-blueprint-node-theme) !important;
    border-radius: 999px !important;
    content: "" !important;
    height: 3px !important;
    opacity: 0.82 !important;
    position: absolute !important;
    top: calc(50% - 1.5px) !important;
    width: 9px !important;
    z-index: 0 !important;
  }

  .input::after {
    left: 8px !important;
  }

  .output::before {
    right: 8px !important;
  }

  .input[data-blueprint-port-count="2"][data-blueprint-port-index="0"],
  .output[data-blueprint-port-count="2"][data-blueprint-port-index="0"] {
    top: calc(50% - 25px) !important;
  }

  .input[data-blueprint-port-count="2"][data-blueprint-port-index="1"],
  .output[data-blueprint-port-count="2"][data-blueprint-port-index="1"] {
    top: calc(50% + 1px) !important;
  }

  .input[data-blueprint-port-count="3"][data-blueprint-port-index="0"],
  .output[data-blueprint-port-count="3"][data-blueprint-port-index="0"] {
    top: calc(50% - 38px) !important;
  }

  .input[data-blueprint-port-count="3"][data-blueprint-port-index="1"],
  .output[data-blueprint-port-count="3"][data-blueprint-port-index="1"] {
    top: calc(50% - 12px) !important;
  }

  .input[data-blueprint-port-count="3"][data-blueprint-port-index="2"],
  .output[data-blueprint-port-count="3"][data-blueprint-port-index="2"] {
    top: calc(50% + 14px) !important;
  }

  :host([data-blueprint-display="minimal"]) .input,
  :host([data-blueprint-display="minimal"]) .output {
    top: calc(50% - 12px) !important;
  }

  .input-socket,
  .output-socket {
    display: inline-flex !important;
    flex: 0 0 14px !important;
    height: 14px !important;
    margin: 0 !important;
    position: relative !important;
    transform: none !important;
    width: 14px !important;
    z-index: 1 !important;
  }

  .input-title,
  .output-title {
    display: none !important;
  }

  :host([data-blueprint-display="minimal"]) .input-title,
  :host([data-blueprint-display="minimal"]) .output-title {
    font-size: 10px !important;
    opacity: 0.65 !important;
  }

  .control,
  .input-control {
    display: none !important;
  }

  .daisy-blueprint-preview:empty {
    display: none !important;
  }

  .daisy-blueprint-preview {
    background: var(--color-base-100) !important;
    border-radius: 0 0 7px 7px !important;
    border-top: 1px solid color-mix(in oklch, var(--color-base-300) 78%, transparent) !important;
    display: grid !important;
    gap: 0 !important;
    padding: 7px 0 !important;
  }

  .daisy-blueprint-preview-row {
    align-items: center !important;
    display: grid !important;
    gap: 10px !important;
    grid-template-columns: minmax(4.5rem, 0.72fr) minmax(0, 1fr) !important;
    min-height: 30px !important;
    padding: 4px 22px !important;
  }

  .daisy-blueprint-preview-row:nth-child(even) {
    background: color-mix(in oklch, var(--color-base-200) 54%, transparent) !important;
  }

  .daisy-blueprint-preview-label {
    color: var(--color-base-content) !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    opacity: 0.55 !important;
    overflow: hidden !important;
    text-transform: uppercase !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  .daisy-blueprint-preview-value {
    color: var(--color-base-content) !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    overflow: hidden !important;
    text-align: right !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  :host([data-blueprint-display="minimal"]) .daisy-blueprint-preview {
    display: none !important;
  }
`;

const socketShadowCss = `
  ${themeHostRules}

  :host {
    --socket-size: 14px !important;
    --socket-margin: 0px !important;
    --socket-color: var(--daisy-blueprint-node-theme) !important;
    display: block !important;
    height: 14px !important;
    width: 14px !important;
  }

  .hoverable {
    border-radius: 999px !important;
    box-sizing: border-box !important;
    display: block !important;
    height: 14px !important;
    padding: 0 !important;
    width: 14px !important;
  }

  .styles {
    box-sizing: border-box !important;
    height: 14px !important;
    width: 14px !important;
  }
`;

const styleSheetCache = new Map();

export function decorateNodeView(root, area, node) {
  const element = area.nodeViews.get(node.id)?.element;

  if (!element) {
    return;
  }

  const theme = normalizeBlueprintTheme(node.__blueprint?.theme);

  element.dataset.blueprintNodeType = node.__blueprint?.type || 'node';
  element.dataset.blueprintDisplay = node.__blueprint?.display || 'detailed';
  element.dataset.blueprintIcon = node.__blueprint?.icon || '';
  element.dataset.blueprintLabel = node.label || '';
  element.dataset.blueprintTheme = theme;
  decorateNodeElement(root, element, theme, node);
  window.requestAnimationFrame?.(() => decorateNodeElement(root, element, theme, node));
  window.setTimeout(() => decorateNodeElement(root, element, theme, node), 50);
}

export function decorateNodeViews(root, area, editor) {
  editor.getNodes().forEach((node) => decorateNodeView(root, area, node));
}

function decorateNodeElement(root, element, theme, node) {
  element.querySelectorAll('rete-node').forEach((nodeElement) => {
    nodeElement.dataset.blueprintTheme = theme;
    nodeElement.dataset.blueprintDisplay = element.dataset.blueprintDisplay || 'detailed';
    nodeElement.dataset.blueprintIcon = element.dataset.blueprintIcon || '';
    nodeElement.dataset.blueprintLabel = element.dataset.blueprintLabel || '';
    decorateNodeShadow(nodeElement, node);
  });
}

function decorateNodeShadow(nodeElement, node) {
  const shadow = nodeElement.shadowRoot;

  if (!shadow) {
    return;
  }

  adoptShadowStyles(shadow, 'node', nodeShadowCss);
  decoratePortRows(shadow);

  const title = shadow.querySelector('.title');

  if (title) {
    decorateNodeTitle(title, nodeElement);
  }

  renderNodePreview(shadow, node);

  shadow.querySelectorAll('.input-socket rete-ref, .output-socket rete-ref').forEach((socketRef) => {
    decorateSocketRef(socketRef, nodeElement.dataset.blueprintTheme);
  });
}

function decoratePortRows(shadow) {
  decoratePortRowGroup(shadow.querySelectorAll('.input'));
  decoratePortRowGroup(shadow.querySelectorAll('.output'));
}

function decoratePortRowGroup(rows) {
  const count = rows.length;

  rows.forEach((row, index) => {
    row.dataset.blueprintPortIndex = String(index);
    row.dataset.blueprintPortCount = String(Math.min(count, 3));
  });
}

function decorateNodeTitle(title, nodeElement) {
  const host = nodeElement.closest('[data-blueprint-display]');
  const icon = host?.dataset.blueprintIcon || nodeElement.dataset.blueprintIcon || '';
  const labelText = host?.dataset.blueprintLabel || nodeElement.dataset.blueprintLabel || '';

  if (title.dataset.blueprintTitleDecorated !== 'true') {
    title.dataset.blueprintTitleDecorated = 'true';
    title.textContent = '';

    const label = document.createElement('span');

    label.dataset.blueprintTitleLabel = 'true';
    label.textContent = labelText;

    title.append(label);
  }

  const label = title.querySelector('[data-blueprint-title-label]');

  if (label) {
    label.textContent = labelText;
  }

  title.querySelector('[data-blueprint-title-icon]')?.remove();

  if (!icon) {
    return;
  }

  const iconElement = document.createElement('span');

  iconElement.dataset.blueprintTitleIcon = 'true';
  iconElement.textContent = icon.slice(0, 3);

  title.append(iconElement);
}

function renderNodePreview(shadow, node) {
  shadow.querySelector('.daisy-blueprint-preview')?.remove();

  if (!node || node.__blueprint?.display === 'minimal') {
    return;
  }

  const previewFields = node.__blueprint?.previewFields || [];

  if (!previewFields.length) {
    return;
  }

  const preview = document.createElement('div');

  preview.className = 'daisy-blueprint-preview';
  preview.dataset.blueprintNodePreview = 'true';

  previewFields.forEach((field) => {
    const value = node.__blueprint?.data?.[field.key];

    if (value === null || value === undefined || typeof value === 'object') {
      return;
    }

    const row = document.createElement('div');
    const label = document.createElement('span');
    const output = document.createElement('span');

    row.className = 'daisy-blueprint-preview-row';
    label.className = 'daisy-blueprint-preview-label';
    output.className = 'daisy-blueprint-preview-value';
    label.textContent = field.label || field.key;
    output.textContent = formatPreviewValue(value);
    row.append(label, output);
    preview.append(row);
  });

  if (!preview.childElementCount) {
    return;
  }

  const insertAfter = shadow.querySelector('.title');

  insertAfter?.after(preview);
}

function formatPreviewValue(value) {
  if (typeof value === 'boolean') {
    return value ? 'true' : 'false';
  }

  return String(value);
}

function decorateSocketRef(socketRef, theme) {
  const socketElement = socketRef.querySelector?.('rete-socket');
  const socketShadow = socketElement?.shadowRoot;

  if (!socketElement || !socketShadow) {
    return;
  }

  socketElement.dataset.blueprintTheme = theme;
  adoptShadowStyles(socketShadow, 'socket', socketShadowCss);
}

function adoptShadowStyles(shadow, key, css) {
  if (shadow.__daisyBlueprintStyleKeys?.has(key)) {
    return;
  }

  const sheet = getConstructableStyleSheet(key, css);

  if (!sheet) {
    return;
  }

  shadow.adoptedStyleSheets = [...shadow.adoptedStyleSheets, sheet];
  shadow.__daisyBlueprintStyleKeys = shadow.__daisyBlueprintStyleKeys || new Set();
  shadow.__daisyBlueprintStyleKeys.add(key);
}

function getConstructableStyleSheet(key, css) {
  if (typeof CSSStyleSheet === 'undefined' || typeof ShadowRoot === 'undefined' || !('adoptedStyleSheets' in ShadowRoot.prototype)) {
    return null;
  }

  if (!styleSheetCache.has(key)) {
    const sheet = new CSSStyleSheet();

    sheet.replaceSync(css);
    styleSheetCache.set(key, sheet);
  }

  return styleSheetCache.get(key);
}

function querySelectorAllDeep(root, selector) {
  const elements = [];
  const visit = (currentRoot) => {
    currentRoot.querySelectorAll?.(selector).forEach((element) => elements.push(element));
    currentRoot.querySelectorAll?.('*').forEach((element) => {
      if (element.shadowRoot) {
        visit(element.shadowRoot);
      }
    });
  };

  visit(root);

  return elements;
}

function getConnectionTheme(connection, editor) {
  const explicitTheme = connection?.data?.theme;

  if (explicitTheme) {
    return normalizeBlueprintTheme(explicitTheme);
  }

  return normalizeBlueprintTheme(editor.getNode(connection?.source)?.__blueprint?.theme);
}

function decorateConnectionElement(element, theme) {
  querySelectorAllDeep(element, 'rete-connection').forEach((connectionElement) => {
    connectionElement.dataset.blueprintConnectionTheme = theme;
  });
}

export function decorateConnectionView(root, area, editor, connection) {
  const element = area.connectionViews.get(connection?.id)?.element;

  if (!element) {
    return;
  }

  const theme = getConnectionTheme(connection, editor);

  element.dataset.blueprintConnectionTheme = theme;
  decorateConnectionElement(element, theme);

  window.requestAnimationFrame?.(() => decorateConnectionElement(element, theme));
  window.setTimeout(() => decorateConnectionElement(element, theme), 50);
}

export function decorateConnectionViews(root, area, editor) {
  editor.getConnections().forEach((connection) => decorateConnectionView(root, area, editor, connection));
}
