@props([
    'src' => null,
    'alt' => '',
    'size' => 'md', // xs|sm|md|lg
    'rounded' => 'full', // none|sm|md|lg|full
    'placeholder' => null,
])

@php
    $sizeMap = [
        'xs' => 'size-6',
        'sm' => 'size-8',
        'md' => 'size-10',
        'lg' => 'size-12',
    ];
    $roundedMap = [
        'none' => 'rounded-none',
        'sm' => 'rounded',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];
    $wrapper = 'avatar';
    $imgClass = ($sizeMap[$size] ?? 'size-10').' '.($roundedMap[$rounded] ?? 'rounded-full');
@endphp

<div {{ $attributes->merge(['class' => $wrapper]) }}>
    <div class="{{$imgClass}}">
        @if($src)
            <img src="{{ $src }}" alt="{{ $alt }}" />
        @else
            <div class="bg-base-200 flex items-center justify-center text-xs {{ $roundedMap[$rounded] ?? 'rounded-full' }}">{{ $placeholder ?? 'A' }}</div>
        @endif
    </div>
  </div>


