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
    const geojson = validGeojson(configuration.geojson);

    if (!canvas || !empty || !output) {
        throw new Error('Map markup is incomplete.');
    }

    if (!geojson && !configuration.drawing) {
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
    let dataLayer = null;

    if (geojson) {
        dataLayer = L.geoJSON(geojson).addTo(map);
        const bounds = dataLayer.getBounds();

        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [16, 16] });
        }
    }

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
        root.dispatchEvent(new CustomEvent('daisy-kit:map:mode', {
            bubbles: true,
            detail: { mode: button.dataset.daisyKitMapMode },
        }));
    };

    tools?.addEventListener('click', onToolClick);

    root.dataset.daisyKitState = 'ready';
    root.dispatchEvent(new CustomEvent('daisy-kit:map:ready', { bubbles: true }));

    return () => {
        tools?.removeEventListener('click', onToolClick);

        if (drawing && onFinish) {
            drawing.off('finish', onFinish);
            drawing.stop();
        }

        dataLayer?.remove();
        map.remove();
        output.replaceChildren();
    };
}

const module = createMountable('map', initializeMap);

export const { mount, mountAll, unmount } = module;
