@props([
    'inputId' => null,
    'value' => null,
    'placeholder' => null,
])

<input type="date" id="{{ $inputId }}" value="{{ $value }}" placeholder="{{ $placeholder }}" {{ $attributes->merge(['class' => 'input input-bordered']) }} />


