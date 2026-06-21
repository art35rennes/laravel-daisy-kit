import { isPersistableFeature, toFeatureCollection } from './features.js';
import { coordinateToLatLng, getMeasurementAnchor } from './measurements.js';

function collectFeatureLatLngs(feature) {
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

function pointInLatLngPolygon(latLng, polygon) {
    const point = Array.isArray(latLng) ? { lat: latLng[0], lng: latLng[1] } : latLng;
    let inside = false;

    for (let index = 0, previousIndex = polygon.length - 1; index < polygon.length; previousIndex = index++) {
        const current = Array.isArray(polygon[index])
            ? { lat: polygon[index][0], lng: polygon[index][1] }
            : polygon[index];
        const previous = Array.isArray(polygon[previousIndex])
            ? { lat: polygon[previousIndex][0], lng: polygon[previousIndex][1] }
            : polygon[previousIndex];
        const intersects = ((current.lat > point.lat) !== (previous.lat > point.lat))
            && (point.lng < ((previous.lng - current.lng) * (point.lat - current.lat) / (previous.lat - current.lat)) + current.lng);

        if (intersects) {
            inside = !inside;
        }
    }

    return inside;
}

function clearSelectedFeatures(draw, selectedFeatureIds) {
    selectedFeatureIds.forEach(featureId => {
        try {
            draw.deselectFeature(featureId);
        } catch {
            // Feature may have been removed since the local selection set was updated.
        }
    });
    selectedFeatureIds.clear();
}

function applyZoneSelection(draw, root, map, selectedFeatureIds, selectionType, predicate) {
    const collection = toFeatureCollection(draw.getSnapshot());
    const selectedIds = [];

    draw.setMode('select');
    clearSelectedFeatures(draw, selectedFeatureIds);

    collection.features.forEach(feature => {
        const featureLatLngs = collectFeatureLatLngs(feature);
        const anchor = getMeasurementAnchor(feature);

        if (featureLatLngs.some(predicate) || (anchor && predicate(anchor))) {
            selectedIds.push(feature.id);
        }
    });

    selectedIds.forEach(featureId => selectedFeatureIds.add(featureId));

    if (selectedIds.length > 0) {
        draw.selectFeature(selectedIds[0]);
    }

    root.dispatchEvent(new CustomEvent('daisy:leaflet:zone-select', {
        detail: {
            type: selectionType,
            featureIds: selectedIds,
            features: collection.features.filter(feature => selectedIds.includes(feature.id)),
            map,
            draw,
        },
    }));

    return selectedIds;
}

function suspendMapDragging(map) {
    const wasDraggingEnabled = Boolean(map.dragging?.enabled?.());

    if (wasDraggingEnabled) {
        map.dragging.disable();
    }

    map.getContainer().classList.add('cursor-crosshair');

    return () => {
        if (wasDraggingEnabled) {
            map.dragging.enable();
        }

        map.getContainer().classList.remove('cursor-crosshair');
    };
}

function startDragZoneSelection(L, map, draw, root, selectedFeatureIds, selectionType, onComplete) {
    const restoreDragging = suspendMapDragging(map);
    const container = map.getContainer();
    let startLatLng = null;
    let previewLayer = null;

    const cleanup = () => {
        container.removeEventListener('pointerdown', onDown);
        document.removeEventListener('pointermove', onMove);
        document.removeEventListener('pointerup', onUp);
        restoreDragging();

        if (previewLayer) {
            map.removeLayer(previewLayer);
            previewLayer = null;
        }
    };

    const renderPreview = (endLatLng) => {
        if (previewLayer) {
            map.removeLayer(previewLayer);
        }

        if (selectionType === 'circle') {
            previewLayer = L.circle(startLatLng, {
                radius: map.distance(startLatLng, endLatLng),
                color: '#2563eb',
                fillOpacity: 0.08,
                weight: 1,
            }).addTo(map);

            return;
        }

        previewLayer = L.rectangle(L.latLngBounds(startLatLng, endLatLng), {
            color: '#2563eb',
            fillOpacity: 0.08,
            weight: 1,
        }).addTo(map);
    };

    const onMove = event => {
        if (!startLatLng) {
            return;
        }

        renderPreview(map.mouseEventToLatLng(event));
    };
    const onUp = event => {
        if (!startLatLng) {
            cleanup();
            return;
        }

        const endLatLng = map.mouseEventToLatLng(event);
        const selectedIds = selectionType === 'circle'
            ? applyZoneSelection(
                draw,
                root,
                map,
                selectedFeatureIds,
                selectionType,
                latLng => map.distance(startLatLng, latLng) <= map.distance(startLatLng, endLatLng),
            )
            : applyZoneSelection(
                draw,
                root,
                map,
                selectedFeatureIds,
                selectionType,
                latLng => L.latLngBounds(startLatLng, endLatLng).contains(latLng),
            );

        cleanup();
        onComplete(selectedIds);
    };
    const onDown = event => {
        if (event.button !== 0) {
            return;
        }

        event.preventDefault();
        startLatLng = map.mouseEventToLatLng(event);
        document.addEventListener('pointermove', onMove);
        document.addEventListener('pointerup', onUp, { once: true });
    };

    container.addEventListener('pointerdown', onDown, { once: true });
}

function startPolygonZoneSelection(L, map, draw, root, selectedFeatureIds, onComplete) {
    const points = [];
    const wasDoubleClickZoomEnabled = Boolean(map.doubleClickZoom?.enabled?.());
    let previewLayer = null;

    map.getContainer().classList.add('cursor-crosshair');

    if (wasDoubleClickZoomEnabled) {
        map.doubleClickZoom.disable();
    }

    const cleanup = () => {
        map.off('click', onClick);
        map.off('mousemove', onMove);
        map.off('dblclick', finish);
        document.removeEventListener('keydown', onKeyDown);
        map.getContainer().classList.remove('cursor-crosshair');

        if (wasDoubleClickZoomEnabled) {
            map.doubleClickZoom.enable();
        }

        if (previewLayer) {
            map.removeLayer(previewLayer);
            previewLayer = null;
        }
    };

    const renderPreview = (cursorLatLng = null) => {
        if (previewLayer) {
            map.removeLayer(previewLayer);
        }

        const previewPoints = cursorLatLng ? [...points, cursorLatLng] : points;

        if (previewPoints.length < 2) {
            return;
        }

        previewLayer = previewPoints.length >= 3
            ? L.polygon(previewPoints, { color: '#2563eb', fillOpacity: 0.08, weight: 1 }).addTo(map)
            : L.polyline(previewPoints, { color: '#2563eb', weight: 1 }).addTo(map);
    };

    const finish = event => {
        event?.originalEvent?.preventDefault();

        if (points.length < 3) {
            cleanup();
            onComplete([]);
            return;
        }

        const selectedIds = applyZoneSelection(
            draw,
            root,
            map,
            selectedFeatureIds,
            'polygon',
            latLng => pointInLatLngPolygon(latLng, points),
        );

        cleanup();
        onComplete(selectedIds);
    };

    const onClick = event => {
        points.push(event.latlng);
        renderPreview();
    };
    const onMove = event => renderPreview(event.latlng);
    const onKeyDown = event => {
        if (event.key === 'Escape') {
            cleanup();
            onComplete([]);
        }

        if (event.key === 'Enter') {
            finish();
        }
    };

    map.on('click', onClick);
    map.on('mousemove', onMove);
    map.on('dblclick', finish);
    document.addEventListener('keydown', onKeyDown);
}

export {
    applyZoneSelection,
    clearSelectedFeatures,
    pointInLatLngPolygon,
    startDragZoneSelection,
    startPolygonZoneSelection,
};
