import { beforeEach, describe, expect, it, vi } from 'vitest';

const map = {
    fitBounds: vi.fn(),
    remove: vi.fn(),
    setView: vi.fn(() => map),
};
const createdLayers = [];
const drawings = [];
const tileLayers = [];

function createLayer() {
    const layer = { addTo: vi.fn(() => layer), getBounds: vi.fn(() => ({ isValid: () => true })), remove: vi.fn() };
    createdLayers.push(layer);

    return layer;
}

vi.mock('leaflet', () => ({
    default: {
        geoJSON: vi.fn(() => createLayer()),
        map: vi.fn(() => map),
        tileLayer: vi.fn(() => {
            const layer = { addTo: vi.fn(() => layer), remove: vi.fn() };
            tileLayers.push(layer);

            return layer;
        }),
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
                <input data-daisy-kit-map-value type="hidden">
                <fieldset data-daisy-kit-map-layers hidden><legend>Layers</legend></fieldset>
                <fieldset data-daisy-kit-map-basemaps hidden><legend>Basemaps</legend></fieldset>
                <fieldset data-daisy-kit-map-tools><button data-daisy-kit-map-mode="linestring" type="button">Draw line</button><button data-daisy-kit-map-mode="polygon" type="button">Draw area</button></fieldset>
                <button data-daisy-kit-map-export disabled type="button">Export drawing</button>
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
        tileLayers.splice(0);
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
            tileUrl: 'https://tiles.example.test/{z}/{x}/{y}.png',
            basemaps: [
                { id: 'light', label: 'Light', selected: true, url: 'https://tiles.example.test/light/{z}/{x}/{y}.png' },
                { id: 'dark', label: 'Dark', url: 'https://tiles.example.test/dark/{z}/{x}/{y}.png' },
            ],
        });
        const layerEvents = [];
        element.addEventListener('daisy-kit:map:layer', (event) => layerEvents.push(event.detail));

        mount(element);
        const layer = element.querySelector('[data-daisy-kit-map-layer="districts"]');
        layer.checked = false;
        layer.dispatchEvent(new Event('change', { bubbles: true }));
        element.querySelector('[data-daisy-kit-map-mode="polygon"]').click();
        drawings[0].handlers.finish('shape');
        element.querySelector('[data-daisy-kit-map-export]').click();
        const dark = element.querySelector('[data-daisy-kit-map-basemap="dark"]');
        dark.checked = true;
        dark.dispatchEvent(new Event('change', { bubbles: true }));

        expect(layerEvents).toEqual([{ id: 'districts', visible: false }]);
        expect(createdLayers[0].remove).toHaveBeenCalledOnce();
        expect(element.querySelector('[data-daisy-kit-map-measurement]').textContent).toContain('m²');
        expect(element.querySelector('[data-daisy-kit-map-mode="polygon"]').getAttribute('aria-pressed')).toBe('true');
        expect(tileLayers).toHaveLength(2);
        expect(dark.checked).toBe(true);
        expect(JSON.parse(element.querySelector('[data-daisy-kit-map-value]').value).features).toHaveLength(1);
    });
});
