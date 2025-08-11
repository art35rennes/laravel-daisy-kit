@props([
    'keys' => null,
    'size' => 'md', // xs|sm|md|lg|xl
])

@php
    $sizeMap = [
        'xs' => 'kbd-xs',
        'sm' => 'kbd-sm',
        'md' => 'kbd-md',
        'lg' => 'kbd-lg',
        'xl' => 'kbd-xl',
    ];
    $sizeClass = $sizeMap[$size] ?? 'kbd-md';
@endphp

@if(is_array($keys))
    <span {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
        @foreach($keys as $i => $k)
            <kbd class="kbd {{ $sizeClass }}">{{ $k }}</kbd>
            @if($i < count($keys) - 1)
                <span class="px-1">+</span>
            @endif
        @endforeach
    </span>
@else
    <kbd {{ $attributes->merge(['class' => 'kbd '.$sizeClass]) }}>
        {{ $keys ?? $slot }}
    </kbd>
@endif


