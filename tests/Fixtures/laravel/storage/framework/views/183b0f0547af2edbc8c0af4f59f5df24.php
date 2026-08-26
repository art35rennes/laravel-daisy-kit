<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['panel', 'section', 'toneClasses', 'detailedUrl' => '#', 'detailModalId' => null]));

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

foreach (array_filter((['panel', 'section', 'toneClasses', 'detailedUrl' => '#', 'detailModalId' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $chartKey = $panel['chart'] ?? $panel['type'];
    $chartParams = ['section' => $section['id'], 'chart' => $chartKey];
    $numericValue = function ($value) {
        preg_match('/-?\d+(?:[.,]\d+)?/', (string) $value, $matches);

        return isset($matches[0]) ? (float) str_replace(',', '.', $matches[0]) : 0.0;
    };
    $slug = fn ($value) => \Illuminate\Support\Str::slug((string) $value);
    $segmentColors = [
        'text-primary' => 'primary',
        'text-info' => 'info',
        'text-success' => 'success',
        'text-warning' => 'warning',
        'text-error' => 'error',
        'text-lime-500' => 'accent',
        'text-base-content/30' => 'neutral',
    ];
?>

<article class="min-w-0 rounded-box border border-base-300 bg-base-100 p-4">
    <div class="mb-4">
        <h3 class="text-sm font-bold"><?php echo e($panel['title']); ?></h3>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($panel['caption'])): ?>
            <p class="mt-1 text-xs text-base-content/60"><?php echo e($panel['caption']); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($panel['type'] === 'donut'): ?>
        <?php
            $segments = $panel['segments'] ?? [];
            $categories = array_map(fn ($segment) => $segment['label'], $segments);
            $data = array_map(fn ($segment) => [
                'name' => $segment['label'],
                'value' => $numericValue($segment['value']),
                'color' => $segmentColors[$segment['class'] ?? ''] ?? ($segment['color'] ?? $section['tone']),
                'drilldown' => [$panel['filter'] ?? 'segment' => $slug($segment['label'])],
                'meta' => [
                    'section' => $section['id'],
                    'chart' => $chartKey,
                    $panel['filter'] ?? 'segment' => $slug($segment['label']),
                ],
                'action' => array_filter([
                    'type' => 'event',
                    'intent' => 'detail',
                    'target' => $detailModalId ? '#'.$detailModalId : null,
                ]),
                'tooltip' => [
                    'rows' => array_values(array_filter([
                        ! empty($segment['detail']) ? ['label' => 'Part', 'value' => $segment['detail']] : null,
                    ])),
                ],
            ], $segments);
        ?>
        <?php if (isset($component)) { $__componentOriginal1339ea37098c7607c9269b4144640640 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1339ea37098c7607c9269b4144640640 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.charts.donut','data' => ['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','title' => null,'subtitle' => null,'categories' => $categories,'series' => [['name' => $panel['title'], 'data' => $data]],'legend' => false,'drilldownUrl' => $detailedUrl,'drilldownParams' => $chartParams,'markers' => $panel['markers'] ?? [],'centerValue' => $panel['center'] ?? null,'centerLabel' => $panel['centerLabel'] ?? null,'options' => ['series' => [['label' => ['show' => false], 'labelLine' => ['show' => false]]]],'valueFormat' => 'number','emptyMessage' => 'Aucune donnée disponible']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::charts.donut'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'series' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['name' => $panel['title'], 'data' => $data]]),'legend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'drilldown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailedUrl),'drilldown-params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($chartParams),'markers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['markers'] ?? []),'center-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['center'] ?? null),'center-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['centerLabel'] ?? null),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['series' => [['label' => ['show' => false], 'labelLine' => ['show' => false]]]]),'value-format' => 'number','empty-message' => 'Aucune donnée disponible']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1339ea37098c7607c9269b4144640640)): ?>
<?php $attributes = $__attributesOriginal1339ea37098c7607c9269b4144640640; ?>
<?php unset($__attributesOriginal1339ea37098c7607c9269b4144640640); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1339ea37098c7607c9269b4144640640)): ?>
<?php $component = $__componentOriginal1339ea37098c7607c9269b4144640640; ?>
<?php unset($__componentOriginal1339ea37098c7607c9269b4144640640); ?>
<?php endif; ?>
        <div class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $segments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button
                    type="button"
                    class="daisy-chart-legend-item flex w-full min-w-0 items-center justify-between gap-3 rounded-field px-1 py-1 text-left hover:bg-base-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    data-chart-legend-index="<?php echo e($loop->index); ?>"
                    aria-label="Mettre en évidence <?php echo e($segment['label']); ?>"
                >
                    <span class="flex min-w-0 items-center gap-2 truncate">
                        <span class="size-2 shrink-0 rounded-full bg-current <?php echo e($segment['class'] ?? 'text-primary'); ?>"></span>
                        <span class="truncate"><?php echo e($segment['label']); ?></span>
                    </span>
                    <span class="shrink-0 font-semibold"><?php echo e($segment['value']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($segment['detail'])): ?> <span class="text-base-content/50">(<?php echo e($segment['detail']); ?>)</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php elseif($panel['type'] === 'bars'): ?>
        <?php
            $items = $panel['items'] ?? [];
            $categories = array_map(fn ($item) => $item['label'], $items);
            $data = array_map(fn ($item) => [
                'name' => $item['label'],
                'value' => $numericValue($item['value']),
                'drilldown' => [$panel['filter'] ?? 'period' => $slug($item['label'])],
            ], $items);
        ?>
        <?php if (isset($component)) { $__componentOriginal235f5c337691b2ac9c41976a67d1e7c6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.charts.bar','data' => ['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','colors' => [$panel['tone'] ?? $section['tone']],'categories' => $categories,'series' => [['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]],'legend' => false,'toolbar' => false,'drilldownUrl' => $detailedUrl,'drilldownParams' => $chartParams,'markers' => $panel['markers'] ?? [],'zoom' => $panel['zoom'] ?? false,'zoomMode' => $panel['zoomMode'] ?? 'inside','showValues' => true,'valueFormat' => 'number','emptyMessage' => 'Aucune donnée disponible']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::charts.bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','colors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$panel['tone'] ?? $section['tone']]),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'series' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]]),'legend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'toolbar' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'drilldown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailedUrl),'drilldown-params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($chartParams),'markers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['markers'] ?? []),'zoom' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['zoom'] ?? false),'zoom-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['zoomMode'] ?? 'inside'),'show-values' => true,'value-format' => 'number','empty-message' => 'Aucune donnée disponible']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6)): ?>
