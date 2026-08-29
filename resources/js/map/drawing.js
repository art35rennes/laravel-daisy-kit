import { emptyCollection } from './configuration.js';

function collection(features) {
    return { features: features.map((feature) => ({ ...feature })), type: 'FeatureCollection' };
}

export async function createDrawing({ L, configuration, emit, map, root, signal }) {
    const enabled = configuration.drawing || configuration.spatialSelection || configuration.measure;
    const valueInput = root.querySelector('[data-daisy-kit-map-value]');
    const measurementOutput = root.querySelector('[data-daisy-kit-map-measurement]');
    const modeOutput = root.querySelector('[data-daisy-kit-map-active-mode]');
    const undoButton = root.querySelector('[data-daisy-kit-map-history="undo"]');
    const redoButton = root.querySelector('[data-daisy-kit-map-history="redo"]');
    const exportButton = root.querySelector('[data-daisy-kit-map-export]');
    const deleteButton = root.querySelector('[data-daisy-kit-map-delete-selected]');
    const selectionPanel = root.querySelector('[data-daisy-kit-map-selection]');
    const selectionSummary = root.querySelector('[data-daisy-kit-map-selection-summary]');

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
            undo: () => false,
        };
    }

    const terra = await import('terra-draw');
    const { TerraDrawLeafletAdapter } = await import('terra-draw-leaflet-adapter');
    if (signal.aborted) return null;

    const modes = [];
    if (configuration.drawing?.point !== false) modes.push(new terra.TerraDrawPointMode());
    if (configuration.drawing?.line !== false) modes.push(new terra.TerraDrawLineStringMode());
    if (configuration.drawing?.polygon !== false) modes.push(new terra.TerraDrawPolygonMode());
    if (configuration.drawing?.rectangle !== false && terra.TerraDrawRectangleMode) modes.push(new terra.TerraDrawRectangleMode());
    modes.push(new terra.TerraDrawSelectMode());

    const drawing = new terra.TerraDraw({
        adapter: new TerraDrawLeafletAdapter({ lib: L, map }),
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
        const features = [...selectedIds].map((id) => drawing.getSnapshotFeature?.(id)).filter(Boolean);
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
        const terraMode = mode === 'edit' ? 'select' : mode;
        try {
            drawing.setMode(terraMode);
        } catch {
            return false;
        }

        currentMode = mode;
        activeObjectType = options.objectType ?? activeObjectType;
        activeDrawLayer = options.drawLayer ?? activeDrawLayer;
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
        selectedIds.clear();
        if (currentMode === 'select') drawing.setMode('select');
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
        const feature = drawing.getSnapshotFeature?.(id) ?? context?.feature;
        if (feature) {
            feature.properties = {
                ...feature.properties,
                ...(activeDrawLayer ? { drawLayer: activeDrawLayer } : {}),
                ...(activeObjectType ? { objectType: activeObjectType } : {}),
            };
        }
        const measurement = await measureFeature(feature);
        if (measurementOutput) {
            measurementOutput.hidden = !measurement;
            measurementOutput.textContent = measurement ?? '';
        }
        syncValue();
        emit('geometry-finish', { feature, id, measurement });
        if (measurement) emit('measurement', { id, value: measurement });
    }

    function onSelect(id) {
        if (id === undefined || id === null) return;
        selectedIds.add(id);
        updateSelection();
    }

    function onDeselect() {
        selectedIds.clear();
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
    if (configuration.value.features.length > 0) drawing.addFeatures(configuration.value.features);
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
        getSelection: () => [...selectedIds].map((id) => drawing.getSnapshotFeature?.(id)).filter(Boolean),
        redo,
        setDrawLayer(id) {
            if (!configuration.drawLayers?.some((layer) => layer.id === id)) return false;
            activeDrawLayer = id;

            return true;
        },
        setMode,
        undo,
    };
}
