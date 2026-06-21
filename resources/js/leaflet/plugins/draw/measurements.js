import area from '@turf/area';
import length from '@turf/length';
import { isPersistableFeature } from './features.js';

const DEFAULT_MEASURE_CONFIG = {
    maxLabels: 16,
};

function formatDistance(kilometers) {
    if (kilometers < 1) {
        return `${Math.round(kilometers * 1000)} m`;
    }

    return `${kilometers.toFixed(2)} km`;
}

function formatArea(squareMeters) {
    if (squareMeters >= 1000000) {
        return `${(squareMeters / 1000000).toFixed(2)} km²`;
    }

    if (squareMeters >= 10000) {
        return `${(squareMeters / 10000).toFixed(2)} ha`;
    }

    return `${Math.round(squareMeters)} m²`;
}

function measureFeature(feature) {
    if (!isPersistableFeature(feature)) {
        return null;
    }

    if (feature.geometry.type === 'Point') {
        const [lng, lat] = feature.geometry.coordinates;

        return {
            type: 'point',
            label: `${lat.toFixed(6)}, ${lng.toFixed(6)}`,
            coordinates: { lat, lng },
        };
    }

    if (feature.geometry.type === 'LineString') {
        const kilometers = length(feature, { units: 'kilometers' });

        return {
            type: 'line',
            label: formatDistance(kilometers),
            distance: { kilometers },
        };
    }

    if (feature.geometry.type === 'Polygon') {
        const squareMeters = area(feature);
        const perimeterFeature = {
            type: 'Feature',
            properties: {},
            geometry: {
                type: 'LineString',
                coordinates: feature.geometry.coordinates[0] || [],
            },
        };
        const perimeterKilometers = length(perimeterFeature, { units: 'kilometers' });

        return {
            type: 'polygon',
            label: `${formatArea(squareMeters)} / ${formatDistance(perimeterKilometers)}`,
            area: { squareMeters },
            perimeter: { kilometers: perimeterKilometers },
        };
    }

    return null;
}

