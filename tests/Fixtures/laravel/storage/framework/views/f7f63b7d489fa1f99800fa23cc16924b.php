<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => null,
    'position' => 'top-end', // top|middle|bottom + start|center|end
    'color' => 'primary', // for badge shortcut
    // Rendu de l'indicateur: badge (par défaut) | status
    'type' => 'badge',
    'statusColor' => 'success',
    // Classes additionnelles pour l'item (permet responsive: sm:indicator-middle ...)
    'itemClass' => null,
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
    'label' => null,
    'position' => 'top-end', // top|middle|bottom + start|center|end
    'color' => 'primary', // for badge shortcut
    // Rendu de l'indicateur: badge (par défaut) | status
    'type' => 'badge',
    'statusColor' => 'success',
    // Classes additionnelles pour l'item (permet responsive: sm:indicator-middle ...)
    'itemClass' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'indicator';
    $posMap = [
        'top-start' => 'indicator-top indicator-start',
        'top-center' => 'indicator-top indicator-center',
        'top-end' => 'indicator-top indicator-end',
        'middle-start' => 'indicator-middle indicator-start',
        'middle-center' => 'indicator-middle indicator-center',
        'middle-end' => 'indicator-middle indicator-end',
        'bottom-start' => 'indicator-bottom indicator-start',
        'bottom-center' => 'indicator-bottom indicator-center',
        'bottom-end' => 'indicator-bottom indicator-end',
    ];
    $indicatorPos = $posMap[$position] ?? $posMap['top-end'];
?>

<div <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <span class="indicator-item <?php echo e($indicatorPos); ?> <?php echo e($itemClass); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($indicator)): ?>
            <?php echo e($indicator); ?>

        <?php else: ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'status'): ?>
                <span class="status status-<?php echo e($statusColor); ?>"></span>
            <?php else: ?>
                <span class="badge badge-<?php echo e($color); ?>"><?php echo e($label); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
    <?php echo e($slot); ?>

</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/utilities/indicator.blade.php ENDPATH**/ ?>