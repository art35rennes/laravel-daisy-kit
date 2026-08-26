<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('daisy::common.loading'),
    'size' => 'lg',
    'skeletonCount' => 3,
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
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('daisy::common.loading'),
    'size' => 'lg',
    'skeletonCount' => 3,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'spinner'): ?>
    <?php if (isset($component)) { $__componentOriginal7b3fa3d26bbf95a68b9bd5fefafe5671 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b3fa3d26bbf95a68b9bd5fefafe5671 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.loading-message','data' => ['message' => $message,'shape' => 'spinner','size' => $size]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.loading-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message),'shape' => 'spinner','size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b3fa3d26bbf95a68b9bd5fefafe5671)): ?>
<?php $attributes = $__attributesOriginal7b3fa3d26bbf95a68b9bd5fefafe5671; ?>
<?php unset($__attributesOriginal7b3fa3d26bbf95a68b9bd5fefafe5671); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b3fa3d26bbf95a68b9bd5fefafe5671)): ?>
<?php $component = $__componentOriginal7b3fa3d26bbf95a68b9bd5fefafe5671; ?>
<?php unset($__componentOriginal7b3fa3d26bbf95a68b9bd5fefafe5671); ?>
<?php endif; ?>
<?php elseif($type === 'skeleton'): ?>
    <div class="flex flex-col items-center gap-6 w-full">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?>
            <p class="text-base text-base-content opacity-70">
                <?php echo e($message); ?>

            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <div class="w-full space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < $skeletonCount; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalae7c293fda9ce77dd4837f94dbe9ec2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae7c293fda9ce77dd4837f94dbe9ec2c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.skeleton','data' => ['width' => 'w-full','height' => 'h-20','rounded' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.skeleton'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['width' => 'w-full','height' => 'h-20','rounded' => 'md']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae7c293fda9ce77dd4837f94dbe9ec2c)): ?>
<?php $attributes = $__attributesOriginalae7c293fda9ce77dd4837f94dbe9ec2c; ?>
<?php unset($__attributesOriginalae7c293fda9ce77dd4837f94dbe9ec2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae7c293fda9ce77dd4837f94dbe9ec2c)): ?>
<?php $component = $__componentOriginalae7c293fda9ce77dd4837f94dbe9ec2c; ?>
<?php unset($__componentOriginalae7c293fda9ce77dd4837f94dbe9ec2c); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
<?php elseif($type === 'progress'): ?>
    <div class="flex flex-col items-center gap-4 w-full">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?>
            <p class="text-base text-base-content opacity-70">
                <?php echo e($message); ?>

            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <?php if (isset($component)) { $__componentOriginaladc97dfa100de6df8becd6d68bcb705c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaladc97dfa100de6df8becd6d68bcb705c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.progress','data' => ['color' => 'primary','class' => 'w-full max-w-md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'primary','class' => 'w-full max-w-md']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaladc97dfa100de6df8becd6d68bcb705c)): ?>
<?php $attributes = $__attributesOriginaladc97dfa100de6df8becd6d68bcb705c; ?>
<?php unset($__attributesOriginaladc97dfa100de6df8becd6d68bcb705c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaladc97dfa100de6df8becd6d68bcb705c)): ?>
<?php $component = $__componentOriginaladc97dfa100de6df8becd6d68bcb705c; ?>
<?php unset($__componentOriginaladc97dfa100de6df8becd6d68bcb705c); ?>
<?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/errors/loading-state-content.blade.php ENDPATH**/ ?>