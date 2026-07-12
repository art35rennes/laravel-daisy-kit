@props([
    'name',
    'length' => 6,
    'value' => null,
    'numeric' => true,
    'joined' => false,
    'size' => 'md',
    'color' => null,
    'required' => false,
    'label' => null,
])

@php
    $length = max(1, min(8, (int) $length));
    $classes = ['otp'];

    if ($joined) {
        $classes[] = 'otp-joined';
    }

    if (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true)) {
        $classes[] = "otp-{$size}";
    }

    if (in_array($color, ['neutral', 'primary', 'secondary', 'accent', 'success', 'info', 'warning', 'error'], true)) {
        $classes[] = "otp-{$color}";
    }

    $inputAttributes = [
        'type' => 'text',
        'name' => $name,
        'value' => old($name, $value),
        'maxlength' => $length,
        'autocomplete' => 'one-time-code',
        'aria-label' => $label ?? \Illuminate\Support\Str::headline($name),
    ];

    if ($numeric) {
        $inputAttributes['inputmode'] = 'numeric';
        $inputAttributes['pattern'] = "[0-9]{{$length}}";
    }
@endphp

<label {{ $attributes->class($classes) }}>
    @for($index = 0; $index < $length; $index++)
        <span></span>
    @endfor
    <input @foreach($inputAttributes as $attribute => $attributeValue) {{ $attribute }}="{{ $attributeValue }}" @endforeach @required($required) />
</label>
