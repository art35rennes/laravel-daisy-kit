@props([
    'for' => null,
    'value' => null,
    'alt' => null,
    'srOnly' => false,
    // Floating label mode
    'floating' => false,
    'span' => null,          // text inside floating <span>
    'spanPosition' => 'before', // before|after relative to the input when floating
])

@if($floating)
    @php($classes = 'floating-label')
    <label {{ $attributes->merge(['class' => $classes]) }}>
        @if($spanPosition === 'before')
            <span>{{ $span ?? $value }}</span>
            {{ $slot }}
        @else
            {{ $slot }}
            <span>{{ $span ?? $value }}</span>
        @endif
    </label>
@else
    @php($labelClass = 'label break-words text-wrap whitespace-normal overflow-hidden')
    @if($srOnly)
        @php($labelClass .= ' sr-only')
    @endif
    <label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => $labelClass]) }}>
        <span class="label-text break-words text-wrap whitespace-normal overflow-hidden">{{ $slot->isNotEmpty() ? $slot : $value }}</span>
        @if($alt)
            <span class="label-text-alt break-words text-wrap whitespace-normal overflow-hidden">{{ $alt }}</span>
        @endif
    </label>
@endif


