<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    /**
     * Position du menu flottant
     * bottom-right|bottom-left|top-right|top-left|left|right|top|bottom
     */
    'position' => 'bottom-right',
    /**
     * Orientation des boutons
     * vertical|horizontal
     */
    'orientation' => 'vertical',
    /**
     * Taille des boutons
     * xs|sm|md|lg|xl
     */
    'buttonSize' => 'md',
    /**
     * Variant des boutons
     * solid|outline|ghost|link|soft|dash
     */
    'buttonVariant' => 'ghost',
    /**
     * Couleur des boutons
     * primary|secondary|accent|info|success|warning|error|neutral
     */
    'buttonColor' => null,
    /**
     * Groupes de boutons
     * [
     *   [
     *     'items' => [
     *       ['icon' => 'pencil', 'label' => 'Edit', 'active' => false, 'href' => '/edit'],
     *       ['icon' => 'eye', 'label' => 'Preview', 'active' => true],
     *     ]
     *   ],
     *   [
     *     'items' => [...]
     *   ]
     * ]
     */
    'groups' => [],
    'allowInlineHandlers' => false,
    /**
     * Espacement entre les groupes (en rem)
     */
    'groupSpacing' => 1.5,
    /**
     * Espacement entre les boutons dans un groupe (en rem)
     */
    'itemSpacing' => 0.5,
    /**
     * Fond du menu
     */
    'bg' => true,
    /**
     * Bordure arrondie
     */
    'rounded' => true,
    /**
     * Ombre
     */
    'shadow' => true,
    /**
     * Padding du conteneur
     */
    'padding' => 'p-2',
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
    /**
     * Position du menu flottant
     * bottom-right|bottom-left|top-right|top-left|left|right|top|bottom
     */
    'position' => 'bottom-right',
    /**
     * Orientation des boutons
     * vertical|horizontal
     */
    'orientation' => 'vertical',
    /**
     * Taille des boutons
     * xs|sm|md|lg|xl
     */
    'buttonSize' => 'md',
    /**
     * Variant des boutons
     * solid|outline|ghost|link|soft|dash
     */
    'buttonVariant' => 'ghost',
    /**
     * Couleur des boutons
     * primary|secondary|accent|info|success|warning|error|neutral
     */
    'buttonColor' => null,
    /**
     * Groupes de boutons
     * [
     *   [
     *     'items' => [
     *       ['icon' => 'pencil', 'label' => 'Edit', 'active' => false, 'href' => '/edit'],
     *       ['icon' => 'eye', 'label' => 'Preview', 'active' => true],
     *     ]
     *   ],
     *   [
     *     'items' => [...]
     *   ]
     * ]
     */
    'groups' => [],
    'allowInlineHandlers' => false,
    /**
     * Espacement entre les groupes (en rem)
     */
    'groupSpacing' => 1.5,
    /**
     * Espacement entre les boutons dans un groupe (en rem)
     */
    'itemSpacing' => 0.5,
    /**
     * Fond du menu
     */
    'bg' => true,
    /**
     * Bordure arrondie
     */
    'rounded' => true,
    /**
     * Ombre
     */
    'shadow' => true,
    /**
     * Padding du conteneur
     */
    'padding' => 'p-2',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Classes de position
    $positionClasses = match($position) {
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'left' => 'left-4 top-1/2 -translate-y-1/2',
        'right' => 'right-4 top-1/2 -translate-y-1/2',
        'top' => 'top-4 left-1/2 -translate-x-1/2',
        'bottom' => 'bottom-4 left-1/2 -translate-x-1/2',
        default => 'bottom-4 right-4',
    };

    // Classes de flex selon l'orientation
    $flexClasses = $orientation === 'horizontal' 
        ? 'flex-row' 
        : 'flex-col';

    // Classes de conteneur
    $containerClasses = 'fixed z-50 flex ' . $flexClasses;
    
    if ($bg) {
        $containerClasses .= ' bg-base-100';
    }
    
    if ($rounded) {
        $containerClasses .= ' rounded-box';
    }
    
    if ($shadow) {
        $containerClasses .= ' shadow';
    }

    $containerClasses .= ' ' . $padding;

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

    $remSpacingClass = function ($value, string $prefix) {
        if (! is_numeric($value)) {
            return null;
        }

        $token = (int) round(((float) $value) * 4);

        return $token >= 0 && $token <= 64 ? "{$prefix}-rem-{$token}" : null;
    };

    $itemSpacingClass = $remSpacingClass($itemSpacing, 'daisy-floating-menu-gap');
    $groupSpacingClass = $remSpacingClass($groupSpacing, 'daisy-floating-menu-divider-spacing');
?>

<div <?php echo e($attributes->merge(['class' => $containerClasses . ' ' . $positionClasses])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupIndex => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="daisy-floating-menu-group flex <?php echo e($flexClasses); ?> <?php echo e($itemSpacingClass); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['items'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $isActive = $item['active'] ?? false;
                    $icon = $item['icon'] ?? null;
                    $label = $item['label'] ?? null;
                    $href = $normalizeHref($item['href'] ?? null);
                    $tag = $href ? 'a' : 'button';
                    
                    // Construction des classes de bouton
                    $buttonClasses = 'btn btn-' . $buttonSize . ' btn-' . $buttonVariant;
                    if ($isActive) {
                        $buttonClasses .= ' btn-active';
                    }
                    if ($buttonColor) {
                        $buttonClasses .= ' btn-' . $buttonColor;
                    }
                    $buttonClasses .= ' btn-square';
                ?>

                <<?php echo e($tag); ?>

                    <?php if($href): ?> href="<?php echo e($href); ?>" <?php endif; ?>
                    <?php if($tag === 'button'): ?> type="button" <?php endif; ?>
                    class="<?php echo e($buttonClasses); ?>"
                    <?php if($label): ?> aria-label="<?php echo e($label); ?>" title="<?php echo e($label); ?>" <?php endif; ?>
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon): ?>
                        <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $icon,'size' => $buttonSize]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($buttonSize)]); ?>
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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </<?php echo e($tag); ?>>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($groupIndex < count($groups) - 1): ?>
            <?php
                $dividerDirection = $orientation === 'horizontal' ? 'vertical' : 'horizontal';
            ?>
            <div class="daisy-floating-menu-divider divider divider-<?php echo e($dividerDirection); ?> <?php echo e($groupSpacingClass); ?>"></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php echo e($slot); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/floating-menu.blade.php ENDPATH**/ ?>