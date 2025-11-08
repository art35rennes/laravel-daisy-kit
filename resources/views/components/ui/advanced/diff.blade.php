@props([
    // Afficher le contrôleur de redimensionnement
    'resizable' => true,
    // Ajouter une classe d'aspect ratio (ex: aspect-16/9)
    'aspect' => null,
    // Rendre la figure focusable pour clavier
    'focusable' => true,
    // Rôle ARIA des items (la doc utilise role="img")
    'itemRole' => 'img',
])

@php
    $classes = 'diff';
    if ($aspect) {
        $classes .= ' ' . $aspect;
    }
    $tabindex = $focusable ? 0 : null;
@endphp

<figure {{ $attributes->merge(['class' => $classes]) }} @if(!is_null($tabindex)) tabindex="0" @endif>
  <div class="diff-item-1" role="{{ $itemRole }}">{{ $before ?? '' }}</div>
  <div class="diff-item-2" role="{{ $itemRole }}">{{ $after ?? '' }}</div>
  @if($resizable)
    <div class="diff-resizer"></div>
  @endif
  </figure>
