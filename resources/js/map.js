import area from '@turf/area';
import length from '@turf/length';
import L from 'leaflet';
import { TerraDraw, TerraDrawLineStringMode, TerraDrawPolygonMode, TerraDrawSelectMode, TerraDrawSessionUndoRedo } from 'terra-draw';
import { TerraDrawLeafletAdapter } from 'terra-draw-leaflet-adapter';

import '../css/map.css';
import { createMountable } from './core/mountable.js';

function validCenter(center) {
    if (!Array.isArray(center) || center.length !== 2) {
        return [48.1173, -1.6778];
    }

    const [latitude, longitude] = center.map(Number);

    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
        return [48.1173, -1.6778];
    }

    return [latitude, longitude];
}

function validGeojson(value) {
    return value && typeof value === 'object' && typeof value.type === 'string' ? value : null;
}

function validLayers(value) {
    if (!Array.isArray(value)) return [];

    const ids = new Set();

    return value.flatMap((layer) => {
        if (!layer || typeof layer.id !== 'string' || layer.id.length === 0 || ids.has(layer.id)) return [];

        const geojson = validGeojson(layer.geojson);

        if (!geojson) return [];

        ids.add(layer.id);

        return [{
            geojson,
            id: layer.id,
            label: typeof layer.label === 'string' && layer.label.length > 0 ? layer.label : layer.id,
            visible: layer.visible !== false,
        }];
    });
}

function validTileUrl(value) {
    if (typeof value !== 'string' || value.length === 0) return null;

    try {
        const url = new URL(value);

        return url.protocol === 'https:' && ['{x}', '{y}', '{z}'].every((placeholder) => decodeURIComponent(url.pathname).includes(placeholder)) ? value : null;
    } catch {
        return null;
    }
}

function validBasemaps(value) {
    if (!Array.isArray(value)) return [];

    const ids = new Set();

    return value.flatMap((basemap) => {
        if (!basemap || typeof basemap.id !== 'string' || basemap.id.length === 0 || ids.has(basemap.id)) return [];

        const url = validTileUrl(basemap.url);

        if (!url) return [];

        ids.add(basemap.id);

        return [{
            attribution: typeof basemap.attribution === 'string' ? basemap.attribution : '',
            id: basemap.id,
            label: typeof basemap.label === 'string' && basemap.label.length > 0 ? basemap.label : basemap.id,
            selected: basemap.selected === true,
            url,
        }];
    });
}

function validWmsOverlays(value) {
    if (!Array.isArray(value)) return [];

    const ids = new Set();

    return value.flatMap((overlay) => {
        if (!overlay || typeof overlay.id !== 'string' || overlay.id.length === 0 || ids.has(overlay.id) || typeof overlay.layers !== 'string' || overlay.layers.length === 0) return [];

        try {
            const url = new URL(overlay.url);

            if (url.protocol !== 'https:') return [];
        } catch {
            return [];
        }

        ids.add(overlay.id);

        return [{
            attribution: typeof overlay.attribution === 'string' ? overlay.attribution : '',
            id: overlay.id,
            label: typeof overlay.label === 'string' && overlay.label.length > 0 ? overlay.label : overlay.id,
            layers: overlay.layers,
            url: overlay.url,
            visible: overlay.visible !== false,
        }];
    });
}

function measurement(feature) {
    if (!feature?.geometry) {
        return null;
    }

    if (['Polygon', 'MultiPolygon'].includes(feature.geometry.type)) {
        return `${Math.round(area(feature)).toLocaleString()} m²`;
    }

    if (['LineString', 'MultiLineString'].includes(feature.geometry.type)) {
        return `${length(feature, { units: 'kilometers' }).toFixed(2)} km`;
    }

    return null;
}

