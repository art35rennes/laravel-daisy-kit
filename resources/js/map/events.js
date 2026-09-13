function snapshot(value) {
    if (value === undefined) return undefined;

    if (typeof structuredClone === 'function') {
        try {
            return structuredClone(value);
        } catch {
            // Fall through for values containing browser or Leaflet instances.
        }
    }

    return JSON.parse(JSON.stringify(value));
}

export function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:map:${name}`, {
        bubbles: true,
        detail: snapshot(detail),
    }));
}
