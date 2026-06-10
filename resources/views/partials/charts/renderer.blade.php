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
    $containerClasses = trim('daisy-chart bg-base-100 card-border rounded-box '.($preset === 'sparkline' ? 'p-2' : 'p-3').' '.$chartWidthClass.' '.($attributes->get('class') ?? ''));
    $frameClasses = trim('daisy-chart-frame relative '.$chartHeightClass);
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

    <script type="application/json" data-chart-config>@json($config)</script>
    @include('daisy::components.partials.assets')
</div>
