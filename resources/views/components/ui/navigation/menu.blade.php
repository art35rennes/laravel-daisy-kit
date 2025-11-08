@props([
    'vertical' => true,
    'size' => null, // xs|sm|md|lg|xl
    // Rendre horizontal à partir d'un breakpoint: sm|md|lg|xl (ajoute "sm:menu-horizontal" ...)
    'horizontalAt' => null,
    // Styles de conteneur
    'bg' => true,
    'rounded' => true,
    // Titre optionnel en tête
    'title' => null,
])

@php
    $classes = 'menu';
    if ($bg) $classes .= ' bg-base-100';
    if ($rounded) $classes .= ' rounded-box';
    if (!$vertical) $classes .= ' menu-horizontal';
    if ($horizontalAt && in_array($horizontalAt, ['sm','md','lg','xl'], true)) {
        $classes .= ' '.$horizontalAt.':menu-horizontal';
    }
    if ($size) $classes .= ' menu-'.$size;
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    @if($title)
        <li class="menu-title">{{ $title }}</li>
    @endif
    {{ $slot }}
  </ul>
