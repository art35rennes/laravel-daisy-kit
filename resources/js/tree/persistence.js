export function createPersistence(key) {
    const storageKey = typeof key === 'string' && key ? `daisy-kit:tree:${key}` : null;
    return {
        read() {
            if (!storageKey) return null;
            try {
                const stored = JSON.parse(localStorage.getItem(storageKey) ?? 'null');
                return stored && typeof stored === 'object' && !Array.isArray(stored) ? stored : null;
            } catch { return null; }
        },
        write(state) {
            if (!storageKey) return;
            try { localStorage.setItem(storageKey, JSON.stringify(state)); } catch { /* Optional storage. */ }
        },
    };
}
