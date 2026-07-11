@if($previewType === 'image')
    <div class="relative min-h-64" data-file-preview-loadable-container>
        <div class="absolute inset-0 space-y-3 p-4" data-file-preview-skeleton aria-label="{{ $loadingLabel }}">
            <div class="skeleton h-full min-h-56 w-full"></div>
        </div>
        <img src="{{ $resolvedPreviewUrl }}" alt="{{ $name ?? 'Image' }}" class="w-full h-auto opacity-0 {{ $previewContext === 'modal' ? 'max-h-[calc(100svh-10rem)]' : $sizes['media'] }} object-contain" loading="lazy" data-file-preview-loadable />
    </div>
@elseif($previewType === 'video')
    <div class="relative min-h-64" data-file-preview-loadable-container>
        <div class="absolute inset-0 p-4" data-file-preview-skeleton aria-label="{{ $loadingLabel }}"><div class="skeleton h-full min-h-56 w-full"></div></div>
        <video src="{{ $resolvedPreviewUrl }}" controls class="w-full opacity-0 {{ $previewContext === 'modal' ? 'max-h-[calc(100svh-10rem)]' : $sizes['media'] }} object-contain" data-file-preview-loadable>
            {{ __('daisy::components.file_preview.video_unsupported') }}
        </video>
    </div>
@elseif($previewType === 'audio')
    <div class="relative flex min-h-32 items-center justify-center bg-base-200 p-4" data-file-preview-loadable-container>
        <div class="absolute inset-4" data-file-preview-skeleton aria-label="{{ $loadingLabel }}"><div class="skeleton h-full w-full"></div></div>
        <audio src="{{ $resolvedPreviewUrl }}" controls class="w-full opacity-0" data-file-preview-loadable>
            {{ __('daisy::components.file_preview.audio_unsupported') }}
        </audio>
    </div>
@elseif($previewType === 'pdf')
    <div class="relative min-h-64" data-file-preview-loadable-container>
        <div class="absolute inset-0 space-y-3 p-4" data-file-preview-skeleton aria-label="{{ $loadingLabel }}">
            <div class="skeleton h-6 w-2/3"></div>
            <div class="skeleton h-4 w-full"></div>
            <div class="skeleton h-4 w-5/6"></div>
            <div class="skeleton h-48 w-full"></div>
        </div>
        <object data="{{ $resolvedPreviewUrl }}" type="application/pdf" class="w-full opacity-0 {{ $previewContext === 'modal' ? 'h-[calc(100svh-12rem)]' : $sizes['frame'] }}" data-file-preview-loadable>
            <iframe src="{{ $resolvedPreviewUrl }}" class="w-full {{ $previewContext === 'modal' ? 'h-[calc(100svh-12rem)]' : $sizes['frame'] }}" title="{{ $name ?? 'PDF preview' }}"></iframe>
        </object>
    </div>
@elseif($previewType === 'text')
    <pre
        class="max-h-96 overflow-auto whitespace-pre-wrap break-words bg-base-200 p-3 text-xs"
        data-file-preview-text
        data-url="{{ $resolvedPreviewUrl }}"
        data-max-bytes="{{ (int) $maxTextPreviewBytes }}"
        data-loading-label="{{ $loadingLabel }}"
        data-error-label="{{ $fallbackLabel }}"
    >{{ $loadingLabel }}</pre>
@elseif($previewType === 'docx')
    <div
        class="min-h-64 overflow-auto bg-base-100 p-3"
        data-file-preview-docx
        data-url="{{ $resolvedPreviewUrl }}"
        data-loading-label="{{ $loadingLabel }}"
        data-error-label="{{ $fallbackLabel }}"
    >
        <div class="space-y-3 p-4" data-file-preview-skeleton aria-label="{{ $loadingLabel }}">
            <div class="skeleton h-6 w-2/3"></div>
            <div class="skeleton h-4 w-full"></div>
            <div class="skeleton h-4 w-5/6"></div>
            <div class="skeleton h-40 w-full"></div>
        </div>
    </div>
@endif
