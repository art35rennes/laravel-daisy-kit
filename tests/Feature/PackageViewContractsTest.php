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
