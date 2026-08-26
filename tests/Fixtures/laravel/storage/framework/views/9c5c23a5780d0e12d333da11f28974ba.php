<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'statusCode' => 500,
    'title' => null,
    'message' => null,
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    'showActions' => true,
    'showDetails' => null, // Auto-détecté depuis config('app.debug'), ne peut pas être forcé à true en production
    'exception' => null, // $exception from Laravel
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
    'statusCode' => 500,
    'title' => null,
    'message' => null,
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    'showActions' => true,
    'showDetails' => null, // Auto-détecté depuis config('app.debug'), ne peut pas être forcé à true en production
    'exception' => null, // $exception from Laravel
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // SÉCURITÉ : Force showDetails à false si on n'est pas en mode debug
    // Même si showDetails=true est passé manuellement, on ne l'accepte que si app.debug=true
    $isDebugMode = config('app.debug', false);
    $showDetails = $isDebugMode && ($showDetails !== false);
    
    // Génération automatique du message si non fourni
    if ($message === null) {
        $message = __('daisy::errors.'.$statusCode.'_message', ['default' => __('daisy::errors.error_message', ['code' => $statusCode])]);
    }
    
    // Extraction des détails de l'exception si disponible (uniquement en mode debug)
    $exceptionMessage = null;
    $exceptionTrace = null;
    if ($showDetails && $exception) {
        $exceptionMessage = $exception->getMessage();
        if (method_exists($exception, 'getTraceAsString')) {
            $exceptionTrace = $exception->getTraceAsString();
        }
    }
?>

<?php if (isset($component)) { $__componentOriginal5efd72db83161bae6e1dc57d5f89d224 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.card','data' => ['class' => 'max-w-2xl mx-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'max-w-2xl mx-auto']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="space-y-6">
        
        <?php if (isset($component)) { $__componentOriginal73e981b44a815f148903fc832a893c39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73e981b44a815f148903fc832a893c39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.errors.error-header','data' => ['statusCode' => $statusCode,'title' => $title]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.errors.error-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['statusCode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusCode),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal73e981b44a815f148903fc832a893c39)): ?>
<?php $attributes = $__attributesOriginal73e981b44a815f148903fc832a893c39; ?>
<?php unset($__attributesOriginal73e981b44a815f148903fc832a893c39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal73e981b44a815f148903fc832a893c39)): ?>
<?php $component = $__componentOriginal73e981b44a815f148903fc832a893c39; ?>
<?php unset($__componentOriginal73e981b44a815f148903fc832a893c39); ?>
<?php endif; ?>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?>
            <p class="text-center text-base text-base-content opacity-80">
                <?php echo e($message); ?>

            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showActions): ?>
            <?php if (isset($component)) { $__componentOriginal5c594156de45920e27cf79cab043e8b8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c594156de45920e27cf79cab043e8b8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.errors.error-actions','data' => ['homeUrl' => $homeUrl,'backUrl' => $backUrl]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.errors.error-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['homeUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($homeUrl),'backUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($backUrl)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c594156de45920e27cf79cab043e8b8)): ?>
<?php $attributes = $__attributesOriginal5c594156de45920e27cf79cab043e8b8; ?>
<?php unset($__attributesOriginal5c594156de45920e27cf79cab043e8b8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c594156de45920e27cf79cab043e8b8)): ?>
<?php $component = $__componentOriginal5c594156de45920e27cf79cab043e8b8; ?>
<?php unset($__componentOriginal5c594156de45920e27cf79cab043e8b8); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDetails && ($exceptionMessage || $exceptionTrace)): ?>
            <?php if (isset($component)) { $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.alert','data' => ['color' => 'error','variant' => 'outline','title' => ''.e(__('daisy::errors.debug_details')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'error','variant' => 'outline','title' => ''.e(__('daisy::errors.debug_details')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($exceptionMessage): ?>
                    <div class="mb-2">
                        <strong><?php echo e(__('daisy::errors.exception_message')); ?>:</strong>
                        <pre class="mt-1 text-xs overflow-x-auto bg-base-200 p-2 rounded"><?php echo e($exceptionMessage); ?></pre>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($exceptionTrace): ?>
                    <details class="mt-2">
                        <summary class="cursor-pointer text-sm font-medium mb-2"><?php echo e(__('daisy::errors.stack_trace')); ?></summary>
                        <pre class="text-xs overflow-x-auto bg-base-200 p-2 rounded max-h-64 overflow-y-auto"><?php echo e($exceptionTrace); ?></pre>
                    </details>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $attributes = $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $component = $__componentOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/errors/error-content.blade.php ENDPATH**/ ?>