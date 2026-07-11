<?php

namespace Art35rennes\DaisyKit\Support;

class DaisyTableColumns
{
    public static function normalizeEditable(
        mixed $editable,
        mixed $editEndpoint,
        mixed $editMethod,
        mixed $editMode,
        mixed $editableColumns,
        mixed $editPolicy,
        array $columns,
    ): array {
        $config = is_array($editable) ? $editable : [];
        $enabled = $editable === true || ($config['enabled'] ?? false) === true;
        $columnKeys = collect($columns)->pluck('key')->filter()->values()->all();
        $defaultColumns = collect($columns)
            ->reject(fn (array $column) => ($column['html'] ?? false) || in_array($column['type'] ?? null, ['actions', 'link', 'resource-link'], true))
            ->pluck('key')
            ->filter()
            ->values()
            ->all();
        $requestedColumns = is_array($editableColumns) && $editableColumns !== []
            ? $editableColumns
            : ($config['columns'] ?? $defaultColumns);
        $update = is_array($config['update'] ?? null) ? $config['update'] : [];
        $create = is_array($config['create'] ?? null) ? $config['create'] : [];
        $legacyEndpoint = $editEndpoint ?? ($config['endpoint'] ?? null);
        $legacyMethod = $editMethod ?? ($config['method'] ?? null);

        return [
            'enabled' => $enabled,
            'endpoint' => self::normalizeEndpoint($update['endpoint'] ?? $legacyEndpoint),
            'method' => self::normalizeMethod($update['method'] ?? $legacyMethod, 'PATCH'),
            'mode' => in_array($config['mode'] ?? $editMode, ['cell', 'row'], true) ? ($config['mode'] ?? $editMode) : 'cell',
            'columns' => collect(is_array($requestedColumns) ? $requestedColumns : [])
                ->filter(fn ($key) => is_string($key) && filled($key) && in_array($key, $columnKeys, true))
                ->unique()
                ->values()
                ->all(),
            'policy' => is_array($editPolicy) && $editPolicy !== [] ? $editPolicy : (is_array($config['policy'] ?? null) ? $config['policy'] : []),
            'update' => [
                'strategy' => ($update['strategy'] ?? null) === 'local' ? 'local' : 'remote',
                'endpoint' => self::normalizeEndpoint($update['endpoint'] ?? $legacyEndpoint),
                'method' => self::normalizeMethod($update['method'] ?? $legacyMethod, 'PATCH'),
            ],
            'create' => [
                'enabled' => ($create['enabled'] ?? false) === true,
                'strategy' => ($create['strategy'] ?? null) === 'local' ? 'local' : 'remote',
                'endpoint' => self::normalizeEndpoint($create['endpoint'] ?? null),
                'method' => self::normalizeMethod($create['method'] ?? null, 'POST'),
                'defaults' => is_array($create['defaults'] ?? null) ? $create['defaults'] : [],
                'position' => 'top',
            ],
        ];
    }

    public static function normalizeEditor(array $column): array
    {
        $editor = is_array($column['editor'] ?? null) ? $column['editor'] : [];
        $type = in_array($editor['type'] ?? null, ['text', 'textarea', 'number', 'select', 'boolean', 'date', 'blade'], true)
            ? $editor['type']
            : 'text';

        return [
            'type' => $type,
            'required' => ($editor['required'] ?? false) === true,
            'options' => array_values(array_filter(
                is_array($editor['options'] ?? null) ? $editor['options'] : [],
                static fn ($option) => is_array($option) && array_key_exists('value', $option)
            )),
            'view' => is_string($editor['view'] ?? null) && filled($editor['view']) ? $editor['view'] : null,
            'template' => null,
        ];
    }

    public static function normalizeLinkPolicy(mixed $policy): array
    {
        if (! is_array($policy)) {
            return ['allowedSchemes' => []];
        }

        return [
            'allowedSchemes' => self::normalizeAllowedSchemes($policy['allowedSchemes'] ?? []),
        ];
    }

