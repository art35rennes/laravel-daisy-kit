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
    'searchDebounce' => 500,
    'filterDebounce' => 500,
    'minSearchChars' => 3,
    'columnVisibility' => false,
    'tableLayout' => 'auto',
    'minWidth' => null,
    'scrollX' => 'auto',
    'externalFilters' => false,
    'toolbarLayout' => 'default',
    'livewireMode' => 'none',
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
    $resolvedSearchDebounce = max(0, (int) $searchDebounce);
    $resolvedFilterDebounce = max(0, (int) $filterDebounce);
    $resolvedMinSearchChars = max(0, (int) $minSearchChars);
    $resolvedTableLayout = $tableLayout === 'fixed' ? 'fixed' : 'auto';
    $resolvedScrollX = in_array($scrollX, ['auto', 'always', 'none'], true) ? $scrollX : 'auto';
    $resolvedToolbarLayout = in_array($toolbarLayout, ['default', 'split', 'hidden'], true) ? $toolbarLayout : 'default';
    $resolvedLivewireMode = in_array($livewireMode, ['ignore', 'morph', 'none'], true) ? $livewireMode : 'none';
    $resolvedExternalFilters = (bool) $externalFilters;
    $hasToolbarStartSlot = isset($toolbarStart) && $toolbarStart instanceof \Illuminate\View\ComponentSlot;
    $hasToolbarSlot = isset($toolbar) && $toolbar instanceof \Illuminate\View\ComponentSlot;
    $hasToolbarEndSlot = isset($toolbarEnd) && $toolbarEnd instanceof \Illuminate\View\ComponentSlot;
    $hasFiltersSlot = isset($filtersSlot) && $filtersSlot instanceof \Illuminate\View\ComponentSlot;
    $hasActionsSlot = isset($actions) && $actions instanceof \Illuminate\View\ComponentSlot;
    $hasControlsSlot = isset($controls) && $controls instanceof \Illuminate\View\ComponentSlot;

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

    $tableClasses .= $resolvedTableLayout === 'fixed' ? ' table-fixed' : ' table-auto';

    $tableClasses = trim($tableClasses.' '.$tableClass);
    $wrapperClasses = trim('daisy-table-shell space-y-4 '.$containerClass);
    $scrollClasses = match ($resolvedScrollX) {
        'always' => 'daisy-table-scroll daisy-table-scroll-always overflow-x-scroll',
        'none' => 'daisy-table-scroll daisy-table-scroll-none overflow-x-visible',
        default => 'daisy-table-scroll overflow-x-auto',
    };

    // `key` stays the stable client-side identifier, while `sortKey` / `filterKey`
    // allow the host app to point server requests at different backend field names.
    $numericClass = static function ($value, string $prefix): ?string {
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
    };

    $columnClasses = static function (array $column, string $target): string {
        $classes = [];

        if ($target === 'header' && filled($column['headerClass'])) {
            $classes[] = $column['headerClass'];
        }

        if ($target === 'cell' && filled($column['cellClass'])) {
            $classes[] = $column['cellClass'];
        }

        array_push($classes, ...array_filter([
            $column['widthClass'],
            $column['minWidthClass'],
            $column['maxWidthClass'],
            $column['alignClass'],
            $column['verticalAlignClass'],
            $column['paddingClass'],
            $column['densityClass'],
            $column['nowrap'] ? 'whitespace-nowrap' : null,
            $column['type'] === 'actions' ? 'daisy-table-actions-cell' : null,
        ]));

        return trim(implode(' ', array_unique(array_filter($classes))));
    };

    $wrapperClassesForColumn = static function (array $column, string $target): string {
        $classes = [$target === 'header' ? 'daisy-table-header-content' : 'daisy-table-cell-content'];

        if ($target === 'header' && filled($column['headerWrapperClass'])) {
            $classes[] = $column['headerWrapperClass'];
        }

        if ($target === 'cell' && filled($column['cellWrapperClass'])) {
            $classes[] = $column['cellWrapperClass'];
        }

        if ($target === 'cell' && $column['type'] === 'actions') {
            $classes[] = 'daisy-table-actions-content';
        }

        if ($target === 'cell') {
            if ($column['truncate'] === 'line') {
                $classes[] = 'truncate';
            } elseif (in_array($column['truncate'], [2, 3], true)) {
                $classes[] = 'line-clamp-'.$column['truncate'];
            }
        }

        return trim(implode(' ', array_unique(array_filter($classes))));
    };

    $normalizeColumn = static function (array $column) use ($numericClass): array {
        $key = is_string($column['key'] ?? null) ? trim($column['key']) : '';
        $filterConfig = is_array($column['filter'] ?? null) ? $column['filter'] : [];
        $filterType = in_array($filterConfig['type'] ?? null, ['text', 'select', 'boolean'], true)
            ? $filterConfig['type']
            : null;
        $type = ($column['type'] ?? null) === 'actions' ? 'actions' : null;
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
            'widthClass' => $width === 'fit' ? 'daisy-table-width-fit' : ($width === 'auto' ? null : $numericClass($width, 'daisy-table-width')),
            'minWidthClass' => $minWidth === 'max-content' ? 'daisy-table-min-width-max' : ($minWidth === 'full' ? 'min-w-full' : $numericClass($minWidth, 'daisy-table-min-width')),
            'maxWidthClass' => $numericClass($maxWidth, 'daisy-table-max-width'),
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

    $tableMinWidthClass = $minWidth === 'full'
        ? 'min-w-full'
        : ($numericClass($minWidth, 'daisy-table-root-min-width') ?? null);

    $tableClasses = trim($tableClasses.' '.$tableMinWidthClass);

    $resolvedColumns = array_values(array_filter(
        array_map($normalizeColumn, is_array($columns) ? $columns : []),
        static fn (array $column) => $column['key'] !== ''
    ));

    if (is_array($columns) && $columns !== [] && $resolvedColumns === []) {
        throw new InvalidArgumentException('The table component requires at least one column with a non-empty key.');
    }

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

    $filterPriority = static function (array $filter): int {
        $id = strtolower(str_replace(['-', '.'], '_', (string) ($filter['id'] ?? '')));

        $priorities = [
            'reference_internal' => 10,
            'reference' => 11,
            'code' => 12,
            'name' => 13,
            'email' => 14,
            'city' => 20,
            'agent_affected' => 21,
            'compliance' => 22,
            'company' => 30,
            'country' => 31,
            'contract_tree_path' => 32,
            'contract_scope_path' => 33,
            'document_kind' => 40,
            'compile_status' => 41,
            'version' => 42,
            'guard_name' => 43,
            'description' => 44,
        ];

        if (array_key_exists($id, $priorities)) {
            return $priorities[$id];
        }

        return str_contains($id, 'intervention') ? 45 : 99;
    };

    $resolvedFilters = $columnFilters
        ->merge($toolbarFilters)
        ->values()
        ->map(fn (array $filter, int $index) => $filter + ['_originalIndex' => $index])
        ->unique('id')
        ->sort(fn (array $first, array $second) => [
            $filterPriority($first),
            $first['_originalIndex'],
        ] <=> [
            $filterPriority($second),
            $second['_originalIndex'],
        ])
        ->map(function (array $filter): array {
            unset($filter['_originalIndex']);

            return $filter;
        })
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
        'externalFilters' => $resolvedExternalFilters,
        'livewireMode' => $resolvedLivewireMode,
        'rows' => $resolvedRows,
        'initialState' => $resolvedInitialState,
        'pageSizeOptions' => $resolvedPageSizeOptions,
        'search' => (bool) $search,
        'searchDebounceMs' => $resolvedSearchDebounce,
        'filterDebounceMs' => $resolvedFilterDebounce,
        'minSearchChars' => $resolvedMinSearchChars,
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
    data-table-layout="{{ $resolvedTableLayout }}"
    data-table-scroll-x="{{ $resolvedScrollX }}"
    data-table-livewire-mode="{{ $resolvedLivewireMode }}"
    @if(filled($minWidth)) data-table-min-width="{{ $minWidth }}" @endif
    data-table-config='@json($config)'
    class="{{ $wrapperClasses }}"
    @if($resolvedLivewireMode === 'ignore') wire:ignore @endif
>
    @if($resolvedToolbarLayout !== 'hidden')
        <div @class([
            'daisy-table-toolbar grid gap-3',
            'md:grid-cols-[minmax(0,1fr)_auto]' => $resolvedToolbarLayout === 'split',
        ])>
            <div class="daisy-table-toolbar-start flex flex-wrap items-center gap-3">
                @if($hasToolbarStartSlot)
                    {{ $toolbarStart }}
                @endif

                @if($hasToolbarSlot)
                    {{ $toolbar }}
                @elseif($search)
                    <label class="input input-sm flex w-full max-w-sm items-center gap-2">
                        <span class="text-base-content/70">{{ __('daisy::common.search') }}</span>
                        <input
                            type="search"
                            class="daisy-table-search grow"
                            data-table-search
                            placeholder="{{ __('daisy::common.search') }}"
                        >
                    </label>
                @endif
            </div>

            @if($hasToolbarEndSlot)
                <div class="daisy-table-toolbar-end flex flex-wrap items-center justify-end gap-3">
                    {{ $toolbarEnd }}
                </div>
            @endif

            @if($hasFiltersSlot)
                <div class="daisy-table-external-filters">
                    {{ $filtersSlot }}
                </div>
            @endif

            @if(! $resolvedExternalFilters && count($resolvedFilters) > 0)
                <div class="daisy-table-filters grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach($resolvedFilters as $filter)
                    @if($filter['type'] === 'text')
                        <label class="input input-sm flex w-full min-w-0 items-center gap-2">
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
                        <label class="label flex w-full min-w-0 items-center gap-2">
                            <span class="label-text text-sm text-base-content/70">{{ $filter['label'] }}</span>
                            <select
                                class="select select-sm min-w-0 flex-1"
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
                        <label class="label w-full cursor-pointer gap-2">
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
                </div>
            @endif

            <div class="daisy-table-controls flex flex-wrap items-center justify-end gap-3">
                @if($hasActionsSlot)
                    {{ $actions }}
                @endif

                @if($hasControlsSlot)
                    {{ $controls }}
                @endif

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
    @endif

    <div class="{{ $scrollClasses }}" @if($resolvedScrollX !== 'none') tabindex="0" @endif>
        <table
            {{ $attributes->merge(['class' => $tableClasses]) }}
            data-table-layout="{{ $resolvedTableLayout }}"
        >
            @if($caption)
                <caption class="text-start text-xs opacity-70">{{ $caption }}</caption>
            @endif

            <colgroup data-table-colgroup>
                @foreach($resolvedColumns as $column)
                    @continue(data_get($resolvedInitialState, 'columnVisibility.'.$column['key']) === false)

                    <col @class([$column['widthClass'], $column['minWidthClass'], $column['maxWidthClass']])>
                @endforeach
            </colgroup>

            <thead>
                <tr data-table-head-row>
                    @foreach($resolvedColumns as $column)
                        @continue(data_get($resolvedInitialState, 'columnVisibility.'.$column['key']) === false)

                        <th
                            class="{{ $columnClasses($column, 'header') }}"
                        >
                            @if($column['sortable'])
                                <button
                                    type="button"
                                    class="daisy-table-head-button"
                                    data-table-sort="{{ $column['key'] }}"
                                    aria-sort="none"
                                >
                                    <span class="{{ $wrapperClassesForColumn($column, 'header') }}">
                                        {{ $column['label'] }}
                                        <span class="daisy-table-sort-indicator" aria-hidden="true">&harr;</span>
                                    </span>
                                </button>
                            @else
                                <span class="{{ $wrapperClassesForColumn($column, 'header') }}">{{ $column['label'] }}</span>
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

                                <td class="{{ $columnClasses($column, 'cell') }}">
                                    <span class="{{ $wrapperClassesForColumn($column, 'cell') }}">
                                        @if($value instanceof Illuminate\Support\HtmlString)
                                            {!! $value !!}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
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
        @isset($footer)
            {{ $footer }}
        @else
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
        @endisset
    </div>
</div>
