@props([
    'columns' => [],
    'rows' => [],
    'mode' => 'client',
    'endpoint' => null,
    'serverAdapter' => null,
    'globalFilterKey' => 'global',
    'filters' => [],
    'filterMode' => 'instant',
    'pageSize' => 10,
    'pageSizeOptions' => [10, 25, 50, 100],
    'search' => true,
    'searchDebounce' => 250,
    'searchMode' => 'fuzzy',
    'columnVisibility' => true,
    'selection' => null,
    'rowKey' => 'id',
    'persistState' => null,
    'stateKey' => null,
    'caption' => null,
    'size' => 'md',
    'zebra' => true,
    'hover' => true,
    'layout' => 'auto',
    'bulkActions' => [],
    'rowActions' => [],
    'rowDetails' => null,
    'editable' => false,
    'initialState' => [],
])

@php
    $table = \Art35rennes\DaisyKit\Table\TableConfiguration::make([
        'columns' => $columns,
        'rows' => $rows,
        'mode' => $mode,
        'endpoint' => $endpoint,
        'serverAdapter' => $serverAdapter,
        'globalFilterKey' => $globalFilterKey,
        'filters' => $filters,
        'filterMode' => $filterMode,
        'pageSize' => $pageSize,
        'pageSizeOptions' => $pageSizeOptions,
        'search' => $search,
        'searchDebounce' => $searchDebounce,
        'searchMode' => $searchMode,
        'columnVisibility' => $columnVisibility,
        'selection' => $selection,
        'rowKey' => $rowKey,
        'persistState' => $persistState,
        'stateKey' => $stateKey,
        'caption' => $caption,
        'size' => $size,
        'zebra' => $zebra,
        'hover' => $hover,
        'layout' => $layout,
        'bulkActions' => $bulkActions,
        'rowActions' => $rowActions,
        'rowDetails' => $rowDetails,
        'editable' => $editable,
        'initialState' => $initialState,
    ]);
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode($table['configuration']);
    $tableView = $table['view'];
@endphp

<section
    {{ $attributes
        ->except(['aria-busy', 'data-daisy-kit-config', 'data-daisy-kit-module', 'data-daisy-kit-state'])
        ->class(['daisy-kit-table', 'card', 'border', 'border-base-300', 'bg-base-100', 'shadow-sm']) }}
    aria-busy="true"
    data-daisy-kit-module="table"
>
    <p class="daisy-kit-table__status alert alert-info" data-daisy-kit-status hidden role="status" aria-live="polite"></p>

    <div class="daisy-kit-table__content" data-daisy-kit-content>
        @include('daisy-kit::internal.table.toolbar', ['tableView' => $tableView])
        @include('daisy-kit::internal.table.filters', ['tableView' => $tableView])
        @include('daisy-kit::internal.table.selection', ['tableView' => $tableView])
        @include('daisy-kit::internal.table.table', ['tableView' => $tableView])
        @include('daisy-kit::internal.table.pagination', ['tableView' => $tableView])
    </div>

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
