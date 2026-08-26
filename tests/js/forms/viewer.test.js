import { describe, expect, it } from 'vitest';
import { installLivewireAdapter, mount, mountAll, unmount } from '../../../resources/js/forms/viewer.js';

async function settle() {
    await Promise.resolve();
    await Promise.resolve();
    await Promise.resolve();
    await Promise.resolve();
    await new Promise((resolve) => setTimeout(resolve, 0));
}

function viewerRoot(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="forms-viewer" data-daisy-kit-state="loading">
            <p data-daisy-kit-status role="status">Loading form…</p>
            <form data-daisy-kit-forms-content></form>
            <script data-daisy-kit-config type="application/json">${configuration}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="forms-viewer"]');
}

describe('forms viewer', () => {
    it('renders a semantic field and emits changes without duplicate mounts', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: { fields: [{ name: 'name', label: 'Name', type: 'text', required: true }] },
            value: { name: 'Ada' },
        }));
        const changes = [];
        root.addEventListener('daisy-kit:forms-viewer:changed', (event) => changes.push(event.detail));

        await mount(root);
        await mount(root);
        await settle();
        const input = root.querySelector('input[name="name"]');
        input.value = 'Grace';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        await settle();

        expect(root.querySelectorAll('label')).toHaveLength(1);
        expect(input.required).toBe(true);
        expect(root.dataset.daisyKitState).toBe('ready');
        expect(changes).toEqual([{ name: 'name', value: 'Grace', values: { name: 'Grace' } }]);

        unmount(root);
        expect(root.dataset.daisyKitState).toBeUndefined();
    });

    it('evaluates JSONata visibility expressions independently for each root', async () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="forms-viewer"><p data-daisy-kit-status role="status"></p><form data-daisy-kit-forms-content></form><script data-daisy-kit-config type="application/json">{"schema":{"fields":[{"name":"name","label":"Name","type":"text"},{"name":"advanced","label":"Advanced","type":"text","visibleWhen":"enabled = true"}]},"value":{"enabled":true}}</script></section>
            <section data-daisy-kit-module="forms-viewer"><p data-daisy-kit-status role="status"></p><form data-daisy-kit-forms-content></form><script data-daisy-kit-config type="application/json">{"schema":{"fields":[{"name":"name","label":"Name","type":"text"},{"name":"advanced","label":"Advanced","type":"text","visibleWhen":"enabled = true"}]},"value":{"enabled":false}}</script></section>
        `;

        const roots = [...document.querySelectorAll('[data-daisy-kit-module="forms-viewer"]')];
        await Promise.all(mountAll());
        await settle();

        expect(roots[0].querySelector('[name="advanced"]').closest('[data-daisy-kit-forms-field]').hidden).toBe(false);
        expect(roots[1].querySelector('[name="advanced"]').closest('[data-daisy-kit-forms-field]').hidden).toBe(true);
    });

    it('shows an accessible empty state and an error for invalid JSON', async () => {
        const emptyRoot = viewerRoot(JSON.stringify({ schema: { fields: [] }, value: {} }));
        await mount(emptyRoot);
        await settle();

        expect(emptyRoot.dataset.daisyKitState).toBe('empty');
        expect(emptyRoot.querySelector('[data-daisy-kit-status]').textContent).toBe('No form fields are available.');

        const invalidRoot = viewerRoot('{invalid');
        mount(invalidRoot);

        expect(invalidRoot.dataset.daisyKitState).toBe('error');
        expect(invalidRoot.querySelector('[data-daisy-kit-status]').hidden).toBe(false);
    });

    it('remounts explicitly when the optional Livewire adapter receives a navigation event', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: { fields: [{ name: 'name', label: 'Name', type: 'text' }] },
            value: { name: 'Ada' },
        }));
        const mounted = [];
        root.addEventListener('daisy-kit:forms-viewer:mounted', () => mounted.push(true));

        mount(root);
        await settle();
        const detach = installLivewireAdapter();
        document.dispatchEvent(new Event('livewire:navigated'));
        await settle();
        detach();

        expect(mounted).toHaveLength(2);
        expect(root.querySelector('[name="name"]')).not.toBeNull();
    });
});
