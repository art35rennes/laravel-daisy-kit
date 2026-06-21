/** @vitest-environment jsdom */

import { describe, expect, it } from 'vitest';
import {
  canConnect,
  emitBlueprintEvent,
  isPointerClick,
  normalizeGraph,
  normalizeControls,
  normalizeNodeTypes,
  readJsonPayload,
  syncTextarea,
  validateControlsData,
} from '../../../resources/js/blueprint/core.js';
import {
  applyNodeData,
  collectPropertyInputData,
  getNodeControls,
  readPropertyInputValue,
  renderProperties,
  setDetailsOpen,
} from '../../../resources/js/blueprint/details-panel.js';
import { bindNodeClickToggle, isInteractiveNodeTarget } from '../../../resources/js/blueprint/interactions.js';
import {
  createNode,
  createSocketRegistry,
  findAutoConnection,
  generateNodeLabel,
  getControlType,
} from '../../../resources/js/blueprint/nodes.js';
import { createPaletteRegistry } from '../../../resources/js/blueprint/palette.js';
import { serializeEditor } from '../../../resources/js/blueprint/serialization.js';
import { normalizeBlueprintTheme, resolveThemeTokens } from '../../../resources/js/blueprint/theme.js';

const nodeTypes = [
  {
    type: 'source',
    label: 'Source',
    category: 'Data',
    outputs: [{ key: 'rows', label: 'Rows', kind: 'dataset' }],
    defaults: { limit: 10 },
  },
  {
    type: 'transform',
    label: 'Transform',
    category: 'Data',
    inputs: [{ key: 'in', label: 'Rows', kind: 'dataset' }],
    outputs: [{ key: 'out', label: 'Rows', kind: 'dataset' }],
  },
  {
    type: 'notify',
    label: 'Notify',
    category: 'Workflow',
    inputs: [{ key: 'in', label: 'Flow', kind: 'flow' }],
  },
];

