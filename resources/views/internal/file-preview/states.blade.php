<div class="daisy-kit-file-preview__states" aria-live="polite">
    <div class="alert alert-info" data-daisy-kit-loading hidden role="status">
        <span class="loading loading-spinner loading-sm" aria-hidden="true"></span>
        <span>{{ __('daisy-kit::file-preview.loading') }}</span>
    </div>

    <p class="alert" data-daisy-kit-empty @if($resolvedPreviewUrl !== null || $resolvedUrl !== null) hidden @endif role="status">
        {{ __('daisy-kit::file-preview.empty') }}
    </p>

    <div class="alert alert-error" data-daisy-kit-status hidden role="alert">
        <span data-daisy-kit-status-message>{{ __('daisy-kit::file-preview.error') }}</span>
        <button class="btn btn-sm btn-ghost" data-daisy-kit-file-preview-retry hidden type="button">
            {{ __('daisy-kit::file-preview.retry') }}
        </button>
    </div>
</div>
