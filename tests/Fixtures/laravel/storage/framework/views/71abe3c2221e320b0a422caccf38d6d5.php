<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'src' => null,
    'alt' => '',
    'size' => 'md', // xs|sm|md|lg|xl|xxl
    'rounded' => 'full', // none|sm|md|lg|xl|full
    'placeholder' => null,
    // statut de présence: online | offline | null
    'status' => null,
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
    'src' => null,
    'alt' => '',
    'size' => 'md', // xs|sm|md|lg|xl|xxl
    'rounded' => 'full', // none|sm|md|lg|xl|full
    'placeholder' => null,
    // statut de présence: online | offline | null
    'status' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // DaisyUI exemples utilisent des largeurs w-*
    $sizeMap = [
        'xs' => 'w-8',
        'sm' => 'w-12',
        'md' => 'w-16',
        'lg' => 'w-20',
        'xl' => 'w-24',
        'xxl' => 'w-32',
    ];
    $roundedMap = [
        'none' => 'rounded-none',
        'sm' => 'rounded',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
    ];
    $placeholderTextSize = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-2xl',
        'xxl' => 'text-3xl',
    ][$size] ?? 'text-base';

    $wrapper = 'avatar';
    if ($status === 'online') $wrapper .= ' avatar-online';
    if ($status === 'offline') $wrapper .= ' avatar-offline';
    if (!$src && !is_null($placeholder)) $wrapper .= ' avatar-placeholder';

    $containerClass = ($sizeMap[$size] ?? 'w-16').' '.($roundedMap[$rounded] ?? 'rounded-full');
?>

<div <?php echo e($attributes->merge(['class' => $wrapper])); ?>>
    <div class="<?php echo e($containerClass); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($src): ?>
            <img src="<?php echo e($src); ?>" alt="<?php echo e($alt); ?>" />
        <?php else: ?>
            <div class="bg-neutral text-neutral-content w-full h-full grid place-items-center <?php echo e($roundedMap[$rounded] ?? 'rounded-full'); ?> <?php echo e($placeholderTextSize); ?>">
                <span><?php echo e($placeholder ?? 'A'); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
  </div>


<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/data-display/avatar.blade.php ENDPATH**/ ?>