describe('blueprint graph contract', () => {
  it('normalizes node types and ports', () => {
    expect(normalizeNodeTypes([
      { type: 'entity', inputs: [{ key: 'id' }], outputs: [{ key: 'out' }] },
      { type: 'relation', description: 'Connects two entities', inputs: [{ key: 'from', kind: 'any' }] },
      { label: 'Ignored' },
    ])).toEqual([
      {
        type: 'entity',
        label: 'entity',
        category: 'General',
        description: '',
        display: 'detailed',
        icon: '',
        theme: 'default',
        nameStrategy: { mode: 'free', prefix: '', value: '' },
        previewFields: [],
        inputs: [{ key: 'id', label: 'id', kind: 'default', type: 'any', multiple: false }],
        outputs: [{ key: 'out', label: 'out', kind: 'default', type: 'any', multiple: true }],
        controls: [],
        defaults: {},
      },
      {
        type: 'relation',
        label: 'relation',
        category: 'General',
        description: 'Connects two entities',
        display: 'detailed',
        icon: '',
        theme: 'default',
        nameStrategy: { mode: 'free', prefix: '', value: '' },
        previewFields: [],
        inputs: [{ key: 'from', label: 'from', kind: 'any', type: 'any', multiple: false }],
        outputs: [],
        controls: [],
        defaults: {},
      },
    ]);
  });

  it('normalizes node display, branding icon, and FormKit-like controls', () => {
    expect(normalizeNodeTypes([
      {
        type: 'http',
        display: 'minimal',
        icon: 'H',
        previewFields: ['method', { key: 'url', label: 'Endpoint' }],
        controls: [
          { id: 'url', type: 'url', label: 'URL', placeholder: 'https://example.test' },
          { name: 'method', type: 'select', options: ['GET', { value: 'post', label: 'POST' }] },
          { key: 'enabled', type: 'toggle', default: true },
        ],
      },
    ])[0]).toMatchObject({
      type: 'http',
      display: 'minimal',
      icon: 'H',
      previewFields: [
        { key: 'method', label: '' },
        { key: 'url', label: 'Endpoint' },
      ],
      controls: [
        {
          key: 'url',
          name: 'url',
          label: 'URL',
          type: 'url',
          placeholder: 'https://example.test',
          required: false,
          options: [],
          default: null,
        },
        {
          key: 'method',
          name: 'method',
          label: 'method',
          type: 'select',
          options: [
            { value: 'GET', label: 'GET' },
            { value: 'post', label: 'POST' },
          ],
        },
        {
          key: 'enabled',
          type: 'checkbox',
          default: true,
        },
      ],
    });
  });

  it('normalizes standalone blueprint controls', () => {
    expect(normalizeControls([
      { key: 'notes', type: 'textarea', help: 'Internal notes' },
      { key: 'count', type: 'integer', min: 0, max: 10, step: 1 },
      { key: 'ignored' },
      { label: 'Missing key' },
    ])).toMatchObject([
      { key: 'notes', type: 'textarea', help: 'Internal notes' },
      { key: 'count', type: 'number', min: 0, max: 10, step: 1 },
      { key: 'ignored', type: 'text' },
    ]);
  });

  it('normalizes graphs and filters invalid edges', () => {
    const graph = normalizeGraph({
      version: 3,
      nodes: [
        { id: 'a', type: 'source', position: { x: '20', y: 30 }, data: { limit: 25 } },
        { id: 'b', type: 'transform' },
        { id: 'b', type: 'transform' },
      ],
      edges: [
        { id: 'valid', source: 'a', sourcePort: 'rows', target: 'b', targetPort: 'in' },
        { id: 'wrong-kind', source: 'a', sourcePort: 'rows', target: 'b', targetPort: 'missing' },
        { id: 'missing-node', source: 'a', sourcePort: 'rows', target: 'c', targetPort: 'in' },
      ],
      viewport: { x: 4, y: 5, k: 0.75 },
    }, nodeTypes);

    expect(graph).toEqual({
      version: 3,
      nodes: [
        {
          id: 'a',
          type: 'source',
          label: 'Source',
          position: { x: 20, y: 30 },
          data: { limit: 25 },
        },
        {
          id: 'b',
          type: 'transform',
          label: 'Transform',
          position: { x: 260, y: 0 },
          data: {},
        },
      ],
      edges: [
        {
          id: 'valid',
          source: 'a',
          sourcePort: 'rows',
          target: 'b',
          targetPort: 'in',
          data: {},
        },
      ],
      viewport: { x: 4, y: 5, zoom: 0.75 },
    });
  });

  it('validates connections by port kind', () => {
    const graph = normalizeGraph({
      nodes: [
        { id: 'a', type: 'source' },
        { id: 'b', type: 'transform' },
        { id: 'c', type: 'notify' },
      ],
    }, nodeTypes);

    expect(canConnect({
      source: 'a',
      sourcePort: 'rows',
      target: 'b',
      targetPort: 'in',
    }, graph.nodes, nodeTypes)).toBe(true);

    expect(canConnect({
      source: 'a',
      sourcePort: 'rows',
      target: 'c',
      targetPort: 'in',
    }, graph.nodes, nodeTypes)).toBe(false);

    expect(canConnect({
      source: 'a',
      sourcePort: 'rows',
      target: 'a',
      targetPort: 'rows',
    }, graph.nodes, nodeTypes)).toBe(false);

    expect(canConnect({
      source: 'a',
      sourcePort: 'rows',
      target: 'b',
      targetPort: 'in',
    }, graph.nodes, [
      nodeTypes[0],
      {
        type: 'transform',
        inputs: [{ key: 'in', kind: 'any' }],
      },
    ])).toBe(true);
  });

  it('validates connections by port type', () => {
    const typedNodeTypes = [
      {
        type: 'number-source',
        outputs: [{ key: 'out', kind: 'value', type: 'int' }],
      },
      {
        type: 'float-sink',
        inputs: [{ key: 'in', kind: 'value', type: 'float' }],
      },
      {
        type: 'string-sink',
        inputs: [{ key: 'in', kind: 'value', type: 'str' }],
      },
    ];
    const graph = normalizeGraph({
      nodes: [
        { id: 'a', type: 'number-source' },
        { id: 'b', type: 'float-sink' },
        { id: 'c', type: 'string-sink' },
      ],
    }, typedNodeTypes);

    expect(canConnect({
      source: 'a',
      sourcePort: 'out',
      target: 'b',
      targetPort: 'in',
    }, graph.nodes, typedNodeTypes)).toBe(true);

    expect(canConnect({
      source: 'a',
      sourcePort: 'out',
      target: 'c',
      targetPort: 'in',
    }, graph.nodes, typedNodeTypes)).toBe(false);
  });

  it('validates node control data before applying edits', () => {
    const controls = normalizeControls([
      { key: 'email', type: 'email', required: true },
      { key: 'priority', type: 'range', min: 1, max: 5 },
      { key: 'ratio', type: 'number', min: 0, max: 1, step: 0.25 },
      { key: 'status', type: 'select', options: ['draft', 'published'] },
      { key: 'code', type: 'text', pattern: '^[A-Z]+$', minLength: 2, maxLength: 4 },
    ]);

    expect(validateControlsData(controls, {
      email: 'ada@example.com',
      priority: 3,
      ratio: 0.5,
      status: 'draft',
      code: 'AB',
    })).toEqual({ valid: true, errors: {} });

    expect(validateControlsData(controls, {
      email: 'invalid',
      priority: 8,
      ratio: 0.6,
      status: 'archived',
      code: 'a',
    })).toEqual({
      valid: false,
      errors: {
        email: ['email'],
        priority: ['max'],
        ratio: ['step'],
        status: ['option'],
        code: ['minLength', 'pattern'],
      },
    });
  });

  it('syncs hidden textarea and emits blueprint events', () => {
    document.body.innerHTML = '<div><textarea data-blueprint-sync></textarea></div>';
    const root = document.querySelector('div');
    const graph = normalizeGraph({ nodes: [{ id: 'a', type: 'source' }] }, nodeTypes);
    let detail = null;

    root.addEventListener('daisy:blueprint:change', (event) => {
      detail = event.detail;
    });

    syncTextarea(root, graph);
    emitBlueprintEvent(root, 'change', { graph });

    expect(root.querySelector('[data-blueprint-sync]').value).toBe(JSON.stringify(graph));
    expect(detail).toEqual({ graph });
  });

  it('reads json payloads from csp safe inert payload elements', () => {
    document.body.innerHTML = `
      <div>
        <textarea hidden readonly data-blueprint-value>{"nodes":[{"id":"a","type":"source"}]}</textarea>
      </div>
    `;
    const root = document.querySelector('div');

    expect(readJsonPayload(root, '[data-blueprint-value]', {})).toEqual({
      nodes: [{ id: 'a', type: 'source' }],
    });
    expect(readJsonPayload(root, '[data-missing]', { ok: true })).toEqual({ ok: true });
  });

  it('emits blueprint error events with details', () => {
    document.body.innerHTML = '<div></div>';
    const root = document.querySelector('div');
    let detail = null;

    root.addEventListener('daisy:blueprint:error', (event) => {
      detail = event.detail;
    });

    emitBlueprintEvent(root, 'error', { message: 'Invalid connection' });

    expect(detail).toEqual({ message: 'Invalid connection' });
  });

  it('distinguishes node clicks from drags', () => {
    const start = { pointerId: 7, x: 100, y: 120 };

    expect(isPointerClick(start, {
      pointerId: 7,
      clientX: 103,
      clientY: 124,
    })).toBe(true);

    expect(isPointerClick(start, {
      pointerId: 7,
      clientX: 120,
      clientY: 140,
    })).toBe(false);

    expect(isPointerClick(start, {
      pointerId: 8,
      clientX: 103,
      clientY: 124,
    })).toBe(false);
  });

  it('normalizes DaisyUI blueprint themes and aliases', () => {
    expect(normalizeBlueprintTheme('warning')).toBe('warning');
    expect(normalizeBlueprintTheme('condition')).toBe('warning');
    expect(normalizeBlueprintTheme('base-100')).toBe('primary');
    expect(resolveThemeTokens('action')).toEqual({
      name: 'success',
      color: 'var(--color-success)',
      content: 'var(--color-success-content)',
    });
  });

  it('generates node labels from the configured naming strategy', () => {
    expect(generateNodeLabel({ type: 'notify', label: 'Notify' })).toBe('Notify');
    expect(generateNodeLabel({
      type: 'http',
      label: 'HTTP',
      nameStrategy: { mode: 'preset', value: 'Fetch profile' },
    }, 4)).toBe('Fetch profile');
    expect(generateNodeLabel({
      type: 'task',
      label: 'Task',
      nameStrategy: { mode: 'auto', prefix: 'Step' },
    }, 2)).toBe('Step 3');
  });

  it('finds a single compatible auto-link pair only when ports are unambiguous', () => {
    const typedNodeTypes = normalizeNodeTypes([
      {
        type: 'source',
        outputs: [{ key: 'rows', kind: 'dataset', type: 'obj' }],
      },
      {
        type: 'transform',
        inputs: [{ key: 'in', kind: 'dataset', type: 'obj' }],
      },
      {
        type: 'ambiguous',
        inputs: [
          { key: 'primary', kind: 'dataset', type: 'obj' },
          { key: 'fallback', kind: 'dataset', type: 'obj' },
        ],
      },
      {
        type: 'flow-only',
        inputs: [{ key: 'in', kind: 'flow' }],
      },
    ]);
    const sourceNode = { id: 'a', __blueprint: { type: 'source' } };
    const targetNode = { id: 'b', __blueprint: { type: 'transform' } };
    const editor = { getConnections: () => [] };

    expect(findAutoConnection(sourceNode, targetNode, editor, typedNodeTypes)).toEqual({
      output: { key: 'rows', label: 'rows', kind: 'dataset', type: 'obj', multiple: true },
      input: { key: 'in', label: 'in', kind: 'dataset', type: 'obj', multiple: false },
    });

    expect(findAutoConnection(
      sourceNode,
      { id: 'c', __blueprint: { type: 'ambiguous' } },
      editor,
      typedNodeTypes,
    )).toBeNull();
    expect(findAutoConnection(
      sourceNode,
      { id: 'd', __blueprint: { type: 'flow-only' } },
      editor,
      typedNodeTypes,
    )).toBeNull();
    expect(findAutoConnection(sourceNode, targetNode, {
      getConnections: () => [{ source: 'a', sourceOutput: 'rows' }],
    }, typedNodeTypes)).toEqual({
      output: { key: 'rows', label: 'rows', kind: 'dataset', type: 'obj', multiple: true },
      input: { key: 'in', label: 'in', kind: 'dataset', type: 'obj', multiple: false },
    });
  });

  it('detects scalar input control types for Rete preview controls', () => {
    expect(getControlType(42)).toBe('number');
    expect(getControlType('42')).toBe('text');
    expect(getControlType(true)).toBe('text');
  });

  it('serializes the Rete editor state into the public blueprint graph contract', () => {
    const editor = {
      getNodes: () => [
        {
          id: 'source-1',
          label: 'Source',
          __blueprint: { type: 'source', data: { limit: 25 } },
        },
        {
          id: 'transform-1',
          label: 'Transform',
          __blueprint: { type: 'transform', data: {} },
        },
      ],
      getConnections: () => [
        {
          id: 'edge-1',
          source: 'source-1',
          sourceOutput: 'rows',
          target: 'transform-1',
          targetInput: 'in',
          data: { theme: 'info' },
        },
      ],
    };
    const area = {
      nodeViews: new Map([
        ['source-1', { position: { x: 120, y: 80 } }],
      ]),
      area: { transform: { x: -10, y: -20, k: 0.8 } },
    };

    expect(serializeEditor(editor, area, { x: 0, y: 0, zoom: 1 })).toEqual({
      version: 1,
      nodes: [
        {
          id: 'source-1',
          type: 'source',
          label: 'Source',
          position: { x: 120, y: 80 },
          data: { limit: 25 },
        },
        {
          id: 'transform-1',
          type: 'transform',
          label: 'Transform',
          position: { x: 260, y: 0 },
          data: {},
        },
      ],
      edges: [
        {
          id: 'edge-1',
          source: 'source-1',
          sourcePort: 'rows',
          target: 'transform-1',
          targetPort: 'in',
          data: { theme: 'info' },
        },
      ],
      viewport: { x: -10, y: -20, zoom: 0.8 },
    });
  });

  it('renders the details panel with editable controls and feedback', () => {
    document.body.innerHTML = `
      <div>
        <aside class="hidden" data-blueprint-details-panel></aside>
        <button class="hidden" data-blueprint-details-backdrop></button>
        <div data-blueprint-properties></div>
      </div>
    `;
    const root = document.querySelector('div');
    const node = {
      id: 'notify-1',
      label: 'Notify <ops>',
      __blueprint: {
        type: 'notify',
        theme: 'success',
        description: 'Send alert',
        data: { channel: 'ops', priority: 3, enabled: true },
        controls: normalizeControls([
          { key: 'channel', label: 'Channel', type: 'select', options: ['ops', 'support'] },
          { key: 'priority', label: 'Priority', type: 'range', min: 1, max: 5 },
          { key: 'enabled', label: 'Enabled', type: 'checkbox' },
        ]),
      },
    };

    renderProperties(root, node, {
      applyNode: 'Apply',
      deleteNode: 'Delete',
      applySuccess: 'Saved',
    }, false, { channel: 'support', priority: 4, enabled: false }, { valid: true, errors: {} });

    expect(root.querySelector('[data-blueprint-details-panel]').classList.contains('hidden')).toBe(false);
    expect(root.querySelector('[data-blueprint-details-backdrop]').classList.contains('hidden')).toBe(false);
    expect(root.querySelector('[data-blueprint-properties]').textContent).toContain('Notify <ops>');
    expect(root.querySelector('[data-blueprint-details-feedback]').textContent).toContain('Saved');
    expect(root.querySelector('select[data-blueprint-property-input="channel"]').value).toBe('support');
    expect(root.querySelector('input[data-blueprint-property-input="priority"]').value).toBe('4');
    expect(root.querySelector('input[data-blueprint-property-input="enabled"]').checked).toBe(false);
    expect(root.querySelector('[data-blueprint-apply-node]')).not.toBeNull();
    expect(root.querySelector('[data-blueprint-delete-node]')).not.toBeNull();
  });

  it('renders details errors with copyable JSON details', () => {
    document.body.innerHTML = `
      <div>
        <aside class="hidden" data-blueprint-details-panel></aside>
        <button class="hidden" data-blueprint-details-backdrop></button>
        <div data-blueprint-properties></div>
      </div>
    `;
    const root = document.querySelector('div');
    const node = {
      id: 'mail-1',
      label: 'Mail',
      __blueprint: {
        type: 'mail',
        theme: 'error',
        data: { email: 'invalid' },
        controls: normalizeControls([{ key: 'email', type: 'email', required: true }]),
      },
    };

    renderProperties(root, node, {
      applyError: 'Invalid fields',
      errorDetails: 'Details',
      copyError: 'Copy',
    }, false, { email: 'invalid' }, { valid: false, errors: { email: ['email'] } });

    expect(root.querySelector('[data-blueprint-details-feedback]').textContent).toContain('Invalid fields');
    expect(root.querySelector('[data-blueprint-error-details]').value).toContain('"email"');
    expect(root.querySelector('[data-blueprint-copy-error]')).not.toBeNull();
  });

  it('renders property details without executing or injecting html from node data', () => {
    document.body.innerHTML = `
      <div>
        <aside class="hidden" data-blueprint-details-panel></aside>
        <button class="hidden" data-blueprint-details-backdrop></button>
        <div data-blueprint-properties></div>
      </div>
    `;
    const root = document.querySelector('div');
    const node = {
      id: 'unsafe',
      label: '<img src=x onerror="window.__blueprintXss = true">',
      __blueprint: {
        type: 'unsafe',
        theme: 'primary',
        description: '<script>window.__blueprintXss = true</script>',
        data: { payload: '<svg onload="window.__blueprintXss = true"></svg>' },
        controls: normalizeControls([
          {
            key: 'payload',
            label: '<b>Payload</b>',
            placeholder: '" autofocus onfocus="window.__blueprintXss = true',
            type: 'text',
          },
        ]),
      },
    };

    renderProperties(root, node, {}, false);

    expect(window.__blueprintXss).toBeUndefined();
    expect(root.querySelector('[data-blueprint-properties]').querySelector('script')).toBeNull();
    expect(root.querySelector('[data-blueprint-properties]').querySelector('img')).toBeNull();
    expect(root.querySelector('[data-blueprint-properties]').textContent).toContain('<img src=x');
    expect(root.querySelector('[data-blueprint-property-input="payload"]').value).toContain('<svg');
  });

  it('closes details and resolves fallback controls from node data', () => {
    document.body.innerHTML = `
      <div>
        <aside data-blueprint-details-panel></aside>
        <button data-blueprint-details-backdrop></button>
        <div data-blueprint-properties></div>
      </div>
    `;
    const root = document.querySelector('div');
    const node = {
      __blueprint: {
        data: { title: 'Draft', score: 2, meta: { ignored: true } },
      },
    };

    expect(getNodeControls(node)).toEqual([
      { key: 'title', label: 'title', type: 'text' },
      { key: 'score', label: 'score', type: 'number' },
      { key: 'meta', label: 'meta', type: 'text' },
    ]);

    setDetailsOpen(root, false);
    expect(root.querySelector('[data-blueprint-details-panel]').classList.contains('hidden')).toBe(true);
    expect(root.querySelector('[data-blueprint-details-backdrop]').classList.contains('hidden')).toBe(true);
  });

  it('reads property input values and applies node data for refreshed previews', () => {
    document.body.innerHTML = `
      <input type="checkbox" checked data-blueprint-property-input="enabled">
      <input type="number" value="7" data-blueprint-property-input="count">
      <input type="range" value="3" data-blueprint-property-input="priority">
      <input type="text" value="ops" data-blueprint-property-input="channel">
    `;
    const node = {
      __blueprint: {
        data: { enabled: false, count: 2, channel: 'support' },
        previewFields: [{ key: 'count', label: 'Count' }, { key: 'channel', label: 'Channel' }],
      },
      controls: {},
    };

    expect(readPropertyInputValue(document.querySelector('[data-blueprint-property-input="enabled"]'))).toBe(true);
    expect(readPropertyInputValue(document.querySelector('[data-blueprint-property-input="count"]'))).toBe(7);
    expect(readPropertyInputValue(document.querySelector('[data-blueprint-property-input="priority"]'))).toBe(3);
    expect(readPropertyInputValue(document.querySelector('[data-blueprint-property-input="channel"]'))).toBe('ops');

    applyNodeData(node, { enabled: true, count: 7, channel: 'ops' });

    expect(node.__blueprint.data).toEqual({ enabled: true, count: 7, channel: 'ops' });
    expect(node.__blueprint.previewFields).toEqual([
      { key: 'count', label: 'Count' },
      { key: 'channel', label: 'Channel' },
    ]);
    expect(node.controls).toEqual({});
  });

  it('collects the latest property input values before applying edits', () => {
    document.body.innerHTML = `
      <div>
        <input type="range" min="1" max="5" step="1" value="5" data-blueprint-property-input="priority">
        <input type="text" value="total > 1000" data-blueprint-property-input="rule">
      </div>
    `;
    const root = document.querySelector('div');

    expect(collectPropertyInputData(root, { priority: 3, rule: 'total > 500' })).toEqual({
      priority: 5,
      rule: 'total > 1000',
    });
  });

  it('creates nodes with non-editable preview field metadata instead of inline Rete controls', () => {
    const normalizedTypes = normalizeNodeTypes([
      {
        type: 'action',
        inputs: [{ key: 'in', kind: 'flow' }],
        outputs: [{ key: 'out', kind: 'flow' }],
        defaults: { title: 'Notify', priority: 2 },
        previewFields: ['priority'],
      },
    ]);
    const node = createNode({
      id: 'action-1',
      type: 'action',
      label: 'Action',
      data: { title: 'Notify', priority: 2 },
    }, normalizedTypes, createSocketRegistry(normalizedTypes), false);

    expect(node.__blueprint.data).toEqual({ title: 'Notify', priority: 2 });
    expect(node.__blueprint.previewFields).toEqual([{ key: 'priority', label: '' }]);
    expect(node.controls).toEqual({});
  });

  it('builds palette groups and node factories for Rete context menu and dock', () => {
    const normalizedTypes = normalizeNodeTypes([
      {
        type: 'trigger',
        label: 'Trigger',
        category: 'Workflow',
        theme: 'primary',
        outputs: [{ key: 'next', kind: 'flow' }],
        defaults: { event: 'Order paid' },
      },
      {
        type: 'notify',
        label: 'Notify',
        category: 'Workflow',
        theme: 'success',
        inputs: [{ key: 'in', kind: 'flow' }],
        defaults: { channel: 'ops' },
      },
      {
        type: 'entity',
        label: 'Entity',
        category: 'Schema',
        theme: 'secondary',
      },
    ]);
    const registry = createPaletteRegistry(normalizedTypes, createSocketRegistry(normalizedTypes), false);

    expect(registry.contextMenuItems.map(([category]) => category)).toEqual(['Workflow', 'Schema']);
    expect(registry.contextMenuItems[0][1].map(([label]) => label)).toEqual(['Trigger', 'Notify']);
    expect(registry.nodeFactories).toHaveLength(3);

    const trigger = registry.nodeFactories[0]();

    expect(trigger.__blueprint).toMatchObject({
      type: 'trigger',
      category: 'Workflow',
      theme: 'primary',
      data: { event: 'Order paid' },
    });
    expect(trigger.label).toBe('Trigger');
  });

  it('binds node click toggles while ignoring drags and interactive targets', () => {
    document.body.innerHTML = `
      <div data-node>
        <button type="button">Edit</button>
      </div>
    `;
    const element = document.querySelector('[data-node]');
    const button = element.querySelector('button');
    const node = { id: 'node-1' };
    const toggledNodes = [];

    expect(bindNodeClickToggle(element, node, (currentNode) => toggledNodes.push(currentNode))).toBe(true);
    expect(bindNodeClickToggle(element, node, (currentNode) => toggledNodes.push(currentNode))).toBe(false);

    element.dispatchEvent(new PointerEvent('pointerdown', {
      button: 0,
      bubbles: true,
      composed: true,
      pointerId: 1,
      clientX: 10,
      clientY: 10,
    }));
    element.dispatchEvent(new PointerEvent('pointerup', {
      bubbles: true,
      composed: true,
      pointerId: 1,
      clientX: 12,
      clientY: 13,
    }));

    element.dispatchEvent(new PointerEvent('pointerdown', {
      button: 0,
      bubbles: true,
      composed: true,
      pointerId: 2,
      clientX: 10,
      clientY: 10,
    }));
    element.dispatchEvent(new PointerEvent('pointerup', {
      bubbles: true,
      composed: true,
      pointerId: 2,
      clientX: 30,
      clientY: 35,
    }));

    button.dispatchEvent(new PointerEvent('pointerdown', {
      button: 0,
      bubbles: true,
      composed: true,
      pointerId: 3,
      clientX: 10,
      clientY: 10,
    }));
    button.dispatchEvent(new PointerEvent('pointerup', {
      bubbles: true,
      composed: true,
      pointerId: 3,
      clientX: 10,
      clientY: 10,
    }));

    expect(toggledNodes).toEqual([node]);
  });

  it('detects interactive node event targets', () => {
    document.body.innerHTML = '<div><input><span></span></div>';
    const input = document.querySelector('input');
    const span = document.querySelector('span');

    expect(isInteractiveNodeTarget({
      composedPath: () => [input, document.body],
    })).toBe(true);
    expect(isInteractiveNodeTarget({
      composedPath: () => [span, document.body],
    })).toBe(false);
  });
});
