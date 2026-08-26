import { afterEach, describe, expect, it, vi } from 'vitest';
import { installLivewireAdapter, mount, mountAll, unmount } from '../../../resources/js/forms/viewer.js';
import canonicalProfileWizard from '../../Fixtures/forms/canonical-v4-profile-wizard.json';

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

afterEach(() => {
    vi.unstubAllGlobals();
});

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
        expect(input.classList.contains('input')).toBe(true);
        expect(input.classList.contains('input-bordered')).toBe(true);
        expect(root.querySelector('[data-daisy-kit-forms-actions] button[type="submit"]').classList.contains('btn-primary')).toBe(true);
        expect(input.required).toBe(true);
        expect(root.dataset.daisyKitState).toBe('ready');
        expect(changes).toEqual([{ name: 'name', value: 'Grace', values: { name: 'Grace' } }]);

        unmount(root);
        expect(root.dataset.daisyKitState).toBeUndefined();
    });

    it('normalizes Laravel boolean values for checkbox and toggle controls', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [
                    { name: 'terms', type: 'checkbox', rules: ['accepted'] },
                    { name: 'newsletter', type: 'toggle' },
                ],
            },
            value: { terms: '1', newsletter: 1 },
        }));

        await mount(root);

        expect(root.querySelector('input[name="terms"]').checked).toBe(true);
        expect(root.querySelector('input[name="newsletter"]').checked).toBe(true);
    });

    it('mounts on an HTTP host that does not provide crypto.randomUUID', async () => {
        vi.stubGlobal('crypto', {});
        const root = viewerRoot(JSON.stringify({
            schema: { fields: [{ name: 'name', label: 'Name', type: 'text' }] },
            value: { name: 'Ada' },
        }));

        mount(root);
        await settle();

        expect(root.dataset.daisyKitState).toBe('ready');
        expect(root.dataset.daisyKitFormsInstance).toMatch(/^daisy-kit-forms-/);
        expect(root.querySelector('input[name="name"]')).not.toBeNull();
    });

    it('returns an instance-local runtime API that reads values, validates, and destroys only its own viewer', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [
                    { name: 'email', label: 'Email', type: 'email', rules: ['required', 'email'] },
                    { name: 'newsletter', label: 'Newsletter', type: 'checkbox' },
                ],
            },
            value: { email: '', newsletter: false },
        }));

        const runtime = mount(root);
        const repeatedMount = mount(root);
        await settle();

        expect(runtime).toEqual(expect.objectContaining({
            destroy: expect.any(Function),
            getValue: expect.any(Function),
            validate: expect.any(Function),
        }));
        expect(repeatedMount).toBe(runtime);
        expect(runtime.getValue()).toEqual({ email: '', newsletter: false });
        expect(runtime.validate()).toBe(false);

        const email = root.querySelector('input[name="email"]');
        const newsletter = root.querySelector('input[name="newsletter"]');
        email.value = 'ada@example.test';
        email.dispatchEvent(new Event('input', { bubbles: true }));
        newsletter.checked = true;
        newsletter.dispatchEvent(new Event('change', { bubbles: true }));
        await settle();

        expect(runtime.getValue()).toEqual({ email: 'ada@example.test', newsletter: true });
        expect(runtime.validate()).toBe(true);

        runtime.destroy();

        expect(root.dataset.daisyKitState).toBeUndefined();
        expect(root.querySelector('[data-daisy-kit-forms-content]').children).toHaveLength(0);
    });

    it('evaluates JSONata visibility expressions independently for each root', async () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="forms-viewer"><p data-daisy-kit-status role="status"></p><form data-daisy-kit-forms-content></form><script data-daisy-kit-config type="application/json">{"schema":{"fields":[{"name":"name","label":"Name","type":"text"},{"name":"advanced","label":"Advanced","type":"text","visibleWhen":{"type":"jsonata","expression":"enabled = true"}}]},"value":{"enabled":true}}</script></section>
            <section data-daisy-kit-module="forms-viewer"><p data-daisy-kit-status role="status"></p><form data-daisy-kit-forms-content></form><script data-daisy-kit-config type="application/json">{"schema":{"fields":[{"name":"name","label":"Name","type":"text"},{"name":"advanced","label":"Advanced","type":"text","visibleWhen":{"type":"jsonata","expression":"enabled = true"}}]},"value":{"enabled":false}}</script></section>
        `;

        const roots = [...document.querySelectorAll('[data-daisy-kit-module="forms-viewer"]')];
        await Promise.all(mountAll());
        await settle();

        expect(roots[0].querySelector('[name="advanced"]').closest('[data-daisy-kit-forms-field]').hidden).toBe(false);
        expect(roots[1].querySelector('[name="advanced"]').closest('[data-daisy-kit-forms-field]').hidden).toBe(true);
    });

    it('accepts the Builder JSONata descriptor for visible and computed fields', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [
                    { name: 'enabled', label: 'Enabled', type: 'checkbox' },
                    {
                        name: 'advanced',
                        label: 'Advanced',
                        type: 'text',
                        visibleWhen: { type: 'jsonata', expression: 'enabled = true' },
                    },
                    { name: 'quantity', label: 'Quantity', type: 'number' },
                    { name: 'unit_price', label: 'Unit price', type: 'number' },
                    {
                        name: 'total',
                        label: 'Total',
                        type: 'number',
                        computed: { type: 'jsonata', expression: 'quantity * unit_price' },
                    },
                ],
            },
            value: { enabled: false, quantity: 2, unit_price: 12 },
        }));

        await mount(root);
        await settle();

        expect(root.querySelector('[name="advanced"]').closest('[data-daisy-kit-forms-field]').hidden).toBe(true);
        expect(root.querySelector('[name="total"]').value).toBe('24');

        const enabled = root.querySelector('input[name="enabled"]');
        enabled.checked = true;
        enabled.dispatchEvent(new Event('change', { bubbles: true }));
        await settle();

        expect(root.querySelector('[name="advanced"]').closest('[data-daisy-kit-forms-field]').hidden).toBe(false);
    });

    it('does not evaluate legacy string JSONata expressions', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [{
                    name: 'advanced',
                    label: 'Advanced',
                    type: 'text',
                    visibleWhen: 'enabled = true',
                }],
            },
            value: { enabled: false },
        }));

        await mount(root);
        await settle();

        expect(root.querySelector('[name="advanced"]').closest('[data-daisy-kit-forms-field]').hidden).toBe(false);
    });

    it('excludes conditionally hidden sections and future wizard steps from validation and FormData', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [
                    { name: 'advanced', label: 'Enable advanced', type: 'checkbox' },
                    {
                        id: 'advanced-section',
                        type: 'section',
                        label: 'Advanced',
                        visibleWhen: { type: 'jsonata', expression: 'advanced = true' },
                        fields: [{ name: 'advanced_note', label: 'Advanced note', type: 'text', rules: ['required'] }],
                    },
                    {
                        id: 'account',
                        type: 'wizardStep',
                        label: 'Account',
                        fields: [{ name: 'email', label: 'Email', type: 'email', rules: ['required', 'email'] }],
                    },
                    {
                        id: 'profile',
                        type: 'wizardStep',
                        label: 'Profile',
                        fields: [{ name: 'phone', label: 'Phone', type: 'tel', rules: ['required'] }],
                    },
                ],
            },
            value: { advanced: false, email: 'ada@example.test' },
        }));

        await mount(root);
        await settle();

        const form = root.querySelector('form');
        const advanced = root.querySelector('input[name="advanced"]');
        const advancedNote = root.querySelector('input[name="advanced_note"]');
        const email = root.querySelector('input[name="email"]');
        const phone = root.querySelector('input[name="phone"]');

        expect(advancedNote.disabled).toBe(true);
        expect(advancedNote.required).toBe(false);
        expect(phone.disabled).toBe(true);
        expect(phone.required).toBe(false);
        expect(form.checkValidity()).toBe(true);
        expect([...new FormData(form).keys()]).toEqual(['email']);

        advanced.checked = true;
        advanced.dispatchEvent(new Event('change', { bubbles: true }));
        await settle();

        expect(advancedNote.disabled).toBe(false);
        expect(advancedNote.required).toBe(true);
        expect(form.checkValidity()).toBe(false);

        advancedNote.value = 'Required only while visible';
        advancedNote.dispatchEvent(new Event('input', { bubbles: true }));
        await settle();
        root.querySelector('[data-daisy-kit-forms-next]').click();
        await settle();

        expect(email.disabled).toBe(true);
        expect(phone.disabled).toBe(false);
        expect(phone.required).toBe(true);
        expect([...new FormData(form).keys()]).toEqual(['advanced', 'advanced_note', 'phone']);
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

    it('renders canonical nested sections and steps with safe controls, errors, readonly fields, and event submission', async () => {
        const root = viewerRoot(JSON.stringify(canonicalProfileWizard));
        const submitted = [];
        const errors = [];
        root.addEventListener('daisy-kit:forms-viewer:submitted', (event) => submitted.push(event.detail));
        root.addEventListener('daisy-kit:forms-viewer:error', (event) => errors.push(event.detail));

        await mount(root);
        await settle();

        const form = root.querySelector('form');
        const email = root.querySelector('input[name="email"]');
        const displayName = root.querySelector('input[name="display_name"]');
        const legacyValue = root.querySelector('input[name="legacy_value"]');
        const steps = root.querySelectorAll('[data-daisy-kit-forms-step]');

        expect(root.querySelector('fieldset[data-daisy-kit-forms-section="profile"]')).not.toBeNull();
        expect(displayName.readOnly).toBe(true);
        expect(email.getAttribute('aria-invalid')).toBe('true');
        expect(root.querySelector('[data-daisy-kit-forms-error="email"]').textContent).toBe('This address is already used.');
        expect(legacyValue).toBeNull();
        expect(root.querySelector('[data-daisy-kit-forms-type-error="legacy_value"]').textContent).toContain('unsafe-legacy-type');
        expect(errors).toContainEqual({
            field: 'legacy_value',
            reason: 'unsupported-type',
            type: 'unsafe-legacy-type',
        });
        expect(steps).toHaveLength(2);
        expect(steps[0].hidden).toBe(false);
        expect(steps[1].hidden).toBe(true);

        root.querySelector('[data-daisy-kit-forms-next]').click();
        await settle();
        expect(steps[0].hidden).toBe(true);
        expect(steps[1].hidden).toBe(false);

        const submission = new Event('submit', { bubbles: true, cancelable: true });
        form.dispatchEvent(submission);

        expect(submission.defaultPrevented).toBe(true);
        expect(submitted).toEqual([{
            values: canonicalProfileWizard.value,
            mode: 'event',
        }]);
    });

    it('renders static content without a false input and keeps unknown types unavailable', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [
                    { type: 'staticText', text: 'This profile is reviewed by the account team.' },
                    { name: 'legacy', type: 'signature', label: 'Legacy signature' },
                ],
            },
        }));

        await mount(root);
        await settle();

        expect(root.querySelector('[data-daisy-kit-forms-static-text]').textContent).toContain('reviewed');
        expect(root.querySelector('input[name="legacy"]')).toBeNull();
        expect(root.querySelector('[data-daisy-kit-forms-type-error="legacy"]').getAttribute('role')).toBe('alert');
    });

    it('keeps HTML submission native and prevents submission for readonly and none modes', async () => {
        const htmlRoot = viewerRoot(JSON.stringify({
            schema: { submit: { mode: 'html' }, fields: [{ name: 'name', type: 'text' }] },
            value: { name: 'Ada' },
        }));
        const htmlSubmissions = [];
        htmlRoot.addEventListener('daisy-kit:forms-viewer:submitted', (event) => htmlSubmissions.push(event.detail));

        await mount(htmlRoot);
        const htmlSubmission = new Event('submit', { bubbles: true, cancelable: true });
        htmlRoot.querySelector('form').dispatchEvent(htmlSubmission);

        expect(htmlSubmission.defaultPrevented).toBe(false);
        expect(htmlSubmissions).toEqual([]);

        unmount(htmlRoot);

        const noneRoot = viewerRoot(JSON.stringify({
            schema: { fields: [{ name: 'name', type: 'text' }] },
            submitMode: 'none',
            readonly: true,
            value: { name: 'Ada' },
        }));

        await mount(noneRoot);
        const noneSubmission = new Event('submit', { bubbles: true, cancelable: true });
        noneRoot.querySelector('form').dispatchEvent(noneSubmission);

        expect(noneRoot.querySelector('input[name="name"]').readOnly).toBe(true);
        expect(noneRoot.querySelector('button')).toBeNull();
        expect(noneSubmission.defaultPrevented).toBe(true);
    });

    it('submits a valid fetch form as multipart data and reports native validation errors first', async () => {
        const fetch = vi.fn().mockResolvedValue({ ok: true, status: 202 });
        vi.stubGlobal('fetch', fetch);
        const root = viewerRoot(JSON.stringify({
            schema: {
                submit: { mode: 'fetch', action: '/profiles', method: 'PATCH' },
                fields: [
                    { name: 'email', type: 'email', required: true },
                    { name: 'attachment', type: 'file' },
                ],
            },
            value: { email: 'ada@example.test' },
        }));
        const submitted = [];
        const errors = [];
        root.addEventListener('daisy-kit:forms-viewer:submitted', (event) => submitted.push(event.detail));
        root.addEventListener('daisy-kit:forms-viewer:error', (event) => errors.push(event.detail));

        await mount(root);
        const form = root.querySelector('form');
        const fileInput = root.querySelector('input[name="attachment"]');
        const file = new File(['report'], 'report.txt', { type: 'text/plain' });
        Object.defineProperty(fileInput, 'files', { value: [file] });

        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        await settle();

        expect(fetch).toHaveBeenCalledWith('/profiles', expect.objectContaining({
            body: expect.any(FormData),
            method: 'PATCH',
        }));
        expect(submitted).toEqual([expect.objectContaining({ mode: 'fetch', status: 202 })]);

        unmount(root);
        const invalidRoot = viewerRoot(JSON.stringify({
            schema: { submit: { mode: 'fetch', action: '/profiles' }, fields: [{ name: 'email', type: 'email', required: true }] },
        }));
        invalidRoot.addEventListener('daisy-kit:forms-viewer:error', (event) => errors.push(event.detail));
        await mount(invalidRoot);
        invalidRoot.querySelector('form').dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        await settle();

        expect(fetch).toHaveBeenCalledTimes(1);
        expect(errors).toContainEqual({ reason: 'validation-failed' });
    });

    it('updates readonly computed fields from JSONata expressions', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [
                    { name: 'quantity', type: 'number' },
                    { name: 'unit_price', type: 'number' },
                    { name: 'total', type: 'number', computed: { type: 'jsonata', expression: 'quantity * unit_price' } },
                ],
            },
            value: { quantity: 2, unit_price: 12 },
        }));

        await mount(root);
        await settle();
        const total = root.querySelector('input[name="total"]');

        expect(total.value).toBe('24');
        expect(total.readOnly).toBe(true);

        const quantity = root.querySelector('input[name="quantity"]');
        quantity.value = '3';
        quantity.dispatchEvent(new Event('input', { bubbles: true }));
        await settle();

        expect(total.value).toBe('36');
    });

    it('applies safe schema attributes and Laravel-style rules before advancing a wizard step', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: {
                fields: [
                    {
                        id: 'account',
                        type: 'wizardStep',
                        label: 'Account',
                        fields: [{
                            name: 'email',
                            type: 'email',
                            label: 'Email',
                            attrs: { autocomplete: 'email', placeholder: 'name@example.test', maxlength: 120 },
                            rules: ['required', 'email', 'max:120'],
                            ui: { width: '1/2' },
                        }],
                    },
                    {
                        id: 'profile',
                        type: 'wizardStep',
                        label: 'Profile',
                        fields: [{ name: 'phone', type: 'tel', label: 'Phone', attrs: { minlength: 8 } }],
                    },
                ],
            },
            value: { email: '' },
        }));
        const errors = [];
        root.addEventListener('daisy-kit:forms-viewer:error', (event) => errors.push(event.detail));

        await mount(root);
        await settle();
        const email = root.querySelector('input[name="email"]');
        const steps = root.querySelectorAll('[data-daisy-kit-forms-step]');

        expect(email.required).toBe(true);
        expect(email.type).toBe('email');
        expect(email.autocomplete).toBe('email');
        expect(email.placeholder).toBe('name@example.test');
        expect(email.maxLength).toBe(120);
        expect(email.closest('[data-daisy-kit-forms-field]').classList).toContain('daisy-kit-forms-field--span-half');

        root.querySelector('[data-daisy-kit-forms-next]').click();
        await settle();

        expect(steps[0].hidden).toBe(false);
        expect(steps[1].hidden).toBe(true);
        expect(errors).toContainEqual({ reason: 'validation-failed', step: 0 });

        email.value = 'ada@example.test';
        email.dispatchEvent(new Event('input', { bubbles: true }));
        root.querySelector('[data-daisy-kit-forms-next]').click();
        await settle();

        expect(steps[0].hidden).toBe(true);
        expect(steps[1].hidden).toBe(false);
    });

    it('reports a progressive validation diagnostic when configured to validate on input', async () => {
        const root = viewerRoot(JSON.stringify({
            schema: { fields: [{ name: 'email', type: 'email', rules: ['required', 'email'] }] },
            validateOn: 'input',
        }));
        const errors = [];
        root.addEventListener('daisy-kit:forms-viewer:error', (event) => errors.push(event.detail));

        await mount(root);
        const email = root.querySelector('input[name="email"]');
        email.value = 'not-an-email';
        email.dispatchEvent(new Event('input', { bubbles: true }));

        expect(errors).toContainEqual({ field: 'email', reason: 'validation-failed' });
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
