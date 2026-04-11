<?php

use Illuminate\Support\Facades\View;

it('renders color-picker with module override', function () {
    $html = View::make('daisy::components.ui.inputs.color-picker', [
        'mode' => 'advanced',
        'value' => '#ff0000',
        'module' => 'color-picker',
    ])->render();

    expect($html)
        ->toContain('data-module="color-picker"')
        ->toContain('data-colorpicker="1"');
});

it('renders chart with module override', function () {
    $html = View::make('daisy::components.ui.advanced.chart', [
        'type' => 'bar',
        'labels' => ['A', 'B'],
        'datasets' => [['label' => 'X', 'data' => [1, 2]]],
        'module' => 'chart',
    ])->render();

    expect($html)
        ->toContain('data-module="chart"')
        ->toContain('data-chart="1"');
});

it('renders base table without legacy selection attributes', function () {
    $html = View::make('daisy::components.ui.data-display.table', [
        'size' => 'lg',
        'zebra' => true,
        'slot' => new \Illuminate\Support\HtmlString('<tbody><tr><td>R1C1</td></tr></tbody>'),
    ])->render();

    expect($html)
        ->toContain('table table-zebra table-lg')
        ->toContain('overflow-x-auto')
        ->toContain('<table')
        ->not->toContain('data-table-select')
        ->not->toContain('data-module=');
});

it('renders advanced table with selection and sort metadata', function () {
    $html = View::make('daisy::components.ui.advanced.table', [
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'rowHeader' => true, 'sortable' => true],
            ['key' => 'status', 'label' => 'Status'],
        ],
        'rows' => [
            ['id' => 10, 'name' => 'Alpha', 'status' => 'Ready'],
            ['id' => 11, 'name' => 'Beta', 'status' => 'Pending'],
            ['id' => 12, 'name' => 'Gamma', 'status' => 'Draft'],
        ],
        'mode' => 'server',
        'selectable' => 'multiple',
        'selected' => [10],
        'sortBy' => 'name',
        'sortDirection' => 'asc',
        'sortUrls' => [
            'name' => [
                'asc' => '/users?sort=name&direction=asc',
                'desc' => '/users?sort=name&direction=desc',
            ],
        ],
    ])->render();

    expect($html)
        ->toContain('data-advanced-table="1"')
        ->toContain('data-selection="multiple"')
        ->toContain('data-table-mode="server"')
        ->toContain('data-select-all')
        ->toContain('aria-checked="mixed"')
        ->toContain('data-indeterminate="true"')
        ->toContain('data-row-select')
        ->toContain('data-row-id="10"')
        ->toContain('/users?sort=name&amp;direction=desc')
        ->toContain('bg-base-200');
});

it('renders advanced table client controls and metadata', function () {
    $html = View::make('daisy::components.ui.advanced.table', [
        'mode' => 'client',
        'searchable' => true,
        'perPageOptions' => [10, 25, 50],
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'filterable' => true],
            ['key' => 'status', 'label' => 'Status', 'filterable' => true, 'filterOptions' => ['ready' => 'Ready']],
        ],
        'rows' => [
            ['id' => 10, 'name' => 'Alpha', 'status' => 'Ready'],
        ],
    ])->render();

    expect($html)
        ->toContain('data-table-mode="client"')
        ->toContain('data-client-options=')
        ->toContain('data-table-search')
        ->toContain('data-column-filter="name"')
        ->toContain('data-column-filter="status"')
        ->toContain('data-client-sort-key="name"')
        ->toContain('data-table-page-size-select')
        ->toContain('data-table-page-prev');
});

it('renders advanced table empty and loading states', function () {
    $emptyHtml = View::make('daisy::components.ui.advanced.table', [
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
        'rows' => [],
        'emptyTitle' => 'Nothing here',
        'emptyDescription' => 'Try broadening your filters.',
    ])->render();

    $loadingHtml = View::make('daisy::components.ui.advanced.table', [
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
        'rows' => [],
        'loading' => true,
    ])->render();

    expect($emptyHtml)
        ->toContain('Nothing here')
        ->toContain('Try broadening your filters.');

    expect($loadingHtml)
        ->toContain('loading')
        ->toContain('spinner');
});

it('renders calendar-full with eventsUrl', function () {
    $html = View::make('daisy::components.ui.advanced.calendar-full', [
        'eventsUrl' => '/api/events',
        'view' => 'month',
    ])->render();

    expect($html)
        ->toContain('data-module="calendar-full"')
        ->toContain('data-events-url="/api/events"');
});
