@props([
    // Couleurs et styles
    'bg' => 'base-100',        // any bg-* token (ex: base-100, neutral, primary)
    'text' => null,            // optional text-* token (ex: base-content, primary-content)
    'shadow' => null,          // null|sm|md|lg
    'rounded' => false,
    // Position
    'fixed' => false,
    'fixedPosition' => 'top',  // top|bottom
    // Cacher le center sous un breakpoint (ex: 'lg' → hidden lg:flex)
    'centerHiddenBelow' => null, // sm|md|lg|xl
])

@php
    $classes = 'navbar bg-'.$bg;
    if ($text) $classes .= ' text-'.$text;
    if ($shadow) $classes .= ' shadow'.($shadow === 'sm' ? '-sm' : ($shadow === 'md' ? '' : ($shadow === 'lg' ? '-lg' : '')));
    if ($rounded) $classes .= ' rounded-box';
    if ($fixed) $classes .= ' fixed '.$fixedPosition.'-0 left-0 right-0 z-50';

    $centerClasses = 'navbar-center';
    if ($centerHiddenBelow && in_array($centerHiddenBelow, ['sm','md','lg','xl'], true)) {
        $centerClasses .= ' hidden '.$centerHiddenBelow.':flex';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <div class="navbar-start">
        {{ $start ?? ($brand ?? '') }}
    </div>
    <div class="{{ $centerClasses }}">
        {{ $center ?? ($nav ?? '') }}
    </div>
    <div class="navbar-end">
        {{ $end ?? ($actions ?? '') }}
    </div>
</div>
