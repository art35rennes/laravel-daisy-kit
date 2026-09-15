@props([
    'value' => '', 'language' => 'text', 'name' => null, 'label' => 'Code',
    'filename' => null, 'readOnly' => false, 'disabled' => false, 'required' => false,
    'lineNumbers' => true, 'lineWrapping' => false, 'tabSize' => 4,
    'toolbar' => true, 'statusBar' => true, 'nonce' => null, 'labels' => [], 'phrases' => [],
])

@php
    $controlLabels = array_replace([
        'copy' => 'Copy', 'search' => 'Search', 'undo' => 'Undo', 'redo' => 'Redo',
        'wrap' => 'Wrap lines', 'expand' => 'Expand',
    ], $labels);
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'language' => $language, 'label' => $label, 'readOnly' => $readOnly,
        'disabled' => $disabled, 'lineNumbers' => $lineNumbers, 'lineWrapping' => $lineWrapping,
        'tabSize' => $tabSize, 'toolbar' => $toolbar, 'nonce' => $nonce,
        'labels' => $labels, 'phrases' => $phrases,
    ]);
@endphp

<fieldset {{ $attributes->only(['id', 'class', 'aria-describedby', 'data-theme'])->class(['fieldset', 'daisy-kit-code-editor']) }} data-daisy-kit-module="code-editor" @disabled($disabled)>
    <legend class="fieldset-legend">{{ $label }}</legend>
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>
    <div class="daisy-kit-code-editor__shell">
        <div class="daisy-kit-code-editor__toolbar" data-code-editor-toolbar hidden role="group" aria-label="{{ $label }}">
            <span class="daisy-kit-code-editor__filename">{{ $filename }}</span>
            <span class="badge badge-ghost badge-sm" data-code-editor-language>{{ $language }}</span>
            <div class="daisy-kit-code-editor__actions">
                @foreach (['search', 'undo', 'redo', 'wrap', 'copy', 'expand'] as $action)
                    <button class="btn btn-ghost btn-xs" type="button" data-code-editor-action="{{ $action }}" @disabled($disabled)>{{ $controlLabels[$action] }}</button>
                @endforeach
            </div>
        </div>
        <textarea class="textarea daisy-kit-code-editor__fallback" @if ($name !== null) name="{{ $name }}" @endif aria-label="{{ $label }}" @readonly($readOnly) @required($required) @disabled($disabled) spellcheck="false">{{ $value }}</textarea>
        <div data-code-editor-host></div>
        @if ($statusBar)
            <div class="daisy-kit-code-editor__status"><span data-code-editor-position></span><span data-code-editor-feedback role="status" aria-live="polite"></span></div>
        @else
            <span class="sr-only" data-code-editor-feedback role="status" aria-live="polite"></span>
        @endif
    </div>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</fieldset>
