<?php

use Illuminate\Support\Facades\Blade;

it('renders the package layout component through its public alias', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.custom' => [],
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Package Test">
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)
        ->toContain('<title>Package Test | Laravel</title>')
        ->toContain('Package body')
        ->toContain('name="csrf-token"');
});

it('applies the configured default theme to the package layout', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.default' => 'suez',
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Package Test">
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)->toContain('data-theme="suez"');
});

it('lets an explicit package layout theme override the configured default theme', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.default' => 'suez',
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Package Test" theme="dark">
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)
        ->toContain('data-theme="dark"')
        ->not->toContain('data-theme="suez"');
});

it('allows package layout themes to be disabled explicitly', function (string $themeExpression) {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.default' => 'suez',
    ]);

    $html = Blade::render(<<<BLADE
        <x-daisy::layout.app title="Package Test" {$themeExpression}>
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)->not->toContain('data-theme=');
})->with([
    'empty string' => 'theme=""',
    'false' => ':theme="false"',
]);

it('renders token-input through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.inputs.token-input name="recipients" :values="['alice@example.com']" />
    BLADE);

    expect($html)
        ->toContain('data-module="token-input"')
        ->toContain('name="recipients[]"')
        ->toContain('alice@example.com');
});

it('renders table through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :columns="[['key' => 'name', 'label' => 'Name']]"
            :rows="[['name' => 'Jane']]"
        />
    BLADE);

    expect($html)
        ->toContain('data-daisy-table="1"')
        ->toContain('Jane')
        ->toContain('Name');
});

it('renders ordered-list through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.layout.ordered-list :items="[['id' => 'plan', 'label' => 'Plan V2']]" />
    BLADE);

    expect($html)
        ->toContain('Plan V2')
        ->toContain('data-ordered-list="1"');
});

it('renders truncate text through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.utilities.truncate-text
            text="REF-2026-000001"
            max-width="max-w-48"
        />
    BLADE);

    expect($html)
        ->toContain('data-module="truncate-text"')
        ->toContain('data-truncate-text-title="REF-2026-000001"')
        ->toContain('class="min-w-0 max-w-48 truncate"')
        ->toContain('REF-2026-000001');
});

it('renders multiline truncate text and native title fallback', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.utilities.truncate-text
            text="A long customer-facing label"
            title="Full label"
            tag="p"
            :tooltip="false"
            :lines="3"
            class="text-sm"
        />
    BLADE);

    expect($html)
        ->toContain('<p')
        ->toContain('class="min-w-0 max-w-full line-clamp-3 text-sm"')
        ->toContain('title="Full label"')
        ->toContain('aria-label="A long customer-facing label"')
        ->not->toContain('data-module="truncate-text"')
        ->not->toContain('data-tip=');
});

it('renders the ui theme selector only when the package dev toggle is enabled', function () {
    config([
        'daisy-kit.dev.show_theme_selector' => true,
        'daisy-kit.themes.builtin' => ['light', 'dark'],
        'daisy-kit.themes.custom' => [
            'brand' => ['name' => 'brand'],
        ],
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.partials.theme-selector position="relative" placement="bottom-left" />
        @stack('scripts')
    BLADE);

    expect($html)
        ->toContain('relative bottom-4 left-4')
        ->toContain('theme-controller')
        ->toContain('brand')
        ->toContain('data-module="theme-controller"')
        ->not->toContain("const THEME_KEY = 'daisy-theme';");
});

it('renders the theme controller from configured themes and default theme', function () {
    config([
        'daisy-kit.themes.default' => 'brand',
        'daisy-kit.themes.builtin' => ['light', 'dark'],
        'daisy-kit.themes.custom' => [
            'brand' => ['name' => 'brand'],
        ],
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.advanced.theme-controller />
    BLADE);

    expect($html)
        ->toContain('data-module="theme-controller"')
        ->toContain('data-default-theme="brand"')
        ->toContain('value="light"')
        ->toContain('value="dark"')
        ->toContain('value="brand"')
        ->toContain('value="brand" class="join-item btn theme-controller btn-sm btn-ghost" aria-label="Brand" checked');
});
