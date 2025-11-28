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

{{--
    CRUD Layout Component

    Layout principal pour les pages CRUD (Create, Read, Update, Delete).
    Fournit un conteneur centré avec espacement vertical entre les sections.

    Les sections individuelles sont gérées par crud-section (layout 2 colonnes responsive).

    Usage:
        <x-daisy::ui.layout.crud-layout>
            <x-daisy::ui.layout.crud-section title="..." />
        </x-daisy::ui.layout.crud-layout>
--}}

@php
    // Normalisation du gap vers une classe Tailwind space-y-*.
    $gapValue = is_numeric($gap) ? (int) $gap : 12;
    $stackClasses = 'space-y-'.$gapValue;
@endphp

{{-- Conteneur principal : centré avec largeur max et espacement vertical --}}
<div {{ $attributes->merge(['class' => trim($container.' '.$stackClasses)]) }}>
    {{ $slot }}

    {{-- Actions globales : slot pour les boutons d'action en bas de page (ex: Save, Cancel) --}}
    @if (isset($actions))
        <div class="mt-8 flex items-center justify-end gap-3">
            {{ $actions }}
        </div>
    @endif
</div>


