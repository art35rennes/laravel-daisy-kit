@props([
    'columns' => [],
    'data' => [],
    'serverSide' => false,
    'ajax' => null,
    'options' => [],
    'responsive' => false,
    'size' => null,
    'zebra' => false,
    'pinRows' => false,
    'pinCols' => false,
    'caption' => null,
    'containerClass' => 'rounded-box border border-base-content/5 bg-base-100 p-4',
    'tableClass' => 'w-full',
])

@php
    if ($serverSide && blank($ajax)) {
        throw new InvalidArgumentException('The datatable component requires an ajax option when serverSide is enabled.');
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
    $wrapperClasses = trim($containerClass);
    $hasStructuredSlots = isset($head) || isset($body) || isset($foot);

    $normalizeColumn = static function (array $column): array {
        $key = $column['data'] ?? $column['key'] ?? null;
        $title = $column['title'] ?? $column['label'] ?? $key;

        return array_filter([
            'data' => $key,
            'name' => $column['name'] ?? $key,
            'title' => $title,
            'orderable' => $column['orderable'] ?? $column['sortable'] ?? null,
            'searchable' => $column['searchable'] ?? null,
            'className' => $column['className'] ?? $column['cellClass'] ?? null,
            'width' => $column['width'] ?? null,
            'visible' => $column['visible'] ?? null,
            'responsivePriority' => $column['responsivePriority'] ?? null,
        ], static fn ($value) => $value !== null);
    };

    $normalizedColumns = array_values(array_filter(
        array_map($normalizeColumn, is_array($columns) ? $columns : []),
        static fn (array $column) => filled($column['data'] ?? null) || filled($column['title'] ?? null)
    ));

    $allowedOptionKeys = [
        'ajax',
        'columns',
        'layout',
        'paging',
        'pageLength',
        'lengthChange',
        'lengthMenu',
        'searching',
        'ordering',
        'order',
        'language',
        'processing',
        'scrollX',
        'responsive',
    ];

    $resolvedOptions = array_intersect_key(is_array($options) ? $options : [], array_flip($allowedOptionKeys));

    $resolvedOptions['serverSide'] = (bool) $serverSide;
    $resolvedOptions['responsive'] = (bool) $responsive;
    $resolvedOptions['columns'] = $resolvedOptions['columns'] ?? $normalizedColumns;
    $resolvedOptions['language'] = array_replace_recursive([
        'emptyTable' => __('daisy::common.empty'),
        'info' => __('daisy::components.datatable_info'),
        'infoEmpty' => __('daisy::components.datatable_info_empty'),
        'infoFiltered' => __('daisy::components.datatable_info_filtered'),
        'lengthMenu' => __('daisy::components.rows_per_page').' _MENU_',
        'loadingRecords' => __('daisy::common.loading'),
        'processing' => __('daisy::common.loading'),
        'search' => __('daisy::common.search'),
        'zeroRecords' => __('daisy::common.no_results'),
        'paginate' => [
            'first' => __('daisy::components.datatable_paginate_first'),
            'last' => __('daisy::components.datatable_paginate_last'),
            'next' => __('daisy::components.datatable_paginate_next'),
            'previous' => __('daisy::components.datatable_paginate_previous'),
        ],
        'aria' => [
            'orderable' => __('daisy::components.datatable_aria_orderable'),
            'orderableReverse' => __('daisy::components.datatable_aria_orderable_reverse'),
        ],
    ], $resolvedOptions['language'] ?? []);

    if ($serverSide) {
        $resolvedOptions['ajax'] = $ajax;
    } else {
        unset($resolvedOptions['ajax']);
    }

    $renderCell = static function ($row, array $column) {
        $value = data_get($row, $column['data'] ?? $column['key'] ?? '');
        $isHtml = (bool) ($column['html'] ?? false);

        if ($isHtml) {
            return new Illuminate\Support\HtmlString((string) $value);
        }

        return $value;
    };
@endphp

<div
    data-daisy-datatable="1"
    data-options='@json($resolvedOptions)'
    class="{{ $wrapperClasses }}"
>
    <table {{ $attributes->merge(['class' => $tableClasses]) }}>
        @if($caption)
            <caption class="text-start text-xs opacity-70">{{ $caption }}</caption>
        @endif

        @if($hasStructuredSlots)
            @isset($head)
                <thead>
                    {{ $head }}
                </thead>
            @elseif(count($normalizedColumns) > 0)
                <thead>
                    <tr>
                        @foreach($normalizedColumns as $column)
                            <th @if(isset($column['width'])) style="width: {{ $column['width'] }}" @endif>{{ $column['title'] ?? $column['data'] }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            @isset($body)
                <tbody>
                    {{ $body }}
                </tbody>
            @elseif(! $serverSide && count($normalizedColumns) > 0)
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            @foreach($columns as $column)
                                @php
                                    $value = $renderCell($row, $column);
                                @endphp
                                <td class="{{ $column['cellClass'] ?? '' }}">
                                    @if($value instanceof Illuminate\Support\HtmlString)
                                        {!! $value !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            @else
                <tbody></tbody>
            @endif

            @isset($foot)
                <tfoot>
                    {{ $foot }}
                </tfoot>
            @endisset
        @elseif(count($normalizedColumns) > 0)
            <thead>
                <tr>
                    @foreach($normalizedColumns as $column)
                        <th @if(isset($column['width'])) style="width: {{ $column['width'] }}" @endif>{{ $column['title'] ?? $column['data'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(! $serverSide)
                    @foreach($data as $row)
                        <tr>
                            @foreach($columns as $column)
                                @php
                                    $value = $renderCell($row, $column);
                                @endphp
                                <td class="{{ $column['cellClass'] ?? '' }}">
                                    @if($value instanceof Illuminate\Support\HtmlString)
                                        {!! $value !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        @else
            {{ $slot }}
        @endif
    </table>
</div>
