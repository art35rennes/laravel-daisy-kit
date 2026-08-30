import { escapeHtml } from './configuration.js';

function popupContent(popup) {
    if (!popup || typeof popup.content !== 'string') return null;
    if (popup.renderer === 'trusted-html' || popup.renderer === 'blade') return popup.content;

    const content = document.createElement('div');
    content.textContent = popup.content;

    return content;
}

function safeAssetUrl(value) {
    if (typeof value !== 'string' || value === '') return null;
    if (value.startsWith('/') && !value.startsWith('//')) return value;

    try {
        const url = new URL(value);

        return url.protocol === 'https:' ? value : null;
    } catch {
        return null;
    }
}

function createLayerControl(container, layer, type, onChange) {
    if (!container || layer.controllable === false) return null;

    const control = document.createElement('input');
    control.checked = type === 'radio' ? layer.selected === true : layer.visible !== false;
    control.classList.add(type === 'radio' ? 'radio' : 'checkbox', `${type === 'radio' ? 'radio' : 'checkbox'}-primary`);
    control.dataset[type === 'radio' ? 'daisyKitMapBasemap' : 'daisyKitMapLayer'] = layer.id;
    control.type = type;
    if (type === 'radio') control.name = `${container.closest('[data-daisy-kit-module="map"]')?.id || 'daisy-kit-map'}-basemap`;
    control.addEventListener('change', onChange);

    const label = document.createElement('label');
    label.classList.add('daisy-kit-map__layer-option', 'label', 'cursor-pointer');
    label.append(control, document.createTextNode(layer.label));
    container.append(label);

    return { control, destroy: () => label.remove() };
}

