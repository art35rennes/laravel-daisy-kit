@props([
    'total' => 1,
    'current' => 1,
])

@php
    $total = max(1, (int) $total);
    $current = max(1, min($current, $total));
@endphp

<div class="join">
    <button class="btn join-item" @disabled($current === 1)>«</button>
    @for($i = 1; $i <= $total; $i++)
        <button class="btn join-item {{ $i === $current ? 'btn-active' : '' }}">{{ $i }}</button>
    @endfor
    <button class="btn join-item" @disabled($current === $total)>»</button>
</div>
