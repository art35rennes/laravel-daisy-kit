@props([
    // Compat: position = horizontal (start|center|end)
    'position' => 'end',
    'horizontal' => null, // start|center|end
    'vertical' => 'bottom', // top|middle|bottom
    'triggerable' => false,
    'limit' => 4,
    'module' => null,
])

@php
    $h = $horizontal ?? $position;
    $horizontalClass = [
        'start' => 'toast-start',
        'center' => 'toast-center',
        'end' => 'toast-end',
    ][$h] ?? 'toast-end';

    $verticalClass = [
        'top' => 'toast-top',
        'middle' => 'toast-middle',
        'bottom' => 'toast-bottom',
    ][$vertical] ?? 'toast-bottom';
@endphp

@php
    $dataAttributes = [];

    if ($triggerable) {
        $dataAttributes = [
            'data-module' => $module ?? 'notify',
            'data-daisy-notify-container' => 'true',
            'data-notify-limit' => $limit,
            'data-notify-horizontal' => $h,
            'data-notify-vertical' => $vertical,
        ];
    }
@endphp

<div {{ $attributes->merge(['class' => 'toast '.$horizontalClass.' '.$verticalClass])->merge($dataAttributes) }}>
    {{ $slot ?? '' }}
</div>
