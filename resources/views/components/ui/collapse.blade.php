@props([
    'title' => null,
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'open' => false,
    'disabled' => false,
])

@php
    $root = 'collapse';
    $root .= $arrow ? ' collapse-arrow' : ' collapse-plus';
    if ($open) $root .= ' collapse-open';
@endphp

<div {{ $attributes->merge(['class' => $root]) }}>
    <input type="checkbox" @checked($open) @disabled($disabled) />
    <div class="collapse-title text-lg font-medium">{{ $title }}</div>
    <div class="collapse-content">{{ $slot }}</div>
</div>
