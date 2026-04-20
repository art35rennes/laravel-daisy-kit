@props([
    'id' => null,
    'type' => null,
    'x' => 0,
    'y' => 0,
    'w' => 3,
    'h' => 2,
    'meta' => null,
])

@php
    $resolvedId = filled($id) ? (string) $id : null;
    $resolvedType = filled($type) ? (string) $type : null;
    $resolvedX = max(0, (int) $x);
    $resolvedY = max(0, (int) $y);
    $resolvedW = max(1, (int) $w);
    $resolvedH = max(1, (int) $h);
    $encodedMeta = $meta === null ? null : json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $rootClasses = trim('grid-stack-item daisy-editable-grid-item '.($attributes->get('class') ?? ''));
    $attributes = $attributes->except('class');
@endphp

<div
    {{ $attributes->merge(['class' => $rootClasses]) }}
    @if($resolvedId) gs-id="{{ $resolvedId }}" @endif
    @if($resolvedType) data-type="{{ $resolvedType }}" @endif
    @if($encodedMeta) data-meta='{{ $encodedMeta }}' @endif
    gs-x="{{ $resolvedX }}"
    gs-y="{{ $resolvedY }}"
    gs-w="{{ $resolvedW }}"
    gs-h="{{ $resolvedH }}"
>
    <div class="grid-stack-item-content daisy-editable-grid-item-content">
        {{ $slot }}
    </div>
</div>
