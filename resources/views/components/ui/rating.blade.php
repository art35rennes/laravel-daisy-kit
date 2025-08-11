@props([
    'name' => 'rating',
    'count' => 5,
    'value' => 0,
    'half' => false,
    'shape' => 'star-2', // star|star-2|heart|circle etc (mask-*)
    'color' => null, // bg-primary etc without bg- prefix
    'readOnly' => false,
    'size' => null, // xs|sm|md|lg|xl
    'clearable' => false, // ajoute un input rating-hidden pour pouvoir clear
])

@php
    $wrapper = 'rating';
    if ($half) $wrapper .= ' rating-half';
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) $wrapper .= ' rating-'.$size;
    $maskBase = 'mask mask-'.$shape;
    $colorClass = $color ? ' bg-'.$color : ' bg-yellow-400';
@endphp

<div {{ $attributes->merge(['class' => $wrapper]) }}>
    @if($readOnly)
        @for($i = 1; $i <= $count; $i++)
            @php $isCurrent = (!$half && (int)$value === $i) || ($half && ($value === $i || $value === ($i - 0.5))); @endphp
            @if(!$half)
                <div class="{{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" @if($isCurrent) aria-current="true" @endif></div>
            @else
                <div class="mask mask-half-1 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ ($i - 0.5) }} star" @if($value === ($i - 0.5)) aria-current="true" @endif></div>
                <div class="mask mask-half-2 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" @if($value === $i) aria-current="true" @endif></div>
            @endif
        @endfor
    @else
        @if($clearable)
            <input type="radio" name="{{ $name }}" value="0" class="rating-hidden" aria-label="Clear rating" />
        @endif
        @if(!$half)
            @for($i = 1; $i <= $count; $i++)
                <input type="radio" name="{{ $name }}" class="{{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" value="{{ $i }}" @checked($value == $i) />
            @endfor
        @else
            @for($i = 1; $i <= $count; $i++)
                <input type="radio" name="{{ $name }}" class="mask mask-half-1 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ ($i - 0.5) }} star" value="{{ ($i - 0.5) }}" @checked($value == ($i - 0.5)) />
                <input type="radio" name="{{ $name }}" class="mask mask-half-2 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" value="{{ $i }}" @checked($value == $i) />
            @endfor
        @endif
    @endif
</div>