function initializeMap(root, configuration) {
    const canvas = root.querySelector('[data-daisy-kit-map-canvas]');
    const empty = root.querySelector('[data-daisy-kit-empty]');
    const output = root.querySelector('[data-daisy-kit-map-measurement]');
    const tools = root.querySelector('[data-daisy-kit-map-tools]');
    const layerTools = root.querySelector('[data-daisy-kit-map-layers]');
    const basemapTools = root.querySelector('[data-daisy-kit-map-basemaps]');
    const exportControl = root.querySelector('[data-daisy-kit-map-export]');
    const undoControl = root.querySelector('[data-daisy-kit-map-history="undo"]');
    const redoControl = root.querySelector('[data-daisy-kit-map-history="redo"]');
    const value = root.querySelector('[data-daisy-kit-map-value]');
    const geojson = validGeojson(configuration.geojson);
    const layers = validLayers(configuration.layers);
    const tileUrl = validTileUrl(configuration.tileUrl);
    const basemaps = validBasemaps(configuration.basemaps);
    const wmsOverlays = validWmsOverlays(configuration.wms);

    if (!canvas || !empty || !output || !layerTools) {
        throw new Error('Map markup is incomplete.');
    }

    if (!geojson && layers.length === 0 && basemaps.length === 0 && wmsOverlays.length === 0 && !tileUrl && !configuration.drawing) {
        empty.hidden = false;
        root.dataset.daisyKitState = 'empty';
        root.dispatchEvent(new CustomEvent('daisy-kit:map:empty', { bubbles: true }));

        return () => {};
    }

    empty.hidden = true;
    const map = L.map(canvas, { attributionControl: true, zoomControl: true }).setView(
        validCenter(configuration.center),
        Number.isFinite(configuration.zoom) ? Number(configuration.zoom) : 12,
    );
    const dataLayers = [];
    let tileLayer = null;

    if (tileUrl && basemaps.length === 0) {
        tileLayer = L.tileLayer(tileUrl, {
            attribution: typeof configuration.tileAttribution === 'string' ? configuration.tileAttribution : '',
        }).addTo(map);
    }

    const configuredBasemaps = basemaps.map((basemap) => {
        const leafletLayer = L.tileLayer(basemap.url, { attribution: basemap.attribution });
        const control = document.createElement('input');
        control.checked = false;
        control.name = 'daisy-kit-map-basemap';
        control.setAttribute('data-daisy-kit-map-basemap', basemap.id);
        control.type = 'radio';
        const label = document.createElement('label');
        label.append(control, document.createTextNode(basemap.label));
        basemapTools?.append(label);

        return { ...basemap, control, leafletLayer };
    });
    let activeBasemap = configuredBasemaps.find((basemap) => basemap.selected) ?? configuredBasemaps[0] ?? null;

    if (activeBasemap) {
        activeBasemap.control.checked = true;
        activeBasemap.leafletLayer.addTo(map);
    }
    if (basemapTools) basemapTools.hidden = configuredBasemaps.length === 0;

    if (geojson) {
        const dataLayer = L.geoJSON(geojson).addTo(map);
        dataLayers.push(dataLayer);
        const bounds = dataLayer.getBounds();

        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [16, 16] });
        }
    }

    const configuredLayers = layers.map((layer) => {
        const leafletLayer = L.geoJSON(layer.geojson);

        if (layer.visible) leafletLayer.addTo(map);

        dataLayers.push(leafletLayer);

        const control = document.createElement('input');
        control.checked = layer.visible;
        control.setAttribute('data-daisy-kit-map-layer', layer.id);
        control.type = 'checkbox';
        const label = document.createElement('label');
        label.append(control, document.createTextNode(layer.label));
        layerTools.append(label);

        return { ...layer, control, leafletLayer };
    });
    const configuredWms = wmsOverlays.map((overlay) => {
        const leafletLayer = L.tileLayer.wms(overlay.url, {
            attribution: overlay.attribution,
            format: 'image/png',
            layers: overlay.layers,
            transparent: true,
        });

        if (overlay.visible) leafletLayer.addTo(map);

        const control = document.createElement('input');
        control.checked = overlay.visible;
        control.setAttribute('data-daisy-kit-map-wms', overlay.id);
        control.type = 'checkbox';
        const label = document.createElement('label');
        label.append(control, document.createTextNode(overlay.label));
        layerTools.append(label);

        return { ...overlay, control, leafletLayer };
    });
    layerTools.hidden = configuredLayers.length + configuredWms.length === 0;

    let drawing = null;
    let onFinish = null;
    let onHistory = null;
    let onSelect = null;
    const drawnFeatures = [];

    if (configuration.drawing) {
        drawing = new TerraDraw({
            adapter: new TerraDrawLeafletAdapter({ lib: L, map }),
            modes: [new TerraDrawLineStringMode(), new TerraDrawPolygonMode(), new TerraDrawSelectMode()],
            undoRedo: { sessionLevel: new TerraDrawSessionUndoRedo() },
        });
        onFinish = (id, context) => {
            const feature = drawing.getSnapshotFeature(id) ?? context?.feature;
            const value = measurement(feature);

            if (value) {
                output.textContent = value;
            }

            if (feature) {
                drawnFeatures.push(feature);
                exportControl?.removeAttribute('disabled');
            }

            root.dispatchEvent(new CustomEvent('daisy-kit:map:drawn', {
                bubbles: true,
                detail: { feature, id, measurement: value },
            }));
        };
        drawing.on('finish', onFinish);
        onHistory = ({ redoStackSize, undoStackSize }) => {
            undoControl?.toggleAttribute('disabled', undoStackSize === 0);
            redoControl?.toggleAttribute('disabled', redoStackSize === 0);
        };
        onSelect = (id) => {
            const feature = drawing.getSnapshotFeature(id);
            root.dispatchEvent(new CustomEvent('daisy-kit:map:select', {
                bubbles: true,
                detail: { feature, id, measurement: measurement(feature) },
            }));
        };
        drawing.on('history', onHistory);
        drawing.on('select', onSelect);
        drawing.start();
    }

    const onToolClick = (event) => {
        const button = event.target.closest('[data-daisy-kit-map-mode]');

        if (!button || !drawing) {
            return;
        }

        drawing.setMode(button.dataset.daisyKitMapMode);
        tools?.querySelectorAll('[data-daisy-kit-map-mode]').forEach((candidate) => candidate.setAttribute('aria-pressed', String(candidate === button)));
        root.dispatchEvent(new CustomEvent('daisy-kit:map:mode', {
            bubbles: true,
            detail: { mode: button.dataset.daisyKitMapMode },
        }));
    };
    const onExport = () => {
        if (drawnFeatures.length === 0) return;

        const collection = { features: drawnFeatures, type: 'FeatureCollection' };
        const serialized = JSON.stringify(collection);

        if (value instanceof HTMLInputElement) value.value = serialized;
        root.dispatchEvent(new CustomEvent('daisy-kit:map:export', {
            bubbles: true,
            detail: { collection, value: serialized },
        }));
    };
    const applyHistory = (action) => {
        if (!drawing) return;

        const changed = action === 'undo' ? drawing.undo() : drawing.redo();

        if (changed) {
            root.dispatchEvent(new CustomEvent(`daisy-kit:map:${action}`, { bubbles: true }));
        }
    };
    const onUndo = () => applyHistory('undo');
    const onRedo = () => applyHistory('redo');
    const onLayerChange = (event) => {
        const control = event.target.closest('[data-daisy-kit-map-layer]');

        if (!(control instanceof HTMLInputElement)) return;

        const layer = configuredLayers.find((candidate) => candidate.id === control.dataset.daisyKitMapLayer);

        if (!layer) return;

        if (control.checked) {
            layer.leafletLayer.addTo(map);
        } else {
            layer.leafletLayer.remove();
        }

        root.dispatchEvent(new CustomEvent('daisy-kit:map:layer', {
            bubbles: true,
            detail: { id: layer.id, visible: control.checked },
        }));
    };
    const onBasemapChange = (event) => {
        const control = event.target.closest('[data-daisy-kit-map-basemap]');

        if (!(control instanceof HTMLInputElement) || !control.checked) return;

        const next = configuredBasemaps.find((basemap) => basemap.id === control.dataset.daisyKitMapBasemap);

        if (!next || next === activeBasemap) return;

        activeBasemap?.leafletLayer.remove();
        next.leafletLayer.addTo(map);
        activeBasemap = next;
        root.dispatchEvent(new CustomEvent('daisy-kit:map:basemap', {
            bubbles: true,
            detail: { id: next.id },
        }));
    };
    const onWmsChange = (event) => {
        const control = event.target.closest('[data-daisy-kit-map-wms]');

        if (!(control instanceof HTMLInputElement)) return;

        const overlay = configuredWms.find((candidate) => candidate.id === control.dataset.daisyKitMapWms);

        if (!overlay) return;

        if (control.checked) {
            overlay.leafletLayer.addTo(map);
        } else {
            overlay.leafletLayer.remove();
        }

        root.dispatchEvent(new CustomEvent('daisy-kit:map:wms', {
            bubbles: true,
            detail: { id: overlay.id, visible: control.checked },
        }));
    };

    tools?.addEventListener('click', onToolClick);
    layerTools.addEventListener('change', onLayerChange);
    layerTools.addEventListener('change', onWmsChange);
    basemapTools?.addEventListener('change', onBasemapChange);
    exportControl?.addEventListener('click', onExport);
    undoControl?.addEventListener('click', onUndo);
    redoControl?.addEventListener('click', onRedo);

    root.dataset.daisyKitState = 'ready';
    root.dispatchEvent(new CustomEvent('daisy-kit:map:ready', { bubbles: true }));

    return () => {
        tools?.removeEventListener('click', onToolClick);
        layerTools.removeEventListener('change', onLayerChange);
        layerTools.removeEventListener('change', onWmsChange);
        basemapTools?.removeEventListener('change', onBasemapChange);
        exportControl?.removeEventListener('click', onExport);
        undoControl?.removeEventListener('click', onUndo);
        redoControl?.removeEventListener('click', onRedo);

        if (drawing && onFinish) {
            drawing.off('finish', onFinish);
            drawing.off('history', onHistory);
            drawing.off('select', onSelect);
            drawing.stop();
        }

        dataLayers.forEach((layer) => layer.remove());
        configuredWms.forEach((overlay) => overlay.leafletLayer.remove());
        tileLayer?.remove();
        configuredBasemaps.forEach((basemap) => basemap.leafletLayer.remove());
        layerTools.replaceChildren();
        layerTools.hidden = true;
        basemapTools?.replaceChildren();
        if (basemapTools) basemapTools.hidden = true;
        map.remove();
        output.replaceChildren();
    };
}

const module = createMountable('map', initializeMap);

export const { mount, mountAll, unmount } = module;
