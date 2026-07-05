<?php

namespace Art35rennes\DaisyKit\Support;

class DaisyTableColumns
{
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
}
