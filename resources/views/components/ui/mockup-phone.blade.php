@props([
    'camera' => true,
])

<div {{ $attributes->merge(['class' => 'mockup-phone']) }}>
  <div class="camera @unless($camera) hidden @endunless"></div>
  <div class="display">
    <div class="artboard artboard-demo phone-1">
      {{ $slot }}
    </div>
  </div>
</div>
