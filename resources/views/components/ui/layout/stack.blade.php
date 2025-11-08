@props([
    'class' => '',
    // Alignements: par défaut, daisyUI aligne en bas.
    'alignV' => 'bottom', // top|bottom
    'alignH' => null,     // start|end
])

@php
    $classes = 'stack';
    // Vertical
    if ($alignV === 'top') {
        $classes .= ' stack-top';
    } elseif ($alignV === 'bottom') {
        $classes .= ' stack-bottom';
    }
    // Horizontal
    if ($alignH === 'start') {
        $classes .= ' stack-start';
    } elseif ($alignH === 'end') {
        $classes .= ' stack-end';
    }
    if (!empty($class)) {
        $classes .= ' '.$class;
    }
@endphp

<div {{ $attributes->merge(['class' => trim($classes)]) }}>
    {{ $slot }}
</div>
