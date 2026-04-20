@props([
    'series' => [],
    'categories' => [],
    'title' => null,
    'subtitle' => null,
    'height' => '120px',
    'width' => '100%',
    'legend' => false,
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

@include('daisy::partials.charts.renderer', ['preset' => 'sparkline'])
