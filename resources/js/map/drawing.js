import { emptyCollection } from './configuration.js';

const privateProperties = new Set([
    'closingPoint', 'committedCoordinateCount', 'coordinatePoint', 'coordinatePointFeatureId',
    'coordinatePointIds', 'currentlyDrawing', 'edited', 'marker', 'midPoint', 'mode',
    'provisionalCoordinateCount', 'selected', 'selectionPoint', 'selectionPointFeatureId', 'snappingPoint',
]);

function publicFeature(feature) {
    const copy = JSON.parse(JSON.stringify(feature));
    copy.properties = Object.fromEntries(Object.entries(copy.properties ?? {}).filter(([key]) => !privateProperties.has(key)));

    return copy;
}

function collection(features) {
    return { features: features.map(publicFeature), type: 'FeatureCollection' };
}

export async function createDrawing({ L, configuration, emit, map, root, signal, sources }) {
    const enabled = configuration.drawing || configuration.spatialSelection || configuration.value.features.length > 0;
    const valueInput = root.querySelector('[data-daisy-kit-map-value]');
    const measurementOutput = root.querySelector('[data-daisy-kit-map-measurement]');
    const modeOutput = root.querySelector('[data-daisy-kit-map-active-mode]');
    const undoButton = root.querySelector('[data-daisy-kit-map-history="undo"]');
    const redoButton = root.querySelector('[data-daisy-kit-map-history="redo"]');
    const exportButton = root.querySelector('[data-daisy-kit-map-export]');
    const deleteButton = root.querySelector('[data-daisy-kit-map-delete-selected]');
    const selectionPanel = root.querySelector('[data-daisy-kit-map-selection]');
    const selectionSummary = root.querySelector('[data-daisy-kit-map-selection-summary]');
    const objectTypeSelect = root.querySelector('[data-daisy-kit-map-object-type]');
    const drawLayerSelect = root.querySelector('[data-daisy-kit-map-draw-layer]');

    if (!enabled) {
        return {
            clearSelection: () => {},
            deleteSelected: () => false,
            destroy: () => {},
            exportGeoJSON: () => emptyCollection(),
            getDrawLayer: () => null,
            getSelection: () => [],
            redo: () => false,
            setDrawLayer: () => false,
            setMode: () => false,
            setObjectType: () => false,
            undo: () => false,
        };
    }

    const terra = await import('terra-draw');
    const { TerraDrawLeafletAdapter } = await import('terra-draw-leaflet-adapter');
    if (signal.aborted) return null;

    const initialGeometryTypes = new Set(configuration.value.features.map((feature) => feature?.geometry?.type));
    const modes = [];
    if (configuration.drawing?.point !== false && (configuration.drawing || initialGeometryTypes.has('Point'))) modes.push(new terra.TerraDrawPointMode());
    if (configuration.drawing?.line !== false && (configuration.drawing || initialGeometryTypes.has('LineString'))) modes.push(new terra.TerraDrawLineStringMode());
    if (configuration.drawing?.polygon !== false && (configuration.drawing || initialGeometryTypes.has('Polygon'))) modes.push(new terra.TerraDrawPolygonMode());
    const needsRectangle = (configuration.drawing && configuration.drawing.rectangle !== false)
        || ['area', 'both'].includes(configuration.spatialSelection?.mode);
    if (needsRectangle && terra.TerraDrawRectangleMode) modes.push(new terra.TerraDrawRectangleMode());
    modes.push(new terra.TerraDrawSelectMode());

    let generatedFeatureId = 0;

    const drawing = new terra.TerraDraw({
        adapter: new TerraDrawLeafletAdapter({ lib: L, map }),
        idStrategy: {
            getId: () => `daisy-kit-feature-${generatedFeatureId++}`,
            isValidId: (id) => (typeof id === 'string' && id !== '') || (typeof id === 'number' && Number.isFinite(id)),
        },
        modes,
        undoRedo: { sessionLevel: new terra.TerraDrawSessionUndoRedo() },
    });
    const selectedIds = new Set();
    let activeDrawLayer = configuration.drawLayers?.[0]?.id ?? null;
    let activeObjectType = configuration.objectTypes?.[0]?.id ?? null;
    let currentMode = null;
    let measurementTools = null;

    async function measureFeature(feature) {
        if (!configuration.measure || !feature?.geometry) return null;
        measurementTools ??= await Promise.all([import('@turf/area'), import('@turf/length')]);
        const [area, length] = measurementTools.map((module) => module.default);

        if (['Polygon', 'MultiPolygon'].includes(feature.geometry.type)) {
            return `${Math.round(area(feature)).toLocaleString()} m²`;
        }
        if (['LineString', 'MultiLineString'].includes(feature.geometry.type)) {
            return `${length(feature, { units: 'kilometers' }).toFixed(2)} km`;
        }

        return null;
    }

    function snapshot() {
        return collection(drawing.getSnapshot?.() ?? []);
    }

    function syncValue(change = true) {
        const geojson = snapshot();
        if (valueInput instanceof HTMLInputElement) {
            valueInput.value = JSON.stringify(geojson);
            valueInput.dispatchEvent(new Event('input', { bubbles: true }));
            if (change) valueInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        exportButton?.toggleAttribute('disabled', geojson.features.length === 0);
        emit('geometry', { geojson });

        return geojson;
    }

    function updateSelection() {
        const features = [...selectedIds].map((id) => drawing.getSnapshotFeature?.(id)).filter(Boolean).map(publicFeature);
        const count = features.length;
        deleteButton?.toggleAttribute('disabled', count === 0);
        if (selectionPanel) selectionPanel.hidden = count === 0;
        if (selectionSummary) {
            const template = configuration.labels.selectedFeatures ?? ':count features selected';
            selectionSummary.textContent = template.replace(':count', String(count));
        }
        emit('selection', { features, ids: [...selectedIds] });

        return features;
    }

    function setMode(mode, options = {}) {
        const terraMode = ['edit', 'feature-select'].includes(mode)
            ? 'select'
            : mode === 'spatial-select' ? 'rectangle' : mode;
        try {
            drawing.setMode(terraMode);
        } catch {
            return false;
        }

        currentMode = mode;
        activeObjectType = options.objectType ?? activeObjectType;
        activeDrawLayer = options.drawLayer ?? activeDrawLayer;
        sources?.setSelectionMode(mode);
        root.querySelectorAll('[data-daisy-kit-map-mode]').forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.daisyKitMapMode === mode));
        });
        if (modeOutput) {
            modeOutput.hidden = false;
            modeOutput.textContent = (configuration.labels.activeMode ?? 'Active mode: :mode').replace(':mode', mode);
        }
        emit('mode', { drawLayer: activeDrawLayer, mode, objectType: activeObjectType });

        return true;
    }

    function clearSelection() {
        const hadSelection = selectedIds.size > 0;

        [...selectedIds].forEach((id) => drawing.deselectFeature?.(id));
        selectedIds.clear();
        updateSelection();

        return hadSelection;
    }

    function deleteSelected() {
        if (selectedIds.size === 0) return false;
        drawing.removeFeatures([...selectedIds]);
        selectedIds.clear();
        updateSelection();
        syncValue();

        return true;
    }

    function undo() {
        const changed = drawing.undo?.() === true;
        if (changed) {
            syncValue();
            emit('history', { action: 'undo' });
        }

        return changed;
    }

    function redo() {
        const changed = drawing.redo?.() === true;
        if (changed) {
            syncValue();
            emit('history', { action: 'redo' });
        }

        return changed;
    }

    function exportGeoJSON() {
        const geojson = syncValue();
        emit('export', { geojson });

        return geojson;
    }

    async function onFinish(id, context) {
        let feature = drawing.getSnapshotFeature?.(id) ?? context?.feature;
        if (currentMode === 'spatial-select' && feature) {
            const selection = await sources?.selectWithin(feature) ?? [];
            drawing.removeFeatures([id]);
            syncValue();
            emit('spatial-selection', { area: publicFeature(feature), features: selection });

            return;
        }

        if (feature) {
            const objectType = configuration.objectTypes?.find((type) => type.id === activeObjectType);
            const drawLayer = configuration.drawLayers?.find((layer) => layer.id === activeDrawLayer);
            const properties = {
                ...(drawLayer?.properties ?? {}),
                ...(objectType?.properties ?? {}),
                ...(activeDrawLayer ? { drawLayer: activeDrawLayer } : {}),
                ...(activeObjectType ? { objectType: activeObjectType } : {}),
            };
            privateProperties.forEach((key) => delete properties[key]);
            drawing.updateFeatureProperties?.(id, properties);
            feature = drawing.getSnapshotFeature?.(id) ?? { ...feature, properties: { ...feature.properties, ...properties } };
        }
        const measurement = await measureFeature(feature);
        if (measurementOutput) {
            measurementOutput.hidden = !measurement;
            measurementOutput.textContent = measurement ?? '';
        }
        syncValue();
        emit('geometry-finish', { feature: feature ? publicFeature(feature) : null, id, measurement });
        if (measurement) emit('measurement', { id, value: measurement });
    }

    function onSelect(id) {
        if (id === undefined || id === null) return;
        selectedIds.add(id);
        updateSelection();
    }

    function onDeselect(id) {
        if (id === undefined || id === null) selectedIds.clear();
        else selectedIds.delete(id);
        updateSelection();
    }

    function onHistory({ redoStackSize = 0, undoStackSize = 0 } = {}) {
        undoButton?.toggleAttribute('disabled', undoStackSize === 0);
        redoButton?.toggleAttribute('disabled', redoStackSize === 0);
    }

    drawing.on('finish', onFinish);
    drawing.on('select', onSelect);
    drawing.on('deselect', onDeselect);
    drawing.on('history', onHistory);
    drawing.start();
    if (configuration.value.features.length > 0) {
        const modeByGeometry = { LineString: 'linestring', Point: 'point', Polygon: 'polygon' };
        drawing.addFeatures(configuration.value.features.map((feature) => ({
            ...feature,
            properties: { ...feature.properties, mode: feature.properties?.mode ?? modeByGeometry[feature.geometry.type] },
        })));
    }
    syncValue(false);

    return {
        clearSelection,
        deleteSelected,
        destroy() {
            drawing.off('finish', onFinish);
            drawing.off('select', onSelect);
            drawing.off('deselect', onDeselect);
            drawing.off('history', onHistory);
            drawing.stop();
            selectedIds.clear();
        },
        exportGeoJSON,
        getDrawLayer: () => activeDrawLayer,
        getSelection: () => [...selectedIds].map((id) => drawing.getSnapshotFeature?.(id)).filter(Boolean).map(publicFeature),
        redo,
        setDrawLayer(id) {
            if (!configuration.drawLayers?.some((layer) => layer.id === id)) return false;
            activeDrawLayer = id;
            if (drawLayerSelect) drawLayerSelect.value = id;

            return true;
        },
        setMode,
        setObjectType(id) {
            const type = configuration.objectTypes?.find((candidate) => candidate.id === id);
            if (!type) return false;
            activeObjectType = id;
            if (objectTypeSelect) objectTypeSelect.value = id;
            if (configuration.drawing) setMode(type.geometry === 'line' ? 'linestring' : type.geometry);

            return true;
        },
        undo,
    };
}
