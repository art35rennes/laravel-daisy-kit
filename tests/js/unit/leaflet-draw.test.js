/* @vitest-environment jsdom */

import { describe, expect, it, vi } from 'vitest';
import {
    applyObjectTypeToFeature,
    applyDrawLayerToFeature,
    applyZoneSelection,
    collectInitialFeatures,
    createActionBadge,
    createDrawLayerSelector,
    createSelectionDetailsPayload,
    createToolButton,
    createToolMenu,
    formatArea,
    formatDistance,
    getMeasurementAnchor,
    hasValidGeometryCoordinates,
    isPersistableFeature,
    measureFeature,
    modeFromFeature,
    modeFromObjectType,
    normalizeActionBadgeConfig,
    normalizeDrawConfig,
    normalizeDrawLayersConfig,
    normalizeDrawStyles,
    normalizeFeatureStyle,
    normalizeIconUrl,
    normalizeMeasureConfig,
    normalizeObjectTypes,
    normalizeSelectionDetailsConfig,
    objectTypeUsesMarkerMode,
    pointInLatLngPolygon,
    prepareFeature,
    propertiesFromDrawLayer,
    propertiesFromObjectType,
    resolveDrawLayer,
    resolveMeasurementLabelPlacement,
    resolveModeStyles,
    resolveSelectStyles,
    resolveToolIcon,
    sanitizeIconMarkup,
    syncMeasurementLayer,
    syncValue,
    toFeatureCollection,
} from '../../../resources/js/leaflet/plugins/draw.js';

