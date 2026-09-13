export function createPersistence(root, configuration) {
    if (!configuration.persistState.enabled || typeof localStorage === 'undefined') {
        return { load: () => null, save: () => {} };
    }

    const key = configuration.persistState.key
        || root.id
        || `map-${[...document.querySelectorAll('[data-daisy-kit-module="map"]')].indexOf(root)}`;
    const storageKey = `daisy-kit:map:${key}`;

    return {
        load() {
            try {
                const value = JSON.parse(localStorage.getItem(storageKey));

                return value && typeof value === 'object' ? value : null;
            } catch {
                return null;
            }
        },
        save(value) {
            try {
                localStorage.setItem(storageKey, JSON.stringify(value));
            } catch {
                // Storage can be disabled by privacy policies. Map use remains available.
            }
        },
    };
}
