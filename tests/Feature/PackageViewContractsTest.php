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
        ->toContain("const THEME_KEY = 'daisy-theme';");
});