export async function createSources({ L, configuration, emit, map, root, signal }) {
    const layerControls = root.querySelector('[data-daisy-kit-map-layers]');
    const basemapControls = root.querySelector('[data-daisy-kit-map-basemaps]');
    const layerMenu = root.querySelector('[data-daisy-kit-map-layer-menu]');
    const records = new Map();
    const basemaps = new Map();
    const aborters = new Map();
    const failedLayers = new Set();
    let activeBasemap = null;
    let markerContainer = null;
    let markerRecords = [];
    let primaryGeoJSON = null;
    let selectionActive = false;
    const selectableFeatures = new Map();
    const selectedFeatures = new Map();

    async function createMarkerContainer() {
        if (!configuration.cluster) return map;

        const { MarkerClusterGroup } = await import('leaflet.markercluster/src/index.js');
        if (signal.aborted) return map;

        const clusterOptions = { ...configuration.cluster };
        delete clusterOptions.enabled;
        markerContainer = new MarkerClusterGroup(clusterOptions);
        markerContainer.addTo(map);

        return markerContainer;
    }

    const markerTarget = await createMarkerContainer();

    function updateSelection() {
        const features = [...selectedFeatures.values()].map(({ feature }) => feature);
        const selectionPanel = root.querySelector('[data-daisy-kit-map-selection]');
        const selectionSummary = root.querySelector('[data-daisy-kit-map-selection-summary]');
        if (selectionPanel) selectionPanel.hidden = features.length === 0;
        if (selectionSummary) {
            const template = configuration.labels.selectedFeatures ?? ':count features selected';
            selectionSummary.textContent = template.replace(':count', String(features.length));
        }
        emit('selection', { features, ids: [...selectedFeatures.keys()], source: 'layers' });

        return features;
    }

    function removeSelectableFeatures(owner) {
        let selectionChanged = false;
        for (const [key, record] of selectableFeatures) {
            if (record.owner !== owner) continue;
            record.layer.off?.('click', record.onClick);
            selectableFeatures.delete(key);
            if (selectedFeatures.has(key)) selectionChanged = true;
            selectedFeatures.delete(key);
        }

        return selectionChanged;
    }

    function setFeatureSelected(key, selected) {
        const record = selectableFeatures.get(key);
        if (!record) return false;
        if (selected) selectedFeatures.set(key, record);
        else selectedFeatures.delete(key);
        record.layer.getElement?.()?.classList.toggle('daisy-kit-map__selected-feature', selected);

        return true;
    }

    function clearSelection(notify = true) {
        const hadSelection = selectedFeatures.size > 0;

        for (const key of selectedFeatures.keys()) setFeatureSelected(key, false);
        if (notify) updateSelection();

        return hadSelection;
    }

    function removePrimaryGeoJSON() {
        const selectionChanged = removeSelectableFeatures('__primary');
        primaryGeoJSON?.remove();
        primaryGeoJSON = null;
        if (selectionChanged) updateSelection();
    }

    function addGeoJSON(data, options = {}, source = {}) {
        let featureIndex = 0;

        return L.geoJSON(data, {
            ...options,
            onEachFeature(feature, layer) {
                options.onEachFeature?.(feature, layer);
                const popup = feature?.properties?.popup;

                if (typeof popup === 'string') {
                    const content = document.createElement('div');
                    content.textContent = popup;
                    layer.bindPopup?.(content);
                }

                if (configuration.spatialSelection && source.selectable !== false) {
                    const rawId = feature?.id ?? feature?.properties?.id ?? featureIndex;
                    const key = `${source.owner ?? '__primary'}:${String(rawId)}`;
                    const onClick = () => {
                        if (!selectionActive) return;
                        setFeatureSelected(key, !selectedFeatures.has(key));
                        updateSelection();
                    };
                    selectableFeatures.set(key, { feature, key, layer, onClick, owner: source.owner ?? '__primary' });
                    layer.on?.('click', onClick);
                    featureIndex += 1;
                }
            },
        });
    }

    function setGeoJSON(data) {
        removePrimaryGeoJSON();

        if (!data?.type) return null;

        primaryGeoJSON = addGeoJSON(data, {}, { owner: '__primary', selectable: true }).addTo(map);

        return primaryGeoJSON;
    }

    function clearMarkers() {
        markerRecords.forEach(({ layer, onClick }) => {
            layer.off?.('click', onClick);
            markerContainer ? markerContainer.removeLayer?.(layer) : layer.remove();
        });
        markerRecords = [];
    }

    function markerIcon(icon) {
        if (!icon) return undefined;
        if (icon.type === 'trusted-html') {
            return L.divIcon({
                className: icon.className ?? '',
                html: icon.content ?? '',
                ...(icon.options ?? {}),
            });
        }

        if (icon.type === 'image' && icon.url) {
            const url = safeAssetUrl(icon.url);

            return url ? L.icon({ iconUrl: url, ...(icon.options ?? {}) }) : undefined;
        }

        return undefined;
    }

    function setMarkers(markers) {
        if (!Array.isArray(markers)) {
            return false;
        }

        clearMarkers();

        for (const marker of markers) {
            const latitude = Number(marker?.position?.[0]);
            const longitude = Number(marker?.position?.[1]);
            if (!Number.isFinite(latitude) || !Number.isFinite(longitude) || Math.abs(latitude) > 90 || Math.abs(longitude) > 180) continue;
            const options = { title: marker.label ?? marker.id };
            const icon = markerIcon(marker.icon);
            if (icon) options.icon = icon;
            const layer = L.marker([latitude, longitude], options);
            const content = popupContent(marker.popup);
            if (content) layer.bindPopup?.(content);

            const onClick = () => emit('marker', {
                id: marker.id,
                label: marker.label,
                position: [latitude, longitude],
                properties: { ...(marker.properties ?? {}) },
            });
            layer.on?.('click', onClick);
            markerTarget.addLayer ? markerTarget.addLayer(layer) : layer.addTo(map);
            markerRecords.push({ layer, marker, onClick });
        }

        return true;
    }

    function tileLayer(layer) {
        const attribution = layer.trustedAttribution === true
            ? layer.attribution ?? ''
            : escapeHtml(layer.attribution ?? '');
        const options = { ...(layer.options ?? {}), attribution };

        return layer.type === 'wms'
            ? L.tileLayer.wms(layer.url, options)
            : L.tileLayer(layer.url, options);
    }

    function persistableVisibility() {
        return Object.fromEntries([...records].map(([id, record]) => [id, record.visible]));
    }

    function setLayerVisibility(id, visible, notify = true) {
        const record = records.get(id);
        if (!record || record.layerConfig.controllable === false) return false;

        record.visible = visible === true;
        record.control?.control && (record.control.control.checked = record.visible);
        if (record.visible) record.leafletLayer?.addTo(map);
        else record.leafletLayer?.remove();
        if (notify) emit('layer', { id, visible: record.visible });

        return true;
    }

    async function readLayerData(layerConfig) {
        if (layerConfig.data?.type) return layerConfig.data;
        if (!layerConfig.url || layerConfig.type !== 'geojson') return null;

        aborters.get(layerConfig.id)?.abort();
        const controller = new AbortController();
        aborters.set(layerConfig.id, controller);
        signal.addEventListener('abort', () => controller.abort(), { once: true });
        const response = await fetch(layerConfig.url, { headers: { Accept: 'application/geo+json, application/json' }, signal: controller.signal });
        if (!response.ok) throw new Error(`Layer ${layerConfig.id} returned HTTP ${response.status}.`);

        return response.json();
    }

    async function mountLayer(layerConfig) {
        const data = await readLayerData(layerConfig);
        if (signal.aborted) return null;
        const leafletLayer = layerConfig.type === 'geojson'
            ? addGeoJSON(data, { style: layerConfig.style ?? undefined }, { owner: layerConfig.id, selectable: layerConfig.selectable })
            : tileLayer(layerConfig);
        const record = {
            control: null,
            data,
            layerConfig,
            leafletLayer,
            visible: layerConfig.visible !== false,
        };
        record.control = createLayerControl(layerControls, layerConfig, 'checkbox', (event) => {
            setLayerVisibility(layerConfig.id, event.currentTarget.checked);
        });
        records.set(layerConfig.id, record);
        if (record.visible) leafletLayer.addTo(map);

        return record;
    }

    async function setLayerData(id, data) {
        const record = records.get(id);
        if (!record || record.layerConfig.type !== 'geojson' || !data?.type) return false;
        const wasVisible = record.visible;
        const selectionChanged = removeSelectableFeatures(id);
        record.leafletLayer?.remove();
        record.data = data;
        record.leafletLayer = addGeoJSON(data, { style: record.layerConfig.style ?? undefined }, { owner: id, selectable: record.layerConfig.selectable });
        if (wasVisible) record.leafletLayer.addTo(map);
        if (selectionChanged) updateSelection();
        emit('layer-data', { data, id });

        return true;
    }

    async function refreshLayer(id) {
        const record = records.get(id);
        if (!record?.layerConfig.url || record.layerConfig.type !== 'geojson') return false;

        try {
            const data = await readLayerData(record.layerConfig);
            await setLayerData(id, data);
            failedLayers.delete(id);
            emit('layer-refresh', { id, status: 'ready' });

            return true;
        } catch (error) {
            if (error.name !== 'AbortError') {
                failedLayers.add(id);
                emit('layer-error', { code: 'layer-source-unavailable', id, message: error.message });
            }

            return false;
        }
    }

    function setBasemap(id, notify = true) {
        const next = basemaps.get(id);
        if (!next || next === activeBasemap) return false;
        activeBasemap?.leafletLayer.remove();
        next.leafletLayer.addTo(map);
        next.control?.control && (next.control.control.checked = true);
        activeBasemap = next;
        if (notify) emit('basemap', { id });

        return true;
    }

    function addBasemap(layerConfig) {
        const record = {
            control: null,
            layerConfig,
            leafletLayer: tileLayer(layerConfig),
        };
        record.control = createLayerControl(basemapControls, layerConfig, 'radio', () => setBasemap(layerConfig.id));
        basemaps.set(layerConfig.id, record);
    }

    const defaultTiles = configuration.provider
        ? { ...configuration.provider, id: '__provider', label: 'Provider', selected: true, trustedAttribution: true, type: 'xyz' }
        : configuration.tileUrl
            ? {
                attribution: configuration.tileAttribution,
                id: '__tiles',
                label: 'Tiles',
                options: configuration.tileOptions,
                selected: true,
                type: 'xyz',
                url: configuration.tileUrl,
            }
            : null;
    const configuredBasemaps = defaultTiles ? [defaultTiles, ...configuration.basemaps] : configuration.basemaps;
    configuredBasemaps.forEach(addBasemap);
    const selectedBasemap = configuredBasemaps.find((layer) => layer.selected) ?? configuredBasemaps[0];
    if (selectedBasemap) setBasemap(selectedBasemap.id, false);

    setGeoJSON(configuration.geojson);
    setMarkers(configuration.markers);
    await Promise.all(configuration.layers.map(async (layerConfig) => {
        try {
            await mountLayer(layerConfig);
        } catch (error) {
            if (error.name !== 'AbortError') {
                failedLayers.add(layerConfig.id);
                const record = {
                    control: null,
                    data: null,
                    layerConfig,
                    leafletLayer: null,
                    visible: layerConfig.visible !== false,
                };
                record.control = createLayerControl(layerControls, layerConfig, 'checkbox', (event) => {
                    setLayerVisibility(layerConfig.id, event.currentTarget.checked);
                });
                records.set(layerConfig.id, record);
                emit('layer-error', { code: 'layer-initialization-failed', id: layerConfig.id, message: error.message });
            }
        }
    }));

    if (basemapControls) basemapControls.hidden = basemaps.size === 0;
    if (layerControls) layerControls.hidden = records.size === 0;
    if (layerMenu) layerMenu.hidden = basemaps.size + records.size === 0;

    function bounds() {
        const layers = [primaryGeoJSON, ...[...records.values()].filter((record) => record.visible).map((record) => record.leafletLayer), ...markerRecords.map(({ layer }) => layer)].filter(Boolean);
        if (layers.length === 0) return null;
        if (typeof L.featureGroup === 'function') return L.featureGroup(layers).getBounds();

        return layers.find((layer) => layer.getBounds)?.getBounds() ?? null;
    }

    async function selectWithin(area) {
        if (!area?.geometry) return [];
        const { default: booleanIntersects } = await import('@turf/boolean-intersects');
        clearSelection(false);
        for (const [key, record] of selectableFeatures) {
            const owner = records.get(record.owner);
            if (owner && !owner.visible) continue;
            try {
                if (booleanIntersects(area, record.feature)) setFeatureSelected(key, true);
            } catch {
                // Invalid external features are ignored without blocking the other selections.
            }
        }

        return updateSelection();
    }

    return {
        activeBasemap: () => activeBasemap?.layerConfig.id ?? null,
        bounds,
        clearSelection,
        destroy() {
            aborters.forEach((controller) => controller.abort());
            clearMarkers();
            markerContainer?.remove();
            primaryGeoJSON?.remove();
            removeSelectableFeatures('__primary');
            records.forEach((record) => {
                removeSelectableFeatures(record.layerConfig.id);
                record.control?.destroy();
                record.leafletLayer?.remove();
            });
            basemaps.forEach((record) => {
                record.control?.destroy();
                record.leafletLayer.remove();
            });
            records.clear();
            basemaps.clear();
        },
        layerVisibility: persistableVisibility,
        getSelection: () => [...selectedFeatures.values()].map(({ feature }) => feature),
        failedLayerIds: () => [...failedLayers],
        refreshLayer,
        selectWithin,
        setBasemap,
        setGeoJSON,
        setLayerData,
        setLayerVisibility,
        setMarkers,
        setSelectionMode(mode) {
            selectionActive = ['feature-select', 'spatial-select'].includes(mode);
        },
    };
}