describe('Leaflet draw helpers', () => {
    it('normalizes draw config defaults', () => {
        expect(normalizeDrawConfig(false)).toBe(false);

        const config = normalizeDrawConfig({ rectangle: false });

        expect(config.point).toBe(true);
        expect(config.line).toBe(true);
        expect(config.rectangle).toBe(false);
        expect(config.undoRedo).toBe(true);
        expect(config.groupedToolbar).toBe(true);
        expect(config.actionBadge).toEqual({ enabled: true, label: 'Outil actif' });
    });

    it('normalizes action badge config', () => {
        expect(normalizeActionBadgeConfig(false)).toEqual({ enabled: false, label: 'Outil actif' });
        expect(normalizeActionBadgeConfig({ enabled: false, label: 'Mode' })).toEqual({ enabled: false, label: 'Outil actif' });
        expect(normalizeActionBadgeConfig({ label: 'Mode en cours' })).toEqual({ enabled: true, label: 'Mode en cours' });
    });

    it('normalizes measure config defaults', () => {
        expect(normalizeMeasureConfig(false)).toBe(false);

        const config = normalizeMeasureConfig(true);

        expect(config.display).toBe('metric');
        expect(config.showTooltip).toBe(true);
        expect(config.maxLabels).toBe(16);
    });

    it('normalizes custom object types for business drawing tools', () => {
        const types = normalizeObjectTypes([
            { id: 'equipment', label: 'Équipement', geometry: 'point', iconSvg: '<svg data-custom="equipment"></svg>', properties: { category: 'asset' } },
            { id: 'pipe', label: 'Canalisation', geometry: 'LineString' },
            { id: 'structure', label: 'Ouvrage', geometry: 'polygon' },
            { id: 'bad', geometry: 'circle' },
        ]);

        expect(types.map(type => type.geometry)).toEqual(['point', 'line', 'polygon', 'point']);
        expect(types[0].iconSvg).toBe('<svg data-custom="equipment"></svg>');
        expect(types[1].icon).toBe('line');
        expect(types[0].properties).toEqual({ category: 'asset' });
        expect(modeFromObjectType(types[0])).toBe('point');
        expect(modeFromObjectType(types[1])).toBe('polyline');
        expect(modeFromObjectType(types[2])).toBe('polygon');
        expect(propertiesFromObjectType(types[0])).toEqual({
            category: 'asset',
            objectType: 'equipment',
            objectLabel: 'Équipement',
        });
    });

    it('normalizes object styles and switches custom point icons to marker mode', () => {
        const [hydrant] = normalizeObjectTypes([
            {
                id: 'hydrant',
                label: 'Borne incendie',
                geometry: 'point',
                markerSvg: '<svg viewBox="0 0 24 24"><path d="M1 1h22v22H1z"/></svg>',
                markerWidth: 30,
                markerHeight: 34,
                style: {
                    color: '#ef4444',
                    outlineColor: '#ffffff',
                    outlineWidth: 2,
                },
            },
        ]);

        expect(hydrant.style.markerUrl).toContain('data:image/svg+xml');
        expect(hydrant.style.markerWidth).toBe(30);
        expect(hydrant.style.markerHeight).toBe(34);
        expect(hydrant.style.pointColor).toBe('#ef4444');
        expect(objectTypeUsesMarkerMode(hydrant)).toBe(true);
        expect(modeFromObjectType(hydrant)).toBe('marker');
        expect(propertiesFromObjectType(hydrant).style.markerUrl).toContain('data:image/svg+xml');
    });

    it('normalizes selectable drawing layers and derives persisted properties', () => {
        const config = normalizeDrawLayersConfig({
            mode: 'select',
            allowNone: true,
            current: 'aep',
            layers: [
                { id: 'aep', label: 'Réseau AEP', properties: { network: 'water' } },
                { id: 'works', label: 'Travaux' },
            ],
        });

        expect(config.mode).toBe('select');
        expect(config.current).toBe('aep');
        expect(config.allowNone).toBe(true);
        expect(resolveDrawLayer(config, 'works').label).toBe('Travaux');
        expect(propertiesFromDrawLayer(config, 'aep')).toEqual({
            network: 'water',
            drawLayerId: 'aep',
            drawLayerLabel: 'Réseau AEP',
        });
        expect(propertiesFromDrawLayer(config, null)).toEqual({
            drawLayerId: null,
            drawLayerLabel: null,
        });
    });

    it('supports fixed and no-layer drawing layer modes', () => {
        const fixed = normalizeDrawLayersConfig({
            mode: 'fixed',
            layerId: 'assets',
            layers: [{ id: 'assets', label: 'Patrimoine' }],
        });
        const none = normalizeDrawLayersConfig({ mode: 'none' });

        expect(fixed.current).toBe('assets');
        expect(propertiesFromDrawLayer(fixed)).toEqual({
            drawLayerId: 'assets',
            drawLayerLabel: 'Patrimoine',
        });
        expect(propertiesFromDrawLayer(none)).toEqual({
            drawLayerId: null,
            drawLayerLabel: null,
        });
    });

    it('uses the first available drawing layer unless no-layer is explicitly selected', () => {
        const implicitDefault = normalizeDrawLayersConfig({
            mode: 'SELECT',
            allowNone: true,
            layers: [{ id: 'aep', label: 'Réseau AEP' }],
        });
        const explicitNone = normalizeDrawLayersConfig({
            mode: 'select',
            current: null,
            allowNone: true,
            layers: [{ id: 'aep', label: 'Réseau AEP' }],
        });

        expect(implicitDefault.current).toBe('aep');
        expect(explicitNone.current).toBeNull();
    });

    it('normalizes selection details config', () => {
        expect(normalizeSelectionDetailsConfig(false)).toEqual({ enabled: false, label: 'Détail de la sélection' });
        expect(normalizeSelectionDetailsConfig({ enabled: false, label: 'Inspecter' })).toEqual({ enabled: false, label: 'Détail de la sélection' });
        expect(normalizeSelectionDetailsConfig({ label: 'Inspecter la sélection' })).toEqual({ enabled: true, label: 'Inspecter la sélection' });
    });

    it('creates a drawing layer selector when the user can choose the current layer', () => {
        const toolbar = document.createElement('div');
        const changes = [];
        const selector = createDrawLayerSelector(toolbar, normalizeDrawLayersConfig({
            mode: 'select',
            allowNone: true,
            current: 'aep',
            noneLabel: 'Sans couche',
            layers: [
                { id: 'aep', label: 'Réseau AEP' },
                { id: 'works', label: 'Travaux' },
            ],
        }), layerId => changes.push(layerId));

        expect(selector.select.options).toHaveLength(3);
        expect(selector.select.value).toBe('aep');
        expect(selector.select.options[0].textContent).toBe('Sans couche');

        selector.select.value = 'works';
        selector.select.dispatchEvent(new Event('change'));

        expect(changes).toEqual(['works']);
    });

    it('normalizes line, polygon and rectangle style aliases for Terra Draw', () => {
        const styles = normalizeDrawStyles({
            line: { color: '#0ea5e9', width: 5, dashArray: '6 3' },
            polygon: { strokeColor: '#166534', strokeWidth: 2, fillColor: '#22c55e', fillOpacity: 0.2 },
            rectangle: { color: '#7c3aed', width: 3, fillColor: '#a78bfa' },
        });

        expect(styles.polyline).toEqual({
            lineStringColor: '#0ea5e9',
            lineStringWidth: 5,
            lineStringDash: [6, 3],
        });
        expect(styles.polygon).toMatchObject({
            polygonOutlineColor: '#166534',
            polygonOutlineWidth: 2,
            polygonFillColor: '#22c55e',
            polygonFillOpacity: 0.2,
        });
        expect(styles.rectangle).toMatchObject({
            outlineColor: '#7c3aed',
            outlineWidth: 3,
            fillColor: '#a78bfa',
        });
        expect(normalizeFeatureStyle('line', { dash: [4, 2] }).lineStringDash).toEqual([4, 2]);
    });

    it('resolves mode styles from feature style before object type defaults', () => {
        const styles = normalizeDrawStyles({ line: { color: '#000000', width: 2 } });
        const [pipe] = normalizeObjectTypes([
            { id: 'pipe', label: 'Conduite AEP', geometry: 'line', style: { color: '#2563eb', width: 4 } },
        ]);
        const modeStyles = resolveModeStyles(styles, [pipe], 'polyline');
        const feature = {
            properties: {
                objectType: 'pipe',
                style: { lineStringColor: '#ef4444' },
            },
        };

        expect(modeStyles.lineStringColor(feature)).toBe('#ef4444');
        expect(modeStyles.lineStringWidth(feature)).toBe(4);
    });

    it('maps GeoJSON geometries to Terra Draw modes', () => {
        expect(modeFromFeature({ geometry: { type: 'Point' }, properties: {} })).toBe('point');
        expect(modeFromFeature({ geometry: { type: 'LineString' }, properties: {} })).toBe('polyline');
        expect(modeFromFeature({ geometry: { type: 'Polygon' }, properties: {} })).toBe('polygon');
        expect(modeFromFeature({ geometry: { type: 'LineString' }, properties: { mode: 'line' } })).toBe('polyline');
        expect(modeFromFeature({ geometry: { type: 'LineString' }, properties: { mode: 'linestring' } })).toBe('polyline');
        expect(modeFromFeature({
            geometry: { type: 'Point' },
            properties: { objectType: 'hydrant' },
        }, normalizeObjectTypes([
            { id: 'hydrant', geometry: 'point', markerUrl: '/hydrant.svg' },
        ]))).toBe('marker');
    });

    it('prepares features with id and mode for Terra Draw', () => {
        const feature = prepareFeature({
            type: 'Feature',
            properties: { name: 'A' },
            geometry: { type: 'Point', coordinates: [2, 48] },
        });

        expect(feature.id).toBeTruthy();
        expect(feature.properties.mode).toBe('point');
    });

    it('filters Terra Draw internal helper features from persisted GeoJSON', () => {
        const collection = toFeatureCollection([
            {
                type: 'Feature',
                properties: { mode: 'point', name: 'A', selected: true, coordinatePointIds: ['handle-a'] },
                geometry: { type: 'Point', coordinates: [2, 48] },
            },
            {
                type: 'Feature',
                properties: { mode: 'point', coordinatePoint: true },
                geometry: { type: 'Point', coordinates: [2, 48] },
            },
        ]);

        expect(collection.features).toHaveLength(1);
        expect(collection.features[0].properties).toEqual({ mode: 'point', name: 'A' });
    });

    it('filters phantom point features without valid coordinates', () => {
        const validPoint = {
            type: 'Feature',
            properties: { mode: 'point', name: 'A' },
            geometry: { type: 'Point', coordinates: [2, 48] },
        };
        const phantomPoint = {
            type: 'Feature',
            properties: { mode: 'point', name: 'phantom' },
            geometry: { type: 'Point', coordinates: [] },
        };
        const missingPoint = {
            type: 'Feature',
            properties: { mode: 'point', name: 'missing' },
            geometry: { type: 'Point' },
        };
        const collection = toFeatureCollection([validPoint, phantomPoint, missingPoint]);

        expect(hasValidGeometryCoordinates(validPoint.geometry)).toBe(true);
        expect(isPersistableFeature(phantomPoint)).toBe(false);
        expect(isPersistableFeature(missingPoint)).toBe(false);
        expect(collection.features).toEqual([validPoint]);
    });

    it('keeps editable polygons while removing Terra Draw coordinate metadata', () => {
        const feature = prepareFeature({
            type: 'Feature',
            properties: { mode: 'polygon', coordinatePointIds: ['handle-a', 'handle-b'] },
            geometry: { type: 'Polygon', coordinates: [[[0, 0], [0, 1], [1, 1], [0, 0]]] },
        });
        const collection = toFeatureCollection([feature]);

        expect(collection.features).toHaveLength(1);
        expect(collection.features[0].geometry.type).toBe('Polygon');
        expect(collection.features[0].properties).toEqual({ mode: 'polygon' });
    });

    it('collects initial value and editable overlay features', () => {
        const value = {
            type: 'FeatureCollection',
            features: [{
                type: 'Feature',
                id: 'a',
                properties: {},
                geometry: { type: 'Point', coordinates: [2, 48] },
            }],
        };
        const overlay = {
            type: 'FeatureCollection',
            features: [{
                type: 'Feature',
                id: 'b',
                properties: {},
                geometry: { type: 'LineString', coordinates: [[2, 48], [3, 49]] },
            }],
        };

        const features = collectInitialFeatures({ value }, { editableCollections: [overlay] });

        expect(features).toHaveLength(2);
        expect(features.map(feature => feature.properties.mode)).toEqual(['point', 'polyline']);
    });

    it('formats distances and areas for metric display', () => {
        expect(formatDistance(0.25)).toBe('250 m');
        expect(formatDistance(1.234)).toBe('1.23 km');
        expect(formatArea(9500)).toBe('9500 m²');
        expect(formatArea(25000)).toBe('2.50 ha');
        expect(formatArea(2500000)).toBe('2.50 km²');
    });

    it('creates translated icon toolbar buttons', () => {
        const toolbar = document.createElement('div');
        const button = createToolButton(toolbar, {
            icon: 'ruler',
            label: 'Mesurer une distance',
            mode: 'polyline',
            action: 'measure-line',
        });

        expect(button.title).toBe('Mesurer une distance');
        expect(button.getAttribute('aria-label')).toBe('Mesurer une distance');
        expect(button.dataset.action).toBe('measure-line');
        expect(button.dataset.mode).toBe('polyline');
        expect(button.querySelector('svg')).not.toBeNull();
        expect(button.querySelector('.sr-only').textContent).toBe('Mesurer une distance');
        expect(button.classList.contains('group')).toBe(true);
        expect(button.querySelector('[aria-hidden="true"].group-hover\\:inline-flex').textContent).toBe('Mesurer une distance');
    });

    it('renders custom object tool icons from integrator SVG or HTML', () => {
        const toolbar = document.createElement('div');
        const svgButton = createToolButton(toolbar, {
            action: 'object:hydrant',
            iconSvg: '<svg data-icon="hydrant" viewBox="0 0 24 24"></svg>',
            label: 'Borne incendie',
            mode: 'point',
        });
        const htmlButton = createToolButton(toolbar, {
            action: 'object:valve',
            iconHtml: '<span data-icon="valve">V</span>',
            label: 'Vanne',
            mode: 'point',
        });

        expect(resolveToolIcon({ icon: 'pipe' })).toContain('<svg');
        expect(svgButton.querySelector('[data-icon="hydrant"]')).not.toBeNull();
        expect(htmlButton.querySelector('[data-icon="valve"]').textContent).toBe('V');
    });

    it('sanitizes custom icon markup before injecting it in toolbar buttons', () => {
        const toolbar = document.createElement('div');
        const button = createToolButton(toolbar, {
            action: 'object:unsafe',
            iconHtml: '<span onclick="alert(1)" data-icon="unsafe">X</span><script>window.bad = true</script><a href="javascript:alert(1)">bad</a>',
            label: 'Unsafe',
            mode: 'point',
        });

        expect(sanitizeIconMarkup('<svg onload="alert(1)"><script>alert(1)</script><path href="javascript:alert(1)"/></svg>')).not.toContain('script');
        expect(button.innerHTML).not.toContain('onclick');
        expect(button.innerHTML).not.toContain('<script');
        expect(button.innerHTML).not.toContain('javascript:');
        expect(button.querySelector('[data-icon="unsafe"]').textContent).toBe('X');
    });

    it('rejects unsafe marker icon urls', () => {
        expect(normalizeIconUrl('javascript:alert(1)')).toBeNull();
        expect(normalizeFeatureStyle('marker', { markerUrl: 'javascript:alert(1)' }).markerUrl).toBeUndefined();
        expect(normalizeIconUrl('/markers/hydrant.svg')).toBe('/markers/hydrant.svg');
        expect(normalizeIconUrl('https://example.com/marker.svg')).toBe('https://example.com/marker.svg');
        expect(normalizeIconUrl('data:image/svg+xml;charset=UTF-8,%3Csvg%3E%3C/svg%3E')).toContain('data:image/svg+xml');
    });

    it('detects points inside polygon selection zones', () => {
        const polygon = [[0, 0], [0, 10], [10, 10], [10, 0]];

        expect(pointInLatLngPolygon([5, 5], polygon)).toBe(true);
        expect(pointInLatLngPolygon([12, 5], polygon)).toBe(false);
    });

    it('selects editable features by zone and dispatches an event', () => {
        const root = document.createElement('div');
        const selectedFeatureIds = new Set();
        const features = [
            {
                id: 'inside',
                type: 'Feature',
                properties: { mode: 'point' },
                geometry: { type: 'Point', coordinates: [2, 48] },
            },
            {
                id: 'outside',
                type: 'Feature',
                properties: { mode: 'point' },
                geometry: { type: 'Point', coordinates: [3, 49] },
            },
        ];
        const draw = {
            deselectFeature: vi.fn(),
            getSnapshot: vi.fn(() => features),
            selectFeature: vi.fn(),
            setMode: vi.fn(),
        };
        const events = [];

        root.addEventListener('daisy:leaflet:zone-select', event => events.push(event.detail));

        const selected = applyZoneSelection(draw, root, {}, selectedFeatureIds, 'rectangle', ([lat, lng]) => lat < 48.5 && lng < 2.5);

        expect(selected).toEqual(['inside']);
        expect(draw.setMode).toHaveBeenCalledWith('select');
        expect(draw.selectFeature).toHaveBeenCalledWith('inside');
        expect(selectedFeatureIds.has('inside')).toBe(true);
        expect(events).toHaveLength(1);
        expect(events[0].type).toBe('rectangle');
        expect(events[0].featureIds).toEqual(['inside']);
    });

    it('keeps every zone-selected feature id and asks Terra Draw to select each one', () => {
        const root = document.createElement('div');
        const selectedFeatureIds = new Set();
        const features = [
            {
                id: 'inside-a',
                type: 'Feature',
                properties: { mode: 'point' },
                geometry: { type: 'Point', coordinates: [2, 48] },
            },
            {
                id: 'inside-b',
                type: 'Feature',
                properties: { mode: 'point' },
                geometry: { type: 'Point', coordinates: [2.1, 48.1] },
            },
            {
                id: 'outside',
                type: 'Feature',
                properties: { mode: 'point' },
                geometry: { type: 'Point', coordinates: [3, 49] },
            },
        ];
        const draw = {
            deselectFeature: vi.fn(),
            getSnapshot: vi.fn(() => features),
            selectFeature: vi.fn(),
            setMode: vi.fn(),
        };

        const selected = applyZoneSelection(draw, root, {}, selectedFeatureIds, 'rectangle', ([lat, lng]) => lat < 48.5 && lng < 2.5);

        expect(selected).toEqual(['inside-a', 'inside-b']);
        expect(draw.selectFeature).toHaveBeenCalledTimes(2);
        expect(draw.selectFeature).toHaveBeenCalledWith('inside-a');
        expect(draw.selectFeature).toHaveBeenCalledWith('inside-b');
        expect([...selectedFeatureIds]).toEqual(['inside-a', 'inside-b']);
    });

    it('applies a custom object type and dispatches an integration event', () => {
        const root = document.createElement('div');
        const feature = {
            id: 'feature-1',
            type: 'Feature',
            properties: { mode: 'point' },
            geometry: { type: 'Point', coordinates: [2, 48] },
        };
        const draw = {
            getSnapshot: vi.fn(() => [feature]),
            getSnapshotFeature: vi.fn(() => ({
                ...feature,
                properties: {
                    ...feature.properties,
                    objectType: 'equipment',
                    objectLabel: 'Équipement',
                },
            })),
            updateFeatureProperties: vi.fn(),
        };
        const events = [];

        root.addEventListener('daisy:leaflet:object-created', event => events.push(event.detail));

        const applied = applyObjectTypeToFeature(draw, root, {}, {
            id: 'equipment',
            label: 'Équipement',
            geometry: 'point',
            properties: { category: 'asset' },
        }, 'feature-1');

        expect(draw.updateFeatureProperties).toHaveBeenCalledWith('feature-1', {
            category: 'asset',
            objectType: 'equipment',
            objectLabel: 'Équipement',
        });
        expect(applied.properties.objectType).toBe('equipment');
        expect(events).toHaveLength(1);
        expect(events[0].objectTypeId).toBe('equipment');
        expect(events[0].exportGeoJSON()).toEqual({
            type: 'FeatureCollection',
            features: [feature],
        });
    });

    it('applies the active drawing layer to a persisted feature', () => {
        const feature = {
            id: 'feature-1',
            type: 'Feature',
            properties: { mode: 'point' },
            geometry: { type: 'Point', coordinates: [2, 48] },
        };
        const draw = {
            getSnapshot: vi.fn(() => [feature]),
            getSnapshotFeature: vi.fn(() => ({
                ...feature,
                properties: {
                    ...feature.properties,
                    drawLayerId: 'aep',
                    drawLayerLabel: 'Réseau AEP',
                },
            })),
            updateFeatureProperties: vi.fn(),
        };
        const config = normalizeDrawLayersConfig({
            mode: 'select',
            current: 'aep',
            layers: [{ id: 'aep', label: 'Réseau AEP' }],
        });

        const applied = applyDrawLayerToFeature(draw, config, 'aep', 'feature-1');

        expect(draw.updateFeatureProperties).toHaveBeenCalledWith('feature-1', {
            drawLayerId: 'aep',
            drawLayerLabel: 'Réseau AEP',
        });
        expect(applied.properties.drawLayerId).toBe('aep');
    });

    it('creates a multi-feature selection details payload', () => {
        const selectedFeatureIds = new Set(['pipe-1', 'hydrant-1']);
        const features = [
            {
                id: 'hydrant-1',
                type: 'Feature',
                properties: { mode: 'point', objectType: 'hydrant' },
                geometry: { type: 'Point', coordinates: [2, 48] },
            },
            {
                id: 'pipe-1',
                type: 'Feature',
                properties: { mode: 'polyline', objectType: 'water_main' },
                geometry: { type: 'LineString', coordinates: [[2, 48], [2.1, 48.1]] },
            },
            {
                id: 'phantom',
                type: 'Feature',
                properties: { mode: 'point' },
                geometry: { type: 'Point', coordinates: [] },
            },
        ];
        const draw = {
            getSnapshot: vi.fn(() => features),
        };
        const payload = createSelectionDetailsPayload(draw, selectedFeatureIds, { id: 'map' });

        expect(payload.count).toBe(2);
        expect(payload.featureIds).toEqual(['pipe-1', 'hydrant-1']);
        expect(payload.primaryFeatureId).toBe('pipe-1');
        expect(payload.features.map(feature => feature.properties.objectType)).toEqual(['water_main', 'hydrant']);
        expect(payload.exportGeoJSON().features).toHaveLength(2);
    });

    it('places measurement labels on feature anchors', () => {
        expect(getMeasurementAnchor({
            type: 'Feature',
            properties: { mode: 'point' },
            geometry: { type: 'Point', coordinates: [2, 48] },
        })).toEqual([48, 2]);

        expect(getMeasurementAnchor({
            type: 'Feature',
            properties: { mode: 'polyline' },
            geometry: { type: 'LineString', coordinates: [[2, 48], [3, 49], [4, 50]] },
        })).toEqual([49, 3]);

        expect(getMeasurementAnchor({
            type: 'Feature',
            properties: { mode: 'polygon' },
            geometry: { type: 'Polygon', coordinates: [[[2, 48], [4, 48], [4, 50], [2, 48]]] },
        })).toEqual([48.666666666666664, 3.3333333333333335]);
    });

    it('places measurement labels below shapes and avoids collisions', () => {
        const map = {
            latLngToLayerPoint: ([lat, lng]) => ({ x: lng * 10, y: lat * 10 }),
            layerPointToLatLng: ([x, y]) => [y / 10, x / 10],
        };
        const polygon = {
            type: 'Feature',
            properties: { mode: 'polygon' },
            geometry: { type: 'Polygon', coordinates: [[[0, 0], [2, 0], [2, 2], [0, 0]]] },
        };
        const occupiedRects = [];

        const firstPlacement = resolveMeasurementLabelPlacement(map, polygon, '10 m² / 20 m', occupiedRects);
        const secondPlacement = resolveMeasurementLabelPlacement(map, polygon, '10 m² / 20 m', occupiedRects);

        expect(firstPlacement.latLng[0]).toBeGreaterThan(2);
        expect(secondPlacement.latLng[0]).toBeGreaterThan(firstPlacement.latLng[0]);
        expect(occupiedRects).toHaveLength(2);
    });

    it('hides measurement labels when collision placement moves them too far from the feature', () => {
        const map = {
            latLngToLayerPoint: ([lat, lng]) => ({ x: lng * 10, y: lat * 10 }),
            layerPointToLatLng: ([x, y]) => [y / 10, x / 10],
        };
        const point = {
            type: 'Feature',
            properties: { mode: 'point' },
            geometry: { type: 'Point', coordinates: [0, 0] },
        };
        const occupiedRects = Array.from({ length: 4 }, (_, index) => ({
            left: -100,
            right: 100,
            top: 10 + (index * 30),
            bottom: 40 + (index * 30),
        }));

        expect(resolveMeasurementLabelPlacement(map, point, '48.000000, 2.000000', occupiedRects, { maxLabelOffsetPx: 60 })).toBeNull();
    });

    it('gives up on measurement placement after too many collisions', () => {
        const map = {
            latLngToLayerPoint: ([lat, lng]) => ({ x: lng * 10, y: lat * 10 }),
            layerPointToLatLng: ([x, y]) => [y / 10, x / 10],
        };
        const point = {
            type: 'Feature',
            properties: { mode: 'point' },
            geometry: { type: 'Point', coordinates: [0, 0] },
        };
        const occupiedRects = Array.from({ length: 12 }, (_, index) => ({
            left: -100,
            right: 100,
            top: 10 + (index * 30),
            bottom: 40 + (index * 30),
        }));

        expect(resolveMeasurementLabelPlacement(map, point, '48.000000, 2.000000', occupiedRects)).toBeNull();
    });

    it('measures line and polygon features', () => {
        const line = measureFeature({
            type: 'Feature',
            properties: { mode: 'linestring' },
            geometry: { type: 'LineString', coordinates: [[0, 0], [0, 1]] },
        });
        const polygon = measureFeature({
            type: 'Feature',
            properties: { mode: 'polygon' },
            geometry: { type: 'Polygon', coordinates: [[[0, 0], [0, 1], [1, 1], [0, 0]]] },
        });

        expect(line.type).toBe('line');
        expect(line.distance.kilometers).toBeGreaterThan(100);
        expect(polygon.type).toBe('polygon');
        expect(polygon.area.squareMeters).toBeGreaterThan(0);
        expect(polygon.perimeter.kilometers).toBeGreaterThan(0);
    });

    it('preserves rectangle mode and measures rectangle polygons as area and perimeter', () => {
        const rectangle = prepareFeature({
            type: 'Feature',
            properties: { mode: 'rectangle' },
            geometry: { type: 'Polygon', coordinates: [[[0, 0], [0, 1], [1, 1], [1, 0], [0, 0]]] },
        });
        const measurement = measureFeature(rectangle);

        expect(rectangle.properties.mode).toBe('rectangle');
        expect(measurement.type).toBe('polygon');
        expect(measurement.label).toContain('/');
        expect(measurement.area.squareMeters).toBeGreaterThan(0);
        expect(measurement.perimeter.kilometers).toBeGreaterThan(0);
    });

    it('syncs GeoJSON to the hidden input and dispatches change and measure events', () => {
        const root = document.createElement('div');
        const input = document.createElement('input');
        const measurePanel = document.createElement('output');
        const feature = {
            type: 'Feature',
            properties: { mode: 'point', selected: true },
            geometry: { type: 'Point', coordinates: [2, 48] },
        };
        const draw = {
            getSnapshot: () => [feature],
        };
        const events = [];

        input.dataset.leafletValue = '1';
        measurePanel.classList.add('hidden');
        root.append(input, measurePanel);
        root.addEventListener('daisy:leaflet:change', event => events.push(['change', event.detail]));
        root.addEventListener('daisy:leaflet:measure', event => events.push(['measure', event.detail]));

        syncValue(draw, root, { valueInputName: 'geometry' }, { showTooltip: true }, measurePanel);

        const collection = JSON.parse(input.value);

        expect(collection.features).toHaveLength(1);
        expect(collection.features[0].properties).toEqual({ mode: 'point' });
        expect(measurePanel.textContent).toBe('48.000000, 2.000000');
        expect(measurePanel.classList.contains('hidden')).toBe(false);
        expect(events.map(([name]) => name)).toEqual(['change', 'measure']);
        expect(events[0][1].inputName).toBe('geometry');
        expect(events[0][1].value).toEqual(collection);
        expect(events[1][1].latest.type).toBe('point');
    });

    it('limits rendered measurement labels while keeping measurements available', () => {
        const collection = {
            type: 'FeatureCollection',
            features: Array.from({ length: 6 }, (_, index) => ({
                id: `point-${index}`,
                type: 'Feature',
                properties: { mode: 'point' },
                geometry: { type: 'Point', coordinates: [2 + index, 48 + index] },
            })),
        };
        const addedLayers = [];
        const measurementLayer = {
            clearLayers: vi.fn(),
        };
        const L = {
            divIcon: vi.fn(options => ({ options })),
            marker: vi.fn(() => ({
                addTo(layer) {
                    addedLayers.push(layer);
                },
            })),
        };
        const map = {
            latLngToLayerPoint: ([lat, lng]) => ({ x: lng * 10, y: lat * 10 }),
            layerPointToLatLng: ([x, y]) => [y / 10, x / 10],
        };

        syncMeasurementLayer(L, map, measurementLayer, collection, { showTooltip: true, maxLabels: 3 });

        expect(measurementLayer.clearLayers).toHaveBeenCalled();
        expect(L.marker).toHaveBeenCalledTimes(3);
        expect(addedLayers).toEqual([measurementLayer, measurementLayer, measurementLayer]);
    });

    it('creates grouped toolbar menus for dense toolsets', () => {
        const toolbar = document.createElement('div');
        document.body.appendChild(toolbar);
        const menu = createToolMenu(toolbar, {
            action: 'draw-tools',
            icon: 'polygon',
            label: 'Dessin',
            items: [
                { icon: 'line', label: 'Dessiner une ligne', mode: 'polyline' },
                { icon: 'polygon', label: 'Dessiner un polygone', mode: 'polygon' },
            ],
        });

        expect(menu.button.getAttribute('aria-haspopup')).toBe('menu');
        expect(menu.button.title).toBe('Dessin');
        expect(menu.button.querySelector('.group-hover\\:inline-flex').textContent).toBe('Dessin');
        expect(menu.panel.getAttribute('role')).toBe('menu');
        expect(menu.buttons).toHaveLength(2);
        expect(menu.buttons[0].title).toBe('Dessiner une ligne');
        expect(toolbar.querySelectorAll('button')).toHaveLength(3);

        expect(menu.button.getAttribute('aria-expanded')).toBe('false');
        expect(menu.panel.classList.contains('hidden')).toBe(true);

        menu.button.click();

        expect(menu.button.getAttribute('aria-expanded')).toBe('true');
        expect(menu.button.title).toBe('');
        expect(menu.button.closest('[data-leaflet-tool-menu]').dataset.open).toBe('true');
        expect(menu.panel.classList.contains('hidden')).toBe(false);
        expect(menu.panel.classList.contains('flex')).toBe(true);
        expect(menu.panel.classList.contains('overflow-visible')).toBe(true);

        menu.button.dispatchEvent(new MouseEvent('mouseleave', { bubbles: true }));

        expect(menu.button.getAttribute('aria-expanded')).toBe('true');

        menu.buttons[0].click();

        expect(menu.button.getAttribute('aria-expanded')).toBe('false');
        expect(menu.panel.classList.contains('hidden')).toBe(true);

        toolbar.remove();
    });

    it('keeps already opened grouped toolbar menus open when another menu opens', () => {
        const toolbar = document.createElement('div');
        document.body.appendChild(toolbar);
        const drawMenu = createToolMenu(toolbar, {
            action: 'draw-tools',
            icon: 'polygon',
            label: 'Dessin',
            items: [{ icon: 'line', label: 'Dessiner une ligne', mode: 'polyline' }],
        });
        const measureMenu = createToolMenu(toolbar, {
            action: 'measure-tools',
            icon: 'ruler',
            label: 'Mesures',
            items: [{ icon: 'ruler', label: 'Mesurer une distance', mode: 'polyline' }],
        });

        drawMenu.button.click();
        measureMenu.button.click();

        expect(drawMenu.button.getAttribute('aria-expanded')).toBe('true');
        expect(measureMenu.button.getAttribute('aria-expanded')).toBe('true');

        toolbar.remove();
    });

    it('provides visible selection feedback for custom marker points and vector features', () => {
        const styles = resolveSelectStyles({
            select: {
                selectedLineStringColor: '#ef4444',
            },
        }, [
            {
                id: 'hydrant',
                style: {
                    markerUrl: '/markers/hydrant.svg',
                    markerWidth: 24,
                    markerHeight: 30,
                },
            },
        ]);
        const feature = {
            properties: {
                objectType: 'hydrant',
            },
        };

        expect(styles.selectedMarkerUrl(feature)).toBe('/markers/hydrant.svg');
        expect(styles.selectedMarkerWidth(feature)).toBe(32);
        expect(styles.selectedMarkerHeight(feature)).toBe(38);
        expect(styles.selectedPointColor).toBe('#2563eb');
        expect(styles.selectedPolygonOutlineColor).toBe('#2563eb');
        expect(styles.selectedLineStringColor).toBe('#ef4444');
    });

    it('closes grouped toolbar menus on escape and outside pointer interactions', () => {
        const toolbar = document.createElement('div');
        document.body.appendChild(toolbar);
        const menu = createToolMenu(toolbar, {
            action: 'measure-tools',
            icon: 'measure',
            label: 'Mesure',
            items: [
                { icon: 'measure', label: 'Mesurer une distance', mode: 'measure-line' },
            ],
        });

        menu.button.click();
        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));

        expect(menu.button.getAttribute('aria-expanded')).toBe('false');

        menu.button.click();
        document.body.dispatchEvent(new PointerEvent('pointerdown', { bubbles: true }));

        expect(menu.button.getAttribute('aria-expanded')).toBe('false');

        toolbar.remove();
    });

    it('shows an active tool badge and clears back to the default selection tool', () => {
        const root = document.createElement('div');
        const onClear = vi.fn();
        const badge = createActionBadge(root, { enabled: true, label: 'Mode' }, onClear);

        expect(badge.element.classList.contains('hidden')).toBe(true);

        badge.show('Dessiner une ligne');

        expect(root.textContent).toContain('Mode : Dessiner une ligne');
        expect(badge.element.classList.contains('inline-flex')).toBe(true);

        badge.element.querySelector('button').click();

        expect(onClear).toHaveBeenCalledTimes(1);

        badge.hide();

        expect(badge.element.classList.contains('hidden')).toBe(true);
    });

    it('does not render an active tool badge when disabled', () => {
        const root = document.createElement('div');
        const badge = createActionBadge(root, { enabled: false, label: 'Mode' }, vi.fn());

        badge.show('Dessiner une ligne');

        expect(root.children).toHaveLength(0);
        expect(badge.element).toBeNull();
    });
});
