import { escapeHtml } from './configuration.js';

function popupContent(popup) {
    if (!popup || typeof popup.content !== 'string') return null;
    if (popup.renderer === 'trusted-html' || popup.renderer === 'blade') return popup.content;

    const content = document.createElement('div');
    content.textContent = popup.content;

    return content;
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
    let activeBasemap = null;
    let markerContainer = null;
    let markerRecords = [];
    let primaryGeoJSON = null;

    async function createMarkerContainer() {
        if (!configuration.cluster) return map;

        await import('leaflet.markercluster');
        if (signal.aborted) return map;

        markerContainer = L.markerClusterGroup(configuration.cluster);
        markerContainer.addTo(map);

        return markerContainer;
    }

    const markerTarget = await createMarkerContainer();

    function removePrimaryGeoJSON() {
        primaryGeoJSON?.remove();
        primaryGeoJSON = null;
    }

    function addGeoJSON(data, options = {}) {
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
            },
        });
    }

    function setGeoJSON(data) {
        removePrimaryGeoJSON();

        if (!data?.type) return null;

        primaryGeoJSON = addGeoJSON(data).addTo(map);

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
            return L.icon({ iconUrl: icon.url, ...(icon.options ?? {}) });
        }

        return undefined;
    }

    function setMarkers(markers) {
        clearMarkers();

        for (const marker of Array.isArray(markers) ? markers : []) {
            const options = { title: marker.label ?? marker.id };
            const icon = markerIcon(marker.icon);
            if (icon) options.icon = icon;
            const layer = L.marker(marker.position, options);
            const content = popupContent(marker.popup);
            if (content) layer.bindPopup?.(content);

            const onClick = () => emit('marker', {
                id: marker.id,
                label: marker.label,
                position: [...marker.position],
                properties: { ...(marker.properties ?? {}) },
            });
            layer.on?.('click', onClick);
            markerTarget.addLayer ? markerTarget.addLayer(layer) : layer.addTo(map);
            markerRecords.push({ layer, marker, onClick });
        }
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
            ? addGeoJSON(data, { style: layerConfig.style ?? undefined })
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
        record.leafletLayer?.remove();
        record.data = data;
        record.leafletLayer = addGeoJSON(data, { style: record.layerConfig.style ?? undefined });
        if (wasVisible) record.leafletLayer.addTo(map);
        emit('layer-data', { data, id });

        return true;
    }

    async function refreshLayer(id) {
        const record = records.get(id);
        if (!record?.layerConfig.url || record.layerConfig.type !== 'geojson') return false;

        try {
            const data = await readLayerData(record.layerConfig);
            await setLayerData(id, data);
            emit('layer-refresh', { id, status: 'ready' });

            return true;
        } catch (error) {
            if (error.name !== 'AbortError') emit('layer-error', { id, message: error.message });

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
                emit('layer-error', { id: layerConfig.id, message: error.message });
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

    return {
        activeBasemap: () => activeBasemap?.layerConfig.id ?? null,
        bounds,
        destroy() {
            aborters.forEach((controller) => controller.abort());
            clearMarkers();
            markerContainer?.remove();
            primaryGeoJSON?.remove();
            records.forEach((record) => {
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
        refreshLayer,
        setBasemap,
        setGeoJSON,
        setLayerData,
        setLayerVisibility,
        setMarkers,
    };
}
