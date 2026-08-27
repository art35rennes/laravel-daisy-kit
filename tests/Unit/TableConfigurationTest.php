<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Table\TableConfiguration;

it('normalizes the restored client table vocabulary', function (): void {
    $table = TableConfiguration::make([
        'columns' => [['key' => 'name', 'label' => 'Name']],
        'rows' => [['uuid' => 'ada', 'name' => 'Ada']],
        'initialState' => ['pagination' => ['pageSize' => 25]],
        'pageSizeOptions' => [50, 10, 25, 25],
        'search' => ['debounce' => 400, 'mode' => 'includes'],
        'selection' => ['mode' => 'multiple', 'rowKey' => 'uuid'],
        'persistState' => 'local',
        'stateKey' => 'people',
    ]);

    expect($table['configuration'])
        ->toMatchArray([
            'mode' => 'client',
            'columns' => [[
                'id' => 'name',
                'key' => 'name',
                'label' => 'Name',
                'cell' => ['renderer' => 'text', 'view' => null],
            ]],
            'pageSize' => 25,
            'pageSizeOptions' => [10, 25, 50],
            'search' => ['enabled' => true, 'debounce' => 400, 'mode' => 'includes'],
            'selection' => ['mode' => 'multiple', 'rowKey' => 'uuid', 'selectFiltered' => true],
            'persistState' => ['mode' => 'local', 'key' => 'people'],
        ]);
});

it('rejects duplicate and missing column keys', function (array $columns, string $message): void {
    expect(fn (): array => TableConfiguration::make(['columns' => $columns]))
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    'missing key' => [[['label' => 'Name']], 'Every table column requires a non-empty key.'],
    'duplicate key' => [[['key' => 'name'], ['key' => 'name']], 'Table column keys must be unique: name.'],
]);

it('requires an endpoint for server mode', function (): void {
    expect(fn (): array => TableConfiguration::make(['mode' => 'server']))
        ->toThrow(InvalidArgumentException::class, 'A non-empty endpoint is required when table mode is server.');
});

it('normalizes the Spatie Query Builder server adapter', function (): void {
    $table = TableConfiguration::make([
        'mode' => 'server',
        'endpoint' => '/people',
        'serverAdapter' => 'spatie-query-builder',
        'globalFilterKey' => 'people',
        'columns' => [['key' => 'name', 'sortKey' => 'users.name']],
        'filters' => [['id' => 'status', 'filterKey' => 'state']],
    ]);

    expect($table['configuration'])
        ->serverAdapter->toBe('spatie-query-builder')
        ->globalFilterKey->toBe('people')
        ->columns->sequence(fn ($column) => $column->sortKey->toBe('users.name'))
        ->filters->sequence(fn ($filter) => $filter->filterKey->toBe('state'));
});

it('rejects invalid server adapters', function (array $configuration, string $message): void {
    expect(fn (): array => TableConfiguration::make($configuration))
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    'unknown adapter' => [[
        'mode' => 'server',
        'endpoint' => '/people',
        'serverAdapter' => 'unknown',
    ], 'Invalid table serverAdapter value.'],
    'adapter in client mode' => [[
        'serverAdapter' => 'spatie-query-builder',
    ], 'Table serverAdapter is only available in server mode.'],
]);

it('rejects ambiguous filter definitions', function (array $filters, string $message): void {
    expect(fn (): array => TableConfiguration::make(['filters' => $filters]))
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    'duplicate id' => [[['id' => 'status'], ['id' => 'status']], 'Table filter ids must be unique: status.'],
    'unknown type' => [[['id' => 'status', 'type' => 'callback']], 'Invalid table filter type value.'],
]);

it('requires an instance key when persistence is enabled', function (): void {
    expect(fn (): array => TableConfiguration::make(['persistState' => 'url']))
        ->toThrow(InvalidArgumentException::class, 'Table stateKey is required when state persistence is enabled.');
});

it('rejects implicit or missing custom cell renderers', function (array $column, string $message): void {
    expect(fn (): array => TableConfiguration::make(['columns' => [$column]]))
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    'implicit html' => [['key' => 'status', 'html' => true], 'Table HTML cells require cell.renderer to be trusted-html explicitly.'],
    'missing blade view' => [['key' => 'status', 'cell' => ['renderer' => 'blade', 'view' => 'missing::cell']], 'Table Blade cell view [missing::cell] does not exist.'],
]);
