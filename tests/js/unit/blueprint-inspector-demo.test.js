// @vitest-environment jsdom

import { beforeEach, describe, expect, it, vi } from 'vitest';
import initBlueprintInspectorDemo from '../../../resources/js/modules/blueprint-inspector-demo.js';

function createDemo() {
    document.body.innerHTML = `
        <div data-blueprint>
            <div data-module="blueprint-inspector-demo">
                <input data-blueprint-demo-field="label">
                <textarea data-blueprint-demo-field="description"></textarea>
                <select data-blueprint-demo-field="category"><option value="work">Work</option></select>
                <input data-blueprint-demo-field="owner">
                <select data-blueprint-demo-field="priority"><option value="high">High</option></select>
                <input type="checkbox" data-blueprint-demo-field="expedited">
                <input type="checkbox" data-blueprint-demo-field="notify">
                <div data-blueprint-demo-node-fields></div>
                <div data-blueprint-demo-transition-fields></div>
                <button data-blueprint-demo-action="save"></button>
                <button data-blueprint-demo-action="cancel"></button>
                <button data-blueprint-demo-action="delete"></button>
            </div>
        </div>
    `;

    const blueprint = document.querySelector('[data-blueprint]');
    blueprint.__daisyBlueprint = {
        removeNode: vi.fn(),
        removeTransition: vi.fn(),
    };

    return {
        blueprint,
        root: document.querySelector('[data-module="blueprint-inspector-demo"]'),
    };
}

describe('Blueprint inspector demo integration', () => {
    beforeEach(() => {
        vi.restoreAllMocks();
    });

    it('hydrates host fields and returns a complete opaque draft', () => {
        const { blueprint, root } = createDemo();
        const setDraft = vi.fn();
        const commit = vi.fn();
        initBlueprintInspectorDemo(root);

        blueprint.dispatchEvent(new CustomEvent('daisy:blueprint:inspector-open', {
            detail: {
                selection: { type: 'node', id: 'review' },
                value: {
                    label: 'Review',
                    description: 'Check the document',
                    category: 'work',
                    data: { owner: 'Ada', priority: 'high', expedited: false, opaque: 42 },
                },
                setDraft,
                commit,
                cancel: vi.fn(),
            },
        }));

        expect(root.querySelector('[data-blueprint-demo-field="label"]').value).toBe('Review');
        root.querySelector('[data-blueprint-demo-field="owner"]').value = 'Grace';
        root.querySelector('[data-blueprint-demo-field="owner"]').dispatchEvent(
            new Event('input', { bubbles: true }),
        );
        root.querySelector('[data-blueprint-demo-action="save"]').click();

        expect(setDraft).toHaveBeenLastCalledWith(expect.objectContaining({
            label: 'Review',
            data: { owner: 'Grace', priority: 'high', expedited: false, opaque: 42 },
        }));
        expect(commit).toHaveBeenCalledWith(expect.objectContaining({
            data: expect.objectContaining({ opaque: 42 }),
        }));
    });

    it('switches structural actions for transition selections', () => {
        const { blueprint, root } = createDemo();
        initBlueprintInspectorDemo(root);

        blueprint.dispatchEvent(new CustomEvent('daisy:blueprint:inspector-open', {
            detail: {
                selection: { type: 'transition', id: 'submit' },
                value: {
                    label: 'Submit',
                    description: '',
                    category: 'work',
                    data: { notify: true },
                },
                setDraft: vi.fn(),
                commit: vi.fn(),
                cancel: vi.fn(),
            },
        }));
        root.querySelector('[data-blueprint-demo-action="delete"]').click();

        expect(root.querySelector('[data-blueprint-demo-node-fields]').hidden).toBe(true);
        expect(root.querySelector('[data-blueprint-demo-transition-fields]').hidden).toBe(false);
        expect(blueprint.__daisyBlueprint.removeTransition).toHaveBeenCalledWith('submit');
        expect(blueprint.__daisyBlueprint.removeNode).not.toHaveBeenCalled();
    });
});
