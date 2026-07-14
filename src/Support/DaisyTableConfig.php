<?php

namespace Art35rennes\DaisyKit\Support;

use InvalidArgumentException;

class DaisyTableConfig
{
    public const ContractVersion = 2;

    public const PersistableStateFields = [
        'sorting',
        'globalFilter',
        'columnFilters',
        'pagination',
        'columnVisibility',
        'columnOrder',
        'columnPinning',
        'columnSizing',
        'expanded',
        'rowSelection',
    ];

    public const DefaultPersistedStateFields = [
        'sorting',
        'globalFilter',
        'columnFilters',
        'pagination',
        'columnVisibility',
        'columnOrder',
        'columnPinning',
        'columnSizing',
    ];

    public const AllowedHttpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public const AllowedCredentials = ['omit', 'same-origin', 'include'];

    public static function validateColumns(array $columns): void
    {
        $keys = array_values(array_filter(array_map(
            static fn (array $column): string => trim((string) ($column['key'] ?? '')),
            $columns,
        )));
        $duplicates = array_values(array_unique(array_diff_assoc($keys, array_unique($keys))));

        if ($duplicates !== []) {
            throw new InvalidArgumentException('Daisy table column keys must be unique. Duplicate keys: '.implode(', ', $duplicates).'.');
        }
    }

    public static function normalizePersistedStateFields(mixed $fields): array
    {
        if (! is_array($fields)) {
            return self::DefaultPersistedStateFields;
        }

        return collect($fields)
            ->filter(fn ($field) => is_string($field) && in_array($field, self::PersistableStateFields, true))
            ->unique()
            ->values()
            ->all();
    }

    public static function normalizeEndpoint(mixed $endpoint, ?string $method = null): ?array
    {
        if (is_string($endpoint) && filled($endpoint)) {
            $endpoint = ['url' => $endpoint];
        }

        if (! is_array($endpoint) || ! is_string($endpoint['url'] ?? null) || blank($endpoint['url'])) {
            return null;
        }

        $resolvedMethod = array_key_exists('method', $endpoint)
            ? self::normalizeMethod($endpoint['method'], $method ?? 'GET')
            : null;
        $credentials = $endpoint['credentials'] ?? null;

        if ($credentials !== null && ! in_array($credentials, self::AllowedCredentials, true)) {
            throw new InvalidArgumentException('Daisy table endpoint credentials must be omit, same-origin, or include.');
        }

        return array_filter([
            'url' => trim($endpoint['url']),
            'method' => $resolvedMethod,
            'headers' => is_array($endpoint['headers'] ?? null) ? $endpoint['headers'] : null,
            'credentials' => is_string($credentials) ? $credentials : null,
        ], static fn ($value) => $value !== null);
    }

    public static function normalizeMethod(mixed $method, string $fallback): string
    {
        $method = is_string($method) && filled($method) ? strtoupper($method) : $fallback;

        if (! in_array($method, self::AllowedHttpMethods, true)) {
            throw new InvalidArgumentException('Daisy table HTTP method ['.$method.'] is not supported.');
        }

        return $method;
    }
}
