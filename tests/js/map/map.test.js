import { describe, expect, it, vi } from 'vitest';

const map = {
    fitBounds: vi.fn(),
    remove: vi.fn(),
    setView: vi.fn(() => map),
};
const dataLayer = { addTo: vi.fn(() => dataLayer), getBounds: vi.fn(() => ({ isValid: () => true })), remove: vi.fn() };

vi.mock('leaflet', () => ({
    default: {
        geoJSON: vi.fn(() => dataLayer),
        map: vi.fn(() => map),
    },
}));
vi.mock('terra-draw', () => ({
    TerraDraw: class {
        off = vi.fn();
        on = vi.fn();
        start = vi.fn();
        stop = vi.fn();
    },
    TerraDrawLineStringMode: class {},
    TerraDrawPolygonMode: class {},
}));
vi.mock('terra-draw-leaflet-adapter', () => ({ TerraDrawLeafletAdapter: class {} }));

import { mount, unmount } from '../../../resources/js/map.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="map">
            <p data-daisy-kit-status hidden role="alert"></p>
            <div data-daisy-kit-content>
                <div data-daisy-kit-map-canvas></div>
                <p data-daisy-kit-empty hidden></p>
                <output data-daisy-kit-map-measurement></output>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="map"]');
}

describe('map entry', () => {
    it('mounts GeoJSON data and removes its Leaflet instance', () => {
        const element = root({
            geojson: { type: 'FeatureCollection', features: [] },
            center: [48.1, -1.6],
            zoom: 12,
        });

        mount(element);

        expect(element.dataset.daisyKitState).toBe('ready');
        expect(map.setView).toHaveBeenCalledWith([48.1, -1.6], 12);

        unmount(element);

        expect(map.remove).toHaveBeenCalledOnce();
    });

    it('shows an empty state without data or drawing', () => {
        const element = root({ geojson: null, drawing: false });

        mount(element);

        expect(element.dataset.daisyKitState).toBe('empty');
        expect(element.querySelector('[data-daisy-kit-empty]').hidden).toBe(false);
    });
});
