import {
    createIconButton,
    getOrCreateControlStack,
} from '../controls.js';

const DEFAULT_GEOLOCATION_CONFIG = {
    enabled: true,
    button: true,
    auto: false,
    watch: false,
    setView: true,
    zoom: null,
    maximumAge: 10000,
    timeout: 10000,
    enableHighAccuracy: false,
    showAccuracy: true,
};

function normalizeGeolocationConfig(geolocation) {
    if (!geolocation) {
        return false;
    }

    return {
        ...DEFAULT_GEOLOCATION_CONFIG,
        ...(geolocation === true ? {} : geolocation),
    };
}

function positionToLatLng(position) {
    return [
        position.coords.latitude,
        position.coords.longitude,
    ];
}

function numberOrNull(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    return Number.isFinite(Number(value)) ? Number(value) : null;
}

function getPositionOptions(config) {
    return {
        enableHighAccuracy: Boolean(config.enableHighAccuracy),
        maximumAge: Number(config.maximumAge),
        timeout: Number(config.timeout),
    };
}

function createGeolocationDetail(position, metadata = {}) {
    const [lat, lng] = positionToLatLng(position);
    const coords = {
        accuracy: numberOrNull(position.coords.accuracy),
        altitude: numberOrNull(position.coords.altitude),
        altitudeAccuracy: numberOrNull(position.coords.altitudeAccuracy),
        heading: numberOrNull(position.coords.heading),
        latitude: lat,
        longitude: lng,
        speed: numberOrNull(position.coords.speed),
    };

    return {
        source: 'browser-geolocation',
        method: metadata.method || 'manual',
        watch: Boolean(metadata.watch),
        watching: Boolean(metadata.watching),
        lat,
        lng,
        accuracy: coords.accuracy,
        altitude: coords.altitude,
        altitudeAccuracy: coords.altitudeAccuracy,
        heading: coords.heading,
        speed: coords.speed,
        timestamp: position.timestamp ?? null,
        coords,
        options: metadata.options || {},
        feature: {
            type: 'Feature',
            properties: {
                source: 'browser-geolocation',
                method: metadata.method || 'manual',
                accuracy: coords.accuracy,
                altitude: coords.altitude,
                altitudeAccuracy: coords.altitudeAccuracy,
                heading: coords.heading,
                speed: coords.speed,
                timestamp: position.timestamp ?? null,
            },
            geometry: {
                type: 'Point',
                coordinates: [lng, lat],
            },
        },
        position,
    };
}

