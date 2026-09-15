import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

vi.hoisted(() => {
    Reflect.deleteProperty(window, 'ElementInternals');
});

import { getInstance, mount, mountAll, unmount } from '../../../resources/js/wysiwyg.js';

function root(overrides = {}) {
    const configuration = {
        attachments: false,
        autofocus: false,
        disabled: false,
        editorId: 'editor-one',
        placeholder: 'Write',
        readonly: false,
        required: false,
        showToolbar: true,
        size: 'md',
        value: '<p>Initial</p>',
        labels: {
            attachmentPending: 'Pending attachment',
            initializationFailed: 'Initialization failed',
        },
        ...overrides,
    };

    const element = document.createElement('fieldset');
    element.dataset.daisyKitModule = 'wysiwyg';
    element.innerHTML = `
        <p data-daisy-kit-status hidden></p>
        <div data-daisy-kit-wysiwyg-editor></div>
        <input data-daisy-kit-wysiwyg-value id="editor-one-input" name="body" type="hidden" value="${configuration.value.replaceAll('"', '&quot;')}">
        <input data-daisy-kit-wysiwyg-validation class="sr-only" type="text" ${configuration.required ? 'required' : ''}>
        <template data-daisy-kit-wysiwyg-toolbar-template>
            <div role="toolbar"><button type="button" data-trix-attribute="bold">Bold</button><button type="button" data-trix-action="x-preview">Preview</button></div>
        </template>
        <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration).replaceAll('<', '\\u003c')}</script>
    `;
    document.body.append(element);

    return element;
}

function attachment(id = 42) {
    const attributes = { contentType: 'image/png', filename: 'diagram.png', filesize: 1200 };

    return {
        file: new File(['image'], 'diagram.png', { type: 'image/png' }),
        getAttributes: () => ({ ...attributes }),
        getHref: () => attributes.href,
        getURL: () => attributes.url,
        id,
        releaseFile: vi.fn(),
        setAttributes: vi.fn((values) => Object.assign(attributes, values)),
        setUploadProgress: vi.fn(),
    };
}

function dispatchAttachment(editor, name, value) {
    const event = new Event(`trix-${name}`, { bubbles: true, cancelable: true });
    Object.defineProperty(event, 'attachment', { value });
    editor.dispatchEvent(event);

    return event;
}

