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

<div {{ $attributes->merge(['class' => trim($rootClasses)]) }}>
    {{ $slot }}
    {{-- Enfants libres: utilisent .col-*, .offset-* --}}
    {{-- Exemple: <div class="col-sm-12 col-xl-4">...</div> --}}
</div>

