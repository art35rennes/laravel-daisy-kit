@props([
    'for' => null,
    'value' => null,
    'alt' => null,
    'srOnly' => false,
])

@php($labelClass = 'label')
@if($srOnly)
    @php($labelClass .= ' sr-only')
@endif

<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => $labelClass]) }}>
    <span class="label-text">{{ $slot->isNotEmpty() ? $slot : $value }}</span>
    @if($alt)
        <span class="label-text-alt">{{ $alt }}</span>
    @endif
</label>


