<div class="daisy-kit-file-preview__states" aria-live="polite">
    <div class="alert alert-info" data-daisy-kit-loading hidden role="status">
        <div class="flex w-full items-center gap-3" aria-hidden="true">
            <span class="skeleton h-10 w-10 shrink-0"></span>
            <span class="flex flex-1 flex-col gap-2">
                <span class="skeleton h-3 w-2/3"></span>
                <span class="skeleton h-3 w-1/3"></span>
            </span>
        </div>
        <span class="sr-only">{{ __('daisy-kit::file-preview.loading') }}</span>
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
