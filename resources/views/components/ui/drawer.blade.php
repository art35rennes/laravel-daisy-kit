@props([
    'id' => 'drawer-toggle',
    'open' => false,
    'sideClass' => 'w-80',
    'end' => false, // drawer-end
    'responsiveOpen' => null, // ex: 'lg' -> lg:drawer-open
])

@php
    $rootClasses = 'drawer';
    if ($end) $rootClasses .= ' drawer-end';
    if ($responsiveOpen) $rootClasses .= ' '.$responsiveOpen.':drawer-open';
@endphp

<div {{ $attributes->merge(['class' => $rootClasses]) }}>
  <input id="{{ $id }}" type="checkbox" class="drawer-toggle" @checked($open) />
  <div class="drawer-content">
    {{ $content ?? $slot }}
  </div>
  <div class="drawer-side">
    <label for="{{ $id }}" aria-label="close sidebar" class="drawer-overlay"></label>
    <ul class="menu p-4 bg-base-200 text-base-content {{ $sideClass }}">
      {{ $side ?? '' }}
    </ul>
  </div>
</div>
