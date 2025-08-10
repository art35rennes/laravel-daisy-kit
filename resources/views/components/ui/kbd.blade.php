@props([
    'keys' => null,
])

<kbd {{ $attributes->merge(['class' => 'kbd']) }}>
    {{ $keys ?? $slot }}
</kbd>


