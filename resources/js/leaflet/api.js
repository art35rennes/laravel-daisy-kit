/**
 * Public runtime API exposed on the Leaflet component root.
 *
 * The API intentionally stays small and stable so host applications can control
 * maps from Livewire/Turbo screens without depending on internal plugin state.
 */

/**
 * @param {HTMLElement} root
 * @param {Object} map
 * @param {Object} cfg
 * @param {Object} context
 * @returns {Object}
 */
function createLeafletApi(root, map, cfg, context) {
    const emptyCollection = () => ({ type: 'FeatureCollection', features: [] });
    let destroyed = false;

    return {
        map,
        config: cfg,
        context,
        exportGeoJSON: () => (
            typeof context.exportGeoJSON === 'function'
                ? context.exportGeoJSON()
                : emptyCollection()
        ),
        setMode: mode => context.drawApi?.setMode?.(mode) ?? false,
        clearSelection: () => context.drawApi?.clearSelection?.() ?? false,
        deleteSelected: () => context.drawApi?.deleteSelected?.() ?? false,
        undo: () => context.drawApi?.undo?.() ?? false,
        redo: () => context.drawApi?.redo?.() ?? false,
        destroy: () => {
            if (destroyed) {
                return false;
            }

            destroyed = true;

            (context.cleanups || []).slice().reverse().forEach(cleanup => {
                try {
                    cleanup();
                } catch {
                    // Cleanup must be best-effort so a partial plugin failure does not strand the map.
                }
            });

            if (typeof map.remove === 'function') {
                map.remove();
            }

            delete root.dataset.leafletReady;
            delete root.daisyLeaflet;
            delete root.__daisyLeafletInitializing;

            return true;
        },
    };
}

export {
    createLeafletApi,
};
