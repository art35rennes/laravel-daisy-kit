@php
    $lengthClass = function ($value, string $prefix) {
        if (! is_string($value) && ! $value instanceof \Stringable && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= 1200 ? "{$prefix}-px-{$token}" : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)%$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= 100 ? "{$prefix}-percent-{$token}" : null;
        }

        return null;
    };

    $hostId = 'chart-'.uniqid();
    $headerClasses = trim('mb-3 flex items-start justify-between gap-3');
    $chartWidthClass = $width === '100%' ? null : $lengthClass($width, 'daisy-chart-width');
    $chartHeightClass = $height === '320px' ? null : $lengthClass($height, 'daisy-chart-height');
    $isDrilldownEnabled = is_string($drilldownUrl ?? null) && trim($drilldownUrl) !== '' && trim($drilldownUrl) !== '#';
    $containerClasses = trim('daisy-chart min-w-0 max-w-full bg-base-100 card-border rounded-box '.($preset === 'sparkline' ? 'p-2' : 'p-3').' '.($isDrilldownEnabled ? 'daisy-chart-clickable' : '').' '.$chartWidthClass.' '.($attributes->get('class') ?? ''));
    $frameClasses = trim('daisy-chart-frame relative min-w-0 max-w-full '.$chartHeightClass);
    $attributes = $attributes->except('class');
    $accessibleRows = [];

    $hasData = false;
    if (is_array($series)) {
        foreach ($series as $seriesIndex => $seriesItem) {
            $data = is_array($seriesItem) ? ($seriesItem['data'] ?? null) : null;
            if (is_array($data) && $data !== []) {
                $hasData = true;
            }

            foreach(is_array($data) ? $data : [] as $dataIndex => $point) {
                $pointValue = is_array($point) ? ($point['value'] ?? '') : $point;
                $pointName = is_array($point)
                    ? ($point['name'] ?? ($categories[$dataIndex] ?? __('Item :number', ['number' => $dataIndex + 1])))
                    : ($categories[$dataIndex] ?? __('Item :number', ['number' => $dataIndex + 1]));
                $pointHasAction = is_array($point)
                    && (is_array($point['action'] ?? null) || is_array($point['drilldown'] ?? null));

                $accessibleRows[] = [
                    'series' => $seriesItem['name'] ?? __('Series :number', ['number' => $seriesIndex + 1]),
                    'name' => $pointName,
                    'value' => $pointValue,
                    'seriesIndex' => $seriesIndex,
                    'dataIndex' => $dataIndex,
                    'actionable' => $pointHasAction || is_array($action ?? null) || $isDrilldownEnabled,
                ];
            }
        }
    }

    $config = [
        'preset' => $preset,
        'series' => $series,
        'categories' => $categories,
        'title' => $title,
        'subtitle' => $subtitle,
        'legend' => (bool) $legend,
        'toolbar' => (bool) $toolbar,
        'loading' => (bool) $loading,
        'emptyMessage' => $emptyMessage,
        'colors' => $colors,
        'palette' => $palette,
        'valueFormat' => $valueFormat,
        'tooltipFormat' => $tooltipFormat,
        'options' => is_array($options) ? $options : [],
        'renderer' => $renderer ?? 'svg',
        'action' => is_array($action ?? null) ? $action : null,
        'showValues' => (bool) ($showValues ?? false),
        'centerValue' => $centerValue ?? null,
        'centerLabel' => $centerLabel ?? null,
        'drilldown' => [
            'url' => $isDrilldownEnabled ? $drilldownUrl : null,
            'params' => is_array($drilldownParams ?? null) ? $drilldownParams : [],
        ],
        'aria' => (bool) ($aria ?? true),
        'markers' => is_array($markers ?? null) ? $markers : [],
        'zoom' => (bool) ($zoom ?? false),
        'zoomMode' => $zoomMode ?? 'inside',
        'orientation' => $orientation ?? 'vertical',
        'state' => [
            'hasData' => $hasData,
        ],
    ];
@endphp

<div
    {{ $attributes->merge(['id' => "{$hostId}-container", 'class' => $containerClasses]) }}
    data-daisy-chart="1"
    data-chart-preset="{{ $preset }}"
    @if(in_array($preset, ['pie', 'donut'], true)) data-chart-circular="1" @endif
    @if($module) data-module="{{ $module }}" @endif
>
    @if($title || $subtitle)
        <div class="{{ $headerClasses }}">
            <div class="min-w-0">
                @if($title)
                    <h3 class="truncate text-sm font-semibold text-base-content">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="mt-1 text-xs text-base-content/70">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="{{ $frameClasses }}">
        <div id="{{ $hostId }}" data-chart-canvas class="h-full w-full" @if($title) aria-label="{{ $title }}" @endif></div>

        <div
            data-chart-empty
            class="@if($hasData || $loading) hidden @endif absolute inset-0 grid place-items-center rounded-box bg-base-100/80 text-center text-sm text-base-content/70"
        >
            <div class="max-w-xs">{{ $emptyMessage }}</div>
        </div>
    </div>

    @if(($dataTable ?? true) && $accessibleRows !== [])
        <details class="daisy-chart-data mt-3 text-sm">
            <summary class="cursor-pointer font-medium text-primary">{{ $dataTableLabel ?? __('View chart data') }}</summary>
            <div class="mt-2 overflow-x-auto rounded-box card-border">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>{{ __('Series') }}</th>
                            <th>{{ __('Data') }}</th>
                            <th class="text-right">{{ __('Value') }}</th>
                            <th class="w-px"><span class="sr-only">{{ __('Action') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accessibleRows as $row)
                            <tr>
                                <td>{{ $row['series'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td class="text-right font-medium">{{ $row['value'] }}</td>
                                <td>
                                    @if($row['actionable'])
                                        <button
                                            type="button"
                                            class="btn btn-ghost btn-xs"
                                            data-chart-accessible-action
                                            data-series-index="{{ $row['seriesIndex'] }}"
                                            data-data-index="{{ $row['dataIndex'] }}"
                                        >
                                            {{ __('Open') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    @endif

    <script type="application/json" data-chart-config>@json($config)</script>
    @include('daisy::components.partials.assets')
</div>
