@props([
    'mobile' => false,
    'position' => 'bottom', // bottom|top
    // Tailles: xs|sm|md|lg|xl (md par défaut DaisyUI)
    'size' => null,
    // Balise wrapper: div|nav
    'as' => 'div',
    // aria-label si as=nav
    'label' => 'Dock',
    // Coller au viewport (recommandé)
    'fixed' => true,
])

@php
    $classes = 'dock';
    if ($fixed) {
        $classes = 'fixed left-0 right-0 '.($position === 'bottom' ? 'bottom-0' : 'top-0').' '.$classes;
    }
    if ($mobile) $classes .= ' dock-mobile';
    if ($position === 'bottom') $classes .= ' dock-bottom';
    if ($position === 'top') $classes .= ' dock-top';
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) {
        $classes .= ' dock-' . $size;
    }
    $tag = in_array($as, ['div','nav'], true) ? $as : 'div';
@endphp

<{{ $tag }} @if($tag==='nav') aria-label="{{ $label }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>
