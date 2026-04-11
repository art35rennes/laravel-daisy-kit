@props([
    'columns' => [],
    'rows' => [],
    'rowKey' => 'id',
    'size' => null,
    'zebra' => false,
    'pinRows' => false,
    'pinCols' => false,
    'caption' => null,
    'mode' => 'auto', // auto | server | client
    'queryBuilder' => false,
    'selectable' => 'none', // none | single | multiple
    'selected' => [],
    'sortBy' => null,
    'sortDirection' => 'asc', // asc | desc
    'sortUrls' => [],
    'sortParameter' => 'sort',
    'loading' => false,
    'emptyTitle' => __('daisy::common.empty'),
    'emptyDescription' => null,
    'paginator' => null,
    'pageParameter' => 'page',
    'perPage' => null,
    'perPageOptions' => [],
    'perPageParameter' => 'per_page',
    'query' => null,
    'searchable' => false,
    'searchValue' => null,
    'searchPlaceholder' => null,
    'searchParameter' => 'search',
])

@php
    $baseQuery = is_array($query) ? $query : request()->query();
    $normalizedSelected = array_values(array_map('strval', is_array($selected) ? $selected : []));
    $isSelectable = in_array($selectable, ['single', 'multiple'], true);
    $isMultiple = $selectable === 'multiple';
    $selectionGroup = 'advanced_table_selection_'.str_replace('.', '_', uniqid('', true));
    $perPageOptions = array_values(array_filter(array_map('intval', is_array($perPageOptions) ? $perPageOptions : []), fn ($value) => $value > 0));
    $queryBuilder = (bool) $queryBuilder;

    if ($mode === 'auto') {
        $mode = $queryBuilder || $paginator ? 'server' : 'client';
    }

    $mode = in_array($mode, ['server', 'client'], true) ? $mode : 'server';
    $isServerMode = $mode === 'server';
    $isClientMode = $mode === 'client';

    $flattenQueryInputs = static function (array $values, ?string $prefix = null) use (&$flattenQueryInputs): array {
        $inputs = [];

        foreach ($values as $key => $value) {
            $inputName = $prefix ? $prefix.'['.$key.']' : (string) $key;

            if (is_array($value)) {
                $inputs = [...$inputs, ...$flattenQueryInputs($value, $inputName)];
                continue;
            }

            $inputs[] = [
                'name' => $inputName,
                'value' => $value,
            ];
        }

        return $inputs;
    };

    $buildQueryUrl = static function (array $overrides = [], array $remove = []) use ($baseQuery) {
        $query = $baseQuery;

        foreach ($remove as $key) {
            unset($query[$key]);
        }

        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($query[$key]);
            } else {
                $query[$key] = $value;
            }
        }

        $queryString = http_build_query($query);

        return $queryString === '' ? request()->url() : request()->url().'?'.$queryString;
    };

    $resolveAlignment = static function (?string $alignment): string {
        return match ($alignment) {
            'center' => 'text-center',
            'end' => 'text-right',
            default => 'text-left',
        };
    };

    $resolveValue = static function ($row, array $column) {
        $key = $column['key'] ?? null;

        if (! $key) {
            return null;
        }

        return data_get($row, $key);
    };

    $resolveRowId = static function ($row) use ($rowKey): string {
        $value = data_get($row, $rowKey);

        return is_scalar($value) ? (string) $value : '';
    };

    $normalizeFilterOptions = static function ($options): array {
        $normalized = [];

        foreach ((array) $options as $key => $option) {
            if (is_array($option)) {
                $normalized[] = [
                    'value' => (string) ($option['value'] ?? $key),
                    'label' => (string) ($option['label'] ?? $option['value'] ?? $key),
                ];

                continue;
            }

            $normalized[] = [
                'value' => is_string($key) ? $key : (string) $option,
                'label' => (string) $option,
            ];
        }

        return $normalized;
    };

    $resolveFilterValue = static function (array $column) use ($baseQuery, $queryBuilder) {
        $filterKey = $column['filterKey'] ?? $column['key'] ?? null;

        if (! $filterKey) {
            return null;
        }

        if ($queryBuilder) {
            return data_get($baseQuery, 'filter.'.$filterKey);
        }

        return $baseQuery[$filterKey] ?? null;
    };

    $toolbarSlot = isset($toolbar) ? trim((string) $toolbar) : '';
    $emptySlot = isset($empty) ? trim((string) $empty) : '';
    $afterTableSlot = isset($afterTable) ? trim((string) $afterTable) : '';

    $searchParameterName = $queryBuilder ? 'filter['.$searchParameter.']' : $searchParameter;
    $searchTerm = $searchValue
        ?? ($queryBuilder ? data_get($baseQuery, 'filter.'.$searchParameter) : ($baseQuery[$searchParameter] ?? ''));
    $searchPlaceholder = $searchPlaceholder ?: __('daisy::common.search');
    $hasSearch = (bool) $searchable;

    $filterColumns = [];
    foreach ($columns as $column) {
        if (! ($column['filterable'] ?? false)) {
            continue;
        }

        $filterColumns[] = [
            'key' => $column['key'] ?? null,
            'label' => $column['label'] ?? ($column['key'] ?? ''),
            'filterKey' => $column['filterKey'] ?? ($column['key'] ?? null),
            'options' => $normalizeFilterOptions($column['filterOptions'] ?? []),
            'value' => $resolveFilterValue($column),
        ];
    }

    $pagination = null;
    if ($isServerMode && is_object($paginator) && method_exists($paginator, 'currentPage') && method_exists($paginator, 'lastPage')) {
        $pagination = [
            'current' => (int) $paginator->currentPage(),
            'last' => max(1, (int) $paginator->lastPage()),
            'perPage' => method_exists($paginator, 'perPage') ? (int) $paginator->perPage() : null,
            'total' => method_exists($paginator, 'total') ? (int) $paginator->total() : null,
            'from' => method_exists($paginator, 'firstItem') ? $paginator->firstItem() : null,
            'to' => method_exists($paginator, 'lastItem') ? $paginator->lastItem() : null,
            'prev' => null,
            'next' => null,
            'pages' => [],
        ];

        for ($page = 1; $page <= $pagination['last']; $page++) {
            $pagination['pages'][$page] = $buildQueryUrl([$pageParameter => $page], []);
        }

        $pagination['prev'] = $pagination['current'] > 1 ? $buildQueryUrl([$pageParameter => $pagination['current'] - 1], []) : null;
        $pagination['next'] = $pagination['current'] < $pagination['last'] ? $buildQueryUrl([$pageParameter => $pagination['current'] + 1], []) : null;
    }

    $effectivePerPage = $perPage ?: ($pagination['perPage'] ?? null) ?: 10;
    if (! in_array((int) $effectivePerPage, $perPageOptions, true)) {
        $perPageOptions[] = (int) $effectivePerPage;
        sort($perPageOptions);
    }

    $pageLinks = [];
    if ($pagination) {
        $maxButtons = 7;
        if ($pagination['last'] <= $maxButtons) {
            for ($i = 1; $i <= $pagination['last']; $i++) {
                $pageLinks[] = $i;
            }
        } else {
            $pageLinks[] = 1;
            $window = $maxButtons - 2;
            $half = (int) floor($window / 2);
            $start = max(2, $pagination['current'] - $half);
            $end = min($pagination['last'] - 1, $start + $window - 1);
            $start = max(2, $end - $window + 1);
            if ($start > 2) $pageLinks[] = null;
            for ($i = $start; $i <= $end; $i++) $pageLinks[] = $i;
            if ($end < $pagination['last'] - 1) $pageLinks[] = null;
            $pageLinks[] = $pagination['last'];
        }
    }

    $columnCount = count($columns) + ($isSelectable ? 1 : 0);
    $visibleSelectedCount = 0;
    foreach ($rows as $row) {
        $rowId = $resolveRowId($row);
        if ($rowId !== '' && in_array($rowId, $normalizedSelected, true)) {
            $visibleSelectedCount++;
        }
    }
    $allVisibleSelected = $isMultiple && count($rows) > 0 && $visibleSelectedCount === count($rows);
    $hasMixedVisibleSelection = $isMultiple && $visibleSelectedCount > 0 && $visibleSelectedCount < count($rows);
    $clientOptions = [
        'pageSize' => (int) $effectivePerPage,
        'searchable' => $hasSearch,
        'sortBy' => $sortBy,
        'sortDirection' => $sortDirection,
        'queryBuilder' => $queryBuilder,
        'searchParameter' => $searchParameter,
        'sortParameter' => $sortParameter,
        'pageParameter' => $pageParameter,
        'perPageParameter' => $perPageParameter,
    ];
