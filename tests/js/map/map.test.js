import { beforeEach, describe, expect, it, vi } from 'vitest';

const mocks = vi.hoisted(() => ({
    clusterGroups: [],
    drawings: [],
    layers: [],
    leafletMap: vi.fn(),
    markers: [],
    mergeIconOptions: vi.fn(),
    scales: [],
    tiles: [],
    wms: [],
}));

const map = {
    fitBounds: vi.fn(),
    getCenter: vi.fn(() => ({ lat: 48.1, lng: -1.6 })),
    getZoom: vi.fn(() => 12),
    invalidateSize: vi.fn(),
    off: vi.fn(),
    on: vi.fn(),
    remove: vi.fn(),
    setView: vi.fn(() => map),
};

function layer() {
    const handlers = {};
    const value = {
        addTo: vi.fn(() => value),
        bindPopup: vi.fn(() => value),
        getBounds: vi.fn(() => ({ isValid: () => true })),
        handlers,
        off: vi.fn((event) => { delete handlers[event]; }),
        on: vi.fn((event, handler) => { handlers[event] = handler; }),
        remove: vi.fn(),
    };
    mocks.layers.push(value);

    return value;
}

vi.mock('leaflet', () => ({
    default: {
        Icon: { Default: { mergeOptions: mocks.mergeIconOptions } },
        control: {
            scale: vi.fn(() => {
                const control = { addTo: vi.fn(() => control) };
                mocks.scales.push(control);

                return control;
            }),
        },
        divIcon: vi.fn((options) => options),
        featureGroup: vi.fn(() => ({ getBounds: () => ({ isValid: () => true }) })),
        geoJSON: vi.fn((data, options = {}) => {
            const value = layer();
            data?.features?.forEach((feature) => options.onEachFeature?.(feature, value));

            return value;
        }),
        icon: vi.fn((options) => options),
        map: mocks.leafletMap,
        marker: vi.fn(() => {
            const value = layer();
            mocks.markers.push(value);

            return value;
        }),
        markerClusterGroup: vi.fn(() => {
            const value = {
                addLayer: vi.fn(),
                addTo: vi.fn(() => value),
                remove: vi.fn(),
                removeLayer: vi.fn(),
            };
            mocks.clusterGroups.push(value);

            return value;
        }),
        tileLayer: Object.assign(vi.fn(() => {
            const value = layer();
            mocks.tiles.push(value);

            return value;
        }), {
            wms: vi.fn(() => {
                const value = layer();
                mocks.wms.push(value);

                return value;
            }),
        }),
    },
}));

vi.mock('leaflet.markercluster/src/index.js', () => ({
    MarkerClusterGroup: class {
        addLayer = vi.fn();
        addTo = vi.fn(() => {
            mocks.clusterGroups.push(this);

            return this;
        });
        remove = vi.fn();
        removeLayer = vi.fn();
    },
}));
vi.mock('leaflet-gesture-handling', () => ({}));

vi.mock('terra-draw', () => ({
    TerraDraw: class {
        handlers = {};
        snapshot = [];
        options;

        constructor(options) { this.options = options; mocks.drawings.push(this); }
        addFeatures = vi.fn((features) => { this.snapshot.push(...features); });
        getSnapshot = vi.fn(() => this.snapshot);
        getSnapshotFeature = vi.fn((id) => {
            const existing = this.snapshot.find((feature) => feature.id === id);
            if (existing) return existing;
            const feature = {
                geometry: { coordinates: [[[-1.7, 48.1], [-1.6, 48.1], [-1.6, 48.2], [-1.7, 48.1]]], type: 'Polygon' },
                id,
                properties: {},
                type: 'Feature',
            };
            this.snapshot.push(feature);

            return feature;
        });
        off = vi.fn();
        on = vi.fn((event, handler) => { this.handlers[event] = handler; });
        redo = vi.fn(() => true);
        removeFeatures = vi.fn((ids) => { this.snapshot = this.snapshot.filter((feature) => !ids.includes(feature.id)); });
        setMode = vi.fn();
        start = vi.fn();
        stop = vi.fn();
        undo = vi.fn(() => true);
        updateFeatureProperties = vi.fn((id, properties) => {
            const feature = this.getSnapshotFeature(id);
            feature.properties = { ...feature.properties, ...properties };
        });
    },
    TerraDrawLineStringMode: class {},
    TerraDrawPointMode: class {},
    TerraDrawPolygonMode: class {},
    TerraDrawRectangleMode: class {},
    TerraDrawSelectMode: class {},
    TerraDrawSessionUndoRedo: class {},
}));
vi.mock('terra-draw-leaflet-adapter', () => ({ TerraDrawLeafletAdapter: class {} }));

import { getInstance, mount, mountAll, unmount } from '../../../resources/js/map.js';

