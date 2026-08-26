<?php
    $label = __($item['label'] ?? '');
    $children = array_values(array_filter(
        (array) data_get($item, 'children', []),
        fn ($child) => data_get($child, 'visible', true) !== false,
    ));
    $hasChildren = $children !== [];
    $itemIsActive = $isItemActive($item);
    $descendantIsActive = $hasActiveDescendant($children);
    $isOpen = (bool) data_get($item, 'open', false) || $itemIsActive || $descendantIsActive;
    $external = (bool) data_get($item, 'external', false);
    $href = $normalizeHref($item['href'] ?? '#');
    $icon = data_get($item, 'icon') ?: $fallbackIcon;
?>

<li
    data-sidebar-item
    data-sidebar-label="<?php echo e($label); ?>"
    data-sidebar-depth="<?php echo e($depth); ?>"
    data-sidebar-section-id="<?php echo e($sectionId); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
        <details <?php if($isOpen): ?> open <?php endif; ?> data-sidebar-details data-sidebar-default-open="<?php echo e($isOpen ? '1' : '0'); ?>">
            <summary
                class="flex items-center gap-2 <?php echo e($itemIsActive ? 'menu-active' : ''); ?>"
                title="<?php echo e($label); ?>"
                aria-label="<?php echo e($label); ?>"
                data-sidebar-row
            >
                <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $icon,'prefix' => $iconPrefix,'size' => 'md','class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'size' => 'md','class' => 'shrink-0']); ?>
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
                <span class="min-w-0 flex-1 truncate sidebar-label"><?php echo e($label); ?></span>
            </summary>
            <ul data-sidebar-submenu>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php echo $__env->make('daisy::components.ui.navigation.partials.sidebar-item', [
                        'item' => $child,
                        'depth' => $depth + 1,
                        'sectionId' => $sectionId,
                        'iconPrefix' => $iconPrefix,
                        'fallbackIcon' => $fallbackIcon,
                        'normalizeHref' => $normalizeHref,
                        'isItemActive' => $isItemActive,
                        'hasActiveDescendant' => $hasActiveDescendant,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </details>
    <?php else: ?>
        <a
            href="<?php echo e($href); ?>"
            <?php if($external): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>
            class="flex items-center gap-2 <?php echo e($itemIsActive ? 'menu-active' : ''); ?>"
            title="<?php echo e($label); ?>"
            aria-label="<?php echo e($label); ?>"
            data-sidebar-row
        >
            <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $icon,'prefix' => $iconPrefix,'size' => 'md','class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'size' => 'md','class' => 'shrink-0']); ?>
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
            <span class="min-w-0 flex-1 truncate sidebar-label"><?php echo e($label); ?></span>
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</li>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/partials/sidebar-item.blade.php ENDPATH**/ ?>