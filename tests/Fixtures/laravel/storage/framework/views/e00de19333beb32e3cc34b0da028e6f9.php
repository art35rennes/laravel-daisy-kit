

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'theme' => null,
    // Grid options
    'gap' => 4,
    'align' => 'start', // start|center|end
    'container' => true,
    'containerClass' => 'container mx-auto p-6',
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
    'title' => null,
    'theme' => null,
    // Grid options
    'gap' => 4,
    'align' => 'start', // start|center|end
    'container' => true,
    'containerClass' => 'container mx-auto p-6',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $slot = $slot ?? '';
?>

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($container): ?>
        <div class="<?php echo e($containerClass); ?>">
            <?php if (isset($component)) { $__componentOriginala8292fbc6719c22f60e6bbff9e345811 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8292fbc6719c22f60e6bbff9e345811 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.grid-layout','data' => ['gap' => $gap,'align' => $align]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.grid-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['gap' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($gap),'align' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($align)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e($slot); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $attributes = $__attributesOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $component = $__componentOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__componentOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>
        </div>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginala8292fbc6719c22f60e6bbff9e345811 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8292fbc6719c22f60e6bbff9e345811 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.grid-layout','data' => ['gap' => $gap,'align' => $align]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.grid-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['gap' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($gap),'align' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($align)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php echo e($slot); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $attributes = $__attributesOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $component = $__componentOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__componentOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $attributes = $__attributesOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__attributesOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $component = $__componentOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__componentOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/layout/grid.blade.php ENDPATH**/ ?>