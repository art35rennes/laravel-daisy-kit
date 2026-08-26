<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::maintenance.maintenance'),
    'theme' => null,
    'message' => null, // Custom message or use Laravel's
    'retryAfter' => null, // Retry-After header value
    'allowedIps' => [], // IPs allowed during maintenance
    'showAllowedIps' => false,
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
    'title' => __('daisy::maintenance.maintenance'),
    'theme' => null,
    'message' => null, // Custom message or use Laravel's
    'retryAfter' => null, // Retry-After header value
    'allowedIps' => [], // IPs allowed during maintenance
    'showAllowedIps' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Utilisation du message Laravel par défaut si non fourni
    if ($message === null) {
        $message = __('daisy::maintenance.message');
    }
    
    // Formatage de la date de retour estimée si disponible
    $estimatedReturn = null;
    if ($retryAfter) {
        try {
            $carbon = \Carbon\Carbon::now()->addSeconds($retryAfter);
            $estimatedReturn = $carbon->format('d/m/Y H:i');
        } catch (\Exception $e) {
            // Ignore si Carbon n'est pas disponible ou erreur de formatage
        }
    }
?>

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="min-h-screen flex items-center justify-center py-8">
        <div class="w-full max-w-2xl space-y-6">
            <?php if (isset($component)) { $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.hero','data' => ['minH' => 'min-h-[16rem]','fullScreen' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['minH' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('min-h-[16rem]'),'fullScreen' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div class="text-center space-y-4">
                    <?php if (isset($component)) { $__componentOriginald4af959213ada05dacebaae2eb0906c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4af959213ada05dacebaae2eb0906c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.loading','data' => ['shape' => 'spinner','size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shape' => 'spinner','size' => 'xl']); ?>
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
                    <h1 class="text-3xl font-bold text-base-content">
                        <?php echo e($title); ?>

                    </h1>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1)): ?>
<?php $attributes = $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1; ?>
<?php unset($__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal40312bcd153c4f1bbfbe6543713be4a1)): ?>
<?php $component = $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1; ?>
<?php unset($__componentOriginal40312bcd153c4f1bbfbe6543713be4a1); ?>
<?php endif; ?>
            
            <?php if (isset($component)) { $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.alert','data' => ['color' => 'warning','variant' => 'soft','title' => ''.e(__('daisy::maintenance.maintenance')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'warning','variant' => 'soft','title' => ''.e(__('daisy::maintenance.maintenance')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <p><?php echo e($message); ?></p>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($estimatedReturn): ?>
                    <p class="mt-2 text-sm">
                        <?php echo e(__('daisy::maintenance.estimated_return', ['time' => $estimatedReturn])); ?>

                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $attributes = $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $component = $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAllowedIps && !empty($allowedIps)): ?>
                <?php if (isset($component)) { $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.alert','data' => ['color' => 'info','variant' => 'outline','title' => ''.e(__('daisy::maintenance.allowed_ips')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'info','variant' => 'outline','title' => ''.e(__('daisy::maintenance.allowed_ips')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <ul class="list-disc list-inside text-sm space-y-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $allowedIps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><?php echo e($ip); ?></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $attributes = $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $component = $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
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
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/errors/maintenance.blade.php ENDPATH**/ ?>