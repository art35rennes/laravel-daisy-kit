<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previewType === 'image'): ?>
    <div class="relative min-h-64" data-file-preview-loadable-container>
        <div class="absolute inset-0 space-y-3 p-4" data-file-preview-skeleton aria-label="<?php echo e($loadingLabel); ?>">
            <div class="skeleton h-full min-h-56 w-full"></div>
        </div>
        <img src="<?php echo e($resolvedPreviewUrl); ?>" alt="<?php echo e($name ?? 'Image'); ?>" class="w-full h-auto opacity-0 <?php echo e($previewContext === 'modal' ? 'max-h-[calc(100svh-10rem)]' : $sizes['media']); ?> object-contain" loading="lazy" data-file-preview-loadable />
    </div>
<?php elseif($previewType === 'video'): ?>
    <div class="relative min-h-64" data-file-preview-loadable-container>
        <div class="absolute inset-0 p-4" data-file-preview-skeleton aria-label="<?php echo e($loadingLabel); ?>"><div class="skeleton h-full min-h-56 w-full"></div></div>
        <video src="<?php echo e($resolvedPreviewUrl); ?>" controls class="w-full opacity-0 <?php echo e($previewContext === 'modal' ? 'max-h-[calc(100svh-10rem)]' : $sizes['media']); ?> object-contain" data-file-preview-loadable>
            <?php echo e(__('daisy::components.file_preview.video_unsupported')); ?>

        </video>
    </div>
<?php elseif($previewType === 'audio'): ?>
    <div class="relative flex min-h-32 items-center justify-center bg-base-200 p-4" data-file-preview-loadable-container>
        <div class="absolute inset-4" data-file-preview-skeleton aria-label="<?php echo e($loadingLabel); ?>"><div class="skeleton h-full w-full"></div></div>
        <audio src="<?php echo e($resolvedPreviewUrl); ?>" controls class="w-full opacity-0" data-file-preview-loadable>
            <?php echo e(__('daisy::components.file_preview.audio_unsupported')); ?>

        </audio>
    </div>
<?php elseif($previewType === 'pdf'): ?>
    <div class="relative min-h-64" data-file-preview-loadable-container>
        <div class="absolute inset-0 space-y-3 p-4" data-file-preview-skeleton aria-label="<?php echo e($loadingLabel); ?>">
            <div class="skeleton h-6 w-2/3"></div>
            <div class="skeleton h-4 w-full"></div>
            <div class="skeleton h-4 w-5/6"></div>
            <div class="skeleton h-48 w-full"></div>
        </div>
        <object data="<?php echo e($resolvedPreviewUrl); ?>" type="application/pdf" class="w-full opacity-0 <?php echo e($previewContext === 'modal' ? 'h-[calc(100svh-12rem)]' : $sizes['frame']); ?>" data-file-preview-loadable>
            <iframe src="<?php echo e($resolvedPreviewUrl); ?>" class="w-full <?php echo e($previewContext === 'modal' ? 'h-[calc(100svh-12rem)]' : $sizes['frame']); ?>" title="<?php echo e($name ?? 'PDF preview'); ?>"></iframe>
        </object>
    </div>
<?php elseif($previewType === 'text'): ?>
    <pre
        class="max-h-96 overflow-auto whitespace-pre-wrap break-words bg-base-200 p-3 text-xs"
        data-file-preview-text
        data-url="<?php echo e($resolvedPreviewUrl); ?>"
        data-max-bytes="<?php echo e((int) $maxTextPreviewBytes); ?>"
        data-loading-label="<?php echo e($loadingLabel); ?>"
        data-error-label="<?php echo e($fallbackLabel); ?>"
    ><?php echo e($loadingLabel); ?></pre>
<?php elseif($previewType === 'docx'): ?>
    <div class="min-w-0" data-file-preview-docx-viewer>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedDocxNotice): ?>
            <p
                class="flex items-start gap-2 border-b border-base-300/60 bg-base-200/70 px-3 py-2 text-xs text-base-content/60"
                role="note"
                data-file-preview-docx-notice
            >
                <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-info-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-0.5 h-3.5 w-3.5 shrink-0']); ?>
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
                <span><?php echo e($resolvedDocxNotice); ?></span>
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedDocxZoomControls): ?>
            <div
                class="flex flex-wrap items-center justify-end gap-1 border-b border-base-300/60
                    bg-base-200/70 px-2 py-1.5"
                data-file-preview-docx-controls
                role="toolbar"
                aria-label="<?php echo e($docxZoomToolbarLabel); ?>"
            >
                <button
                    type="button"
                    class="btn btn-xs btn-square btn-ghost"
                    data-file-preview-docx-zoom-action="out"
                    aria-label="<?php echo e($docxZoomOutLabel); ?>"
                    title="<?php echo e($docxZoomOutLabel); ?>"
                >&minus;</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    'fit-width' => $docxFitWidthLabel,
                    '50' => '50 %',
                    '75' => '75 %',
                    '100' => '100 %',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoomValue => $zoomLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button
                        type="button"
                        class="btn btn-xs btn-ghost"
                        data-file-preview-docx-zoom="<?php echo e($zoomValue); ?>"
                        aria-pressed="false"
                    ><?php echo e($zoomLabel); ?></button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <button
                    type="button"
                    class="btn btn-xs btn-square btn-ghost"
                    data-file-preview-docx-zoom-action="in"
                    aria-label="<?php echo e($docxZoomInLabel); ?>"
                    title="<?php echo e($docxZoomInLabel); ?>"
                >+</button>
                <span class="sr-only" data-file-preview-docx-zoom-status aria-live="polite"></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'daisy-docx-preview-viewport min-h-64 overflow-x-auto overflow-y-auto bg-base-100 p-3',
                'max-h-[calc(100svh-12rem)]' => $previewContext === 'modal',
                'max-h-96' => $previewContext !== 'modal',
            ]); ?>"
            data-file-preview-docx
            data-url="<?php echo e($resolvedPreviewUrl); ?>"
            data-loading-label="<?php echo e($loadingLabel); ?>"
            data-error-label="<?php echo e($fallbackLabel); ?>"
            data-docx-view="<?php echo e($resolvedDocxView); ?>"
            data-docx-zoom="<?php echo e($resolvedDocxZoom); ?>"
        >
            <div class="space-y-3 p-4" data-file-preview-skeleton aria-label="<?php echo e($loadingLabel); ?>">
                <div class="skeleton h-6 w-2/3"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-5/6"></div>
                <div class="skeleton h-40 w-full"></div>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/partials/file-preview-body.blade.php ENDPATH**/ ?>