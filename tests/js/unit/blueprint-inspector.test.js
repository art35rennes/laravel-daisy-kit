// @vitest-environment jsdom

import { afterEach, describe, expect, it, vi } from 'vitest';
import { createInspector } from '../../../resources/js/blueprint/inspector.js';

/**
 * Creates the Blueprint inspector DOM contract.
 *
 * @returns {HTMLElement} Blueprint root.
 */
function createRoot() {
    document.body.innerHTML = `
        <div id="blueprint">
            <button data-blueprint-inspector-backdrop class="hidden"></button>
            <aside data-blueprint-inspector class="hidden">
                <h2 data-blueprint-inspector-title></h2>
                <span data-blueprint-dirty-indicator class="hidden"></span>
                <form data-blueprint-inspector-form>
                    <input name="label">
                    <textarea name="description"></textarea>
                    <select name="category"></select>
                    <div data-blueprint-integrator-fields></div>
                </form>
            </aside>
            <dialog data-blueprint-discard-dialog>
                <button data-blueprint-action="keep-editing"></button>
                <button data-blueprint-action="discard-changes"></button>
            </dialog>
        </div>
    `;

    return document.querySelector('#blueprint');
}

const categories = [{
    value: 'workflow-step',
    label: 'Workflow step',
    defaults: { forwardable: true },
    fields: [
        { key: 'status', type: 'select', label: 'Status', required: true, options: [
            { value: 'draft', label: 'Draft', disabled: false },
            { value: 'done', label: 'Done', disabled: false },
        ] },
        { key: 'readonly', type: 'checkbox', label: 'Readonly' },
    ],
}];

describe('Blueprint inspector', () => {
    afterEach(() => {
        vi.useRealTimers();
    });

    it('commits generic and integrator fields as one change', () => {
        const onCommit = vi.fn();
        const inspector = createInspector(createRoot(), {
            autosave: false,
            onCommit,
            enhanceRichControls: false,
        });
        inspector.open({
            selection: { type: 'node', id: 'review' },
            entity: {
                id: 'review',
                label: 'Review',
                description: '',
                category: 'workflow-step',
                data: { status: 'draft', opaque: 'preserved' },
            },
            categories,
            titlePrefix: 'Step',
        });
        const form = document.querySelector('[data-blueprint-inspector-form]');
        form.elements.label.value = 'Approval';
        form.querySelector('[data-blueprint-field="status"]').value = 'done';
        form.querySelector('[data-blueprint-field="readonly"]').checked = true;
        form.elements.label.dispatchEvent(new Event('input', { bubbles: true }));

        expect(inspector.isDirty()).toBe(true);
        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));

        expect(onCommit).toHaveBeenCalledOnce();
        expect(onCommit).toHaveBeenCalledWith(
            { type: 'node', id: 'review' },
            {
                label: 'Approval',
                description: '',
                category: 'workflow-step',
                data: {
                    status: 'done',
                    readonly: true,
                    opaque: 'preserved',
                    forwardable: true,
                },
            },
        );
        expect(inspector.isDirty()).toBe(false);
    });

    it('guards a pending action until dirty changes are discarded', () => {
        const root = createRoot();
        const pendingAction = vi.fn();
        const inspector = createInspector(root, {
            autosave: false,
            onCommit: vi.fn(),
            enhanceRichControls: false,
        });
        inspector.open({
            selection: { type: 'node', id: 'review' },
            entity: {
                id: 'review',
                label: 'Review',
                description: '',
                category: 'workflow-step',
                data: { status: 'draft' },
            },
            categories,
            titlePrefix: 'Step',
        });
        root.querySelector('[name="label"]').value = 'Changed';
        root.querySelector('[name="label"]').dispatchEvent(new Event('input', { bubbles: true }));

        expect(inspector.request(pendingAction)).toBe(false);
        expect(root.querySelector('[data-blueprint-discard-dialog]').hasAttribute('open')).toBe(true);
        expect(pendingAction).not.toHaveBeenCalled();

        root.querySelector('[data-blueprint-action="keep-editing"]').click();
        expect(root.querySelector('[data-blueprint-discard-dialog]').hasAttribute('open')).toBe(false);
        expect(inspector.isDirty()).toBe(true);

        inspector.request(pendingAction);
        root.querySelector('[data-blueprint-action="discard-changes"]').click();

        expect(pendingAction).toHaveBeenCalledOnce();
        expect(inspector.isDirty()).toBe(false);
    });

    it('autosaves a dirty draft after the debounce', async () => {
        vi.useFakeTimers();
        const onCommit = vi.fn();
        const root = createRoot();
        const inspector = createInspector(root, {
            autosave: true,
            onCommit,
            enhanceRichControls: false,
        });
        inspector.open({
            selection: { type: 'node', id: 'review' },
            entity: {
                id: 'review',
                label: 'Review',
                description: '',
                category: 'workflow-step',
                data: { status: 'draft' },
            },
            categories,
            titlePrefix: 'Step',
        });
        root.querySelector('[name="label"]').value = 'Autosaved';
        root.querySelector('[name="label"]').dispatchEvent(new Event('input', { bubbles: true }));

        await vi.advanceTimersByTimeAsync(400);

        expect(onCommit).toHaveBeenCalledOnce();
        expect(onCommit.mock.calls[0][1].label).toBe('Autosaved');
        expect(inspector.isDirty()).toBe(false);
    });

    it('reports the selected entity data path for invalid required fields', () => {
        const root = createRoot();
        const onCommit = vi.fn();
        const onError = vi.fn();
        const inspector = createInspector(root, {
            autosave: false,
            onCommit,
            onError,
            enhanceRichControls: false,
        });
        inspector.open({
            selection: { type: 'node', id: 'review' },
            entity: {
                id: 'review',
                label: 'Review',
                description: '',
                category: 'workflow-step',
                data: { status: '' },
            },
            categories,
            titlePrefix: 'Step',
        });

        root.querySelector('[data-blueprint-inspector-form]').dispatchEvent(
            new Event('submit', { bubbles: true, cancelable: true }),
        );

        expect(onCommit).not.toHaveBeenCalled();
        expect(onError).toHaveBeenCalledWith(
            expect.any(Error),
            expect.objectContaining({
                path: 'nodes.review.data.status',
                selection: { type: 'node', id: 'review' },
            }),
        );
    });
});