function root(configuration, id = '') {
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
        <section data-daisy-kit-module="map" ${id ? `id="${id}"` : ''} aria-busy="true">
            <p data-daisy-kit-status hidden></p>
            <div data-daisy-kit-content>
                <div data-daisy-kit-map-canvas></div>
                <div data-daisy-kit-map-loading></div>
                <div data-daisy-kit-map-empty hidden></div>
                <div data-daisy-kit-map-error hidden><p data-daisy-kit-map-error-message></p><button data-daisy-kit-map-retry></button></div>
                <output data-daisy-kit-map-measurement hidden></output>
                <output data-daisy-kit-map-active-mode hidden></output>
                <input data-daisy-kit-map-value type="hidden">
                <button data-daisy-kit-map-geolocate type="button"></button>
                <button data-daisy-kit-map-fit-bounds type="button"></button>
                <button data-daisy-kit-map-fullscreen type="button"></button>
                <fieldset data-daisy-kit-map-layers hidden><legend>Layers</legend></fieldset>
                <fieldset data-daisy-kit-map-basemaps hidden><legend>Basemaps</legend></fieldset>
                <fieldset data-daisy-kit-map-tools>
                    <button data-daisy-kit-map-mode="point" type="button"></button>
                    <button data-daisy-kit-map-mode="linestring" type="button"></button>
                    <button data-daisy-kit-map-mode="polygon" type="button"></button>
                    <button data-daisy-kit-map-mode="rectangle" type="button"></button>
                    <button data-daisy-kit-map-mode="edit" type="button"></button>
                    <button data-daisy-kit-map-history="undo" disabled type="button"></button>
                    <button data-daisy-kit-map-history="redo" disabled type="button"></button>
                    <button data-daisy-kit-map-delete-selected disabled type="button"></button>
                    <button data-daisy-kit-map-export disabled type="button"></button>
                </fieldset>
                <aside data-daisy-kit-map-selection hidden><p data-daisy-kit-map-selection-summary></p><button data-daisy-kit-map-clear-selection></button></aside>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;
    document.body.append(wrapper.firstElementChild);

    return document.body.lastElementChild;
}

async function mounted(element) {
    const event = new Promise((resolve) => element.addEventListener('daisy-kit:map:mounted', resolve, { once: true }));
    const instance = mount(element);
    await event;

    return instance;
}

