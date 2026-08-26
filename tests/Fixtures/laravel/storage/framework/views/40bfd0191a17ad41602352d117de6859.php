<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // items: tableau d'onglets
    // ex: [ ['label' => 'Tab 1', 'active' => true, 'disabled' => false, 'href' => '#', 'content' => '...'], ... ]
    'items' => [],
    // Styles: box|boxed|border|bordered|lifted
    'variant' => null,
    // Tailles: xs|sm|md|lg|xl (classe sur le conteneur)
    'size' => null,
    // Placement: top|bottom
    'placement' => 'top',
    // Mode radio + contenu juste après chaque tab
    'radioName' => null, // si fourni, rend <input type="radio" class="tab"> + <div class="tab-content"> pour chaque item
    'contentClass' => 'border-base-300 bg-base-100 p-6',
    'errorBag' => null,
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
    // items: tableau d'onglets
    // ex: [ ['label' => 'Tab 1', 'active' => true, 'disabled' => false, 'href' => '#', 'content' => '...'], ... ]
    'items' => [],
    // Styles: box|boxed|border|bordered|lifted
    'variant' => null,
    // Tailles: xs|sm|md|lg|xl (classe sur le conteneur)
    'size' => null,
    // Placement: top|bottom
    'placement' => 'top',
    // Mode radio + contenu juste après chaque tab
    'radioName' => null, // si fourni, rend <input type="radio" class="tab"> + <div class="tab-content"> pour chaque item
    'contentClass' => 'border-base-300 bg-base-100 p-6',
    'errorBag' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Construction des classes CSS selon la variante, la taille et le placement.
    $classes = 'tabs';
    if ($variant) {
        // Mapping des variantes (support des alias : box/boxed, border/bordered).
        $map = [
            'box' => 'tabs-box',
            'boxed' => 'tabs-box',
            'border' => 'tabs-border',
            'bordered' => 'tabs-border',
            'lifted' => 'tabs-lift',
        ];
        $classes .= ' '.($map[$variant] ?? '');
    }
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) {
        $classes .= ' tabs-'.$size;
    }
    // Placement des onglets : top (défaut) ou bottom.
    if ($placement === 'bottom') {
        $classes .= ' tabs-bottom';
    } else {
        $classes .= ' tabs-top';
    }

    // Mode radio : si radioName est fourni, utilise des inputs radio + contenu associé (pattern daisyUI).
    $isRadio = !empty($radioName);
    $generatedRadio = $isRadio ? $radioName : null;
    // Génération d'un nom unique si radioName est null mais que le mode radio est activé.
    if (!$generatedRadio && $radioName !== null) {
        $generatedRadio = uniqid('tabs_', false);
    }
    $resolvedErrorBag = $errorBag instanceof \Illuminate\Support\ViewErrorBag
        ? $errorBag
        : (view()->shared('errors') instanceof \Illuminate\Support\ViewErrorBag ? view()->shared('errors') : new \Illuminate\Support\ViewErrorBag());
    $errorLabel = __('daisy::components.tab_error');

    $normalizeHref = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if ($url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : null;
    };
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isRadio): ?>
    
    <div role="tablist" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                if (data_get($tab, 'visible', true) === false) {
                    continue;
                }
                // Extraction des propriétés de l'onglet.
                $isActive = (bool) data_get($tab, 'active', false);
                $isDisabled = (bool) data_get($tab, 'disabled', false);
                $label = data_get($tab, 'label', __('daisy::components.tab'));
                $href = $normalizeHref(data_get($tab, 'href'));
                $iconName = data_get($tab, 'iconName');
                $errorKey = data_get($tab, 'errorKey');
                $hasError = is_string($errorKey) && $resolvedErrorBag->has($errorKey);
                // Construction des classes : tab de base + états (active, disabled).
                $tabClasses = 'tab'.($isActive ? ' tab-active' : '').($isDisabled ? ' tab-disabled' : '').($hasError ? ' text-error' : '');
            ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($href): ?>
                <a role="tab" href="<?php echo e($href); ?>" class="<?php echo e($tabClasses); ?>" aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconName): ?><?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $iconName] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span><?php echo e($label); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasError): ?><span aria-hidden="true">•</span><span class="sr-only"><?php echo e($errorLabel); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
            <?php else: ?>
                <button role="tab" class="<?php echo e($tabClasses); ?>" <?php if($isDisabled): echo 'disabled'; endif; ?> aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconName): ?><?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $iconName] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span><?php echo e($label); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasError): ?><span aria-hidden="true">•</span><span class="sr-only"><?php echo e($errorLabel); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
<?php else: ?>
    
    <div <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                if (data_get($tab, 'visible', true) === false) {
                    continue;
                }
                $label = data_get($tab, 'label', __('daisy::components.tab'));
                // L'onglet est checked s'il est explicitement actif OU si c'est le premier (index 0).
                $checked = array_key_exists('active', $tab) ? (bool) data_get($tab, 'active') : ($index === 0);
                $isDisabled = (bool) data_get($tab, 'disabled', false);
                $errorKey = data_get($tab, 'errorKey');
                $hasError = is_string($errorKey) && $resolvedErrorBag->has($errorKey);
            ?>
            
            <input type="radio" name="<?php echo e($generatedRadio); ?>" class="tab <?php echo e($hasError ? 'text-error' : ''); ?>" aria-label="<?php echo e($label); ?>" <?php if($checked): echo 'checked'; endif; ?> <?php if($isDisabled): echo 'disabled'; endif; ?> />
            
            <div class="tab-content <?php echo e($contentClass); ?>"><?php echo e($tab['content'] ?? ''); ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/tabs.blade.php ENDPATH**/ ?>