describe('wysiwyg entry', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
    });

    afterEach(async () => {
        for (const element of document.querySelectorAll('[data-daisy-kit-module="wysiwyg"]')) {
            unmount(element);
        }
        document.body.innerHTML = '';
        await Promise.resolve();
    });

    it('mounts idempotently, exposes Trix, synchronizes HTML, and restores on unmount', () => {
        const element = root();
        const mounted = vi.fn();
        element.addEventListener('daisy-kit:wysiwyg:mounted', mounted);

        const instance = mount(element);
        const editor = element.querySelector('trix-editor');

        expect(instance).toBe(getInstance(element));
        expect(mount(element)).toBe(instance);
        expect(instance.getTrixEditor()).toBe(editor.editor);
        expect(globalThis.Trix).toBeUndefined();
        expect(editor.getAttribute('input')).toBe('editor-one-input');
        expect(editor.getAttribute('toolbar')).toBe('editor-one-toolbar');
        expect(mounted).toHaveBeenCalledTimes(1);

        expect(instance.setValue('<p>Changed</p>')).toBe(true);
        expect(instance.getValue()).toContain('Changed');
        expect(instance.clear()).toBe(true);
        expect(instance.getValue()).not.toContain('Changed');

        expect(unmount(element)).toBe(true);
        expect(instance.getTrixEditor()).toBeNull();
        expect(element.querySelector('trix-editor')).toBeNull();
        expect(element.querySelector('input').value).toBe('<p>Initial</p>');
        expect(unmount(element)).toBe(false);
    });

    it('keeps the toolbar absent when it is disabled', () => {
        const element = root({ showToolbar: false });

        mount(element);

        const toolbar = element.querySelector('trix-toolbar');
        expect(toolbar.hasAttribute('hidden')).toBe(true);
        expect(element.querySelector('trix-editor').getAttribute('toolbar')).toBe(toolbar.id);
    });

    it('supports multiple roots and leaves a disposed facade inert', () => {
        const first = root({ editorId: 'first' });
        const second = root({ editorId: 'second' });
        const instances = mountAll();

        expect(instances).toHaveLength(2);
        expect(instances[0]).not.toBe(instances[1]);
        unmount(first);
        expect(instances[0].setValue('<p>Late</p>')).toBe(false);
        expect(instances[0].focus()).toBe(false);
    });

    it('rejects files by default', () => {
        const element = root();
        mount(element);
        const event = new Event('trix-file-accept', { bubbles: true, cancelable: true });
        Object.defineProperty(event, 'file', { value: new File(['x'], 'x.txt') });

        element.querySelector('trix-editor').dispatchEvent(event);

        expect(event.defaultPrevented).toBe(true);
    });

    it('keeps Trix sanitization on programmatic HTML values', () => {
        const element = root();
        const instance = mount(element);

        expect(instance.setValue('<p>Safe</p><img src="x" onerror="alert(1)"><script>alert(2)</script><a href="javascript:alert(3)">Link</a>')).toBe(true);

        expect(instance.getValue()).toContain('Safe');
        expect(instance.getValue()).not.toMatch(/<script|onerror|javascript:/i);
    });

    it('sanitizes the initial HTML value before exposing it', () => {
        const element = root({ value: '<p>Initial safe</p><img src=x onerror=alert(1)><script>alert(2)</script>' });
        const instance = mount(element);

        expect(instance.getValue()).toContain('Initial safe');
        expect(instance.getValue()).not.toMatch(/<script|onerror/i);
    });

    it('applies disabled, readonly, required and autofocus semantics', () => {
        const element = root({ autofocus: true, disabled: true, readonly: true, required: true });
        const instance = mount(element);
        const editor = element.querySelector('trix-editor');

        expect(editor.hasAttribute('autofocus')).toBe(true);
        expect(editor.hasAttribute('disabled')).toBe(true);
        expect(editor.hasAttribute('required')).toBe(true);
        expect(editor.getAttribute('aria-readonly')).toBe('true');
        expect(editor.getAttribute('contenteditable')).toBe('false');
        expect(element.querySelector('[data-daisy-kit-wysiwyg-validation]').required).toBe(true);
        expect([...element.querySelectorAll('trix-toolbar button')].every((button) => button.disabled)).toBe(true);
        expect(instance.focus()).toBe(false);
    });

    it('restores the initial document and attachment validity on native form reset', async () => {
        const form = document.createElement('form');
        document.body.append(form);
        const element = root({ attachments: true });
        form.append(element);
        const instance = mount(element);
        const pending = attachment();
        dispatchAttachment(element.querySelector('trix-editor'), 'attachment-add', pending);
        instance.setValue('<p>Changed</p>');

        form.reset();
        await Promise.resolve();

        expect(instance.getValue()).toContain('Initial');
        expect(instance.getAttachments()).toEqual([]);
        expect(pending.releaseFile).toHaveBeenCalledOnce();
        expect(instance.undo()).toBe(false);
        expect(element.querySelector('[data-daisy-kit-wysiwyg-validation]').validationMessage).toBe('');
    });

    it('moves focus through toolbar actions with arrow and boundary keys', () => {
        const element = root();
        mount(element);
        const buttons = [...element.querySelectorAll('trix-toolbar button')];

        buttons[0].focus();
        buttons[0].dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        expect(document.activeElement).toBe(buttons[1]);
        buttons[1].dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'Home' }));
        expect(document.activeElement).toBe(buttons[0]);
        buttons[0].dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'End' }));
        expect(document.activeElement).toBe(buttons.at(-1));
    });

    it('coordinates pending attachments through the public facade', () => {
        const element = root({ attachments: true, required: true });
        const instance = mount(element);
        const editor = element.querySelector('trix-editor');
        const added = vi.fn();
        const resolved = vi.fn();
        const progressed = vi.fn();
        element.addEventListener('daisy-kit:wysiwyg:attachment-add', added);
        element.addEventListener('daisy-kit:wysiwyg:attachment-resolved', resolved);
        element.addEventListener('daisy-kit:wysiwyg:attachment-progress', progressed);
        const pending = attachment();

        dispatchAttachment(editor, 'attachment-add', pending);

        expect(added.mock.calls[0][0].detail).toMatchObject({ id: '42', name: 'diagram.png', size: 1200, type: 'image/png' });
        expect(added.mock.calls[0][0].detail.file).toBe(pending.file);
        expect(instance.getAttachments()).toEqual([expect.objectContaining({ id: '42', pending: true })]);
        expect(element.querySelector('[data-daisy-kit-wysiwyg-validation]').validationMessage).toBe('Pending attachment');
        expect(element.querySelector('[data-daisy-kit-wysiwyg-validation]').checkValidity()).toBe(false);
        expect(editor.getAttribute('aria-invalid')).toBe('true');

        expect(instance.setAttachmentProgress('42', 45)).toBe(true);
        expect(pending.setUploadProgress).toHaveBeenCalledWith(45);
        expect(progressed.mock.calls[0][0].detail).toEqual({ id: '42', progress: 45 });
        expect(instance.resolveAttachment('42', { url: 'javascript:alert(1)' })).toBe(false);
        expect(instance.resolveAttachment('42', { url: 'https://example.test/diagram.png' })).toBe(true);
        expect(pending.setAttributes).toHaveBeenCalledWith({ href: 'https://example.test/diagram.png', url: 'https://example.test/diagram.png' });
        expect(instance.getAttachments()[0].pending).toBe(false);
        expect(element.querySelector('[data-daisy-kit-wysiwyg-validation]').validationMessage).toBe('');
        expect(element.querySelector('[data-daisy-kit-wysiwyg-validation]').checkValidity()).toBe(true);
        expect(editor.getAttribute('aria-invalid')).toBe('false');
        expect(resolved).toHaveBeenCalledOnce();
    });

    it('re-emits attachment edits, removals, and custom actions without leaking Trix attachments', () => {
        const element = root({ attachments: true });
        const instance = mount(element);
        const editor = element.querySelector('trix-editor');
        const edited = vi.fn();
        const removed = vi.fn();
        const action = vi.fn();
        element.addEventListener('daisy-kit:wysiwyg:attachment-edit', edited);
        element.addEventListener('daisy-kit:wysiwyg:attachment-remove', removed);
        element.addEventListener('daisy-kit:wysiwyg:action', action);
        const value = attachment(7);
        dispatchAttachment(editor, 'attachment-add', value);

        dispatchAttachment(editor, 'attachment-edit', value);
        dispatchAttachment(editor, 'attachment-remove', value);
        const actionEvent = new Event('trix-action-invoke', { bubbles: true });
        Object.defineProperty(actionEvent, 'actionName', { value: 'x-preview' });
        editor.dispatchEvent(actionEvent);

        expect(edited.mock.calls[0][0].detail).toEqual(expect.objectContaining({ id: '7' }));
        expect(removed.mock.calls[0][0].detail).toEqual(expect.objectContaining({ id: '7' }));
        expect(value.releaseFile).toHaveBeenCalledOnce();
        expect(edited.mock.calls[0][0].detail).not.toHaveProperty('attachment');
        expect(action).toHaveBeenCalledOnce();
        expect(instance.getAttachments()).toEqual([]);
    });
});
