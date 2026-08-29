import { normalizeConfiguration } from './configuration.js';
import { createDrawing } from './drawing.js';
import { emit as emitEvent } from './events.js';
import { createPersistence } from './persistence.js';
import { createSources } from './sources.js';

function finiteLatLng(latlng) {
    return latlng && Number.isFinite(Number(latlng.lat)) && Number.isFinite(Number(latlng.lng));
}

function visibleSize(canvas) {
    const rect = canvas.getBoundingClientRect?.();
    const width = rect?.width || canvas.clientWidth;
    const height = rect?.height || canvas.clientHeight;

    return Number.isFinite(width) && Number.isFinite(height) && width > 0 && height > 0;
}

function clone(value) {
    return JSON.parse(JSON.stringify(value));
}

export function createMapRuntime({ L, onDestroy, rawConfiguration, root }) {
    const configuration = normalizeConfiguration(rawConfiguration);
    const canvas = root.querySelector('[data-daisy-kit-map-canvas]');
    const loading = root.querySelector('[data-daisy-kit-map-loading]');
    const empty = root.querySelector('[data-daisy-kit-map-empty]');
    const errorPanel = root.querySelector('[data-daisy-kit-map-error]');
    const errorMessage = root.querySelector('[data-daisy-kit-map-error-message]');
    const status = root.querySelector('[data-daisy-kit-status]');
    const controller = new AbortController();
    const persistence = createPersistence(root, configuration);
    const persisted = persistence.load();
    const listeners = [];
    let map = null;
    let sources = null;
    let drawing = null;
    let resizeObserver = null;
    let resizeFrame = null;
    let watchId = null;
    let destroyed = false;

    const state = {
        basemap: persisted?.basemap ?? null,
        center: persisted?.center ?? configuration.center,
        drawingToolsVisible: persisted?.drawingToolsVisible ?? true,
        layerVisibility: persisted?.layerVisibility ?? {},
        measurement: null,
        mode: null,
        selection: [],
        zoom: persisted?.zoom ?? configuration.zoom,
    };

    const emit = (name, detail = {}) => emitEvent(root, name, detail);

    function listen(target, event, handler, options) {
        if (!target) return;
        target.addEventListener(event, handler, options);
        listeners.push(() => target.removeEventListener(event, handler, options));
    }

    function saveState() {
        persistence.save({
            basemap: sources?.activeBasemap() ?? state.basemap,
            center: state.center,
            drawingToolsVisible: state.drawingToolsVisible,
            layerVisibility: sources?.layerVisibility() ?? state.layerVisibility,
            zoom: state.zoom,
        });
    }

    function setFeedback(nextState, message = '') {
        root.dataset.daisyKitState = nextState;
        root.setAttribute('aria-busy', String(nextState === 'loading'));
        if (loading) loading.hidden = nextState !== 'loading';
        if (empty) empty.hidden = nextState !== 'empty';
        if (errorPanel) errorPanel.hidden = nextState !== 'error';
        if (status) {
            status.hidden = message === '';
            status.textContent = message;
            status.classList.toggle('alert-error', nextState === 'error');
            status.classList.toggle('alert-info', nextState !== 'error');
        }
        if (errorMessage && message) errorMessage.textContent = message;
    }

    function hasData() {
        return Boolean(configuration.geojson)
            || configuration.layers.length > 0
            || configuration.markers.length > 0
            || configuration.basemaps.length > 0
            || Boolean(configuration.provider || configuration.tileUrl || configuration.drawing);
    }

    function invalidateSize() {
        if (!map || !canvas || !visibleSize(canvas)) return false;
        map.invalidateSize({ animate: false, pan: false });

        return true;
    }

    function scheduleResize() {
        if (resizeFrame !== null) cancelAnimationFrame(resizeFrame);
        resizeFrame = requestAnimationFrame(() => {
            resizeFrame = null;
            invalidateSize();
        });
    }

    function fitBounds(options = {}) {
        if (!map || !sources) return false;
        const bounds = sources.bounds();
        if (!bounds?.isValid?.()) return false;
        map.fitBounds(bounds, { padding: [24, 24], ...options });

        return true;
    }

    function setView(center, zoom = state.zoom, options = {}) {
        const latitude = Number(center?.[0]);
        const longitude = Number(center?.[1]);
        const nextZoom = Number(zoom);
        if (!Number.isFinite(latitude) || !Number.isFinite(longitude) || !Number.isFinite(nextZoom)) return false;
        state.center = [latitude, longitude];
        state.zoom = nextZoom;
        map?.setView(state.center, state.zoom, options);
        saveState();

        return true;
    }

    function locationOptions(options = {}) {
        return {
            enableHighAccuracy: configuration.geolocation?.enableHighAccuracy === true,
            maximumAge: configuration.geolocation?.maximumAge ?? 10_000,
            timeout: configuration.geolocation?.timeout ?? 10_000,
            ...options,
        };
    }

    function applyLocation(position, tracked = false) {
        const center = [Number(position.coords.latitude), Number(position.coords.longitude)];
        if (!center.every(Number.isFinite)) return null;
        if (configuration.geolocation?.setView !== false) {
            setView(center, configuration.geolocation?.zoom ?? state.zoom);
        }
        emit('geolocation', {
            accuracy: Number(position.coords.accuracy) || null,
            center,
            tracked,
        });

        return center;
    }

    function geolocationError(error) {
        const detail = {
            code: error?.code ?? 'unavailable',
            message: error?.message || 'Geolocation is unavailable.',
        };
        emit('geolocation-error', detail);

        return detail;
    }

    function locate(options = {}) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                const error = geolocationError();
                reject(error);

                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => resolve(applyLocation(position)),
                (error) => reject(geolocationError(error)),
                locationOptions(options),
            );
        });
    }

    function startGeolocation(options = {}) {
        if (!navigator.geolocation || watchId !== null) return false;
        watchId = navigator.geolocation.watchPosition(
            (position) => applyLocation(position, true),
            geolocationError,
            locationOptions(options),
        );
        emit('geolocation-start', {});

        return true;
    }

    function stopGeolocation() {
        if (watchId === null || !navigator.geolocation) return false;
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        emit('geolocation-stop', {});

        return true;
    }

    function fullscreen() {
        if (!document.fullscreenElement) return root.requestFullscreen?.();

        return document.exitFullscreen?.();
    }

    function bindControls() {
        root.querySelectorAll('[data-daisy-kit-map-mode]').forEach((button) => {
            listen(button, 'click', () => facade.setMode(button.dataset.daisyKitMapMode));
        });
        listen(root.querySelector('[data-daisy-kit-map-fit-bounds]'), 'click', () => fitBounds());
        listen(root.querySelector('[data-daisy-kit-map-geolocate]'), 'click', () => locate().catch(() => {}));
        listen(root.querySelector('[data-daisy-kit-map-fullscreen]'), 'click', fullscreen);
        listen(root.querySelector('[data-daisy-kit-map-history="undo"]'), 'click', () => facade.undo());
        listen(root.querySelector('[data-daisy-kit-map-history="redo"]'), 'click', () => facade.redo());
        listen(root.querySelector('[data-daisy-kit-map-export]'), 'click', () => facade.exportGeoJSON());
        listen(root.querySelector('[data-daisy-kit-map-delete-selected]'), 'click', () => facade.deleteSelected());
        listen(root.querySelector('[data-daisy-kit-map-clear-selection]'), 'click', () => facade.clearSelection());
        listen(root.querySelector('[data-daisy-kit-map-retry]'), 'click', async () => {
            const failed = configuration.layers.filter((layer) => layer.url && layer.type === 'geojson');
            setFeedback('loading');
            await Promise.all(failed.map((layer) => sources.refreshLayer(layer.id)));
            setFeedback(hasData() ? 'ready' : 'empty');
        });
    }

    function bindMapEvents() {
        const onView = () => {
            const center = map.getCenter?.();
            const zoom = map.getZoom?.();
            if (!finiteLatLng(center) || !Number.isFinite(Number(zoom))) return;
            state.center = [Number(center.lat), Number(center.lng)];
            state.zoom = Number(zoom);
            saveState();
            emit('view', { center: state.center, zoom: state.zoom });
        };
        map.on('moveend zoomend', onView);
        listeners.push(() => map?.off('moveend zoomend', onView));
    }

    async function start() {
        if (!canvas) throw new Error('Map markup is incomplete.');
        setFeedback('loading');
        if (configuration.gestureHandling) await import('leaflet-gesture-handling');
        if (controller.signal.aborted) return;

        map = L.map(canvas, {
            attributionControl: true,
            gestureHandling: configuration.gestureHandling,
            maxZoom: configuration.maxZoom,
            minZoom: configuration.minZoom,
            preferCanvas: configuration.preferCanvas,
            trackResize: false,
            zoomControl: true,
        });
        setView(state.center, state.zoom);
        bindMapEvents();
        bindControls();

        if (configuration.scale) L.control.scale().addTo(map);
        sources = await createSources({ L, configuration, emit, map, root, signal: controller.signal });
        if (controller.signal.aborted) return;

        for (const [id, visible] of Object.entries(state.layerVisibility)) {
            sources.setLayerVisibility(id, visible, false);
        }
        if (state.basemap) sources.setBasemap(state.basemap, false);

        drawing = await createDrawing({ L, configuration, emit, map, root, signal: controller.signal });
        if (controller.signal.aborted) return;

        if (configuration.fitBounds) fitBounds();
        resizeObserver = typeof ResizeObserver === 'function' ? new ResizeObserver(scheduleResize) : null;
        resizeObserver?.observe(canvas);
        setFeedback(hasData() ? 'ready' : 'empty');
        emit(hasData() ? 'ready' : 'empty', { state: facade.getState() });
    }

    function internalDestroy() {
        if (destroyed) return;
        destroyed = true;
        controller.abort();
        stopGeolocation();
        if (resizeFrame !== null) cancelAnimationFrame(resizeFrame);
        resizeObserver?.disconnect();
        listeners.splice(0).forEach((remove) => remove());
        drawing?.destroy();
        sources?.destroy();
        map?.remove();
        map = null;
        root.removeAttribute('aria-busy');
    }

    const facade = {
        clearSelection: () => drawing?.clearSelection(),
        deleteSelected: () => drawing?.deleteSelected() ?? false,
        destroy: onDestroy,
        exportGeoJSON: () => drawing?.exportGeoJSON() ?? configuration.value,
        fitBounds,
        getDrawLayer: () => drawing?.getDrawLayer() ?? null,
        getLeafletMap: () => map,
        getSelection: () => drawing?.getSelection() ?? [],
        getState: () => clone({
            ...state,
            basemap: sources?.activeBasemap() ?? state.basemap,
            layerVisibility: sources?.layerVisibility() ?? state.layerVisibility,
        }),
        invalidateSize,
        locate,
        redo: () => drawing?.redo() ?? false,
        refreshLayer: (id) => sources?.refreshLayer(id) ?? Promise.resolve(false),
        setBasemap(id) {
            const changed = sources?.setBasemap(id) ?? false;
            if (changed) saveState();

            return changed;
        },
        setDrawLayer: (id) => drawing?.setDrawLayer(id) ?? false,
        setGeoJSON(data) {
            const layer = sources?.setGeoJSON(data) ?? null;
            emit('data', { geojson: data });

            return Boolean(layer);
        },
        setLayerData: (id, data) => sources?.setLayerData(id, data) ?? Promise.resolve(false),
        setLayerVisibility(id, visible) {
            const changed = sources?.setLayerVisibility(id, visible) ?? false;
            if (changed) saveState();

            return changed;
        },
        setMarkers(markers) {
            sources?.setMarkers(markers);
            emit('markers', { markers });
        },
        setMode(mode, options) {
            const changed = drawing?.setMode(mode, options) ?? false;
            if (changed) state.mode = mode;

            return changed;
        },
        setView,
        startGeolocation,
        stopGeolocation,
        undo: () => drawing?.undo() ?? false,
    };

    return { facade, internalDestroy, start };
}
