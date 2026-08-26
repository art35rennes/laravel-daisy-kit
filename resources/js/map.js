import area from '@turf/area';
import length from '@turf/length';
import L from 'leaflet';
import { TerraDraw, TerraDrawLineStringMode, TerraDrawPolygonMode } from 'terra-draw';
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
    const geojson = validGeojson(configuration.geojson);
    const layers = validLayers(configuration.layers);

    if (!canvas || !empty || !output || !layerTools) {
        throw new Error('Map markup is incomplete.');
    }

    if (!geojson && layers.length === 0 && !configuration.drawing) {
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
    layerTools.hidden = configuredLayers.length === 0;

    let drawing = null;
    let onFinish = null;

    if (configuration.drawing) {
        drawing = new TerraDraw({
            adapter: new TerraDrawLeafletAdapter({ lib: L, map }),
            modes: [new TerraDrawLineStringMode(), new TerraDrawPolygonMode()],
        });
        onFinish = (id, context) => {
            const feature = drawing.getSnapshotFeature(id) ?? context?.feature;
            const value = measurement(feature);

            if (value) {
                output.textContent = value;
            }

            root.dispatchEvent(new CustomEvent('daisy-kit:map:drawn', {
                bubbles: true,
                detail: { feature, id, measurement: value },
            }));
        };
        drawing.on('finish', onFinish);
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

    tools?.addEventListener('click', onToolClick);
    layerTools.addEventListener('change', onLayerChange);

    root.dataset.daisyKitState = 'ready';
    root.dispatchEvent(new CustomEvent('daisy-kit:map:ready', { bubbles: true }));

    return () => {
        tools?.removeEventListener('click', onToolClick);
        layerTools.removeEventListener('change', onLayerChange);

        if (drawing && onFinish) {
            drawing.off('finish', onFinish);
            drawing.stop();
        }

        dataLayers.forEach((layer) => layer.remove());
        layerTools.replaceChildren();
        layerTools.hidden = true;
        map.remove();
        output.replaceChildren();
    };
}

const module = createMountable('map', initializeMap);

export const { mount, mountAll, unmount } = module;
