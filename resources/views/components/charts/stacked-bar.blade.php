@props([
    'series' => [],
    'categories' => [],
    'title' => null,
    'subtitle' => null,
    'height' => '320px',
    'width' => '100%',
    'legend' => true,
    'toolbar' => false,
    'loading' => false,
    'emptyMessage' => 'No data available',
    'colors' => null,
    'palette' => ['primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'],
    'valueFormat' => 'number',
    'tooltipFormat' => null,
    'options' => [],
    'module' => null,
])

@include('daisy::partials.charts.renderer', ['preset' => 'stacked-bar'])
