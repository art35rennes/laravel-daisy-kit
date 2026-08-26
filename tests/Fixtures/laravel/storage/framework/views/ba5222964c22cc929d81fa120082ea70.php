<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => null,
    'type' => null,
    'x' => 0,
    'y' => 0,
    'w' => 3,
    'h' => 2,
    'meta' => null,
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
    'id' => null,
    'type' => null,
    'x' => 0,
    'y' => 0,
    'w' => 3,
    'h' => 2,
    'meta' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $resolvedId = filled($id) ? (string) $id : null;
    $resolvedType = filled($type) ? (string) $type : null;
    $resolvedX = max(0, (int) $x);
    $resolvedY = max(0, (int) $y);
    $resolvedW = max(1, (int) $w);
    $resolvedH = max(1, (int) $h);
    $encodedMeta = $meta === null ? null : json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $rootClasses = trim('grid-stack-item daisy-editable-grid-item '.($attributes->get('class') ?? ''));
    $attributes = $attributes->except('class');
?>

<div
    <?php echo e($attributes->merge(['class' => $rootClasses])); ?>

    <?php if($resolvedId): ?> gs-id="<?php echo e($resolvedId); ?>" <?php endif; ?>
    <?php if($resolvedType): ?> data-type="<?php echo e($resolvedType); ?>" <?php endif; ?>
    <?php if($encodedMeta): ?> data-meta='<?php echo e($encodedMeta); ?>' <?php endif; ?>
    gs-x="<?php echo e($resolvedX); ?>"
    gs-y="<?php echo e($resolvedY); ?>"
    gs-w="<?php echo e($resolvedW); ?>"
    gs-h="<?php echo e($resolvedH); ?>"
>
    <div class="grid-stack-item-content daisy-editable-grid-item-content">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/editable-grid-item.blade.php ENDPATH**/ ?>