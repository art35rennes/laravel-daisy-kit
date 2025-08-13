@props([
    // Local container (default) ou global
    'global' => false,
    // Couleur de la barre
    'color' => '#3b82f6',
    // Hauteur (ex: 6px, 0.5rem)
    'height' => '10px',
    // Décalage top (px) pour sticky/fixed
    'offset' => 0,
    // Container CSS selector (optionnel). Si null: détection auto du conteneur scrollable, ou window si global
    'container' => null,
    // Modal trigger
    'scroll' => null,          // pourcentage (0..100) à partir duquel on ouvre la modal
    'target' => null,          // sélecteur (ex: #myModal) d'un <dialog>
    'openOnce' => true,        // n'ouvrir qu'une fois (true) ou à chaque dépassement (false)
    // Classes supplémentaires
    'class' => '',
])

@php
    $attrs = [
        'data-scrollstatus' => '1',
        'data-global' => $global ? 'true' : 'false',
        'data-color' => $color,
        'data-height' => $height,
        'data-offset' => (string)($offset ?? 0),
    ];
    if ($container) $attrs['data-container'] = $container;
    if ($scroll !== null) $attrs['data-scroll'] = (string)$scroll;
    if ($target) $attrs['data-target'] = $target;
    if ($openOnce === false) $attrs['data-open-once'] = 'false';
@endphp

<div {{ $attributes->merge(['class' => trim('w-full '.$class)])->merge($attrs) }}>
    <div data-scrollstatus-progress></div>
</div>


@include('daisy::components.partials.assets')
