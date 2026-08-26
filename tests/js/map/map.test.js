import { beforeEach, describe, expect, it, vi } from 'vitest';

const map = {
    fitBounds: vi.fn(),
    remove: vi.fn(),
    setView: vi.fn(() => map),
};
const createdLayers = [];
const drawings = [];

function createLayer() {
    const layer = { addTo: vi.fn(() => layer), getBounds: vi.fn(() => ({ isValid: () => true })), remove: vi.fn() };
    createdLayers.push(layer);

    return layer;
}

vi.mock('leaflet', () => ({
    default: {
        geoJSON: vi.fn(() => createLayer()),
        map: vi.fn(() => map),
    },
}));
vi.mock('terra-draw', () => ({
    TerraDraw: class {
        handlers = {};
        constructor() { drawings.push(this); }
        getSnapshotFeature = vi.fn(() => ({
            geometry: { coordinates: [[[-1.7, 48.1], [-1.6, 48.1], [-1.6, 48.2], [-1.7, 48.1]]], type: 'Polygon' },
            type: 'Feature',
        }));
        off = vi.fn();
        on = vi.fn((event, handler) => { this.handlers[event] = handler; });
        setMode = vi.fn();
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
                <fieldset data-daisy-kit-map-layers hidden><legend>Layers</legend></fieldset>
                <fieldset data-daisy-kit-map-tools><button data-daisy-kit-map-mode="linestring" type="button">Draw line</button><button data-daisy-kit-map-mode="polygon" type="button">Draw area</button></fieldset>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="map"]');
}

describe('map entry', () => {
    beforeEach(() => {
        createdLayers.splice(0);
        drawings.splice(0);
        map.fitBounds.mockClear();
        map.remove.mockClear();
        map.setView.mockClear();
    });

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

    it('controls named GeoJSON layers and exposes drawing measurements', () => {
        const element = root({
            drawing: true,
            layers: [{
                geojson: { features: [], type: 'FeatureCollection' },
                id: 'districts',
                label: 'Districts',
            }],
        });
        const layerEvents = [];
        element.addEventListener('daisy-kit:map:layer', (event) => layerEvents.push(event.detail));

        mount(element);
        const layer = element.querySelector('[data-daisy-kit-map-layer="districts"]');
        layer.checked = false;
        layer.dispatchEvent(new Event('change', { bubbles: true }));
        element.querySelector('[data-daisy-kit-map-mode="polygon"]').click();
        drawings[0].handlers.finish('shape');

        expect(layerEvents).toEqual([{ id: 'districts', visible: false }]);
        expect(createdLayers[0].remove).toHaveBeenCalledOnce();
        expect(element.querySelector('[data-daisy-kit-map-measurement]').textContent).toContain('m²');
        expect(element.querySelector('[data-daisy-kit-map-mode="polygon"]').getAttribute('aria-pressed')).toBe('true');
    });
});
