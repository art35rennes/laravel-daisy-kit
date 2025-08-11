@props([
    'src' => null,
    'alt' => '',
    'size' => 'md', // xs|sm|md|lg|xl|xxl
    'rounded' => 'full', // none|sm|md|lg|xl|full
    'placeholder' => null,
    // statut de présence: online | offline | null
    'status' => null,
])

@php
    // DaisyUI exemples utilisent des largeurs w-*
    $sizeMap = [
        'xs' => 'w-8',
        'sm' => 'w-12',
        'md' => 'w-16',
        'lg' => 'w-20',
        'xl' => 'w-24',
        'xxl' => 'w-32',
    ];
    $roundedMap = [
        'none' => 'rounded-none',
        'sm' => 'rounded',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
    ];
    $placeholderTextSize = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-2xl',
        'xxl' => 'text-3xl',
    ][$size] ?? 'text-base';

    $wrapper = 'avatar';
    if ($status === 'online') $wrapper .= ' avatar-online';
    if ($status === 'offline') $wrapper .= ' avatar-offline';
    if (!$src && !is_null($placeholder)) $wrapper .= ' avatar-placeholder';

    $containerClass = ($sizeMap[$size] ?? 'w-16').' '.($roundedMap[$rounded] ?? 'rounded-full');
@endphp

<div {{ $attributes->merge(['class' => $wrapper]) }}>
    <div class="{{$containerClass}}">
        @if($src)
            <img src="{{ $src }}" alt="{{ $alt }}" />
        @else
            <div class="bg-neutral text-neutral-content w-full h-full grid place-items-center {{ $roundedMap[$rounded] ?? 'rounded-full' }} {{$placeholderTextSize}}">
                <span>{{ $placeholder ?? 'A' }}</span>
            </div>
        @endif
    </div>
  </div>


