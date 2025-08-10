@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => trim('stack '.$class)]) }}>
    {{ $slot }}
</div>
