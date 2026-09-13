<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders the tree as a semantic CSP-safe module', function (): void {
    $html = view('daisy-kit::components.tree', [
        'items' => [
            [
                'id' => 'root',
                'label' => '</script><img src=x onerror=alert(1)>',
                'children' => [['id' => 'child', 'label' => 'Child']],
            ],
        ],
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)->toContain('data-daisy-kit-module="tree"')
        ->toContain('role="tree"')
        ->toContain('aria-label="Tree"')
        ->toContain('data-daisy-kit-status')
        ->not->toContain('<img')
        ->not->toContain('style=')
        ->not->toContain('x-daisy::')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? '')))->toMatchArray([
            'items' => [
                [
                    'id' => 'root',
                    'label' => '</script><img src=x onerror=alert(1)>',
                    'children' => [['id' => 'child', 'label' => 'Child', 'children' => []]],
                ],
            ],
            'multiple' => false,
            'name' => null,
            'persistenceKey' => null,
            'searchable' => false,
            'searchSource' => null,
        ]);
});

it('provides an accessible empty tree shell', function (): void {
    $html = view('daisy-kit::components.tree', ['items' => []])->render();

    expect($html)->toContain('role="tree"')
        ->not->toContain('tabindex="0"')
        ->toContain('role="status"')
        ->toContain('aria-live="polite"');
});

it('renders one JSON-backed Laravel field in single and multiple modes', function (bool $multiple): void {
    $html = view('daisy-kit::components.tree', [
        'items' => [['id' => 'docs', 'label' => 'Documentation']],
        'multiple' => $multiple,
        'name' => 'areas',
    ])->render();

    expect($html)->toContain('name="areas"')
        ->not->toContain('name="areas[]"')
        ->toContain('data-daisy-kit-tree-value')
        ->toContain('value="[]"');
})->with([false, true]);