function escapeHtml(value) {
    return value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function coordinateToLatLng(coordinate) {
    if (!Array.isArray(coordinate) || coordinate.length < 2) {
        return null;
    }

    return [coordinate[1], coordinate[0]];
}

function getMeasurementAnchor(feature) {
    if (!isPersistableFeature(feature)) {
        return null;
    }

    if (feature.geometry.type === 'Point') {
        return coordinateToLatLng(feature.geometry.coordinates);
    }

    if (feature.geometry.type === 'LineString') {
        const coordinates = feature.geometry.coordinates || [];

        return coordinateToLatLng(coordinates[Math.floor((coordinates.length - 1) / 2)]);
    }

    if (feature.geometry.type === 'Polygon') {
        const ring = (feature.geometry.coordinates?.[0] || []).filter((coordinate, index, coordinates) => {
            if (index !== coordinates.length - 1) {
                return true;
            }

            return coordinate[0] !== coordinates[0]?.[0] || coordinate[1] !== coordinates[0]?.[1];
        });

        if (ring.length === 0) {
            return null;
        }

        const totals = ring.reduce((carry, coordinate) => ({
            lat: carry.lat + coordinate[1],
            lng: carry.lng + coordinate[0],
        }), { lat: 0, lng: 0 });

        return [totals.lat / ring.length, totals.lng / ring.length];
    }

    return null;
}

function collectLatLngCoordinates(feature) {
    if (!isPersistableFeature(feature)) {
        return [];
    }

    if (feature.geometry.type === 'Point') {
        return [coordinateToLatLng(feature.geometry.coordinates)].filter(Boolean);
    }

    if (feature.geometry.type === 'LineString') {
        return (feature.geometry.coordinates || []).map(coordinateToLatLng).filter(Boolean);
    }

    if (feature.geometry.type === 'Polygon') {
        return (feature.geometry.coordinates?.[0] || []).map(coordinateToLatLng).filter(Boolean);
    }

    return [];
}

function estimateMeasurementLabelSize(label) {
    return {
        width: Math.max(54, Math.min(220, (label.length * 7) + 18)),
        height: 24,
    };
}

function rectanglesOverlap(first, second) {
    return !(
        first.right < second.left ||
        first.left > second.right ||
        first.bottom < second.top ||
        first.top > second.bottom
    );
}

function resolveMeasurementLabelPlacement(map, feature, label, occupiedRects = []) {
    const latLngCoordinates = collectLatLngCoordinates(feature);

    if (!map || latLngCoordinates.length === 0 || typeof map.latLngToLayerPoint !== 'function') {
        const fallbackAnchor = getMeasurementAnchor(feature);

        return fallbackAnchor
            ? { latLng: fallbackAnchor, size: estimateMeasurementLabelSize(label) }
            : null;
    }

    const points = latLngCoordinates
        .map(latLng => map.latLngToLayerPoint(latLng))
        .filter(Boolean);

    if (points.length === 0) {
        return null;
    }

    const size = estimateMeasurementLabelSize(label);
    const bounds = points.reduce((carry, point) => ({
        minX: Math.min(carry.minX, point.x),
        maxX: Math.max(carry.maxX, point.x),
        minY: Math.min(carry.minY, point.y),
        maxY: Math.max(carry.maxY, point.y),
    }), {
        minX: Number.POSITIVE_INFINITY,
        maxX: Number.NEGATIVE_INFINITY,
        minY: Number.POSITIVE_INFINITY,
        maxY: Number.NEGATIVE_INFINITY,
    });

    const x = (bounds.minX + bounds.maxX) / 2;
    let y = bounds.maxY + 12;
    let rect = {
        left: x - (size.width / 2),
        right: x + (size.width / 2),
        top: y,
        bottom: y + size.height,
    };

    let attempts = 0;

    while (occupiedRects.some(occupiedRect => rectanglesOverlap(rect, occupiedRect))) {
        attempts += 1;

        if (attempts > 8) {
            return null;
        }

        y += size.height + 6;
        rect = {
            ...rect,
            top: y,
            bottom: y + size.height,
        };
    }

    occupiedRects.push(rect);

    if (typeof map.layerPointToLatLng !== 'function') {
        return null;
    }

    return {
        latLng: map.layerPointToLatLng([x, y]),
        size,
    };
}

function createMeasurePanel(root) {
    const panel = document.createElement('output');
    panel.className = [
        'daisy-leaflet-measure',
        'absolute',
        'bottom-2',
        'left-2',
        'z-[1000]',
        'rounded-box',
        'bg-base-100',
        'px-2',
        'py-1',
        'text-xs',
        'font-medium',
        'pointer-events-none',
        'shadow',
        'hidden',
    ].join(' ');
    panel.setAttribute('aria-live', 'polite');

    root.appendChild(panel);

    return panel;
}

function createMeasurementLayer(L, map) {
    return L.layerGroup().addTo(map);
}

function syncMeasurementLayer(L, map, measurementLayer, collection, measureConfig) {
    if (!L || !measurementLayer) {
        return;
    }

    measurementLayer.clearLayers();

    if (!measureConfig?.showTooltip) {
        return;
    }

    const occupiedRects = [];
    const maxLabels = Number.isFinite(Number(measureConfig.maxLabels))
        ? Math.max(0, Number(measureConfig.maxLabels))
        : DEFAULT_MEASURE_CONFIG.maxLabels;
    const measuredFeatures = collection.features
        .map(feature => ({ feature, measurement: measureFeature(feature) }))
        .filter(entry => entry.measurement)
        .sort((first, second) => {
            const priority = { polygon: 0, line: 1, point: 2 };

            return (priority[first.measurement.type] ?? 3) - (priority[second.measurement.type] ?? 3);
        })
        .slice(0, maxLabels);

    measuredFeatures.forEach(({ feature, measurement }) => {
        const placement = resolveMeasurementLabelPlacement(map, feature, measurement.label, occupiedRects);

        if (!placement) {
            return;
        }

        L.marker(placement.latLng, {
            interactive: false,
            keyboard: false,
            icon: L.divIcon({
                className: 'daisy-leaflet-measure-label',
                html: `<span class="inline-block whitespace-nowrap rounded-box bg-base-100/95 px-2 py-1 text-xs font-semibold shadow ring-1 ring-base-300">${escapeHtml(measurement.label)}</span>`,
                iconAnchor: [placement.size.width / 2, 0],
                iconSize: [placement.size.width, placement.size.height],
            }),
        }).addTo(measurementLayer);
    });
}

export {
    coordinateToLatLng,
    createMeasurePanel,
    createMeasurementLayer,
    formatArea,
    formatDistance,
    getMeasurementAnchor,
    measureFeature,
    resolveMeasurementLabelPlacement,
    syncMeasurementLayer,
};
