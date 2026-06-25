import { NodeEditor } from 'rete';
import { AreaPlugin, AreaExtensions } from 'rete-area-plugin';
import { ConnectionPlugin, Presets as ConnectionPresets } from 'rete-connection-plugin';
import { LitPlugin, Presets as LitPresets } from '@retejs/lit-plugin';
import { HistoryPlugin, Presets as HistoryPresets } from 'rete-history-plugin';
import { MinimapPlugin } from 'rete-minimap-plugin';
import { ReadonlyPlugin } from 'rete-readonly-plugin';
import { ContextMenuPlugin, Presets as ContextMenuPresets } from 'rete-context-menu-plugin';
import { ReroutePlugin } from 'rete-connection-reroute-plugin';
import {
  canConnect,
  emitBlueprintEvent,
  normalizeGraph,
  normalizeNodeTypes,
  readJsonPayload,
  syncTextarea,
  validateControlsData,
} from './core.js';
import {
  applyNodeData,
  activateDetailsTab,
  collectPropertyInputData,
  getNodeControls,
  readPropertyInputValue,
  renderProperties,
  setDetailsOpen,
} from './details-panel.js';
import { bindNodeClickToggle } from './interactions.js';
import {
  createConnection,
  createNode,
  createSocketRegistry,
  defaultNodeTypes,
  findAutoConnection,
  generateNodeLabel,
  getNodeViewPosition,
} from './nodes.js';
import { createPaletteRegistry } from './palette.js';
import {
  decorateConnectionView,
  decorateConnectionViews,
  decorateNodeView,
  decorateNodeViews,
} from './rendering.js';
import { serializeEditor } from './serialization.js';

const isReadonly = (root) => root.dataset.readonly === 'true' || root.dataset.mode === 'view';

const readBoolean = (root, name, fallback = true) => {
  const value = root.dataset[name];

  if (value === undefined || value === '') {
    return fallback;
  }

  return value === 'true';
};

