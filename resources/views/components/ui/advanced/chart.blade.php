@props([
    'type' => 'bar',                 // bar|line|area|doughnut|pie|radar|polarArea|scatter|bubble
    'labels' => [],                  // array
    'datasets' => [],                // array of { label, data, color?, ...Chart.js dataset opts }
    'options' => [],                 // Chart.js options (array/assoc)
    'height' => '320px',
    'width' => '100%',
    'responsive' => true,
    'maintainAspectRatio' => false,
    'colors' => null,                // array of color tokens or CSS colors (takes precedence over dataset.color)
    'palette' => ['primary','secondary','accent','info','success','warning','error'], // fallback palette
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $id = $attributes->get('id') ?? 'chart-'.uniqid();
    $containerClasses = trim('daisy-chart bg-base-100 border border-base-300 rounded-box p-3 '.($attributes->get('class') ?? ''));
    // retire la classe du merge automatique pour l'ajouter proprement plus bas
    $attributes = $attributes->except('class');

    $config = [
        'type' => $type,
        'data' => [
            'labels' => $labels,
            'datasets' => $datasets,
        ],
        'options' => array_merge([
            'responsive' => (bool) $responsive,
            'maintainAspectRatio' => (bool) $maintainAspectRatio,
        ], is_array($options) ? $options : []),
        // Options Daisy supplémentaires
        'daisy' => [
            'colors' => $colors,
            'palette' => $palette,
        ],
    ];
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }} data-module="{{ $module ?? 'chart' }}" data-chart="1" style="width: {{ $width }};">
    <div class="relative" style="height: {{ $height }};">
        <canvas id="{{ $id }}"></canvas>
    </div>
    <script type="application/json" data-config>@json($config)</script>
    @include('daisy::components.partials.assets')
    {{-- API JS: window.DaisyChart.init(el) / initAll() / create(canvas, config) --}}
</div>