    public static function normalizeCell(array $column): array
    {
        $cell = is_array($column['cell'] ?? null) ? $column['cell'] : [];
        $renderer = is_string($cell['renderer'] ?? null) ? $cell['renderer'] : null;

        if (is_string($column['view'] ?? null) && filled($column['view'])) {
            $renderer = 'blade';
            $cell['view'] = $column['view'];
        }

        if (($column['html'] ?? false) === true && $renderer === null) {
            $renderer = 'html';
        }

        if (($column['type'] ?? null) === 'link' || ($column['type'] ?? null) === 'resource-link') {
            $renderer ??= 'link';
        }

        $renderer = in_array($renderer, ['text', 'html', 'blade', 'link', 'actions'], true) ? $renderer : 'text';

        return [
            'renderer' => $renderer,
            'view' => is_string($cell['view'] ?? null) && filled($cell['view']) ? $cell['view'] : null,
            'allowedSchemes' => self::normalizeAllowedSchemes($cell['allowedSchemes'] ?? []),
        ];
    }

    public static function normalizeToolbarFilter(array $filter): array
    {
        $key = is_string($filter['key'] ?? $filter['id'] ?? null) ? trim((string) ($filter['key'] ?? $filter['id'])) : '';
        $type = in_array($filter['type'] ?? null, ['text', 'select', 'boolean', 'date', 'date-range'], true) ? $filter['type'] : null;

        return [
            'id' => $key,
            'label' => $filter['label'] ?? $key,
            'type' => $type,
            'filterKey' => is_string($filter['filterKey'] ?? null) && filled($filter['filterKey']) ? $filter['filterKey'] : $key,
            'filterKeyFrom' => is_string($filter['filterKeyFrom'] ?? null) && filled($filter['filterKeyFrom']) ? $filter['filterKeyFrom'] : null,
            'filterKeyTo' => is_string($filter['filterKeyTo'] ?? null) && filled($filter['filterKeyTo']) ? $filter['filterKeyTo'] : null,
            'options' => array_values(array_filter(
                is_array($filter['options'] ?? null) ? $filter['options'] : [],
                static fn ($option) => is_array($option) && filled($option['value'] ?? null)
            )),
        ];
    }

    public static function numericClass(mixed $value, string $prefix): ?string
    {
        if (! is_string($value) && ! $value instanceof \Stringable && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= 1200 ? $prefix.'-px-'.$token : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)rem$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1] * 4);

            return $token >= 1 && $token <= 512 ? $prefix.'-rem-'.$token : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)%$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= 100 ? $prefix.'-percent-'.$token : null;
        }

        return null;
    }

    protected static function normalizeAllowedSchemes(mixed $schemes): array
    {
        if (! is_array($schemes)) {
            return [];
        }

        return collect($schemes)
            ->map(fn ($scheme) => strtolower(rtrim(trim((string) $scheme), ':')))
            ->filter(fn (string $scheme) => preg_match('/^[a-z][a-z0-9+.-]*$/', $scheme) === 1)
            ->reject(fn (string $scheme) => in_array($scheme, DaisyTableUrlPolicy::BlockedSchemes, true))
            ->unique()
            ->values()
            ->all();
    }

    protected static function normalizeEndpoint(mixed $endpoint): ?array
    {
        if (is_string($endpoint) && filled($endpoint)) {
            return ['url' => $endpoint];
        }

        if (! is_array($endpoint) || ! is_string($endpoint['url'] ?? null) || blank($endpoint['url'])) {
            return null;
        }

        return array_filter([
            'url' => $endpoint['url'],
            'headers' => is_array($endpoint['headers'] ?? null) ? $endpoint['headers'] : null,
            'credentials' => is_string($endpoint['credentials'] ?? null) ? $endpoint['credentials'] : null,
        ], static fn ($value) => $value !== null);
    }

    protected static function normalizeMethod(mixed $method, string $fallback): string
    {
        return is_string($method) && filled($method) ? strtoupper($method) : $fallback;
    }
}