async function init(root) {
  if (root.__daisyBlueprint) {
    return root.__daisyBlueprint;
  }

  const canvas = root.querySelector('[data-blueprint-canvas]');
  const rawNodeTypes = readJsonPayload(root, '[data-blueprint-node-types]', defaultNodeTypes);
  const nodeTypes = normalizeNodeTypes(rawNodeTypes);
  const graph = normalizeGraph(readJsonPayload(root, '[data-blueprint-value]', {}), nodeTypes);
  const readonly = isReadonly(root);
  const details = readBoolean(root, 'details');
  const autoLink = readBoolean(root, 'autoLink');
  const i18n = readJsonPayload(root, '[data-blueprint-i18n]', {});
  const sockets = createSocketRegistry(nodeTypes);
  const editor = new NodeEditor();
  const area = new AreaPlugin(canvas);
  const connection = new ConnectionPlugin();
  const render = new LitPlugin();
  const history = readBoolean(root, 'history') ? new HistoryPlugin({ timing: 200 }) : null;
  let arrangePlugin = null;
  const readonlyPlugin = new ReadonlyPlugin();
  const minimap = readBoolean(root, 'minimap') ? new MinimapPlugin({ boundViewport: true }) : null;
  const reroute = readBoolean(root, 'reroute') ? new ReroutePlugin() : null;
  let selectedNode = null;
  let detailsNode = null;
  let detailsDraftData = {};
  let suppressDetailsToggle = false;

  editor.use(readonlyPlugin.root);
  editor.use(area);
  area.use(readonlyPlugin.area);
  connection.use(readonlyPlugin.connection);
  area.use(connection);
  area.use(render);

  connection.addPreset(ConnectionPresets.classic.setup());
  render.addPreset(LitPresets.classic.setup());

  if (minimap) {
    area.use(minimap);
    render.addPreset(LitPresets.minimap.setup());
  }

  if (reroute) {
    connection.use(reroute);
    render.addPreset(LitPresets.reroute.setup());
  }

  if (history) {
    area.use(history);
    history.addPreset(HistoryPresets.classic.setup());
  }

  if (readBoolean(root, 'autoArrange')) {
    const { AutoArrangePlugin, Presets: ArrangePresets } = await import('rete-auto-arrange-plugin');

    arrangePlugin = new AutoArrangePlugin();
    area.use(arrangePlugin);
    arrangePlugin.addPreset(ArrangePresets.classic.setup());
  }

  const { nodeFactories, contextMenuItems } = createPaletteRegistry(nodeTypes, sockets, readonly);

  if (!readonly) {
    const contextMenu = new ContextMenuPlugin({
      items: ContextMenuPresets.classic.setup(contextMenuItems),
    });

    area.use(contextMenu);
    render.addPreset(LitPresets.contextMenu.setup());
  }

  if (!readonly && readBoolean(root, 'dock', false)) {
    const { DockPlugin, DockPresets } = await import('rete-dock-plugin');
    const dock = new DockPlugin();

    area.use(dock);
    dock.addPreset(DockPresets.classic.setup({ area, size: 120, scale: 0.65 }));
    nodeFactories.forEach((factory, index) => dock.add(factory, index));
  }

  editor.addPipe((context) => {
    if (context.type === 'connectioncreate') {
      const currentGraph = serializeEditor(editor, area, graph.viewport);
      const edge = {
        source: context.data.source,
        sourcePort: String(context.data.sourceOutput),
        target: context.data.target,
        targetPort: String(context.data.targetInput),
      };

      if (!canConnect(edge, currentGraph.nodes, nodeTypes)) {
        emitBlueprintEvent(root, 'error', { message: i18n.invalidConnection || 'Invalid connection', edge });
        return undefined;
      }
    }

    if (['nodecreated', 'noderemoved', 'connectioncreated', 'connectionremoved'].includes(context.type)) {
      queueSync();
    }

    return context;
  });

  function closeDetails() {
    detailsNode = null;
    detailsDraftData = {};
    setDetailsOpen(root, false);
  }

  function openDetails(node) {
    if (!details || !node) {
      return;
    }

    detailsNode = node;
    detailsDraftData = { ...(node.__blueprint?.data || {}) };
    renderProperties(root, node, i18n, readonly, detailsDraftData);
  }

  function toggleNodeDetails(node) {
    if (!node || suppressDetailsToggle) {
      return;
    }

    selectedNode = node;

    if (!details) {
      return;
    }

    if (detailsNode?.id === node.id) {
      closeDetails();
      return;
    }

    openDetails(node);
  }

  area.addPipe((context) => {
    if (context.type === 'nodepicked') {
      selectedNode = editor.getNode(context.data.id);
      emitBlueprintEvent(root, 'select', { node: selectedNode });
    }

    if (context.type === 'rendered' && context.data?.type === 'node') {
      const node = editor.getNode(context.data.payload.id);

      if (node) {
        decorateNodeView(root, area, node);
        bindNodeClickToggle(area.nodeViews.get(node.id)?.element, node, toggleNodeDetails);
      }
    }

    if (context.type === 'rendered' && context.data?.type === 'connection') {
      decorateConnectionView(root, area, editor, context.data.payload);
    }

    if (['nodetranslated', 'zoomed', 'translated'].includes(context.type)) {
      queueSync();
    }

    return context;
  });

  for (const nodeData of graph.nodes) {
    const node = createNode(nodeData, nodeTypes, sockets, readonly);
    await editor.addNode(node);
    await area.translate(node.id, nodeData.position);
    decorateNodeView(root, area, node);
  }

  for (const edge of graph.edges) {
    const source = editor.getNode(edge.source);
    const target = editor.getNode(edge.target);

    if (source && target) {
      await editor.addConnection(createConnection(edge, source, target));
    }
  }

  AreaExtensions.simpleNodesOrder(area);

  if (graph.nodes.length && readBoolean(root, 'fitOnInit')) {
    await AreaExtensions.zoomAt(area, editor.getNodes());
  }

  if (readonly) {
    readonlyPlugin.enable();
  }

  decorateNodeViews(root, area, editor);
  decorateConnectionViews(root, area, editor);

  function getGraph() {
    return normalizeGraph(serializeEditor(editor, area, graph.viewport), nodeTypes);
  }

  function syncNow() {
    const nextGraph = getGraph();
    syncTextarea(root, nextGraph);
    emitBlueprintEvent(root, 'change', { graph: nextGraph });
  }

  function queueSync() {
    const schedule = window.requestAnimationFrame || ((callback) => window.setTimeout(callback, 0));

    schedule(syncNow);
  }

  async function addNode(type) {
    if (readonly) {
      return null;
    }

    suppressDetailsToggle = true;
    closeDetails();

    const typeDefinition = nodeTypes.find((nodeType) => nodeType.type === type) || nodeTypes[0];
    const selectedPosition = selectedNode ? getNodeViewPosition(area, selectedNode) : { x: 40, y: 40 };
    const position = selectedNode
      ? { x: selectedPosition.x + 300, y: selectedPosition.y }
      : { x: 40, y: 40 };
    const existingCount = editor.getNodes()
      .filter((candidate) => candidate.__blueprint?.type === typeDefinition.type)
      .length;
    const node = createNode({
      id: `${typeDefinition.type}-${Date.now()}`,
      type: typeDefinition.type,
      label: generateNodeLabel(typeDefinition, existingCount),
      data: typeDefinition.defaults,
      position,
    }, nodeTypes, sockets, readonly);
    const sourceNode = selectedNode;

    await editor.addNode(node);
    await area.translate(node.id, position);
    decorateNodeView(root, area, node);

    if (autoLink && sourceNode) {
      const pair = findAutoConnection(sourceNode, node, editor, nodeTypes);

      if (pair) {
        await editor.addConnection(createConnection({
          id: `edge-${Date.now()}`,
          sourcePort: pair.output.key,
          targetPort: pair.input.key,
          data: {},
        }, sourceNode, node));
        decorateConnectionViews(root, area, editor);
      }
    }

    selectedNode = node;
    syncNow();
    queueSync();
    window.setTimeout(() => {
      suppressDetailsToggle = false;
      syncNow();
    }, 0);

    return node;
  }

  async function removeNode(node = selectedNode) {
    if (readonly || !node) {
      return false;
    }

    const connections = editor.getConnections()
      .filter((candidate) => candidate.source === node.id || candidate.target === node.id);

    for (const currentConnection of connections) {
      await editor.removeConnection(currentConnection.id);
    }

    await editor.removeNode(node.id);
    selectedNode = null;
    if (detailsNode?.id === node.id) {
      closeDetails();
    }
    queueSync();

    return true;
  }

  async function applyNodeProperties() {
    const node = detailsNode || selectedNode;

    if (readonly || !node) {
      return false;
    }

    const controls = getNodeControls(node);
    detailsDraftData = collectPropertyInputData(root, detailsDraftData);
    const validation = validateControlsData(controls, detailsDraftData);

    if (!validation.valid) {
      renderProperties(root, node, i18n, readonly, detailsDraftData, validation);
      emitBlueprintEvent(root, 'error', {
        message: i18n.applyError || 'Some fields are invalid.',
        node,
        errors: validation.errors,
      });

      return false;
    }

    applyNodeData(node, detailsDraftData);
    await area.update('node', node.id);
    decorateNodeView(root, area, node);
    renderProperties(root, node, i18n, readonly, detailsDraftData, validation);
    queueSync();

    return true;
  }

  async function toggleFullscreen() {
    if (root.classList.contains('daisy-blueprint-fullscreen-fallback')) {
      root.classList.remove('daisy-blueprint-fullscreen', 'daisy-blueprint-fullscreen-fallback');

      return;
    }

    if (document.fullscreenElement === root) {
      await document.exitFullscreen?.();
      root.classList.remove('daisy-blueprint-fullscreen');

      return;
    }

    root.classList.add('daisy-blueprint-fullscreen');

    try {
      await root.requestFullscreen?.();
    } catch (_) {
      root.classList.add('daisy-blueprint-fullscreen-fallback');
    }
  }

  const api = {
    editor,
    area,
    history,
    arrange: arrangePlugin,
    getGraph,
    addNode,
    removeNode,
    async undo() {
      if (readonly) return;
      await history?.undo();
      queueSync();
    },
    async redo() {
      if (readonly) return;
      await history?.redo();
      queueSync();
    },
    async arrange() {
      await arrangePlugin?.layout();
      queueSync();
    },
    async fit() {
      const nodes = editor.getNodes();
      if (nodes.length) {
        await AreaExtensions.zoomAt(area, nodes);
      }
    },
    async fullscreen() {
      await toggleFullscreen();
    },
    destroy() {
      area.destroy();
    },
  };

  root.querySelectorAll('[data-blueprint-add-node]').forEach((button) => {
    button.addEventListener('pointerdown', () => {
      suppressDetailsToggle = true;
      closeDetails();
    });
  });

  root.querySelectorAll('[data-blueprint-palette-menu]').forEach((palette) => {
    palette.addEventListener('pointerdown', () => {
      suppressDetailsToggle = true;
      closeDetails();
      window.setTimeout(() => {
        suppressDetailsToggle = false;
      }, 0);
    });
  });

  root.querySelectorAll('[data-blueprint-action]').forEach((button) => {
    button.addEventListener('click', () => {
      if (readonly && !['fit', 'arrange', 'fullscreen'].includes(button.dataset.blueprintAction)) {
        return;
      }

      const action = button.dataset.blueprintAction;

      if (action === 'undo') void api.undo();
      if (action === 'redo') void api.redo();
      if (action === 'arrange') void api.arrange();
      if (action === 'fit') void api.fit();
      if (action === 'fullscreen') void api.fullscreen();
    });
  });

  root.addEventListener('click', (event) => {
    const addButton = event.target.closest?.('[data-blueprint-add-node]');

    if (addButton && root.contains(addButton)) {
      void addNode(addButton.dataset.blueprintAddNode).finally(() => {
        window.setTimeout(() => {
          suppressDetailsToggle = false;
        }, 0);
      });
      return;
    }

    const tab = event.target.closest?.('[data-blueprint-details-tab]');
    if (tab && root.contains(tab)) {
      activateDetailsTab(root, tab.dataset.blueprintDetailsTab);
      return;
    }

    if (event.target.closest?.('[data-blueprint-details-close], [data-blueprint-details-backdrop]')) {
      closeDetails();
      return;
    }

    if (event.target.closest?.('[data-blueprint-apply-node]')) {
      void applyNodeProperties();
      return;
    }

    const copyErrorButton = event.target.closest?.('[data-blueprint-copy-error]');
    if (copyErrorButton) {
      const detailsValue = root.querySelector('[data-blueprint-error-details]')?.value || '';

      void navigator.clipboard?.writeText(detailsValue);
      return;
    }

    if (!event.target.closest?.('[data-blueprint-delete-node]')) {
      return;
    }

    void removeNode();
  });

  const updateSelectedProperty = (event) => {
    const input = event.target.closest?.('[data-blueprint-property-input]');

    if (!input || !detailsNode || readonly) {
      return;
    }

    detailsDraftData[input.dataset.blueprintPropertyInput] = readPropertyInputValue(input);
  };

  root.addEventListener('input', updateSelectedProperty);
  root.addEventListener('change', updateSelectedProperty);
  root.addEventListener('code:change', updateSelectedProperty);
  root.addEventListener('trix-change', updateSelectedProperty);

  document.addEventListener('fullscreenchange', () => {
    if (document.fullscreenElement !== root) {
      root.classList.remove('daisy-blueprint-fullscreen', 'daisy-blueprint-fullscreen-fallback');
    }
  });

  if (details) {
    renderProperties(root, null, i18n, readonly);
  }
  syncTextarea(root, getGraph());
  emitBlueprintEvent(root, 'init', { graph: getGraph(), readonly });

  root.__daisyBlueprint = api;

  return api;
}

export default init;
