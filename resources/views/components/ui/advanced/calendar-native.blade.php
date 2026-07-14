@props([
    'inputId' => null,
    'value' => null,
    'placeholder' => null,
    'min' => null,
    'max' => null,
])

<input
    type="date"
    @if(!is_null($inputId)) id="{{ $inputId }}" @endif
    @if(!is_null($value)) value="{{ $value }}" @endif
    @if(!is_null($placeholder)) placeholder="{{ $placeholder }}" @endif
    @if(!is_null($min)) min="{{ $min }}" @endif
    @if(!is_null($max)) max="{{ $max }}" @endif
    {{ $attributes->merge(['class' => 'input daisy-native-picker-date']) }}
/>
