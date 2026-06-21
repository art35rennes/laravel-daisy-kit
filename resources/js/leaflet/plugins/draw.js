/**
 * GIS drawing plugin for Daisy Leaflet.
 *
 * Uses Terra Draw instead of Leaflet-Geoman to keep the package on MIT-licensed
 * drawing primitives. The persisted value is plain GeoJSON FeatureCollection.
 *
 * @module leaflet/plugins/draw
 */

import {
    normalizeIconUrl,
    sanitizeIconMarkup,
} from './draw/icons.js';
import {
    STYLE_PROPERTY_BY_MODE,
    TOOL_ICONS,
    modeFromObjectType,
    normalizeActionBadgeConfig,
    normalizeDrawConfig,
    normalizeDrawStyles,
    normalizeFeatureStyle,
    normalizeMeasureConfig,
    normalizeObjectTypes,
    objectTypeUsesMarkerMode,
    propertiesFromObjectType,
    resolveToolIcon,
} from './draw/config.js';
import {
    collectInitialFeatures,
    isPersistableFeature,
    modeFromFeature,
    prepareFeature,
    toFeatureCollection,
} from './draw/features.js';
import {
    coordinateToLatLng,
    createMeasurePanel,
    createMeasurementLayer,
    formatArea,
    formatDistance,
    getMeasurementAnchor,
    measureFeature,
    resolveMeasurementLabelPlacement,
    syncMeasurementLayer,
} from './draw/measurements.js';
import {
    createActionBadge,
    createToolButton,
    createToolMenu,
    createToolbar,
    setActiveButton,
} from './draw/toolbar.js';
import {
    applyZoneSelection,
    clearSelectedFeatures,
    pointInLatLngPolygon,
    startDragZoneSelection,
    startPolygonZoneSelection,
} from './draw/selection.js';
import {
    TerraDraw,
    TerraDrawPointMode,
    TerraDrawMarkerMode,
    TerraDrawPolyLineMode,
    TerraDrawPolygonMode,
    TerraDrawRectangleMode,
    TerraDrawSelectMode,
    TerraDrawModeUndoRedo,
    TerraDrawSessionUndoRedo,
    TerraDrawUndoRedoKeyboardShortcuts,
} from 'terra-draw';
import { TerraDrawLeafletAdapter } from 'terra-draw-leaflet-adapter';

/**
 * @param {TerraDraw} draw
 * @param {HTMLElement} root
 * @param {L.Map} map
 * @param {Object|null} objectType
 * @param {string|null} featureId
 * @returns {Object|null}
 */
function applyObjectTypeToFeature(draw, root, map, objectType, featureId) {
    if (!draw || !root || !objectType || !featureId || typeof draw.updateFeatureProperties !== 'function') {
        return null;
    }

    const properties = propertiesFromObjectType(objectType);

    draw.updateFeatureProperties(featureId, properties);

    const feature = typeof draw.getSnapshotFeature === 'function'
        ? draw.getSnapshotFeature(featureId)
        : draw.getSnapshot().find(snapshotFeature => snapshotFeature.id === featureId);

    root.dispatchEvent(new CustomEvent('daisy:leaflet:object-created', {
        detail: {
            feature,
            featureId,
            objectType,
            objectTypeId: objectType.id,
            map,
            draw,
            exportGeoJSON: () => toFeatureCollection(draw.getSnapshot()),
        },
    }));

    return feature || null;
}

/**
 * @param {Object} drawStyles
 * @param {Object[]} objectTypes
 * @param {string} mode
 * @returns {Object}
 */
