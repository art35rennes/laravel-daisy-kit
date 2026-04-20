@php
    $hostId = 'chart-'.uniqid();
    $headerClasses = trim('mb-3 flex items-start justify-between gap-3');
    $containerClasses = trim('daisy-chart bg-base-100 card-border rounded-box '.($preset === 'sparkline' ? 'p-2' : 'p-3').' '.($attributes->get('class') ?? ''));
    $attributes = $attributes->except('class');

    $hasData = false;
    if (is_array($series)) {
        foreach ($series as $seriesItem) {
            $data = is_array($seriesItem) ? ($seriesItem['data'] ?? null) : null;
            if (is_array($data) && $data !== []) {
                $hasData = true;
                break;
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
        'state' => [
            'hasData' => $hasData,
        ],
    ];
@endphp

<div
    {{ $attributes->merge(['class' => $containerClasses]) }}
    data-daisy-chart="1"
    @if($module) data-module="{{ $module }}" @endif
    style="width: {{ $width }};"
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

    <div class="relative" style="height: {{ $height }};">
        <div id="{{ $hostId }}" data-chart-canvas class="h-full w-full" @if($title) aria-label="{{ $title }}" @endif></div>

        <div
            data-chart-empty
            class="@if($hasData || $loading) hidden @endif absolute inset-0 grid place-items-center rounded-box bg-base-100/80 text-center text-sm text-base-content/70"
        >
            <div class="max-w-xs">{{ $emptyMessage }}</div>
        </div>
    </div>

    <script type="application/json" data-chart-config>@json($config)</script>
    @include('daisy::components.partials.assets')
</div>
