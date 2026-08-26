@props([
    'src' => null,
    'type' => null,
    'name' => 'File preview',
    'maxBytes' => null,
])

<section
    {{ $attributes->merge(['data-daisy-kit-module' => 'file-preview']) }}
    aria-label="{{ $name }}"
>
    <p data-daisy-kit-status hidden role="alert"></p>

    <div data-daisy-kit-content>
        <p data-daisy-kit-loading hidden role="status">Loading preview…</p>
        <p data-daisy-kit-empty hidden>No file is selected for preview.</p>
        <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts" title="{{ $name }}"></iframe>
    </div>

    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'src' => $src,
        'type' => $type,
        'name' => $name,
        'maxBytes' => $maxBytes,
    ]) !!}</script>
</section>
