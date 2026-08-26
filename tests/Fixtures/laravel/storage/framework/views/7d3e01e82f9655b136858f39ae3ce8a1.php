<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => [],
    'current' => 1,
    'stepsContents' => [],
    'vertical' => false,
    'horizontal' => false,
    'horizontalAt' => null,
    'linear' => false,
    'allowClickNav' => true,
    'persist' => false,
    'showControls' => true,
    'prevText' => null,
    'nextText' => null,
    'finishText' => null,
    'controlsClass' => '',
    'validateBeforeNext' => false,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
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
    'items' => [],
    'current' => 1,
    'stepsContents' => [],
    'vertical' => false,
    'horizontal' => false,
    'horizontalAt' => null,
    'linear' => false,
    'allowClickNav' => true,
    'persist' => false,
    'showControls' => true,
    'prevText' => null,
    'nextText' => null,
    'finishText' => null,
    'controlsClass' => '',
    'validateBeforeNext' => false,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rootId = $attributes->get('id');
    
    // Calcul des index ajustés : les étapes désactivées ne sont pas comptées dans la numérotation.
    // Exemple : [step1, step2(disabled), step3] → step1=1, step2=disabled, step3=2 (pas 3).
    $calculateAdjustedIndexes = function($items) {
        $disabledCounts = [];
        $disabledCount = 0;
        
        foreach ($items as $idx => $item) {
            $disabled = is_array($item) && ($item['disabled'] ?? false);
            $disabledCounts[$idx] = $disabledCount;
            if ($disabled) $disabledCount++;
        }
        
        return $disabledCounts;
    };
    
    // Traitement d'un item d'étape : extraction des données (label, icon, état) et calcul de l'index ajusté.
    $processStepItem = function($item, $idx, $disabledCounts, $current) {
        $adjustedIndex = $idx + 1 - $disabledCounts[$idx];
        $disabled = is_array($item) && ($item['disabled'] ?? false);
        $invalid = is_array($item) && ($item['invalid'] ?? false);
        
        $stepData = [
            'label' => is_array($item) ? ($item['label'] ?? __('daisy::form.step', ['number' => $adjustedIndex])) : (string) $item,
            'icon' => is_array($item) ? ($item['icon'] ?? null) : null,
            'disabled' => $disabled,
            'invalid' => $invalid,
            'index' => $adjustedIndex,
        ];
        
        // Détermination de la couleur selon l'état : error si invalide, primary si complétée/active.
        if ($invalid) {
            $stepData['color'] = 'error';
        } elseif (!$disabled && $adjustedIndex <= $current) {
            $stepData['color'] = 'primary';
        }
        
        return $stepData;
    };
    
    // Traitement de tous les items pour générer la liste des étapes avec index ajustés.
    $disabledCounts = $calculateAdjustedIndexes($items);
    $stepsItems = [];
    
    foreach ($items as $idx => $item) {
        $stepsItems[] = $processStepItem($item, $idx, $disabledCounts, $current);
    }
    
    // Préparation des attributs du conteneur pour l'initialisation JavaScript.
    $containerAttrs = $attributes->class('w-full relative isolate')->merge([
        'data-module' => ($module ?? 'stepper'),
        'data-stepper' => true,
        'data-linear' => $linear ? 'true' : 'false',
        'data-allow-click' => $allowClickNav ? 'true' : 'false',
        'data-persist' => $persist ? 'true' : 'false',
        'data-current' => (int) $current,
        'data-validate-before-next' => $validateBeforeNext ? 'true' : 'false',
    ]);
    $resolvedPrevText = $prevText ?: __('daisy::form.previous');
    $resolvedNextText = $nextText ?: __('daisy::form.next');
    $resolvedFinishText = $finishText ?: __('daisy::form.finish');
?>

<div <?php echo e($containerAttrs); ?>>
    
    <div class="mb-4" data-stepper-headers>
        <?php if (isset($component)) { $__componentOriginal40a8b56802e388c367cfcc5185c1390a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal40a8b56802e388c367cfcc5185c1390a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.steps','data' => ['items' => $stepsItems,'current' => $current,'vertical' => $vertical,'horizontal' => $horizontal,'horizontalAt' => $horizontalAt,'allowClickNav' => $allowClickNav,'rootId' => $rootId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.steps'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stepsItems),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($current),'vertical' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vertical),'horizontal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($horizontal),'horizontalAt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($horizontalAt),'allowClickNav' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($allowClickNav),'rootId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rootId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal40a8b56802e388c367cfcc5185c1390a)): ?>
<?php $attributes = $__attributesOriginal40a8b56802e388c367cfcc5185c1390a; ?>
<?php unset($__attributesOriginal40a8b56802e388c367cfcc5185c1390a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal40a8b56802e388c367cfcc5185c1390a)): ?>
<?php $component = $__componentOriginal40a8b56802e388c367cfcc5185c1390a; ?>
<?php unset($__componentOriginal40a8b56802e388c367cfcc5185c1390a); ?>
<?php endif; ?>
    </div>

    
    <div class="space-y-4 relative z-0" data-stepper-contents>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stepsItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php $stepIndex = $stepItem['index']; ?>
            
            
            <div class="<?php if($stepIndex !== (int)$current): ?> hidden <?php endif; ?>" 
                 data-step-content 
                 data-step-index="<?php echo e($stepIndex); ?>"
                 <?php if($rootId): ?> 
                     id="<?php echo e($rootId); ?>-panel-<?php echo e($stepIndex); ?>"
                     aria-labelledby="<?php echo e($rootId); ?>-header-<?php echo e($stepIndex); ?>"
                 <?php endif; ?>
                 role="region"
                 aria-hidden="<?php echo e($stepIndex !== (int)$current ? 'true' : 'false'); ?>">
                <?php
                    // Vérification de la présence de contenu externe (via $stepsContents array).
                    $hasExternalContent = is_array($stepsContents) && array_key_exists($stepIndex, $stepsContents);
                ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasExternalContent): ?>
                    <?php echo $stepsContents[$stepIndex] instanceof \Illuminate\View\ComponentSlot ? $stepsContents[$stepIndex]->toHtml() : (string) $stepsContents[$stepIndex]; ?>

                <?php elseif(isset(${'step_'.$stepIndex})): ?>
                    <?php echo e(${'step_'.$stepIndex}); ?>

                <?php elseif(isset($slot) && $slot->isNotEmpty()): ?>
                    <?php echo e($slot); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showControls): ?>
        <div class="mt-4 flex items-center justify-between <?php echo e($controlsClass); ?>" data-stepper-controls>
            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['variant' => 'ghost','size' => 'sm','dataStepperPrev' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','size' => 'sm','data-stepper-prev' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e($resolvedPrevText); ?>

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
            
            <div class="flex gap-2">
                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['size' => 'sm','dataStepperNext' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','data-stepper-next' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e($resolvedNextText); ?>

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
                
                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['size' => 'sm','color' => 'success','dataStepperFinish' => true,'class' => 'hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','color' => 'success','data-stepper-finish' => true,'class' => 'hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e($resolvedFinishText); ?>

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
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/stepper.blade.php ENDPATH**/ ?>