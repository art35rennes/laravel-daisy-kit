@props([
    'file' => null,
    'url' => null,
    'previewUrl' => null,
    'downloadUrl' => null,
    'name' => null,
    'type' => null,
    'mimeType' => null,
    'extension' => null,
    'fileSize' => null,
    'thumbnail' => null,
    'layout' => 'card',
    'size' => 'md',
    'previewMode' => 'auto',
    'showMeta' => true,
    'showCompactPreview' => true,
    'notice' => null,
    'showPreviewAction' => true,
    'showDownloadAction' => true,
    'showOpenAction' => true,
    'actionOrder' => ['preview', 'open', 'download'],
    'previewActionClass' => 'btn-primary',
    'openActionClass' => 'btn-ghost',
    'downloadActionClass' => 'btn-ghost',
    'docxPreview' => true,
    'docxView' => 'page',
    'docxZoom' => 100,
    'docxZoomControls' => true,
    'docxNotice' => null,
    'modalTitle' => null,
    'modalSize' => '5xl',
    'modalBoxClass' => null,
    'modalContentClass' => null,
    'showModalFooter' => true,
    'maxPreviewBytes' => 10 * 1024 * 1024,
    'maxTextPreviewBytes' => 64 * 1024,
])

@php
    use Art35rennes\DaisyKit\Support\FilePreview;
    use Art35rennes\DaisyKit\Support\JsonConfiguration;
    use Illuminate\View\ComponentSlot;

    $noticeSlot = $notice instanceof ComponentSlot ? $notice : null;
    $noticeText = $noticeSlot ? null : $notice;
    $metadataValues = array_replace(FilePreview::metadata($file), array_filter([
        'url' => $url,
        'previewUrl' => $previewUrl,
        'downloadUrl' => $downloadUrl,
        'name' => $name,
        'type' => $type,
        'mimeType' => $mimeType,
        'extension' => $extension,
        'size' => $fileSize,
    ], static fn (mixed $value): bool => $value !== null));
    $capabilities = FilePreview::capabilities($metadataValues);
    $resolvedType = $capabilities['type'];
    $metadataName = $metadataValues['name'] ?? null;
    $resolvedName = is_string($metadataName) || $metadataName instanceof \Stringable
        ? trim((string) $metadataName)
        : __('daisy-kit::file-preview.preview');
    $resolvedName = $resolvedName !== '' ? $resolvedName : __('daisy-kit::file-preview.preview');
    $resolvedMimeType = is_string($metadataValues['mimeType'] ?? null) ? $metadataValues['mimeType'] : null;
    $resolvedExtension = is_string($metadataValues['extension'] ?? null) ? ltrim($metadataValues['extension'], '.') : null;
    $resolvedFileSize = is_numeric($metadataValues['size'] ?? null) ? (int) $metadataValues['size'] : null;
    $resolvedUrl = FilePreview::safeUrl($metadataValues['url'] ?? null);
    $resolvedPreviewUrl = FilePreview::safeUrl($metadataValues['previewUrl'] ?? null) ?? $resolvedUrl;
    $resolvedDownloadUrl = FilePreview::safeUrl($metadataValues['downloadUrl'] ?? null) ?? $resolvedUrl;
    $resolvedThumbnail = FilePreview::safeUrl($thumbnail);
    $canPreview = $capabilities['isPreviewable'] && $resolvedPreviewUrl !== null && ($resolvedType !== 'docx' || $docxPreview);
    $canDownload = $resolvedDownloadUrl !== null;
    $resolvedLayout = in_array($layout, ['card', 'compact-list', 'action-only'], true) ? $layout : 'card';
    $resolvedPreviewMode = in_array($previewMode, ['auto', 'inline', 'modal', 'download'], true) ? $previewMode : 'auto';
    $resolvedPreviewMode = $resolvedPreviewMode === 'auto'
        ? ($resolvedType === 'image' ? 'modal' : 'inline')
        : $resolvedPreviewMode;
    $canPreview = $canPreview && $resolvedPreviewMode !== 'download';
    $resolvedActionOrder = array_values(array_unique(array_intersect(
        is_array($actionOrder) ? $actionOrder : [],
        ['preview', 'open', 'download'],
    )));
    $sizeClass = match ($size) {
        'sm' => 'daisy-kit-file-preview--sm',
        'lg' => 'daisy-kit-file-preview--lg',
        default => 'daisy-kit-file-preview--md',
    };
    $modalSizeClass = match ($modalSize) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        default => 'max-w-5xl',
    };
    $modalTitleId = 'daisy-kit-file-preview-title-'.\Illuminate\Support\Str::uuid();
    $configuration = JsonConfiguration::encode([
        'url' => $resolvedUrl,
        'previewUrl' => $resolvedPreviewUrl,
        'downloadUrl' => $resolvedDownloadUrl,
        'name' => $resolvedName,
        'type' => $resolvedType,
        'mimeType' => $resolvedMimeType,
        'extension' => $resolvedExtension,
        'fileSize' => $resolvedFileSize,
        'layout' => $resolvedLayout,
        'previewMode' => $resolvedPreviewMode,
        'canPreview' => $canPreview,
        'canDownload' => $canDownload,
        'maxPreviewBytes' => max(1, min((int) $maxPreviewBytes, 50 * 1024 * 1024)),
        'maxTextPreviewBytes' => max(1, min((int) $maxTextPreviewBytes, 1024 * 1024)),
        'docxView' => in_array($docxView, ['page', 'width'], true) ? $docxView : 'page',
        'docxZoom' => max(25, min((int) $docxZoom, 200)),
        'labels' => [
            'error' => __('daisy-kit::file-preview.error'),
            'frameNotReady' => __('daisy-kit::file-preview.error'),
            'invalidType' => __('daisy-kit::file-preview.invalid_type'),
            'truncated' => __('daisy-kit::file-preview.truncated'),
            'tooLarge' => __('daisy-kit::file-preview.too_large'),
        ],
    ]);