@endphp

<div
    {{ $attributes->merge(['class' => 'space-y-4']) }}
    data-advanced-table="1"
    data-selection="{{ $selectable }}"
    data-table-mode="{{ $mode }}"
    @if($isClientMode)
        data-client-options='@json($clientOptions)'
    @endif
>
    @if($toolbarSlot !== '' || $hasSearch || count($filterColumns) > 0)
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            @if($isServerMode && ($hasSearch || count($filterColumns) > 0))
                <form method="GET" action="{{ request()->url() }}" class="flex flex-1 flex-wrap items-end gap-3" data-table-server-controls>
                    @foreach($baseQuery as $queryKey => $queryValue)
                        @if(! in_array($queryKey, [$pageParameter, $sortParameter, 'filter', $perPageParameter], true))
                            @foreach($flattenQueryInputs([$queryKey => $queryValue]) as $queryInput)
                                <input type="hidden" name="{{ $queryInput['name'] }}" value="{{ $queryInput['value'] }}" />
                            @endforeach
                        @endif
                    @endforeach

                    @if($hasSearch)
                        <label class="form-control min-w-56 flex-1">
                            <span class="label-text mb-1 text-sm">{{ __('daisy::common.search') }}</span>
                            <input type="search" name="{{ $searchParameterName }}" value="{{ $searchTerm }}" class="input input-bordered w-full" placeholder="{{ $searchPlaceholder }}" />
                        </label>
                    @endif

                    @foreach($filterColumns as $filterColumn)
                        <label class="form-control min-w-44">
                            <span class="label-text mb-1 text-sm">{{ $filterColumn['label'] }}</span>
                            @if(count($filterColumn['options']) > 0)
                                <select
                                    name="{{ $queryBuilder ? 'filter['.$filterColumn['filterKey'].']' : $filterColumn['filterKey'] }}"
                                    class="select select-bordered"
                                >
                                    <option value="">{{ __('daisy::common.all') }}</option>
                                    @foreach($filterColumn['options'] as $option)
                                        <option value="{{ $option['value'] }}" @selected((string) $filterColumn['value'] === (string) $option['value'])>{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="search"
                                    name="{{ $queryBuilder ? 'filter['.$filterColumn['filterKey'].']' : $filterColumn['filterKey'] }}"
                                    value="{{ $filterColumn['value'] }}"
                                    class="input input-bordered"
                                    placeholder="{{ $filterColumn['label'] }}"
                                />
                            @endif
                        </label>
                    @endforeach

                    <button type="submit" class="btn btn-primary">{{ __('daisy::common.search') }}</button>
                </form>
            @elseif($isClientMode && ($hasSearch || count($filterColumns) > 0))
                <div class="flex flex-1 flex-wrap items-end gap-3" data-table-client-controls>
                    @if($hasSearch)
                        <label class="form-control min-w-56 flex-1">
                            <span class="label-text mb-1 text-sm">{{ __('daisy::common.search') }}</span>
                            <input type="search" value="{{ $searchTerm }}" class="input input-bordered w-full" placeholder="{{ $searchPlaceholder }}" data-table-search />
                        </label>
                    @endif

                    @foreach($filterColumns as $filterColumn)
                        <label class="form-control min-w-44">
                            <span class="label-text mb-1 text-sm">{{ $filterColumn['label'] }}</span>
                            @if(count($filterColumn['options']) > 0)
                                <select class="select select-bordered" data-column-filter="{{ $filterColumn['filterKey'] }}">
                                    <option value="">{{ __('daisy::common.all') }}</option>
                                    @foreach($filterColumn['options'] as $option)
                                        <option value="{{ $option['value'] }}" @selected((string) $filterColumn['value'] === (string) $option['value'])>{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="search"
                                    value="{{ $filterColumn['value'] }}"
                                    class="input input-bordered"
                                    placeholder="{{ $filterColumn['label'] }}"
                                    data-column-filter="{{ $filterColumn['filterKey'] }}"
                                />
                            @endif
                        </label>
                    @endforeach
                </div>
            @endif

            @if($toolbarSlot !== '')
                <div class="flex flex-wrap items-center gap-3">
                    {{ $toolbar }}
                </div>
            @endif
        </div>
    @endif

    <x-daisy::ui.data-display.table
        :size="$size"
        :zebra="$zebra"
        :pinRows="$pinRows"
        :pinCols="$pinCols"
        :caption="$caption"
        containerClass="rounded-box border border-base-content/5 bg-base-100"
        class="w-full"
    >
        <x-slot:head>
            <tr>
                @if($isSelectable)
                    <th class="w-0">
                        @if($isMultiple)
                            <label class="flex w-full cursor-pointer items-center justify-center py-1" aria-label="{{ __('daisy::components.select_all_rows') }}">
                                <input
                                    type="checkbox"
                                    class="checkbox checkbox-sm"
                                    data-select-all
                                    aria-label="{{ __('daisy::components.select_all_rows') }}"
                                    aria-checked="{{ $hasMixedVisibleSelection ? 'mixed' : ($allVisibleSelected ? 'true' : 'false') }}"
                                    data-indeterminate="{{ $hasMixedVisibleSelection ? 'true' : 'false' }}"
                                    @checked($allVisibleSelected)
                                />
                            </label>
                        @endif
                    </th>
                @endif

                @foreach($columns as $column)
                    @php
                        $columnKey = $column['key'] ?? null;
                        $sortKey = $column['sortKey'] ?? $columnKey;
                        $isSorted = $sortBy === $sortKey || $sortBy === $columnKey;
                        $alignmentClass = $resolveAlignment($column['align'] ?? null);
                        $headerClasses = trim($alignmentClass.' '.($column['headerClass'] ?? ''));
                        $width = $column['width'] ?? null;
                        $sortDirectionForLink = $sortDirection === 'asc' && $isSorted ? 'desc' : 'asc';
                        $sortUrl = $sortKey ? ($sortUrls[$sortKey][$sortDirectionForLink] ?? null) : null;

                        if (! $sortUrl && $isServerMode && ($column['sortable'] ?? false) && $sortKey) {
                            $sortToken = $sortDirectionForLink === 'desc' ? '-'.$sortKey : $sortKey;
                            if ($queryBuilder) {
                                $sortUrl = $buildQueryUrl([$sortParameter => $sortToken], [$pageParameter]);
                            } else {
                                $sortUrl = $buildQueryUrl([$sortParameter => $sortToken], [$pageParameter]);
                            }
                        }
                    @endphp

                    <th @if($width) style="width: {{ $width }}" @endif class="{{ $headerClasses }}">
                        @if(($column['sortable'] ?? false) && $isServerMode && $sortUrl)
                            <a href="{{ $sortUrl }}" class="inline-flex items-center gap-2 link link-hover no-underline font-semibold">
                                <span>{{ $column['label'] ?? $columnKey }}</span>
                                @if($isSorted)
                                    <span aria-hidden="true">{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                @else
                                    <span aria-hidden="true" class="opacity-40">↕</span>
                                @endif
                            </a>
                        @elseif(($column['sortable'] ?? false) && $isClientMode && $columnKey)
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 font-semibold cursor-pointer"
                                data-client-sort-key="{{ $sortKey }}"
                            >
                                <span>{{ $column['label'] ?? $columnKey }}</span>
                                @if($isSorted)
                                    <span aria-hidden="true">{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                @else
                                    <span aria-hidden="true" class="opacity-40">↕</span>
                                @endif
                            </button>
                        @else
                            <span class="inline-flex items-center gap-2 font-semibold">
                                <span>{{ $column['label'] ?? $columnKey }}</span>
                                @if($isSorted)
                                    <span aria-hidden="true">{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                @endif
                            </span>
                        @endif
                    </th>
                @endforeach
            </tr>
        </x-slot:head>

        <x-slot:body>
            @if($loading)
                <tr>
                    <td colspan="{{ $columnCount }}" class="py-10">
                        <x-daisy::ui.feedback.loading-message />
                    </td>
                </tr>
            @elseif(count($rows) === 0)
                <tr>
                    <td colspan="{{ $columnCount }}" class="py-4">
                        @if($emptySlot !== '')
                            {{ $empty }}
                        @else
                            <x-daisy::ui.feedback.empty-state :title="$emptyTitle" :message="$emptyDescription" size="sm" class="py-8" />
                        @endif
                    </td>
                </tr>
            @else
                @foreach($rows as $row)
                    @php
                        $rowId = $resolveRowId($row);
                        $isSelected = $rowId !== '' && in_array($rowId, $normalizedSelected, true);
                    @endphp
                    <tr
                        @class([$isSelected ? 'bg-base-200' : null])
                        aria-selected="{{ $isSelected ? 'true' : 'false' }}"
                        data-table-row
                        data-row-id="{{ $rowId }}"
                    >
                        @if($isSelectable)
                            <td class="w-0">
                                @if($selectable === 'single')
                                    <label class="flex w-full cursor-pointer items-center justify-center py-1">
                                        <input
                                            type="radio"
                                            name="{{ $selectionGroup }}"
                                            value="{{ $rowId }}"
                                            class="radio radio-sm"
                                            data-row-select
                                            data-row-id="{{ $rowId }}"
                                            @checked($isSelected)
                                            aria-label="{{ __('daisy::components.select_row') }} {{ $rowId }}"
                                        />
                                    </label>
                                @else
                                    <label class="flex w-full cursor-pointer items-center justify-center py-1">
                                        <input
                                            type="checkbox"
                                            value="{{ $rowId }}"
                                            class="checkbox checkbox-sm"
                                            data-row-select
                                            data-row-id="{{ $rowId }}"
                                            @checked($isSelected)
                                            aria-label="{{ __('daisy::components.select_row') }} {{ $rowId }}"
                                        />
                                    </label>
                                @endif
                            </td>
                        @endif

                        @foreach($columns as $column)
                            @php
                                $columnKey = $column['key'] ?? null;
                                $filterKey = $column['filterKey'] ?? $columnKey;
                                $sortKey = $column['sortKey'] ?? $columnKey;
                                $value = $resolveValue($row, $column);
                                $textValue = trim(strip_tags((string) $value));
                                $alignmentClass = $resolveAlignment($column['align'] ?? null);
                                $cellClasses = trim($alignmentClass.' '.($column['cellClass'] ?? ''));
                                $isRowHeader = (bool) ($column['rowHeader'] ?? false);
                                $isHtml = (bool) ($column['html'] ?? false);
                            @endphp

                            @if($isRowHeader)
                                <th
                                    scope="row"
                                    class="{{ $cellClasses }}"
                                    data-column-key="{{ $columnKey }}"
                                    data-filter-key="{{ $filterKey }}"
                                    data-sort-key="{{ $sortKey }}"
                                    data-value="{{ e($textValue) }}"
                                >
                                    @if($isHtml)
                                        {!! $value !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </th>
                            @else
                                <td
                                    class="{{ $cellClasses }}"
                                    data-column-key="{{ $columnKey }}"
                                    data-filter-key="{{ $filterKey }}"
                                    data-sort-key="{{ $sortKey }}"
                                    data-value="{{ e($textValue) }}"
                                >
                                    @if($isHtml)
                                        {!! $value !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </x-slot:body>
    </x-daisy::ui.data-display.table>

    @if($isServerMode || $isClientMode)
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-3 text-sm text-base-content/70">
                @if($isServerMode && $pagination && $pagination['from'] && $pagination['to'] && $pagination['total'])
                    <span>{{ __('daisy::components.showing_results', ['from' => $pagination['from'], 'to' => $pagination['to'], 'total' => $pagination['total']]) }}</span>
                @elseif($isClientMode)
                    <span data-table-page-info></span>
                @endif

                @if($isSelectable)
                    <span data-selected-summary @if(count($normalizedSelected) === 0) class="hidden" @endif>
                        {{ count($normalizedSelected) }} {{ __('daisy::components.selected_rows') }}
                    </span>
                @endif

                @if(count($perPageOptions) > 0)
                    @if($isServerMode)
                        <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2">
                            @foreach($baseQuery as $queryKey => $queryValue)
                                @if(! in_array($queryKey, [$perPageParameter, $pageParameter], true))
                                    @foreach($flattenQueryInputs([$queryKey => $queryValue]) as $queryInput)
                                        <input type="hidden" name="{{ $queryInput['name'] }}" value="{{ $queryInput['value'] }}" />
                                    @endforeach
                                @endif
                            @endforeach
                            <label class="label cursor-pointer gap-2">
                                <span class="label-text">{{ __('daisy::components.rows_per_page') }}</span>
                                <select name="{{ $perPageParameter }}" class="select select-bordered select-sm" onchange="this.form.submit()">
                                    @foreach($perPageOptions as $option)
                                        <option value="{{ $option }}" @selected((int) $effectivePerPage === (int) $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </form>
                    @else
                        <label class="label cursor-pointer gap-2">
                            <span class="label-text">{{ __('daisy::components.rows_per_page') }}</span>
                            <select class="select select-bordered select-sm" data-table-page-size-select>
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" @selected((int) $effectivePerPage === (int) $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endif
                @endif
            </div>

            @if($isServerMode && $pagination && $pagination['last'] > 1)
                <nav class="join" aria-label="{{ __('daisy::components.pagination') }}">
                    <a href="{{ $pagination['prev'] ?: '#' }}" @class(['btn join-item btn-sm', 'btn-disabled pointer-events-none' => ! $pagination['prev']]) aria-label="Previous">«</a>
                    @foreach($pageLinks as $page)
                        @if(is_null($page))
                            <span class="btn join-item btn-sm btn-disabled hidden sm:inline-flex">…</span>
                        @else
                            <a href="{{ $pagination['pages'][$page] ?? $buildQueryUrl([$pageParameter => $page], []) }}" @class(['btn join-item btn-sm hidden sm:inline-flex', 'btn-active' => $page === $pagination['current']])>{{ $page }}</a>
                        @endif
                    @endforeach
                    <a href="{{ $pagination['next'] ?: '#' }}" @class(['btn join-item btn-sm', 'btn-disabled pointer-events-none' => ! $pagination['next']]) aria-label="Next">»</a>
                </nav>
            @elseif($isClientMode)
                <div class="flex items-center gap-3">
                    <nav class="join" aria-label="{{ __('daisy::components.pagination') }}">
                        <button type="button" class="btn join-item btn-sm" data-table-page-prev aria-label="Previous">«</button>
                        <span class="btn join-item btn-sm" data-table-page-indicator>1</span>
                        <button type="button" class="btn join-item btn-sm" data-table-page-next aria-label="Next">»</button>
                    </nav>
                </div>
            @endif
        </div>
    @endif

    @if($afterTableSlot !== '')
        <div>
            {{ $afterTable }}
        </div>
    @endif
</div>

@include('daisy::components.partials.assets')
