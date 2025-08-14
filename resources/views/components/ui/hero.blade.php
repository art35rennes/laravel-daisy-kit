@props([
    'overlay' => false,
    'imageUrl' => null,
    'minH' => 'min-h-[24rem]',
    // Plein écran
    'fullScreen' => false,
    // Disposition
    'row' => false,
    'reverse' => false,
    // Couleurs/texte
    'text' => null,         // ex: neutral-content
    'bg' => null,           // ex: base-200
    'overlayClass' => 'bg-opacity-60',
    // Classes supplémentaires
    'contentMax' => 'max-w-md',
    'contentClass' => null,
])

@php
    $rootClasses = 'hero ' . ($fullScreen ? 'min-h-screen' : $minH);
    if ($bg) $rootClasses .= ' bg-'.$bg;

    $contentClasses = 'hero-content';
    if ($row) $contentClasses .= ' flex-col lg:flex-row';
    if ($reverse) $contentClasses .= ' lg:flex-row-reverse';

    // Texte: si explicitement fourni, on l'utilise; sinon si image/overlay → neutral-content, sinon rien
    $textClass = $text ? ' text-'.$text : (($imageUrl || $overlay) ? ' text-neutral-content' : '');
    $contentClasses .= ' '.$textClass;

    if ($contentClass) $contentClasses .= ' '.$contentClass;
@endphp

<div {{ $attributes->merge(['class' => $rootClasses]) }} @if($imageUrl) style="background-image: url('{{ $imageUrl }}');" @endif>
  @if($overlay)
    <div class="hero-overlay {{ $overlayClass }}"></div>
  @endif
  <div class="{{ $contentClasses }}">
    @isset($figure)
      <div class="w-full max-w-sm">
        {{ $figure }}
      </div>
    @endisset
    <div class="{{ $contentMax }} w-full">
      {{ $slot }}
    </div>
  </div>
</div>