<?php $attributes = $__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6; ?>
<?php unset($__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal235f5c337691b2ac9c41976a67d1e7c6)): ?>
<?php $component = $__componentOriginal235f5c337691b2ac9c41976a67d1e7c6; ?>
<?php unset($__componentOriginal235f5c337691b2ac9c41976a67d1e7c6); ?>
<?php endif; ?>
    <?php elseif($panel['type'] === 'line'): ?>
        <?php
            $labels = $panel['labels'] ?? [];
            $values = $panel['values'] ?? [];
            $data = array_map(fn ($value, $index) => [
                'name' => $labels[$index] ?? "Point {$index}",
                'value' => $numericValue($value),
                'drilldown' => [$panel['filter'] ?? 'date' => $slug($labels[$index] ?? $index)],
            ], $values, array_keys($values));
        ?>
        <?php if (isset($component)) { $__componentOriginalb172e527f322074d642cf57fd4d9feda = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb172e527f322074d642cf57fd4d9feda = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.charts.line','data' => ['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','colors' => [$panel['tone'] ?? $section['tone']],'categories' => $labels,'series' => [['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]],'legend' => false,'drilldownUrl' => $detailedUrl,'drilldownParams' => $chartParams,'markers' => $panel['markers'] ?? [],'zoom' => $panel['zoom'] ?? false,'zoomMode' => $panel['zoomMode'] ?? 'inside','showValues' => true,'valueFormat' => 'number','emptyMessage' => 'Aucune donnée disponible']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::charts.line'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','colors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$panel['tone'] ?? $section['tone']]),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($labels),'series' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]]),'legend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'drilldown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailedUrl),'drilldown-params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($chartParams),'markers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['markers'] ?? []),'zoom' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['zoom'] ?? false),'zoom-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['zoomMode'] ?? 'inside'),'show-values' => true,'value-format' => 'number','empty-message' => 'Aucune donnée disponible']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb172e527f322074d642cf57fd4d9feda)): ?>
<?php $attributes = $__attributesOriginalb172e527f322074d642cf57fd4d9feda; ?>
<?php unset($__attributesOriginalb172e527f322074d642cf57fd4d9feda); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb172e527f322074d642cf57fd4d9feda)): ?>
<?php $component = $__componentOriginalb172e527f322074d642cf57fd4d9feda; ?>
<?php unset($__componentOriginalb172e527f322074d642cf57fd4d9feda); ?>
<?php endif; ?>
    <?php else: ?>
        <?php
            $items = $panel['items'] ?? [];
            $categories = array_map(fn ($item) => $item['label'], $items);
            $data = array_map(fn ($item) => [
                'name' => $item['label'],
                'value' => $numericValue($item['value']),
                'drilldown' => [$panel['filter'] ?? 'item' => $slug($item['label'])],
            ], $items);
        ?>
        <?php if (isset($component)) { $__componentOriginal235f5c337691b2ac9c41976a67d1e7c6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.charts.bar','data' => ['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','colors' => [$panel['tone'] ?? $section['tone']],'orientation' => 'horizontal','categories' => $categories,'series' => [['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]],'legend' => false,'toolbar' => false,'drilldownUrl' => $detailedUrl,'drilldownParams' => $chartParams,'markers' => $panel['markers'] ?? [],'zoom' => $panel['zoom'] ?? false,'zoomMode' => $panel['zoomMode'] ?? 'inside','showValues' => true,'valueFormat' => 'number','emptyMessage' => 'Aucune donnée disponible']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::charts.bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!bg-transparent !p-0 !shadow-none !border-0','height' => '190px','colors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$panel['tone'] ?? $section['tone']]),'orientation' => 'horizontal','categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'series' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]]),'legend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'toolbar' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'drilldown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailedUrl),'drilldown-params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($chartParams),'markers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['markers'] ?? []),'zoom' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['zoom'] ?? false),'zoom-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($panel['zoomMode'] ?? 'inside'),'show-values' => true,'value-format' => 'number','empty-message' => 'Aucune donnée disponible']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6)): ?>
<?php $attributes = $__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6; ?>
<?php unset($__attributesOriginal235f5c337691b2ac9c41976a67d1e7c6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal235f5c337691b2ac9c41976a67d1e7c6)): ?>
<?php $component = $__componentOriginal235f5c337691b2ac9c41976a67d1e7c6; ?>
<?php unset($__componentOriginal235f5c337691b2ac9c41976a67d1e7c6); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</article>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/partials/reporting/panel.blade.php ENDPATH**/ ?>