<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'statusCode' => 500, // 404, 403, 500, 503, etc.
    'title' => null, // Auto-generated if null
    'message' => null, // Auto-generated if null
    'theme' => null,
    // Routes
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    // Options
    'showIllustration' => true,
    'showActions' => true,
    'showDetails' => null, // Auto-détecté depuis config('app.debug'), ne peut pas être forcé à true en production
    'exception' => null, // $exception from Laravel (injected automatically)
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
    'statusCode' => 500, // 404, 403, 500, 503, etc.
    'title' => null, // Auto-generated if null
    'message' => null, // Auto-generated if null
    'theme' => null,
    // Routes
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    // Options
    'showIllustration' => true,
    'showActions' => true,
    'showDetails' => null, // Auto-détecté depuis config('app.debug'), ne peut pas être forcé à true en production
    'exception' => null, // $exception from Laravel (injected automatically)
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
?>

<?php
    // Génération automatique du titre si non fourni
    if ($title === null) {
        $title = __('daisy::errors.'.$statusCode.'_title', ['default' => __('daisy::errors.error_title', ['code' => $statusCode])]);
    }
    
    // Génération automatique du message si non fourni
    if ($message === null) {
        $message = __('daisy::errors.'.$statusCode.'_message', ['default' => __('daisy::errors.error_message', ['code' => $statusCode])]);
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

    <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-8">
        <div class="w-full space-y-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showIllustration): ?>
                <?php if (isset($component)) { $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.hero','data' => ['minH' => 'min-h-[12rem]','fullScreen' => false,'class' => 'mb-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['minH' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('min-h-[12rem]'),'fullScreen' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'class' => 'mb-8']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="text-center">
                        <div class="text-6xl mb-4">⚠️</div>
                        <p class="text-base-content/70"><?php echo e(__('daisy::errors.error_occurred')); ?></p>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <?php if (isset($component)) { $__componentOriginald21be948d98f6fa1db0308caf35e65e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald21be948d98f6fa1db0308caf35e65e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.errors.error-content','data' => ['statusCode' => $statusCode,'title' => $title,'message' => $message,'homeUrl' => $homeUrl,'backUrl' => $backUrl,'showActions' => $showActions,'showDetails' => $showDetails,'exception' => $exception]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.errors.error-content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['statusCode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusCode),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message),'homeUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($homeUrl),'backUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($backUrl),'showActions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showActions),'showDetails' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showDetails),'exception' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($exception)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald21be948d98f6fa1db0308caf35e65e8)): ?>
<?php $attributes = $__attributesOriginald21be948d98f6fa1db0308caf35e65e8; ?>
<?php unset($__attributesOriginald21be948d98f6fa1db0308caf35e65e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald21be948d98f6fa1db0308caf35e65e8)): ?>
<?php $component = $__componentOriginald21be948d98f6fa1db0308caf35e65e8; ?>
<?php unset($__componentOriginald21be948d98f6fa1db0308caf35e65e8); ?>
<?php endif; ?>
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


<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/errors/error.blade.php ENDPATH**/ ?>