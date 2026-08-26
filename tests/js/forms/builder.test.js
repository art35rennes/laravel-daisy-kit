import { describe, expect, it } from 'vitest';
import { mount, unmount } from '../../../resources/js/forms/builder.js';

function builderRoot(configuration, content = '') {
    document.body.innerHTML = `
        <section data-daisy-kit-module="forms-builder" data-daisy-kit-state="loading">
            <p data-daisy-kit-status role="status">Loading form builder…</p>
            ${content}
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="forms-builder"]');
}

describe('forms builder', () => {
    it('uses Livewire as the sole owner when its server-rendered authoring surface is present', () => {
        const root = builderRoot(
            { livewireAvailable: true, schema: { fields: [] } },
            '<div data-daisy-kit-livewire-builder><button type="button">Add field</button></div>',
        );

        mount(root);

        expect(root.dataset.daisyKitState).toBe('ready');
        expect(root.querySelector('[data-daisy-kit-livewire-builder]')).not.toBeNull();
        expect(root.querySelector('[data-daisy-kit-status]').hidden).toBe(true);

        unmount(root);

        expect(root.dataset.daisyKitState).toBeUndefined();
        expect(root.querySelector('[data-daisy-kit-livewire-builder]')).not.toBeNull();
    });

    it('reports the optional Livewire enhancement as unavailable instead of mounting a reduced second builder', () => {
        const root = builderRoot({ livewireAvailable: false, schema: { fields: [] } });
        const unavailable = [];
        root.addEventListener('daisy-kit:forms-builder:unavailable', (event) => unavailable.push(event.detail));

        mount(root);

        expect(root.dataset.daisyKitState).toBe('empty');
        expect(root.querySelector('[data-daisy-kit-status]').textContent).toContain('requires optional Livewire 4');
        expect(root.querySelector('[data-daisy-kit-forms-builder-content]')).toBeNull();
        expect(unavailable).toEqual([{ reason: 'livewire-unavailable' }]);
    });

    it('keeps a diagnostic error for a broken Livewire render rather than silently falling back', () => {
        const root = builderRoot({ livewireAvailable: true, schema: { fields: [] } });

        mount(root);

        expect(root.dataset.daisyKitState).toBe('error');
        expect(root.querySelector('[data-daisy-kit-status]').textContent).toContain('Livewire form builder mount point');
    });

    it('keeps invalid configuration as an explicit error', () => {
        document.body.innerHTML = '<section data-daisy-kit-module="forms-builder"><p data-daisy-kit-status role="status"></p><script data-daisy-kit-config type="application/json">{invalid</script></section>';
        const root = document.querySelector('[data-daisy-kit-module="forms-builder"]');

        mount(root);

        expect(root.dataset.daisyKitState).toBe('error');
    });
});
