import { ClassicPreset } from 'rete';
import { portsCompatible } from './core.js';
import { normalizeBlueprintTheme } from './theme.js';

export const defaultNodeTypes = [
  {
    type: 'task',
    label: 'Task',
    category: 'Workflow',
    theme: 'success',
    inputs: [{ key: 'in', label: 'In', kind: 'flow' }],
    outputs: [{ key: 'out', label: 'Out', kind: 'flow', multiple: true }],
    defaults: {},
  },
];

export function createSocketRegistry(nodeTypes) {
  const sockets = new Map();

  nodeTypes.forEach((nodeType) => {
    [...nodeType.inputs, ...nodeType.outputs].forEach((port) => {
      if (!sockets.has(port.kind)) {
        sockets.set(port.kind, new ClassicPreset.Socket(port.kind));
      }
    });
  });

  if (!sockets.size) {
    sockets.set('default', new ClassicPreset.Socket('default'));
  }

  return {
    get(kind = 'default') {
      if (!sockets.has(kind)) {
        sockets.set(kind, new ClassicPreset.Socket(kind));
      }

      return sockets.get(kind);
    },
  };
}

export const getControlType = (value) => (typeof value === 'number' ? 'number' : 'text');

export function createNode(nodeData, nodeTypes, sockets, readonly) {
  const typeDefinition = nodeTypes.find((nodeType) => nodeType.type === nodeData.type) || nodeTypes[0];
  const node = new ClassicPreset.Node(nodeData.label || typeDefinition?.label || nodeData.type);
  const previewRows = getPreviewFields(typeDefinition, nodeData.data || {}).length;
  const portRows = (typeDefinition?.inputs?.length || 0) + (typeDefinition?.outputs?.length || 0);
  const display = typeDefinition?.display || 'detailed';

  node.id = nodeData.id;
  node.width = display === 'minimal' ? 188 : 248;
  node.height = display === 'minimal'
    ? Math.max(74, 52 + Math.max(portRows, 1) * 18)
    : Math.max(126, 54 + previewRows * 34, 88 + Math.max(portRows, 1) * 28);
  node.__blueprint = {
    type: nodeData.type,
    category: typeDefinition?.category || '',
    description: typeDefinition?.description || '',
    display,
    icon: typeDefinition?.icon || '',
    theme: normalizeBlueprintTheme(typeDefinition?.theme),
    nameStrategy: typeDefinition?.nameStrategy || { mode: 'free' },
    controls: typeDefinition?.controls || [],
    previewFields: getPreviewFields(typeDefinition, nodeData.data || {}),
    data: { ...(nodeData.data || {}) },
  };

  (typeDefinition?.inputs || []).forEach((input) => {
    node.addInput(input.key, new ClassicPreset.Input(sockets.get(input.kind), input.label, Boolean(input.multiple)));
  });

  (typeDefinition?.outputs || []).forEach((output) => {
    node.addOutput(output.key, new ClassicPreset.Output(sockets.get(output.kind), output.label, Boolean(output.multiple)));
  });

  return node;
}

export function getPreviewFields(typeDefinition, data = {}) {
  if (typeDefinition?.previewFields?.length) {
    return typeDefinition.previewFields;
  }

  const controls = typeDefinition?.controls || [];
  const fields = controls
    .filter((control) => data[control.key] !== null && data[control.key] !== undefined && typeof data[control.key] !== 'object')
    .slice(0, 3)
    .map((control) => ({ key: control.key, label: control.label || control.key }));

  if (fields.length) {
    return fields;
  }

  return Object.entries(data)
    .filter(([, value]) => value !== null && value !== undefined && typeof value !== 'object')
    .slice(0, 3)
    .map(([key]) => ({ key, label: key }));
}

export function createConnection(edge, source, target) {
  const connection = new ClassicPreset.Connection(source, edge.sourcePort, target, edge.targetPort);

  connection.id = edge.id;
  connection.data = edge.data || {};

  return connection;
}

export function getNodeTypeDefinition(node, nodeTypes) {
  return nodeTypes.find((nodeType) => nodeType.type === node?.__blueprint?.type) || null;
}

export function getNodeViewPosition(area, node) {
  return area.nodeViews.get(node?.id)?.position || { x: 40, y: 40 };
}

export function generateNodeLabel(typeDefinition, existingCount = 0) {
  const strategy = typeDefinition?.nameStrategy || { mode: 'free' };

  if (strategy.mode === 'preset' && strategy.value) {
    return strategy.value;
  }

  if (strategy.mode === 'auto') {
    const prefix = strategy.prefix || typeDefinition?.label || typeDefinition?.type || 'Node';

    return `${prefix} ${existingCount + 1}`;
  }

  return typeDefinition?.label || typeDefinition?.type || 'Node';
}

export function findAutoConnection(sourceNode, targetNode, editor, nodeTypes) {
  const sourceType = getNodeTypeDefinition(sourceNode, nodeTypes);
  const targetType = getNodeTypeDefinition(targetNode, nodeTypes);

  if (!sourceType || !targetType) {
    return null;
  }

  const connections = editor.getConnections();
  const freeOutputs = sourceType.outputs.filter((output) => {
    if (output.multiple) {
      return true;
    }

    return !connections.some((connection) => (
      connection.source === sourceNode.id && String(connection.sourceOutput) === output.key
    ));
  });
  const freeInputs = targetType.inputs.filter((input) => (
    !connections.some((connection) => (
      connection.target === targetNode.id && String(connection.targetInput) === input.key
    ))
  ));
  const compatiblePairs = freeOutputs.flatMap((output) => (
    freeInputs
      .filter((input) => portsCompatible(output, input))
      .map((input) => ({ output, input }))
  ));

  if (freeOutputs.length !== 1 || freeInputs.length !== 1 || compatiblePairs.length !== 1) {
    return null;
  }

  return compatiblePairs[0];
}
