@props([
    'camera' => true,
    // Couleur de bordure ex: primary, secondary, accent...
    'borderColor' => null,
    // Contenu: soit via slot, soit via image wallpaper
    'wallpaper' => null,
    // Classes additionnelles pour la zone d'affichage
    'displayClass' => null,
])

@php
    $rootClasses = 'mockup-phone';
    if ($borderColor) {
        $rootClasses .= ' border-' . $borderColor;
    }
    // overflow-hidden pour éviter les débordements de wallpaper et centrer correctement la notch
    $displayClasses = 'mockup-phone-display overflow-hidden relative' . ($displayClass ? ' ' . $displayClass : '');
@endphp

<div {{ $attributes->merge(['class' => $rootClasses]) }}>
  @if($camera)
    <div class="mockup-phone-camera"></div>
  @endif
  <div class="{{ $displayClasses }}">
    @if($wallpaper)
      <img alt="wallpaper" src="{{ $wallpaper }}" class="w-full h-full object-cover" />
    @else
      {{ $slot }}
    @endif
  </div>
</div>
