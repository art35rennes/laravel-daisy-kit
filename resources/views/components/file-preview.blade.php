@props([
    'src' => null,
    'type' => null,
    'name' => 'File preview',
    'maxBytes' => null,
    'layout' => 'standard',
    'notice' => null,
])

<section
    {{ $attributes->merge(['data-daisy-kit-module' => 'file-preview']) }}
    aria-label="{{ $name }}"
>
    <p data-daisy-kit-status hidden role="alert"></p>

    <div data-daisy-kit-content>
        <p data-daisy-kit-loading hidden role="status">Loading preview…</p>
        <p data-daisy-kit-empty hidden>No file is selected for preview.</p>
        <dialog data-daisy-kit-file-preview-modal aria-label="{{ $name }}"></dialog>
        <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts" title="{{ $name }}"></iframe>
        <dl data-daisy-kit-file-preview-metadata hidden>
            <dt>Name</dt><dd data-daisy-kit-file-preview-name></dd>
            <dt>Type</dt><dd data-daisy-kit-file-preview-type></dd>
            <dt>Size</dt><dd data-daisy-kit-file-preview-size></dd>
        </dl>
        <p data-daisy-kit-file-preview-notice hidden role="status"></p>
        <p data-daisy-kit-file-preview-controls>
            <button data-daisy-kit-file-preview-open-preview type="button">Preview file</button>
            <button data-daisy-kit-file-preview-layout type="button">Toggle expanded layout</button>
            <button data-daisy-kit-file-preview-zoom="out" type="button">Zoom out</button>
            <button data-daisy-kit-file-preview-zoom="in" type="button">Zoom in</button>
        </p>
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
        'notice' => $notice,
    ]) !!}</script>
</section>
