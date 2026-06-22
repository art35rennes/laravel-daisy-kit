@php
    $actions = $actions ?? ['preview', 'download'];
    $buttonSize = $buttonSize ?? 'btn-xs';
    $buttonExtraClass = $buttonExtraClass ?? '';
    $previewActionButtonClass = $previewActionButtonClass ?? '';
    $labelled = $labelled ?? false;
    $modalActions = $modalActions ?? false;
    $iconOnlyClass = $labelled ? '' : 'btn-circle';
    $previewExtraClass = trim($buttonExtraClass.' '.$previewActionButtonClass);
@endphp

@foreach($actions as $action)
    @if($action === 'preview' && $isPreviewable && $previewMode === 'modal' && ! $modalActions)
        <button type="button" class="btn {{ $previewButtonClass }} {{ $buttonSize }} {{ $iconOnlyClass }} {{ $previewExtraClass }}" data-file-preview-open-modal="{{ $modalId }}" aria-label="{{ $previewLabel }}" title="{{ $previewLabel }}">
            <x-icon name="bi-eye" class="w-4 h-4 {{ $labelled ? 'mr-2' : '' }}" />
            @if($labelled)
                {{ $previewLabel }}
            @endif
        </button>
    @elseif($action === 'preview' && $isPreviewable && $previewMode === 'download' && ! $modalActions)
        <a href="{{ $resolvedPreviewUrl }}" target="_blank" rel="noopener noreferrer" class="btn {{ $openButtonClass }} {{ $buttonSize }} {{ $iconOnlyClass }} {{ $previewExtraClass }}" aria-label="{{ $openLabel }}" title="{{ $openLabel }}">
            <x-icon name="bi-box-arrow-up-right" class="w-4 h-4 {{ $labelled ? 'mr-2' : '' }}" />
            @if($labelled)
                {{ $openLabel }}
            @endif
        </a>
    @elseif($action === 'open' && $isPreviewable)
        <a href="{{ $resolvedPreviewUrl }}" target="_blank" rel="noopener noreferrer" class="btn {{ $modalActions ? $modalOpenButtonClass : $openButtonClass }} {{ $buttonSize }} {{ $iconOnlyClass }} {{ $buttonExtraClass }}" title="{{ $openLabel }}">
            <x-icon name="bi-box-arrow-up-right" class="w-4 h-4 {{ $labelled ? 'mr-2' : '' }}" />
            @if($labelled)
                {{ $openLabel }}
            @endif
        </a>
    @elseif($action === 'download' && $canDownload && (! $modalActions || $downloadFromPreview))
        <button
            type="button"
            class="btn {{ $modalActions ? $modalDownloadButtonClass : $downloadButtonClass }} {{ $buttonSize }} {{ $iconOnlyClass }} {{ $buttonExtraClass }} file-download"
            data-file-download
            data-url="{{ $resolvedDownloadUrl }}"
            data-filename="{{ $name ?? 'file' }}"
            title="{{ $downloadLabel }}"
        >
            <x-icon name="bi-download" class="w-4 h-4 {{ $labelled ? 'mr-2' : '' }}" />
            @if($labelled)
                {{ $downloadLabel }}
            @endif
        </button>
    @elseif($action === 'close' && $modalActions)
        <form method="dialog">
            <button type="submit" class="btn {{ $modalCloseButtonClass }} {{ $buttonSize }}" title="{{ $closeLabel }}">
                {{ $closeLabel }}
            </button>
        </form>
    @endif
@endforeach
