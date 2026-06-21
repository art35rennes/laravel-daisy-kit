export function serializeEditor(editor, area, fallbackViewport) {
  const nodes = editor.getNodes().map((node, index) => {
    const view = area.nodeViews.get(node.id);

    return {
      id: node.id,
      type: node.__blueprint?.type || 'node',
      label: node.label,
      position: {
        x: view?.position?.x ?? index * 260,
        y: view?.position?.y ?? 0,
      },
      data: { ...(node.__blueprint?.data || {}) },
    };
  });

  const edges = editor.getConnections().map((connection) => ({
    id: connection.id,
    source: connection.source,
    sourcePort: String(connection.sourceOutput),
    target: connection.target,
    targetPort: String(connection.targetInput),
    data: { ...(connection.data || {}) },
  }));

  return {
    version: 1,
    nodes,
    edges,
    viewport: {
      x: area.area?.transform?.x ?? fallbackViewport.x,
      y: area.area?.transform?.y ?? fallbackViewport.y,
      zoom: area.area?.transform?.k ?? fallbackViewport.zoom,
    },
  };
}
