@if($isPreviewable && $previewMode === 'modal')
    <button type="button" class="btn btn-ghost {{ $buttonSize ?? 'btn-xs' }} btn-circle" data-file-preview-open-modal="{{ $modalId }}" title="{{ $previewLabel }}">
        <x-icon name="bi-eye" class="w-4 h-4" />
    </button>
@elseif($isPreviewable && $previewMode === 'download')
    <a href="{{ $resolvedPreviewUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost {{ $buttonSize ?? 'btn-xs' }} btn-circle" title="{{ $openLabel }}">
        <x-icon name="bi-box-arrow-up-right" class="w-4 h-4" />
    </a>
@endif

@if($canDownload)
    <button
        type="button"
        class="btn btn-ghost {{ $buttonSize ?? 'btn-xs' }} btn-circle file-download"
        data-file-download
        data-url="{{ $resolvedDownloadUrl }}"
        data-filename="{{ $name ?? 'file' }}"
        title="{{ $downloadLabel }}"
    >
        <x-icon name="bi-download" class="w-4 h-4" />
    </button>
@endif
