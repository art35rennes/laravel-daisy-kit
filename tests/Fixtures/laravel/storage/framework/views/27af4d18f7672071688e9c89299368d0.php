<?php if (! $__env->hasRenderedOnce('ac176185-37e8-46da-8408-292ed404b63b')): $__env->markAsRenderedOnce('ac176185-37e8-46da-8408-292ed404b63b');
$__env->startPush('styles'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('daisy-kit.auto_assets')): ?>
        <?php
            $assetManager = \Art35rennes\DaisyKit\Support\PackageAsset::class;
            $cssEntry = $assetManager::sourceEntry('css');
            $hasManifest = $assetManager::hasManifest();
            $hasPublishedSource = $assetManager::hasPublishedSource('css');
            $buildDirectory = $assetManager::buildDirectory();
            $cspNonce = config('daisy-kit.csp_nonce');
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('daisy-kit.use_vite')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasManifest): ?>
                <?php echo app('Illuminate\Foundation\Vite')($cssEntry, $buildDirectory); ?>
            <?php elseif($hasPublishedSource): ?>
                <?php echo app('Illuminate\Foundation\Vite')($cssEntry); ?>
            <?php else: ?>
                <?php echo $assetManager::stylesheetTags($cssEntry, $cspNonce); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <?php echo $assetManager::stylesheetTags($cssEntry, $cspNonce); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopPush(); endif; ?>

<?php if (! $__env->hasRenderedOnce('de13e60e-8516-4dfe-b6d4-ab4045048068')): $__env->markAsRenderedOnce('de13e60e-8516-4dfe-b6d4-ab4045048068');
$__env->startPush('scripts'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('daisy-kit.auto_assets')): ?>
        <?php
            $assetManager = \Art35rennes\DaisyKit\Support\PackageAsset::class;
            $jsEntry = $assetManager::sourceEntry('js');
            $hasManifest = $assetManager::hasManifest();
            $hasPublishedSource = $assetManager::hasPublishedSource('js');
            $buildDirectory = $assetManager::buildDirectory();
            $cspNonce = config('daisy-kit.csp_nonce');
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('daisy-kit.use_vite')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasManifest): ?>
                <?php echo app('Illuminate\Foundation\Vite')($jsEntry, $buildDirectory); ?>
            <?php elseif($hasPublishedSource): ?>
                <?php echo app('Illuminate\Foundation\Vite')($jsEntry); ?>
            <?php else: ?>
                <?php echo $assetManager::scriptTags($jsEntry, $cspNonce); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <?php echo $assetManager::scriptTags($jsEntry, $cspNonce); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/partials/assets.blade.php ENDPATH**/ ?>