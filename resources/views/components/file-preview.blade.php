@props([
    'src' => null,
    'type' => null,
    'name' => 'File preview',
    'maxBytes' => null,
    'layout' => 'standard',
    'notice' => null,
])

<section
    {{ $attributes->class(['card', 'border', 'border-base-300', 'bg-base-100', 'shadow-sm', 'daisy-kit-file-preview'])->merge(['data-daisy-kit-module' => 'file-preview']) }}
    aria-label="{{ $name }}"
>
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>

    <div class="card-body" data-daisy-kit-content>
        <p class="alert" data-daisy-kit-loading hidden role="status">Loading preview…</p>
        <p class="alert" data-daisy-kit-empty hidden>No file is selected for preview.</p>
        <dialog class="modal" data-daisy-kit-file-preview-modal aria-label="{{ $name }}">
            <div class="modal-box">
                <p class="flex flex-wrap gap-2">
                    <button class="btn btn-sm" data-daisy-kit-file-preview-zoom="out" type="button">Zoom out</button>
                    <button class="btn btn-sm" data-daisy-kit-file-preview-zoom="in" type="button">Zoom in</button>
                    <button class="btn btn-sm" data-daisy-kit-file-preview-close-preview type="button">Close preview</button>
                </p>
            </div>
        </dialog>
        <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts" title="{{ $name }}"></iframe>
        <dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-sm" data-daisy-kit-file-preview-metadata hidden>
            <dt>Name</dt><dd data-daisy-kit-file-preview-name></dd>
            <dt>Type</dt><dd data-daisy-kit-file-preview-type></dd>
            <dt>Size</dt><dd data-daisy-kit-file-preview-size></dd>
        </dl>
        <p class="alert alert-warning" data-daisy-kit-file-preview-notice hidden role="status"></p>
        <p class="flex flex-wrap gap-2" data-daisy-kit-file-preview-controls>
            <button class="btn btn-primary" data-daisy-kit-file-preview-open-preview type="button">Preview file</button>
            <button class="btn" data-daisy-kit-file-preview-layout type="button">Toggle expanded layout</button>
            <button class="btn btn-sm" data-daisy-kit-file-preview-zoom="out" type="button">Zoom out</button>
            <button class="btn btn-sm" data-daisy-kit-file-preview-zoom="in" type="button">Zoom in</button>
        </p>
        <p class="flex flex-wrap gap-2" data-daisy-kit-file-preview-actions hidden>
            <a class="btn btn-sm" data-daisy-kit-file-preview-open hidden rel="noopener" target="_blank">Open file</a>
            <a class="btn btn-sm" data-daisy-kit-file-preview-download hidden>Download file</a>
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
