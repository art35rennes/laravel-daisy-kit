@props([
    'mode' => 'date',             // date | range | multi
    'months' => 1,
    'showPrevNext' => true,
    'monthsClass' => 'flex flex-wrap gap-4 justify-center',
    // Attributs Cally communs
    'value' => null,              // date: YYYY-MM-DD | range: YYYY-MM-DD/YYYY-MM-DD | multi: implementation Cally
    'min' => null,                // YYYY-MM-DD
    'max' => null,                // YYYY-MM-DD
    'locale' => null,             // ex: en-GB, fr-FR
    // Compat icônes historiques (optionnel)
    'prevIcon' => null,
    'nextIcon' => null,
])

@php
    $tag = match ($mode) {
        'range' => 'calendar-range',
        'multi' => 'calendar-multi',
        default => 'calendar-date',
    };
@endphp

<{{ $tag }}
    {{ $attributes->merge(['class' => 'cally']) }}
    @if(!is_null($value)) value="{{ $value }}" @endif
    @if(!is_null($min)) min="{{ $min }}" @endif
    @if(!is_null($max)) max="{{ $max }}" @endif
    @if(!is_null($locale)) locale="{{ $locale }}" @endif
    @if($months > 1) months="{{ (int) $months }}" @endif
>
    @if($showPrevNext)
        @isset($previous)
            <span slot="previous">{{ $previous }}</span>
        @elseif(!is_null($prevIcon))
            <span slot="previous">{{ $prevIcon }}</span>
        @else
            <x-bi-chevron-left aria-label="Previous" class="size-4" slot="previous" />
        @endisset

        @isset($next)
            <span slot="next">{{ $next }}</span>
        @elseif(!is_null($nextIcon))
            <span slot="next">{{ $nextIcon }}</span>
        @else
            <x-bi-chevron-right aria-label="Next" class="size-4" slot="next" />
        @endisset
    @endif

    @isset($heading)
        <span slot="heading">{{ $heading }}</span>
    @endisset

    @if($months > 1)
        <div class="{{ $monthsClass }}">
            @for($i = 0; $i < (int) $months; $i++)
                <calendar-month @if($i > 0) offset="{{ $i }}" @endif></calendar-month>
            @endfor
        </div>
    @else
        <calendar-month></calendar-month>
    @endif

    {{ $slot }}
</{{ $tag }}>

@include('daisy::components.partials.assets')

@pushOnce('styles')
    <style>
        /* Cally x DaisyUI theming (cf. docs Theming) */
        :where(calendar-date.cally, calendar-range.cally, calendar-multi.cally) svg {
            height: 16px;
            width: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.5;
        }

        :where(calendar-date.cally, calendar-range.cally, calendar-multi.cally)::part(button) {
            border: 1px solid oklch(var(--b3));
            background-color: oklch(var(--b1));
            border-radius: 0.375rem;
            width: 26px;
            height: 26px;
            margin: 0; /* évite tout écart inutile entre les jours */
            position: relative; /* permet les overlays éventuels */
        }

        :where(calendar-date.cally, calendar-range.cally, calendar-multi.cally)::part(button):focus-visible {
            outline: 2px solid oklch(var(--p));
            outline-offset: 2px;
        }

        /* Couleurs d'accent basées sur le thème DaisyUI courant */
        calendar-month {
            --color-accent: oklch(var(--p));
            --color-text-on-accent: oklch(var(--pc));
        }

        /* Boutons jour: légère rondeur (ne touche pas les états sélectionnés) */
        calendar-month::part(button) {
            border-radius: 0.375rem;
        }

        /* IMPORTANT: on délègue l'affichage de la sélection et des plages au style par défaut Cally. */

        /* Focus clavier sur un jour: anneau net sans masquer le contenu */
        :where(calendar-date.cally, calendar-range.cally, calendar-multi.cally)::part(day):focus-visible {
            outline: 2px solid oklch(var(--p));
            outline-offset: 2px;
        }
    </style>
@endPushOnce
