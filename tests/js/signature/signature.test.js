import { beforeEach, describe, expect, it, vi } from 'vitest';

const pads = [];
const images = [];
let deferImages = false;

vi.mock('signature_pad', () => ({
    default: class {
        data = [];
        handlers = new Map();
        constructor() { pads.push(this); }
        addEventListener(name, handler) { this.handlers.set(name, handler); }
        clear() { this.data = []; }
        fromData(data) { this.data = [...data]; }
        isEmpty() { return this.data.length === 0; }
        off = vi.fn();
        removeEventListener(name) { this.handlers.delete(name); }
        toData() { return [...this.data]; }
        toDataURL() { return 'data:image/png;base64,SIGNED'; }
        toSVG() { return '<svg></svg>'; }
    },
}));

import { getInstance, mount, unmount } from '../../../resources/js/signature.js';

function root(value = '') {
    document.body.innerHTML = `<fieldset data-daisy-kit-module="signature"><canvas data-daisy-kit-signature-canvas width="640" height="240"></canvas><input data-daisy-kit-signature-value value="${value}"><button data-daisy-kit-signature-undo></button><button data-daisy-kit-signature-redo></button><button data-daisy-kit-signature-clear></button><script data-daisy-kit-config type="application/json">{"value":"${value}","required":true}</script></fieldset>`;
    const canvas = document.querySelector('canvas');
    vi.spyOn(canvas, 'getBoundingClientRect').mockReturnValue({ width: 320 });
    vi.spyOn(HTMLCanvasElement.prototype, 'getContext').mockReturnValue({ scale: vi.fn(), drawImage: vi.fn() });
    return document.querySelector('fieldset');
}

