@props([
    'url' => 'https://',
    'bordered' => true,
    'bg' => 'base-300',       // arrière-plan du mockup-browser
    'contentBg' => 'base-200', // arrière-plan de la zone de contenu
    'showToolbar' => true,
    'inputClass' => null,      // classes additionnelles pour l'input de la toolbar
])

@php
    $rootClasses = 'mockup-browser bg-' . $bg;
    if ($bordered) {
        $rootClasses .= ' border';
    }
    $contentClasses = 'bg-' . $contentBg;
    $toolbarInputClasses = 'input' . ($inputClass ? ' ' . $inputClass : '');
@endphp

<div {{ $attributes->merge(['class' => $rootClasses]) }}>
  @if($showToolbar)
    <div class="mockup-browser-toolbar">
      @isset($toolbar)
        {{ $toolbar }}
      @else
        <div class="{{ $toolbarInputClasses }}">{{ $url }}</div>
      @endisset
    </div>
  @endif
  <div class="{{ $contentClasses }}">
    {{ $slot }}
  </div>
</div>
