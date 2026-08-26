<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
    'drilldownUrl' => null,
    'drilldownParams' => [],
    'aria' => true,
    'markers' => [],
    'zoom' => false,
    'zoomMode' => 'inside',
    'renderer' => 'svg',
    'action' => null,
    'showValues' => false,
    'centerValue' => null,
    'centerLabel' => null,
    'dataTable' => true,
    'dataTableLabel' => 'Voir les données',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
    'drilldownUrl' => null,
    'drilldownParams' => [],
    'aria' => true,
    'markers' => [],
    'zoom' => false,
    'zoomMode' => 'inside',
    'renderer' => 'svg',
    'action' => null,
    'showValues' => false,
    'centerValue' => null,
    'centerLabel' => null,
    'dataTable' => true,
    'dataTableLabel' => 'Voir les données',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php echo $__env->make('daisy::partials.charts.renderer', ['preset' => 'donut'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/charts/donut.blade.php ENDPATH**/ ?>