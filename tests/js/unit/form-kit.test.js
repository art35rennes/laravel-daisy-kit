/** @vitest-environment jsdom */

import { describe, expect, it, vi } from 'vitest';
import { evaluateExpression, registerFunction } from '../../../resources/js/form-kit/jsonata-engine.js';
import { createFormRuntime } from '../../../resources/js/form-kit/runtime.js';
import { createDefaultSchema, validateSchema } from '../../../resources/js/form-kit/schema.js';
import initColorPicker from '../../../resources/js/color-picker.js';
import initFormBuilder from '../../../resources/js/modules/form-builder.js';
import initFormViewer from '../../../resources/js/modules/form-viewer.js';

async function tick() {
    await new Promise((resolve) => setTimeout(resolve, 0));
    await new Promise((resolve) => setTimeout(resolve, 0));
}

async function frame() {
    await new Promise((resolve) => {
        if (typeof window.requestAnimationFrame === 'function') {
            window.requestAnimationFrame(resolve);

            return;
        }

        setTimeout(resolve, 0);
    });
}

describe('form-kit schema', () => {
    it('canonicalizes and validates a v1 schema', () => {
        const result = validateSchema(createDefaultSchema());

        expect(result.valid).toBe(true);
        expect(result.schema.version).toBe('1.0');
    });

    it('accepts common native input field types', () => {
        const result = validateSchema({
            version: '1.0',
            id: 'contact',
            fields: [
                { id: 'phone', type: 'tel', name: 'phone' },
                { id: 'website', type: 'url', name: 'website' },
                { id: 'starts_at', type: 'datetime-local', name: 'starts_at' },
                { id: 'starts_time', type: 'time', name: 'starts_time' },
                { id: 'billing_month', type: 'month', name: 'billing_month' },
                { id: 'brand_color', type: 'color', name: 'brand_color' },
            ],
        });

        expect(result.valid).toBe(true);
    });

    it('uses canonical non-submitting field taxonomy during schema validation', () => {
        const contentResult = validateSchema({
            version: '1.0',
            id: 'content',
            fields: [
                { id: 'copy', type: 'staticText', text: 'Read first.' },
                { id: 'group', type: 'section', fields: [] },
            ],
        });

        expect(contentResult.valid).toBe(true);

        const invalidResult = validateSchema({
            version: '1.0',
            id: 'content',
            fields: [
                { id: 'email', type: 'email', name: '', label: 'Email' },
            ],
        });

        expect(invalidResult.valid).toBe(false);
        expect(invalidResult.errors.map((error) => error.code)).toContain('invalid_name');
    });

    it('rejects unsupported versions and dependency cycles', () => {
        const result = validateSchema({
            version: '2.0',
            id: 'bad',
            fields: [
                {
                    id: 'a',
                    type: 'text',
                    name: 'a',
                    computed: { type: 'jsonata', expression: 'values.b', dependsOn: ['b'] },
                },
                {
                    id: 'b',
                    type: 'text',
                    name: 'b',
                    computed: { type: 'jsonata', expression: 'values.a', dependsOn: ['a'] },
                },
            ],
        });

        expect(result.valid).toBe(false);
        expect(result.errors.map((error) => error.code)).toContain('unsupported_version');
        expect(result.errors.map((error) => error.code)).toContain('dependency_cycle');
    });
});

describe('form-kit JSONata runtime', () => {
    it('registers custom functions and evaluates expressions', async () => {
        registerFunction('$double', (value) => value * 2, {
            name: '$double',
            signature: '<n:n>',
        });

        const result = await evaluateExpression('$double(values.quantity)', {
            values: { quantity: 4 },
        });

        expect(result.ok).toBe(true);
        expect(result.value).toBe(8);
    });

    it('normalizes unknown function errors', async () => {
        const result = await evaluateExpression('$missingFunction()', {});

        expect(result.ok).toBe(false);
        expect(result.error.code).toBe('unknown_function');
    });
});

