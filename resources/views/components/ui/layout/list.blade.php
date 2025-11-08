@props([
    'vertical' => true,
    // Styles utilitaires inspirés de la doc
    'bg' => false,          // ajoute bg-base-100
    'rounded' => false,     // ajoute rounded-box
    'shadow' => null,       // null|sm|md|lg -> shadow-sm/md/lg
    'title' => null,        // texte en-tête (li spécifique)
])

@php
    $classes = 'list';
    // Le composant est vertical par défaut. Un mode horizontal n'est pas prévu par DaisyUI pour list
    if ($bg) $classes .= ' bg-base-100';
    if ($rounded) $classes .= ' rounded-box';
    if ($shadow) $classes .= ' shadow'.($shadow === 'sm' ? '-sm' : ($shadow === 'md' ? '-md' : ($shadow === 'lg' ? '-lg' : '')));
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    @if($title)
        <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">{{ $title }}</li>
    @endif
    {{ $slot }}
  </ul>
