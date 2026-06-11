@props([
    'file' => null,
    'url' => null,
    'type' => null,
    'mimeType' => null,
    'extension' => null,
    'label' => null,
    'disabledWhenUnavailable' => true,
])

@php
    use Art35rennes\DaisyKit\Support\FilePreview;

    $metadata = array_replace(FilePreview::metadata($file), array_filter([
        'url' => $url,
        'type' => $type,
        'mimeType' => $mimeType,
        'extension' => $extension,
    ], fn ($value) => $value !== null));
    $capabilities = FilePreview::capabilities($metadata);
    $isPreviewable = $capabilities['isPreviewable'];
    $label = $label ?: __('daisy::components.file_preview.preview');
@endphp

@if($isPreviewable || $disabledWhenUnavailable)
    <button
        type="button"
        @disabled(! $isPreviewable)
        data-file-preview-trigger
        data-file-preview-type="{{ $capabilities['type'] }}"
        data-file-preview-renderer="{{ $capabilities['renderer'] ?? '' }}"
        @if(! $isPreviewable) data-file-preview-reason="{{ $capabilities['reason'] }}" @endif
        {{ $attributes->merge(['class' => 'btn btn-sm btn-ghost']) }}
    >
        <x-icon name="bi-eye" class="w-4 h-4" />
        <span>{{ $label }}</span>
    </button>
@endif
