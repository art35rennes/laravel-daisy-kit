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
    $rootClasses = 'drawer';
    if ($end) $rootClasses .= ' drawer-end';
    if ($responsiveOpen) $rootClasses .= ' '.$responsiveOpen.':drawer-open';

    $contentClasses = 'drawer-content';
    if ($fullHeight) $contentClasses .= ' min-h-screen';
    if (!empty($contentClass)) $contentClasses .= ' '.$contentClass;
@endphp

<div {{ $attributes->merge(['class' => $rootClasses]) }}>
  <input id="{{ $id }}" type="checkbox" class="drawer-toggle" @checked($open) />
  <div class="{{ $contentClasses }}">
    {{ $content ?? $slot }}
  </div>
  <div class="drawer-side">
    <label for="{{ $id }}" aria-label="close sidebar" class="drawer-overlay"></label>
    @if($sideIsMenu)
      <ul class="menu p-4 bg-base-200 text-base-content h-full overflow-y-auto border-r border-base-content/10 {{ $sideClass }}">
        {{ $side ?? '' }}
      </ul>
    @else
      <div class="bg-base-200 text-base-content h-full overflow-y-auto border-r border-base-content/10 {{ $sideClass }}">
        {{ $side ?? '' }}
      </div>
    @endif
  </div>
</div>
