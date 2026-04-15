@props([
    'columns' => [],
    'rows' => [],
    'mode' => 'client',
    'endpoint' => null,
    'method' => 'GET',
    'serverAdapter' => null,
    'persistState' => false,
    'stateKey' => null,
    'globalFilterKey' => 'global',
    'filters' => [],
    'initialState' => [],
    'pageSizeOptions' => [10, 25, 50],
    'search' => true,
    'columnVisibility' => false,
    'caption' => null,
    'size' => null,
    'zebra' => false,
    'pinRows' => false,
    'pinCols' => false,
    'emptyLabel' => null,
    'loadingLabel' => null,
    'errorLabel' => null,
    'containerClass' => 'rounded-box border border-base-content/5 bg-base-100 p-4',
    'tableClass' => 'w-full',
])

@php
    $resolvedMode = $mode === 'server' ? 'server' : 'client';
    $resolvedServerAdapter = $serverAdapter === 'spatie-query-builder' ? 'spatie-query-builder' : null;
    $resolvedPersistState = in_array($persistState, ['url', 'local'], true) ? $persistState : false;

    if ($resolvedMode === 'server' && blank($endpoint)) {
        throw new InvalidArgumentException('The table component requires an endpoint prop when mode is set to server.');
    }

    if ($resolvedMode !== 'server' && $resolvedServerAdapter !== null) {
        throw new InvalidArgumentException('The table component only allows a serverAdapter when mode is set to server.');
    }

    $sizeMap = ['xs', 'sm', 'md', 'lg', 'xl'];
    $tableClasses = 'table';

    if ($zebra) {
        $tableClasses .= ' table-zebra';
    }

    if (in_array($size, $sizeMap, true)) {
        $tableClasses .= ' table-'.$size;
    }

    if ($pinRows) {
        $tableClasses .= ' table-pin-rows';
    }

    if ($pinCols) {
        $tableClasses .= ' table-pin-cols';
    }

    $tableClasses = trim($tableClasses.' '.$tableClass);
    $wrapperClasses = trim('daisy-table-shell space-y-4 '.$containerClass);

    // `key` stays the stable client-side identifier, while `sortKey` / `filterKey`
    // allow the host app to point server requests at different backend field names.
    $normalizeColumn = static function (array $column): array {
        $key = is_string($column['key'] ?? null) ? trim($column['key']) : '';
        $filterConfig = is_array($column['filter'] ?? null) ? $column['filter'] : [];
        $filterType = in_array($filterConfig['type'] ?? null, ['text', 'select', 'boolean'], true)
            ? $filterConfig['type']
            : null;

        return [
            'key' => $key,
            'label' => $column['label'] ?? $column['title'] ?? $key,
            'sortable' => (bool) ($column['sortable'] ?? false),
            'filterable' => (bool) ($column['filterable'] ?? false),
            'sortKey' => is_string($column['sortKey'] ?? null) && filled($column['sortKey']) ? $column['sortKey'] : $key,
            'filterKey' => is_string($column['filterKey'] ?? null) && filled($column['filterKey']) ? $column['filterKey'] : $key,
            'visible' => (bool) ($column['visible'] ?? true),
            'width' => $column['width'] ?? null,
            'cellClass' => $column['cellClass'] ?? '',
            'headerClass' => $column['headerClass'] ?? '',
            'html' => (bool) ($column['html'] ?? false),
            'filter' => $filterType ? [
                'type' => $filterType,
                'options' => array_values(array_filter(
                    is_array($filterConfig['options'] ?? null) ? $filterConfig['options'] : [],
                    static fn ($option) => is_array($option) && filled($option['value'] ?? null)
                )),
            ] : null,
        ];
    };

    $normalizeToolbarFilter = static function (array $filter): array {
        $key = is_string($filter['key'] ?? $filter['id'] ?? null) ? trim((string) ($filter['key'] ?? $filter['id'])) : '';
        $type = in_array($filter['type'] ?? null, ['text', 'select', 'boolean'], true) ? $filter['type'] : null;

        return [
            'id' => $key,
            'label' => $filter['label'] ?? $key,
            'type' => $type,
            'filterKey' => is_string($filter['filterKey'] ?? null) && filled($filter['filterKey']) ? $filter['filterKey'] : $key,
            'options' => array_values(array_filter(
                is_array($filter['options'] ?? null) ? $filter['options'] : [],
                static fn ($option) => is_array($option) && filled($option['value'] ?? null)
            )),
        ];
    };

    $resolvedColumns = array_values(array_filter(
        array_map($normalizeColumn, is_array($columns) ? $columns : []),
        static fn (array $column) => $column['key'] !== ''
    ));

    $columnFilters = collect($resolvedColumns)
        ->filter(fn (array $column) => $column['filterable'] && is_array($column['filter']))
        ->map(fn (array $column) => [
            'id' => $column['key'],
            'label' => $column['label'],
            'type' => $column['filter']['type'],
            'filterKey' => $column['filterKey'],
            'options' => $column['filter']['options'] ?? [],
        ]);

    $toolbarFilters = collect(is_array($filters) ? $filters : [])
        ->map($normalizeToolbarFilter)
        ->filter(fn (array $filter) => $filter['id'] !== '' && $filter['type'] !== null);

    $resolvedFilters = $columnFilters
        ->merge($toolbarFilters)
        ->unique('id')
        ->values()
        ->all();

    $resolvedPageSizeOptions = array_values(array_unique(array_filter(
        array_map(static fn ($value) => is_numeric($value) ? (int) $value : null, is_array($pageSizeOptions) ? $pageSizeOptions : []),
        static fn ($value) => is_int($value) && $value > 0
    )));

    if ($resolvedPageSizeOptions === []) {
        $resolvedPageSizeOptions = [10, 25, 50];
    }

    $filterIds = collect($resolvedFilters)->pluck('id')->all();

    $resolvedInitialState = [
        'sorting' => array_values(array_filter(
            is_array($initialState['sorting'] ?? null) ? $initialState['sorting'] : [],
            static fn ($entry) => filled($entry['id'] ?? null)
        )),
        'pagination' => [
            'pageIndex' => max(0, (int) data_get($initialState, 'pagination.pageIndex', 0)),
            'pageSize' => in_array((int) data_get($initialState, 'pagination.pageSize', $resolvedPageSizeOptions[0]), $resolvedPageSizeOptions, true)
                ? (int) data_get($initialState, 'pagination.pageSize', $resolvedPageSizeOptions[0])
                : $resolvedPageSizeOptions[0],
        ],
        'globalFilter' => (string) ($initialState['globalFilter'] ?? ''),
        'columnFilters' => array_values(array_filter(
            is_array($initialState['columnFilters'] ?? null) ? $initialState['columnFilters'] : [],
            static fn ($entry) => in_array($entry['id'] ?? null, $filterIds, true)
        )),
        'columnVisibility' => array_reduce($resolvedColumns, function (array $carry, array $column) use ($initialState): array {
            $carry[$column['key']] = data_get($initialState, 'columnVisibility.'.$column['key'], $column['visible']) !== false;

            return $carry;
        }, []),
    ];

    $resolvedEndpoint = is_array($endpoint) ? $endpoint : (filled($endpoint) ? ['url' => $endpoint] : null);

    $resolvedRows = $resolvedMode === 'client' && is_iterable($rows)
        ? collect($rows)->map(fn ($row) => is_array($row) ? $row : (array) $row)->values()->all()
        : [];

    $renderCell = static function (array $row, array $column) {
        $value = data_get($row, $column['key']);

        if ($column['html']) {
            return new Illuminate\Support\HtmlString((string) $value);
        }

        return $value;
    };

    // Keep the serialized config explicit so the JS runtime can switch transport
    // adapters without exposing a generic frontend options surface.
    $config = [
        'mode' => $resolvedMode,
        'method' => strtoupper((string) $method),
        'serverAdapter' => $resolvedServerAdapter,
        'persistState' => $resolvedPersistState,
        'stateKey' => $stateKey,
        'globalFilterKey' => filled($globalFilterKey) ? (string) $globalFilterKey : 'global',
        'endpoint' => $resolvedEndpoint,
        'columns' => $resolvedColumns,
        'filters' => $resolvedFilters,
        'rows' => $resolvedRows,
        'initialState' => $resolvedInitialState,
        'pageSizeOptions' => $resolvedPageSizeOptions,
        'search' => (bool) $search,
        'columnVisibility' => (bool) $columnVisibility,
        'emptyLabel' => $emptyLabel ?: __('daisy::common.no_results'),
        'loadingLabel' => $loadingLabel ?: __('daisy::common.loading'),
        'errorLabel' => $errorLabel ?: __('daisy::components.table_error'),
        'labels' => [
            'search' => __('daisy::common.search'),
            'rowsPerPage' => __('daisy::components.rows_per_page'),
            'showingResults' => __('daisy::components.showing_results'),
            'previous' => __('daisy::components.table_previous'),
            'next' => __('daisy::components.table_next'),
            'columns' => __('daisy::components.table_columns'),
            'page' => __('daisy::components.table_page'),
            'filters' => __('daisy::components.table_filters'),
            'all' => __('daisy::common.all'),
        ],
    ];
