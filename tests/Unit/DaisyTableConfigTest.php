<?php

use Art35rennes\DaisyKit\Support\DaisyTableConfig;

it('normalizes persistence fields and secure endpoint options', function (): void {
    expect(DaisyTableConfig::normalizePersistedStateFields(['sorting', 'expanded', 'expanded', 'invalid']))
        ->toBe(['sorting', 'expanded'])
        ->and(DaisyTableConfig::normalizeEndpoint([
            'url' => '/users/{rowId}',
            'method' => 'patch',
            'credentials' => 'same-origin',
        ]))->toBe([
            'url' => '/users/{rowId}',
            'method' => 'PATCH',
            'credentials' => 'same-origin',
        ]);
});

it('rejects malformed columns with controlled validation errors', function (): void {
    expect(fn (): null => DaisyTableConfig::validateColumns([['key' => 'name'], 'invalid']))
        ->toThrow(InvalidArgumentException::class, 'arrays')
        ->and(fn (): null => DaisyTableConfig::validateColumns([['label' => 'Missing key']]))
        ->toThrow(InvalidArgumentException::class, 'non-empty key')
        ->and(fn (): null => DaisyTableConfig::validateColumns([['key' => 'name'], ['key' => 'name']]))
        ->toThrow(InvalidArgumentException::class, 'Duplicate keys: name');
});

it('validates client row keys recursively', function (): void {
    DaisyTableConfig::validateRows([['id' => 0]], 'id');

    expect(fn (): null => DaisyTableConfig::validateRows([['name' => 'Missing']], 'id'))
        ->toThrow(InvalidArgumentException::class, 'non-empty id')
        ->and(fn (): null => DaisyTableConfig::validateRows([
            ['id' => 'parent', 'children' => [['id' => 'child']]],
            ['id' => 'other', 'children' => [['id' => 'child']]],
        ], 'id', 'children'))
        ->toThrow(InvalidArgumentException::class, 'Duplicate value: child')
        ->and(fn (): null => DaisyTableConfig::validateRows([
            ['id' => 'parent', 'children' => 'invalid'],
        ], 'id', 'children'))
        ->toThrow(InvalidArgumentException::class, 'array of row arrays');
});

it('rejects unsafe transport and mutation methods', function (): void {
    expect(fn (): string => DaisyTableConfig::normalizeMethod('TRACE', 'GET'))
        ->toThrow(InvalidArgumentException::class, 'not supported')
        ->and(fn (): string => DaisyTableConfig::normalizeMutationMethod('GET', 'PATCH'))
        ->toThrow(InvalidArgumentException::class, 'mutation method')
        ->and(fn (): ?array => DaisyTableConfig::normalizeEndpoint(['url' => '/users', 'credentials' => 'unsafe']))
        ->toThrow(InvalidArgumentException::class, 'credentials');
});
