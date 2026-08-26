@props([
    'src' => null,
    'type' => null,
    'name' => 'File preview',
    'maxBytes' => null,
    'layout' => 'standard',
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
        <dl data-daisy-kit-file-preview-metadata hidden>
            <dt>Name</dt><dd data-daisy-kit-file-preview-name></dd>
            <dt>Type</dt><dd data-daisy-kit-file-preview-type></dd>
            <dt>Size</dt><dd data-daisy-kit-file-preview-size></dd>
        </dl>
        <button data-daisy-kit-file-preview-layout type="button">Toggle expanded layout</button>
        <p data-daisy-kit-file-preview-actions hidden>
            <a data-daisy-kit-file-preview-open hidden rel="noopener" target="_blank">Open file</a>
            <a data-daisy-kit-file-preview-download hidden>Download file</a>
        </p>
    </div>

    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'src' => $src,
        'type' => $type,
        'name' => $name,
        'maxBytes' => $maxBytes,
        'layout' => $layout,
    ]) !!}</script>
</section>