@endphp

<div
    data-daisy-table="1"
    data-table-config='@json($config)'
    class="{{ $wrapperClasses }}"
>
    <div class="daisy-table-toolbar flex flex-wrap items-center justify-between gap-3">
        @if($search)
            <label class="input input-sm flex w-full max-w-sm items-center gap-2">
                <span class="text-base-content/70">{{ __('daisy::common.search') }}</span>
                <input
                    type="search"
                    class="daisy-table-search grow"
                    data-table-search
                    placeholder="{{ __('daisy::common.search') }}"
                >
            </label>
        @else
            <div></div>
        @endif

        <div class="flex flex-wrap items-center gap-3">
            @foreach($resolvedFilters as $filter)
                @if($filter['type'] === 'text')
                    <label class="input input-sm flex items-center gap-2">
                        <span class="text-base-content/70">{{ $filter['label'] }}</span>
                        <input
                            type="text"
                            class="grow"
                            data-table-filter="{{ $filter['id'] }}"
                            data-table-filter-type="text"
                            placeholder="{{ $filter['label'] }}"
                        >
                    </label>
                @elseif($filter['type'] === 'select')
                    <label class="label flex items-center gap-2">
                        <span class="label-text text-sm text-base-content/70">{{ $filter['label'] }}</span>
                        <select
                            class="select select-sm"
                            data-table-filter="{{ $filter['id'] }}"
                            data-table-filter-type="select"
                        >
                            <option value="">{{ __('daisy::common.all') }}</option>
                            @foreach($filter['options'] as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] ?? $option['value'] }}</option>
                            @endforeach
                        </select>
                    </label>
                @elseif($filter['type'] === 'boolean')
                    <label class="label cursor-pointer gap-2">
                        <span class="label-text text-sm text-base-content/70">{{ $filter['label'] }}</span>
                        <input
                            type="checkbox"
                            class="toggle toggle-sm"
                            data-table-filter="{{ $filter['id'] }}"
                            data-table-filter-type="boolean"
                        >
                    </label>
                @endif
            @endforeach

            <label class="label flex items-center gap-2">
                <span class="label-text text-sm text-base-content/70">{{ __('daisy::components.rows_per_page') }}</span>
                <select class="select select-sm" data-table-page-size>
                    @foreach($resolvedPageSizeOptions as $option)
                        <option value="{{ $option }}" @selected($option === $resolvedInitialState['pagination']['pageSize'])>{{ $option }}</option>
                    @endforeach
                </select>
            </label>

            @if($columnVisibility)
                <details class="dropdown dropdown-end">
                    <summary class="btn btn-sm btn-ghost">{{ __('daisy::components.table_columns') }}</summary>
                    <div class="daisy-table-column-menu dropdown-content rounded-box border border-base-content/10 bg-base-100 p-1 shadow" data-table-column-menu></div>
                </details>
            @endif
        </div>
    </div>

    <div class="daisy-table-scroll overflow-x-auto">
        <table {{ $attributes->merge(['class' => $tableClasses]) }}>
            @if($caption)
                <caption class="text-start text-xs opacity-70">{{ $caption }}</caption>
            @endif

            <thead>
                <tr data-table-head-row>
                    @foreach($resolvedColumns as $column)
                        @continue(data_get($resolvedInitialState, 'columnVisibility.'.$column['key']) === false)

                        <th
                            @class([$column['headerClass']])
                            @if($column['width']) style="width: {{ $column['width'] }}" @endif
                        >
                            @if($column['sortable'])
                                <button
                                    type="button"
                                    class="daisy-table-head-button"
                                    data-table-sort="{{ $column['key'] }}"
                                    aria-sort="none"
                                >
                                    {{ $column['label'] }}
                                    <span class="daisy-table-sort-indicator" aria-hidden="true">&harr;</span>
                                </button>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody data-table-body>
                @if($resolvedMode === 'client' && count($resolvedRows) > 0)
                    @foreach($resolvedRows as $row)
                        <tr>
                            @foreach($resolvedColumns as $column)
                                @continue(data_get($resolvedInitialState, 'columnVisibility.'.$column['key']) === false)

                                @php
                                    $value = $renderCell($row, $column);
                                @endphp

                                <td class="{{ $column['cellClass'] }}">
                                    @if($value instanceof Illuminate\Support\HtmlString)
                                        {!! $value !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @elseif($resolvedMode === 'server')
                    <tr class="daisy-table-loading-row">
                        <td colspan="{{ max(1, count($resolvedColumns)) }}">{{ $loadingLabel ?: __('daisy::common.loading') }}</td>
                    </tr>
                @else
                    <tr class="daisy-table-empty-row">
                        <td colspan="{{ max(1, count($resolvedColumns)) }}">{{ $emptyLabel ?: __('daisy::common.no_results') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="daisy-table-footer flex flex-wrap items-center justify-between gap-3">
        <p class="daisy-table-status text-sm text-base-content/70" data-table-info>
            {{ __('daisy::components.showing_results', ['from' => count($resolvedRows) > 0 ? 1 : 0, 'to' => count($resolvedRows), 'total' => count($resolvedRows)]) }}
        </p>

        <div class="flex items-center gap-3">
            <span class="text-sm text-base-content/70" data-table-page-indicator>
                {{ __('daisy::components.table_page', ['page' => 1, 'pages' => 1]) }}
            </span>

            <div class="join">
                <button type="button" class="btn btn-sm join-item" data-table-prev>{{ __('daisy::components.table_previous') }}</button>
                <button type="button" class="btn btn-sm join-item" data-table-next>{{ __('daisy::components.table_next') }}</button>
            </div>
        </div>
    </div>
</div>
