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
            'columns' => [['id' => 'name', 'key' => 'name', 'label' => 'Name']],
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
