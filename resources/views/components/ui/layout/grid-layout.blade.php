@props([
    'gap' => 4,
    'align' => 'start',   // start|center|end
    'class' => '',
    'styleStack' => 'styles',
])

@php
    $gapValue = is_numeric($gap) ? (string) ((int) $gap) : (string) $gap;
    $alignMap = [
        'start' => 'items-start',
        'center' => 'items-center',
        'end' => 'items-end',
    ];
    $alignClass = $alignMap[$align] ?? $alignMap['start'];

    $rootClasses = 'daisy-grid grid grid-cols-12 gap-'.$gapValue.' '.$alignClass;
    if (! empty($class)) {
        $rootClasses .= ' '.$class;
    }
@endphp

@pushOnce('styles')
<style>
/* Daisy Kit Grid Layout — Bootstrap-like utilities for grid spans and offsets */
/* Default fallback: any child spans full width unless overridden by a col-* class */
.daisy-grid > * { grid-column: 1 / -1; }

/* Base spans */
@for ($i = 1; $i <= 12; $i++)
.col-{{ $i }} { grid-column: span {{ $i }} / span {{ $i }}; }
@endfor

/* Base offsets */
@for ($i = 0; $i <= 11; $i++)
.offset-{{ $i }} { grid-column-start: {{ $i + 1 }}; }
@endfor

/* Responsive breakpoints */
@php
    $bps = ['sm' => '640px', 'md' => '768px', 'lg' => '1024px', 'xl' => '1280px', '2xl' => '1536px'];
@endphp
@foreach ($bps as $bp => $minWidth)
@media (min-width: {{ $minWidth }}) {
@for ($i = 1; $i <= 12; $i++)
  .col-{{ $bp }}-{{ $i }} { grid-column: span {{ $i }} / span {{ $i }}; }
@endfor
@for ($i = 0; $i <= 11; $i++)
  .offset-{{ $bp }}-{{ $i }} { grid-column-start: {{ $i + 1 }}; }
@endfor
}
@endforeach
</style>
@endPushOnce

<div {{ $attributes->merge(['class' => trim($rootClasses)]) }}>
    {{ $slot }}
    {{-- Enfants libres: utilisent .col-*, .offset-* --}}
    {{-- Exemple: <div class="col-sm-12 col-xl-4">...</div> --}}
</div>


