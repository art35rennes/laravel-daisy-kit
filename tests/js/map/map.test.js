import { beforeEach, describe, expect, it, vi } from 'vitest';

const { leafletMap, mergeIconOptions } = vi.hoisted(() => ({ leafletMap: vi.fn(), mergeIconOptions: vi.fn() }));

const map = {
    fitBounds: vi.fn(),
    invalidateSize: vi.fn(),
    on: vi.fn(),
    off: vi.fn(),
    remove: vi.fn(),
    setView: vi.fn(() => map),
};
const createdLayers = [];
const drawings = [];
const tileLayers = [];
const wmsLayers = [];
const markers = [];

function createLayer() {
    const layer = { addTo: vi.fn(() => layer), getBounds: vi.fn(() => ({ isValid: () => true })), remove: vi.fn() };
    createdLayers.push(layer);

    return layer;
}

vi.mock('leaflet', () => ({
    default: {
        Icon: { Default: { mergeOptions: mergeIconOptions } },
        geoJSON: vi.fn(() => createLayer()),
        map: leafletMap,
        marker: vi.fn(() => {
            const marker = { addTo: vi.fn(() => marker), off: vi.fn(), on: vi.fn(), remove: vi.fn() };
            markers.push(marker);

            return marker;
        }),
        tileLayer: Object.assign(vi.fn(() => {
            const layer = { addTo: vi.fn(() => layer), remove: vi.fn() };
            tileLayers.push(layer);

            return layer;
        }), {
            wms: vi.fn(() => {
                const layer = { addTo: vi.fn(() => layer), remove: vi.fn() };
                wmsLayers.push(layer);

                return layer;
            }),
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
        redo = vi.fn(() => true);
        setMode = vi.fn();
        start = vi.fn();
        stop = vi.fn();
        undo = vi.fn(() => true);
    },
    TerraDrawLineStringMode: class {},
    TerraDrawPointMode: class {},
    TerraDrawPolygonMode: class {},
    TerraDrawSelectMode: class {},
    TerraDrawSessionUndoRedo: class {},
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
                <button data-daisy-kit-map-geolocate type="button">Use my location</button>
                <fieldset data-daisy-kit-map-layers hidden><legend>Layers</legend></fieldset>
                <fieldset data-daisy-kit-map-basemaps hidden><legend>Basemaps</legend></fieldset>
                <fieldset data-daisy-kit-map-tools><button data-daisy-kit-map-mode="point" type="button">Draw point</button><button data-daisy-kit-map-mode="linestring" type="button">Draw line</button><button data-daisy-kit-map-mode="polygon" type="button">Draw area</button><button data-daisy-kit-map-mode="edit" type="button">Edit drawing</button><button data-daisy-kit-map-mode="select" type="button">Select drawing</button><button data-daisy-kit-map-mode="spatial-select" type="button">Select geographic feature</button><button data-daisy-kit-map-history="undo" disabled type="button">Undo</button><button data-daisy-kit-map-history="redo" disabled type="button">Redo</button></fieldset>
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
        wmsLayers.splice(0);
        markers.splice(0);
        map.fitBounds.mockClear();
        map.invalidateSize.mockClear();
        map.remove.mockClear();
        map.setView.mockClear();
        map.on.mockClear();
        map.off.mockClear();
        leafletMap.mockClear();
        leafletMap.mockImplementation(() => map);
    });

    it('configures Leaflet marker assets through statically imported Vite URLs', () => {
        expect(mergeIconOptions).toHaveBeenCalledWith(expect.objectContaining({
            iconRetinaUrl: expect.any(String),
            iconUrl: expect.any(String),
            shadowUrl: expect.any(String),
        }));
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

    it('uses a positive-size observer instead of Leaflet resize animation', () => {
        let resizeCallback;
        const observe = vi.fn();
        const disconnect = vi.fn();
        vi.stubGlobal('ResizeObserver', class {
            constructor(callback) { resizeCallback = callback; }
            disconnect = disconnect;
            observe = observe;
        });
        const element = root({ geojson: { type: 'FeatureCollection', features: [] } });
        const canvas = element.querySelector('[data-daisy-kit-map-canvas]');
        Object.defineProperties(canvas, {
            clientHeight: { configurable: true, value: 240 },
            clientWidth: { configurable: true, value: 320 },
        });

        mount(element);
        resizeCallback();

        expect(map.invalidateSize).toHaveBeenCalledWith({ animate: false, pan: false });
        expect(vi.mocked(map.setView)).toHaveBeenCalled();
        expect(leafletMap.mock.calls.at(-1)?.[1]).toMatchObject({ trackResize: false });

        unmount(element);

        expect(disconnect).toHaveBeenCalledOnce();
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
            wms: [{ id: 'zoning', label: 'Zoning', layers: 'city:zoning', url: 'https://maps.example.test/wms' }],
        });
        const layerEvents = [];
        const selectionEvents = [];
        element.addEventListener('daisy-kit:map:layer', (event) => layerEvents.push(event.detail));
        element.addEventListener('daisy-kit:map:select', (event) => selectionEvents.push(event.detail));

        mount(element);
        const layer = element.querySelector('[data-daisy-kit-map-layer="districts"]');
        expect(layer.classList.contains('checkbox-primary')).toBe(true);
        expect(element.querySelector('[data-daisy-kit-map-basemap="light"]').classList.contains('radio-primary')).toBe(true);
        layer.checked = false;
        layer.dispatchEvent(new Event('change', { bubbles: true }));
        element.querySelector('[data-daisy-kit-map-mode="polygon"]').click();
        drawings[0].handlers.finish('shape');
        drawings[0].handlers.history({ redoStackSize: 0, undoStackSize: 1 });
        element.querySelector('[data-daisy-kit-map-history="undo"]').click();
        drawings[0].handlers.select('shape');
        element.querySelector('[data-daisy-kit-map-export]').click();
        const dark = element.querySelector('[data-daisy-kit-map-basemap="dark"]');
        dark.checked = true;
        dark.dispatchEvent(new Event('change', { bubbles: true }));
        const zoning = element.querySelector('[data-daisy-kit-map-wms="zoning"]');
        zoning.checked = false;
        zoning.dispatchEvent(new Event('change', { bubbles: true }));

        expect(layerEvents).toEqual([{ id: 'districts', visible: false }]);
        expect(createdLayers[0].remove).toHaveBeenCalledOnce();
        expect(element.querySelector('[data-daisy-kit-map-measurement]').textContent).toContain('m²');
        expect(selectionEvents).toMatchObject([{ id: 'shape', measurement: expect.stringContaining('m²') }]);
        expect(element.querySelector('[data-daisy-kit-map-mode="polygon"]').getAttribute('aria-pressed')).toBe('true');
        expect(drawings[0].undo).toHaveBeenCalledOnce();
        expect(element.querySelector('[data-daisy-kit-map-history="undo"]').disabled).toBe(false);
        expect(tileLayers).toHaveLength(2);
        expect(wmsLayers).toHaveLength(1);
        expect(wmsLayers[0].remove).toHaveBeenCalledOnce();
        expect(dark.checked).toBe(true);
        expect(JSON.parse(element.querySelector('[data-daisy-kit-map-value]').value).features).toHaveLength(1);
    });

    it('renders markers and supports optional geolocation, point/edit modes and spatial selection', () => {
        const originalGeolocation = navigator.geolocation;
        const getCurrentPosition = vi.fn((success) => success({ coords: { latitude: 48.11, longitude: -1.67 } }));
        Object.defineProperty(navigator, 'geolocation', { configurable: true, value: { getCurrentPosition } });
        const element = root({
            drawing: true,
            geolocation: true,
            geojson: {
                features: [{ geometry: { coordinates: [[[-1.7, 48.1], [-1.6, 48.1], [-1.6, 48.2], [-1.7, 48.1]]], type: 'Polygon' }, id: 'district', type: 'Feature' }],
                type: 'FeatureCollection',
            },
            markers: [{ id: 'city-hall', label: 'City hall', position: [48.11, -1.67] }],
            spatialSelection: true,
        });
        const selections = [];
        element.addEventListener('daisy-kit:map:spatial-select', (event) => selections.push(event.detail));

        mount(element);
        element.querySelector('[data-daisy-kit-map-geolocate]').click();
        element.querySelector('[data-daisy-kit-map-mode="point"]').click();
        element.querySelector('[data-daisy-kit-map-mode="edit"]').click();
        element.querySelector('[data-daisy-kit-map-mode="spatial-select"]').click();
        const clickHandler = map.on.mock.calls.find(([event]) => event === 'click')?.[1];
        clickHandler({ latlng: { lat: 48.15, lng: -1.65 } });

        expect(getCurrentPosition).toHaveBeenCalledOnce();
        expect(markers).toHaveLength(1);
        expect(map.setView).toHaveBeenCalledWith([48.11, -1.67], 12);
        expect(drawings[0].setMode).toHaveBeenNthCalledWith(1, 'point');
        expect(drawings[0].setMode).toHaveBeenNthCalledWith(2, 'select');
        expect(selections).toEqual([expect.objectContaining({ feature: expect.objectContaining({ id: 'district' }) })]);
        expect(element.dataset.daisyKitSpatialSelection).toBe('district');
        unmount(element);
        expect(markers[0].remove).toHaveBeenCalledOnce();
        expect(map.off).toHaveBeenCalledWith('click', expect.any(Function));
        Object.defineProperty(navigator, 'geolocation', { configurable: true, value: originalGeolocation });
    });
});
