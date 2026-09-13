import { describe, expect, it, vi } from 'vitest';
import { createMountable } from '../../../resources/js/core/mountable.js';

describe('mountable module contract', () => {
    it('mounts once, supports multiple roots, and destroys each instance', () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="example"><script data-daisy-kit-config type="application/json">{}</script></section>
            <section data-daisy-kit-module="example"><script data-daisy-kit-config type="application/json">{}</script></section>
        `;
        const destroyed = [];
        const module = createMountable('example', (root) => () => destroyed.push(root));
        const roots = [...document.querySelectorAll('[data-daisy-kit-module]')];
        const unmountedDetails = [];
        roots.forEach((root) => root.addEventListener('daisy-kit:example:unmounted', (event) => unmountedDetails.push(event.detail)));

        module.mountAll();
        module.mount(roots[0]);

        expect(roots.map((root) => root.dataset.daisyKitState)).toEqual(['ready', 'ready']);

        expect(roots.map((root) => module.unmount(root))).toEqual([true, true]);
        expect(module.unmount(roots[0])).toBe(false);

        expect(destroyed).toHaveLength(2);
        expect(unmountedDetails).toEqual([{}, {}]);
    });

    it('keeps a stable public facade and exposes it through getInstance', () => {
        document.body.innerHTML = '<section data-daisy-kit-module="example"><script data-daisy-kit-config type="application/json">{}</script></section>';
        const root = document.querySelector('[data-daisy-kit-module]');
        const destroy = vi.fn();
        const module = createMountable('example', () => ({ destroy, refresh: vi.fn(() => true) }));

        const instance = module.mount(root);

        expect(instance).toBe(module.mount(root));
        expect(instance).toBe(module.getInstance(root));
        expect(Object.keys(instance)).toEqual(['refresh']);
        expect(instance.destroy).toBeUndefined();
        expect(instance.refresh()).toBe(true);

        expect(module.unmount(root)).toBe(true);

        expect(destroy).toHaveBeenCalledOnce();
        expect(module.getInstance(root)).toBeNull();
    });

    it('does not register an instance when initialization returns no facade', () => {
        document.body.innerHTML = '<section data-daisy-kit-module="example"><script data-daisy-kit-config type="application/json">{}</script></section>';
        const root = document.querySelector('[data-daisy-kit-module]');
        const module = createMountable('example', () => undefined);

        expect(module.mount(root)).toBeNull();
        expect(module.getInstance(root)).toBeNull();
        expect(module.unmount(root)).toBe(false);
        expect(root.dataset.daisyKitState).toBeUndefined();
    });

    it('shows an accessible error state for invalid JSON', () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="example">
                <p data-daisy-kit-status hidden role="alert"></p>
                <script data-daisy-kit-config type="application/json">{invalid}</script>
            </section>
        `;
        const root = document.querySelector('[data-daisy-kit-module]');
        const module = createMountable('example', () => {});

        module.mount(root);

        expect(root.dataset.daisyKitState).toBe('error');
        expect(root.querySelector('[data-daisy-kit-status]').hidden).toBe(false);
    });

    it('keeps an initialization failure diagnosable without exposing it in the UI', () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="example">
                <p data-daisy-kit-status hidden role="alert"></p>
                <script data-daisy-kit-config type="application/json">{}</script>
            </section>
        `;
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:example:error', (event) => errors.push(event.detail));
        const module = createMountable('example', () => {
            throw new Error('The configured source cannot be reached.');
        });

        module.mount(root);

        expect(root.dataset.daisyKitState).toBe('error');
        expect(root.querySelector('[data-daisy-kit-status]').textContent).toBe('This module could not be initialized.');
        expect(errors).toEqual([{
            code: 'initialization-failed',
            message: 'The configured source cannot be reached.',
        }]);
    });

});
