<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'message' => __('daisy::common.loading'),
    'shape' => 'spinner',
    'size' => 'lg',
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
    'message' => __('daisy::common.loading'),
    'shape' => 'spinner',
    'size' => 'lg',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col items-center gap-4">
    <?php if (isset($component)) { $__componentOriginald4af959213ada05dacebaae2eb0906c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4af959213ada05dacebaae2eb0906c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.loading','data' => ['shape' => $shape,'size' => $size]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shape' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shape),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4af959213ada05dacebaae2eb0906c2)): ?>
<?php $attributes = $__attributesOriginald4af959213ada05dacebaae2eb0906c2; ?>
<?php unset($__attributesOriginald4af959213ada05dacebaae2eb0906c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4af959213ada05dacebaae2eb0906c2)): ?>
<?php $component = $__componentOriginald4af959213ada05dacebaae2eb0906c2; ?>
<?php unset($__componentOriginald4af959213ada05dacebaae2eb0906c2); ?>
<?php endif; ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?>
        <p class="text-base text-base-content opacity-70">
            <?php echo e($message); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/feedback/loading-message.blade.php ENDPATH**/ ?>