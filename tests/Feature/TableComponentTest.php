<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders the table as a CSP-safe explicitly mounted module', function (): void {
    $html = view('daisy-kit::components.table', [
        'columns' => [
            ['id' => 'name', 'label' => 'Name'],
        ],
        'rows' => [
            ['name' => '</script><img src=x onerror=alert(1)>'],
        ],
        'pageSize' => 20,
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)->toContain('data-daisy-kit-module="table"')
        ->toContain('data-daisy-kit-content')
        ->toContain('aria-busy="true"')
        ->toContain('data-daisy-kit-status')
        ->not->toContain('<img')
        ->not->toContain('style=')
        ->not->toContain('x-daisy::')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? '')))->toBe([
            'columns' => [['id' => 'name', 'label' => 'Name']],
            'rows' => [['name' => '</script><img src=x onerror=alert(1)>']],
            'pageSize' => 20,
        ]);
});

it('renders a semantic table shell for an empty dataset', function (): void {
    $html = view('daisy-kit::components.table', [
        'columns' => [],
        'rows' => [],
    ])->render();

    expect($html)->toContain('<table')
        ->toContain('<thead')
        ->toContain('<tbody')
        ->toContain('role="status"')
        ->toContain('aria-live="polite"');
});
