<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'file' => null,
    'url' => null,
    'name' => null,
    'type' => null,
    'mimeType' => null,
    'extension' => null,
    'fileSize' => null,
    'thumbnail' => null,
    'downloadable' => true,
    'size' => 'md',
    'openMode' => null,
    'previewUrl' => null,
    'downloadUrl' => null,
    'previewType' => null,
    'previewMode' => 'auto',
    'showPreviewAction' => true,
    'showDownloadAction' => true,
    'downloadFromPreview' => true,
    'showMeta' => true,
    'maxTextPreviewBytes' => 65536,
    'docxPreview' => true,
    'docxView' => 'page',
    'docxZoom' => 100,
    'docxZoomControls' => false,
    'docxNotice' => null,
    'layout' => 'card',
    'actionOnly' => false,
    'actionButtonClass' => null,
    'buttonClass' => null,
    'showCompactPreview' => true,
    'actionOrder' => ['preview', 'download'],
    'modalActionOrder' => ['download', 'close', 'open'],
    'previewButtonVariant' => 'ghost',
    'downloadButtonVariant' => 'ghost',
    'openButtonVariant' => 'ghost',
    'closeButtonVariant' => 'ghost',
    'modalDownloadButtonVariant' => 'primary',
    'modalOpenButtonVariant' => 'ghost',
    'modalCloseButtonVariant' => null,
    'modalTitle' => null,
    'modalSize' => '7xl',
    'modalMaxHeightClass' => 'max-h-[calc(100svh-4rem)]',
    'modalBoxClass' => '',
    'modalContentClass' => '',
    'modalFooter' => true,
    'modalFooterSticky' => true,
    'logPreviewErrors' => false,
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
    'file' => null,
    'url' => null,
    'name' => null,
    'type' => null,
    'mimeType' => null,
    'extension' => null,
    'fileSize' => null,
    'thumbnail' => null,
    'downloadable' => true,
    'size' => 'md',
    'openMode' => null,
    'previewUrl' => null,
    'downloadUrl' => null,
    'previewType' => null,
    'previewMode' => 'auto',
    'showPreviewAction' => true,
    'showDownloadAction' => true,
    'downloadFromPreview' => true,
    'showMeta' => true,
    'maxTextPreviewBytes' => 65536,
    'docxPreview' => true,
    'docxView' => 'page',
    'docxZoom' => 100,
    'docxZoomControls' => false,
    'docxNotice' => null,
    'layout' => 'card',
    'actionOnly' => false,
    'actionButtonClass' => null,
    'buttonClass' => null,
    'showCompactPreview' => true,
    'actionOrder' => ['preview', 'download'],
    'modalActionOrder' => ['download', 'close', 'open'],
    'previewButtonVariant' => 'ghost',
    'downloadButtonVariant' => 'ghost',
    'openButtonVariant' => 'ghost',
    'closeButtonVariant' => 'ghost',
    'modalDownloadButtonVariant' => 'primary',
    'modalOpenButtonVariant' => 'ghost',
    'modalCloseButtonVariant' => null,
    'modalTitle' => null,
    'modalSize' => '7xl',
    'modalMaxHeightClass' => 'max-h-[calc(100svh-4rem)]',
    'modalBoxClass' => '',
    'modalContentClass' => '',
    'modalFooter' => true,
    'modalFooterSticky' => true,
    'logPreviewErrors' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use Art35rennes\DaisyKit\Support\FilePreview;

    $normalizeUrl = function($value) {
        if (!is_string($value) && !$value instanceof \Stringable) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '/') || str_starts_with($value, '#') || str_starts_with($value, 'blob:')) {
            return $value;
        }

        return preg_match('/^https?:\/\//i', $value) === 1 ? $value : null;
    };

    $formatSize = function($value) {
        if (!is_numeric($value)) {
            return $value;
        }

        $bytes = (float) $value;
        $units = ['B', 'KB', 'MB', 'GB'];

        foreach ($units as $unit) {
            if ($bytes < 1024 || $unit === 'GB') {
                return rtrim(rtrim(number_format($bytes, $unit === 'B' ? 0 : 1), '0'), '.').' '.$unit;
            }

            $bytes /= 1024;
        }

        return $value;
    };

    $normalizeActions = function($value, array $fallback) {
        if (is_string($value)) {
            $value = preg_split('/[\s,|]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($value)) {
            return $fallback;
        }

        $allowed = ['preview', 'download', 'open', 'close'];

        return collect($value)
            ->map(fn ($action) => is_string($action) ? trim($action) : null)
            ->filter(fn ($action) => $action && in_array($action, $allowed, true))
            ->values()
            ->all() ?: $fallback;
    };

    $buttonVariantClass = function($variant, string $fallback = 'ghost') {
        $variant = is_string($variant) ? $variant : $fallback;

        return match ($variant) {
            'outline' => 'btn-outline',
            'info' => 'btn-info',
            'ghost' => 'btn-ghost',
            'primary' => 'btn-primary',
            'secondary' => 'btn-secondary',
            'accent' => 'btn-accent',
            'success' => 'btn-success',
            'warning' => 'btn-warning',
            'error' => 'btn-error',
            'neutral' => 'btn-neutral',
            default => 'btn-'.$fallback,
        };
    };

    $fileMetadata = FilePreview::metadata($file);
    $metadata = array_filter([
        'url' => $url,
        'name' => $name,
        'type' => $type,
        'mimeType' => $mimeType,
        'extension' => $extension,
        'size' => $fileSize,
        'previewUrl' => $previewUrl,
        'downloadUrl' => $downloadUrl,
    ], fn ($value) => $value !== null);
    $metadata = array_replace($fileMetadata, $metadata);

    $resolvedUrl = $normalizeUrl($metadata['url'] ?? null);
    $resolvedPreviewUrl = $normalizeUrl($metadata['previewUrl'] ?? null) ?: $resolvedUrl;
    $resolvedDownloadUrl = $normalizeUrl($metadata['downloadUrl'] ?? null) ?: $resolvedUrl;
    $thumbnail = $normalizeUrl($thumbnail);
    $name = $metadata['name'] ?? ($resolvedUrl ? basename(parse_url($resolvedUrl, PHP_URL_PATH) ?: $resolvedUrl) : null);
    $fileSize = $formatSize($metadata['size'] ?? $fileSize);

    $type = FilePreview::type($metadata);
    $previewType = $previewType ?: FilePreview::type([
        'url' => $resolvedPreviewUrl,
        'type' => $previewType,
        'mimeType' => $mimeType,
        'extension' => $extension,
    ]);

    if ($previewType === 'other') {
        $previewType = $type;
    }

    $capabilities = FilePreview::capabilities([
        'url' => $resolvedPreviewUrl,
        'type' => $previewType,
        'mimeType' => $mimeType,
        'extension' => $extension,
    ]);
    $isPreviewable = $showPreviewAction && $resolvedPreviewUrl && $capabilities['isPreviewable'] && ($previewType !== 'docx' || $docxPreview);
    $canDownload = $downloadable && $showDownloadAction && $resolvedDownloadUrl;
    $resolvedDocxView = $docxView === 'fit-width' ? 'fit-width' : 'page';
    $resolvedDocxZoom = min(100, max(10, (int) $docxZoom));
    $resolvedDocxZoomControls = filter_var($docxZoomControls, FILTER_VALIDATE_BOOLEAN);
    $resolvedDocxNotice = is_string($docxNotice) && trim($docxNotice) !== ''
        ? trim($docxNotice)
        : null;

    if ($openMode !== null && $previewMode === 'auto') {
        $previewMode = $openMode === 'blank' ? 'download' : $openMode;
    }

    if ($previewMode === 'auto') {
        $previewMode = in_array($previewType, ['image'], true) ? 'modal' : 'inline';
    }

    if (!in_array($previewMode, ['inline', 'modal', 'download'], true)) {
        $previewMode = 'inline';
    }

    $sizeMap = [
        'xs' => ['container' => 'max-w-32', 'media' => 'max-h-24', 'frame' => 'h-48', 'icon' => 'w-6 h-6'],
        'sm' => ['container' => 'max-w-48', 'media' => 'max-h-32', 'frame' => 'h-64', 'icon' => 'w-8 h-8'],
        'md' => ['container' => 'max-w-64', 'media' => 'max-h-48', 'frame' => 'h-80', 'icon' => 'w-10 h-10'],
        'lg' => ['container' => 'max-w-96', 'media' => 'max-h-64', 'frame' => 'h-[28rem]', 'icon' => 'w-12 h-12'],
        'xl' => ['container' => 'max-w-[32rem]', 'media' => 'max-h-96', 'frame' => 'h-[36rem]', 'icon' => 'w-16 h-16'],
    ];
    $sizes = $sizeMap[$size] ?? $sizeMap['md'];
    $modalSizeMap = [
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-[calc(100vw-2rem)]',
    ];
    $modalSizeClass = $modalSizeMap[$modalSize] ?? $modalSizeMap['7xl'];
    $modalMaxHeightClass = is_string($modalMaxHeightClass) && trim($modalMaxHeightClass) !== '' ? trim($modalMaxHeightClass) : 'max-h-[calc(100svh-4rem)]';
    $modalBoxClass = is_string($modalBoxClass) ? $modalBoxClass : '';
    $actionOrder = $normalizeActions($actionOrder, ['preview', 'download']);
    $modalActionOrder = $normalizeActions($modalActionOrder, ['download', 'close', 'open']);
    $previewButtonClass = $buttonVariantClass($previewButtonVariant);
    $downloadButtonClass = $buttonVariantClass($downloadButtonVariant);
    $openButtonClass = $buttonVariantClass($openButtonVariant);
    $closeButtonClass = $buttonVariantClass($closeButtonVariant);
    $modalDownloadButtonClass = $buttonVariantClass($modalDownloadButtonVariant, 'primary');
    $modalOpenButtonClass = $buttonVariantClass($modalOpenButtonVariant);
    $modalCloseButtonClass = $buttonVariantClass($modalCloseButtonVariant ?? $closeButtonVariant);
    $isCompactList = $layout === 'compact-list';
    $isActionOnly = filter_var($actionOnly, FILTER_VALIDATE_BOOLEAN)
        || in_array($layout, ['action', 'action-only', 'standalone-action'], true);
    $previewActionButtonClass = is_string($actionButtonClass) && trim($actionButtonClass) !== ''
        ? trim($actionButtonClass)
        : (is_string($buttonClass) && trim($buttonClass) !== '' ? trim($buttonClass) : '');
    $standaloneActionButtonClass = $previewActionButtonClass !== ''
        ? $previewActionButtonClass
        : 'btn-ghost btn-xs btn-circle h-8 min-h-8 w-8';

    $icons = [
        'image' => 'bi-image',
        'video' => 'bi-play-circle',
        'audio' => 'bi-music-note-beamed',
        'pdf' => 'bi-file-pdf',
        'text' => 'bi-file-text',
        'docx' => 'bi-file-word',
        'document' => 'bi-file-text',
        'spreadsheet' => 'bi-file-spreadsheet',
        'presentation' => 'bi-file-slides',
        'archive' => 'bi-file-zip',
        'other' => 'bi-file-earmark',
    ];
    $icon = $icons[$type] ?? $icons['other'];
    $modalId = $previewMode === 'modal' && $isPreviewable ? 'file-preview-modal-'.\Illuminate\Support\Str::uuid() : null;

    $downloadLabel = __('daisy::common.download');
    $closeLabel = __('daisy::common.close');
    $previewLabel = __('daisy::components.file_preview.preview');
    $openLabel = __('daisy::components.file_preview.open');
    $loadingLabel = __('daisy::common.loading');
    $fallbackLabel = __('daisy::components.file_preview.preview_unavailable');
    $docxZoomToolbarLabel = __('daisy::components.file_preview.zoom_toolbar');
    $docxFitWidthLabel = __('daisy::components.file_preview.fit_width');
    $docxZoomOutLabel = __('daisy::components.file_preview.zoom_out');
    $docxZoomInLabel = __('daisy::components.file_preview.zoom_in');
    $modalTitleText = $modalTitle ?? $name ?? $previewLabel;
?>

<?php if (! $__env->hasRenderedOnce('cb64b8b9-6919-4dec-b45e-6c6a221e70e5')): $__env->markAsRenderedOnce('cb64b8b9-6919-4dec-b45e-6c6a221e70e5'); ?>
    <?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<<?php echo e($isActionOnly ? 'span' : 'div'); ?>

    <?php echo e($attributes->merge(['class' => $isActionOnly ? 'file-preview inline-flex items-center' : 'file-preview inline-block w-full '.($isCompactList ? '' : $sizes['container']), 'data-module' => 'file-preview'])); ?>

    data-file-preview-type="<?php echo e($previewType); ?>"
    data-log-preview-errors="<?php echo e($logPreviewErrors ? 'true' : 'false'); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActionOnly): ?>
        <?php echo $__env->make('daisy::partials.file-preview-actions', [
            'buttonSize' => '',
            'buttonExtraClass' => '',
            'previewActionButtonClass' => $standaloneActionButtonClass,
            'actions' => ['preview'],
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($isPreviewable && $previewMode === 'inline'): ?>
        <div class="overflow-hidden rounded-box card-border bg-base-100">
            <div class="flex items-center justify-between gap-3 border-b border-base-300/60 bg-base-200/70 px-3 py-2">
                <div class="min-w-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name): ?>
                        <p class="truncate text-sm font-medium"><?php echo e($name); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showMeta && $fileSize): ?>
                        <p class="text-xs text-base-content/70"><?php echo e($fileSize); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="flex shrink-0 items-center gap-1">
                    <?php echo $__env->make('daisy::partials.file-preview-actions', ['buttonSize' => 'btn-xs', 'actions' => $actionOrder], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <div class="bg-base-100">
                <?php echo $__env->make('daisy::partials.file-preview-body', ['previewContext' => 'inline'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    <?php elseif($isCompactList): ?>
        <div class="flex min-w-0 items-center gap-2 rounded-box px-2 py-1.5 transition-colors hover:bg-base-200">
            <div class="min-w-0 flex-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name): ?>
                    <p class="truncate text-sm font-medium"><?php echo e($name); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showMeta && $fileSize): ?>
                    <p class="truncate text-xs text-base-content/70"><?php echo e($fileSize); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="flex shrink-0 items-center gap-1">
                <?php echo $__env->make('daisy::partials.file-preview-actions', ['buttonSize' => 'btn-xs', 'actions' => $actionOrder], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    <?php elseif(! $showCompactPreview && $isPreviewable): ?>
        <div class="flex w-full">
            <?php echo $__env->make('daisy::partials.file-preview-actions', ['buttonSize' => 'btn-md', 'buttonExtraClass' => 'btn-block', 'actions' => ['preview'], 'labelled' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    <?php else: ?>
        <div class="rounded-box card-border bg-base-200 p-3 transition-colors hover:bg-base-300">
            <div class="flex items-center gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thumbnail): ?>
                    <img src="<?php echo e($thumbnail); ?>" alt="" class="<?php echo e($sizes['icon']); ?> rounded object-cover" loading="lazy" />
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => ''.e($icon).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => ''.e($sizes['icon']).' shrink-0 text-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="min-w-0 flex-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name): ?>
                        <p class="truncate text-sm font-medium"><?php echo e($name); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showMeta && $fileSize): ?>
                        <p class="text-xs text-base-content/70"><?php echo e($fileSize); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="flex shrink-0 items-center gap-1">
                    <?php echo $__env->make('daisy::partials.file-preview-actions', ['buttonSize' => 'btn-xs', 'actions' => $actionOrder], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modalId): ?>
        <dialog id="<?php echo e($modalId); ?>" class="modal" aria-labelledby="<?php echo e($modalId); ?>-title">
            <div class="modal-box flex w-11/12 <?php echo e($modalSizeClass); ?> <?php echo e($modalMaxHeightClass); ?> <?php echo e($modalBoxClass); ?> flex-col overflow-hidden p-0">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-3 border-b border-base-300/60 bg-base-100 px-4 py-3">
                    <h3 id="<?php echo e($modalId); ?>-title" class="min-w-0 truncate text-base font-semibold"><?php echo e($modalTitleText); ?></h3>
                    <form method="dialog" class="shrink-0">
                        <button type="submit" class="btn btn-sm btn-circle <?php echo e($modalCloseButtonClass); ?>" aria-label="<?php echo e($closeLabel); ?>" title="<?php echo e($closeLabel); ?>">
                            <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-x'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                        </button>
                    </form>
                </div>

                <div class="min-h-0 flex-1 overflow-x-auto overflow-y-auto bg-base-100 <?php echo e($modalContentClass); ?>">
                    <?php echo $__env->make('daisy::partials.file-preview-body', ['previewContext' => 'modal'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modalFooter): ?>
                    <div class="<?php echo e($modalFooterSticky ? 'sticky bottom-0 z-10' : ''); ?> flex flex-wrap justify-end gap-2 border-t border-base-300/60 bg-base-100 px-4 py-3">
                        <?php echo $__env->make('daisy::partials.file-preview-actions', ['buttonSize' => 'btn-sm', 'actions' => $modalActionOrder, 'labelled' => true, 'modalActions' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button><?php echo e($closeLabel); ?></button>
            </form>
        </dialog>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</<?php echo e($isActionOnly ? 'span' : 'div'); ?>>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/data-display/file-preview.blade.php ENDPATH**/ ?>