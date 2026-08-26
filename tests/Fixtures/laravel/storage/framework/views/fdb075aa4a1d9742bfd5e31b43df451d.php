<?php
    use Art35rennes\DaisyKit\Helpers\ThemeHelper;
    use Art35rennes\DaisyKit\Support\PackageAsset;

    $customThemesCss = ThemeHelper::generateCustomThemesCss();
    $shouldRenderInlineCustomCss = (bool) config('daisy-kit.themes.inline_custom_css', false);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shouldRenderInlineCustomCss && $customThemesCss): ?>
    <?php if (! $__env->hasRenderedOnce('f70ceadf-a1d6-427e-99eb-698afb988454')): $__env->markAsRenderedOnce('f70ceadf-a1d6-427e-99eb-698afb988454');
$__env->startPush('styles'); ?>
        <style<?php echo PackageAsset::nonceAttribute(); ?>>
            <?php echo $customThemesCss; ?>

        </style>
    <?php $__env->stopPush(); endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/partials/custom-themes.blade.php ENDPATH**/ ?>