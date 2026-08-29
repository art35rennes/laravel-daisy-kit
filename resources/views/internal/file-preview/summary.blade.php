<div class="daisy-kit-file-preview__summary">
    @if($resolvedThumbnail && $showCompactPreview)
        <img class="daisy-kit-file-preview__thumbnail" src="{{ $resolvedThumbnail }}" alt="">
    @else
        <span class="daisy-kit-file-preview__type badge badge-outline">{{ strtoupper($resolvedExtension ?: $resolvedType) }}</span>
    @endif

    <div class="daisy-kit-file-preview__identity">
        <p class="font-medium break-words">{{ $resolvedName }}</p>

        @if(isset($metadata) && $metadata instanceof \Illuminate\View\ComponentSlot)
            {{ $metadata }}
        @elseif($showMeta)
            <dl class="daisy-kit-file-preview__metadata text-sm text-base-content/70" data-daisy-kit-file-preview-metadata>
                <div><dt class="sr-only">{{ __('daisy-kit::file-preview.type') }}</dt><dd>{{ $resolvedType }}</dd></div>
                @if($resolvedFileSize !== null)
                    <div><dt class="sr-only">{{ __('daisy-kit::file-preview.size') }}</dt><dd>{{ \Art35rennes\DaisyKit\Support\FilePreview::formatSize($resolvedFileSize) }}</dd></div>
                @endif
            </dl>
        @endif
    </div>

    <div class="daisy-kit-file-preview__actions-wrap">
        @include('daisy-kit::internal.file-preview.actions')
    </div>
</div>

@if($noticeSlot)
    <div class="alert alert-warning" data-daisy-kit-file-preview-notice>{{ $noticeSlot }}</div>
@elseif(is_string($noticeText) && trim($noticeText) !== '')
    <p class="alert alert-warning" data-daisy-kit-file-preview-notice>{{ $noticeText }}</p>
@elseif($resolvedType === 'docx' && is_string($docxNotice) && trim($docxNotice) !== '')
    <p class="alert alert-warning" data-daisy-kit-file-preview-notice>{{ $docxNotice }}</p>
@endif

@unless($canPreview)
    <p class="alert alert-info" data-daisy-kit-file-preview-unsupported role="status">
        {{ __('daisy-kit::file-preview.unsupported') }}
    </p>
@endunless
