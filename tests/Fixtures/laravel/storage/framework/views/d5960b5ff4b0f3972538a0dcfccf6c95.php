<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => [],
    'sortable' => false,
    'handle' => true,
    'name' => null,
    'persist' => false,
    'disabled' => false,
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
    'sortable' => false,
    'handle' => true,
    'name' => null,
    'persist' => false,
    'disabled' => false,
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
    $rootId = $attributes->get('id') ?: ('ordered-list-'.uniqid());
    $resolvedSortable = (bool) $sortable && ! (bool) $disabled;
    $resolvedPersist = (bool) $persist && filled($name);
    $renderedItems = is_array($items) ? array_values($items) : [];
    $hasSlotContent = isset($slot) && trim((string) $slot) !== '';
    $rootClasses = trim('list list-decimal daisy-ordered-list '.($attributes->get('class') ?? ''));
    $attributes = $attributes->except('class');
?>

<ol
    <?php echo e($attributes->merge(['id' => $rootId, 'class' => $rootClasses])); ?>

    data-module="<?php echo e($module ?? 'ordered-list'); ?>"
    data-ordered-list="1"
    data-sortable="<?php echo e($resolvedSortable ? 'true' : 'false'); ?>"
    data-handle="<?php echo e($handle ? 'true' : 'false'); ?>"
    data-disabled="<?php echo e($disabled ? 'true' : 'false'); ?>"
    data-persist="<?php echo e($resolvedPersist ? 'true' : 'false'); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSlotContent): ?>
        <?php echo e($slot); ?>

    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $renderedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $itemId = filled($item['id'] ?? null) ? (string) $item['id'] : 'ordered-item-'.$index;
                $itemDisabled = (bool) ($item['disabled'] ?? false);
                $itemLabel = $item['label'] ?? $item['title'] ?? $itemId;
                $itemContent = $item['content'] ?? null;
            ?>
            <li
                class="list-row daisy-ordered-list-item<?php echo e($itemDisabled ? ' opacity-60' : ''); ?>"
                data-ordered-list-item
                data-id="<?php echo e($itemId); ?>"
                data-disabled="<?php echo e($itemDisabled ? 'true' : 'false'); ?>"
            >
                <div class="flex items-start gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($handle && $resolvedSortable): ?>
                        <span
                            class="btn btn-ghost btn-xs btn-square mt-0.5 cursor-grab select-none daisy-drag-handle"
                            data-ordered-list-handle
                            aria-hidden="true"
                        >
                            <span aria-hidden="true">⋮⋮</span>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="min-w-0 flex-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($itemLabel)): ?>
                            <div class="font-medium"><?php echo e($itemLabel); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemContent instanceof \Illuminate\Contracts\Support\Htmlable): ?>
                            <?php echo $itemContent->toHtml(); ?>

                        <?php elseif($itemContent instanceof \Illuminate\Support\HtmlString): ?>
                            <?php echo $itemContent; ?>

                        <?php elseif(filled($itemContent)): ?>
                            <div class="text-sm text-base-content/70"><?php echo e($itemContent); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </li>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</ol>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedPersist): ?>
    <input type="hidden" name="<?php echo e($name); ?>" value="" data-ordered-list-input-for="<?php echo e($rootId); ?>">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/ordered-list.blade.php ENDPATH**/ ?>