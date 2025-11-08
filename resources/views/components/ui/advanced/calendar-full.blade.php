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
    {{ $attributes->merge(['class' => 'calendar-full block w-full']) }}
></div>

@include('daisy::components.ui.partials.calendar-event')

@include('daisy::components.partials.assets')

@pushOnce('styles')
    <style>
        /* Styles minimaux pour le calendrier complet (thémés par DaisyUI) */
        .calendar-full { --cf-border: oklch(var(--b3)); --cf-bg: oklch(var(--b1)); --cf-emp: oklch(var(--bc)); --cf-accent: oklch(var(--p)); }
        .cf-toolbar { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-bottom:.5rem; }
        .cf-toolbar .btn { min-height: 2rem; height: 2rem; }
        .cf-grid { border:1px solid var(--cf-border); background:var(--cf-bg); border-radius:.5rem; overflow:hidden; }
        .cf-month, .cf-week, .cf-day { display:grid; }
        .cf-month { display:grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
        .cf-cell { border-right:1px solid var(--cf-border); border-bottom:1px solid var(--cf-border); padding:.25rem; position:relative; min-height:5rem; }
        .cf-cell.is-today { background:color-mix(in oklch, var(--cf-accent) 6%, transparent); }
        .cf-cell .cf-date { font-size:.75rem; opacity:.7; }
        .cf-event { display:block; position:relative; background:color-mix(in oklch, var(--cf-accent) 18%, var(--cf-bg)); color: oklch(var(--pc)); border:1px solid color-mix(in oklch, var(--cf-accent) 40%, transparent); border-radius:.25rem; padding:0 .25rem; line-height:1.25rem; height:1.25rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .cf-event + .cf-event { margin-top: .125rem; }
        .cf-more { font-size:.75rem; opacity:.7; margin-top:.25rem; display:block; }
        .cf-week { display:grid; grid-template-columns: 4rem repeat(7, minmax(0, 1fr)); width:100%; }
        .cf-hour { border-bottom:1px dashed var(--cf-border); font-size:.75rem; opacity:.6; padding-right:.25rem; text-align:right; }
        .cf-slot { position:relative; border-left:1px solid var(--cf-border); border-bottom:1px dashed color-mix(in oklch, var(--cf-border) 40%, transparent); }
        .cf-block { position:absolute; left:2px; right:2px; border-radius:.25rem; padding:.25rem .375rem; overflow:hidden; color: oklch(var(--pc)); background:color-mix(in oklch, var(--cf-accent) 22%, var(--cf-bg)); border:1px solid color-mix(in oklch, var(--cf-accent) 40%, transparent); display:flex; align-items:flex-start; }
        .cf-list .cf-li { display:flex; align-items:center; gap:.5rem; padding:.5rem 0; border-bottom:1px solid var(--cf-border); }
        .cf-badge { width:.5rem; height:.5rem; border-radius:9999px; background: color-mix(in oklch, var(--cf-accent) 40%, var(--cf-bg)); display:inline-block; }
        .cf-dot { width:.5rem; height:.5rem; border-radius:9999px; flex:0 0 auto; background: currentColor; box-shadow: inset 0 0 0 1px color-mix(in oklch, var(--cf-bg) 60%, black); }
    </style>
@endPushOnce