@endphp

<section
    {{ $attributes
        ->except(['data-daisy-kit-config', 'data-daisy-kit-module', 'data-daisy-kit-state'])
        ->class([
            'daisy-kit-file-preview',
            $sizeClass,
            'card border border-base-300 bg-base-100 shadow-sm' => $resolvedLayout !== 'action-only',
        ]) }}
    data-daisy-kit-module="file-preview"
    data-daisy-kit-file-preview-capability="{{ $canPreview ? 'previewable' : ($resolvedPreviewUrl === null && $resolvedUrl === null ? 'empty' : 'unsupported') }}"
    data-daisy-kit-layout="{{ $resolvedLayout }}"
    data-daisy-kit-file-preview-type="{{ $resolvedType }}"
    aria-label="{{ $resolvedName }}"
>
    @if($resolvedLayout !== 'action-only')
        <div class="card-body gap-4" data-daisy-kit-content>
            @include('daisy-kit::internal.file-preview.states')

            @if($resolvedPreviewUrl !== null || $resolvedUrl !== null)
                @include('daisy-kit::internal.file-preview.summary')

                @if($resolvedPreviewMode === 'inline' && $canPreview)
                    <div class="daisy-kit-file-preview__inline" data-daisy-kit-file-preview-inline-content>
                        @include('daisy-kit::internal.file-preview.frame')
                    </div>
                @else
                    <div data-daisy-kit-file-preview-inline-content></div>
                @endif
            @endif
        </div>
    @else
        @include('daisy-kit::internal.file-preview.actions')
        @include('daisy-kit::internal.file-preview.states')
        <div data-daisy-kit-file-preview-inline-content></div>
    @endif

    @include('daisy-kit::internal.file-preview.modal')

    @if($canPreview && $resolvedPreviewMode !== 'inline')
        <div class="daisy-kit-file-preview__staging" data-daisy-kit-file-preview-frame-staging>
            @include('daisy-kit::internal.file-preview.frame')
        </div>
    @endif

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
