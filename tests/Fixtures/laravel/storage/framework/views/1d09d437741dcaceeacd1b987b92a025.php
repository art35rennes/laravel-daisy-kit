<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => [],
    'current' => 0,
    'vertical' => false,
    'horizontal' => false,
    'horizontalAt' => null,
    'color' => 'primary',
    'allowClickNav' => false,
    'rootId' => null,
    'indicatorAttribute' => 'data-step-index',
    'indicatorOffset' => 0,
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
    'current' => 0,
    'vertical' => false,
    'horizontal' => false,
    'horizontalAt' => null,
    'color' => 'primary',
    'allowClickNav' => false,
    'rootId' => null,
    'indicatorAttribute' => 'data-step-index',
    'indicatorOffset' => 0,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Configuration des classes d'orientation : responsive (horizontalAt) > vertical > horizontal > défaut.
    $orientationClasses = match(true) {
        $horizontalAt => "steps-vertical {$horizontalAt}:steps-horizontal",
        $vertical => 'steps-vertical',
        $horizontal => 'steps-horizontal',
        default => '',
    };

    // Validation de la couleur : doit être une couleur daisyUI valide.
    $validColors = ['neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'];
    $defaultColor = in_array($color, $validColors) ? $color : 'primary';

    // Fonction helper pour extraire et normaliser les propriétés d'un item d'étape.
    $extractItemData = function($item, $index) use ($defaultColor, $current, $validColors, $allowClickNav, $rootId, $indicatorAttribute, $indicatorOffset) {
        // Extraction des propriétés de base (support array ou string simple).
        $data = [
            'label' => is_array($item) ? ($item['label'] ?? '') : (string) $item,
            'icon' => is_array($item) ? ($item['icon'] ?? null) : null,
            'disabled' => is_array($item) ? (bool) ($item['disabled'] ?? false) : false,
            'invalid' => is_array($item) ? (bool) ($item['invalid'] ?? false) : false,
            // Index de l'étape : priorité à item.index, sinon calculé depuis l'index du tableau.
            'stepIndex' => is_array($item) ? ($item['index'] ?? ($index + 1)) : ($index + 1),
        ];
        
        // Une étape est "done" si son index est <= current ET qu'elle n'est pas disabled.
        $data['isDone'] = ($data['stepIndex'] <= $current) && !$data['disabled'];
        
        // Gestion des couleurs : couleur explicite de l'item > couleur par défaut si done > aucune couleur.
        $itemColor = is_array($item) ? ($item['color'] ?? null) : null;
        if ($itemColor && in_array($itemColor, $validColors)) {
            $data['colorClass'] = "step-{$itemColor}";
        } elseif ($data['isDone']) {
            $data['colorClass'] = "step-{$defaultColor}";
        } else {
            $data['colorClass'] = '';
        }
        
        // Construction des classes CSS : step de base + couleur + états (error, disabled).
        $classes = ['step'];
        if ($data['colorClass']) $classes[] = $data['colorClass'];
        if ($data['invalid']) $classes[] = 'step-error';
        if ($data['disabled']) $classes[] = 'pointer-events-none opacity-50';
        $data['classes'] = implode(' ', $classes);
        
        // Attributs d'accessibilité : tabindex/role pour navigation clavier, id/aria-controls pour ARIA.
        $data['attributes'] = [];
        if ($allowClickNav && !$data['disabled']) {
            $data['attributes']['tabindex'] = '0';
            $data['attributes']['role'] = 'button';
        }
        if ($rootId) {
            $data['attributes']['id'] = "{$rootId}-header-{$data['stepIndex']}";
            $data['attributes']['aria-controls'] = "{$rootId}-panel-{$data['stepIndex']}";
        }
        $safeIndicatorAttribute = is_string($indicatorAttribute) && preg_match('/^[a-zA-Z_:][-a-zA-Z0-9_:.]*$/', $indicatorAttribute)
            ? $indicatorAttribute
            : 'data-step-index';
        $data['attributes'][$safeIndicatorAttribute] = $data['stepIndex'] + (int) $indicatorOffset;
        
        return $data;
    };
?>

<ul <?php echo e($attributes->merge(['class' => "steps {$orientationClasses}"])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php $stepData = $extractItemData($item, $index); ?>
        
        <li class="<?php echo e($stepData['classes']); ?>" 
            <?php $__currentLoopData = $stepData['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e($attr); ?>="<?php echo e($value); ?>"
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stepData['icon']): ?>
                <span class="step-icon">
                    <?php $__icon = $stepData['icon']; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_string($__icon) && !str_contains($__icon, '<')): ?>
                        <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $__icon,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($__icon),'size' => 'sm']); ?>
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
                    <?php else: ?>
                        <?php echo $__icon; ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <?php echo e($stepData['label']); ?>

        </li>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</ul>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/steps.blade.php ENDPATH**/ ?>