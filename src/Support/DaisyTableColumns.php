<?php

namespace Art35rennes\DaisyKit\Support;

class DaisyTableColumns
{
    public static function normalize(array $column): array
    {
        $key = is_string($column['key'] ?? null) ? trim($column['key']) : '';
        $filterConfig = is_array($column['filter'] ?? null) ? $column['filter'] : [];
        $filterType = in_array($filterConfig['type'] ?? null, ['text', 'select', 'boolean', 'date', 'date-range'], true)
            ? $filterConfig['type']
            : null;
        $type = in_array($column['type'] ?? null, ['actions', 'link', 'resource-link'], true) ? $column['type'] : null;
        $cell = self::normalizeCell($column);
        $width = $column['width'] ?? null;
        $minWidth = $column['minWidth'] ?? null;
        $maxWidth = $column['maxWidth'] ?? null;
        $align = in_array($column['align'] ?? null, ['left', 'center', 'right'], true) ? $column['align'] : null;
        $verticalAlign = in_array($column['verticalAlign'] ?? null, ['top', 'middle', 'bottom'], true) ? $column['verticalAlign'] : null;
        $padding = in_array($column['padding'] ?? null, ['none', 'compact', 'normal'], true) ? $column['padding'] : null;
        $density = in_array($column['density'] ?? null, ['compact', 'normal'], true) ? $column['density'] : null;
        $truncate = $column['truncate'] ?? false;

        if ($type === 'actions') {
            $width ??= 'fit';
            $align ??= 'center';
            $column['nowrap'] ??= true;
            $density ??= 'compact';
        }

        return [
            'key' => $key,
            'type' => $type,
            'label' => $column['label'] ?? $column['title'] ?? $key,
            'sortable' => (bool) ($column['sortable'] ?? false),
            'filterable' => (bool) ($column['filterable'] ?? false),
            'sortKey' => is_string($column['sortKey'] ?? null) && filled($column['sortKey']) ? $column['sortKey'] : $key,
            'filterKey' => is_string($column['filterKey'] ?? null) && filled($column['filterKey']) ? $column['filterKey'] : $key,
            'visible' => (bool) ($column['visible'] ?? true),
            'width' => $width,
            'minWidth' => $minWidth,
            'maxWidth' => $maxWidth,
            'widthClass' => $width === 'fit' ? 'daisy-table-width-fit' : ($width === 'auto' ? null : self::numericClass($width, 'daisy-table-width')),
            'minWidthClass' => $minWidth === 'max-content' ? 'daisy-table-min-width-max' : ($minWidth === 'full' ? 'min-w-full' : self::numericClass($minWidth, 'daisy-table-min-width')),
            'maxWidthClass' => self::numericClass($maxWidth, 'daisy-table-max-width'),
            'align' => $align,
            'alignClass' => match ($align) {
                'center' => 'text-center',
                'right' => 'text-right',
                'left' => 'text-left',
                default => null,
            },
            'verticalAlign' => $verticalAlign,
            'verticalAlignClass' => match ($verticalAlign) {
                'top' => 'align-top',
                'middle' => 'align-middle',
                'bottom' => 'align-bottom',
                default => null,
            },
            'padding' => $padding,
            'paddingClass' => match ($padding) {
                'none' => 'p-0',
                'compact' => 'px-2 py-1',
                default => null,
            },
            'density' => $density,
            'densityClass' => $density === 'compact' ? 'daisy-table-cell-compact' : null,
            'nowrap' => (bool) ($column['nowrap'] ?? false),
            'truncate' => in_array($truncate, ['line', 2, 3], true) ? $truncate : false,
            'cellWrapperClass' => $column['cellWrapperClass'] ?? '',
            'headerWrapperClass' => $column['headerWrapperClass'] ?? '',
            'cellClass' => $column['cellClass'] ?? '',
            'headerClass' => $column['headerClass'] ?? '',
            'help' => is_string($column['help'] ?? null) ? trim($column['help']) : '',
            'trusted' => in_array($cell['renderer'], ['trusted-html', 'blade'], true),
            'cell' => $cell,
            'editor' => self::normalizeEditor($column),
            'enableResizing' => ($column['enableResizing'] ?? true) !== false,
            'size' => is_numeric($column['size'] ?? null) ? (int) $column['size'] : null,
            'minSize' => is_numeric($column['minSize'] ?? null) ? (int) $column['minSize'] : null,
            'maxSize' => is_numeric($column['maxSize'] ?? null) ? (int) $column['maxSize'] : null,
            'filter' => $filterType ? [
                'type' => $filterType,
                'filterKeyFrom' => is_string($filterConfig['filterKeyFrom'] ?? null) && filled($filterConfig['filterKeyFrom']) ? $filterConfig['filterKeyFrom'] : null,
                'filterKeyTo' => is_string($filterConfig['filterKeyTo'] ?? null) && filled($filterConfig['filterKeyTo']) ? $filterConfig['filterKeyTo'] : null,
                'options' => array_values(array_filter(
                    is_array($filterConfig['options'] ?? null) ? $filterConfig['options'] : [],
                    static fn ($option) => is_array($option) && filled($option['value'] ?? null)
                )),
            ] : null,
        ];
    }

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
            ->reject(fn (array $column) => ($column['trusted'] ?? false) || in_array($column['type'] ?? null, ['actions', 'link', 'resource-link'], true))
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
        $updateMethod = $update['method'] ?? data_get($update, 'endpoint.method') ?? $legacyMethod;
        $createMethod = $create['method'] ?? data_get($create, 'endpoint.method');

