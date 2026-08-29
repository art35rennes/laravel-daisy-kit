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
    const drawLayerControls = [...root.querySelectorAll('[data-daisy-kit-map-draw-layer-visibility]')];

    if (!enabled) {
        return {
            clearSelection: () => {},
            deleteSelected: () => false,
            destroy: () => {},
            exportGeoJSON: () => emptyCollection(),
            getDrawLayer: () => null,
            getSelection: () => [],
            getVisibleDrawLayers: () => [],
            redo: () => false,
            setDrawLayer: () => false,
            setVisibleDrawLayers: () => false,
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
    const availableModes = new Set();
    const addMode = (name, mode) => {
        availableModes.add(name);
        modes.push(mode);
    };
    if (configuration.drawing?.point !== false && (configuration.drawing || initialGeometryTypes.has('Point'))) addMode('point', new terra.TerraDrawPointMode());
    if (configuration.drawing?.line !== false && (configuration.drawing || initialGeometryTypes.has('LineString'))) addMode('linestring', new terra.TerraDrawLineStringMode());
    if (configuration.drawing?.polygon !== false && (configuration.drawing || initialGeometryTypes.has('Polygon'))) addMode('polygon', new terra.TerraDrawPolygonMode());
    const needsRectangle = (configuration.drawing && configuration.drawing.rectangle !== false)
        || ['area', 'both'].includes(configuration.spatialSelection?.mode);
    if (needsRectangle && terra.TerraDrawRectangleMode) addMode('rectangle', new terra.TerraDrawRectangleMode());
    addMode('select', new terra.TerraDrawSelectMode());

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
    const configuredDrawLayerIds = new Set(configuration.drawLayers?.map(({ id }) => id) ?? []);
    const initiallyVisible = configuration.drawLayers?.filter(({ visible }) => visible !== false).map(({ id }) => id) ?? [];
    const visibleDrawLayers = new Set(
        configuration.drawLayerSelection === 'multiple'
            ? (initiallyVisible.length > 0 ? initiallyVisible : activeDrawLayer ? [activeDrawLayer] : [])
            : [initiallyVisible[0] ?? activeDrawLayer].filter(Boolean),
    );
    activeDrawLayer = getVisibleDrawLayers()[0] ?? activeDrawLayer;
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

    function writeValue(valueInput, serialized) {
        if (!(valueInput instanceof HTMLInputElement)) return;
        if (valueInput.getAttribute('value') !== serialized) valueInput.setAttribute('value', serialized);
        if (valueInput.value !== serialized) valueInput.value = serialized;
    }

    function featureIsVisible(feature) {
        const layer = feature?.properties?.drawLayer;

        return !layer || !configuredDrawLayerIds.has(layer) || visibleDrawLayers.has(layer);
    }

    function visibility(feature) {
        return featureIsVisible(feature) ? undefined : 0;
    }

    function applyLayerVisibility() {
        const styles = {
            linestring: {
                closingPointOpacity: visibility,
                closingPointOutlineOpacity: visibility,
                coordinatePointOpacity: visibility,
                coordinatePointOutlineOpacity: visibility,
                lineStringOpacity: visibility,
                snappingPointOpacity: visibility,
                snappingPointOutlineOpacity: visibility,
            },
            point: { pointOpacity: visibility, pointOutlineOpacity: visibility },
            polygon: {
                closingPointOpacity: visibility,
                closingPointOutlineOpacity: visibility,
                coordinatePointOpacity: visibility,
                coordinatePointOutlineOpacity: visibility,
                fillOpacity: visibility,
                outlineOpacity: visibility,
                snappingPointOpacity: visibility,
                snappingPointOutlineOpacity: visibility,
            },
            rectangle: { fillOpacity: visibility, outlineOpacity: visibility },
            select: {
                midPointOpacity: visibility,
                midPointOutlineOpacity: visibility,
                selectedLineStringOpacity: visibility,
                selectedPointOpacity: visibility,
                selectedPointOutlineOpacity: visibility,
                selectedPolygonFillOpacity: visibility,
                selectedPolygonOutlineOpacity: visibility,
                selectionPointOpacity: visibility,
                selectionPointOutlineOpacity: visibility,
            },
        };

        Object.entries(styles)
            .filter(([mode]) => availableModes.has(mode))
            .forEach(([mode, modeStyles]) => drawing.setModeStyles?.(mode, modeStyles));
    }

    function getVisibleDrawLayers() {
        return configuration.drawLayers?.map(({ id }) => id).filter((id) => visibleDrawLayers.has(id)) ?? [];
    }

    function setVisibleDrawLayers(ids, notify = true) {
        if (!Array.isArray(ids) || ids.length === 0 || ids.some((id) => !configuredDrawLayerIds.has(id))) return false;
        const nextIds = [...new Set(ids)];
        if (configuration.drawLayerSelection !== 'multiple' && nextIds.length !== 1) return false;

        visibleDrawLayers.clear();
        nextIds.forEach((id) => visibleDrawLayers.add(id));
        if (activeDrawLayer && !visibleDrawLayers.has(activeDrawLayer)) {
            activeDrawLayer = nextIds[0];
            if (drawLayerSelect) drawLayerSelect.value = activeDrawLayer;
        }
        drawLayerControls.forEach((control) => {
            control.checked = visibleDrawLayers.has(control.value);
        });
        [...selectedIds].forEach((id) => {
            const feature = drawing.getSnapshotFeature?.(id);
            if (!featureIsVisible(feature)) {
                drawing.deselectFeature?.(id);
                selectedIds.delete(id);
            }
        });
        updateSelection();
        applyLayerVisibility();
        if (notify) emit('draw-layers', { ids: getVisibleDrawLayers(), mode: configuration.drawLayerSelection });

        return true;
    }

    function syncValue(change = true) {
        const geojson = snapshot();
        const valueInput = root.querySelector('[data-daisy-kit-map-value]');
        if (valueInput instanceof HTMLInputElement) {
            writeValue(valueInput, JSON.stringify(geojson));
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
        [...selectedIds].forEach((id) => drawing.deselectFeature?.(id));
        selectedIds.clear();
        updateSelection();
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
        if (!featureIsVisible(drawing.getSnapshotFeature?.(id))) {
            drawing.deselectFeature?.(id);

            return;
        }
        selectedIds.add(id);
        updateSelection();
    }

    function onDeselect(id) {
        if (id === undefined || id === null) selectedIds.clear();
        else selectedIds.delete(id);
        updateSelection();
    }

    function onHistory({ redoSize = 0, undoSize = 0 } = {}) {
        undoButton?.toggleAttribute('disabled', undoSize === 0);
        redoButton?.toggleAttribute('disabled', redoSize === 0);
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
    setVisibleDrawLayers(getVisibleDrawLayers(), false);
    syncValue(false);
    const restoreValue = () => {
        const valueInput = root.querySelector('[data-daisy-kit-map-value]');
        if (!(valueInput instanceof HTMLInputElement)) return;
        const serialized = JSON.stringify(snapshot());
        writeValue(valueInput, serialized);
    };
    const valueObserver = new MutationObserver(restoreValue);
    valueObserver.observe(root, { attributeFilter: ['value'], attributes: true, childList: true, subtree: true });
    const onPageShow = () => queueMicrotask(restoreValue);
    window.addEventListener('pageshow', onPageShow);

    return {
        clearSelection,
        deleteSelected,
        destroy() {
            window.removeEventListener('pageshow', onPageShow);
            valueObserver?.disconnect();
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
        getVisibleDrawLayers,
        redo,
        setDrawLayer(id) {
            if (!configuration.drawLayers?.some((layer) => layer.id === id)) return false;
            activeDrawLayer = id;
            if (drawLayerSelect) drawLayerSelect.value = id;
            const nextVisible = configuration.drawLayerSelection === 'multiple'
                ? [...new Set([...getVisibleDrawLayers(), id])]
                : [id];
            setVisibleDrawLayers(nextVisible);

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
        setVisibleDrawLayers,
        undo,
    };
}
