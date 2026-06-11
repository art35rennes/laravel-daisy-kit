@props([
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
])

@php
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
@endphp

@once
    @include('daisy::components.partials.assets')
@endonce

<div
    {{ $attributes->merge(['class' => 'file-preview inline-block w-full '.$sizes['container'], 'data-module' => 'file-preview']) }}
    data-file-preview-type="{{ $previewType }}"
>
    @if($isPreviewable && $previewMode === 'inline')
        <div class="overflow-hidden rounded-box card-border bg-base-100">
            <div class="flex items-center justify-between gap-3 border-b border-base-300/60 bg-base-200/70 px-3 py-2">
                <div class="min-w-0">
                    @if($name)
                        <p class="truncate text-sm font-medium">{{ $name }}</p>
                    @endif
                    @if($showMeta && $fileSize)
                        <p class="text-xs text-base-content/70">{{ $fileSize }}</p>
                    @endif
                </div>
                <div class="flex shrink-0 items-center gap-1">
                    @include('daisy::partials.file-preview-actions', ['buttonSize' => 'btn-xs'])
                </div>
            </div>
            <div class="bg-base-100">
                @include('daisy::partials.file-preview-body', ['previewContext' => 'inline'])
            </div>
        </div>
    @else
        <div class="rounded-box card-border bg-base-200 p-3 transition-colors hover:bg-base-300">
            <div class="flex items-center gap-3">
                @if($thumbnail)
                    <img src="{{ $thumbnail }}" alt="" class="{{ $sizes['icon'] }} rounded object-cover" loading="lazy" />
                @else
                    <x-icon name="{{ $icon }}" class="{{ $sizes['icon'] }} shrink-0 text-primary" />
                @endif

                <div class="min-w-0 flex-1">
                    @if($name)
                        <p class="truncate text-sm font-medium">{{ $name }}</p>
                    @endif
                    @if($showMeta && $fileSize)
                        <p class="text-xs text-base-content/70">{{ $fileSize }}</p>
                    @endif
                </div>

                <div class="flex shrink-0 items-center gap-1">
                    @include('daisy::partials.file-preview-actions', ['buttonSize' => 'btn-xs'])
                </div>
            </div>
        </div>
    @endif

    @if($modalId)
        <x-daisy::ui.overlay.modal
            :id="$modalId"
            :title="$name ?? $previewLabel"
            size="7xl"
            :boxClass="'p-0'"
        >
            <div class="bg-base-100">
                @include('daisy::partials.file-preview-body', ['previewContext' => 'modal'])
            </div>
            <x-slot:actions>
                @if($downloadFromPreview && $canDownload)
                    <button
                        type="button"
                        class="btn btn-primary file-download"
                        data-file-download
                        data-url="{{ $resolvedDownloadUrl }}"
                        data-filename="{{ $name ?? 'file' }}"
                        title="{{ $downloadLabel }}"
                    >
                        <x-icon name="bi-download" class="w-4 h-4 mr-2" />
                        {{ $downloadLabel }}
                    </button>
                @endif
                <form method="dialog">
                    <button type="submit" class="btn">{{ $closeLabel }}</button>
                </form>
            </x-slot:actions>
        </x-daisy::ui.overlay.modal>
    @endif
</div>
