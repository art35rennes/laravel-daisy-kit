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
        'rowActions' => [['id' => 'approve', 'label' => 'Approve']],
        'rowDetails' => ['accessor' => 'summary', 'mode' => 'inline'],
        'editable' => ['columns' => ['name'], 'endpoint' => '/people/{rowId}'],
        'persistState' => 'url',
        'stateKey' => 'people',
        'initialState' => ['globalFilter' => 'Ada'],
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)->toContain('data-daisy-kit-module="table"')
        ->toContain('data-daisy-kit-content')
        ->toContain('aria-busy="true"')
        ->toContain('data-daisy-kit-status')
        ->not->toContain('<img')
        ->not->toContain('style=')
        ->not->toContain('x-daisy::')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? '')))->toMatchArray([
            'mode' => 'client',
            'columns' => [['id' => 'name', 'key' => 'name', 'label' => 'Name']],
            'rows' => [['name' => '</script><img src=x onerror=alert(1)>']],
            'pageSize' => 20,
            'bulkActions' => [],
            'rowActions' => [['id' => 'approve', 'label' => 'Approve']],
            'rowDetails' => ['accessor' => 'summary', 'mode' => 'inline'],
            'editable' => ['columns' => ['name'], 'endpoint' => '/people/{rowId}'],
            'persistState' => ['mode' => 'url', 'key' => 'people'],
            'initialState' => ['globalFilter' => 'Ada'],
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

it('keeps host attributes while protecting module lifecycle hooks', function (): void {
    $html = (string) $this->blade(<<<'BLADE'
        <x-daisy-kit::table
            id="people"
            aria-label="People directory"
            data-analytics="directory"
            data-daisy-kit-module="host-override"
            aria-busy="false"
        />
        BLADE);

    expect($html)
        ->toContain('id="people"')
        ->toContain('aria-label="People directory"')
        ->toContain('data-analytics="directory"')
        ->toContain('data-daisy-kit-module="table"')
        ->toContain('aria-busy="true"')
        ->not->toContain('data-daisy-kit-module="host-override"');
});

it('renders the restored product table contract with private structured controls', function (): void {
    $html = view('daisy-kit::components.table', [
        'columns' => [
            [
                'key' => 'name',
                'label' => 'Name',
                'sortable' => true,
                'filterable' => true,
                'filter' => ['type' => 'text'],
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'visible' => false,
                'filterable' => true,
                'filter' => [
                    'type' => 'select',
                    'options' => [['value' => 'ready', 'label' => 'Ready']],
                ],
            ],
        ],
        'rows' => [['id' => 'ada', 'name' => 'Ada Lovelace', 'status' => 'ready']],
        'mode' => 'client',
        'filters' => [
            ['id' => 'active', 'label' => 'Active only', 'type' => 'boolean'],
        ],
        'initialState' => ['pagination' => ['pageSize' => 25]],
        'pageSizeOptions' => [10, 25, 50],
        'search' => true,
        'searchDebounce' => 350,
        'searchMode' => 'includes',
        'columnVisibility' => true,
        'selection' => 'multiple',
        'rowKey' => 'id',
        'persistState' => 'url',
        'stateKey' => 'people-table',
        'caption' => 'People directory',
        'size' => 'sm',
        'zebra' => true,
        'hover' => true,
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);
    $configuration = JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));

    expect($html)
        ->toContain('daisy-kit-table__toolbar')
        ->toContain('daisy-kit-table__filters')
        ->toContain('daisy-kit-table__selection')
        ->toContain('daisy-kit-table__results')
        ->toContain('data-daisy-kit-table-page-size')
        ->toContain('data-daisy-kit-table-column-controls')
        ->toContain('data-daisy-kit-table-filter="active"')
        ->toContain('<caption')
        ->toContain('People directory')
        ->not->toContain('x-daisy::')
        ->not->toContain('style=')
        ->and($configuration)->toMatchArray([
            'mode' => 'client',
            'pageSizeOptions' => [10, 25, 50],
            'search' => [
                'enabled' => true,
                'debounce' => 350,
                'mode' => 'includes',
            ],
            'columnVisibility' => true,
            'selection' => ['mode' => 'multiple', 'rowKey' => 'id', 'selectFiltered' => true],
            'persistState' => ['mode' => 'url', 'key' => 'people-table'],
        ]);
});
