@props([
    'title' => null,
    'imageUrl' => null,
    'bordered' => false,
    'compact' => false,
    'side' => false,
    'imageFull' => false,
    'color' => null, // base-100 (default) or any bg-* utility
])

@php
    $root = 'card';
    if ($compact) $root .= ' card-compact';
    if ($side) $root .= ' card-side';
    if ($imageFull) $root .= ' image-full';
    if ($bordered) $root .= ' card-border';

    $bgClass = $color ? ' bg-'.$color : ' bg-base-100';
    $root .= $bgClass.' shadow';
@endphp

<div {{ $attributes->merge(['class' => $root]) }}>
    @if($imageUrl || isset($figure))
        <figure>
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="" />
            @else
                {{ $figure }}
            @endif
        </figure>
    @endif

    <div class="card-body">
        @if($title)
            <h2 class="card-title">{{ $title }}</h2>
        @endif
        <div>{{ $slot }}</div>
        @isset($actions)
            <div class="card-actions justify-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
