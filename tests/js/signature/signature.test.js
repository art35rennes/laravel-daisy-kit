import { beforeEach, describe, expect, it, vi } from 'vitest';

const pads = [];

vi.mock('signature_pad', () => ({
    default: class {
        data = [];
        handlers = new Map();
        constructor() { pads.push(this); }
        addEventListener(name, handler) { this.handlers.set(name, handler); }
        clear() { this.data = []; }
        fromData(data) { this.data = [...data]; }
        fromDataURL = vi.fn(async () => { this.data = [{ points: [{}] }]; });
        isEmpty() { return this.data.length === 0; }
        off = vi.fn();
        removeEventListener(name) { this.handlers.delete(name); }
        toData() { return [...this.data]; }
        toDataURL() { return this.isEmpty() ? 'data:image/png;base64,EMPTY' : 'data:image/png;base64,SIGNED'; }
        toSVG() { return '<svg></svg>'; }
    },
}));

import { getInstance, mount, unmount } from '../../../resources/js/signature.js';

function root(value = '') {
    document.body.innerHTML = `<fieldset data-daisy-kit-module="signature"><canvas data-daisy-kit-signature-canvas width="640" height="240"></canvas><input data-daisy-kit-signature-value value="${value}"><button data-daisy-kit-signature-undo></button><button data-daisy-kit-signature-redo></button><button data-daisy-kit-signature-clear></button><script data-daisy-kit-config type="application/json">{"value":"${value}","required":true}</script></fieldset>`;
    const canvas = document.querySelector('canvas');
    vi.spyOn(canvas, 'getBoundingClientRect').mockReturnValue({ width: 320 });
    vi.spyOn(canvas, 'getContext').mockReturnValue({ scale: vi.fn() });
    return document.querySelector('fieldset');
}

describe('signature entry', () => {
    beforeEach(() => { pads.length = 0; });

    it('exposes a stable facade, synchronizes PNG values, and restores input on destroy', async () => {
        const element = root('data:image/png;base64,AAAA');
        const instance = mount(element);
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
        pads[0].fromDataURL.mockRejectedValueOnce(new Error('Invalid image'));
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
