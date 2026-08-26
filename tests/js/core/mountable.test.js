import { describe, expect, it, vi } from 'vitest';
import { createMountable, installLivewireAdapter } from '../../../resources/js/core/mountable.js';

describe('mountable module contract', () => {
    it('mounts once, supports multiple roots, and destroys each instance', () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="example"><script data-daisy-kit-config type="application/json">{}</script></section>
            <section data-daisy-kit-module="example"><script data-daisy-kit-config type="application/json">{}</script></section>
        `;
        const destroyed = [];
        const module = createMountable('example', (root) => () => destroyed.push(root));
        const roots = [...document.querySelectorAll('[data-daisy-kit-module]')];

        module.mountAll();
        module.mount(roots[0]);

        expect(roots.map((root) => root.dataset.daisyKitState)).toEqual(['ready', 'ready']);

        roots.forEach((root) => module.unmount(root));

        expect(destroyed).toHaveLength(2);
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

    it('destroys orphaned Livewire roots for every module before mounting each replacement once', () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="first"><script data-daisy-kit-config type="application/json">{}</script></section>
            <section data-daisy-kit-module="second"><script data-daisy-kit-config type="application/json">{}</script></section>
        `;
        const [firstRoot, secondRoot] = [...document.querySelectorAll('[data-daisy-kit-module]')];
        const firstUnmount = vi.fn();
        const secondUnmount = vi.fn();
        const firstMountAll = vi.fn();
        const secondMountAll = vi.fn();
        const detachFirst = installLivewireAdapter('first', firstMountAll, firstUnmount);
        const detachSecond = installLivewireAdapter('second', secondMountAll, secondUnmount);

        document.body.innerHTML = `
            <section data-daisy-kit-module="first"><script data-daisy-kit-config type="application/json">{}</script></section>
            <section data-daisy-kit-module="second"><script data-daisy-kit-config type="application/json">{}</script></section>
        `;
        expect(document.querySelector('[data-daisy-kit-module="first"]')).not.toBe(firstRoot);
        expect(document.querySelector('[data-daisy-kit-module="second"]')).not.toBe(secondRoot);
        document.dispatchEvent(new Event('livewire:navigated'));
        detachFirst();
        detachSecond();

        expect(firstUnmount).toHaveBeenCalledTimes(1);
        expect(secondUnmount).toHaveBeenCalledTimes(1);
        expect(firstUnmount.mock.calls[0][0]).toBe(firstRoot);
        expect(secondUnmount.mock.calls[0][0]).toBe(secondRoot);
        expect(firstMountAll).toHaveBeenCalledExactlyOnceWith(document);
        expect(secondMountAll).toHaveBeenCalledExactlyOnceWith(document);
    });
});
