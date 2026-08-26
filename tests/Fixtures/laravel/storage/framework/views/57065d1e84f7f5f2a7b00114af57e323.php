<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'backgroundImage' => null,
    'backgroundClass' => null,
    'overlay' => null,
    'overlayClass' => 'bg-black/50',
    'padding' => 'px-4 py-10 sm:px-6',
    'cardClass' => 'w-full max-w-md space-y-6 rounded-box border border-base-300/70 bg-base-100/90 p-6 shadow-xl backdrop-blur',
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
    'backgroundImage' => null,
    'backgroundClass' => null,
    'overlay' => null,
    'overlayClass' => 'bg-black/50',
    'padding' => 'px-4 py-10 sm:px-6',
    'cardClass' => 'w-full max-w-md space-y-6 rounded-box border border-base-300/70 bg-base-100/90 p-6 shadow-xl backdrop-blur',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $normalizeImageUrl = function ($url) {
        if (! is_string($url) && ! $url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $backgroundImage = $normalizeImageUrl($backgroundImage);
    $backgroundClass = is_string($backgroundClass) ? trim($backgroundClass) : null;
    $hasBackground = $backgroundImage !== null || ($backgroundClass !== null && $backgroundClass !== '');
    $showOverlay = $overlay ?? $hasBackground;
?>

<section <?php echo e($attributes->class(['relative min-h-screen min-h-dvh w-full overflow-hidden bg-base-200', $padding])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBackground): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($backgroundImage): ?>
            <img src="<?php echo e($backgroundImage); ?>" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover <?php echo e($backgroundClass); ?>">
        <?php else: ?>
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat <?php echo e($backgroundClass); ?>"></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showOverlay): ?>
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 <?php echo e($overlayClass); ?>"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="relative z-10 flex min-h-[calc(100dvh-5rem)] min-h-[calc(100vh-5rem)] items-center justify-center">
        <div class="<?php echo e($cardClass); ?>">
            <?php echo e($slot); ?>

        </div>
    </div>
</section>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/layout/auth-shell.blade.php ENDPATH**/ ?>