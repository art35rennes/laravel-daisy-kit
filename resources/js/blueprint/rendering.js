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
  }

  .title {
    align-items: center !important;
    background: var(--daisy-blueprint-node-theme) !important;
    border-bottom: 1px solid color-mix(in oklch, var(--daisy-blueprint-node-theme) 42%, var(--color-base-300)) !important;
    border-radius: 6px 6px 0 0 !important;
    color: var(--daisy-blueprint-node-theme-content) !important;
    display: flex !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    gap: 8px !important;
    justify-content: space-between !important;
    line-height: 1.2 !important;
    padding: 9px 12px !important;
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
    border-top: 1px solid var(--color-base-300) !important;
    box-sizing: border-box !important;
    display: flex !important;
    gap: 8px !important;
    min-height: 34px !important;
    padding: 5px 16px !important;
    width: 100% !important;
  }

  .input:nth-of-type(even),
  .output:nth-of-type(even) {
    background: color-mix(in oklch, var(--color-base-200) 72%, var(--color-base-100) 28%) !important;
  }

  .input {
    justify-content: flex-start !important;
    text-align: left !important;
  }

  .output {
    justify-content: flex-end !important;
    text-align: right !important;
  }

  :host([data-blueprint-display="minimal"]) .input,
  :host([data-blueprint-display="minimal"]) .output {
    background: var(--color-base-100) !important;
    border-top: 0 !important;
    min-height: 24px !important;
    padding: 1px 10px !important;
  }

  .input-socket,
  .output-socket {
    display: inline-flex !important;
    flex: 0 0 18px !important;
    height: 18px !important;
    margin: 0 !important;
    position: relative !important;
    transform: none !important;
    width: 18px !important;
  }

  .input-title,
  .output-title {
    color: var(--color-base-content) !important;
    flex: 0 1 auto !important;
    font-size: 12px !important;
    font-weight: 650 !important;
    line-height: 18px !important;
    margin: 0 !important;
    min-width: 0 !important;
    opacity: 0.82 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  :host([data-blueprint-display="minimal"]) .input-title,
  :host([data-blueprint-display="minimal"]) .output-title {
    font-size: 10px !important;
    opacity: 0.65 !important;
  }

  .control,
  .input-control {
    background: var(--color-base-100) !important;
    border-top: 1px solid var(--color-base-300) !important;
    box-sizing: border-box !important;
    padding: 8px 12px !important;
    width: 100% !important;
  }

  :host([data-blueprint-display="minimal"]) .control,
  :host([data-blueprint-display="minimal"]) .input-control {
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
  decorateNodeElement(root, element, theme);
  window.requestAnimationFrame?.(() => decorateNodeElement(root, element, theme));
  window.setTimeout(() => decorateNodeElement(root, element, theme), 50);
}

export function decorateNodeViews(root, area, editor) {
  editor.getNodes().forEach((node) => decorateNodeView(root, area, node));
}

function decorateNodeElement(root, element, theme) {
  element.querySelectorAll('rete-node').forEach((nodeElement) => {
    nodeElement.dataset.blueprintTheme = theme;
    nodeElement.dataset.blueprintDisplay = element.dataset.blueprintDisplay || 'detailed';
    decorateNodeShadow(nodeElement);
  });
}

function decorateNodeShadow(nodeElement) {
  const shadow = nodeElement.shadowRoot;

  if (!shadow) {
    return;
  }

  adoptShadowStyles(shadow, 'node', nodeShadowCss);

  const title = shadow.querySelector('.title');

  if (title) {
    decorateNodeTitle(title, nodeElement);
  }

  shadow.querySelectorAll('.input-socket rete-ref, .output-socket rete-ref').forEach((socketRef) => {
    decorateSocketRef(socketRef, nodeElement.dataset.blueprintTheme);
  });
}

function decorateNodeTitle(title, nodeElement) {
  const host = nodeElement.closest('[data-blueprint-display]');
  const icon = host?.dataset.blueprintIcon || '';

  if (title.dataset.blueprintTitleDecorated !== 'true') {
    title.dataset.blueprintTitleDecorated = 'true';
    title.textContent = '';

    const label = document.createElement('span');

    label.dataset.blueprintTitleLabel = 'true';
    label.textContent = host?.dataset.blueprintLabel || '';

    title.append(label);
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
