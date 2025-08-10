@props([
    'url' => 'https://',
])

<div {{ $attributes->merge(['class' => 'mockup-browser border bg-base-300']) }}>
  <div class="mockup-browser-toolbar">
    <div class="input">{{ $url }}</div>
  </div>
  <div class="bg-base-200">
    {{ $slot }}
  </div>
</div>
