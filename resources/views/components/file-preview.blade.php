@props([
    'src' => null,
    'type' => null,
    'name' => 'File preview',
])

<section
    {{ $attributes->merge(['data-daisy-kit-module' => 'file-preview']) }}
    aria-label="{{ $name }}"
>
    <p data-daisy-kit-status hidden role="alert"></p>

    <div data-daisy-kit-content>
        <p data-daisy-kit-loading hidden role="status">Loading preview…</p>
        <p data-daisy-kit-empty hidden>No file is selected for preview.</p>
        <img alt="" data-daisy-kit-file-preview-image hidden>
        <pre data-daisy-kit-file-preview-text hidden></pre>
        <div data-daisy-kit-file-preview-docx hidden></div>
    </div>

    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'src' => $src,
        'type' => $type,
        'name' => $name,
    ]) !!}</script>
</section>
