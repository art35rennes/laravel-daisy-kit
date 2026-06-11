@if($previewType === 'image')
    <img src="{{ $resolvedPreviewUrl }}" alt="{{ $name ?? 'Image' }}" class="w-full h-auto {{ $previewContext === 'modal' ? 'max-h-[calc(100svh-10rem)]' : $sizes['media'] }} object-contain" loading="lazy" />
@elseif($previewType === 'video')
    <video src="{{ $resolvedPreviewUrl }}" controls class="w-full {{ $previewContext === 'modal' ? 'max-h-[calc(100svh-10rem)]' : $sizes['media'] }} object-contain">
        {{ __('daisy::components.file_preview.video_unsupported') }}
    </video>
@elseif($previewType === 'audio')
    <div class="flex min-h-32 items-center justify-center bg-base-200 p-4">
        <audio src="{{ $resolvedPreviewUrl }}" controls class="w-full">
            {{ __('daisy::components.file_preview.audio_unsupported') }}
        </audio>
    </div>
@elseif($previewType === 'pdf')
    <object data="{{ $resolvedPreviewUrl }}" type="application/pdf" class="w-full {{ $previewContext === 'modal' ? 'h-[calc(100svh-12rem)]' : $sizes['frame'] }}">
        <iframe src="{{ $resolvedPreviewUrl }}" class="w-full {{ $previewContext === 'modal' ? 'h-[calc(100svh-12rem)]' : $sizes['frame'] }}" title="{{ $name ?? 'PDF preview' }}"></iframe>
    </object>
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
        <div class="flex min-h-48 items-center justify-center text-sm text-base-content/70">{{ $loadingLabel }}</div>
    </div>
@endif
