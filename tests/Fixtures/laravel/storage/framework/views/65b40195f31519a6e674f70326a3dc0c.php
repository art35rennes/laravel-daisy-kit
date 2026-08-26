<?php
    $id = is_array($node) && array_key_exists('id', $node) ? (string) $node['id'] : '';
    $label = is_array($node) ? (string) ($node['label'] ?? $id) : '';
    $children = is_array($node) && is_array($node['children'] ?? null) ? $node['children'] : [];
    $isLazy = is_array($node) && ($node['lazy'] ?? false) === true;
    $hasChildren = $isLazy || count($children) > 0;
    $expanded = $hasChildren && ! $isLazy && (bool) ($node['expanded'] ?? false);
    $nodeDisabled = (bool) ($disabledParent ?? false) || (bool) ($node['disabled'] ?? false);
    $selectedSet = array_fill_keys($selectedValues, true);

    $leafIds = function (array $items) use (&$leafIds): array {
        $ids = [];

        foreach ($items as $item) {
            if (! is_array($item) || ! array_key_exists('id', $item)) {
                continue;
            }

            $itemChildren = is_array($item['children'] ?? null) ? $item['children'] : [];

            if (($item['lazy'] ?? false) === true || count($itemChildren) === 0) {
                $ids[] = (string) $item['id'];
            } else {
                $ids = [...$ids, ...$leafIds($itemChildren)];
            }
        }

        return $ids;
    };

    $descendantLeafIds = $hasChildren && ! $isLazy ? $leafIds($children) : [];
    $isDirectlySelected = isset($selectedSet[$id]);
    $selectedLeafCount = count(array_filter($descendantLeafIds, fn ($leafId) => isset($selectedSet[$leafId])));
    $isSelected = $selection === 'single'
        ? $isDirectlySelected
        : ($isDirectlySelected || ($hasChildren && ! $isLazy && count($descendantLeafIds) > 0 && $selectedLeafCount === count($descendantLeafIds)));
    $isMixed = $selection === 'multiple'
        && ! $isSelected
        && ! $isLazy
        && count($descendantLeafIds) > 0
        && $selectedLeafCount > 0
        && $selectedLeafCount < count($descendantLeafIds);
    $indentLevel = min(64, max(0, $level - 1));
    $domId = $treeId.'-item-'.substr(hash('sha256', $id.'-'.$level), 0, 16);
    $state = $isMixed ? 'mixed' : ($isSelected ? 'true' : 'false');
?>

<li id="<?php echo e($domId); ?>" role="treeitem" aria-level="<?php echo e($level); ?>" <?php if($hasChildren): ?> aria-expanded="<?php echo e($expanded ? 'true' : 'false'); ?>" <?php endif; ?> <?php if($selection === 'multiple'): ?> aria-checked="<?php echo e($state); ?>" <?php else: ?> aria-selected="<?php echo e($isSelected ? 'true' : 'false'); ?>" <?php endif; ?> data-id="<?php echo e($id); ?>" data-level="<?php echo e($level); ?>" <?php if($isLazy): ?> data-lazy="1" <?php endif; ?> <?php if($nodeDisabled): ?> aria-disabled="true" <?php endif; ?> tabindex="-1" class="outline-none focus-visible:ring-2 focus-visible:ring-primary">
    <div class="flex items-center gap-2 rounded px-2 py-1 hover:bg-base-200 daisy-tree-indent-<?php echo e($indentLevel); ?>" data-node-header="1">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
            <button type="button" class="btn btn-ghost btn-xs btn-square shrink-0" aria-label="<?php echo e($expanded ? __('daisy::components.tree-view-collapse', ['label' => $label]) : __('daisy::components.tree-view-expand', ['label' => $label])); ?>" data-tree-toggle="1" tabindex="-1">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-chevron-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 '.e($expanded ? 'hidden' : '').'','data-tree-collapsed-icon' => '1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 '.e($expanded ? '' : 'hidden').'','data-tree-expanded-icon' => '1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            </button>
        <?php else: ?>
            <span class="inline-block w-6 shrink-0" aria-hidden="true"></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selection === 'single'): ?>
            <?php if (isset($component)) { $__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.radio','data' => ['name' => $name,'value' => $id,'checked' => $isSelected,'disabled' => $nodeDisabled,'size' => $controlSize,'class' => 'shrink-0','tabindex' => '-1','dataTreeControl' => '1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($id),'checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSelected),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nodeDisabled),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($controlSize),'class' => 'shrink-0','tabindex' => '-1','data-tree-control' => '1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1)): ?>
<?php $attributes = $__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1; ?>
<?php unset($__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1)): ?>
<?php $component = $__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1; ?>
<?php unset($__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1); ?>
<?php endif; ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.checkbox','data' => ['name' => !$hasChildren && $name ? $name.'[]' : null,'value' => $id,'checked' => $isSelected,'indeterminate' => $isMixed,'disabled' => $nodeDisabled,'bindOld' => false,'size' => $controlSize,'class' => 'shrink-0','tabindex' => '-1','dataTreeControl' => '1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$hasChildren && $name ? $name.'[]' : null),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($id),'checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSelected),'indeterminate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isMixed),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nodeDisabled),'bind-old' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($controlSize),'class' => 'shrink-0','tabindex' => '-1','data-tree-control' => '1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $attributes = $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $component = $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <span class="min-w-0 flex-1 select-none break-words <?php echo e($nodeDisabled ? 'opacity-50' : 'cursor-default'); ?>" data-tree-label="1"><?php echo e($label); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
        <ul role="group" class="ml-4 border-l pl-2 <?php echo e($expanded ? '' : 'hidden'); ?>" data-tree-group="1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLazy): ?>
                <li role="presentation" class="hidden px-2 py-1 text-sm opacity-60" data-tree-lazy-placeholder="1"></li>
            <?php else: ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php echo $__env->make('daisy::components.ui.partials.tree-node', [
                        'node' => $child,
                        'level' => $level + 1,
                        'treeId' => $treeId,
                        'selection' => $selection,
                        'valueMode' => $valueMode,
                        'selectedValues' => $selectedValues,
                        'name' => $name,
                        'controlSize' => $controlSize,
                        'disabledParent' => $nodeDisabled,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</li>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/partials/tree-node.blade.php ENDPATH**/ ?>