describe('form-kit viewer runtime', () => {
    it('hydrates the viewer bridge from embedded schema value and errors JSON', async () => {
        document.body.innerHTML = `
            <form id="signup-viewer" data-form-id="signup-viewer" data-module="form-viewer" data-submit-mode="none" data-validate-on="change" data-readonly="true" action="/forms" method="POST">
                <div data-form-field="email">
                    <input data-form-input="email" name="email" value="ada@example.com" />
                    <p data-form-errors="email" class="hidden"></p>
                </div>
                <script type="application/json" data-form-schema>
                    {"version":"1.0","id":"signup","fields":[{"id":"email","type":"email","name":"email","label":"Email","rules":["required","email"]}]}
                </script>
                <script type="application/json" data-form-value>{"email":"ada@example.com"}</script>
                <script type="application/json" data-form-errors-payload>{"email":["Already used."]}</script>
            </form>
        `;

        const root = document.querySelector('[data-module="form-viewer"]');
        const runtime = initFormViewer(root);

        await tick();

        expect(root.__daisyFormRuntime).toBe(runtime);
        expect(window.DaisyFormViewer.get('signup-viewer')).toBe(runtime);
        expect(window.DaisyFormViewer.getByElement(root)).toBe(runtime);
        expect(runtime.state.schema.id).toBe('signup');
        expect(runtime.state.values.email).toBe('ada@example.com');
        expect(runtime.state.errors.email).toEqual(['Already used.']);
        expect(root.dataset.formRuntimeState).toBe('ready');
        expect(root.dataset.formSubmitMode).toBe('none');
        expect(root.dataset.formValidateOn).toBe('change');
        expect(root.dataset.formReadonly).toBe('true');
        expect(runtime.getSubmitMode()).toBe('none');
        expect(runtime.getValidateOn()).toBe('change');
        expect(runtime.isReadonly()).toBe(true);
        expect(await runtime.submit()).toBe(true);
    });

    it('exposes a public viewer API for host integrations', async () => {
        document.body.innerHTML = `
            <form id="profile-viewer" data-form-id="profile-viewer">
                <div data-form-field="name">
                    <input data-form-input="name" name="name" value="Ada" />
                    <p data-form-errors="name" class="hidden"></p>
                </div>
                <button type="submit" data-form-submit>Submit</button>
            </form>
        `;

        const root = document.querySelector('form');
        const changes = [];
        const submissions = [];
        const readyEvents = [];
        root.addEventListener('daisy-form:change', (event) => changes.push(event.detail));
        root.addEventListener('daisy-form:submit', (event) => submissions.push(event.detail));

        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'profile',
                fields: [{ id: 'name', type: 'text', name: 'name', label: 'Name', rules: ['required'] }],
            },
            submitMode: 'event',
        });
        const unsubscribeReady = runtime.on('daisy-form:ready', (event) => readyEvents.push(event.detail));

        await tick();

        expect(runtime.id).toBe('profile-viewer');
        expect(runtime.getSubmitMode()).toBe('event');
        expect(runtime.getValidateOn()).toBe('submit');
        expect(runtime.isReadonly()).toBe(false);
        expect(runtime.isValid()).toBe(true);
        expect(runtime.getSchema().id).toBe('profile');
        expect(runtime.getField('name').id).toBe('name');
        expect(runtime.getVisibleFields().map((field) => field.id)).toEqual(['name']);
        expect(runtime.getInput('name')).toBe(root.querySelector('[name="name"]'));
        expect(runtime.getValue('name')).toBe('Ada');
        expect(root.dataset.formRuntimeState).toBe('ready');
        expect(readyEvents.at(-1).id).toBe('profile-viewer');
        unsubscribeReady();

        await runtime.setValue('name', 'Grace');

        expect(root.querySelector('[name="name"]').value).toBe('Grace');
        expect(runtime.getValues()).toEqual({ name: 'Grace' });
        expect(runtime.getValues({ visible: true })).toEqual({ name: 'Grace' });
        expect(changes.at(-1).id).toBe('profile-viewer');
        expect(changes.at(-1).runtime).toBe(runtime);

        runtime.setErrors({ name: ['Required.'] });
        expect(runtime.getErrors().name).toEqual(['Required.']);
        expect(root.querySelector('[data-form-errors="name"]').textContent).toBe('Required.');
        runtime.clearErrors();
        expect(runtime.getErrors()).toEqual({});

        await runtime.submit();
        expect(submissions.at(-1).id).toBe('profile-viewer');
        expect(submissions.at(-1).values).toEqual({ name: 'Grace' });

        await runtime.reset({ name: 'Ada' });
        expect(runtime.getValue('name')).toBe('Ada');

        runtime.destroy();
        expect(root.dataset.formRuntimeState).toBe('destroyed');
    });

    it('keeps static text visible without treating it as submitted viewer data', async () => {
        document.body.innerHTML = `
            <form id="content-viewer" data-form-id="content-viewer">
                <fieldset data-form-field="project">Project details.</fieldset>
                <div data-form-field="intro">Read before continuing.</div>
                <div data-form-field="email">
                    <input data-form-input="email" name="email" value="hide" />
                    <p data-form-errors="email" class="hidden"></p>
                </div>
                <button type="submit" data-form-submit>Submit</button>
            </form>
        `;

        const root = document.querySelector('form');
        const submissions = [];
        root.addEventListener('daisy-form:submit', (event) => submissions.push(event.detail.values));

        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'content',
                fields: [
                    {
                        id: 'project',
                        type: 'section',
                        label: 'Project',
                        visibleWhen: { type: 'jsonata', expression: "values.email = 'show'", dependsOn: ['email'] },
                        fields: [],
                    },
                    {
                        id: 'intro',
                        type: 'staticText',
                        text: 'Read before continuing.',
                        visibleWhen: { type: 'jsonata', expression: "values.email = 'show'", dependsOn: ['email'] },
                    },
                    { id: 'email', type: 'email', name: 'email', label: 'Email' },
                ],
            },
            submitMode: 'event',
        });

        await tick();

        expect(runtime.getValues()).toEqual({ email: 'hide' });
        expect(runtime.serialize()).toEqual({ email: 'hide' });
        expect(runtime.getVisibleFields().map((field) => field.id)).toEqual(['email']);
        expect(root.querySelector('[data-form-field="project"]').classList.contains('hidden')).toBe(true);
        expect(root.querySelector('[data-form-field="intro"]').classList.contains('hidden')).toBe(true);

        await runtime.setValue('email', 'show');

        expect(runtime.getValues()).toEqual({ email: 'show' });
        expect(runtime.serialize()).toEqual({ email: 'show' });
        expect(runtime.getVisibleFields().map((field) => field.id)).toEqual(['intro', 'email']);
        expect(root.querySelector('[data-form-field="project"]').classList.contains('hidden')).toBe(false);
        expect(root.querySelector('[data-form-field="intro"]').classList.contains('hidden')).toBe(false);

        await runtime.submit();

        expect(submissions.at(-1)).toEqual({ email: 'show' });
    });

    it('unregisters viewer bridge instances when destroyed', async () => {
        document.body.innerHTML = `
            <form id="destroyable-viewer" data-form-id="destroyable-viewer" data-module="form-viewer" data-submit-mode="none">
                <div data-form-field="name">
                    <input data-form-input="name" name="name" value="Ada" />
                    <p data-form-errors="name" class="hidden"></p>
                </div>
                <script type="application/json" data-form-schema>
                    {"version":"1.0","id":"profile","fields":[{"id":"name","type":"text","name":"name","label":"Name"}]}
                </script>
                <script type="application/json" data-form-value>{"name":"Ada"}</script>
                <script type="application/json" data-form-errors-payload>{}</script>
            </form>
        `;

        const root = document.querySelector('[data-module="form-viewer"]');
        const runtime = initFormViewer(root);

        await tick();

        expect(window.DaisyFormViewer.get('destroyable-viewer')).toBe(runtime);

        runtime.destroy();

        expect(window.DaisyFormViewer.get('destroyable-viewer')).toBe(null);
        expect(window.DaisyFormViewer.getByElement(root)).toBe(null);
    });

    it('prunes disconnected viewer runtimes from the global registry', async () => {
        document.body.innerHTML = `
            <section>
                <form id="stale-viewer" data-form-id="stale-viewer" data-module="form-viewer" data-submit-mode="none">
                    <div data-form-field="name">
                        <input data-form-input="name" name="name" value="Ada" />
                        <p data-form-errors="name" class="hidden"></p>
                    </div>
                    <script type="application/json" data-form-schema>
                        {"version":"1.0","id":"profile","fields":[{"id":"name","type":"text","name":"name","label":"Name"}]}
                    </script>
                    <script type="application/json" data-form-value>{"name":"Ada"}</script>
                    <script type="application/json" data-form-errors-payload>{}</script>
                </form>
                <form id="live-viewer" data-form-id="live-viewer" data-module="form-viewer" data-submit-mode="none">
                    <div data-form-field="email">
                        <input data-form-input="email" name="email" value="ada@example.com" />
                        <p data-form-errors="email" class="hidden"></p>
                    </div>
                    <script type="application/json" data-form-schema>
                        {"version":"1.0","id":"contact","fields":[{"id":"email","type":"email","name":"email","label":"Email"}]}
                    </script>
                    <script type="application/json" data-form-value>{"email":"ada@example.com"}</script>
                    <script type="application/json" data-form-errors-payload>{}</script>
                </form>
            </section>
        `;

        const staleRoot = document.querySelector('#stale-viewer');
        const liveRoot = document.querySelector('#live-viewer');
        const staleRuntime = initFormViewer(staleRoot);
        const liveRuntime = initFormViewer(liveRoot);

        await tick();

        expect(window.DaisyFormViewer.all()).toEqual(expect.arrayContaining([staleRuntime, liveRuntime]));

        staleRoot.remove();

        expect(window.DaisyFormViewer.get('stale-viewer')).toBe(null);
        expect(window.DaisyFormViewer.getByElement(staleRoot)).toBe(null);
        expect(window.DaisyFormViewer.all()).toEqual([liveRuntime]);
    });

    it('initializes nested color picker controls rendered by the viewer', async () => {
        document.body.innerHTML = `
            <form id="color-viewer" data-form-id="color-viewer" data-module="form-viewer" data-submit-mode="none">
                <div data-colorpicker="1"
                    data-module="color-picker"
                    data-value="#2f80ed"
                    data-disabled="false"
                    data-dropdown="true"
                    data-swatches="[]"
                    data-swatches-height="0"
                    data-show-palette="true"
                    data-show-inputs="true"
                    data-show-format-toggle="true"
                    data-show-alpha="false"
                    data-show-hue="true"
                    data-form-input="brand_color">
                    <input type="hidden" name="brand_color" value="#2f80ed" data-colorpicker-input>
                    <div class="dropdown">
                        <div tabindex="0" role="button" data-colorpicker-trigger>
                            <span data-colorchip></span>
                            <span data-colortext>#2f80ed</span>
                        </div>
                        <div data-colorpicker-panel></div>
                    </div>
                </div>
                <script type="application/json" data-form-schema>
                    {"version":"1.0","id":"brand","fields":[{"id":"brand_color","type":"color","name":"brand_color","label":"Brand color"}]}
                </script>
                <script type="application/json" data-form-value>{"brand_color":"#2f80ed"}</script>
                <script type="application/json" data-form-errors-payload>{}</script>
            </form>
        `;

        const root = document.querySelector('[data-module="form-viewer"]');
        const runtime = initFormViewer(root);
        const picker = root.querySelector('[data-colorpicker="1"]');
        const dropdown = picker.querySelector('.dropdown');
        const trigger = picker.querySelector('[data-colorpicker-trigger]');

        expect(picker.__cpInit).toBe(true);

        trigger.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(dropdown.classList.contains('dropdown-open')).toBe(true);

        const formatSelect = picker.querySelector('[data-colorpicker-panel] select');
        formatSelect.value = 'hex';
        formatSelect.dispatchEvent(new Event('change', { bubbles: true }));

        expect(dropdown.classList.contains('dropdown-open')).toBe(true);
        expect(runtime.getValue('brand_color')).toBe('#2f80ed');
    });

    it('syncs color picker custom changes into the viewer runtime', async () => {
        document.body.innerHTML = `
            <form id="color-viewer" data-form-id="color-viewer">
                <div data-colorpicker="1" data-form-input="brand_color">
                    <input type="hidden" name="brand_color" value="#2f80ed" data-colorpicker-input>
                </div>
                <script type="application/json" data-form-schema>
                    {"version":"1.0","id":"brand","fields":[{"id":"brand_color","type":"color","name":"brand_color","label":"Brand color"}]}
                </script>
            </form>
        `;

        const root = document.querySelector('#color-viewer');
        const picker = root.querySelector('[data-colorpicker="1"]');
        const input = picker.querySelector('[data-colorpicker-input]');
        const runtime = createFormRuntime(root, {
            schema: JSON.parse(root.querySelector('[data-form-schema]').textContent),
        });

        input.value = '#ff3366';
        picker.dispatchEvent(new CustomEvent('colorpicker:change', {
            bubbles: true,
            detail: { value: '#ff3366' },
        }));

        await tick();

        expect(runtime.getValue('brand_color')).toBe('#ff3366');
    });

    it('handles visibility, validation and computed values', async () => {
        document.body.innerHTML = `
            <form>
                <div data-form-field="customer_type">
                    <input data-form-input="customer_type" name="customer_type" value="personal" />
                    <p data-form-errors="customer_type" class="hidden"></p>
                </div>
                <div data-form-field="company_vat">
                    <input data-form-input="company_vat" name="company_vat" value="" />
                    <p data-form-errors="company_vat" class="hidden"></p>
                </div>
                <div data-form-field="quantity">
                    <input data-form-input="quantity" name="quantity" value="2" />
                    <p data-form-errors="quantity" class="hidden"></p>
                </div>
                <div data-form-field="unit_price">
                    <input data-form-input="unit_price" name="unit_price" value="10" />
                    <p data-form-errors="unit_price" class="hidden"></p>
                </div>
                <div data-form-field="total">
                    <input data-form-input="total" name="total" value="" />
                    <p data-form-errors="total" class="hidden"></p>
                </div>
            </form>
        `;

        const root = document.querySelector('form');
        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'quote',
                fields: [
                    { id: 'customer_type', type: 'text', name: 'customer_type' },
                    {
                        id: 'company_vat',
                        type: 'text',
                        name: 'company_vat',
                        visibleWhen: { type: 'jsonata', expression: "values.customer_type = 'company'", dependsOn: ['customer_type'] },
                    },
                    {
                        id: 'quantity',
                        type: 'number',
                        name: 'quantity',
                        rules: [{ type: 'jsonata', expression: '$number(field.value) > 0', dependsOn: ['quantity'], message: 'Quantity must be positive.' }],
                    },
                    { id: 'unit_price', type: 'number', name: 'unit_price' },
                    {
                        id: 'total',
                        type: 'number',
                        name: 'total',
                        computed: { type: 'jsonata', expression: '$number(values.quantity) * $number(values.unit_price)', dependsOn: ['quantity', 'unit_price'], mode: 'readonly' },
                    },
                ],
            },
        });

        await tick();

        expect(runtime.state.visible.company_vat).toBe(false);
        expect(root.querySelector('[name="total"]').value).toBe('20');
        expect(await runtime.validate()).toBe(true);

        root.querySelector('[name="customer_type"]').value = 'company';
        root.dispatchEvent(new Event('input', { bubbles: true }));
        await tick();

        expect(runtime.state.visible.company_vat).toBe(true);
    });

    it('clears stale expression diagnostics after expressions recover', async () => {
        document.body.innerHTML = `
            <form>
                <div data-form-field="quantity">
                    <input data-form-input="quantity" name="quantity" value="2" />
                    <p data-form-errors="quantity" class="hidden"></p>
                </div>
                <div data-form-field="total">
                    <input data-form-input="total" name="total" value="" />
                    <p data-form-errors="total" class="hidden"></p>
                </div>
            </form>
        `;

        const root = document.querySelector('form');
        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'totals',
                fields: [
                    { id: 'quantity', type: 'number', name: 'quantity', label: 'Quantity' },
                    {
                        id: 'total',
                        type: 'number',
                        name: 'total',
                        label: 'Total',
                        computed: { type: 'jsonata', expression: '$missingFunction()', mode: 'readonly' },
                    },
                ],
            },
        });

        await tick();

        expect(runtime.getErrors().total.at(0)).toBeTruthy();
        expect(root.querySelector('[data-form-errors="total"]').classList.contains('hidden')).toBe(false);

        runtime.getField('total').computed.expression = '$number(values.quantity) * 2';
        await runtime.refresh();

        expect(runtime.getErrors()).toEqual({});
        expect(root.querySelector('[data-form-errors="total"]').classList.contains('hidden')).toBe(true);
        expect(runtime.getValue('total')).toBe(4);
        expect(root.querySelector('[name="total"]').value).toBe('4');
    });

    it('navigates multi-step forms and only submits on the final step', async () => {
        document.body.innerHTML = `
            <form>
                <div data-form-step="contact" data-form-step-index="0">
                    <div data-form-field="name">
                        <input data-form-input="name" name="name" value="Ada" />
                        <p data-form-errors="name" class="hidden"></p>
                    </div>
                </div>
                <div data-form-step="details" data-form-step-index="1">
                    <div data-form-field="email">
                        <input data-form-input="email" name="email" value="ada@example.com" />
                        <p data-form-errors="email" class="hidden"></p>
                    </div>
                </div>
                <button type="button" data-form-previous>Previous</button>
                <button type="button" data-form-next>Next</button>
                <button type="submit" data-form-submit>Submit</button>
            </form>
        `;

        const root = document.querySelector('form');
        const submissions = [];
        root.addEventListener('daisy-form:submit', (event) => submissions.push(event.detail.values));

        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'signup',
                layout: { type: 'multi-step' },
                fields: [
                    {
                        id: 'contact',
                        type: 'wizardStep',
                        label: 'Contact',
                        fields: [{ id: 'name', type: 'text', name: 'name', rules: ['required'] }],
                    },
                    {
                        id: 'details',
                        type: 'wizardStep',
                        label: 'Details',
                        fields: [{ id: 'email', type: 'email', name: 'email', rules: ['required', 'email'] }],
                    },
                ],
            },
        });

        await tick();

        expect(runtime.state.currentStep).toBe(0);
        expect(root.querySelector('[data-form-step="contact"]').classList.contains('hidden')).toBe(false);
        expect(root.querySelector('[data-form-step="details"]').classList.contains('hidden')).toBe(true);
        expect(root.querySelector('[data-form-submit]').classList.contains('hidden')).toBe(true);

        root.querySelector('[data-form-next]').click();
        await tick();

        expect(runtime.state.currentStep).toBe(1);
        expect(root.querySelector('[data-form-step="contact"]').classList.contains('hidden')).toBe(true);
        expect(root.querySelector('[data-form-step="details"]').classList.contains('hidden')).toBe(false);
        expect(root.querySelector('[data-form-submit]').classList.contains('hidden')).toBe(false);

        root.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        await tick();

        expect(submissions).toEqual([{ name: 'Ada', email: 'ada@example.com' }]);
    });

    it('validates on input or change when configured', async () => {
        document.body.innerHTML = `
            <form>
                <div data-form-field="email">
                    <input data-form-input="email" name="email" value="" />
                    <p data-form-errors="email" class="hidden"></p>
                </div>
            </form>
        `;

        const root = document.querySelector('form');
        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'signup',
                fields: [{ id: 'email', type: 'email', name: 'email', label: 'Email', rules: ['required', 'email'] }],
            },
            validateOn: 'input',
        });

        await tick();

        root.querySelector('[name="email"]').value = 'invalid';
        root.dispatchEvent(new Event('input', { bubbles: true }));
        await tick();

        expect(runtime.state.valid).toBe(false);
        expect(root.querySelector('[data-form-errors="email"]').textContent).toContain('valid email');

        root.querySelector('[name="email"]').value = 'ada@example.com';
        root.dispatchEvent(new Event('change', { bubbles: true }));
        await tick();

        expect(runtime.state.valid).toBe(true);
    });

    it('falls back to event submit mode for invalid runtime and schema submit modes', async () => {
        document.body.innerHTML = `
            <form>
                <div data-form-field="email">
                    <input data-form-input="email" name="email" value="ada@example.com" />
                    <p data-form-errors="email" class="hidden"></p>
                </div>
                <button type="submit" data-form-submit>Submit</button>
            </form>
        `;

        const root = document.querySelector('form');
        const submissions = [];
        root.addEventListener('daisy-form:submit', (event) => submissions.push(event.detail.values));

        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'signup',
                fields: [{ id: 'email', type: 'email', name: 'email', label: 'Email', rules: ['required', 'email'] }],
                submit: { mode: 'bogus' },
            },
            submitMode: 'invalid',
        });

        await tick();
        expect(await runtime.submit()).toBe(true);

        expect(submissions).toEqual([{ email: 'ada@example.com' }]);
    });

    it('lets an explicit valid runtime submit mode override the schema submit mode', async () => {
        document.body.innerHTML = `
            <form>
                <div data-form-field="email">
                    <input data-form-input="email" name="email" value="ada@example.com" />
                    <p data-form-errors="email" class="hidden"></p>
                </div>
                <button type="submit" data-form-submit>Submit</button>
            </form>
        `;

        const root = document.querySelector('form');
        const submissions = [];
        root.addEventListener('daisy-form:submit', (event) => submissions.push(event.detail.values));

        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'signup',
                fields: [{ id: 'email', type: 'email', name: 'email', label: 'Email', rules: ['required', 'email'] }],
                submit: { mode: 'event' },
            },
            submitMode: 'none',
        });

        await tick();
        expect(await runtime.submit()).toBe(true);

        expect(submissions).toEqual([]);
    });

    it('uses the original viewer method data attribute for fetch submissions', async () => {
        document.body.innerHTML = `
            <form action="/contacts/1" method="POST" data-form-method="PATCH">
                <div data-form-field="email">
                    <input data-form-input="email" name="email" value="ada@example.com" />
                    <p data-form-errors="email" class="hidden"></p>
                </div>
                <button type="submit" data-form-submit>Submit</button>
                <script type="application/json" data-form-schema>
                    {"version":"1.0","id":"signup","fields":[{"id":"email","type":"email","name":"email","label":"Email","rules":["required","email"]}],"submit":{"mode":"fetch"}}
                </script>
                <script type="application/json" data-form-value>{"email":"ada@example.com"}</script>
                <script type="application/json" data-form-errors-payload>{}</script>
            </form>
        `;

        const root = document.querySelector('form');
        const fetch = vi.fn().mockResolvedValue({ ok: true });
        const previousFetch = globalThis.fetch;
        globalThis.fetch = fetch;

        const runtime = initFormViewer(root);

        await tick();
        await runtime.submit();

        expect(fetch).toHaveBeenCalledWith('/contacts/1', expect.objectContaining({
            method: 'PATCH',
        }));

        globalThis.fetch = previousFetch;
    });
});

