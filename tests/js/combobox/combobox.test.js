import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, mountAll, unmount } from '../../../resources/js/combobox.js';

function markup(configuration) {
    return `<section data-daisy-kit-module="combobox"><p data-daisy-kit-status hidden role="alert"></p><input data-daisy-kit-combobox-input role="combobox"><ul data-daisy-kit-combobox-listbox role="listbox" hidden></ul><div data-daisy-kit-combobox-tokens></div><div data-daisy-kit-combobox-values></div>${configuration.required ? '<input data-daisy-kit-combobox-required required>' : ''}<script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script></section>`;
}

describe('combobox', () => {
    afterEach(() => vi.unstubAllGlobals());

    it('filters locally, emits changes, and serializes repeated Laravel fields', () => {
        document.body.innerHTML = markup({ name: 'users', multiple: true, allowCustom: true, options: [{ value: 'ada', label: 'Ada Lovelace' }, { value: 'grace', label: 'Grace Hopper' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const changes = vi.fn(); root.addEventListener('daisy-kit:combobox:change', changes);
        const instance = mount(root);
        const input = root.querySelector('input'); input.value = 'ada'; input.dispatchEvent(new Event('input'));
        root.querySelector('[data-value="ada"]').click();

        expect(instance.getValue()).toEqual(['ada']);
        expect(root.querySelector('[name="users[]"]').value).toBe('ada');
        expect(changes).toHaveBeenCalledOnce();
        expect(changes.mock.calls[0][0].detail).toEqual({ value: ['ada'], values: ['ada'] });
        unmount(root); expect(getInstance(root)).toBeNull();
    });

    it('aborts a stale remote request', async () => {
        const pending = new Promise(() => {}); vi.stubGlobal('fetch', vi.fn().mockReturnValue(pending));
        document.body.innerHTML = markup({ source: '/users', minChars: 1, debounce: 0 });
        const root = document.querySelector('[data-daisy-kit-module]'); const input = root.querySelector('input'); mount(root);
        input.value = 'a'; input.dispatchEvent(new Event('input')); await new Promise((resolve) => setTimeout(resolve));
        input.value = 'ad'; input.dispatchEvent(new Event('input')); await new Promise((resolve) => setTimeout(resolve));
        expect(fetch.mock.calls[0][1].signal.aborted).toBe(true);
        unmount(root); vi.unstubAllGlobals();
    });

    it('announces the active option, validates selection, and accepts pasted custom tokens', () => {
        document.body.innerHTML = markup({ name: 'tags', multiple: true, required: true, allowCustom: true, tokenSeparators: [','], options: [{ value: 'ada', label: 'Ada' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const input = root.querySelector('[data-daisy-kit-combobox-input]');
        const required = root.querySelector('[data-daisy-kit-combobox-required]');
        const instance = mount(root);

        input.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowDown' }));
        expect(input.getAttribute('aria-activedescendant')).toContain('option-0');
        expect(required.checkValidity()).toBe(false);

        const paste = new Event('paste', { bubbles: true, cancelable: true });
        Object.defineProperty(paste, 'clipboardData', { value: { getData: () => 'alpha,beta,alpha' } });
        input.dispatchEvent(paste);

        expect(instance.getValue()).toEqual(['alpha', 'beta']);
        expect(required.checkValidity()).toBe(true);
        expect([...root.querySelectorAll('[name="tags[]"]')].map((field) => field.value)).toEqual(['alpha', 'beta']);
    });

    it('returns booleans from synchronous commands and keeps its facade stable', () => {
        document.body.innerHTML = markup({ multiple: false, options: [{ value: 'ada', label: 'Ada' }] });
        const root = document.querySelector('[data-daisy-kit-module]');

        const instance = mount(root);

        expect(mount(root)).toBe(instance);
        expect(mountAll(document)).toEqual([instance]);
        expect(Object.keys(instance).sort()).toEqual(['clear', 'close', 'getValue', 'open', 'refresh', 'setValue']);
        expect(instance.setValue('ada')).toBe(true);
        expect(instance.clear()).toBe(true);
        expect(instance.open()).toBe(true);
        expect(instance.close()).toBe(true);
    });

    it('emits loading start and finish and returns true for a valid remote refresh', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: true, json: async () => ({ items: [{ value: 'ada', label: 'Ada' }] }) }));
        document.body.innerHTML = markup({ source: '/users', minChars: 0, debounce: 0 });
        const root = document.querySelector('[data-daisy-kit-module]');
        const loading = [];
        root.addEventListener('daisy-kit:combobox:loading', (event) => loading.push(event.detail));

        await expect(mount(root).refresh()).resolves.toBe(true);

        expect(loading).toEqual([
            { loading: true, query: '' },
            { loading: false, query: '' },
        ]);
    });

    it('does not announce loading completion for a stale request while its replacement is active', async () => {
        let resolveFirst;
        let resolveSecond;
        const firstResponse = new Promise((resolve) => { resolveFirst = resolve; });
        const secondResponse = new Promise((resolve) => { resolveSecond = resolve; });
        vi.stubGlobal('fetch', vi.fn()
            .mockReturnValueOnce(firstResponse)
            .mockReturnValueOnce(secondResponse));
        document.body.innerHTML = markup({ source: '/users', minChars: 1, debounce: 100000 });
        const root = document.querySelector('[data-daisy-kit-module]');
        const input = root.querySelector('[data-daisy-kit-combobox-input]');
        const loading = [];
        root.addEventListener('daisy-kit:combobox:loading', (event) => loading.push(event.detail));
        const instance = mount(root);
        input.value = 'a';
        input.dispatchEvent(new Event('input'));
        const firstRefresh = instance.refresh();
        input.value = 'ada';
        input.dispatchEvent(new Event('input'));
        const secondRefresh = instance.refresh();

        resolveFirst({ ok: true, json: async () => ({ items: [] }) });
        await expect(firstRefresh).resolves.toBe(false);

        expect(loading).toEqual([
            { loading: true, query: 'a' },
            { loading: true, query: 'ada' },
        ]);
        expect(root.getAttribute('aria-busy')).toBe('true');

        resolveSecond({ ok: true, json: async () => ({ items: [] }) });
        await expect(secondRefresh).resolves.toBe(true);
        expect(loading.at(-1)).toEqual({ loading: false, query: 'ada' });
        expect(root.hasAttribute('aria-busy')).toBe(false);
        unmount(root);
    });

    it('ignores an obsolete HTTP error before parsing or emitting it', async () => {
        let resolveFirst;
        let resolveSecond;
        const firstResponse = new Promise((resolve) => { resolveFirst = resolve; });
        const secondResponse = new Promise((resolve) => { resolveSecond = resolve; });
        const staleJson = vi.fn(async () => ({ invalid: true }));
        vi.stubGlobal('fetch', vi.fn()
            .mockReturnValueOnce(firstResponse)
            .mockReturnValueOnce(secondResponse));
        document.body.innerHTML = markup({ source: '/users', minChars: 1, debounce: 100000 });
        const root = document.querySelector('[data-daisy-kit-module]');
        const input = root.querySelector('[data-daisy-kit-combobox-input]');
        const errors = [];
        const loading = [];
        root.addEventListener('daisy-kit:combobox:error', (event) => errors.push(event.detail));
        root.addEventListener('daisy-kit:combobox:loading', (event) => loading.push(event.detail));
        const instance = mount(root);
        input.value = 'a';
        input.dispatchEvent(new Event('input'));
        const firstRefresh = instance.refresh();
        input.value = 'ada';
        input.dispatchEvent(new Event('input'));
        const secondRefresh = instance.refresh();

        resolveFirst({ ok: false, status: 503, json: staleJson });
        await expect(firstRefresh).resolves.toBe(false);

        expect(staleJson).not.toHaveBeenCalled();
        expect(errors).toEqual([]);
        expect(loading).toEqual([
            { loading: true, query: 'a' },
            { loading: true, query: 'ada' },
        ]);

        resolveSecond({ ok: true, json: async () => ({ items: [] }) });
        await secondRefresh;
        unmount(root);
    });

    it('ignores an obsolete invalid JSON result when it becomes stale during parsing', async () => {
        let rejectFirstJson;
        let resolveSecond;
        const firstJson = new Promise((resolve, reject) => { rejectFirstJson = reject; });
        const secondResponse = new Promise((resolve) => { resolveSecond = resolve; });
        vi.stubGlobal('fetch', vi.fn()
            .mockResolvedValueOnce({ ok: true, json: () => firstJson })
            .mockReturnValueOnce(secondResponse));
        document.body.innerHTML = markup({ source: '/users', minChars: 1, debounce: 100000 });
        const root = document.querySelector('[data-daisy-kit-module]');
        const input = root.querySelector('[data-daisy-kit-combobox-input]');
        const errors = [];
        const loading = [];
        root.addEventListener('daisy-kit:combobox:error', (event) => errors.push(event.detail));
        root.addEventListener('daisy-kit:combobox:loading', (event) => loading.push(event.detail));
        const instance = mount(root);
        input.value = 'a';
        input.dispatchEvent(new Event('input'));
        const firstRefresh = instance.refresh();
        await Promise.resolve();
        input.value = 'ada';
        input.dispatchEvent(new Event('input'));
        const secondRefresh = instance.refresh();

        rejectFirstJson(new SyntaxError('Invalid JSON'));
        await expect(firstRefresh).resolves.toBe(false);

        expect(errors).toEqual([]);
        expect(loading).toEqual([
            { loading: true, query: 'a' },
            { loading: true, query: 'ada' },
        ]);

        resolveSecond({ ok: true, json: async () => ({ items: [] }) });
        await secondRefresh;
        unmount(root);
    });

    it('reports a structured remote error without throwing', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: true, json: async () => ({ invalid: true }) }));
        document.body.innerHTML = markup({ source: '/users', minChars: 0, debounce: 0 });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:combobox:error', (event) => errors.push(event.detail));

        await expect(mount(root).refresh()).resolves.toBe(false);

        expect(errors).toEqual([{
            code: 'invalid-response',
            message: 'The combobox source returned an invalid response.',
            query: '',
        }]);
    });

    it('rejects values beyond the configured maximum without changing the selection', () => {
        document.body.innerHTML = markup({ maxItems: 1, multiple: true, options: [{ value: 'ada' }, { value: 'grace' }], value: ['ada'] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:combobox:error', (event) => errors.push(event.detail));
        const instance = mount(root);

        expect(instance.setValue(['ada', 'grace'])).toBe(false);

        expect(instance.getValue()).toEqual(['ada']);
        expect(errors).toEqual([{
            code: 'max-items',
            maxItems: 1,
            message: 'The combobox cannot contain more than 1 item.',
            values: ['ada'],
        }]);
    });
});
