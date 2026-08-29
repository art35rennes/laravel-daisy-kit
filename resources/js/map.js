import L from 'leaflet';
import markerIconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import markerIconUrl from 'leaflet/dist/images/marker-icon.png';
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png';

import '../css/map.css';
import { readConfiguration, showConfigurationError, showError } from './core/configuration.js';
import { emit } from './map/events.js';
import { createMapRuntime } from './map/runtime.js';

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIconRetinaUrl,
    iconUrl: markerIconUrl,
    shadowUrl: markerShadowUrl,
});

const instances = new WeakMap();

export function getInstance(root) {
    return instances.get(root)?.facade ?? null;
}

export function mount(root) {
    if (!(root instanceof Element)) {
        throw new TypeError('Daisy Kit modules mount Element roots only.');
    }
    if (instances.has(root)) return instances.get(root).facade;

    const { error, value } = readConfiguration(root);
    if (error) {
        showConfigurationError(root);

        return null;
    }

    let runtime;
    runtime = createMapRuntime({
        L,
        onDestroy: () => unmount(root),
        rawConfiguration: value,
        root,
    });
    instances.set(root, runtime);
    runtime.start()
        .then(() => {
            if (instances.get(root) !== runtime) return;
            emit(root, 'mounted', { state: runtime.facade.getState() });
        })
        .catch((error) => {
            if (instances.get(root) !== runtime) return;
            const fallback = value.labels?.error ?? 'The map could not be loaded.';
            const message = error instanceof Error && error.message !== '' ? error.message : fallback;
            showError(root, message);
            const errorPanel = root.querySelector('[data-daisy-kit-map-error]');
            const errorMessage = root.querySelector('[data-daisy-kit-map-error-message]');
            if (errorPanel) errorPanel.hidden = false;
            if (errorMessage) errorMessage.textContent = message;
            emit(root, 'error', {
                code: 'initialization-failed',
                message,
            });
        });

    return runtime.facade;
}

export function mountAll(scope = document) {
    return [...scope.querySelectorAll('[data-daisy-kit-module="map"]')].map(mount);
}

export function unmount(root) {
    const runtime = instances.get(root);
    if (!runtime) return;

    runtime.internalDestroy();
    instances.delete(root);
    delete root.dataset.daisyKitState;
    emit(root, 'unmounted', {});
}
