<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::form.wizard.title'),
    'action' => '#',
    'method' => 'POST',
    'steps' => [], // [['key' => 'profile', 'label' => 'Profil', 'icon' => 'user']]
    'currentStep' => 1,
    'linear' => true,
    'allowClickNav' => false,
    'showSummary' => true,
    'prevText' => __('daisy::form.previous'),
    'nextText' => __('daisy::form.next'),
    'finishText' => __('daisy::form.finish'),
    'resumeSlot' => 'summary',
    'autoRefreshCsrf' => true,
    'wizardKey' => 'wizard',
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
    'title' => __('daisy::form.wizard.title'),
    'action' => '#',
    'method' => 'POST',
    'steps' => [], // [['key' => 'profile', 'label' => 'Profil', 'icon' => 'user']]
    'currentStep' => 1,
    'linear' => true,
    'allowClickNav' => false,
    'showSummary' => true,
    'prevText' => __('daisy::form.previous'),
    'nextText' => __('daisy::form.next'),
    'finishText' => __('daisy::form.finish'),
    'resumeSlot' => 'summary',
    'autoRefreshCsrf' => true,
    'wizardKey' => 'wizard',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use Art35rennes\DaisyKit\Helpers\WizardPersistence;
    
    // Générer un ID unique pour cette instance si non fourni
    $instanceId = $attributes->get('id') ?? 'wizard-'.uniqid();
    
    // Récupérer l'étape courante depuis la session ou utiliser celle fournie
    $current = WizardPersistence::getCurrentStep($wizardKey) ?? $currentStep;
    $totalSteps = count($steps);
    $isLastStep = $current >= $totalSteps;
    $isFirstStep = $current <= 1;
    
    // Récupérer les données persistées
    $wizardData = WizardPersistence::get($wizardKey);
    
    // Construire les items pour le stepper
    $stepItems = [];
    $stepsContents = [];

    foreach ($steps as $index => $step) {
        $stepKey = is_array($step) ? ($step['key'] ?? null) : null;
        $stepLabel = is_array($step) ? ($step['label'] ?? "Step ".($index + 1)) : (string) $step;
        $stepIcon = is_array($step) ? ($step['icon'] ?? null) : null;
        $stepIndex = $index + 1;
        
        $stepItems[] = [
            'key' => $stepKey ?? 'step_'.$stepIndex,
            'label' => $stepLabel,
            'icon' => $stepIcon,
            'disabled' => $linear && $stepIndex > $current,
        ];

        // Mapper le contenu des vues de démonstration sur l'index de l'étape.
        if ($stepKey && isset(${'step_'.$stepKey})) {
            $stepsContents[$stepIndex] = ${'step_'.$stepKey};
        }
    }
?>

<?php
    $formMethod = strtoupper($method);
    $isGet = $formMethod === 'GET';
    $htmlMethod = $isGet ? 'GET' : 'POST';
?>

<form 
    id="<?php echo e($instanceId); ?>"
    action="<?php echo e($action); ?>" 
    method="<?php echo e($htmlMethod); ?>" 
    data-module="wizard"
    data-wizard-key="<?php echo e($wizardKey); ?>"
    data-wizard-instance-id="<?php echo e($instanceId); ?>"
    data-linear="<?php echo e($linear ? 'true' : 'false'); ?>"
    data-current-step="<?php echo e($current); ?>"
    <?php echo e($attributes->except(['id'])->class('space-y-6')); ?>

>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isGet): ?>
        <?php echo csrf_field(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isGet && $formMethod !== 'POST'): ?>
        <?php echo method_field($formMethod); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($autoRefreshCsrf && !$isGet): ?>
        <?php if (isset($component)) { $__componentOriginal11d192a26b600b197a9d17a6b06c2323 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11d192a26b600b197a9d17a6b06c2323 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.utilities.csrf-keeper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.utilities.csrf-keeper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11d192a26b600b197a9d17a6b06c2323)): ?>
<?php $attributes = $__attributesOriginal11d192a26b600b197a9d17a6b06c2323; ?>
<?php unset($__attributesOriginal11d192a26b600b197a9d17a6b06c2323); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11d192a26b600b197a9d17a6b06c2323)): ?>
<?php $component = $__componentOriginal11d192a26b600b197a9d17a6b06c2323; ?>
<?php unset($__componentOriginal11d192a26b600b197a9d17a6b06c2323); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <input type="hidden" name="_wizard_step" value="<?php echo e($current); ?>" />
    <input type="hidden" name="_wizard_key" value="<?php echo e($wizardKey); ?>" />
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
        <h2 class="text-2xl font-semibold mb-6"><?php echo e($title); ?></h2>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    
    <?php if (isset($component)) { $__componentOriginalbe533ac3007122dd2e245904b3907516 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe533ac3007122dd2e245904b3907516 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.stepper','data' => ['items' => $stepItems,'current' => $current,'linear' => $linear,'allowClickNav' => $allowClickNav,'stepsContents' => $stepsContents,'showControls' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.stepper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stepItems),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($current),'linear' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($linear),'allowClickNav' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($allowClickNav),'stepsContents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stepsContents),'showControls' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe533ac3007122dd2e245904b3907516)): ?>
<?php $attributes = $__attributesOriginalbe533ac3007122dd2e245904b3907516; ?>
<?php unset($__attributesOriginalbe533ac3007122dd2e245904b3907516); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe533ac3007122dd2e245904b3907516)): ?>
<?php $component = $__componentOriginalbe533ac3007122dd2e245904b3907516; ?>
<?php unset($__componentOriginalbe533ac3007122dd2e245904b3907516); ?>
<?php endif; ?>
    
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLastStep && $showSummary): ?>
        <div class="card card-border bg-base-200" data-summary="summary" aria-label="summary">
            <div class="card-body">
                <h3 class="card-title"><?php echo e(__('daisy::form.wizard.summary')); ?></h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($summary)): ?>
                    <?php echo $summary; ?>

                <?php else: ?>
                    <p class="text-sm text-base-content/70"><?php echo e(__('daisy::form.wizard.summary_empty')); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    
    <div class="flex items-center justify-between pt-6 border-t">
        <div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isFirstStep): ?>
                <button 
                    type="button" 
                    class="btn btn-ghost"
                    data-wizard-prev
                >
                    <?php echo e($prevText); ?>

                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        <div class="flex items-center gap-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actions)): ?>
                <?php echo $actions; ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isLastStep): ?>
                <button 
                    type="button" 
                    class="btn btn-primary"
                    data-wizard-next
                >
                    <?php echo e($nextText); ?>

                </button>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'submit','variant' => 'solid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'solid']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e($finishText); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</form>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views//templates/form/form-wizard.blade.php ENDPATH**/ ?>