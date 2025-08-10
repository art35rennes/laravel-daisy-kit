@props([
    'name' => 'rating',
    'count' => 5,
    'value' => 0,
    'half' => false,
    'shape' => 'star-2', // star|star-2|heart|circle etc (mask-*)
    'color' => null, // bg-primary etc without bg- prefix
    'readOnly' => false,
])

@php
    $wrapper = 'rating';
    $maskBase = 'mask mask-'.$shape;
    $colorClass = $color ? ' bg-'.$color : ' bg-yellow-400';
@endphp

<div {{ $attributes->merge(['class' => $wrapper]) }}>
    @if(!$half)
        @for($i = 1; $i <= $count; $i++)
            <input type="radio" name="{{ $name }}" class="{{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" value="{{ $i }}" @checked($value == $i) @disabled($readOnly) />
        @endfor
    @else
        @for($i = 1; $i <= $count; $i++)
            <input type="radio" name="{{ $name }}" class="mask mask-half-1 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" value="{{ ($i - 0.5) }}" @checked($value == ($i - 0.5)) @disabled($readOnly) />
            <input type="radio" name="{{ $name }}" class="mask mask-half-2 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" value="{{ $i }}" @checked($value == $i) @disabled($readOnly) />
        @endfor
    @endif
</div>
