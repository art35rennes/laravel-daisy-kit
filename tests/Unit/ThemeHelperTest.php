<?php

use Art35rennes\DaisyKit\Helpers\ThemeHelper;
use Illuminate\Support\Facades\Blade;

covers(ThemeHelper::class);

it('returns an empty CSS payload when no custom themes are configured', function () {
    config(['daisy-kit.themes.custom' => []]);

    expect(ThemeHelper::generateCustomThemesCss())->toBe('');
});

it('generates daisyUI theme CSS from custom theme configuration', function () {
    config([
        'daisy-kit.themes.custom' => [
            'brand' => [
                'name' => 'brand-theme',
                'default' => true,
                'prefersdark' => true,
                'color-scheme' => 'dark',
                'colors' => [
                    'primary' => 'oklch(55% 0.3 240)',
                    'base-100' => 'oklch(12% 0.02 240)',
                ],
                'radius' => [
                    'selector' => '0.5rem',
                ],
                'size' => [
                    'field' => '0.25rem',
                ],
                'border' => '2px',
                'depth' => 1,
                'noise' => 0,
            ],
        ],
    ]);

    $css = ThemeHelper::generateCustomThemesCss();

    expect($css)
        ->toContain('@plugin "daisyui/theme"')
        ->toContain('name: "brand-theme";')
        ->toContain('default: true;')
        ->toContain('prefersdark: true;')
        ->toContain('color-scheme: "dark";')
        ->toContain('--color-primary: oklch(55% 0.3 240);')
        ->toContain('--color-base-100: oklch(12% 0.02 240);')
        ->toContain('--radius-selector: 0.5rem;')
        ->toContain('--size-field: 0.25rem;')
        ->toContain('--border: 2px;')
        ->toContain('--depth: 1;')
        ->toContain('--noise: 0;');
});

it('returns built-in and custom themes without duplicates', function () {
    config([
        'daisy-kit.themes.builtin' => [
            'light' => ['default' => true],
            'dark' => ['prefersdark' => true],
            'cupcake',
            'dark',
        ],
        'daisy-kit.themes.custom' => [
            'brand' => ['name' => 'brand'],
            'dark' => ['name' => 'dark'],
        ],
    ]);

    expect(ThemeHelper::getAllThemes())->toBe([
        'light',
        'dark',
        'cupcake',
        'brand',
    ]);
});

it('pushes generated custom themes into the public partial output', function () {
    config([
        'daisy-kit.themes.custom' => [
            'brand' => [
                'colors' => [
                    'primary' => '#123456',
                ],
            ],
        ],
    ]);

    $html = Blade::render(<<<'BLADE'
        @include('daisy::components.partials.custom-themes')
        @stack('styles')
    BLADE);

    expect($html)
        ->toContain('<style>')
        ->toContain('--color-primary: #123456;');
});
