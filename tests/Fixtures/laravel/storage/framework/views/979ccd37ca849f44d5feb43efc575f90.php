<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'container' => true,
    'theme' => null,
    'htmlClass' => null,
    'bodyClass' => null,
    'fontUrl' => 'https://fonts.bunny.net/css?family=instrument-sans:400,500,600',
    'loadDefaultFont' => true,
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
    'title' => null,
    'container' => true,
    'theme' => null,
    'htmlClass' => null,
    'bodyClass' => null,
    'fontUrl' => 'https://fonts.bunny.net/css?family=instrument-sans:400,500,600',
    'loadDefaultFont' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $resolvedTheme = $theme === null
        ? \Art35rennes\DaisyKit\Helpers\ThemeHelper::getDefaultTheme()
        : $theme;

    $resolvedTheme = is_string($resolvedTheme) || $resolvedTheme instanceof \Stringable
        ? trim((string) $resolvedTheme)
        : null;

    $normalizeStylesheetUrl = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
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

    $fontUrl = $normalizeStylesheetUrl($fontUrl);
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" <?php if($resolvedTheme !== null && $resolvedTheme !== ''): ?> data-theme="<?php echo e($resolvedTheme); ?>" <?php endif; ?> <?php if($htmlClass): ?> class="<?php echo e($htmlClass); ?>" <?php endif; ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ? $title.' | ' : ''); ?><?php echo e(config('app.name', 'Laravel')); ?></title>
    
    <?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('daisy::components.partials.custom-themes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loadDefaultFont && $fontUrl): ?>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="<?php echo e($fontUrl); ?>" rel="stylesheet" />
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo e($head ?? ''); ?>

    
</head>
<body class="<?php echo e(trim('bg-base-100 text-base-content min-h-screen overflow-x-hidden '.$bodyClass)); ?>">
    <div class="<?php echo e($container ? 'container mx-auto px-4 sm:px-6 py-4 sm:py-6' : ''); ?>">
        <?php echo e($slot); ?>

    </div>
    <?php echo e($scripts ?? ''); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/layout/app.blade.php ENDPATH**/ ?>