<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['kpi', 'toneClasses']));

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

foreach (array_filter((['kpi', 'toneClasses']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<article class="min-w-0 rounded-box border border-base-300 bg-base-100 p-4">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate text-sm font-bold"><?php echo e($kpi['label']); ?></h3>
            <p class="mt-2 text-4xl font-bold leading-none tracking-tight"><?php echo e($kpi['value']); ?></p>
            <p class="mt-2 text-sm text-base-content/60"><?php echo e($kpi['unit']); ?></p>
        </div>
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full <?php echo e($toneClasses[$kpi['tone']]['soft']); ?> <?php echo e($toneClasses[$kpi['tone']]['text']); ?>">
            <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $kpi['icon'],'class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpi['icon']),'class' => 'h-5 w-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
        </span>
    </div>
    <div class="mt-4 flex min-h-9 items-end justify-between gap-3">
        <p class="min-w-0 text-xs font-semibold leading-snug <?php echo e($toneClasses[$kpi['tone']]['text']); ?>"><?php echo e($kpi['trend']); ?></p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($kpi['sparklineData'])): ?>
            <?php if (isset($component)) { $__componentOriginal7a190927b591fb30db07ac514dbbe6e3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a190927b591fb30db07ac514dbbe6e3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.charts.sparkline','data' => ['class' => '!bg-transparent !p-0 !shadow-none !border-0 shrink-0','height' => '50px','width' => '140px','categories' => $kpi['sparklineLabels'] ?? [],'series' => [['name' => $kpi['label'], 'data' => $kpi['sparklineData']]],'colors' => [$kpi['tone']],'drilldownUrl' => $kpi['drilldownUrl'] ?? null,'drilldownParams' => $kpi['drilldownParams'] ?? [],'valueFormat' => 'number','emptyMessage' => 'Aucune donnée disponible','aria' => true,'dataTable' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::charts.sparkline'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!bg-transparent !p-0 !shadow-none !border-0 shrink-0','height' => '50px','width' => '140px','categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpi['sparklineLabels'] ?? []),'series' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['name' => $kpi['label'], 'data' => $kpi['sparklineData']]]),'colors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$kpi['tone']]),'drilldown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpi['drilldownUrl'] ?? null),'drilldown-params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpi['drilldownParams'] ?? []),'value-format' => 'number','empty-message' => 'Aucune donnée disponible','aria' => true,'data-table' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a190927b591fb30db07ac514dbbe6e3)): ?>
<?php $attributes = $__attributesOriginal7a190927b591fb30db07ac514dbbe6e3; ?>
<?php unset($__attributesOriginal7a190927b591fb30db07ac514dbbe6e3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a190927b591fb30db07ac514dbbe6e3)): ?>
<?php $component = $__componentOriginal7a190927b591fb30db07ac514dbbe6e3; ?>
<?php unset($__componentOriginal7a190927b591fb30db07ac514dbbe6e3); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</article>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/partials/reporting/metric-card.blade.php ENDPATH**/ ?>