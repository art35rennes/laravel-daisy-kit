import { afterEach, describe, expect, it, vi } from 'vitest';
import { mount, unmount, getInstance } from '../../../resources/js/code-editor.js';

const roots = [];
function fixture(config = {}) {
    const root = document.createElement('fieldset');
    root.dataset.daisyKitModule = 'code-editor';
    root.innerHTML = '<legend>Code</legend><textarea name="code">initial</textarea><div data-code-editor-host></div><div data-code-editor-toolbar hidden></div><output data-code-editor-position></output><span data-code-editor-language></span><span data-code-editor-feedback></span><p data-daisy-kit-status hidden></p>';
    const script = document.createElement('script');
    script.type = 'application/json';
    script.dataset.daisyKitConfig = '';
    script.textContent = JSON.stringify(config);
    root.append(script);
    document.body.append(root);
    roots.push(root);
    return root;
}
afterEach(() => roots.splice(0).forEach(root => { unmount(root); root.remove(); }));

describe('code editor native value contract', () => {
    it('mounts once and preserves current content on destruction', () => {
        const root = fixture();
        const editor = mount(root);
        expect(editor).not.toBeNull();
        expect(mount(root)).toBe(editor);
        expect(getInstance(root)).toBe(editor);
        expect(editor.getValue()).toBe('initial');
        expect(editor.setValue('</textarea><script>alert(1)</script>')).toBe(true);
        expect(root.querySelector('textarea').value).toContain('</textarea>');
        expect(unmount(root)).toBe(true);
        expect(root.querySelector('.cm-editor')).toBeNull();
        expect(root.querySelector('textarea').value).toContain('</textarea>');
        expect(editor.setValue('late')).toBe(false);
    });

    it('emits one change per replacement and resets history', () => {
        const root = fixture();
        const changes = [];
        root.addEventListener('daisy-kit:code-editor:change', event => changes.push(event.detail));
        const editor = mount(root);
        editor.setValue('next');
        editor.setValue('next');
        expect(changes).toEqual([{ value: 'next', origin: 'api' }]);
        expect(editor.undo()).toBe(false);
    });

    it('keeps instances independent and supports read-only reconfiguration', () => {
        const first = mount(fixture({ readOnly: true }));
        const second = mount(fixture());
        expect(first.getState().readOnly).toBe(true);
        expect(first.setReadOnly(false)).toBe(true);
        expect(first.setLineWrapping(true)).toBe(true);
        first.setValue('one');
        expect(second.getValue()).toBe('initial');
        expect(first.getState().lineWrapping).toBe(true);
    });

    it('loads a grammar and reports unsupported languages without losing content', async () => {
        const editor = mount(fixture());
        expect(await editor.setLanguage('json')).toBe(true);
        expect(editor.getState().language).toBe('json');
        expect(await editor.setLanguage('unknown')).toBe(false);
        expect(editor.getValue()).toBe('initial');
    });

    it('resets the document after native form reset and respects cancellation', async () => {
        const root = fixture();
        const form = document.createElement('form');
        root.before(form);
        form.append(root);
        const editor = mount(root);
        editor.setValue('edited');
        expect(new FormData(form).get('code')).toBe('edited');
        form.reset();
        await Promise.resolve();
        expect(editor.getValue()).toBe('initial');
        expect(root.querySelector('.cm-content').textContent).toBe('initial');
        form.addEventListener('reset', event => event.preventDefault());
        editor.setValue('keep');
        form.reset();
        await Promise.resolve();
        expect(editor.getValue()).toBe('keep');
        unmount(root);
        form.remove();
    });

    it('does not apply stale grammar loads or update destroyed editors', async () => {
        const root = fixture();
        const editor = mount(root);
        const slow = editor.setLanguage('php');
        const last = editor.setLanguage('text');
        expect(await last).toBe(true);
        expect(await slow).toBe(false);
        const destroyed = editor.setLanguage('yaml');
        unmount(root);
        expect(await destroyed).toBe(false);
    });

    it('tracks a disabled parent fieldset and restores keyboard access', async () => {
        const root = fixture();
        const editor = mount(root);
        root.disabled = true;
        await Promise.resolve();
        expect(editor.focus()).toBe(false);
        expect(root.querySelector('.cm-content').getAttribute('contenteditable')).toBe('false');
        root.disabled = false;
        await Promise.resolve();
        expect(editor.focus()).toBe(true);
    });

    it('copies the complete document and reports clipboard denial', async () => {
        const writeText = vi.fn().mockResolvedValue(undefined);
        Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText } });
        const root = fixture();
        const editor = mount(root);
        expect(await editor.copy()).toBe(true);
        expect(writeText).toHaveBeenCalledWith('initial');
        expect(root.querySelector('[data-code-editor-feedback]').textContent).toBe('Copied');
        writeText.mockRejectedValue(new Error('Denied'));
        const errors = [];
        root.addEventListener('daisy-kit:code-editor:error', event => errors.push(event.detail.code));
        expect(await editor.copy()).toBe(false);
        expect(errors).toEqual(['clipboard-unavailable']);
        delete navigator.clipboard;
    });
});
