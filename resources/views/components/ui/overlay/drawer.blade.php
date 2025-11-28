@props([
    'id' => 'drawer-toggle',
    'open' => false,
    'sideClass' => 'w-80',
    'end' => false, // drawer-end
    'responsiveOpen' => null, // ex: 'lg' -> lg:drawer-open
    // Quand true, la zone côté est un <ul class="menu ...">. Sinon, on rend le contenu brut.
    'sideIsMenu' => true,
    // Contrôle de la hauteur du contenu: par défaut plein écran, peut être désactivé pour les démos compactes
    'fullHeight' => true,
    // Classes supplémentaires injectées dans la zone contenu
    'contentClass' => '',
])

@php
    // Construction des classes CSS selon les options (placement, responsive).
    $rootClasses = 'drawer';
    // Placement : drawer-end pour sidebar à droite (défaut : gauche).
    if ($end) $rootClasses .= ' drawer-end';
    // Ouverture responsive : sidebar toujours visible à partir d'un breakpoint (ex: lg:drawer-open).
    if ($responsiveOpen) $rootClasses .= ' '.$responsiveOpen.':drawer-open';

    // Classes pour la zone de contenu principal.
    $contentClasses = 'drawer-content';
    // Hauteur pleine écran par défaut (peut être désactivée pour des layouts compacts).
    if ($fullHeight) $contentClasses .= ' min-h-screen';
    if (!empty($contentClass)) $contentClasses .= ' '.$contentClass;
@endphp

{{-- Drawer : layout avec sidebar rétractable (pattern daisyUI) --}}
<div {{ $attributes->merge(['class' => $rootClasses]) }}>
  {{-- Checkbox caché : contrôle l'état open/close du drawer (toggle via label) --}}
  <input id="{{ $id }}" type="checkbox" class="drawer-toggle" @checked($open) />
  {{-- Zone de contenu principal : contient le contenu de la page --}}
  <div class="{{ $contentClasses }}">
    {{ $content ?? $slot }}
  </div>
  {{-- Zone sidebar : menu ou contenu personnalisé --}}
  <div class="drawer-side">
    {{-- Overlay cliquable : ferme le drawer au clic (label pointant vers le checkbox) --}}
    <label for="{{ $id }}" aria-label="close sidebar" class="drawer-overlay"></label>
    @if($sideIsMenu)
      {{-- Mode menu : rend un <ul class="menu"> pour la navigation (défaut) --}}
      <ul class="menu p-4 bg-base-200 text-base-content h-full overflow-y-auto border-r border-base-content/10 {{ $sideClass }}">
        {{ $side ?? '' }}
      </ul>
    @else
      {{-- Mode contenu libre : rend un <div> pour du contenu personnalisé --}}
      <div class="bg-base-200 text-base-content h-full overflow-y-auto border-r border-base-content/10 {{ $sideClass }}">
        {{ $side ?? '' }}
      </div>
    @endif
  </div>
</div>
