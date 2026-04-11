@props([
    'size' => null, // xs | sm | md | lg | xl
    'zebra' => false,
    'pinRows' => false,
    'pinCols' => false,
    'caption' => null,
    'responsive' => true,
    'containerClass' => '',
    'paginationMode' => null, // null | server | client
    'paginator' => null,
    'pageParameter' => 'page',
    'perPage' => null,
    'perPageOptions' => [],
    'perPageParameter' => 'per_page',
    'query' => null,
])

@php
    $sizeMap = ['xs', 'sm', 'md', 'lg', 'xl'];
    $baseQuery = is_array($query) ? $query : request()->query();

    $classes = 'table';

    if ($zebra) {
        $classes .= ' table-zebra';
    }

    if (in_array($size, $sizeMap, true)) {
        $classes .= ' table-'.$size;
    }

    if ($pinRows) {
        $classes .= ' table-pin-rows';
    }

    if ($pinCols) {
        $classes .= ' table-pin-cols';
    }

    $wrapperClasses = trim(($responsive ? 'overflow-x-auto ' : '').$containerClass);
    $hasStructuredSlots = isset($head) || isset($body) || isset($foot);
    $paginationMode = in_array($paginationMode, ['server', 'client'], true) ? $paginationMode : null;
    $perPageOptions = array_values(array_filter(array_map('intval', is_array($perPageOptions) ? $perPageOptions : []), fn ($value) => $value > 0));
    $buildQueryUrl = static function (array $overrides = [], array $remove = []) use ($baseQuery, $pageParameter) {
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
    $pagination = null;
    if ($paginationMode === 'server' && is_object($paginator) && method_exists($paginator, 'currentPage') && method_exists($paginator, 'lastPage')) {
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

    $effectivePerPage = $perPage ?: ($pagination['perPage'] ?? null);
    if ($effectivePerPage && ! in_array((int) $effectivePerPage, $perPageOptions, true)) {
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
@endphp

<div data-simple-table-root>
    <div
        @class([$wrapperClasses])
        @if($paginationMode === 'client')
            data-simple-table="1"
            data-table-pagination-mode="client"
            data-table-page-size="{{ $effectivePerPage ?: 10 }}"
            data-table-page-parameter="{{ $pageParameter }}"
            data-table-per-page-parameter="{{ $perPageParameter }}"
        @endif
    >
        <table {{ $attributes->merge(['class' => $classes]) }}>
            @if($caption)
                <caption class="text-start text-xs opacity-70">{{ $caption }}</caption>
            @endif

            @if($hasStructuredSlots)
                @isset($head)
                    <thead>
                        {{ $head }}
                    </thead>
                @endisset

                @isset($body)
                    <tbody>
                        {{ $body }}
                    </tbody>
                @endisset

                @isset($foot)
                    <tfoot>
                        {{ $foot }}
                    </tfoot>
                @endisset
            @else
                {{ $slot }}
            @endif
        </table>
    </div>

    @if($paginationMode === 'server' || $paginationMode === 'client')
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        @if(count($perPageOptions) > 0)
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
                    <select
                        name="{{ $perPageParameter }}"
                        class="select select-bordered select-sm"
                        @if($paginationMode === 'client') data-table-page-size-select @else onchange="this.form.submit()" @endif
                    >
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}" @selected((int) $effectivePerPage === (int) $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </label>
            </form>
        @endif

        @if($paginationMode === 'server' && $pagination)
            <nav class="join" aria-label="{{ __('daisy::components.pagination') }}">
                <a href="{{ $pagination['prev'] ?: '#' }}" @class(['btn join-item btn-sm', 'btn-disabled pointer-events-none' => ! $pagination['prev']]) aria-label="Previous">«</a>
                @foreach($pageLinks as $page)
                    @if(is_null($page))
                        <span class="btn join-item btn-sm btn-disabled hidden sm:inline-flex">…</span>
                    @else
                        <a href="{{ $pagination['pages'][$page] }}" @class(['btn join-item btn-sm hidden sm:inline-flex', 'btn-active' => $page === $pagination['current']])>{{ $page }}</a>
                    @endif
                @endforeach
                <a href="{{ $pagination['next'] ?: '#' }}" @class(['btn join-item btn-sm', 'btn-disabled pointer-events-none' => ! $pagination['next']]) aria-label="Next">»</a>
            </nav>
        @elseif($paginationMode === 'client')
            <div class="flex items-center gap-3">
                <span class="text-sm text-base-content/70" data-table-page-info></span>
                <nav class="join" aria-label="{{ __('daisy::components.pagination') }}">
                    <button type="button" class="btn join-item btn-sm" data-table-page-prev aria-label="Previous">«</button>
                    <span class="btn join-item btn-sm" data-table-page-indicator>1</span>
                    <button type="button" class="btn join-item btn-sm" data-table-page-next aria-label="Next">»</button>
                </nav>
            </div>
        @endif
        </div>
    @endif
</div>