describe('signature entry', () => {
    beforeEach(() => {
        pads.length = 0;
        images.length = 0;
        deferImages = false;
        vi.stubGlobal('Image', class {
            constructor() { images.push(this); }
            get src() { return this.value; }
            set src(value) {
                this.value = value;
                if (deferImages) return;
                if (value === 'invalid') queueMicrotask(() => this.onerror(new Error('Invalid image')));
                else queueMicrotask(() => this.onload());
            }
        });
    });

    it.each(['clear', 'unmount'])('cancels a pending import on %s without a late change', async (action) => {
        const element = root();
        const instance = mount(element);
        const changes = vi.fn();
        element.addEventListener('daisy-kit:signature:change', changes);
        deferImages = true;
        const pending = instance.setValue('data:image/png;base64,SIGNED');
        if (action === 'clear') instance.clear();
        else unmount(element);
        const count = changes.mock.calls.length;
        images[0]?.onload?.();

        await expect(pending).resolves.toBe(false);
        expect(element.querySelector('input').value).toBe('');
        expect(changes).toHaveBeenCalledTimes(count);
    });

    it('cancels an import when drawing begins so its late image cannot erase the stroke', async () => {
        const element = root();
        const instance = mount(element);
        deferImages = true;
        const pending = instance.setValue('data:image/png;base64,SIGNED');
        const lateLoad = images[0].onload;
        pads[0].handlers.get('beginStroke')?.();
        pads[0].data = [{ points: [{ x: 20, y: 30 }] }];
        lateLoad();

        await expect(pending).resolves.toBe(false);
        expect(instance.toData()).toEqual([{ points: [{ x: 20, y: 30 }] }]);
        unmount(element);
        expect(pads[0].handlers.has('beginStroke')).toBe(false);
    });

    it('lets the newest import win and suppresses a canceled import error', async () => {
        const element = root();
        const instance = mount(element);
        const errors = vi.fn();
        element.addEventListener('daisy-kit:signature:error', errors);
        deferImages = true;
        const oldImport = instance.setValue('data:image/png;base64,OLD');
        const staleError = images[0].onerror;
        const newImport = instance.setValue('data:image/png;base64,NEW');
        images[1].onload();
        staleError();

        await expect(oldImport).resolves.toBe(false);
        await expect(newImport).resolves.toBe(true);
        expect(errors).not.toHaveBeenCalled();
        expect(instance.isEmpty()).toBe(false);
    });

    it('undoes new strokes without clearing an imported signature', async () => {
        const element = root();
        const instance = mount(element);
        await instance.setValue('data:image/png;base64,SIGNED');
        pads[0].data = [{ points: [{ x: 20, y: 30 }] }];
        pads[0].handlers.get('endStroke')();

        expect(instance.undo()).toBe(true);
        expect(instance.toData()).toEqual([]);
        expect(instance.isEmpty()).toBe(false);
        expect(element.querySelector('input').value).toBe('data:image/png;base64,SIGNED');
    });

    it('includes an imported image in SVG when requested', async () => {
        const element = root();
        const instance = mount(element);
        await instance.setValue('data:image/png;base64,SIGNED');

        expect(instance.toSVG({ includeDataUrl: true })).toContain('href="data:image/png;base64,SIGNED"');
        expect(atob(instance.toDataURL('image/svg+xml', { includeDataUrl: true }).split(',')[1])).toContain('<image');
    });

    it('leaves disposed facade mutations inert', async () => {
        const element = root();
        const instance = mount(element);
        unmount(element);
        const changes = vi.fn();
        element.addEventListener('daisy-kit:signature:change', changes);

        expect(instance.clear()).toBe(false);
        expect(instance.undo()).toBe(false);
        expect(instance.redo()).toBe(false);
        await expect(instance.setValue('data:image/png;base64,SIGNED')).resolves.toBe(false);
        expect(changes).not.toHaveBeenCalled();
        expect(element.querySelector('input').value).toBe('');
    });

    it('preserves an imported raster signature and its native value across resize', async () => {
        let notifyResize;
        vi.stubGlobal('ResizeObserver', class {
            constructor(callback) { notifyResize = callback; }
            observe() {}
            disconnect() {}
        });
        vi.spyOn(window, 'requestAnimationFrame').mockImplementation((callback) => { callback(); return 1; });
        const element = root();
        const instance = mount(element);
        await instance.setValue('data:image/png;base64,SIGNED');
        vi.spyOn(element.querySelector('canvas'), 'getBoundingClientRect').mockReturnValue({ width: 400 });

        notifyResize();

        expect(instance.isEmpty()).toBe(false);
        expect(element.querySelector('input').value).toBe('data:image/png;base64,SIGNED');
        instance.clear();
        expect(element.querySelector('input').value).toBe('');
        unmount(element);
        vi.unstubAllGlobals();
    });

    it('exposes a stable facade, synchronizes PNG values, and restores input on destroy', async () => {
        const element = root('data:image/png;base64,AAAA');
        const instance = mount(element);
        await Promise.resolve();
        await Promise.resolve();

        expect(instance).toBe(getInstance(element));
        expect(Object.keys(instance).sort()).toEqual(['clear', 'isEmpty', 'redo', 'setValue', 'toData', 'toDataURL', 'toSVG', 'undo']);
        expect(instance.toSVG()).toBe('<svg></svg>');
        expect(element.querySelector('input').value).toBe('data:image/png;base64,SIGNED');

        unmount(element);

        expect(pads[0].off).toHaveBeenCalled();
        expect(element.querySelector('input').value).toBe('data:image/png;base64,AAAA');
    });

    it('supports undo, redo, clear, and stroke events', () => {
        const element = root();
        const changes = [];
        const cleared = [];
        element.addEventListener('daisy-kit:signature:change', (event) => changes.push(event.detail));
        element.addEventListener('daisy-kit:signature:clear', (event) => cleared.push(event.detail));
        const instance = mount(element);
        pads[0].data = [{ points: [1] }, { points: [2] }];
        pads[0].handlers.get('endStroke')();

        expect(instance.undo()).toBe(true);
        expect(instance.redo()).toBe(true);
        expect(instance.clear()).toBe(true);

        expect(instance.isEmpty()).toBe(true);
        expect(changes.length).toBeGreaterThan(2);
        expect(cleared).toEqual([{ empty: true, value: '' }]);
    });

    it('returns a deeply detached point data snapshot', () => {
        const element = root();
        const instance = mount(element);
        pads[0].data = [{ color: 'black', points: [{ pressure: 0.5, x: 10, y: 20 }] }];

        const snapshot = instance.toData();
        snapshot[0].color = 'red';
        snapshot[0].points[0].x = 99;

        expect(pads[0].data).toEqual([{ color: 'black', points: [{ pressure: 0.5, x: 10, y: 20 }] }]);
    });

    it('returns a boolean from setValue and reports import failures without throwing', async () => {
        const element = root();
        const errors = [];
        element.addEventListener('daisy-kit:signature:error', (event) => errors.push(event.detail));
        const instance = mount(element);

        await expect(instance.setValue('data:image/png;base64,SIGNED')).resolves.toBe(true);
        await expect(instance.setValue('invalid')).resolves.toBe(false);

        expect(errors).toEqual([{
            code: 'invalid-value',
            message: 'Invalid image',
            value: 'invalid',
        }]);

        await expect(instance.setValue(null)).resolves.toBe(false);
        expect(errors.at(-1)).toEqual({
            code: 'invalid-value',
            message: 'The signature value must be a Data URL string.',
            value: null,
        });
    });
});