function dispatchGeolocationEvent(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy:leaflet:geolocation:${name}`, {
        detail,
    }));
}

function renderPosition(L, map, state, config, detail) {
    const latLng = [detail.lat, detail.lng];
    const accuracy = Number(detail.accuracy || 0);

    if (!state.marker) {
        state.marker = L.marker(latLng, {
            interactive: false,
            keyboard: false,
        }).addTo(map);
    } else {
        state.marker.setLatLng?.(latLng);
    }

    if (config.showAccuracy && accuracy > 0) {
        if (!state.accuracyLayer) {
            state.accuracyLayer = L.circle(latLng, {
                radius: accuracy,
                interactive: false,
                keyboard: false,
                color: '#2563eb',
                fillColor: '#2563eb',
                fillOpacity: 0.08,
                opacity: 0.35,
                weight: 1,
            }).addTo(map);
        } else {
            state.accuracyLayer.setLatLng?.(latLng);
            state.accuracyLayer.setRadius?.(accuracy);
        }
    }

    if (!config.showAccuracy && state.accuracyLayer) {
        map.removeLayer?.(state.accuracyLayer);
        state.accuracyLayer = null;
    }

    if (config.setView) {
        const hasConfiguredZoom = config.zoom !== null && config.zoom !== undefined && config.zoom !== '';
        const zoom = hasConfiguredZoom && Number.isFinite(Number(config.zoom))
            ? Number(config.zoom)
            : map.getZoom?.();

        if (Number.isFinite(zoom)) {
            map.setView?.(latLng, zoom);
        } else {
            map.panTo?.(latLng);
        }
    }

    return {
        ...detail,
    };
}

function removeGeolocationLayers(map, state) {
    if (state.marker) {
        map.removeLayer?.(state.marker);
        state.marker = null;
    }

    if (state.accuracyLayer) {
        map.removeLayer?.(state.accuracyLayer);
        state.accuracyLayer = null;
    }
}

function addGeolocationButton(root, config, api) {
    if (!config.button) {
        return null;
    }

    const stack = getOrCreateControlStack(root, config.position || 'topright');
    const wrapper = document.createElement('div');

    wrapper.className = 'daisy-leaflet-geolocation-controls relative flex flex-col items-end gap-2';

    const button = createIconButton(wrapper, 'locate', config.watch ? 'Suivre ma position' : 'Me localiser');

    button.classList.add('bg-base-100', 'shadow');
    button.setAttribute('aria-pressed', 'false');
    button.addEventListener('click', () => {
        if (config.watch) {
            if (api.isWatching()) {
                api.stop();
            } else {
                api.start();
            }

            return;
        }

        api.locate();
    });

    stack.appendChild(wrapper);

    return { wrapper, button };
}

async function apply(L, map, cfg, context) {
    const config = normalizeGeolocationConfig(cfg.geolocation);

    if (!config?.enabled) {
        return;
    }

    const root = context.root;
    const geolocation = navigator.geolocation;
    const state = {
        accuracyLayer: null,
        button: null,
        lastDetail: null,
        marker: null,
        watchId: null,
    };

    const handleSuccess = (position, metadata = {}) => {
        const geolocationDetail = createGeolocationDetail(position, {
            ...metadata,
            watching: state.watchId !== null || metadata.method === 'watch',
        });
        const detail = {
            ...renderPosition(L, map, state, config, geolocationDetail),
            map,
        };

        state.lastDetail = detail;
        dispatchGeolocationEvent(root, 'success', detail);

        return detail;
    };
    const handleError = (error, metadata = {}) => {
        dispatchGeolocationEvent(root, 'error', { error, map, ...metadata });

        return error;
    };
    const request = (method, run) => {
        const options = getPositionOptions(config);
        const metadata = {
            method,
            options,
            watch: method === 'watch',
        };

        dispatchGeolocationEvent(root, 'request', { map, ...metadata });
        run(
            position => handleSuccess(position, metadata),
            error => handleError(error, metadata),
            options,
        );
    };
    const api = {
        getLastPosition: () => state.lastDetail,
        isSupported: () => Boolean(geolocation),
        isWatching: () => state.watchId !== null,
        locate: (method = 'manual') => {
            if (!geolocation) {
                handleError(new Error('Geolocation is not supported by this browser.'), { method, watch: false });
                return false;
            }

            request(method, geolocation.getCurrentPosition.bind(geolocation));

            return true;
        },
        start: () => {
            if (!geolocation) {
                handleError(new Error('Geolocation is not supported by this browser.'), { method: 'watch', watch: true });
                return false;
            }

            if (state.watchId !== null) {
                return true;
            }

            const options = getPositionOptions(config);
            const metadata = { method: 'watch', options, watch: true };

            dispatchGeolocationEvent(root, 'request', { map, ...metadata });
            state.watchId = geolocation.watchPosition(
                position => handleSuccess(position, metadata),
                error => handleError(error, metadata),
                options,
            );
            state.button?.setAttribute('aria-pressed', 'true');

            return true;
        },
        stop: () => {
            if (state.watchId === null) {
                return false;
            }

            geolocation?.clearWatch?.(state.watchId);
            state.watchId = null;
            state.button?.setAttribute('aria-pressed', 'false');
            dispatchGeolocationEvent(root, 'stop', { map });

            return true;
        },
        clear: () => {
            removeGeolocationLayers(map, state);

            return true;
        },
        destroy: () => {
            api.stop();
            api.clear();
            state.wrapper?.remove();

            return true;
        },
    };
    const controlsConfig = context.controlsConfig || {};

    if (controlsConfig.geolocation !== false) {
        const controls = addGeolocationButton(root, {
            ...config,
            position: controlsConfig.position || 'topright',
        }, api);

        state.wrapper = controls?.wrapper || null;
        state.button = controls?.button || null;
    }

    context.geolocationApi = api;
    context.cleanups?.push(() => api.destroy());

    if (config.watch || config.auto) {
        if (config.watch) {
            api.start();
        } else {
            api.locate('auto');
        }
    }
}

export {
    apply,
    createGeolocationDetail,
    normalizeGeolocationConfig,
    positionToLatLng,
};
