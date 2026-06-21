/* @vitest-environment jsdom */

import { afterEach, describe, expect, it, vi } from 'vitest';
import { apply as applyFullscreen } from '../../../resources/js/leaflet/plugins/fullscreen.js';

function createLeafletMock() {
    const controls = [];

    class BaseControl {
        addTo(map) {
            const node = this.onAdd(map);
            controls.push(node);
            map.controls.push(node);

            return this;
        }
    }

    return {
        controls,
        Control: {
            extend(definition) {
                return class extends BaseControl {
                    constructor() {
                        super();
                        Object.assign(this, definition);
                    }
                };
            },
        },
        DomUtil: {
            create(tagName, className, parent = null) {
                const element = document.createElement(tagName);
                element.className = className;

                if (parent) {
                    parent.appendChild(element);
                }

                return element;
            },
        },
        DomEvent: {
            disableClickPropagation: vi.fn(),
            on(element, eventName, callback) {
                element.addEventListener(eventName, callback);
            },
            stop(event) {
                event.preventDefault();
                event.stopPropagation();
            },
        },
    };
}

afterEach(() => {
    vi.restoreAllMocks();
    document.body.innerHTML = '';
});

describe('Leaflet fullscreen plugin', () => {
    it('requests fullscreen on the Daisy root so custom controls remain visible', async () => {
        const L = createLeafletMock();
        const root = document.createElement('div');
        const mapContainer = document.createElement('div');
        const layerControls = document.createElement('div');
        let fullscreenElement = null;
        const map = {
            controls: [],
            getContainer: () => mapContainer,
            invalidateSize: vi.fn(),
        };

        root.className = 'relative h-[28rem]';
        mapContainer.className = 'leaflet-container h-full';
        layerControls.className = 'daisy-leaflet-layer-controls';
        root.append(mapContainer, layerControls);
        document.body.appendChild(root);

        Object.defineProperty(document, 'fullscreenElement', {
            configurable: true,
            get: () => fullscreenElement,
        });
        root.requestFullscreen = vi.fn(() => {
            fullscreenElement = root;
            document.dispatchEvent(new Event('fullscreenchange'));

            return Promise.resolve();
        });
        document.exitFullscreen = vi.fn(() => {
            fullscreenElement = null;
            document.dispatchEvent(new Event('fullscreenchange'));

            return Promise.resolve();
        });
        vi.spyOn(window, 'requestAnimationFrame').mockImplementation(callback => {
            callback();

            return 1;
        });

        await applyFullscreen(L, map, {}, { root, cleanups: [] });

        const button = L.controls[0].querySelector('.leaflet-control-zoom-fullscreen');
        button.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(root.requestFullscreen).toHaveBeenCalledTimes(1);
        expect(fullscreenElement).toBe(root);
        expect(fullscreenElement).not.toBe(mapContainer);
        expect(root.classList.contains('daisy-leaflet-fullscreen-active')).toBe(true);
        expect(root.contains(layerControls)).toBe(true);
        expect(button.getAttribute('aria-pressed')).toBe('true');
        expect(map.invalidateSize).toHaveBeenCalled();

        button.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(document.exitFullscreen).toHaveBeenCalledTimes(1);
        expect(root.classList.contains('daisy-leaflet-fullscreen-active')).toBe(false);
        expect(button.getAttribute('aria-pressed')).toBe('false');
    });
});
