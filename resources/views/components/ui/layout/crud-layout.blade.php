@props([
    // Breakpoint sur lequel on bascule en 2 colonnes.
    // Valeurs supportées: sm|md|lg|xl|2xl
    'breakpoint' => 'lg',
    // Conteneur visuel (permet de limiter à une colonne centrale).
    // Ex.: 'max-w-4xl mx-auto px-4 sm:px-6'
    'container' => 'max-w-4xl mx-auto px-4 sm:px-6',
    // Espacement vertical entre sections.
    'gap' => 12,
    // Props présentes pour compatibilité/évolutions (ratio appliqué par crud-section).
    'categoryWidth' => '1/3',
    'contentWidth' => '2/3',
])

@php
    // Normalise le gap vers une classe Tailwind.
    $gapValue = is_numeric($gap) ? (int) $gap : 12;
    $stackClasses = 'space-y-'.$gapValue;
@endphp

<div {{ $attributes->merge(['class' => trim($container.' '.$stackClasses)]) }}>
    {{ $slot }}

    @if (isset($actions))
        <div class="mt-8 flex items-center justify-end gap-3">
            {{ $actions }}
        </div>
    @endif
</div>


