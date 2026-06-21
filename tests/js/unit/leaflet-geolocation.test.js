/* @vitest-environment jsdom */

import { afterEach, describe, expect, it, vi } from 'vitest';
import {
    apply as applyGeolocation,
    createGeolocationDetail,
    normalizeGeolocationConfig,
    positionToLatLng,
} from '../../../resources/js/leaflet/plugins/geolocation.js';

function createPosition(latitude = 48.117, longitude = -1.678, accuracy = 12) {
    return {
        coords: {
            accuracy,
            altitude: 42,
            altitudeAccuracy: 3,
            heading: 180,
            latitude,
            longitude,
            speed: 1.5,
        },
        timestamp: 1710000000000,
    };
}

function createLayer() {
    return {
        addedTo: null,
        addTo(map) {
            this.addedTo = map;

            return this;
        },
        setLatLng: vi.fn(),
        setRadius: vi.fn(),
    };
}

function createLeafletMock() {
    return {
        circle: vi.fn(() => createLayer()),
        marker: vi.fn(() => createLayer()),
    };
}

function createMapMock() {
    return {
        getZoom: vi.fn(() => 13),
        panTo: vi.fn(),
        removeLayer: vi.fn(layer => {
            layer.addedTo = null;
        }),
        setView: vi.fn(),
    };
}

function mockGeolocation(geolocation) {
    Object.defineProperty(navigator, 'geolocation', {
        configurable: true,
        value: geolocation,
    });
}

afterEach(() => {
    vi.restoreAllMocks();
    document.body.innerHTML = '';
    Object.defineProperty(navigator, 'geolocation', {
        configurable: true,
        value: undefined,
    });
});

describe('Leaflet geolocation plugin', () => {
    it('normalizes config defaults and positions', () => {
        expect(normalizeGeolocationConfig(false)).toBe(false);
        expect(normalizeGeolocationConfig(true)).toMatchObject({
            enabled: true,
            button: true,
            auto: false,
            watch: false,
            setView: true,
            showAccuracy: true,
        });
        expect(positionToLatLng(createPosition(48, 2))).toEqual([48, 2]);
    });

    it('creates a rich integration object from the browser position', () => {
        const position = createPosition();
        const detail = createGeolocationDetail(position, {
            method: 'manual',
            options: { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 },
            watch: false,
            watching: false,
        });

        expect(detail).toMatchObject({
            source: 'browser-geolocation',
            method: 'manual',
            watch: false,
            watching: false,
            lat: 48.117,
            lng: -1.678,
            accuracy: 12,
            altitude: 42,
            altitudeAccuracy: 3,
            heading: 180,
            speed: 1.5,
            timestamp: 1710000000000,
            coords: {
                latitude: 48.117,
                longitude: -1.678,
                accuracy: 12,
                altitude: 42,
                altitudeAccuracy: 3,
                heading: 180,
                speed: 1.5,
            },
            options: {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 5000,
            },
            feature: {
                type: 'Feature',
                properties: {
                    source: 'browser-geolocation',
                    method: 'manual',
                    accuracy: 12,
                    altitude: 42,
                    altitudeAccuracy: 3,
                    heading: 180,
                    speed: 1.5,
                    timestamp: 1710000000000,
                },
                geometry: {
                    type: 'Point',
                    coordinates: [-1.678, 48.117],
                },
            },
            position,
        });
    });

    it('adds an on-demand location button and renders marker plus accuracy circle', async () => {
        const root = document.createElement('div');
        const L = createLeafletMock();
        const map = createMapMock();
        const position = createPosition();
        const events = [];
        const geolocation = {
            getCurrentPosition: vi.fn(success => success(position)),
        };

        mockGeolocation(geolocation);
        root.addEventListener('daisy:leaflet:geolocation:request', event => events.push(['request', event.detail]));
        root.addEventListener('daisy:leaflet:geolocation:success', event => events.push(['success', event.detail]));

        await applyGeolocation(L, map, {
            geolocation: true,
        }, {
            root,
            controlsConfig: { position: 'topright' },
            cleanups: [],
        });

        const button = root.querySelector('.daisy-leaflet-geolocation-controls button');

        button.click();

        expect(button.getAttribute('aria-label')).toBe('Me localiser');
        expect(geolocation.getCurrentPosition).toHaveBeenCalledWith(expect.any(Function), expect.any(Function), {
            enableHighAccuracy: false,
            maximumAge: 10000,
            timeout: 10000,
        });
        expect(L.marker).toHaveBeenCalledWith([48.117, -1.678], expect.objectContaining({ interactive: false }));
        expect(L.circle).toHaveBeenCalledWith([48.117, -1.678], expect.objectContaining({ radius: 12 }));
        expect(map.setView).toHaveBeenCalledWith([48.117, -1.678], 13);
        expect(events.map(([name]) => name)).toEqual(['request', 'success']);
        expect(events[0][1]).toMatchObject({ method: 'manual', watch: false, options: { timeout: 10000 } });
        expect(events[1][1]).toMatchObject({
            source: 'browser-geolocation',
            method: 'manual',
            lat: 48.117,
            lng: -1.678,
            accuracy: 12,
            altitude: 42,
            altitudeAccuracy: 3,
            heading: 180,
            speed: 1.5,
            timestamp: 1710000000000,
            feature: {
                type: 'Feature',
                geometry: { type: 'Point', coordinates: [-1.678, 48.117] },
            },
            map,
        });
    });

    it('starts and stops realtime tracking and exposes the public api hooks through context', async () => {
        const root = document.createElement('div');
        const L = createLeafletMock();
        const map = createMapMock();
        const cleanups = [];
        const context = {
            root,
            controlsConfig: { position: 'topright' },
            cleanups,
        };
        const geolocation = {
            clearWatch: vi.fn(),
            watchPosition: vi.fn(success => {
                success(createPosition(48.2, -1.7, 5));

                return 42;
            }),
        };

        mockGeolocation(geolocation);

        await applyGeolocation(L, map, {
            geolocation: { watch: true, zoom: 16 },
        }, context);

        const button = root.querySelector('.daisy-leaflet-geolocation-controls button');

        expect(context.geolocationApi.isWatching()).toBe(true);
        expect(button.getAttribute('aria-label')).toBe('Suivre ma position');
        expect(button.getAttribute('aria-pressed')).toBe('true');
        expect(map.setView).toHaveBeenCalledWith([48.2, -1.7], 16);
        expect(context.geolocationApi.getLastPosition()).toMatchObject({
            method: 'watch',
            watch: true,
            watching: true,
            lat: 48.2,
            lng: -1.7,
            accuracy: 5,
        });

        expect(context.geolocationApi.stop()).toBe(true);
        expect(geolocation.clearWatch).toHaveBeenCalledWith(42);
        expect(button.getAttribute('aria-pressed')).toBe('false');
        expect(context.geolocationApi.isWatching()).toBe(false);

        cleanups.forEach(cleanup => cleanup());
        expect(map.removeLayer).toHaveBeenCalled();
    });

    it('honors controls config when the integrator hides the geolocation button', async () => {
        const root = document.createElement('div');
        const geolocation = {
            getCurrentPosition: vi.fn(),
        };

        mockGeolocation(geolocation);

        await applyGeolocation(createLeafletMock(), createMapMock(), {
            geolocation: true,
        }, {
            root,
            controlsConfig: { geolocation: false },
            cleanups: [],
        });

        expect(root.querySelector('.daisy-leaflet-geolocation-controls')).toBeNull();
        expect(root.querySelector('.daisy-leaflet-control-stack')).toBeNull();
    });
});
