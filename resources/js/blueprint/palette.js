import { createNode } from './nodes.js';

export function createNodeFactory(nodeType, nodeTypes, sockets, readonly) {
  return () => createNode({
    id: `${nodeType.type}-${Date.now()}`,
    type: nodeType.type,
    label: nodeType.label,
    data: nodeType.defaults,
  }, nodeTypes, sockets, readonly);
}

export function createPaletteRegistry(nodeTypes, sockets, readonly) {
  const nodeFactories = [];
  const typeGroups = nodeTypes.reduce((groups, nodeType) => {
    if (!groups.has(nodeType.category)) {
      groups.set(nodeType.category, []);
    }

    const factory = createNodeFactory(nodeType, nodeTypes, sockets, readonly);

    nodeFactories.push(factory);
    groups.get(nodeType.category).push([nodeType.label, factory]);

    return groups;
  }, new Map());

  return {
    nodeFactories,
    typeGroups,
    contextMenuItems: Array.from(typeGroups.entries()),
  };
}