describe('map entry', () => {
    beforeEach(() => {
        document.body.replaceChildren();
        localStorage.clear();
        Object.values(mocks).filter(Array.isArray).forEach((values) => values.splice(0));
        Object.values(map).filter((value) => typeof value?.mockClear === 'function').forEach((mock) => mock.mockClear());
        mocks.leafletMap.mockReset();
        mocks.leafletMap.mockImplementation(() => map);
        vi.stubGlobal('ResizeObserver', class {
            disconnect = vi.fn();
            observe = vi.fn();
        });
        vi.stubGlobal('requestAnimationFrame', (callback) => { callback(); return 1; });
        vi.stubGlobal('cancelAnimationFrame', vi.fn());
    });

    it('configures Leaflet marker assets through Vite URLs', () => {
        expect(mocks.mergeIconOptions).toHaveBeenCalledWith(expect.objectContaining({
            iconRetinaUrl: expect.any(String),
            iconUrl: expect.any(String),
            shadowUrl: expect.any(String),
        }));
    });

    it('returns one stable facade per isolated root', async () => {
        const first = root({ geojson: { features: [], type: 'FeatureCollection' } }, 'first-map');
        const second = root({ drawing: true }, 'second-map');
        const [firstInstance, secondInstance] = mountAll(document);

        expect(firstInstance).toBe(getInstance(first));
        expect(mount(first)).toBe(firstInstance);
        expect(secondInstance).not.toBe(firstInstance);
        expect(Object.keys(firstInstance).sort()).toEqual([
            'clearSelection', 'deleteSelected', 'destroy', 'exportGeoJSON', 'fitBounds',
            'getDrawLayer', 'getLeafletMap', 'getSelection', 'getState', 'invalidateSize',
            'locate', 'redo', 'refreshLayer', 'setBasemap', 'setDrawLayer', 'setGeoJSON',
            'setLayerData', 'setLayerVisibility', 'setMarkers', 'setMode', 'setView',
            'startGeolocation', 'stopGeolocation', 'undo',
        ]);
        await vi.waitFor(() => expect(first.dataset.daisyKitState).toBe('ready'));

        firstInstance.destroy();
        expect(getInstance(first)).toBeNull();
        expect(getInstance(second)).toBe(secondInstance);
    });

    it('guards resize invalidation and non-finite external views', async () => {
        let resizeCallback;
        vi.stubGlobal('ResizeObserver', class {
            constructor(callback) { resizeCallback = callback; }
            disconnect = vi.fn();
            observe = vi.fn();
        });
        const element = root({ center: [48.1, -1.6], geojson: { features: [], type: 'FeatureCollection' }, zoom: 12 });
        const canvas = element.querySelector('[data-daisy-kit-map-canvas]');
        const instance = await mounted(element);
        Object.defineProperties(canvas, {
            clientHeight: { configurable: true, value: 0 },
            clientWidth: { configurable: true, value: 0 },
        });
        resizeCallback();

        expect(map.invalidateSize).not.toHaveBeenCalled();
        expect(instance.setView([Number.NaN, Number.NaN], 8)).toBe(false);
        expect(map.setView).not.toHaveBeenCalledWith([Number.NaN, Number.NaN], 8, expect.anything());

        Object.defineProperties(canvas, {
            clientHeight: { configurable: true, value: 240 },
            clientWidth: { configurable: true, value: 320 },
        });
        resizeCallback();
        expect(map.invalidateSize).toHaveBeenCalledWith({ animate: false, pan: false });
    });

    it('controls GeoJSON, XYZ and WMS layers through the facade', async () => {
        const element = root({
            basemaps: [
                { id: 'light', label: 'Light', selected: true, type: 'xyz', url: '/light/{z}/{x}/{y}.png' },
                { id: 'dark', label: 'Dark', type: 'xyz', url: '/dark/{z}/{x}/{y}.png' },
            ],
            layers: [
                { data: { features: [], type: 'FeatureCollection' }, id: 'districts', label: 'Districts', type: 'geojson', visible: true },
                { id: 'zoning', label: 'Zoning', options: { layers: 'city:zoning' }, type: 'wms', url: 'https://maps.example.test/wms', visible: false },
            ],
        });
        const events = [];
        element.addEventListener('daisy-kit:map:layer', (event) => events.push(event.detail));
        const instance = await mounted(element);

        expect(element.querySelector('[data-daisy-kit-map-layer="districts"]').classList).toContain('checkbox-primary');
        expect(mocks.wms).toHaveLength(1);
        expect(instance.setLayerVisibility('districts', false)).toBe(true);
        expect(instance.setBasemap('dark')).toBe(true);
        expect(await instance.setLayerData('districts', { features: [{ geometry: null, type: 'Feature' }], type: 'FeatureCollection' })).toBe(true);
        expect(events).toEqual([{ id: 'districts', visible: false }]);
        expect(instance.getState()).toMatchObject({ basemap: 'dark', layerVisibility: { districts: false, zoning: false } });
    });

    it('loads remote GeoJSON with retry and reports local layer errors', async () => {
        const fetch = vi.fn()
            .mockResolvedValueOnce({ ok: false, status: 503 })
            .mockResolvedValueOnce({ json: async () => ({ features: [], type: 'FeatureCollection' }), ok: true });
        vi.stubGlobal('fetch', fetch);
        const element = root({ layers: [{ id: 'remote', label: 'Remote', type: 'geojson', url: '/api/map/remote' }] });
        const errors = [];
        element.addEventListener('daisy-kit:map:layer-error', (event) => errors.push(event.detail));
        const instance = await mounted(element);

        expect(errors[0]).toMatchObject({ id: 'remote', message: expect.stringContaining('503') });
        expect(element.dataset.daisyKitState).toBe('error');
        expect(await instance.refreshLayer('remote')).toBe(true);
        expect(element.dataset.daisyKitState).toBe('ready');
        expect(fetch).toHaveBeenCalledTimes(2);
    });

    it('clusters markers and preserves explicit popup renderers', async () => {
        const element = root({
            cluster: { enabled: true, maxClusterRadius: 60 },
            markers: [{
                id: 'office',
                label: 'Office',
                popup: { content: '<strong>Office</strong>', renderer: 'trusted-html' },
                position: [48.1, -1.6],
            }],
        });
        await mounted(element);

        expect(mocks.clusterGroups).toHaveLength(1);
        expect(mocks.clusterGroups[0].addLayer).toHaveBeenCalledOnce();
        expect(mocks.leafletMap).toHaveBeenCalledWith(expect.anything(), expect.objectContaining({ maxZoom: 19 }));
        expect(mocks.markers[0].bindPopup).toHaveBeenCalledWith('<strong>Office</strong>');
        expect(window.L).toBeUndefined();
    });

    it('synchronizes drawing, selection, measurement, history and export', async () => {
        const element = root({
            drawLayers: [{ id: 'water', label: 'Water' }],
            drawing: { enabled: true, rectangle: true },
            labels: { activeMode: 'Active: :mode', selectedFeatures: ':count selected' },
            measure: { enabled: true },
            objectTypes: [{ geometry: 'point', id: 'hydrant', label: 'Hydrant' }],
            value: { features: [], type: 'FeatureCollection' },
        });
        const input = element.querySelector('[data-daisy-kit-map-value]');
        const nativeEvents = [];
        input.addEventListener('input', () => nativeEvents.push('input'));
        input.addEventListener('change', () => nativeEvents.push('change'));
        const instance = await mounted(element);
        const drawing = mocks.drawings[0];

        expect(instance.setMode('rectangle')).toBe(true);
        await drawing.handlers.finish('shape-1');
        drawing.handlers.select('shape-1');
        drawing.handlers.history({ cause: 'push', redoSize: 0, stack: 'session', undoSize: 1 });

        expect(element.querySelector('[data-daisy-kit-map-measurement]').hidden).toBe(false);
        expect(element.querySelector('[data-daisy-kit-map-measurement]').textContent).toContain('m²');
        expect(element.querySelector('[data-daisy-kit-map-history="undo"]').disabled).toBe(false);
        expect(element.querySelector('[data-daisy-kit-map-history="redo"]').disabled).toBe(true);
        expect(instance.getSelection()).toHaveLength(1);
        expect(instance.getDrawLayer()).toBe('water');
        expect(instance.undo()).toBe(true);
        drawing.handlers.history({ cause: 'undo', redoSize: 1, stack: 'session', undoSize: 0 });
        expect(element.querySelector('[data-daisy-kit-map-history="undo"]').disabled).toBe(true);
        expect(element.querySelector('[data-daisy-kit-map-history="redo"]').disabled).toBe(false);
        expect(instance.redo()).toBe(true);
        expect(instance.exportGeoJSON()).toMatchObject({
            features: [expect.objectContaining({ properties: expect.objectContaining({ drawLayer: 'water', objectType: 'hydrant' }) })],
            type: 'FeatureCollection',
        });
        expect(instance.exportGeoJSON().features[0].properties).not.toHaveProperty('mode');
        expect(nativeEvents).toContain('change');
        expect(JSON.parse(input.value)).toMatchObject({ type: 'FeatureCollection' });
        expect(instance.deleteSelected()).toBe(true);
    });

    it('hydrates public drawing ids without leaking Terra Draw properties', async () => {
        const instance = await mounted(root({
            drawing: true,
            value: {
                features: [{ geometry: { coordinates: [-1.6, 48.1], type: 'Point' }, id: 'asset-1', properties: { asset: true }, type: 'Feature' }],
                type: 'FeatureCollection',
            },
        }));
        const drawing = mocks.drawings.at(-1);

        expect(drawing.options.idStrategy.isValidId('asset-1')).toBe(true);
        expect(drawing.addFeatures).toHaveBeenCalledWith([
            expect.objectContaining({ properties: { asset: true, mode: 'point' } }),
        ]);
        expect(instance.exportGeoJSON().features[0].properties).toEqual({ asset: true });
    });

    it('selects GeoJSON features by click or by a drawn area', async () => {
        const element = root({
            geojson: {
                features: [{ geometry: { coordinates: [-1.6, 48.1], type: 'Point' }, id: 'office', properties: {}, type: 'Feature' }],
                type: 'FeatureCollection',
            },
            spatialSelection: { enabled: true, mode: 'both' },
        });
        const instance = await mounted(element);
        const geojsonLayer = mocks.layers.find((candidate) => candidate.handlers.click);

        expect(instance.setMode('feature-select')).toBe(true);
        geojsonLayer.handlers.click();
        expect(instance.getSelection()).toHaveLength(1);

        instance.clearSelection();
        expect(instance.getSelection()).toEqual([]);
        expect(instance.setMode('spatial-select')).toBe(true);
        await mocks.drawings.at(-1).handlers.finish('selection-area', {});
        expect(instance.getSelection()).toHaveLength(1);
    });

    it('supports one-shot and watched geolocation', async () => {
        const getCurrentPosition = vi.fn((success) => success({ coords: { accuracy: 5, latitude: 48.11, longitude: -1.67 } }));
        const watchPosition = vi.fn((success) => { success({ coords: { accuracy: 4, latitude: 48.12, longitude: -1.68 } }); return 42; });
        const clearWatch = vi.fn();
        Object.defineProperty(navigator, 'geolocation', { configurable: true, value: { clearWatch, getCurrentPosition, watchPosition } });
        const instance = await mounted(root({ geolocation: { enabled: true, setView: true } }));

        await instance.locate();
        expect(instance.startGeolocation()).toBe(true);
        expect(instance.stopGeolocation()).toBe(true);
        expect(getCurrentPosition).toHaveBeenCalledOnce();
        expect(watchPosition).toHaveBeenCalledOnce();
        expect(clearWatch).toHaveBeenCalledWith(42);
        expect(map.setView).toHaveBeenCalledWith([48.12, -1.68], 12, {});
    });
});
