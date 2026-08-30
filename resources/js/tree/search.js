import { rankItem } from '@tanstack/match-sorter-utils';

export function matchingNodes(model, query, mode) {
    if (!query) return null;
    const matches = new Set();
    const normalized = query.toLocaleLowerCase();
    for (const node of model.nodes.values()) {
        const label = `${node.label} ${node.description}`;
        const matched = mode === 'fuzzy' ? rankItem(label, query).passed : label.toLocaleLowerCase().includes(normalized);
        if (!matched) continue;
        matches.add(node.id);
        model.ancestors(node.id).forEach((id) => matches.add(id));
    }
    return matches;
}
