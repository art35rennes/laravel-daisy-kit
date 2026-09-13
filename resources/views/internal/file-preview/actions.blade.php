@if(isset($trigger) && $trigger instanceof \Illuminate\View\ComponentSlot)
    <span data-daisy-kit-file-preview-trigger-slot>{{ $trigger }}</span>
@endif

@if(isset($actions) && $actions instanceof \Illuminate\View\ComponentSlot)
    <div data-daisy-kit-file-preview-actions>{{ $actions }}</div>
@else
    <div class="daisy-kit-file-preview__actions flex flex-wrap gap-2" data-daisy-kit-file-preview-actions>
        @foreach($resolvedActionOrder as $action)
            @if($action === 'preview' && $canPreview && $showPreviewAction)
                @if(isset($trigger) && $trigger instanceof \Illuminate\View\ComponentSlot)
                    @continue
                @else
                    <button class="btn btn-sm {{ $previewActionClass }}" data-daisy-kit-file-preview-open-preview type="button">
                        {{ __('daisy-kit::file-preview.preview') }}
                    </button>
                @endif
            @elseif($action === 'open' && $canPreview && $showOpenAction)
                <a class="btn btn-sm {{ $openActionClass }}" data-daisy-kit-file-preview-open hidden rel="noopener noreferrer" target="_blank">
                    {{ __('daisy-kit::file-preview.open') }}
                </a>
            @elseif($action === 'download' && $canDownload && $showDownloadAction)
                <a
                    class="btn btn-sm {{ $downloadActionClass }}"
                    data-daisy-kit-file-preview-download
                    href="{{ $resolvedDownloadUrl }}"
                    download="{{ $resolvedName }}"
                >
                    {{ __('daisy-kit::file-preview.download') }}
                </a>
            @endif
        @endforeach
    </div>
@endif
