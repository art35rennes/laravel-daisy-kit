@props([
    // mode: 'date' (sélection simple) ou 'range' (intervalle)
    'mode' => 'date',
    // nombre de mois affichés
    'months' => 1,
    // afficher les contrôles de navigation
    'showPrevNext' => true,
    // classes du conteneur des mois (pour layout)
    'monthsClass' => 'flex flex-wrap gap-4 justify-center',
])

@php
    $isRange = $mode === 'range';
    $tag = $isRange ? 'calendar-range' : 'calendar-date';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'cally']) }} @if($months > 1) months="{{ (int)$months }}" @endif>
    @if($showPrevNext)
        @isset($prevIcon)
            <span slot="previous">{{ $prevIcon }}</span>
        @else
            <x-heroicon-o-chevron-left aria-label="Previous" class="fill-current size-4" slot="previous" />
        @endisset
        @isset($nextIcon)
            <span slot="next">{{ $nextIcon }}</span>
        @else
            <x-heroicon-o-chevron-right aria-label="Next" class="fill-current size-4" slot="next" />
        @endisset
    @endif

    @if($months > 1)
        <div class="{{ $monthsClass }}">
            @for($i = 0; $i < (int)$months; $i++)
                <calendar-month @if($i>0) offset="{{ $i }}" @endif></calendar-month>
            @endfor
        </div>
    @else
        <calendar-month></calendar-month>
    @endif

    {{ $slot }}
</{{ $tag }}>


