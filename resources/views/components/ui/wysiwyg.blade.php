@props([
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'disabled' => false,
    'inputId' => null,
    'toolbar' => true, // true|false
    'height' => null,  // ex: '20rem'
    'attachments' => false, // pièces jointes Trix
    // Lazy init options
    // false => init auto; 'button'|true => bouton pour init à la demande
    'lazy' => false,
    'lazyButtonLabel' => 'Activer l\'éditeur',
])

@php
    $inputId = $inputId ?: ($name ? 'trix-'.str_replace(['[',']','.'], '-', $name).'-'.uniqid() : 'trix-'.uniqid());
    $classes = 'trix-wrapper';
    $attachmentsAttr = $attachments ? '1' : '0';
    $isDeferred = $lazy === true || $lazy === 1 || $lazy === '1' || $lazy === 'button';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} data-trix-attachments="{{ $attachmentsAttr }}" @if($isDeferred) data-trix-deferred="1" @endif>
    @if($isDeferred)
        <div class="mb-2">
            <button type="button" class="btn btn-primary btn-sm" data-trix-init-button>{{ $lazyButtonLabel }}</button>
        </div>
    @endif
    <div data-trix-container @if($isDeferred) class="hidden" @endif>
        @if($toolbar)
            <trix-toolbar id="{{ $inputId }}-toolbar"></trix-toolbar>
        @endif
        @if($name)
            <input id="{{ $inputId }}-input" type="hidden" name="{{ $name }}" value="{{ $value }}" />
            <trix-editor input="{{ $inputId }}-input" placeholder="{{ $placeholder }}" @disabled($disabled)
                @if($toolbar) toolbar="{{ $inputId }}-toolbar" @endif
                style="{{ $height ? 'min-height: '.$height.';' : '' }}"></trix-editor>
        @else
            <trix-editor placeholder="{{ $placeholder }}" @disabled($disabled)
                style="{{ $height ? 'min-height: '.$height.';' : '' }}">{!! $value ?? $slot !!}</trix-editor>
        @endif
    </div>

    @include('daisy::components.partials.assets')
</div>


