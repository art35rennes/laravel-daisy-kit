<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Table;

use Illuminate\Support\Facades\View;
use InvalidArgumentException;

final class TableConfiguration
{
    /**
     * @param  array<string, mixed>  $input
     * @return array{configuration: array<string, mixed>, view: array<string, mixed>}
     */
    public static function make(array $input): array
    {
        $mode = self::enum($input['mode'] ?? 'client', ['client', 'server'], 'mode');
        $columns = self::columns($input['columns'] ?? []);
        $filters = self::filters($input['filters'] ?? []);
        $filterMode = self::enum($input['filterMode'] ?? 'instant', ['instant', 'manual'], 'filterMode');
        $pageSizeOptions = self::pageSizeOptions($input['pageSizeOptions'] ?? [10, 25, 50, 100]);
        $pageSize = self::pageSize($input, $pageSizeOptions);
        $pageSizeOptions = self::pageSizeOptions([...$pageSizeOptions, $pageSize]);
        $search = self::search($input);
        $selection = self::selection($input);
        $persistence = self::persistence($input);
        $labels = self::labels();
        $serverAdapter = $input['serverAdapter'] ?? null;

        $endpoint = $input['endpoint'] ?? null;

        if ($mode === 'server' && (! is_string($endpoint) || trim($endpoint) === '')) {
            throw new InvalidArgumentException('A non-empty endpoint is required when table mode is server.');
        }

        if ($serverAdapter !== null) {
            $serverAdapter = self::enum($serverAdapter, ['spatie-query-builder'], 'serverAdapter');

            if ($mode !== 'server') {
                throw new InvalidArgumentException('Table serverAdapter is only available in server mode.');
            }
        }

        $globalFilterKey = $input['globalFilterKey'] ?? 'global';

        if (! is_string($globalFilterKey) || trim($globalFilterKey) === '') {
            throw new InvalidArgumentException('Table globalFilterKey must be a non-empty string.');
        }

        $configuration = [
            'mode' => $mode,
            'endpoint' => $endpoint,
            'serverAdapter' => $serverAdapter,
            'globalFilterKey' => $globalFilterKey,
            'columns' => $columns,
            'rows' => self::rows($input['rows'] ?? [], $columns, $selection['rowKey']),
            'filters' => $filters,
            'filterMode' => $filterMode,
            'pageSize' => $pageSize,
            'pageSizeOptions' => $pageSizeOptions,
            'search' => $search,
            'columnVisibility' => ($input['columnVisibility'] ?? true) === true,
            'selection' => $selection,
            'bulkActions' => self::array($input['bulkActions'] ?? [], 'bulkActions'),
            'rowActions' => self::array($input['rowActions'] ?? [], 'rowActions'),
            'rowDetails' => $input['rowDetails'] ?? null,
            'editable' => $input['editable'] ?? false,
            'persistState' => $persistence,
            'labels' => $labels,
            'initialState' => self::array($input['initialState'] ?? [], 'initialState'),
            'presentation' => [
                'caption' => is_string($input['caption'] ?? null) ? $input['caption'] : null,
                'size' => self::enum($input['size'] ?? 'md', ['xs', 'sm', 'md', 'lg'], 'size'),
                'zebra' => ($input['zebra'] ?? true) === true,
                'hover' => ($input['hover'] ?? true) === true,
                'layout' => self::enum($input['layout'] ?? 'auto', ['auto', 'fixed'], 'layout'),
            ],
        ];

        return [
            'configuration' => $configuration,
            'view' => [
                'caption' => $configuration['presentation']['caption'],
                'columnVisibility' => $configuration['columnVisibility'],
                'filters' => $configuration['filters'],
                'filterMode' => $filterMode,
                'hasColumnFilters' => collect($columns)->contains(fn (array $column): bool => is_array($column['filter'] ?? null)),
                'pageSize' => $pageSize,
                'pageSizeOptions' => $pageSizeOptions,
                'search' => $search,
                'selection' => $selection,
                'size' => $configuration['presentation']['size'],
                'zebra' => $configuration['presentation']['zebra'],
                'hover' => $configuration['presentation']['hover'],
                'labels' => $labels,
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function columns(mixed $columns): array
    {
        $columns = self::array($columns, 'columns');
        $normalized = [];
        $seen = [];

        foreach ($columns as $column) {
            if (! is_array($column)) {
                throw new InvalidArgumentException('Every table column must be an array.');
            }

            $column = self::stringKeyedArray($column, 'column');

            $id = $column['key'] ?? $column['id'] ?? null;

            if (! is_string($id) || trim($id) === '') {
                throw new InvalidArgumentException('Every table column requires a non-empty key.');
            }

            if (isset($seen[$id])) {
                throw new InvalidArgumentException("Table column keys must be unique: {$id}.");
            }

            $seen[$id] = true;
            $normalized[] = [...$column, 'cell' => self::cell($column), 'id' => $id, 'key' => $id];
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $column
     * @return array{renderer: string, view: ?string}
     */
    private static function cell(array $column): array
    {
        $cell = is_array($column['cell'] ?? null) ? $column['cell'] : [];
        $renderer = $cell['renderer'] ?? null;

        if (($column['html'] ?? false) === true || $renderer === 'html') {
            throw new InvalidArgumentException('Table HTML cells require cell.renderer to be trusted-html explicitly.');
        }

        if (is_string($column['view'] ?? null) && trim($column['view']) !== '') {
            $renderer = 'blade';
            $cell['view'] = $column['view'];
        }

        $renderer ??= 'text';

        if (! in_array($renderer, ['blade', 'text', 'trusted-html'], true)) {
            throw new InvalidArgumentException('Invalid table cell renderer.');
        }

        $view = is_string($cell['view'] ?? null) && trim($cell['view']) !== '' ? $cell['view'] : null;

        if ($renderer === 'blade' && ($view === null || ! View::exists($view))) {
            throw new InvalidArgumentException("Table Blade cell view [{$view}] does not exist.");
        }

        return ['renderer' => $renderer, 'view' => $view];
    }

    /**
     * @param  list<array<string, mixed>>  $columns
     * @return list<array<string, mixed>>
     */
    private static function rows(mixed $rows, array $columns, string $rowKey): array
    {
        $rows = self::array($rows, 'rows');
        $normalizedRows = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                throw new InvalidArgumentException('Every table row must be an array.');
            }

            $normalized = self::stringKeyedArray($row, 'row');

            foreach ($columns as $column) {
                $cell = $column['cell'] ?? null;
                $key = $column['key'] ?? null;

                if (! is_array($cell) || ($cell['renderer'] ?? null) !== 'blade' || ! is_string($key)) {
                    continue;
                }

                $view = $cell['view'] ?? null;

                if (! is_string($view)) {
                    continue;
                }

                $normalized[$key] = trim(View::make($view, [
                    'column' => $column,
                    'item' => $row,
                    'row' => $normalized,
                    'table' => ['rowKey' => $rowKey],
                    'value' => data_get($normalized, $key),
                ])->render());
            }

            $normalizedRows[] = $normalized;
        }

        return $normalizedRows;
    }

    /** @return array<string, string> */
    private static function labels(): array
    {
        $keys = [
            'actions', 'all', 'apply_filters', 'cancel', 'clear_selection', 'close', 'columns', 'details', 'edit', 'edit_error',
            'edit_response_error', 'filter_column', 'filters', 'loading_error', 'missing_content', 'next',
            'no_matching_rows', 'no_results', 'normal', 'on_this_page', 'outside_this_page', 'page', 'pagination',
            'pin_end', 'pin_start', 'previous', 'rows_per_page', 'rows_selected', 'save', 'scroll_hint',
            'scrollable_table', 'search', 'search_placeholder', 'select_all_results', 'select_page',
            'select_page_aria', 'select_row_aria', 'showing_results', 'source_error', 'source_response_error',
            'table_controls', 'visible_columns', 'yes', 'no',
        ];

        return collect($keys)
            ->mapWithKeys(fn (string $key): array => [str($key)->camel()->toString() => __("daisy-kit::table.{$key}")])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function filters(mixed $filters): array
    {
        $filters = self::array($filters, 'filters');
        $normalized = [];
        $seen = [];

        foreach ($filters as $filter) {
            if (! is_array($filter)) {
                throw new InvalidArgumentException('Every table filter must be an array.');
            }

            $filter = self::stringKeyedArray($filter, 'filter');
            $id = $filter['id'] ?? null;

            if (! is_string($id) || trim($id) === '') {
                throw new InvalidArgumentException('Every table filter requires a non-empty id.');
            }

            if (isset($seen[$id])) {
                throw new InvalidArgumentException("Table filter ids must be unique: {$id}.");
            }

            $seen[$id] = true;
            $normalized[] = [
                ...$filter,
                'id' => $id,
                'type' => self::enum($filter['type'] ?? 'text', ['boolean', 'date', 'number', 'select', 'text'], 'filter type'),
            ];
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{enabled: bool, debounce: int, mode: string}
     */
    private static function search(array $input): array
    {
        $value = $input['search'] ?? true;
        $options = is_array($value) ? $value : [];
        $debounce = $options['debounce'] ?? $input['searchDebounce'] ?? 250;

        if (! is_int($debounce) || $debounce < 0 || $debounce > 5000) {
            throw new InvalidArgumentException('Table search debounce must be between 0 and 5000 milliseconds.');
        }

        return [
            'enabled' => $value !== false && ($options['enabled'] ?? true) === true,
            'debounce' => $debounce,
            'mode' => self::enum($options['mode'] ?? $input['searchMode'] ?? 'fuzzy', ['fuzzy', 'includes'], 'searchMode'),
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{mode: string, rowKey: string, selectFiltered: bool, summaryVisibility: string}
     */
    private static function selection(array $input): array
    {
        $value = $input['selection'] ?? 'none';
        $options = is_array($value) ? $value : [];
        $mode = self::enum($options['mode'] ?? $value, ['none', 'single', 'multiple'], 'selection');
        $rowKey = $options['rowKey'] ?? $input['rowKey'] ?? 'id';

        if (! is_string($rowKey) || trim($rowKey) === '') {
            throw new InvalidArgumentException('Table rowKey must be a non-empty string.');
        }

        return [
            'mode' => $mode,
            'rowKey' => $rowKey,
            'selectFiltered' => ($options['selectFiltered'] ?? true) === true,
            'summaryVisibility' => self::enum($options['summaryVisibility'] ?? 'always', ['always', 'after-first-selection'], 'selection.summaryVisibility'),
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{mode: string, key: string}|null
     */
    private static function persistence(array $input): ?array
    {
        $mode = $input['persistState'] ?? null;

        if ($mode === null || $mode === false || $mode === 'none') {
            return null;
        }

        $mode = self::enum($mode, ['url', 'local'], 'persistState');
        $key = $input['stateKey'] ?? null;

        if (! is_string($key) || trim($key) === '') {
            throw new InvalidArgumentException('Table stateKey is required when state persistence is enabled.');
        }

        return ['mode' => $mode, 'key' => $key];
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  list<int>  $options
     */
    private static function pageSize(array $input, array $options): int
    {
        $initialState = is_array($input['initialState'] ?? null) ? $input['initialState'] : [];
        $pagination = is_array($initialState['pagination'] ?? null) ? $initialState['pagination'] : [];
        $pageSize = $pagination['pageSize'] ?? $input['pageSize'] ?? $options[0];

        if (! is_int($pageSize) || $pageSize < 1) {
            throw new InvalidArgumentException('Table pageSize must be a positive integer.');
        }

        return $pageSize;
    }

    /** @return list<int> */
    private static function pageSizeOptions(mixed $options): array
    {
        $options = self::array($options, 'pageSizeOptions');
        $normalized = array_values(array_unique(array_filter($options, fn (mixed $option): bool => is_int($option) && $option > 0)));
        sort($normalized);

        if ($normalized === []) {
            throw new InvalidArgumentException('Table pageSizeOptions must contain at least one positive integer.');
        }

        return $normalized;
    }

    /** @param list<string> $allowed */
    private static function enum(mixed $value, array $allowed, string $name): string
    {
        if (! is_string($value) || ! in_array($value, $allowed, true)) {
            throw new InvalidArgumentException("Invalid table {$name} value.");
        }

        return $value;
    }

    /** @return array<mixed> */
    private static function array(mixed $value, string $name): array
    {
        if (! is_array($value)) {
            throw new InvalidArgumentException("Table {$name} must be an array.");
        }

        return $value;
    }

    /**
     * @param  array<mixed>  $value
     * @return array<string, mixed>
     */
    private static function stringKeyedArray(array $value, string $name): array
    {
        $result = [];

        foreach ($value as $key => $item) {
            if (! is_string($key)) {
                throw new InvalidArgumentException("Table {$name} keys must be strings.");
            }

            $result[$key] = $item;
        }

        return $result;
    }
}
