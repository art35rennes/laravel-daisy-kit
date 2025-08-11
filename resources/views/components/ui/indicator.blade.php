@props([
    'label' => null,
    'position' => 'top-end', // top|middle|bottom + start|center|end
    'color' => 'primary', // for badge shortcut
    // Rendu de l'indicateur: badge (par défaut) | status
    'type' => 'badge',
    'statusColor' => 'success',
    // Classes additionnelles pour l'item (permet responsive: sm:indicator-middle ...)
    'itemClass' => null,
])

@php
    $classes = 'indicator';
    $posMap = [
        'top-start' => 'indicator-top indicator-start',
        'top-center' => 'indicator-top indicator-center',
        'top-end' => 'indicator-top indicator-end',
        'middle-start' => 'indicator-middle indicator-start',
        'middle-center' => 'indicator-middle indicator-center',
        'middle-end' => 'indicator-middle indicator-end',
        'bottom-start' => 'indicator-bottom indicator-start',
        'bottom-center' => 'indicator-bottom indicator-center',
        'bottom-end' => 'indicator-bottom indicator-end',
    ];
    $indicatorPos = $posMap[$position] ?? $posMap['top-end'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <span class="indicator-item {{ $indicatorPos }} {{ $itemClass }}">
        @isset($indicator)
            {{ $indicator }}
        @else
            @if($type === 'status')
                <span class="status status-{{ $statusColor }}"></span>
            @else
                <span class="badge badge-{{ $color }}">{{ $label }}</span>
            @endif
        @endisset
    </span>
    {{ $slot }}
</div>