function resolveModeStyles(drawStyles, objectTypes, mode) {
    const propertyKey = STYLE_PROPERTY_BY_MODE[mode] || mode;
    const modeDefaults = drawStyles[mode] || {};
    const objectTypeStyles = new Map(objectTypes.map(objectType => [objectType.id, objectType.style || {}]));

    return Object.fromEntries(
        Object.entries({
            ...modeDefaults,
            ...Object.assign({}, ...objectTypes.map(objectType => objectType.style || {})),
        }).map(([styleKey, defaultValue]) => [
            styleKey,
            (feature) => {
                const featureStyle = feature?.properties?.style;
                const objectStyle = objectTypeStyles.get(feature?.properties?.objectType);

                return featureStyle?.[styleKey]
                    ?? objectStyle?.[styleKey]
                    ?? feature?.properties?.[propertyKey]?.[styleKey]
                    ?? defaultValue;
            },
        ]),
    );
}

/**
 * @param {Object} drawConfig
 * @param {Object[]} objectTypes
 * @returns {Array}
 */
function buildModes(drawConfig, objectTypes = []) {
    const modes = [];
    const drawStyles = normalizeDrawStyles(drawConfig.styles);

    if (drawConfig.point) {
        modes.push(new TerraDrawPointMode({
            editable: true,
            styles: resolveModeStyles(drawStyles, objectTypes, 'point'),
        }));
    }

    if (objectTypes.some(objectTypeUsesMarkerMode) || Object.keys(drawStyles.marker || {}).length > 0) {
        modes.push(new TerraDrawMarkerMode({
            editable: true,
            styles: resolveModeStyles(drawStyles, objectTypes, 'marker'),
        }));
    }

    if (drawConfig.line) {
        modes.push(new TerraDrawPolyLineMode({
            styles: resolveModeStyles(drawStyles, objectTypes, 'polyline'),
        }));
    }

    if (drawConfig.polygon) {
        modes.push(new TerraDrawPolygonMode({
            editable: true,
            showCoordinatePoints: true,
            styles: resolveModeStyles(drawStyles, objectTypes, 'polygon'),
        }));
    }

    if (drawConfig.rectangle) {
        modes.push(new TerraDrawRectangleMode({
            styles: resolveModeStyles(drawStyles, objectTypes, 'rectangle'),
        }));
    }

    if (drawConfig.select) {
        modes.push(new TerraDrawSelectMode({
            keyEvents: {
                deselect: 'Escape',
                delete: drawConfig.delete ? 'Backspace' : null,
                rotate: null,
                scale: null,
            },
            flags: {
                point: { feature: { draggable: true } },
                polyline: {
                    feature: {
                        draggable: true,
                        coordinates: { draggable: true, midpoints: true, deletable: true },
                    },
                },
                polygon: {
                    feature: {
                        draggable: true,
                        coordinates: { draggable: true, midpoints: true, deletable: true },
                    },
                },
                rectangle: { feature: { draggable: true } },
            },
        }));
    }

    return modes;
}

/**
 * @param {HTMLElement} root
 * @returns {HTMLInputElement|null}
 */
function findValueInput(root) {
    return root.querySelector('input[data-leaflet-value]');
}

/**
 * @param {TerraDraw} draw
 * @param {HTMLElement} root
 * @param {Object} cfg
 * @param {Object|false} measureConfig
 * @param {HTMLElement|null} measurePanel
 * @param {Object|null} measurementContext
 * @returns {void}
 */
function syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext = null) {
    const collection = toFeatureCollection(draw.getSnapshot());
    const input = findValueInput(root);

    if (input) {
        input.value = JSON.stringify(collection);
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const measurements = collection.features.map(measureFeature).filter(Boolean);
    const latestMeasurement = measurements[measurements.length - 1] || null;

    if (measurePanel) {
        if (measureConfig?.showTooltip && latestMeasurement) {
            measurePanel.textContent = latestMeasurement.label;
            measurePanel.classList.remove('hidden');
        } else {
            measurePanel.textContent = '';
            measurePanel.classList.add('hidden');
        }
    }

    if (measurementContext) {
        measurementContext.collection = collection;
    }

    syncMeasurementLayer(
        measurementContext?.L,
        measurementContext?.map,
        measurementContext?.measurementLayer,
        collection,
        measureConfig,
    );

    root.dispatchEvent(new CustomEvent('daisy:leaflet:change', {
        detail: {
            value: collection,
            inputName: cfg.valueInputName || null,
            measurements,
            draw,
        },
    }));

    if (measureConfig) {
        root.dispatchEvent(new CustomEvent('daisy:leaflet:measure', {
            detail: {
                measurements,
                latest: latestMeasurement,
                draw,
            },
        }));
    }
}

/**
 * Enables Terra Draw editing on a Leaflet map.
 *
 * @param {L} L
 * @param {L.Map} map
 * @param {Object} cfg
 * @param {Object} context
 * @returns {Promise<void>}
 */
export async function apply(L, map, cfg, context) {
    const drawConfig = normalizeDrawConfig(cfg.draw);

    if (!drawConfig) {
        return;
    }

    const objectTypes = normalizeObjectTypes(cfg.objectTypes);
    const modes = buildModes(drawConfig, objectTypes);

    if (modes.length === 0) {
        return;
    }

    const measureConfig = normalizeMeasureConfig(cfg.measure);
    const root = context.root;

    if (measureConfig && context.controlsState?.measurements === false) {
        measureConfig.showTooltip = false;
    }

    const adapter = new TerraDrawLeafletAdapter({ lib: L, map });
    const undoRedo = drawConfig.undoRedo ? {
        modeLevel: new TerraDrawModeUndoRedo({ maxStackSize: 50 }),
        sessionLevel: new TerraDrawSessionUndoRedo({ maxStackSize: 50 }),
        keyboardShortcuts: new TerraDrawUndoRedoKeyboardShortcuts(),
    } : undefined;
    const draw = new TerraDraw({ adapter, modes, undoRedo });
    const initialFeatures = collectInitialFeatures(cfg, context, objectTypes);
    const toolbar = drawConfig.toolbar ? createToolbar(root) : null;
    const measurePanel = measureConfig ? createMeasurePanel(root) : null;
    const measurementContext = measureConfig ? {
        L,
        map,
        measurementLayer: createMeasurementLayer(L, map),
        collection: null,
    } : null;
    const selectedFeatureIds = new Set();
    let activeToolbarAction = drawConfig.select ? 'select' : null;
    let activeObjectType = null;
    let actionBadge = null;

    draw.start();

    if (initialFeatures.length > 0) {
        draw.addFeatures(initialFeatures);
    }

    if (drawConfig.select) {
        draw.setMode('select');
    }

    const buttons = [];
    const objectCreatedFeatureIds = new Set();
    const cleanupCallbacks = [];

    const finishObjectFeature = (featureId) => {
        if (!activeObjectType || !featureId || objectCreatedFeatureIds.has(featureId)) {
            return typeof draw.getSnapshotFeature === 'function' ? draw.getSnapshotFeature(featureId) : null;
        }

        const feature = typeof draw.getSnapshotFeature === 'function' ? draw.getSnapshotFeature(featureId) : null;

        if (!isPersistableFeature(feature)) {
            return feature || null;
        }

        objectCreatedFeatureIds.add(featureId);

        return applyObjectTypeToFeature(draw, root, map, activeObjectType, featureId);
    };

    if (toolbar) {
        toolbar.classList.toggle('hidden', context.controlsState?.drawToolbar === false);

        const completeZoneSelection = () => {
            if (drawConfig.select) {
                activeToolbarAction = 'select';
                activeObjectType = null;
                setActiveButton(buttons, activeToolbarAction);
                actionBadge?.hide();
            }
        };
        const startZoneSelection = selectionType => {
            draw.setMode('select');
            activeObjectType = null;

            if (selectionType === 'circle' || selectionType === 'rectangle') {
                startDragZoneSelection(L, map, draw, root, selectedFeatureIds, selectionType, completeZoneSelection);
                return;
            }

            startPolygonZoneSelection(L, map, draw, root, selectedFeatureIds, completeZoneSelection);
        };
        const objectButtonDefinitions = objectTypes
            .filter(objectType => drawConfig[objectType.geometry] !== false)
            .map(objectType => ({
                action: `object:${objectType.id}`,
                icon: objectType.icon || objectType.geometry,
                iconHtml: objectType.iconHtml,
                iconSvg: objectType.iconSvg,
                label: objectType.label,
                mode: modeFromObjectType(objectType),
                objectType,
            }));

        const selectionButtonDefinitions = [
            drawConfig.select && drawConfig.zoneSelect !== false
                ? { action: 'select-circle', icon: 'selectCircle', label: 'Sélection par cercle', mode: 'select', zoneSelection: 'circle' }
                : null,
            drawConfig.select && drawConfig.zoneSelect !== false
                ? { action: 'select-rectangle', icon: 'selectRectangle', label: 'Sélection par rectangle', mode: 'select', zoneSelection: 'rectangle' }
                : null,
            drawConfig.select && drawConfig.zoneSelect !== false
                ? { action: 'select-polygon', icon: 'selectPolygon', label: 'Sélection par polygone', mode: 'select', zoneSelection: 'polygon' }
                : null,
        ].filter(Boolean);
        const drawButtonDefinitions = [
            drawConfig.point ? { icon: 'point', label: 'Placer un point', mode: 'point' } : null,
            drawConfig.line ? { icon: 'line', label: 'Dessiner une ligne', mode: 'polyline' } : null,
            drawConfig.polygon ? { icon: 'polygon', label: 'Dessiner un polygone', mode: 'polygon' } : null,
            drawConfig.rectangle ? { icon: 'rectangle', label: 'Dessiner un rectangle', mode: 'rectangle' } : null,
        ].filter(Boolean);
        const measureButtonDefinitions = [
            measureConfig && drawConfig.line
                ? { action: 'measure-line', icon: 'ruler', label: 'Mesurer une distance', mode: 'polyline' }
                : null,
            measureConfig && drawConfig.polygon
                ? { action: 'measure-area', icon: 'area', label: 'Mesurer une surface', mode: 'polygon' }
                : null,
        ].filter(Boolean);
        const flatButtonDefinitions = [
            ...objectButtonDefinitions,
            drawConfig.select ? { icon: 'select', label: 'Sélectionner', mode: 'select' } : null,
            ...selectionButtonDefinitions,
            ...drawButtonDefinitions,
            ...measureButtonDefinitions,
        ].filter(Boolean);

        const activateSelectionMode = () => {
            if (!drawConfig.select) {
                return;
            }

            draw.setMode('select');
            activeToolbarAction = 'select';
            activeObjectType = null;
            setActiveButton(buttons, activeToolbarAction);
            actionBadge?.hide();
        };
        actionBadge = createActionBadge(root, drawConfig.actionBadge, activateSelectionMode);
        const handleToolActivation = (definition) => {
            const nextAction = definition.action || definition.mode;

            if (activeToolbarAction === nextAction && drawConfig.select) {
                activateSelectionMode();
                return;
            }

            activeToolbarAction = nextAction;
            setActiveButton(buttons, activeToolbarAction);

            if (definition.zoneSelection) {
                actionBadge?.show(definition.label);
                startZoneSelection(definition.zoneSelection);
                return;
            }

            draw.setMode(definition.mode);
            activeObjectType = definition.objectType || null;
            if (definition.mode === 'select') {
                actionBadge?.hide();
            } else {
                actionBadge?.show(definition.label);
            }
        };
        const registerToolButton = (button, definition) => {
            button.addEventListener('click', event => {
                event.preventDefault();
                event.stopPropagation();
                handleToolActivation(definition);
            });
        };

        const addFlatButton = (definition) => {
            const button = createToolButton(toolbar, definition);
            buttons.push(button);
            registerToolButton(button, definition);
        };

        if (drawConfig.groupedToolbar) {
            if (drawConfig.select) {
                addFlatButton({ icon: 'select', label: 'Sélectionner', mode: 'select' });
            }

            [
                objectButtonDefinitions.length > 0
                    ? { action: 'object-tools', icon: 'equipment', label: 'Objets métier', items: objectButtonDefinitions }
                    : null,
                drawButtonDefinitions.length > 0
                    ? { action: 'draw-tools', icon: 'polygon', label: 'Dessin', items: drawButtonDefinitions }
                    : null,
                selectionButtonDefinitions.length > 0
                    ? { action: 'selection-tools', icon: 'selectRectangle', label: 'Sélection par zone', items: selectionButtonDefinitions }
                    : null,
                measureButtonDefinitions.length > 0
                    ? { action: 'measure-tools', icon: 'ruler', label: 'Mesures', items: measureButtonDefinitions }
                    : null,
            ].filter(Boolean).forEach(group => {
                const menu = createToolMenu(toolbar, group);

                menu.buttons.forEach((button, index) => {
                    buttons.push(button);
                    registerToolButton(button, group.items[index]);
                });
            });
        } else {
            flatButtonDefinitions.forEach(addFlatButton);
        }

        if (drawConfig.delete) {
            const deleteButton = createToolButton(toolbar, {
                icon: 'delete',
                label: 'Supprimer la sélection',
                mode: 'delete',
            });
            buttons.push(deleteButton);

            deleteButton.addEventListener('click', () => {
                if (selectedFeatureIds.size > 0) {
                    draw.removeFeatures([...selectedFeatureIds]);
                    selectedFeatureIds.clear();
                    syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
                }

                if (drawConfig.select) {
                    draw.setMode('select');
                    activeToolbarAction = 'select';
                    activeObjectType = null;
                    setActiveButton(buttons, activeToolbarAction);
                    actionBadge?.hide();
                }
            });
        }

        if (drawConfig.undoRedo) {
            const undoButton = createToolButton(toolbar, {
                icon: 'undo',
                label: 'Annuler',
                mode: 'undo',
            });
            const redoButton = createToolButton(toolbar, {
                icon: 'redo',
                label: 'Rétablir',
                mode: 'redo',
            });
            buttons.push(undoButton, redoButton);

            undoButton.addEventListener('click', () => {
                if (draw.canUndo() && draw.undo()) {
                    syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
                }
            });

            redoButton.addEventListener('click', () => {
                if (draw.canRedo() && draw.redo()) {
                    syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
                }
            });
        }

        setActiveButton(buttons, activeToolbarAction || draw.getMode());
    }

    const onControlsChange = event => {
        const controlsState = event.detail?.state || {};

        if (measureConfig) {
            measureConfig.showTooltip = controlsState.measurements !== false;
        }

        if (toolbar) {
            toolbar.classList.toggle('hidden', controlsState.drawToolbar === false);
        }

        syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
    };
    const onMapMove = () => {
        if (measurementContext?.collection) {
            syncMeasurementLayer(L, map, measurementContext.measurementLayer, measurementContext.collection, measureConfig);
        }
    };

    root.addEventListener('daisy:leaflet:controls-change', onControlsChange);
    cleanupCallbacks.push(() => root.removeEventListener('daisy:leaflet:controls-change', onControlsChange));

    draw.on('select', id => selectedFeatureIds.add(id));
    draw.on('deselect', id => selectedFeatureIds.delete(id));
    draw.on('change', (featureIds = [], changeType = null) => {
        const changedFeatureIds = Array.isArray(featureIds) ? featureIds : [featureIds].filter(Boolean);

        if (activeObjectType?.geometry === 'point' && changeType === 'create') {
            changedFeatureIds.forEach(featureId => finishObjectFeature(featureId));
        }

        syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
    });
    map.on('zoomend moveend', onMapMove);
    cleanupCallbacks.push(() => map.off?.('zoomend moveend', onMapMove));
    draw.on('finish', (featureId, finishContext = null) => {
        const feature = finishObjectFeature(featureId);

        root.dispatchEvent(new CustomEvent('daisy:leaflet:draw-finish', {
            detail: {
                feature,
                featureId,
                objectType: activeObjectType,
                objectTypeId: activeObjectType?.id || null,
                finishContext,
                map,
                draw,
                exportGeoJSON: () => toFeatureCollection(draw.getSnapshot()),
            },
        }));

        if (drawConfig.select) {
            draw.setMode('select');
            activeToolbarAction = 'select';
            activeObjectType = null;
            setActiveButton(buttons, activeToolbarAction);
            actionBadge?.hide();
        }

        syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
    });

    syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);

    context.draw = draw;
    context.exportGeoJSON = () => toFeatureCollection(draw.getSnapshot());
    context.drawApi = {
        setMode: mode => {
            if (!mode || typeof draw.setMode !== 'function') {
                return false;
            }

            draw.setMode(mode);

            return true;
        },
        clearSelection: () => {
            if (selectedFeatureIds.size === 0) {
                return false;
            }

            clearSelectedFeatures(draw, selectedFeatureIds);

            return true;
        },
        deleteSelected: () => {
            if (selectedFeatureIds.size === 0 || typeof draw.removeFeatures !== 'function') {
                return false;
            }

            draw.removeFeatures([...selectedFeatureIds]);
            selectedFeatureIds.clear();
            syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);

            return true;
        },
        undo: () => {
            if (typeof draw.canUndo !== 'function' || typeof draw.undo !== 'function' || !draw.canUndo()) {
                return false;
            }

            const changed = Boolean(draw.undo());
            if (changed) {
                syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
            }

            return changed;
        },
        redo: () => {
            if (typeof draw.canRedo !== 'function' || typeof draw.redo !== 'function' || !draw.canRedo()) {
                return false;
            }

            const changed = Boolean(draw.redo());
            if (changed) {
                syncValue(draw, root, cfg, measureConfig, measurePanel, measurementContext);
            }

            return changed;
        },
        destroy: () => {
            cleanupCallbacks.splice(0).reverse().forEach(cleanup => cleanup());
            toolbar?.querySelectorAll('[data-leaflet-tool-menu]').forEach(wrapper => wrapper.__daisyCloseToolMenu?.());
            toolbar?.remove();
            measurePanel?.remove();
            actionBadge?.element?.remove();
            measurementContext?.measurementLayer?.clearLayers?.();
            if (measurementContext?.measurementLayer && typeof map.removeLayer === 'function') {
                map.removeLayer(measurementContext.measurementLayer);
            }
            if (typeof draw.stop === 'function') {
                draw.stop();
            }

            return true;
        },
    };
    context.cleanups?.push(() => context.drawApi?.destroy?.());
}

export {
    collectInitialFeatures,
    applyZoneSelection,
    createActionBadge,
    createToolButton,
    createToolMenu,
    formatArea,
    formatDistance,
    getMeasurementAnchor,
    normalizeDrawStyles,
    normalizeFeatureStyle,
    objectTypeUsesMarkerMode,
    resolveMeasurementLabelPlacement,
    resolveModeStyles,
    measureFeature,
    modeFromFeature,
    modeFromObjectType,
    normalizeActionBadgeConfig,
    normalizeDrawConfig,
    normalizeIconUrl,
    normalizeMeasureConfig,
    normalizeObjectTypes,
    applyObjectTypeToFeature,
    pointInLatLngPolygon,
    prepareFeature,
    propertiesFromObjectType,
    resolveToolIcon,
    sanitizeIconMarkup,
    syncValue,
    syncMeasurementLayer,
    toFeatureCollection,
};
