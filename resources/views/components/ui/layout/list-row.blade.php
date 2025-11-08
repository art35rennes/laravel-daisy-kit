@props([
    // Slot libre pour le contenu de la ligne
])

<li {{ $attributes->merge(['class' => 'list-row']) }}>
    {{ $slot }}
</li>


