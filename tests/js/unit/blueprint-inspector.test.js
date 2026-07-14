// @vitest-environment jsdom

import { describe, expect, it, vi } from 'vitest';
import { createInspector } from '../../../resources/js/blueprint/inspector.js';

function createRoot() {
    document.body.innerHTML = `
        <div id="blueprint">
            <dialog data-blueprint-inspector class="hidden">
                <h2 data-blueprint-inspector-title></h2>
                <span data-blueprint-dirty-indicator class="hidden"></span>
                <button data-blueprint-action="close-inspector"></button>
                <div data-blueprint-inspector-content></div>
                <button data-blueprint-inspector-backdrop></button>
            </dialog>
            <dialog data-blueprint-discard-dialog>
                <button data-blueprint-action="keep-editing"></button>
                <button data-blueprint-action="discard-changes"></button>
            </dialog>
        </div>
    `;

    return document.querySelector('#blueprint');
}

function nodeValue(overrides = {}) {
    return {
        label: 'Review',
        description: 'Review the document',
        category: 'approval',
        data: { owner: 'Ada' },
        ...overrides,
    };
}

describe('Blueprint inspector', () => {
    it('opens an isolated draft and exposes session-bound actions', () => {
        const onOpen = vi.fn();
        const inspector = createInspector(createRoot(), {
            onOpen,
            onCommit: vi.fn(),
            onCancel: vi.fn(),
        });
        const source = nodeValue();

        const context = inspector.open({
            selection: { type: 'node', id: 'review' },
            value: source,
            title: 'Step: Review',
            isNew: false,
        });
        source.data.owner = 'Changed outside';
        context.value.data.owner = 'Changed by consumer';

        expect(onOpen).toHaveBeenCalledOnce();
        expect(onOpen.mock.calls[0][0]).toMatchObject({
            selection: { type: 'node', id: 'review' },
            isNew: false,
            value: nodeValue(),
            setDraft: expect.any(Function),
            commit: expect.any(Function),
            cancel: expect.any(Function),
        });
        expect(inspector.getDraft()).toEqual(nodeValue());
        expect(document.querySelector('[data-blueprint-inspector-title]').textContent).toBe('Step: Review');
    });

    it('tracks a generic draft and commits only the supported object', () => {
        const onCommit = vi.fn((selection, value) => ({ ...value, id: selection.id }));
        const inspector = createInspector(createRoot(), {
            onOpen: vi.fn(),
            onCommit,
            onCancel: vi.fn(),
        });
        const context = inspector.open({
            selection: { type: 'node', id: 'review' },
            value: nodeValue(),
            title: 'Step: Review',
        });

        context.setDraft({
            label: 'Approval',
            description: '',
            category: 'done',
            data: { owner: 'Grace' },
            ignored: true,
        });

        expect(inspector.isDirty()).toBe(true);
        expect(document.querySelector('[data-blueprint-dirty-indicator]').classList.contains('hidden')).toBe(false);
        expect(context.commit()).toEqual({
            id: 'review',
            label: 'Approval',
            description: '',
            category: 'done',
            data: { owner: 'Grace' },
        });
        expect(onCommit).toHaveBeenCalledWith(
            { type: 'node', id: 'review' },
            {
                label: 'Approval',
                description: '',
                category: 'done',
                data: { owner: 'Grace' },
            },
            expect.objectContaining({ isNew: false }),
        );
        expect(document.querySelector('[data-blueprint-inspector]').classList.contains('hidden')).toBe(true);
    });

    it('preserves opaque data when a draft omits the data property', () => {
        const onCommit = vi.fn((selection, value) => ({ ...value, id: selection.id }));
        const inspector = createInspector(createRoot(), {
            onOpen: vi.fn(),
            onCommit,
            onCancel: vi.fn(),
        });
        const context = inspector.open({
            selection: { type: 'node', id: 'review' },
            value: nodeValue(),
            title: 'Step: Review',
        });

        context.commit({
            label: 'Approval',
            description: '',
            category: 'done',
        });

        expect(onCommit).toHaveBeenCalledWith(
            { type: 'node', id: 'review' },
            {
                label: 'Approval',
                description: '',
                category: 'done',
                data: { owner: 'Ada' },
            },
            expect.objectContaining({ isNew: false }),
        );
    });

    it('keeps the modal open and reports invalid generic values', () => {
        const onCommit = vi.fn();
        const onError = vi.fn();
        const inspector = createInspector(createRoot(), {
            onOpen: vi.fn(),
            onCommit,
            onCancel: vi.fn(),
            onError,
        });
        const context = inspector.open({
            selection: { type: 'transition', id: 'submit' },
            value: nodeValue(),
            title: 'Transition: Submit',
        });

        expect(context.commit({ ...nodeValue(), data: [] })).toBeNull();
        expect(onCommit).not.toHaveBeenCalled();
        expect(onError).toHaveBeenCalledWith(
            expect.objectContaining({ message: 'invalid_inspector_data' }),
            expect.objectContaining({
                path: 'transitions.submit.data',
                selection: { type: 'transition', id: 'submit' },
            }),
        );
        expect(document.querySelector('[data-blueprint-inspector]').classList.contains('hidden')).toBe(false);
    });

    it('asks before cancelling a dirty draft and emits cancellation after confirmation', () => {
        const root = createRoot();
        const onCancel = vi.fn();
        const inspector = createInspector(root, {
            onOpen: vi.fn(),
            onCommit: vi.fn(),
            onCancel,
        });
        const context = inspector.open({
            selection: { type: 'node', id: 'review' },
            value: nodeValue(),
            title: 'Step: Review',
        });
        context.setDraft(nodeValue({ label: 'Changed' }));

        expect(context.cancel('api')).toBe(false);
        expect(root.querySelector('[data-blueprint-discard-dialog]').hasAttribute('open')).toBe(true);
        expect(onCancel).not.toHaveBeenCalled();

        root.querySelector('[data-blueprint-action="keep-editing"]').click();
        expect(inspector.isDirty()).toBe(true);

        context.cancel('close');
        root.querySelector('[data-blueprint-action="discard-changes"]').click();

        expect(onCancel).toHaveBeenCalledWith(
            { type: 'node', id: 'review' },
            nodeValue(),
            nodeValue({ label: 'Changed' }),
            expect.objectContaining({ isNew: false, reason: 'close' }),
        );
        expect(root.querySelector('[data-blueprint-inspector]').classList.contains('hidden')).toBe(true);
    });

    it('rejects actions retained from an obsolete inspector session', () => {
        const onCommit = vi.fn((selection, value) => ({ ...value, id: selection.id }));
        const inspector = createInspector(createRoot(), {
            onOpen: vi.fn(),
            onCommit,
            onCancel: vi.fn(),
        });
        const first = inspector.open({
            selection: { type: 'node', id: 'first' },
            value: nodeValue({ label: 'First' }),
            title: 'Step: First',
        });
        inspector.open({
            selection: { type: 'node', id: 'second' },
            value: nodeValue({ label: 'Second' }),
            title: 'Step: Second',
        });

        expect(first.setDraft(nodeValue({ label: 'Stale' }))).toBeNull();
        expect(first.commit(nodeValue({ label: 'Stale' }))).toBeNull();
        expect(first.cancel()).toBe(false);
        expect(onCommit).not.toHaveBeenCalled();
        expect(inspector.getDraft().label).toBe('Second');
    });
});
