import { describe, expect, it } from 'vitest';
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
});
