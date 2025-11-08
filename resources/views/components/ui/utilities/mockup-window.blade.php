@props([
  'bordered' => true,
  // Couleur de fond et de bordure du conteneur
  'bg' => 'base-100',
  'borderColor' => 'base-300', // utilisé si bordered=true
  // Zone de contenu interne
  'contentBg' => null, // ex: base-200; si null, pas de classe bg appliquée
  'contentClass' => null,
  // Bordure supérieure optionnelle sur la zone de contenu (exemples de la doc)
  'contentTopBorder' => false,
  'contentTopBorderColor' => 'base-300',
])

@php
  $root = 'mockup-window bg-' . $bg;
  if ($bordered) {
    $root .= ' border border-' . $borderColor;
  }
  $inner = '';
  if ($contentBg) {
    $inner .= ' bg-' . $contentBg;
  }
  if ($contentTopBorder) {
    $inner .= ' border-t border-' . $contentTopBorderColor;
  }
  if ($contentClass) {
    $inner .= ' ' . $contentClass;
  }
@endphp

<div {{ $attributes->merge(['class' => trim($root)]) }}>
  <div class="{{ trim($inner) }}">{{ $slot }}</div>
</div>
