{{--
    Calendar Component (Wrapper)

    Composant wrapper qui délègue le rendu à un provider spécifique :
    - native : calendrier HTML5 natif (<input type="date">)
    - cally : calendrier avancé avec le web component Cally (défaut)

    Usage:
        <x-daisy::ui.advanced.calendar provider="cally" mode="range" />
--}}

@props([
    'provider' => 'cally', // cally | native
    // Props forwards
    'mode' => 'date',     // date | range | multi (cally)
    'months' => 1,        // (cally)
    'showPrevNext' => true,
    'inputId' => null,
    'value' => null,
    'min' => null,
    'max' => null,
    'locale' => null,
    'placeholder' => null,
])

@if($provider === 'native')
    {{-- Provider natif : utilise l'input date HTML5 (simple, accessible, mais limité) --}}
    <x-daisy::ui.advanced.calendar-native :inputId="$inputId" :value="$value" :placeholder="$placeholder" {{ $attributes }} />
@else
    {{-- Provider Cally (défaut) : web component avancé avec support range, multi-select, etc. --}}
    <x-daisy::ui.advanced.calendar-cally
        :mode="$mode"
        :months="$months"
        :showPrevNext="$showPrevNext"
        :value="$value"
        :min="$min"
        :max="$max"
        :locale="$locale"
        {{ $attributes }}
    >
        {{ $slot }}
    </x-daisy::ui.advanced.calendar-cally>
@endif
