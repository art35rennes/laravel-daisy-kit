@if($canPreview)
    <dialog class="modal" data-daisy-kit-file-preview-modal aria-labelledby="{{ $modalTitleId }}">
        <div class="modal-box {{ $modalSizeClass }} {{ $modalBoxClass }}" data-daisy-kit-file-preview-modal-box>
            <header class="daisy-kit-file-preview__modal-header">
                <h2 class="text-lg font-semibold" id="{{ $modalTitleId }}">
                    {{ $modalTitle ?: __('daisy-kit::file-preview.modal_title', ['name' => $resolvedName]) }}
                </h2>
                <button class="btn btn-sm btn-circle btn-ghost" data-daisy-kit-file-preview-close-preview type="button" aria-label="{{ __('daisy-kit::file-preview.close') }}">✕</button>
            </header>

            @if($resolvedType === 'docx' && $docxZoomControls)
                <div class="flex flex-wrap gap-2" aria-label="{{ __('daisy-kit::file-preview.preview') }}">
                    <div class="join">
                        <button class="btn btn-sm join-item" data-daisy-kit-file-preview-zoom="out" type="button">{{ __('daisy-kit::file-preview.zoom_out') }}</button>
                        <output class="btn btn-sm join-item pointer-events-none" data-daisy-kit-file-preview-zoom-output>{{ max(25, min((int) $docxZoom, 200)) }}%</output>
                        <button class="btn btn-sm join-item" data-daisy-kit-file-preview-zoom="in" type="button">{{ __('daisy-kit::file-preview.zoom_in') }}</button>
                    </div>
                    <button class="btn btn-sm" data-daisy-kit-file-preview-zoom="fit" type="button">{{ __('daisy-kit::file-preview.zoom_fit') }}</button>
                </div>
            @endif

            <div
                class="daisy-kit-file-preview__modal-content {{ $modalContentClass }}"
                data-daisy-kit-file-preview-modal-content
                tabindex="0"
                role="region"
                aria-label="{{ __('daisy-kit::file-preview.modal_title', ['name' => $resolvedName]) }}"
            >
                @if($resolvedPreviewMode !== 'inline')
                    @include('daisy-kit::internal.file-preview.frame')
                @endif
            </div>

            @if($showModalFooter || ($canDownload && $showDownloadAction))
                <footer class="daisy-kit-file-preview__modal-footer modal-action">
                    @if($showModalFooter)
                        @if(isset($modalFooter) && $modalFooter instanceof \Illuminate\View\ComponentSlot)
                            {{ $modalFooter }}
                        @else
                            <button class="btn" data-daisy-kit-file-preview-close-preview type="button">{{ __('daisy-kit::file-preview.close') }}</button>
                        @endif
                    @endif

                    @if($canDownload && $showDownloadAction)
                        <a
                            class="btn {{ $downloadActionClass }}"
                            data-daisy-kit-file-preview-download
                            data-daisy-kit-file-preview-modal-download
                            href="{{ $resolvedDownloadUrl }}"
                            download="{{ $resolvedName }}"
                        >
                            {{ __('daisy-kit::file-preview.download') }}
                        </a>
                    @endif
                </footer>
            @endif
        </div>
        <form method="dialog" class="modal-backdrop"><button>{{ __('daisy-kit::file-preview.close') }}</button></form>
    </dialog>
@endif
