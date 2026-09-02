import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, mountAll, unmount } from '../../../resources/js/combobox.js';

function markup(configuration) {
    return `<section data-daisy-kit-module="combobox"><p data-daisy-kit-status hidden role="alert"></p><div data-daisy-kit-combobox-shell><div data-daisy-kit-combobox-control><div data-daisy-kit-combobox-tokens></div><input data-daisy-kit-combobox-input role="combobox"><button data-daisy-kit-combobox-toggle type="button"></button></div><div data-daisy-kit-combobox-popup hidden><p data-daisy-kit-combobox-popup-status role="status" hidden></p><ul data-daisy-kit-combobox-listbox role="listbox"></ul></div></div><div data-daisy-kit-combobox-values></div>${configuration.required ? '<input data-daisy-kit-combobox-required required>' : ''}<script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script></section>`;
}

describe('combobox', () => {
    afterEach(() => vi.unstubAllGlobals());

    it('keeps the popup closed after Escape, Tab and a keyboard selection', () => {
        document.body.innerHTML = markup({ options: [{ value: 'ada', label: 'Ada' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const instance = mount(root);
        const input = root.querySelector('input');
        for (const key of ['Escape', 'Tab']) {
            instance.open();
            input.dispatchEvent(new KeyboardEvent('keydown', { key, bubbles: true }));
            expect(input.getAttribute('aria-expanded')).toBe('false');
        }
        input.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true }));
        input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
        expect(instance.getValue()).toBe('ada');
        expect(input.getAttribute('aria-expanded')).toBe('false');
        unmount(root);
    });

    it('filters locally, emits changes, and serializes repeated Laravel fields', () => {
        document.body.innerHTML = markup({ name: 'users', multiple: true, allowCustom: true, options: [{ value: 'ada', label: 'Ada Lovelace' }, { value: 'grace', label: 'Grace Hopper' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const changes = vi.fn(); root.addEventListener('daisy-kit:combobox:change', changes);
        const instance = mount(root);
        const input = root.querySelector('input'); input.value = 'ada'; input.dispatchEvent(new Event('input'));
        root.querySelector('[data-value="ada"]').click();

        expect(instance.getValue()).toEqual(['ada']);
        expect(root.querySelector('[data-daisy-kit-combobox-token]').getAttribute('aria-label')).toBe('Remove Ada Lovelace');
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

    it('restores its ARIA shell without rerendering after an in-flight unmount', async () => {
        let rejectRequest;
        vi.stubGlobal('fetch', vi.fn().mockReturnValue(new Promise((resolve, reject) => {
            rejectRequest = reject;
        })));
        document.body.innerHTML = markup({ source: '/users', minChars: 0, debounce: 0 });
        const root = document.querySelector('[data-daisy-kit-module]');
        const input = root.querySelector('[data-daisy-kit-combobox-input]');
        const listbox = root.querySelector('[data-daisy-kit-combobox-listbox]');
        const popup = root.querySelector('[data-daisy-kit-combobox-popup]');
        const toggle = root.querySelector('[data-daisy-kit-combobox-toggle]');
        const instance = mount(root);
        const refresh = instance.refresh();

        expect(root.getAttribute('aria-busy')).toBe('true');
        expect(unmount(root)).toBe(true);
        expect(input.hasAttribute('aria-controls')).toBe(false);
        expect(listbox.hasAttribute('aria-multiselectable')).toBe(false);
        expect(toggle.hasAttribute('aria-controls')).toBe(false);
        expect(popup.hidden).toBe(true);

        rejectRequest(new DOMException('Aborted', 'AbortError'));
        await expect(refresh).resolves.toBe(false);

        expect(root.hasAttribute('aria-busy')).toBe(false);
        expect(input.hasAttribute('aria-expanded')).toBe(false);
        expect(popup.hidden).toBe(true);
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
        expect(Object.keys(instance).sort()).toEqual([
            'clear',
            'clearOptionRenderer',
            'close',
            'getValue',
            'open',
            'refresh',
            'setOptionRenderer',
            'setValue',
        ]);
        expect(instance.setValue('ada')).toBe(true);
        expect(instance.clear()).toBe(true);
        expect(instance.open()).toBe(true);
        expect(instance.close()).toBe(true);
    });

    it('loads and opens remote suggestions on first focus when an empty query is allowed', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ items: [{ value: 'ada', label: 'Ada Lovelace' }] }),
        }));
        document.body.innerHTML = markup({ source: '/reviewers', minChars: 0, debounce: 0 });
        const root = document.querySelector('[data-daisy-kit-module]');
        const input = root.querySelector('[data-daisy-kit-combobox-input]');
        mount(root);

        input.dispatchEvent(new FocusEvent('focus'));
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        await vi.waitFor(() => expect(root.querySelector('[role=option]')?.textContent).toContain('Ada Lovelace'));
        input.dispatchEvent(new FocusEvent('focus'));

        expect(fetch.mock.calls[0][0].toString()).toContain('query=');
        expect(fetch).toHaveBeenCalledOnce();
        expect(input.getAttribute('aria-expanded')).toBe('true');
        expect(root.querySelector('[data-daisy-kit-combobox-popup]').hidden).toBe(false);
    });

    it('renders rich person suggestions as safe structured content and bounds the result list', () => {
        document.body.innerHTML = markup({
            maxSuggestions: 2,
            options: [
                { value: 'ada', label: 'Ada Lovelace', description: 'ada@example.test', meta: 'Platform', initials: 'AL', avatar: '/ada.jpg' },
                { value: 'grace', label: 'Grace Hopper', description: 'grace@example.test', initials: 'GH' },
                { value: 'margaret', label: 'Margaret Hamilton' },
            ],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        mount(root).open();

        expect(root.querySelectorAll('[role=option]')).toHaveLength(2);
        expect(root.querySelector('[role=option] img')?.getAttribute('src')).toBe('/ada.jpg');
        expect(root.querySelector('[role=option]')?.textContent).toContain('ada@example.test');
        expect(root.querySelector('[role=option]')?.textContent).toContain('Platform');
        expect(root.querySelectorAll('[data-daisy-kit-combobox-avatar-fallback]')).toHaveLength(2);
    });

    it('supports a host option renderer while retaining the semantic option wrapper', () => {
        document.body.innerHTML = markup({ options: [{ value: 'ada', label: 'Ada Lovelace', description: 'ada@example.test' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const instance = mount(root);
        const renderer = vi.fn((option, context) => {
            expect(Object.isFrozen(option)).toBe(true);
            expect(Object.isFrozen(context)).toBe(true);
            const content = document.createElement('strong');
            content.textContent = `${option.label} (${context.selected ? 'selected' : 'available'})`;

            return content;
        });

        expect(instance.setOptionRenderer(renderer)).toBe(true);
        expect(root.querySelector('[role=option] strong')?.textContent).toBe('Ada Lovelace (available)');
        expect(root.querySelector('[role=option]')?.getAttribute('aria-selected')).toBe('false');
        expect(instance.clearOptionRenderer()).toBe(true);
        expect(root.querySelector('[role=option] strong')).toBeNull();
        expect(instance.setOptionRenderer('unsafe')).toBe(false);
    });

    it('falls back safely and reports a structured error when a host renderer throws', () => {
        document.body.innerHTML = markup({ options: [{ value: 'ada', label: 'Ada Lovelace' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:combobox:error', (event) => errors.push(event.detail));
        const instance = mount(root);

        expect(instance.setOptionRenderer(() => { throw new Error('Host renderer failed.'); })).toBe(true);

        expect(root.querySelector('[role=option]')?.textContent).toContain('Ada Lovelace');
        instance.open();
        expect(errors).toEqual([{
            code: 'option-render-failed',
            message: 'Host renderer failed.',
            value: 'ada',
        }]);
    });

    it('keeps multiple suggestions open and removes the last token with empty-input Backspace', () => {
        document.body.innerHTML = markup({ multiple: true, options: [{ value: 'ada', label: 'Ada' }, { value: 'grace', label: 'Grace' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const input = root.querySelector('[data-daisy-kit-combobox-input]');
        const instance = mount(root);
        instance.open();
        root.querySelector('[data-value=ada]').click();

        expect(input.getAttribute('aria-expanded')).toBe('true');
        expect(document.activeElement).toBe(input);

        input.value = '';
        input.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'Backspace' }));
        expect(instance.getValue()).toEqual([]);
    });

    it('keeps an empty state visible instead of silently closing the popup', () => {
        document.body.innerHTML = markup({ labels: { noResults: 'No matching reviewers.' }, options: [] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const instance = mount(root);

        expect(instance.open()).toBe(true);

        expect(root.querySelector('[data-daisy-kit-combobox-popup]').hidden).toBe(false);
        expect(root.querySelector('[data-daisy-kit-combobox-popup-status]').textContent).toBe('No matching reviewers.');
        expect(root.querySelector('[data-daisy-kit-combobox-popup-status]').hidden).toBe(false);
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