describe('form-kit builder bridge', () => {
    it('defers drop zone priming until the drag actually starts', async () => {
        document.body.innerHTML = `
            <div data-module="form-builder">
                <table>
                    <tbody>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="section" data-builder-drop-action="before" data-builder-drop-kind="position" data-builder-drop-parent="__root" data-builder-drop-index="0" data-builder-drop-previous=""></button>
                            </td>
                        </tr>
                        <tr data-builder-field="section" data-builder-field-depth="0">
                            <td>
                                <span data-builder-drag-handle data-builder-drag-field="section" data-builder-drag-descendants='["child"]' data-builder-drag-parent="__root" data-builder-drag-index="0"></span>
                                <button data-builder-select><span>Section</span></button>
                                <span data-builder-type-badge>section</span>
                            </td>
                        </tr>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="child" data-builder-drop-action="before" data-builder-drop-kind="position" data-builder-drop-parent="section" data-builder-drop-index="0" data-builder-drop-previous=""></button>
                            </td>
                        </tr>
                        <tr data-builder-field="child" data-builder-field-depth="1">
                            <td>Child</td>
                        </tr>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="after" data-builder-drop-action="before" data-builder-drop-kind="position" data-builder-drop-parent="__root" data-builder-drop-index="1" data-builder-drop-previous="section"></button>
                            </td>
                        </tr>
                        <tr data-builder-field="after" data-builder-field-depth="0">
                            <td>After</td>
                        </tr>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="tail" data-builder-drop-action="before" data-builder-drop-kind="position" data-builder-drop-parent="__root" data-builder-drop-index="2" data-builder-drop-previous="after"></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;

        const root = document.querySelector('[data-module="form-builder"]');
        const handle = root.querySelector('[data-builder-drag-handle]');

        initFormBuilder(root);
        handle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            button: 0,
            clientX: 10,
            clientY: 10,
        }));

        expect(root.dataset.builderDragReady).toBe('1');
        expect(root.dataset.dragging).toBeUndefined();
        expect(root.querySelector('[data-builder-field="section"]').hasAttribute('data-builder-dragging-row')).toBe(false);

        document.dispatchEvent(new MouseEvent('pointermove', {
            bubbles: true,
            button: 0,
            clientX: 220,
            clientY: 220,
        }));

        await frame();
        await frame();

        expect(root.dataset.dragging).toBe('section');
        expect(root.querySelector('[data-builder-field="section"]').hasAttribute('data-builder-dragging-row')).toBe(true);
        expect(root.querySelector('[data-builder-drop-target="section"]').hasAttribute('data-builder-drop-disabled')).toBe(true);
        expect(root.querySelector('[data-builder-drop-target="child"]').hasAttribute('data-builder-drop-disabled')).toBe(true);
        expect(root.querySelector('[data-builder-drop-target="after"]').hasAttribute('data-builder-drop-disabled')).toBe(true);
        expect(root.querySelector('[data-builder-drop-target="tail"]').hasAttribute('data-builder-drop-disabled')).toBe(false);

        document.dispatchEvent(new MouseEvent('pointerup', {
            bubbles: true,
            button: 0,
            clientX: 10,
            clientY: 10,
        }));

        expect(root.dataset.dragging).toBeUndefined();
        expect(root.querySelector('[data-builder-field="section"]').hasAttribute('data-builder-dragging-row')).toBe(false);
    });

    it('hides same-level drop zones that would keep the dragged field in place', async () => {
        document.body.innerHTML = `
            <div data-module="form-builder">
                <table>
                    <tbody>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="first" data-builder-drop-action="before" data-builder-drop-kind="position" data-builder-drop-parent="__root" data-builder-drop-index="0" data-builder-drop-previous=""></button>
                            </td>
                        </tr>
                        <tr data-builder-field="first" data-builder-field-depth="0">
                            <td>First</td>
                        </tr>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="middle" data-builder-drop-action="before" data-builder-drop-kind="position" data-builder-drop-parent="__root" data-builder-drop-index="1" data-builder-drop-previous="first"></button>
                            </td>
                        </tr>
                        <tr data-builder-field="middle" data-builder-field-depth="0">
                            <td>
                                <span data-builder-drag-handle data-builder-drag-field="middle" data-builder-drag-descendants="[]" data-builder-drag-parent="__root" data-builder-drag-index="1"></span>
                                <button data-builder-select><span>Middle</span></button>
                                <span data-builder-type-badge>text</span>
                            </td>
                        </tr>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="last" data-builder-drop-action="before" data-builder-drop-kind="position" data-builder-drop-parent="__root" data-builder-drop-index="2" data-builder-drop-previous="middle"></button>
                            </td>
                        </tr>
                        <tr data-builder-field="last" data-builder-field-depth="0">
                            <td>Last</td>
                        </tr>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="last" data-builder-drop-action="after" data-builder-drop-kind="position" data-builder-drop-parent="__root" data-builder-drop-index="3" data-builder-drop-previous="last"></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;

        const root = document.querySelector('[data-module="form-builder"]');
        const handle = root.querySelector('[data-builder-drag-handle]');

        initFormBuilder(root);
        handle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            button: 0,
            clientX: 10,
            clientY: 10,
        }));

        document.dispatchEvent(new MouseEvent('pointermove', {
            bubbles: true,
            button: 0,
            clientX: 80,
            clientY: 180,
        }));

        await frame();
        await frame();

        expect(root.querySelector('[data-builder-drop-target="first"]').hasAttribute('data-builder-drop-disabled')).toBe(false);
        expect(root.querySelector('[data-builder-drop-target="middle"]').hasAttribute('data-builder-drop-disabled')).toBe(true);
        expect(root.querySelector('[data-builder-drop-target="last"][data-builder-drop-action="before"]').hasAttribute('data-builder-drop-disabled')).toBe(true);
        expect(root.querySelector('[data-builder-drop-target="last"][data-builder-drop-action="after"]').hasAttribute('data-builder-drop-disabled')).toBe(false);

        document.dispatchEvent(new MouseEvent('pointerup', {
            bubbles: true,
            button: 0,
            clientX: 80,
            clientY: 180,
        }));
    });

    it('keeps the dragged row anchored when drop zones change layout height', async () => {
        document.body.innerHTML = `
            <div data-module="form-builder">
                <table>
                    <tbody>
                        <tr data-builder-drop-row>
                            <td>
                                <button data-builder-drop-zone data-builder-drop-target="before" data-builder-drop-action="before"></button>
                            </td>
                        </tr>
                        <tr data-builder-field="section" data-builder-field-depth="0">
                            <td>
                                <span data-builder-drag-handle data-builder-drag-field="section" data-builder-drag-descendants="[]"></span>
                                <button data-builder-select><span>Section</span></button>
                                <span data-builder-type-badge>section</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;

        const root = document.querySelector('[data-module="form-builder"]');
        const row = root.querySelector('[data-builder-field="section"]');
        const handle = root.querySelector('[data-builder-drag-handle]');
        const scrollBy = vi.spyOn(window, 'scrollBy').mockImplementation(() => {});

        row.getBoundingClientRect = () => ({
            top: root.dataset.dragging ? 360 : 300,
        });

        initFormBuilder(root);
        handle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            button: 0,
            clientX: 10,
            clientY: 10,
        }));

        document.dispatchEvent(new MouseEvent('pointermove', {
            bubbles: true,
            button: 0,
            clientX: 80,
            clientY: 300,
        }));

        await frame();
        await frame();
        await frame();

        expect(scrollBy).toHaveBeenCalledWith(0, 60);

        document.dispatchEvent(new MouseEvent('pointerup', {
            bubbles: true,
            button: 0,
            clientX: 80,
            clientY: 300,
        }));

        await frame();

        expect(scrollBy).toHaveBeenCalledWith(0, -60);
        scrollBy.mockRestore();
    });
});

describe('color picker', () => {
    it('keeps dropdown interactions inside the panel from reaching global closers', () => {
        document.body.innerHTML = `
            <div data-colorpicker="1"
                data-value="#123456"
                data-disabled="false"
                data-dropdown="true"
                data-swatches="[]"
                data-swatches-height="0"
                data-show-palette="true"
                data-show-inputs="true"
                data-show-format-toggle="true"
                data-show-alpha="true"
                data-show-hue="true">
                <div class="dropdown dropdown-open">
                    <button type="button" data-colorpicker-trigger>Open</button>
                    <div data-colorpicker-panel></div>
                </div>
            </div>
        `;

        const root = document.querySelector('[data-colorpicker="1"]');
        const dropdown = root.querySelector('.dropdown');
        const globalCloser = vi.fn(() => dropdown.classList.remove('dropdown-open'));

        document.addEventListener('click', globalCloser);

        initColorPicker(root);

        const formatSelect = root.querySelector('[data-colorpicker-panel] select');
        formatSelect.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(globalCloser).not.toHaveBeenCalled();
        expect(dropdown.classList.contains('dropdown-open')).toBe(true);

        document.removeEventListener('click', globalCloser);
    });

    it('keeps format select changes inside the picker dropdown', async () => {
        document.body.innerHTML = `
            <form>
                <div data-colorpicker="1"
                    data-value="#123456"
                    data-disabled="false"
                    data-dropdown="true"
                    data-swatches="[]"
                    data-swatches-height="0"
                    data-show-palette="true"
                    data-show-inputs="true"
                    data-show-format-toggle="true"
                    data-show-alpha="true"
                    data-show-hue="true">
                    <div class="dropdown dropdown-open">
                        <button type="button" data-colorpicker-trigger>Open</button>
                        <div data-colorpicker-panel></div>
                    </div>
                </div>
            </form>
        `;

        const form = document.querySelector('form');
        const root = document.querySelector('[data-colorpicker="1"]');
        const dropdown = root.querySelector('.dropdown');
        const formChange = vi.fn();
        const colorChange = vi.fn();

        form.addEventListener('change', formChange);
        root.addEventListener('colorpicker:change', colorChange);

        initColorPicker(root);

        const formatSelect = root.querySelector('[data-colorpicker-panel] select');
        formatSelect.value = 'hex';
        formatSelect.dispatchEvent(new Event('change', { bubbles: true }));

        await new Promise((resolve) => setTimeout(resolve, 60));

        expect(formChange).not.toHaveBeenCalled();
        expect(colorChange).toHaveBeenCalledOnce();
        expect(dropdown.classList.contains('dropdown-open')).toBe(true);
    });

    it('attaches dropdown controls when a picker was already marked initialized', () => {
        document.body.innerHTML = `
            <div data-colorpicker="1"
                data-value="#123456"
                data-disabled="false"
                data-dropdown="true"
                data-swatches="[]"
                data-swatches-height="0"
                data-show-palette="true"
                data-show-inputs="true"
                data-show-format-toggle="true"
                data-show-alpha="true"
                data-show-hue="true">
                <div class="dropdown">
                    <button type="button" data-colorpicker-trigger>Open</button>
                    <div data-colorpicker-panel></div>
                </div>
            </div>
        `;

        const root = document.querySelector('[data-colorpicker="1"]');
        const dropdown = root.querySelector('.dropdown');
        const trigger = root.querySelector('[data-colorpicker-trigger]');

        root.__cpInit = true;

        initColorPicker(root);
        trigger.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(dropdown.classList.contains('dropdown-open')).toBe(true);
        expect(root.__cpDropdownInit).toBe(true);
    });

    it('opens dropdowns when the visible wrapper receives the click', () => {
        document.body.innerHTML = `
            <div data-colorpicker="1"
                data-value="#123456"
                data-disabled="false"
                data-dropdown="true"
                data-swatches="[]"
                data-swatches-height="0"
                data-show-palette="true"
                data-show-inputs="true"
                data-show-format-toggle="true"
                data-show-alpha="true"
                data-show-hue="true">
                <div class="dropdown">
                    <button type="button" data-colorpicker-trigger>Open</button>
                    <div data-colorpicker-panel></div>
                </div>
            </div>
        `;

        const root = document.querySelector('[data-colorpicker="1"]');
        const dropdown = root.querySelector('.dropdown');

        initColorPicker(root);
        dropdown.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(dropdown.classList.contains('dropdown-open')).toBe(true);
    });
});