        return [
            'enabled' => $enabled,
            'endpoint' => DaisyTableConfig::normalizeEndpoint($update['endpoint'] ?? $legacyEndpoint, 'PATCH'),
            'method' => DaisyTableConfig::normalizeMethod($updateMethod, 'PATCH'),
            'mode' => in_array($config['mode'] ?? $editMode, ['cell', 'row'], true) ? ($config['mode'] ?? $editMode) : 'cell',
            'columns' => collect(is_array($requestedColumns) ? $requestedColumns : [])
                ->filter(fn ($key) => is_string($key) && filled($key) && in_array($key, $columnKeys, true))
                ->unique()
                ->values()
                ->all(),
            'policy' => is_array($editPolicy) && $editPolicy !== [] ? $editPolicy : (is_array($config['policy'] ?? null) ? $config['policy'] : []),
            'update' => [
                'strategy' => ($update['strategy'] ?? null) === 'local' ? 'local' : 'remote',
                'endpoint' => DaisyTableConfig::normalizeEndpoint($update['endpoint'] ?? $legacyEndpoint, 'PATCH'),
                'method' => DaisyTableConfig::normalizeMethod($updateMethod, 'PATCH'),
            ],
            'create' => [
                'enabled' => ($create['enabled'] ?? false) === true,
                'strategy' => ($create['strategy'] ?? null) === 'local' ? 'local' : 'remote',
                'endpoint' => DaisyTableConfig::normalizeEndpoint($create['endpoint'] ?? null, 'POST'),
                'method' => DaisyTableConfig::normalizeMethod($createMethod, 'POST'),
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

        if (($column['html'] ?? false) === true || $renderer === 'html') {
            throw new \InvalidArgumentException('The Daisy table html renderer was removed. Use cell.renderer = trusted-html explicitly.');
        }

        if (is_string($column['view'] ?? null) && filled($column['view'])) {
            $renderer = 'blade';
            $cell['view'] = $column['view'];
        }

        if (($column['type'] ?? null) === 'link' || ($column['type'] ?? null) === 'resource-link') {
            $renderer ??= 'link';
        }

        if (($column['type'] ?? null) === 'actions' && $renderer === null) {
            $renderer = 'actions';
        }

        $renderer = in_array($renderer, ['text', 'trusted-html', 'blade', 'link', 'actions'], true) ? $renderer : 'text';

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
