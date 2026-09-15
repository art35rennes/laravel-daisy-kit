@props([
    'name',
    'label' => 'Content',
    'value' => '',
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'showToolbar' => true,
    'attachments' => false,
    'size' => 'md',
])

@php
    $editorId = 'daisy-kit-wysiwyg-'.\Illuminate\Support\Str::uuid();
    $normalizedSize = in_array($size, ['sm', 'md', 'lg'], true) ? $size : 'md';
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'attachments' => $attachments === true,
        'autofocus' => $autofocus === true,
        'disabled' => $disabled === true,
        'editorId' => $editorId,
        'labelId' => $editorId.'-label',
        'placeholder' => $placeholder,
        'readonly' => $readonly === true,
        'required' => $required === true,
        'showToolbar' => $showToolbar === true,
        'size' => $normalizedSize,
        'value' => is_string($value) ? $value : '',
        'labels' => [
            'attachmentPending' => __('daisy-kit::wysiwyg.attachment_pending'),
            'attachment' => __('daisy-kit::wysiwyg.attachment'),
            'initializationFailed' => __('daisy-kit::wysiwyg.initialization_failed'),
        ],
    ]);
@endphp

<fieldset {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['fieldset', 'daisy-kit-wysiwyg', 'daisy-kit-wysiwyg--'.$normalizedSize]) }} data-daisy-kit-module="wysiwyg" @disabled($disabled)>
    <label class="fieldset-legend" for="{{ $editorId }}" id="{{ $editorId }}-label">{{ $label }}</label>
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>
    <div data-daisy-kit-wysiwyg-editor></div>
    <input
        data-daisy-kit-wysiwyg-value
        id="{{ $editorId }}-input"
        name="{{ $name }}"
        type="hidden"
        value="{{ is_string($value) ? $value : '' }}"
        @disabled($disabled)
    >
    <input
        aria-label="{{ $label }}"
        class="sr-only"
        data-daisy-kit-wysiwyg-validation
        tabindex="-1"
        type="text"
        @required($required)
        @disabled($disabled)
    >
    @if ($showToolbar)
        <template data-daisy-kit-wysiwyg-toolbar-template>
            @isset($toolbar)
                {{ $toolbar }}
            @else
                @include('daisy-kit::internal.wysiwyg.toolbar', ['attachments' => $attachments])
            @endisset
        </template>
    @endif
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</fieldset>
