<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'editable' => true,
    'items' => [],
    'columns' => 12,
    'cellHeight' => 96,
    'gap' => 16,
    'static' => false,
    'float' => false,
    'minRow' => 0,
    'acceptWidgets' => false,
    'layout' => 'list',
    'responsive' => null,
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
    'editable' => true,
    'items' => [],
    'columns' => 12,
    'cellHeight' => 96,
    'gap' => 16,
    'static' => false,
    'float' => false,
    'minRow' => 0,
    'acceptWidgets' => false,
    'layout' => 'list',
    'responsive' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rootId = $attributes->get('id') ?: ('editable-grid-'.uniqid());
    $resolvedEditable = (bool) $editable;
    $resolvedStatic = (bool) $static || ! $resolvedEditable;
    $resolvedColumns = max(1, (int) $columns);
    $resolvedCellHeight = max(40, (int) $cellHeight);
    $resolvedGap = max(0, (int) $gap);
    $resolvedFloat = (bool) $float;
    $resolvedMinRow = max(0, (int) $minRow);
    $resolvedLayout = in_array($layout, ['list', 'compact', 'moveScale', 'move', 'scale', 'none'], true) ? $layout : 'list';
    $resolvedAcceptWidgets = is_bool($acceptWidgets) ? $acceptWidgets : (filled($acceptWidgets) ? (string) $acceptWidgets : false);
    $resolvedResponsive = null;

    if (is_bool($responsive)) {
        $resolvedResponsive = $responsive ? [
            'columnWidth' => 320,
            'columnMax' => $resolvedColumns,
            'layout' => $resolvedLayout,
        ] : null;
    } elseif (is_array($responsive) && $responsive !== []) {
        $resolvedResponsive = $responsive;
        if (! array_key_exists('layout', $resolvedResponsive)) {
            $resolvedResponsive['layout'] = $resolvedLayout;
        }
    }

    $renderedItems = is_array($items) ? array_values($items) : [];
    $hasSlotContent = isset($slot)
        && (method_exists($slot, 'isEmpty') ? ! $slot->isEmpty() : trim((string) $slot) !== '');

    $config = [
        'editable' => $resolvedEditable,
        'columns' => $resolvedColumns,
        'cellHeight' => $resolvedCellHeight,
        'gap' => $resolvedGap,
        'static' => $resolvedStatic,
        'float' => $resolvedFloat,
        'minRow' => $resolvedMinRow,
        'acceptWidgets' => $resolvedAcceptWidgets,
        'layout' => $resolvedLayout,
        'responsive' => $resolvedResponsive,
    ];

    $surfaceClasses = trim('grid-stack daisy-editable-grid '.($attributes->get('class') ?? ''));
    $attributes = $attributes->except('class');
?>

<div
    class="daisy-editable-grid-host"
    data-module="editable-grid"
    data-editable-grid="1"
    data-editable="<?php echo e($resolvedEditable ? '1' : '0'); ?>"
    data-static="<?php echo e($resolvedStatic ? '1' : '0'); ?>"
>
    <div <?php echo e($attributes->merge(['id' => $rootId, 'class' => $surfaceClasses])); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSlotContent): ?>
            <?php echo e($slot); ?>

        <?php else: ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $renderedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => $item['id'] ?? null,'type' => $item['type'] ?? null,'x' => $item['x'] ?? 0,'y' => $item['y'] ?? 0,'w' => $item['w'] ?? 3,'h' => $item['h'] ?? 2,'meta' => $item['meta'] ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['id'] ?? null),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['type'] ?? null),'x' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['x'] ?? 0),'y' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['y'] ?? 0),'w' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['w'] ?? 3),'h' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['h'] ?? 2),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['meta'] ?? null)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php ($content = $item['content'] ?? null); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($content instanceof \Illuminate\Contracts\Support\Htmlable): ?>
                        <?php echo $content->toHtml(); ?>

                    <?php elseif($content instanceof \Illuminate\Support\HtmlString): ?>
                        <?php echo $content; ?>

                    <?php elseif(filled($content)): ?>
                        <?php echo e($content); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <script type="application/json" data-editable-grid-config>
        <?php echo json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>

    </script>

    <?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/editable-grid.blade.php ENDPATH**/ ?>