/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';
import initSign from '../../../resources/js/modules/sign.js';

function installCanvasStub() {
    HTMLCanvasElement.prototype.getContext = vi.fn(() => ({
        beginPath: vi.fn(),
        clearRect: vi.fn(),
        drawImage: vi.fn(),
        lineCap: 'round',
        lineJoin: 'round',
        lineTo: vi.fn(),
        moveTo: vi.fn(),
        scale: vi.fn(),
        stroke: vi.fn(),
        strokeStyle: '',
        lineWidth: 1,
    }));

    HTMLCanvasElement.prototype.toDataURL = vi.fn(() => 'data:image/png;base64,current');
}

function installImageStub() {
    global.Image = class {
        listeners = {};

        addEventListener(name, listener) {
            this.listeners[name] = listener;
        }

        set src(value) {
            this._src = value;
            queueMicrotask(() => this.listeners.load?.());
        }

        get src() {
            return this._src;
        }
    };
}

function installResizeObserverStub() {
    const callbacks = [];

    global.ResizeObserver = class {
        constructor(callback) {
            callbacks.push(callback);
        }

        observe() {}

        disconnect() {}
    };

    return {
        flush() {
            callbacks.forEach((callback) => callback());
        },
    };
}

function createRoot(value = 'data:image/png;base64,initial') {
    document.body.innerHTML = `
        <div data-sign="1" data-width="400" data-height="200" data-responsive="true">
            <div data-sign-canvas-wrapper>
                <canvas data-sign-canvas width="400" height="200"></canvas>
            </div>
            <button type="button" data-sign-clear>Clear</button>
            <button type="button" data-sign-download>Download</button>
            <input type="hidden" data-sign-input value="${value}">
        </div>
    `;

    const root = document.querySelector('[data-sign="1"]');
    const wrapper = root.querySelector('[data-sign-canvas-wrapper]');

    Object.defineProperty(wrapper, 'clientWidth', {
        configurable: true,
        value: 320,
    });

    return root;
}

describe('sign module', () => {
    beforeEach(() => {
        vi.restoreAllMocks();
        installCanvasStub();
        installImageStub();
    });

    it('restores the initial signature without applying inline canvas styles', async () => {
        const root = createRoot();
        initSign(root);
        await Promise.resolve();

        expect(root.getSignature()).toBe('data:image/png;base64,initial');
        expect(root.querySelector('[data-sign-input]').value).toBe('data:image/png;base64,initial');
        expect(root.querySelector('[data-sign-canvas]').getAttribute('style')).toBeNull();
    });

    it('keeps the visible signature and hidden input stable after responsive resize', async () => {
        const resizeObserver = installResizeObserverStub();
        const root = createRoot();
        initSign(root);
        await Promise.resolve();

        resizeObserver.flush();
        await Promise.resolve();

        expect(root.getSignature()).toBe('data:image/png;base64,initial');
        expect(root.querySelector('[data-sign-input]').value).toBe('data:image/png;base64,initial');
    });
});
