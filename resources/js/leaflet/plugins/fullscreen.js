/**
 * Fullscreen control plugin.
 *
 * The control targets the Daisy Leaflet root instead of the internal Leaflet
 * container, because Daisy controls (layers, settings, draw toolbar) are
 * siblings of the Leaflet container and must remain visible in fullscreen.
 *
 * @module leaflet/plugins/fullscreen
 */

/**
 * @param {L} L - The Leaflet namespace.
 * @param {L.Map} map - The Leaflet map instance.
 * @param {Object} context
 * @returns {void}
 */
function addRootFullscreenControl(L, map, context = {}) {
    if (typeof L.Control?.extend !== 'function') {
        return;
    }

    const root = context.root || map.getContainer?.();
    const syncMapSize = () => {
        window.requestAnimationFrame?.(() => map.invalidateSize?.({ animate: false }));
        window.setTimeout(() => map.invalidateSize?.({ animate: false }), 120);
    };

    const NativeFullscreenControl = L.Control.extend({
        options: {
            position: 'topleft',
        },

        onAdd() {
            const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            const button = L.DomUtil.create('a', 'leaflet-control-zoom-fullscreen leaflet-fullscreen-icon', container);
            const syncState = () => {
                const isFullscreen = document.fullscreenElement === root;

                root.classList.toggle('daisy-leaflet-fullscreen-active', isFullscreen);
                button.classList.toggle('leaflet-fullscreen-on', isFullscreen);
                button.setAttribute('aria-pressed', String(isFullscreen));
                syncMapSize();
            };

            button.href = '#';
            button.role = 'button';
            button.title = 'Plein écran';
            button.setAttribute('aria-label', 'Plein écran');
            button.setAttribute('aria-pressed', 'false');

            L.DomEvent.disableClickPropagation(container);
            L.DomEvent.on(button, 'click', L.DomEvent.stop);
            L.DomEvent.on(button, 'click', () => {
                if (document.fullscreenElement === root) {
                    void document.exitFullscreen?.();

                    return;
                }

                if (typeof root.requestFullscreen === 'function') {
                    void root.requestFullscreen();
                    return;
                }

                root.classList.add('daisy-leaflet-fullscreen-active');
                button.classList.add('leaflet-fullscreen-on');
                button.setAttribute('aria-pressed', 'true');
                syncMapSize();
            });
            document.addEventListener('fullscreenchange', syncState);
            context.cleanups?.push(() => {
                document.removeEventListener('fullscreenchange', syncState);
                root.classList.remove('daisy-leaflet-fullscreen-active');
            });

            return container;
        },
    });

    new NativeFullscreenControl().addTo(map);
}

/**
 * Adds the fullscreen control to the map.
 *
 * @param {L} L - The Leaflet namespace.
 * @param {L.Map} map - The Leaflet map instance.
 * @param {Object} cfg
 * @param {Object} context
 * @returns {Promise<void>}
 */
export async function apply(L, map, cfg = {}, context = {}) {
    addRootFullscreenControl(L, map, context);
}

export {
    addRootFullscreenControl,
};
