import { rankItem } from '@tanstack/match-sorter-utils';

export function nodeMatches(node, query, mode) {
    if (!query) return true;
    const label = `${node.label} ${node.description}`;
    return mode === 'fuzzy'
        ? rankItem(label, query).passed
        : label.toLocaleLowerCase().includes(query.toLocaleLowerCase());
}

export function matchingNodes(model, query, mode) {
    if (!query) return null;
    const matches = new Set();
    for (const node of model.nodes.values()) {
        if (!nodeMatches(node, query, mode)) continue;
        matches.add(node.id);
        model.ancestors(node.id).forEach((id) => matches.add(id));
    }
    return matches;
}
