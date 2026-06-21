/* @vitest-environment jsdom */

import { afterEach, describe, expect, it, vi } from 'vitest';
import initLeaflet, {
    addBasemaps,
    addLayerControl,
    addLayerMenuControl,
    addOverlays,
    addUserControls,
    bindLayerToggleEvents,
    collectActiveLayerNames,
    createLeafletApi,
    createMap,
    createOverlayLayer,
    getControlsPositionClasses,
    getOrCreateControlStack,
    loadControlsState,
    loadGeoJsonData,
    normalizeLayerControlConfig,
    normalizeControlsConfig,
    persistControlsState,
    shouldUseNativeLayerControl,
} from '../../../resources/js/leaflet/index.js';
import { apply as applyCluster } from '../../../resources/js/leaflet/plugins/cluster.js';

vi.mock('leaflet.markercluster', () => ({}));

function createLayer(type, payload = {}) {
    return {
        type,
        payload,
        addedTo: null,
        addTo(map) {
            this.addedTo = map;

            return this;
        },
        getBounds() {
            return { isValid: () => false };
        },
    };
}

function createLeafletMock() {
    const L = {
        tileLayer: vi.fn((url, options) => createLayer('xyz', { url, options })),
        geoJSON: vi.fn((data, options) => createLayer('geojson', { data, options })),
        control: {
            layers: vi.fn((baseLayers, overlayLayers, options) => ({
                baseLayers,
                overlayLayers,
                options,
                addedTo: null,
                addTo(map) {
                    this.addedTo = map;

                    return this;
                },
            })),
        },
    };

    L.tileLayer.wms = vi.fn((url, options) => createLayer('wms', { url, options }));

    return L;
}

function createMapMock() {
    return {
        handlers: {},
        hasLayer(layer) {
            return layer.addedTo === this;
        },
        on(events, callback) {
            events.split(' ').forEach(event => {
                this.handlers[event] = callback;
            });
        },
        removeLayer(layer) {
            layer.addedTo = null;
        },
        fire(event, payload = {}) {
            this.handlers[event]?.({ type: event, ...payload });
        },
    };
}

afterEach(() => {
    window.localStorage.clear();
    delete window.L;
    vi.restoreAllMocks();
});

