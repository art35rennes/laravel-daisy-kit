/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';

import initSelect from '../../../resources/js/modules/select.js';

describe('select module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        vi.restoreAllMocks();
    });

    it('does not enhance the hidden native select inside an enhanced wrapper twice', () => {
        document.body.innerHTML = `
            <div data-module="select" class="dropdown w-full">
                <label class="input flex w-full items-center gap-2">
                    <input type="text" data-role="input" class="grow" autocomplete="off">
                </label>
                <ul data-role="list" role="listbox" class="dropdown-content hidden"></ul>
                <select data-module="select" data-role="native" hidden>
                    <option value="">Choisir...</option>
                    <option value="fr">France</option>
                </select>
            </div>
        `;

        const wrapper = document.querySelector('[data-module="select"]');
        const nativeSelect = wrapper.querySelector('select');

        initSelect(wrapper);
        initSelect(nativeSelect);

        expect(document.querySelectorAll('[data-role="input"]')).toHaveLength(1);
        expect(document.querySelectorAll('.dropdown')).toHaveLength(1);
    });

    it('filters local search options from the hidden native select', async () => {
        document.body.innerHTML = `
            <div data-module="select" class="dropdown w-full" data-debounce="1">
                <label class="input flex w-full items-center gap-2">
                    <input type="text" data-role="input" class="grow" autocomplete="off">
                </label>
                <ul data-role="list" role="listbox" class="dropdown-content hidden"></ul>
                <select data-role="native" hidden>
                    <option value="">Choose...</option>
                    <option value="laravel">Laravel</option>
                    <option value="livewire">Livewire</option>
                    <option value="alpine">Alpine.js</option>
                </select>
            </div>
        `;

        const wrapper = document.querySelector('[data-module="select"]');
        const input = wrapper.querySelector('[data-role="input"]');

        initSelect(wrapper);

        input.value = 'la';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        await new Promise((resolve) => setTimeout(resolve, 5));

        const options = Array.from(wrapper.querySelectorAll('button[role="option"]')).map((button) => button.textContent);

        expect(options).toEqual(['Laravel']);
        expect(wrapper.classList.contains('dropdown-open')).toBe(true);
    });

    it('renders and updates semantic color swatches', () => {
        document.body.innerHTML = `
            <div data-module="select" class="dropdown w-full">
                <label class="input flex w-full items-center gap-2">
                    <span data-role="swatch" class="h-3 w-3 shrink-0 rounded-full bg-primary" aria-hidden="true"></span>
                    <input type="text" data-role="input" class="grow" autocomplete="off">
                </label>
                <ul data-role="list" role="listbox" class="dropdown-content hidden"></ul>
                <select data-role="native" hidden>
                    <option value="primary" data-swatch="primary" selected>Traitement courant</option>
                    <option value="warning" data-swatch="warning">A surveiller</option>
                </select>
            </div>
        `;

        const wrapper = document.querySelector('[data-module="select"]');
        const input = wrapper.querySelector('[data-role="input"]');
        const selectedSwatch = wrapper.querySelector('[data-role="swatch"]');

        initSelect(wrapper);
        input.value = '';
        input.dispatchEvent(new Event('focus'));

        expect(wrapper.querySelector('[data-select-swatch="warning"]')).not.toBeNull();

        const warningOption = Array.from(wrapper.querySelectorAll('button[role="option"]'))
            .find((option) => option.textContent.includes('A surveiller'));

        warningOption.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(selectedSwatch.classList.contains('bg-warning')).toBe(true);
        expect(selectedSwatch.classList.contains('bg-primary')).toBe(false);
    });

    it('shows every local option without allowing a search query when configured', () => {
        document.body.innerHTML = `
            <div data-module="select" class="dropdown w-full" data-searchable="false">
                <label class="input flex w-full items-center gap-2">
                    <input type="text" data-role="input" class="grow" autocomplete="off">
                </label>
                <ul data-role="list" role="listbox" class="dropdown-content hidden"></ul>
                <select data-role="native" hidden>
                    <option value="primary" selected>Traitement courant</option>
                    <option value="warning">A surveiller</option>
                </select>
            </div>
        `;

        const wrapper = document.querySelector('[data-module="select"]');
        const input = wrapper.querySelector('[data-role="input"]');

        initSelect(wrapper);
        input.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(input.readOnly).toBe(true);
        expect(wrapper.querySelectorAll('button[role="option"]')).toHaveLength(2);
        expect(wrapper.classList.contains('dropdown-open')).toBe(true);
    });

    it('renders remote autocomplete results once and syncs the native select', async () => {
        document.body.innerHTML = `
            <div data-module="select" class="dropdown w-full" data-endpoint="/api/tags" data-param="search" data-min-chars="1" data-debounce="1">
                <label class="input flex w-full items-center gap-2">
                    <input type="text" data-role="input" class="grow" autocomplete="off">
                </label>
                <ul data-role="list" role="listbox" class="dropdown-content hidden"></ul>
                <select data-role="native" hidden>
                    <option value="">Choose...</option>
                </select>
            </div>
        `;

        global.fetch = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => [{ value: 'laravel', label: 'Laravel' }],
        });

        const wrapper = document.querySelector('[data-module="select"]');
        const input = wrapper.querySelector('[data-role="input"]');
        const nativeSelect = wrapper.querySelector('select');

        initSelect(wrapper);

        input.value = 'la';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        await new Promise((resolve) => setTimeout(resolve, 5));

        const optionButtons = wrapper.querySelectorAll('button[role="option"]');

        expect(optionButtons).toHaveLength(1);
        expect(optionButtons[0].textContent).toContain('Laravel');

        optionButtons[0].dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(input.value).toBe('Laravel');
        expect(nativeSelect.value).toBe('laravel');
        expect(nativeSelect.querySelectorAll('option[value="laravel"]')).toHaveLength(1);
    });
});
