@props([
    /** @var bool Use quarter-circle (flower) layout for speed-dial buttons. */
    'flower' => false,
])

@php
    $root = 'fab';
    if ($flower) {
        $root .= ' fab-flower';
    }
@endphp

<div {{ $attributes->merge(['class' => $root]) }}>
    @isset($trigger)
        {{ $trigger }}
    @endisset
    {{ $slot }}
</div>