describe('Leaflet GIS layer helpers', () => {
    it('marks a map ready only after the public API is exposed and supports reinit after destroy', async () => {
        vi.spyOn(window, 'requestAnimationFrame').mockImplementation(callback => {
            callback();

            return 1;
        });
        vi.spyOn(window, 'cancelAnimationFrame').mockImplementation(() => {});

        const root = document.createElement('div');
        const container = document.createElement('div');
        container.id = 'leaflet-init-test';
        root.dataset.module = 'leaflet';
        root.innerHTML = `
            <div class="daisy-leaflet-loading"></div>
            <script type="application/json" data-config>
                {"containerId":"leaflet-init-test","center":{"lat":48.117,"lng":-1.678},"zoom":12,"tiles":false}
            </script>
        `;
        root.appendChild(container);
        document.body.appendChild(root);

        const map = {
            handlers: {},
            setView: vi.fn(() => map),
            invalidateSize: vi.fn(),
            fitBounds: vi.fn(),
            remove: vi.fn(),
            on: vi.fn(),
            hasLayer: vi.fn(() => false),
        };
        window.L = {
            map: vi.fn(() => map),
            tileLayer: vi.fn(),
            geoJSON: vi.fn(() => createLayer('geojson')),
            latLngBounds: vi.fn(() => ({
                extend: vi.fn(),
                isValid: () => false,
            })),
            control: {
                layers: vi.fn(),
            },
        };

        const initEvent = vi.fn();
        root.addEventListener('daisy:leaflet:init', initEvent);

        const result = await initLeaflet(root);

        expect(result).toBe(map);
        expect(root.dataset.leafletReady).toBe('1');
        expect(root.daisyLeaflet).toMatchObject({ map, config: expect.any(Object), context: expect.any(Object) });
        expect(initEvent).toHaveBeenCalledTimes(1);
        expect(root.__daisyLeafletInitializing).toBeUndefined();

        expect(root.daisyLeaflet.destroy()).toBe(true);
        expect(map.remove).toHaveBeenCalledTimes(1);
        expect(root.dataset.leafletReady).toBeUndefined();
        expect(root.daisyLeaflet).toBeUndefined();

        const secondResult = await initLeaflet(root);

        expect(secondResult).toBe(map);
        expect(root.daisyLeaflet).toBeDefined();
        expect(window.L.map).toHaveBeenCalledTimes(2);
    });

    it('sets a default max zoom when clustering is enabled', () => {
        const container = document.createElement('div');
        const map = {
            setView: vi.fn(() => map),
        };
        const L = {
            map: vi.fn(() => map),
        };

        const result = createMap(L, container, {
            center: { lat: 48.117, lng: -1.678 },
            zoom: 12,
            cluster: true,
        });

        expect(result).toBe(map);
        expect(L.map).toHaveBeenCalledWith(container, { maxZoom: 18 });
        expect(map.setView).toHaveBeenCalledWith([48.117, -1.678], 12);
    });

    it('uses a safe cluster max zoom fallback when the map has no maxZoom helper', async () => {
        const marker = createLayer('marker');
        const group = {
            markers: [],
            addedTo: null,
            addLayer(layer) {
                this.markers.push(layer);
            },
        };
        const L = {
            markerClusterGroup: vi.fn(() => group),
        };
        const map = {
            addLayer: vi.fn((layer) => {
                layer.addedTo = map;
            }),
        };

        await applyCluster(L, map, { clusterOptions: {} }, { markers: [marker] });

        expect(L.markerClusterGroup).toHaveBeenCalledWith({ disableClusteringAtZoom: 18 });
        expect(group.markers).toEqual([marker]);
        expect(map.addLayer).toHaveBeenCalledWith(group);
    });

    it('creates XYZ and WMS basemaps and activates the configured layer', () => {
        const L = createLeafletMock();
        const map = createMapMock();

        const { baseLayers, activeLayer } = addBasemaps(L, map, {
            basemaps: [
                { id: 'xyz', label: 'Plan', type: 'xyz', url: '/tiles/{z}/{x}/{y}.png' },
                { id: 'wms', label: 'Ortho', type: 'wms', url: '/wms', options: { layers: 'ortho' }, active: true },
            ],
        });

        expect(Object.keys(baseLayers)).toEqual(['Plan', 'Ortho']);
        expect(L.tileLayer).toHaveBeenCalledWith('/tiles/{z}/{x}/{y}.png', {});
        expect(L.tileLayer.wms).toHaveBeenCalledWith('/wms', { layers: 'ortho' });
        expect(activeLayer.type).toBe('wms');
        expect(activeLayer.addedTo).toBe(map);
    });

    it('ignores malformed basemaps and overlays without breaking map initialization', async () => {
        const L = createLeafletMock();
        const map = createMapMock();
        const geojson = { type: 'FeatureCollection', features: [] };

        const { baseLayers } = addBasemaps(L, map, {
            basemaps: [
                null,
                'bad',
                { id: 'missing-url', label: 'Missing URL', type: 'xyz' },
                { id: 'ok', label: 'Plan', type: 'xyz', url: '/tiles/{z}/{x}/{y}.png' },
            ],
        });
        const { overlayLayers, renderedLayers } = await addOverlays(L, map, {
            overlays: [
                null,
                'bad',
                { id: 'missing-raster-url', label: 'Missing Raster', type: 'wms' },
                { id: 'empty-geojson', label: 'Empty GeoJSON', type: 'geojson' },
                { id: 'geojson', label: 'Données', type: 'geojson', data: geojson },
            ],
        });

        expect(Object.keys(baseLayers)).toEqual(['Plan']);
        expect(Object.keys(overlayLayers)).toEqual(['Données']);
        expect(renderedLayers).toHaveLength(1);
    });

    it('creates readonly XYZ, WMS, and GeoJSON overlays', async () => {
        const L = createLeafletMock();
        const map = createMapMock();
        const geojson = { type: 'FeatureCollection', features: [] };

        const { overlayLayers, renderedLayers, editableCollections } = await addOverlays(L, map, {
            draw: true,
            overlays: [
                { id: 'parcels', label: 'Parcelles', type: 'geojson', data: geojson, style: { color: '#f00' } },
                { id: 'risk', label: 'Risques', type: 'wms', url: '/wms', visible: false },
                { id: 'grid', label: 'Grille', type: 'xyz', url: '/grid/{z}/{x}/{y}.png' },
            ],
        });

        expect(Object.keys(overlayLayers)).toEqual(['Parcelles', 'Risques', 'Grille']);
        expect(renderedLayers).toHaveLength(3);
        expect(editableCollections).toEqual([]);
        expect(L.geoJSON).toHaveBeenCalledWith(geojson, { style: { color: '#f00' } });
        expect(overlayLayers.Parcelles.addedTo).toBe(map);
        expect(overlayLayers.Risques.addedTo).toBeNull();
        expect(overlayLayers.Grille.addedTo).toBe(map);
    });

    it('keeps locked overlays visible and out of the user layer menu', async () => {
        const L = createLeafletMock();
        const map = createMapMock();
        const geojson = { type: 'FeatureCollection', features: [] };

        const { overlayLayers, lockedOverlayLayers } = await addOverlays(L, map, {
            layerControl: normalizeLayerControlConfig({ lockedOverlays: ['sector'] }),
            overlays: [
                { id: 'sector', label: 'Secteur imposé', type: 'geojson', data: geojson, visible: false },
                { id: 'labels', label: 'Libellés', type: 'xyz', url: '/labels/{z}/{x}/{y}.png', control: false },
                { id: 'cadastre', label: 'Cadastre', type: 'wms', url: '/wms' },
            ],
        });

        expect(Object.keys(overlayLayers)).toEqual(['Cadastre']);
        expect(Object.keys(lockedOverlayLayers)).toEqual(['Secteur imposé', 'Libellés']);
        expect(lockedOverlayLayers['Secteur imposé'].addedTo).toBe(map);
        expect(lockedOverlayLayers['Libellés'].addedTo).toBe(map);
    });

    it('keeps editable GeoJSON overlays out of readonly layers when draw is enabled', async () => {
        const L = createLeafletMock();
        const map = createMapMock();
        const editable = {
            type: 'FeatureCollection',
            features: [{
                type: 'Feature',
                properties: {},
                geometry: { type: 'Point', coordinates: [2, 48] },
            }],
        };

        const { overlayLayers, renderedLayers, editableCollections } = await addOverlays(L, map, {
            draw: true,
            overlays: [{ id: 'editable', type: 'geojson', data: editable, editable: true }],
        });

        expect(overlayLayers).toEqual({});
        expect(renderedLayers).toEqual([]);
        expect(editableCollections).toEqual([editable]);
        expect(L.geoJSON).not.toHaveBeenCalled();
    });

    it('loads GeoJSON overlays from URL for readonly and editable layers', async () => {
        const L = createLeafletMock();
        const map = createMapMock();
        const remote = { type: 'FeatureCollection', features: [] };

        globalThis.fetch = vi.fn(() => Promise.resolve({
            ok: true,
            text: () => Promise.resolve(JSON.stringify(remote)),
        }));

        const readonlyLayer = await createOverlayLayer(L, { type: 'geojson', url: '/readonly.geojson' });
        const { editableCollections } = await addOverlays(L, map, {
            draw: true,
            overlays: [{ id: 'editable', type: 'geojson', url: '/editable.geojson', editable: true }],
        });

        expect(readonlyLayer.type).toBe('geojson');
        expect(readonlyLayer.payload.data).toEqual(remote);
        expect(editableCollections).toEqual([remote]);
        expect(globalThis.fetch).toHaveBeenCalledWith('/readonly.geojson', {
            headers: { Accept: 'application/geo+json, application/json' },
        });
        expect(globalThis.fetch).toHaveBeenCalledWith('/editable.geojson', {
            headers: { Accept: 'application/geo+json, application/json' },
        });
    });

    it('returns null when remote GeoJSON cannot be loaded', async () => {
        globalThis.fetch = vi.fn(() => Promise.resolve({ ok: false }));

        await expect(loadGeoJsonData({ url: '/missing.geojson' })).resolves.toBeNull();
    });

    it('dispatches Daisy layer toggle events from Leaflet layer events', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const layer = createLayer('xyz');
        const events = [];

        root.addEventListener('daisy:leaflet:layer-toggle', event => events.push(event.detail));
        bindLayerToggleEvents(root, map);

        map.fire('overlayadd', { name: 'Cadastre', layer });

        expect(events).toHaveLength(1);
        expect(events[0]).toMatchObject({
            map,
            name: 'Cadastre',
            type: 'overlayadd',
            layer,
            activeBasemap: null,
            activeOverlays: [],
            lockedOverlays: [],
        });
    });

    it('uses the native Leaflet layer control only when explicitly requested', () => {
        const L = createLeafletMock();
        const map = createMapMock();
        const baseLayer = createLayer('xyz');
        const overlayLayer = createLayer('geojson');

        expect(shouldUseNativeLayerControl({ layerControl: normalizeLayerControlConfig(true) })).toBe(false);
        expect(shouldUseNativeLayerControl({ layerControl: normalizeLayerControlConfig({ native: true }) })).toBe(true);

        expect(addLayerControl(L, map, { layerControl: normalizeLayerControlConfig(true) }, { Plan: baseLayer }, { Cadastre: overlayLayer })).toBeNull();

        const control = addLayerControl(L, map, { layerControl: normalizeLayerControlConfig({ native: true }) }, { Plan: baseLayer }, { Cadastre: overlayLayer });

        expect(control.addedTo).toBe(map);
        expect(L.control.layers).toHaveBeenCalledWith({ Plan: baseLayer }, { Cadastre: overlayLayer }, { collapsed: false });
    });

    it('does not create the Daisy layer menu when native layer control is requested', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const baseLayer = createLayer('xyz');
        const overlayLayer = createLayer('geojson');

        baseLayer.addTo(map);
        overlayLayer.addTo(map);

        const menu = addLayerMenuControl({}, map, {
            containerId: 'map',
        }, {
            root,
            baseLayers: { Plan: baseLayer },
            overlayLayers: { Cadastre: overlayLayer },
            lockedOverlayLayers: {},
            layerControlConfig: normalizeLayerControlConfig({ native: true }),
            controlsState: loadControlsState(null),
            controlsStorageKey: null,
        });

        expect(menu).toBeNull();
        expect(root.querySelector('.daisy-leaflet-layer-controls')).toBeNull();
    });

    it('normalizes and persists user controls state', () => {
        const controls = normalizeControlsConfig({ persist: true, storageKey: 'map-controls', overlays: false });

        expect(controls.persist).toBe(true);
        expect(controls.storageKey).toBe('map-controls');
        expect(controls.basemaps).toBe(true);
        expect(controls.overlays).toBe(false);

        expect(normalizeLayerControlConfig({ mode: 'single', lockedOverlays: ['sector'] })).toMatchObject({
            enabled: true,
            mode: 'single',
            lockedOverlays: ['sector'],
            native: false,
        });

        persistControlsState('map-controls', {
            basemap: 'Plan',
            drawToolbar: false,
            measurements: false,
            overlays: { Cadastre: true },
        });

        expect(loadControlsState('map-controls')).toMatchObject({
            basemap: 'Plan',
            drawToolbar: false,
            measurements: false,
            overlays: { Cadastre: true },
        });
    });

    it('keeps legacy top-right offsets available for native Leaflet controls', () => {
        expect(getControlsPositionClasses('topright', false)).toContain('top-14');
        expect(getControlsPositionClasses('topright', true)).toContain('top-28');
    });

    it('groups layer and settings controls in the same top-right column', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const baseLayer = createLayer('xyz');
        const overlayLayer = createLayer('geojson');
        const controlsState = loadControlsState(null);
        const controlsConfig = normalizeControlsConfig(true);

        baseLayer.addTo(map);
        overlayLayer.addTo(map);

        addLayerMenuControl({}, map, {
            containerId: 'map',
        }, {
            root,
            baseLayers: { Plan: baseLayer },
            overlayLayers: { Cadastre: overlayLayer },
            lockedOverlayLayers: {},
            layerControlConfig: normalizeLayerControlConfig(true),
            controlsState,
            controlsStorageKey: null,
        });
        addUserControls({}, map, {
            containerId: 'map',
            controls: controlsConfig,
            draw: true,
            measure: true,
        }, {
            root,
            markers: [],
            renderedOverlayLayers: [],
            editableCollections: [],
            controlsConfig,
            controlsStorageKey: null,
            controlsState,
        });

        const stack = getOrCreateControlStack(root, 'topright');
        const layerMenu = root.querySelector('.daisy-leaflet-layer-controls');
        const settingsMenu = root.querySelector('.daisy-leaflet-controls');

        expect(root.querySelectorAll('.daisy-leaflet-control-stack')).toHaveLength(1);
        expect(stack.children[0]).toBe(layerMenu);
        expect(stack.children[1]).toBe(settingsMenu);
        expect(layerMenu.classList.contains('absolute')).toBe(false);
        expect(settingsMenu.classList.contains('absolute')).toBe(false);
    });

    it('creates a user controls menu and dispatches persisted state changes', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const controlsConfig = normalizeControlsConfig({ persist: true, storageKey: 'leaflet-demo-controls' });
        const controlsState = loadControlsState('leaflet-demo-controls');
        const events = [];

        root.addEventListener('daisy:leaflet:controls-change', event => events.push(event.detail.state));

        addUserControls({}, map, {
            containerId: 'map',
            controls: controlsConfig,
            draw: true,
            measure: true,
        }, {
            root,
            markers: [],
            renderedOverlayLayers: [],
            editableCollections: [],
            controlsConfig,
            controlsStorageKey: 'leaflet-demo-controls',
            controlsState,
        });

        const measureInput = [...root.querySelectorAll('label')]
            .find(label => label.textContent.includes('Afficher les mesures'))
            .querySelector('input');

        measureInput.checked = false;
        measureInput.dispatchEvent(new Event('change', { bubbles: true }));

        expect(root.querySelector('.daisy-leaflet-controls')).not.toBeNull();
        expect(root.querySelector('.daisy-leaflet-controls').textContent).not.toContain('Cadastre');
        expect(events.some(state => state.measurements === false)).toBe(true);
        expect(JSON.parse(window.localStorage.getItem('leaflet-demo-controls')).measurements).toBe(false);
    });

    it('creates a dedicated layer menu with multiple overlay stacking', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const baseLayer = createLayer('xyz');
        const overlayLayer = createLayer('geojson');
        const controlsState = loadControlsState(null);
        const layerEvents = [];

        baseLayer.addTo(map);
        overlayLayer.addTo(map);
        root.addEventListener('daisy:leaflet:layer-toggle', event => layerEvents.push(event.detail));

        addLayerMenuControl({}, map, {
            containerId: 'map',
        }, {
            root,
            baseLayers: { Plan: baseLayer },
            overlayLayers: { Cadastre: overlayLayer },
            lockedOverlayLayers: { 'Secteur imposé': createLayer('geojson') },
            layerControlConfig: normalizeLayerControlConfig(true),
            controlsState,
            controlsStorageKey: null,
        });

        const menu = root.querySelector('.daisy-leaflet-layer-controls');
        const overlayInput = [...menu.querySelectorAll('label')]
            .find(label => label.textContent.includes('Cadastre'))
            .querySelector('input');

        overlayInput.checked = false;
        overlayInput.dispatchEvent(new Event('change', { bubbles: true }));

        expect(menu).not.toBeNull();
        expect(menu.textContent).toContain('Fonds');
        expect(menu.textContent).toContain('Couches');
        expect(menu.textContent).toContain('Couches imposées');
        expect(overlayInput.type).toBe('checkbox');
        expect(layerEvents.at(-1)).toMatchObject({
            map,
            name: 'Cadastre',
            type: 'overlayremove',
            layer: overlayLayer,
            activeBasemap: 'Plan',
            activeOverlays: [],
            lockedOverlays: ['Secteur imposé'],
        });
    });

    it('creates a dedicated single-active overlay layer menu', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const cadastreLayer = createLayer('geojson');
        const labelsLayer = createLayer('xyz');
        const controlsState = loadControlsState(null);
        const layerEvents = [];

        cadastreLayer.addTo(map);
        labelsLayer.addTo(map);
        root.addEventListener('daisy:leaflet:layer-toggle', event => layerEvents.push(event.detail));

        addLayerMenuControl({}, map, {
            containerId: 'map',
        }, {
            root,
            baseLayers: {},
            overlayLayers: { Cadastre: cadastreLayer, Libellés: labelsLayer },
            lockedOverlayLayers: {},
            layerControlConfig: normalizeLayerControlConfig({ mode: 'single' }),
            controlsState,
            controlsStorageKey: null,
        });

        expect(cadastreLayer.addedTo).toBe(map);
        expect(labelsLayer.addedTo).toBeNull();

        const labelsInput = [...root.querySelectorAll('label')]
            .find(label => label.textContent.includes('Libellés'))
            .querySelector('input');

        labelsInput.checked = true;
        labelsInput.dispatchEvent(new Event('change', { bubbles: true }));

        expect(labelsInput.type).toBe('radio');
        expect(labelsLayer.addedTo).toBe(map);
        expect(cadastreLayer.addedTo).toBeNull();
        expect(controlsState.overlays).toEqual({ Cadastre: false, Libellés: true });
        expect(layerEvents.map(event => [event.type, event.name])).toEqual([
            ['overlayremove', 'Cadastre'],
            ['overlayadd', 'Libellés'],
        ]);
        expect(layerEvents.at(-1).activeOverlays).toEqual(['Libellés']);
    });

    it('collects active layers and exposes safe public API no-ops without drawing', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const active = createLayer('xyz');
        const inactive = createLayer('xyz');
        const cleanup = vi.fn();

        active.addTo(map);

        expect(collectActiveLayerNames(map, { Active: active, Inactive: inactive })).toEqual(['Active']);

        const api = createLeafletApi(root, map, { containerId: 'map' }, {
            cleanups: [cleanup],
        });

        root.dataset.leafletReady = '1';
        root.daisyLeaflet = api;

        expect(api.exportGeoJSON()).toEqual({ type: 'FeatureCollection', features: [] });
        expect(api.setMode('select')).toBe(false);
        expect(api.getDrawLayer()).toBeNull();
        expect(api.setDrawLayer('aep')).toBe(false);
        expect(api.getSelectionDetails()).toMatchObject({ count: 0, featureIds: [], features: [] });
        expect(api.showSelectionDetails()).toBe(false);
        expect(api.clearSelection()).toBe(false);
        expect(api.deleteSelected()).toBe(false);
        expect(api.getGeolocation()).toBeNull();
        expect(api.locate()).toBe(false);
        expect(api.startGeolocation()).toBe(false);
        expect(api.stopGeolocation()).toBe(false);
        expect(api.isGeolocationWatching()).toBe(false);
        expect(api.undo()).toBe(false);
        expect(api.redo()).toBe(false);
        expect(api.destroy()).toBe(true);
        expect(cleanup).toHaveBeenCalledTimes(1);
        expect(root.dataset.leafletReady).toBeUndefined();
        expect(root.daisyLeaflet).toBeUndefined();
        expect(api.destroy()).toBe(false);
    });

    it('delegates public drawing API methods when draw API is available', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const drawApi = {
            setMode: vi.fn(() => true),
            getDrawLayer: vi.fn(() => ({ id: 'aep', label: 'Réseau AEP' })),
            setDrawLayer: vi.fn(() => true),
            getSelectionDetails: vi.fn(() => ({ count: 2, featureIds: ['a', 'b'], features: [{ id: 'a' }, { id: 'b' }] })),
            showSelectionDetails: vi.fn(() => true),
            clearSelection: vi.fn(() => true),
            deleteSelected: vi.fn(() => true),
            undo: vi.fn(() => true),
            redo: vi.fn(() => true),
        };

        const api = createLeafletApi(root, map, { containerId: 'map' }, {
            drawApi,
            exportGeoJSON: () => ({ type: 'FeatureCollection', features: [{ id: 'a' }] }),
            cleanups: [],
        });

        expect(api.exportGeoJSON().features).toHaveLength(1);
        expect(api.setMode('polygon')).toBe(true);
        expect(api.getDrawLayer()).toEqual({ id: 'aep', label: 'Réseau AEP' });
        expect(api.setDrawLayer(null)).toBe(true);
        expect(api.getSelectionDetails().count).toBe(2);
        expect(api.showSelectionDetails()).toBe(true);
        expect(api.clearSelection()).toBe(true);
        expect(api.deleteSelected()).toBe(true);
        expect(api.undo()).toBe(true);
        expect(api.redo()).toBe(true);
        expect(drawApi.setMode).toHaveBeenCalledWith('polygon');
        expect(drawApi.setDrawLayer).toHaveBeenCalledWith(null);
        expect(drawApi.showSelectionDetails).toHaveBeenCalledOnce();
    });

    it('delegates public geolocation API methods when geolocation API is available', () => {
        const root = document.createElement('div');
        const map = createMapMock();
        const lastPosition = { method: 'manual', accuracy: 8 };
        const geolocationApi = {
            getLastPosition: vi.fn(() => lastPosition),
            isWatching: vi.fn(() => true),
            locate: vi.fn(() => true),
            start: vi.fn(() => true),
            stop: vi.fn(() => true),
        };

        const api = createLeafletApi(root, map, { containerId: 'map' }, {
            geolocationApi,
            cleanups: [],
        });

        expect(api.getGeolocation()).toBe(lastPosition);
        expect(api.locate()).toBe(true);
        expect(api.startGeolocation()).toBe(true);
        expect(api.stopGeolocation()).toBe(true);
        expect(api.isGeolocationWatching()).toBe(true);
    });
});
