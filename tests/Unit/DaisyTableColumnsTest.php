<?php

use Art35rennes\DaisyKit\Support\DaisyTableColumns;

it('normalizes table cells and view shorthand', function (): void {
    expect(DaisyTableColumns::normalizeCell([
        'key' => 'actions',
        'type' => 'actions',
        'view' => 'table.actions',
        'cell' => ['allowedSchemes' => ['myapp', 'javascript']],
    ]))->toEqual([
        'renderer' => 'blade',
        'view' => 'table.actions',
        'allowedSchemes' => ['myapp'],
    ])
        ->and(DaisyTableColumns::normalizeCell([
            'key' => 'profile',
            'type' => 'resource-link',
        ]))->toEqual([
            'renderer' => 'link',
            'view' => null,
            'allowedSchemes' => [],
        ]);
});

it('normalizes link policies and numeric classes', function (): void {
    expect(DaisyTableColumns::normalizeLinkPolicy([
        'allowedSchemes' => ['myapp:', 'intent', 'data'],
    ]))->toEqual([
        'allowedSchemes' => ['myapp', 'intent'],
    ])
        ->and(DaisyTableColumns::numericClass('180px', 'daisy-table-width'))->toBe('daisy-table-width-px-180')
        ->and(DaisyTableColumns::numericClass('128rem', 'daisy-table-root-min-width'))->toBe('daisy-table-root-min-width-rem-512')
        ->and(DaisyTableColumns::numericClass('33%', 'daisy-table-width'))->toBe('daisy-table-width-percent-33');
});

it('normalizes date range toolbar filters', function (): void {
    $filter = DaisyTableColumns::normalizeToolbarFilter([
        'key' => 'period',
        'label' => 'Period',
        'type' => 'date-range',
        'filterKeyFrom' => 'started_after',
        'filterKeyTo' => 'started_before',
    ]);

    expect($filter)->toEqual([
        'id' => 'period',
        'label' => 'Period',
        'type' => 'date-range',
        'filterKey' => 'period',
        'filterKeyFrom' => 'started_after',
        'filterKeyTo' => 'started_before',
        'options' => [],
    ]);
});
