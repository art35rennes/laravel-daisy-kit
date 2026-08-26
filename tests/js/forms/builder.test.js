import { describe, expect, it } from 'vitest';
import { mount, unmount } from '../../../resources/js/forms/builder.js';

async function settle() {
    await Promise.resolve();
}

function builderRoot(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="forms-builder" data-daisy-kit-state="loading">
            <p data-daisy-kit-status role="status">Loading form builder…</p>
            <div data-daisy-kit-forms-builder-content></div>
            <script data-daisy-kit-config type="application/json">${configuration}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="forms-builder"]');
}

describe('forms builder', () => {
    it('updates a schema locally and cleans listeners on unmount', async () => {
        const root = builderRoot(JSON.stringify({ schema: { fields: [{ name: 'email', label: 'Email', type: 'email' }] } }));
        const updates = [];
        root.addEventListener('daisy-kit:forms-builder:changed', (event) => updates.push(event.detail));

        mount(root);
        await settle();
        const label = root.querySelector('[data-daisy-kit-builder-label]');
        label.value = 'Email address';
        label.dispatchEvent(new Event('input', { bubbles: true }));

        expect(root.dataset.daisyKitState).toBe('ready');
        expect(updates).toEqual([{ schema: { fields: [{ name: 'email', label: 'Email address', type: 'email' }] } }]);

        unmount(root);
        label.value = 'Ignored';
        label.dispatchEvent(new Event('input', { bubbles: true }));

        expect(updates).toHaveLength(1);
    });

    it('shows empty and invalid-configuration states', async () => {
        const emptyRoot = builderRoot(JSON.stringify({ schema: { fields: [] } }));
        mount(emptyRoot);
        await settle();
        expect(emptyRoot.dataset.daisyKitState).toBe('empty');

        const invalidRoot = builderRoot('{invalid');
        mount(invalidRoot);
        expect(invalidRoot.dataset.daisyKitState).toBe('error');
    });

    it('does not render or emit after unmount', async () => {
        const root = builderRoot(JSON.stringify({ schema: { fields: [] } }));
        const updates = [];
        root.addEventListener('daisy-kit:forms-builder:changed', (event) => updates.push(event.detail));

        mount(root);
        unmount(root);
        await settle();

        expect(root.dataset.daisyKitState).toBeUndefined();
        expect(root.querySelector('[data-daisy-kit-forms-builder-content]').children).toHaveLength(0);

        mount(root);
        await settle();
        const detachedButton = root.querySelector('button');
        unmount(root);
        detachedButton.click();

        expect(updates).toEqual([]);
        expect(root.querySelector('[data-daisy-kit-forms-builder-content]').children).toHaveLength(0);
    });
});
