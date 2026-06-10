@props([
    // Vue initiale: month | week | day | year | list
    'view' => 'month',
    // Vues disponibles pour le switcher
    'views' => ['year','month','week','day','list'],
    // Date ISO (YYYY-MM-DD) de départ. Null = aujourd'hui
    'initialDate' => null,
    // Données d'évènements: tableau PHP encodé en JSON côté data-attr
    'events' => null,
    // URL JSON pour chargement AJAX (params fournis: start, end)
    'eventsUrl' => null,
    // Premier jour de la semaine (0=Dimanche .. 6=Samedi). FR par défaut
    'firstDay' => 1,
    // Plage horaire visible pour les vues week/day (ex: 6..22)
    'hourStart' => 6,
    'hourEnd' => 22,
    // Hauteur: auto | fixed(px)
    'height' => 'auto',
    // Détail par défaut: none | modal (clic événement). none = évènement personnalisé uniquement
    'detail' => 'modal',
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $heightClass = null;
    if ($height !== 'auto') {
        $heightValue = trim((string) $height);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $heightValue, $matches) === 1) {
            $token = (int) round((float) $matches[1]);
            $heightClass = $token >= 1 && $token <= 1200 ? 'daisy-calendar-min-height-px-'.$token : null;
        } elseif (is_numeric($heightValue)) {
            $token = (int) round((float) $heightValue);
            $heightClass = $token >= 1 && $token <= 1200 ? 'daisy-calendar-min-height-px-'.$token : null;
        }
    }

    $data = [
        'view' => $view,
        'views' => array_values($views ?? []),
        'initialDate' => $initialDate,
        'firstDay' => (int) $firstDay,
        'hourStart' => (int) $hourStart,
        'hourEnd' => (int) $hourEnd,
        'height' => $height,
        'detail' => $detail,
    ];
@endphp

<div
    data-module="{{ $module ?? 'calendar-full' }}"
    data-calendar-full="1"
    data-options='@json($data)'
    @if($events) data-events='@json($events)' @endif
    @if($eventsUrl) data-events-url="{{ $eventsUrl }}" @endif
    {{ $attributes->merge(['class' => trim('calendar-full block w-full '.$heightClass)]) }}
></div>

@include('daisy::components.ui.partials.calendar-event')

@include('daisy::components.partials.assets')
