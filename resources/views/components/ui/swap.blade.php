@props([
    'rotate' => false,
    'flip' => false,
    'checked' => false,
])

@php
    $classes = 'swap';
    if ($rotate) $classes .= ' swap-rotate';
    if ($flip) $classes .= ' swap-flip';
@endphp

<label {{ $attributes->merge(['class' => $classes]) }}>
    <input type="checkbox" @checked($checked) />
    <div class="swap-on">
        {{ $on ?? '' }}
    </div>
    <div class="swap-off">
        {{ $off ?? '' }}
    </div>
</label>
